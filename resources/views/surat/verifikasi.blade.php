<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat - Surat Metrologi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --success: #10b981;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
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
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 40px;
            text-align: center;
            border: 1px solid var(--gray-200);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background-color: #ecfdf5;
            color: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .icon-box svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--gray-900);
        }

        p.subtitle {
            color: var(--gray-500);
            margin-bottom: 32px;
            font-size: 15px;
        }

        .detail-card {
            background-color: var(--gray-50);
            border-radius: 16px;
            padding: 24px;
            text-align: left;
            margin-bottom: 32px;
            border: 1px solid var(--gray-100);
        }

        .detail-item {
            margin-bottom: 16px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }

        .value {
            font-size: 15px;
            font-weight: 500;
            color: var(--gray-900);
        }

        .footer {
            font-size: 13px;
            color: var(--gray-500);
        }

        .brand {
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: var(--success);
            color: white;
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div class="status-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
            </svg>
            Surat Terverifikasi Asli
        </div>

        <h1>{{ $surat->judul }}</h1>
        <p class="subtitle">Dokumen ini valid dan terdaftar dalam sistem.</p>

        <div class="detail-card">
            <div class="detail-item">
                <span class="label">Nomor Surat</span>
                <div class="value">{{ $surat->nomor_surat ?? 'Dalam Proses' }}</div>
            </div>
            <div class="detail-item">
                <span class="label">Jenis Surat</span>
                <div class="value">{{ $surat->jenis_label }}</div>
            </div>
            <div class="detail-item">
                <span class="label">Tanggal Keluar</span>
                <div class="value">{{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : '-' }}</div>
            </div>
            <div class="detail-item">
                <span class="label">Pihak Pengusul</span>
                <div class="value">{{ $surat->user->name }}</div>
            </div>
        </div>

        <div class="footer">
            Sistem Informasi <a href="/" class="brand">Surat Metrologi</a><br>
            Balai Pengelola - SUML
        </div>
    </div>
</body>
</html>
