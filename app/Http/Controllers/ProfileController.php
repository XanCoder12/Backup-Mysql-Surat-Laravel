<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Handle base64 encoded profile photo from cropper
        if ($request->filled('cropped_photo')) {
            $base64Image = $request->input('cropped_photo');
            $imageParts = explode(';base64,', $base64Image);
            
            if (count($imageParts) === 2) {
                $imageTypeAux = explode('image/', $imageParts[0]);
                $imageType = isset($imageTypeAux[1]) ? explode(';', $imageTypeAux[1])[0] : 'png';
                $imageBase64 = base64_decode($imageParts[1]);
                
                // Validate magic bytes untuk mencegah RCE (validasi konten file, bukan extension)
                if (!$this->isValidImageMagicBytes($imageBase64)) {
                    return Redirect::route('profile.edit')->withErrors(['profile_photo' => 'File bukan merupakan gambar yang valid.']);
                }
                
                $fileName = 'profile_photos/' . uniqid() . '.' . $imageType;

                if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
                }

                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageBase64);
                $user->profile_photo = $fileName;
            }
        } elseif ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            
            // Validasi magic bytes sebelum menyimpan file
            $filePath = $request->file('profile_photo')->path();
            if (!$this->isValidImageMagicBytes(file_get_contents($filePath))) {
                return Redirect::route('profile.edit')->withErrors(['profile_photo' => 'File bukan merupakan gambar yang valid.']);
            }
            
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Validasi magic bytes untuk image files.
     * Mencegah upload file berbahaya yang di-disguise sebagai image.
     * Fix untuk RCE prevention - validasi konten file, bukan hanya extension.
     */
    private function isValidImageMagicBytes(string $fileContent): bool
    {
        if (empty($fileContent)) {
            return false;
        }

        // Magic bytes untuk image formats yang diizinkan
        $validSignatures = [
            'JPEG' => [0xFF, 0xD8, 0xFF],
            'PNG'  => [0x89, 0x50, 0x4E, 0x47],
            'GIF87' => [0x47, 0x49, 0x46, 0x38, 0x37],
            'GIF89' => [0x47, 0x49, 0x46, 0x38, 0x39],
            'WEBP' => [0x52, 0x49, 0x46, 0x46], // RIFF (WebP uses RIFF container)
        ];

        $fileBytes = array_values(unpack('C*', substr($fileContent, 0, 12)));

        foreach ($validSignatures as $format => $signature) {
            if (count($fileBytes) < count($signature)) {
                continue;
            }

            $match = true;
            for ($i = 0; $i < count($signature); $i++) {
                if ($fileBytes[$i] !== $signature[$i]) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                // Extra check untuk WebP
                if ($format === 'WEBP' && strpos($fileContent, 'WEBP') === false) {
                    continue;
                }
                return true;
            }
        }

        // Fallback: gunakan getimagesize untuk extra validation
        $tmpFile = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($tmpFile, $fileContent);
        $imageInfo = @getimagesize($tmpFile);
        @unlink($tmpFile);

        return $imageInfo !== false && in_array($imageInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP]);
    }
}
