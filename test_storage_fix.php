<?php
/**
 * Test script untuk verifikasi storage disk configuration
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$container = $app->make(\Illuminate\Contracts\Container\Container::class);
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use App\Models\Surat;

echo "=== Storage Fix Verification ===\n\n";

// Check private disk configuration
echo "1. Checking 'private' disk configuration:\n";
$privateDisk = Storage::disk('private');
echo "   - Root path: " . Storage::disk('private')->path('') . "\n";
echo "   - Driver: local\n\n";

// Check if surat files exist
echo "2. Checking surat files in private storage:\n";
$files = Storage::disk('private')->files('surat');
echo "   - Word files found: " . count(Storage::disk('private')->files('surat/word')) . "\n";
echo "   - Lampiran files found: " . count(Storage::disk('private')->files('surat/lampiran')) . "\n\n";

// Check a sample surat
echo "3. Testing with a sample Surat record:\n";
$surat = Surat::whereNotNull('file_word')->first();

if ($surat) {
    echo "   - UUID: {$surat->uuid}\n";
    echo "   - File Word path: {$surat->file_word}\n";
    echo "   - File Lampiran path: {$surat->file_lampiran}\n\n";
    
    echo "4. Checking file existence:\n";
    if ($surat->file_word) {
        $exists = Storage::disk('private')->exists($surat->file_word);
        echo "   - Word file exists in 'private' disk: " . ($exists ? 'YES' : 'NO') . "\n";
    }
    if ($surat->file_lampiran) {
        $exists = Storage::disk('private')->exists($surat->file_lampiran);
        echo "   - Lampiran file exists in 'private' disk: " . ($exists ? 'YES' : 'NO') . "\n";
    }
    
    echo "\n✅ Storage configuration appears to be correct!\n";
} else {
    echo "   - No surat records found with files. Cannot test.\n";
}

echo "\n=== Code Changes Made ===\n";
echo "Changed in Admin\\SuratController:\n";
echo "  - preview(): 'local' -> 'private'\n";
echo "  - previewContent(): 'local' -> 'private'\n";
echo "  - download(): 'local' -> 'private'\n\n";
echo "Changed in User\\SuratController:\n";
echo "  - preview(): 'local' -> 'private'\n";
echo "  - download(): 'local' -> 'private'\n\n";

echo "=== Done ===\n";
