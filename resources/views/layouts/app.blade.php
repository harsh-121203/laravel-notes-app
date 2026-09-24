<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Workspace')</title>
    
    <!-- Clean Swiss / Neo-grotesque font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Clean Minimalist Palette (Todoist / Linux GTK inspired) */
            --bg-canvas: #fafafa;
            --bg-surface: #ffffff;
            --border-subtle: #f0f0f0;
            --border-line: #e5e5e5;
            --border-active: #262626;

            --text-title: #171717;
            --text-body: #404040;
            --text-muted: #737373;
            --text-faint: #a3a3a3;

            --accent: #dc2626;
            --accent-soft: #fef2f2;
            --forest: #166534;
            --forest-soft: #f0fdf4;
            --amber-soft: #fffbeb;

            --radius-xs: 4px;
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            overflow-y: scroll; /* Prevents scrollbar layout jumps */
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Minimal Header (No emojis, no heavy badges) */
        header.app-topbar {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-line);
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .app-title-mark {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-title);
            letter-spacing: -0.02em;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dot-indicator {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            display: inline-block;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .academic-badge {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            border: 1px solid var(--border-line);
            padding: 0.2rem 0.55rem;
            border-radius: var(--radius-xs);
            background: #fdfdfd;
        }

        /* Toast Container fixed without moving DOM flow */
        .toast-slot {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }

        .toast-msg {
            pointer-events: auto;
            background: #171717;
            color: #ffffff;
            font-size: 0.825rem;
            font-weight: 500;
            padding: 0.65rem 1.1rem;
            border-radius: var(--radius-sm);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from { transform: translateY(8px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Buttons with tactile click depression */
        .btn-press {
            appearance: none;
            border: 1px solid var(--border-line);
            background: var(--bg-surface);
            color: var(--text-title);
            font-family: inherit;
            font-size: 0.825rem;
            font-weight: 600;
            padding: 0.45rem 0.85rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: background 0.1s ease, border-color 0.1s ease, transform 0.08s ease, box-shadow 0.08s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            user-select: none;
        }
        .btn-press:hover {
            background: #f5f5f5;
            border-color: #d4d4d4;
        }
        .btn-press:active {
            transform: translateY(1px);
            box-shadow: 0 0 0 rgba(0,0,0,0);
        }

        .btn-primary-press {
            background: #171717;
            color: #ffffff;
            border-color: #171717;
        }
        .btn-primary-press:hover {
            background: #262626;
            border-color: #262626;
        }
        .btn-primary-press:active {
            transform: translateY(1px);
            background: #000000;
        }

        .btn-danger-icon {
            background: transparent;
            border: none;
            color: var(--text-faint);
            cursor: pointer;
            padding: 0.25rem 0.4rem;
            border-radius: var(--radius-xs);
            font-size: 0.85rem;
            line-height: 1;
            transition: all 0.12s ease;
        }
        .btn-danger-icon:hover {
            color: var(--accent);
            background: var(--accent-soft);
        }
        .btn-danger-icon:active {
            transform: scale(0.92);
        }

        /* Main Workspace Canvas */
        main.main-viewport {
            flex: 1;
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem 3.5rem;
        }

        footer.app-foot {
            border-top: 1px solid var(--border-line);
            padding: 1rem 0;
            background: var(--bg-surface);
            text-align: center;
            font-size: 0.775rem;
            color: var(--text-muted);
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Top Bar -->
    <header class="app-topbar">
        <div class="topbar-left">
            <a href="{{ route('dashboard') }}" class="app-title-mark">
                <span class="dot-indicator"></span>
                <span>Workspace</span>
            </a>
        </div>

        <div class="topbar-right">
            <span class="academic-badge">Laravel MVC</span>
            <span>Local Database</span>
        </div>
    </header>

    <!-- Fixed Floating Toast Notification (Does not push content down) -->
    @if(session('success'))
        <div class="toast-slot">
            <div class="toast-msg">
                <span>{{ session('success') }}</span>
                <span style="cursor: pointer; opacity: 0.7;" onclick="this.parentElement.remove();">&times;</span>
            </div>
        </div>
    @endif

    <!-- Content Viewport -->
    <main class="main-viewport">
        @yield('content')
    </main>

    <footer class="app-foot">
        <p>Built with Laravel 12 & Native Blade • Minimalist Two-Column Architecture</p>
    </footer>

    @yield('scripts')
</body>
</html>
