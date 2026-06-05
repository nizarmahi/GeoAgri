{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GeoAgri — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        /* ─── Variables ─────────────────────────────────────── */
        :root {
            --sidebar-w: 210px;
            --header-h: 56px;

            --bg: #f5f6fa;
            --bg-white: #ffffff;
            --bg-muted: #f0f1f6;

            --sidebar-bg: #ffffff;
            --sidebar-border: #e8eaf0;

            --primary: #2d3bde;
            --primary-10: rgba(45, 59, 222, .08);
            --primary-20: rgba(45, 59, 222, .16);

            --green: #16a34a;
            --green-10: rgba(22, 163, 74, .10);
            --red: #dc2626;
            --red-10: rgba(220, 38, 38, .10);
            --orange: #ea580c;
            --orange-10: rgba(234, 88, 12, .10);
            --amber: #d97706;

            --text: #111827;
            --text-muted: #6b7280;
            --text-light: #9ca3af;

            --border: #e5e7eb;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
            --shadow: 0 4px 16px rgba(0, 0, 0, .08);

            --font: 'Sora', sans-serif;
            --mono: 'DM Mono', monospace;
        }

        /* ─── Reset ─────────────────────────────────────────── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: var(--font);
            font-size: 14px;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ─── App Shell ─────────────────────────────────────── */
        .app-shell {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ─── Sidebar ───────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: relative;
            z-index: 50;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .brand-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.4px;
        }

        .brand-sub {
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            margin-top: 1px;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 12px 10px;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }

        .nav-item:hover {
            background: var(--bg-muted);
            color: var(--text);
        }

        .nav-item.active {
            background: var(--primary-10);
            color: var(--primary);
        }

        .nav-item .nav-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: .7;
        }

        .nav-item.active .nav-icon {
            opacity: 1;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--sidebar-border);
        }

        /* ─── Content Area ──────────────────────────────────── */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ─── Top Header ────────────────────────────────────── */
        .top-header {
            height: var(--header-h);
            background: var(--bg-white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            flex-shrink: 0;
        }

        .header-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--green);
            background: var(--green-10);
            padding: 3px 10px;
            border-radius: 20px;
            margin-left: 12px;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse 1.8s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .5;
                transform: scale(.8);
            }
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: background .15s, color .15s;
        }

        .icon-btn:hover {
            background: var(--bg-muted);
            color: var(--text);
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
        }

        /* ─── Page Body ─────────────────────────────────────── */
        .page-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        /* ─── Utility ───────────────────────────────────────── */
        .card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: opacity .15s, transform .1s;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            opacity: .88;
        }

        .btn-outline {
            background: transparent;
            color: var(--text);
            border-color: var(--border);
        }

        .btn-outline:hover {
            background: var(--bg-muted);
        }

        /* Badge status */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            font-family: var(--mono);
            letter-spacing: .3px;
        }

        .badge-valid {
            background: var(--green-10);
            color: var(--green);
        }

        .badge-null {
            background: var(--orange-10);
            color: var(--orange);
        }

        .badge-error {
            background: var(--red-10);
            color: var(--red);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-shell">

        {{-- ── Sidebar ────────────────────────────────── --}}
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-name">GeoAgri</div>
                <div class="brand-sub">Analytic Curator</div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('analisis-harga') }}"
                    class="nav-item {{ request()->routeIs('analisis-harga') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    Analisis Harga
                </a>

                <a href="{{ route('analisis-null') }}"
                    class="nav-item {{ request()->routeIs('analisis-null') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 20V10M12 20V4M6 20v-6" />
                    </svg>
                    Analisis NULL
                </a>

                <a href="{{ route('analisis-pasar') }}"
                    class="nav-item {{ request()->routeIs('analisis-pasar') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3h18v6H3zM6 9v12M18 9v12M9 21h6" />
                        <circle cx="12" cy="15" r="2" />
                    </svg>
                    Analisis Pasar
                </a>

                <a href="{{ route('data.index') }}" class="nav-item {{ request()->routeIs('data.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9h18M3 15h18M9 3v18M15 3v18" stroke-linecap="round" />
                    </svg>
                    Data
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="#" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01" />
                    </svg>
                    Bantuan
                </a>
                <a href="#" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
                    </svg>
                    Keluar
                </a>
            </div>
        </aside>

        {{-- ── Content Area ────────────────────────────── --}}
        <div class="content-area">
            <header class="top-header">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="header-title">GeoAgri</span>
                    {{-- <span class="live-badge">
                        <span class="live-dot"></span>
                        LIVE PULSE
                    </span> --}}
                </div>
                <div class="header-actions">
                    {{-- Notif --}}
                    <button class="icon-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                    </button>
                    {{-- Settings --}}
                    <button class="icon-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="3" />
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                        </svg>
                    </button>
                    <div class="avatar">A</div>
                </div>
            </header>

            <main class="page-body">
                @yield('content')
            </main>
        </div>

    </div>

    @stack('scripts')
</body>

</html>
