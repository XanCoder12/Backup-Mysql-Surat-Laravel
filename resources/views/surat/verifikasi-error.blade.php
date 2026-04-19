<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Gagal - Surat Metrologi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-900: #111827;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            border: 1px solid var(--gray-200);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background-color: #fee2e2;
            color: var(--danger);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 12px;
        }

        p {
            color: var(--gray-500);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .btn {
            background-color: var(--gray-900);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="40" height="40">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <h1>Data Tidak Ditemukan</h1>
        <p>Maaf, dokumen dengan kode verifikasi tersebut tidak terdaftar di sistem kami atau sudah tidak berlaku.</p>
        <a href="/" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>
