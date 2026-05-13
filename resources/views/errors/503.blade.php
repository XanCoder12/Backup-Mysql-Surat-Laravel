<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance — BP SUML</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream:    #FAF8F3;
            --cream-2:  #F3EFE6;
            --cream-3:  #E8E2D5;
            --ink:      #1C1917;
            --ink-2:    #44403C;
            --ink-3:    #78716C;
            --gold:     #B45309;
            --gold-2:   #D97706;
            --gold-bg:  #FEF3C7;
            --blue:     #1E40AF;
            --blue-bg:  #EFF6FF;
            --border:   rgba(28, 25, 23, 0.10);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--cream);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle grid pattern background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 48px 48px;
            z-index: 0;
            pointer-events: none;
        }

        /* Top warm gradient wash */
        body::after {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 40vh;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(251, 191, 36, 0.12), transparent);
            z-index: 0;
            pointer-events: none;
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 680px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2.5rem;
        }

        /* ---- Header / Logo bar ---- */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .top-bar .logo-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .top-bar .logo-area img {
            height: 36px;
            width: auto;
        }

        .top-bar .logo-area .org-name {
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-2);
            letter-spacing: 0.03em;
            line-height: 1.3;
        }

        .top-bar .org-name span {
            display: block;
            font-weight: 300;
            font-size: 11px;
            color: var(--ink-3);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: var(--gold-bg);
            border: 1px solid rgba(180, 83, 9, 0.2);
            font-size: 12px;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: 0.04em;
        }

        .status-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold-2);
            animation: blink 2s infinite ease-in-out;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* ---- Main card ---- */
        .main-card {
            width: 100%;
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3rem 3rem 2.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 12px 40px rgba(0,0,0,0.07);
            position: relative;
            overflow: hidden;
        }

        /* Decorative corner ornament */
        .main-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle at top right, rgba(251, 191, 36, 0.10), transparent 70%);
            pointer-events: none;
        }

        .card-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        .card-eyebrow .line {
            flex: 1;
            height: 1px;
            background: var(--cream-3);
        }

        .card-eyebrow .label {
            font-size: 11px;
            font-weight: 600;
            color: var(--ink-3);
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        .icon-block {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: var(--cream);
            border: 1px solid var(--cream-3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
        }

        .icon-wrap i {
            font-size: 28px;
            color: var(--blue);
        }

        .icon-wrap .badge {
            position: absolute;
            bottom: -6px;
            right: -6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--gold-2);
            border: 2px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrap .badge i {
            font-size: 9px;
            color: #fff;
        }

        .headline {
            font-family: 'Lora', serif;
            font-size: clamp(26px, 5vw, 38px);
            font-weight: 700;
            line-height: 1.2;
            color: var(--ink);
            letter-spacing: -0.02em;
        }

        .headline em {
            font-style: italic;
            color: var(--blue);
        }

        .subtext {
            font-size: 15px;
            line-height: 1.75;
            color: var(--ink-3);
            margin-top: 0.875rem;
        }

        /* ---- Progress ---- */
        .progress-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.625rem;
        }

        .progress-label span {
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-3);
            letter-spacing: 0.05em;
        }

        .progress-label .pct {
            color: var(--blue);
        }

        .progress-track {
            width: 100%;
            height: 6px;
            background: var(--cream-2);
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 68%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--blue) 0%, #3B82F6 100%);
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0;
            width: 40%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5));
            animation: sweep 2.5s infinite linear;
        }

        @keyframes sweep {
            0% { transform: translateX(-200%); }
            100% { transform: translateX(300%); }
        }

        /* ---- Info grid ---- */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 1.5rem;
        }

        .info-cell {
            padding: 1rem 1.125rem;
            border-radius: 12px;
            background: var(--cream);
            border: 1px solid var(--cream-3);
        }

        .info-cell .cell-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-cell .cell-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
        }

        .info-cell.status .cell-label { color: var(--gold); }
        .info-cell.eta .cell-label    { color: var(--blue); }
        .info-cell.scope .cell-label  { color: #059669; }

        /* ---- Footer ---- */
        .footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            width: 100%;
        }

        .divider {
            width: 40px;
            height: 1px;
            background: var(--cream-3);
        }

        .footer-logo img {
            height: 28px;
            opacity: 0.45;
            filter: grayscale(0.6);
            transition: opacity 0.3s, filter 0.3s;
            cursor: pointer;
        }

        .footer-logo img:hover {
            opacity: 0.9;
            filter: grayscale(0);
        }

        .footer-copy {
            font-size: 11px;
            font-weight: 500;
            color: var(--ink-3);
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .footer-links {
            display: flex;
            gap: 1.25rem;
        }

        .footer-links a {
            color: var(--ink-3);
            font-size: 17px;
            text-decoration: none;
            transition: color 0.2s;
            line-height: 1;
        }

        .footer-links a:hover {
            color: var(--ink);
        }

        /* ---- Responsive ---- */
        @media (max-width: 560px) {
            .main-card {
                padding: 2rem 1.5rem 2rem;
            }

            .icon-block {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .top-bar .org-name {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">

        <!-- Top bar -->
        <div class="top-bar">
            <div class="logo-area">
                <img src="{{ asset('images/BP_SUML2.png') }}" alt="Logo BP SUML">
                <div class="org-name">
                    Balai Pengelolaan SUML
                    <span>Kementerian Perdagangan RI</span>
                </div>
            </div>
            <div class="status-pill">
                <span class="dot"></span>
                Maintenance
            </div>
        </div>

        <!-- Main card -->
        <div class="main-card">

            <div class="card-eyebrow">
                <span class="label">Pemberitahuan Sistem</span>
                <span class="line"></span>
            </div>

            <div class="icon-block">
                <div class="icon-wrap">
                    <i class="bi bi-tools"></i>
                    <div class="badge">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                </div>
                <div>
                    <h1 class="headline">
                        Sistem Sedang <em>Ditingkatkan</em>
                    </h1>
                    <p class="subtext">
                        Tim IT kami sedang melakukan pembaruan rutin untuk memastikan sistem berjalan lebih cepat, andal, dan aman. Mohon bersabar sebentar — kami akan segera kembali.
                    </p>
                </div>
            </div>

            <div class="progress-section">
                <div class="progress-label">
                    <span>Progres Pembaruan</span>
                    <span class="pct">68%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill"></div>
                </div>

                <div class="info-grid">
                    <div class="info-cell status">
                        <div class="cell-label">Status</div>
                        <div class="cell-value">Sedang Dikerjakan</div>
                    </div>
                    <div class="info-cell eta">
                        <div class="cell-label">Estimasi Selesai</div>
                        <div class="cell-value">± 30 Menit</div>
                    </div>
                    <div class="info-cell scope">
                        <div class="cell-label">Dampak</div>
                        <div class="cell-value">Semua Fitur</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="divider"></div>
            <div class="footer-logo">
                <img src="{{ asset('images/BP_SUML2.png') }}" alt="Logo">
            </div>
            <p class="footer-copy">&copy; {{ date('Y') }} Balai Pengelolaan SUML</p>
            <div class="footer-links">
                <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                <a href="#" aria-label="Telegram"><i class="bi bi-telegram"></i></a>
                <a href="#" aria-label="Email"><i class="bi bi-envelope"></i></a>
            </div>
        </div>

    </div>
</body>
</html>