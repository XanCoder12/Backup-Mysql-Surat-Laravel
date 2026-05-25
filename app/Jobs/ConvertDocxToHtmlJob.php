<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use App\Services\DocxToHtmlConverter;
use App\Services\HtmlSanitizer;

class ConvertDocxToHtmlJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 menit max
    public $tries = 3;

    public function __construct(
        private string $filePath,
        private string $cacheKey,
    ) {}

    public function handle(): void
    {
        try {
            $converter = new DocxToHtmlConverter($this->filePath);
            $htmlRaw = $converter->convert();
            $htmlContent = HtmlSanitizer::clean($htmlRaw);
            
            // Simpan ke cache (7 hari)
            Cache::put($this->cacheKey, $htmlContent, now()->addDays(7));
        } catch (\Exception $e) {
            // Log error tapi jangan crash
            \Illuminate\Support\Facades\Log::error('DOCX conversion failed', [
                'file' => $this->filePath,
                'error' => $e->getMessage(),
            ]);
            
            // Simpan error message ke cache juga
            Cache::put($this->cacheKey, '<p style="color:red;">Gagal convert file: ' . htmlspecialchars($e->getMessage()) . '</p>', now()->addHours(1));
        }
    }
}
