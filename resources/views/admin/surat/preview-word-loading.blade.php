<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses Preview - {{ $surat->judul }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 400px;
        }
        h1 { color: #333; font-size: 24px; margin-bottom: 20px; }
        p { color: #666; font-size: 16px; margin-bottom: 30px; }
        
        .spinner {
            border: 4px solid #f0f0f0;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .info {
            background: #f0f4f8;
            border-left: 4px solid #2563eb;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            text-align: left;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Memproses Preview</h1>
        <p>File sedang dikonversi. Mohon tunggu sebentar...</p>
        <div class="info">
            <strong>Dokumen:</strong> {{ $surat->judul }}<br>
            <strong>File:</strong> {{ $fileName }}
        </div>
    </div>

    <script>
        const cacheKey = '{{ $cacheKey }}';
        const pollInterval = 2000; // Poll setiap 2 detik
        let pollCount = 0;
        const maxPolls = 300; // Max 10 menit (300 x 2 detik)

        function checkConversion() {
            pollCount++;
            
            fetch('{{ route("admin.surat.preview-status", "") }}/' + cacheKey)
                .then(response => response.json())
                .then(data => {
                    if (data.ready) {
                        // Preview siap, tampilkan
                        document.body.innerHTML = data.html;
                    } else if (pollCount < maxPolls) {
                        // Belum siap, poll lagi
                        setTimeout(checkConversion, pollInterval);
                    } else {
                        // Timeout
                        document.body.innerHTML = '<div class="container"><h1>⏱️ Waktu Habis</h1><p>Proses konversi memakan waktu terlalu lama. Coba download file alih-alih.</p><a href="{{ route("admin.surat.download", [$surat, $tipe]) }}" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:6px;">Download File</a></div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    setTimeout(checkConversion, pollInterval);
                });
        }

        // Start polling
        checkConversion();
    </script>
</body>
</html>
