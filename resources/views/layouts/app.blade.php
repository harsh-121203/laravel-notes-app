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

    <!-- Central Design Tokens & Theme Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Early Theme Loader to prevent flash of wrong theme -->
    <script>
        (function() {
            try {
                function isColorDark(hex) {
                    if (!hex || typeof hex !== 'string') return false;
                    var h = hex.trim().replace('#', '');
                    if (h.length === 3) h = h.split('').map(function(c) { return c + c; }).join('');
                    if (h.length !== 6) return false;
                    var r = parseInt(h.substr(0, 2), 16), g = parseInt(h.substr(2, 2), 16), b = parseInt(h.substr(4, 2), 16);
                    return ((0.299 * r + 0.587 * g + 0.114 * b) / 255) < 0.5;
                }

                var saved = localStorage.getItem('workspace_custom_theme');
                if (saved) {
                    var parsed = JSON.parse(saved);
                    if (parsed && parsed.vars) {
                        var root = document.documentElement;
                        for (var key in parsed.vars) {
                            root.style.setProperty(key, parsed.vars[key]);
                        }
                        var canvas = parsed.vars['--bg-canvas'] || '#fafafa';
                        var surface = parsed.vars['--bg-surface'] || '#ffffff';
                        var accent = parsed.vars['--accent'] || '#111827';
                        var isDark = isColorDark(canvas) || isColorDark(surface);
                        var isAccDark = isColorDark(accent);

                        root.style.setProperty('--color-scheme', isDark ? 'dark' : 'light');
                        root.style.setProperty('--calendar-icon-filter', isDark ? 'invert(1)' : 'none');
                        root.style.setProperty('--border-subtle', isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)');
                        root.style.setProperty('--btn-hover-bg', isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.04)');
                        root.style.setProperty('--item-hover-bg', isDark ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.02)');
                        root.style.setProperty('--badge-bg', isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)');
                        root.style.setProperty('--accent-contrast', isAccDark ? '#ffffff' : '#111827');
                        root.style.colorScheme = isDark ? 'dark' : 'light';
                    }
                }
            } catch(e) {}
        })();
    </script>

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
            <!-- Theme Studio Settings Trigger -->
            <button type="button" class="btn-press" onclick="openThemeModal()" style="font-size: 0.775rem; padding: 0.3rem 0.65rem;" title="Customize App Theme & Colors">
                🎨 Theme
            </button>
            <span class="academic-badge">Laravel MVC</span>
            <span>Local Database</span>
        </div>
    </header>

    <!-- Theme Studio Modal -->
    @include('partials.theme-modal')

    <!-- Fixed Floating Toast Notification -->
    <div class="toast-slot">
        @if(session('success'))
            <div class="toast-msg">
                <span>{{ session('success') }}</span>
                <span style="cursor: pointer; opacity: 0.7; margin-left: auto;" onclick="this.parentElement.remove();">&times;</span>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-msg" style="background: #dc2626;">
                <span>{{ session('error') }}</span>
                <span style="cursor: pointer; opacity: 0.7; margin-left: auto;" onclick="this.parentElement.remove();">&times;</span>
            </div>
        @endif

        @if($errors->any())
            <div class="toast-msg" style="background: #dc2626;">
                <span>{{ $errors->first() }}</span>
                <span style="cursor: pointer; opacity: 0.7; margin-left: auto;" onclick="this.parentElement.remove();">&times;</span>
            </div>
        @endif
    </div>

    <!-- Content Viewport -->
    <main class="main-viewport">
        @yield('content')
    </main>

    <footer class="app-foot">
        <p>Built with Laravel 12 & Native Blade • Modular Architecture</p>
    </footer>

    <!-- Central Workspace Controller Script -->
    <script src="{{ asset('js/workspace.js') }}"></script>
    @yield('scripts')
</body>
</html>
