<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratDeleteRequest;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\SuratMasukNotification;
use App\Notifications\DeleteRequestNotification;
use App\Notifications\SuratDeletedNotification;
use App\Notifications\FileRevisiNotification;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

    class SuratController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat::where('user_id', Auth::id())
                      ->with('tahapans')
                      ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $surats = $query->paginate(10)->withQueryString();

        return view('user.surat.index', compact('surats'));
    }

    public function create()
    {
        /** @var FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        $templates = collect($publicDisk->files('templates'))
            ->map(fn($path) => [
                'nama' => basename($path),
                'url'  => $publicDisk->url($path),
            ])->values();

        return view('user.surat.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'jenis'          => 'required|in:nota_dinas,surat_dinas,surat_keputusan,surat_pernyataan,surat_keterangan,surat_undangan,surat_lainnya',
            'sifat'          => 'required|in:biasa,segera,rahasia',
            'tujuan'         => 'required|string|max:500',
            'file_word'      => 'required|file|mimes:docx,doc|max:10240',
            'file_lampiran'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        // Upload file
        $fileWord = $request->file('file_word')->store('surat/word', 'public');
        $fileLamp = $request->file('file_lampiran')
                  ? $request->file('file_lampiran')->store('surat/lampiran', 'public')
                  : null;

        // Hitung deadline SLA = 24 jam kerja (skip Sabtu-Minggu)
        $deadline = $this->hitungSLA(now());

        $surat = Surat::create([
            'user_id'       => Auth::id(),
            'judul'         => $request->judul,
            'jenis'         => $request->jenis,
            'sifat'         => $request->sifat,
            'tujuan'        => $request->tujuan,
            'file_word'     => $fileWord,
            'file_lampiran' => $fileLamp,
            'tahap_sekarang'=> 1,
            'status'        => 'proses',
            'deadline_sla'  => $deadline,
        ]);

        // Inisialisasi semua tahapan
        $surat->initTahapan();

        // Set tahap 2 jadi 'proses' (siap diverifikasi arsiparis)
        $surat->tahapans()->where('tahap', 2)->update(['status' => 'proses']);

        // Update tahap_sekarang ke 2 karena tahap 1 sudah selesai otomatis
        $surat->update(['tahap_sekarang' => 2]);

        // Notif ke SEMUA admin (semua role)
        User::whereIn('role', ['admin', 'admin_aspirasi', 'admin_kasubbag_tu', 'admin_kepala_balai'])
            ->each(fn($a) => $a->notify(new SuratMasukNotification($surat)));

        return redirect()->route('user.surat.show', $surat)
                         ->with('success', 'Surat berhasil diajukan! Silakan pantau statusnya di bawah.');
    }

    public function show(Surat $surat)
    {
        // Pastikan hanya pemilik yang bisa lihat
        abort_if($surat->user_id !== Auth::id(), 403);

        $surat->load(['tahapans' => function($query) {
            $query->orderBy('tahap')->with('diprosesByUser');
        }]);

        return view('user.surat.show', compact('surat'));
    }

    // Hitung 24 jam kerja (skip Sabtu & Minggu)
    private function hitungSLA(Carbon $dari): Carbon
    {
        $deadline = $dari->copy();
        $jamDitambahkan = 0;

        // Tambah 24 jam, skip weekend
        while ($jamDitambahkan < 24) {
            $deadline->addHour();

            // Jika masuk weekend, skip ke Senin
            while ($deadline->isWeekend()) {
                $deadline->addDay()->startOfDay();
            }

            $jamDitambahkan++;
        }

        return $deadline;
    }

    /**
     * Request delete surat (butuh approval admin jika sedang diproses)
     */
    public function requestDelete(Request $request, Surat $surat)
    {
        // Pastikan hanya pemilik yang bisa request
        abort_if($surat->user_id !== Auth::id(), 403);

        // Cek apakah sudah ada request delete yang pending
        $existingRequest = SuratDeleteRequest::where('surat_id', $surat->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'Permintaan hapus sudah pernah dikirim dan masih menunggu approval admin.');
        }

        // Jika surat ditolak, selesai, atau SLA terlambat - bisa langsung hapus tanpa approval
        $bisaLangsungHapus = in_array($surat->status, ['ditolak', 'selesai']) || $surat->sla_status === 'terlambat';

        if ($bisaLangsungHapus) {
            // Validasi ringan, alasan opsional
            $request->validate([
                'alasan' => 'nullable|string|max:500',
            ]);

            // Langsung hapus surat
            $this->hapusSurat($surat);

            // Notifikasi ke semua admin (sekadar info)
            $alasan = $request->alasan ?? 'Penghapusan manual oleh user';
            User::whereIn('role', ['admin', 'admin_aspirasi', 'admin_kasubbag_tu', 'admin_kepala_balai'])
                ->each(function ($admin) use ($surat, $alasan) {
                    $admin->notify(new SuratDeletedNotification($surat, $alasan));
                });

            return redirect()->route('user.surat.index')
                ->with('success', 'Surat berhasil dihapus.');
        }

        // Jika sedang diproses - buat request delete dengan status pending
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        $deleteRequest = SuratDeleteRequest::create([
            'surat_id' => $surat->id,
            'user_id'  => Auth::id(),
            'alasan'   => $request->alasan,
            'status'   => 'pending',
        ]);

        // Kirim notifikasi ke semua admin
        User::whereIn('role', ['admin', 'admin_aspirasi', 'admin_kasubbag_tu', 'admin_kepala_balai'])
            ->each(function ($admin) use ($deleteRequest) {
                $admin->notify(new DeleteRequestNotification($deleteRequest));
            });

        return back()->with('info', 'Permintaan hapus telah dikirim. Menunggu persetujuan admin.');
    }

    /**
     * Hapus surat (setelah disetujui admin atau bisa langsung hapus)
     */
    private function hapusSurat(Surat $surat)
    {
        // Hapus file word
        if ($surat->file_word) {
            $wordPath = storage_path('app/public/' . $surat->file_word);
            if (file_exists($wordPath)) {
                @unlink($wordPath);
            }
        }

        // Hapus file lampiran
        if ($surat->file_lampiran) {
            $lampiranPath = storage_path('app/public/' . $surat->file_lampiran);
            if (file_exists($lampiranPath)) {
                @unlink($lampiranPath);
            }
        }

        // Hapus surat (tahapans akan terhapus otomatis karena cascade)
        $surat->delete();
    }

    /**
     * Upload ulang file perbaikan jika surat ditolak
     */
    public function reuploadFile(Request $request, Surat $surat)
    {
        // Pastikan hanya pemilik yang bisa upload ulang
        abort_if($surat->user_id !== Auth::id(), 403);

        // Hanya bisa jika status ditolak
        if (!$surat->bisaRevisi()) {
            return back()->with('error', 'Upload ulang hanya bisa dilakukan jika surat ditolak.');
        }

        $request->validate([
            'file_word'     => 'required|file|mimes:docx,doc|max:10240',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        if ($surat->file_word) {
            $wordPath = storage_path('app/public/' . $surat->file_word);
            if (file_exists($wordPath)) {
                @unlink($wordPath);
            }
        }
        if ($surat->file_lampiran) {
            $lampiranPath = storage_path('app/public/' . $surat->file_lampiran);
            if (file_exists($lampiranPath)) {
                @unlink($lampiranPath);
            }
        }

        // Upload file baru
        $fileWord = $request->file('file_word')->store('surat/word', 'public');
        $fileLamp = $request->file('file_lampiran')
                  ? $request->file('file_lampiran')->store('surat/lampiran', 'public')
                  : null;

        $tahapDitolak = $surat->tahap_sekarang;
        $tahapanDitolak = $surat->tahapans()->where('tahap', $tahapDitolak)->first();
        $namaTahapDitolak = $tahapanDitolak ? $tahapanDitolak->nama_tahap : 'Tahap ' . $tahapDitolak;

        $surat->update([
            'file_word'         => $fileWord,
            'file_lampiran'     => $fileLamp,
            'status'            => 'revisi',
            'status_revisi'     => true,
            'revisi_count'      => $surat->revisi_count + 1,
            'revisi_uploaded_at'=> now(),
        ]);

        $surat->tahapans()->where('tahap', $tahapDitolak)->update([
            'status' => 'proses',
        ]);

        // Notif ke SEMUA admin
        User::whereIn('role', ['admin', 'admin_aspirasi', 'admin_kasubbag_tu', 'admin_kepala_balai'])
            ->each(fn($a) => $a->notify(new FileRevisiNotification(
                $surat,
                $tahapDitolak,
                $namaTahapDitolak
            )));

        return back()->with('success', 'File perbaikan berhasil diupload! Menunggu review admin.');
    }
}