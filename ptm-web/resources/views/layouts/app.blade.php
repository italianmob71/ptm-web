<!DOCTYPE html>
<html lang="en" class="h-full" data-theme="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>{{ config('app.name', 'PTM') }}@if(isset($title) && $title) | {{ $title }}@endif</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=EB+Garamond:wght@400;500;600;700;400italic;500italic&family=JetBrains+Mono:wght@400;500;600&family=Noto+Serif+Hebrew:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased" style="background-color: var(--color-bg); color: var(--color-text);">
    <header class="sticky top-0 z-50 border-b" style="background-color: var(--color-surface); border-color: var(--color-border);">
        <div class="mx-auto max-w-7xl px-4 h-16 flex items-center">
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="{{ route('home') }}" class="w-8 h-8 flex items-center justify-center rounded text-sm font-bold" style="background-color: var(--color-accent); color: var(--color-text-inv);">
                    P
                </a>
                <a href="{{ route('home') }}" class="font-semibold text-lg tracking-tight">PTM</a>
            </div>

            <nav class="hidden md:flex flex-1 justify-center gap-8 text-sm">
                <a href="{{ route('home') }}" class="hover:opacity-75" style="color: var(--color-text-muted);">Home</a>
                <a href="#about" class="hover:opacity-75" style="color: var(--color-text-muted);">About</a>
                <a href="#resources" class="hover:opacity-75" style="color: var(--color-text-muted);">Resources</a>
            </nav>

            <div class="ml-auto md:ml-0">
                <button id="themeToggle" 
                        class="px-3 py-1.5 text-sm rounded-full border flex items-center gap-2"
                        style="border-color: var(--color-border); background-color: var(--color-surface);"
                        aria-label="Toggle theme">
                    <span id="themeIcon">🌙</span>
                    <span id="themeLabel" class="hidden sm:inline">Dark</span>
                </button>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t py-8 text-center text-sm mt-12" style="border-color: var(--color-border); color: var(--color-text-muted);">
        <div class="mx-auto max-w-7xl px-4">
            © {{ date('Y') }} Project Truth Ministries
        </div>
    </footer>

    <script>
        (function () {
            const KEY = 'ptm-theme';
            const root = document.documentElement;
            const btn = document.getElementById('themeToggle');
            const icon = document.getElementById('themeIcon');
            const label = document.getElementById('themeLabel');

            function styleButtonForTarget(target) {
                if (target === 'light') {
                    if (icon) icon.textContent = '☀️';
                    if (label) label.textContent = 'Light';
                    if (btn) {
                        btn.style.backgroundColor = 'oklch(0.94 0.020 83)';
                        btn.style.borderColor = 'oklch(0.62 0.030 65)';
                        btn.style.color = 'oklch(0.22 0.030 45)';
                    }
                } else {
                    if (icon) icon.textContent = '🌙';
                    if (label) label.textContent = 'Dark';
                    if (btn) {
                        btn.style.backgroundColor = 'oklch(0.12 0.010 40)';
                        btn.style.borderColor = 'oklch(0.24 0.015 50)';
                        btn.style.color = 'oklch(0.93 0.015 80)';
                    }
                }
            }

            function applyTheme(current) {
                root.setAttribute('data-theme', current);
                const target = current === 'dark' ? 'light' : 'dark';
                styleButtonForTarget(target);
            }

            const saved = localStorage.getItem(KEY);
            const initial = saved || 'dark';
            applyTheme(initial);

            if (btn) {
                btn.addEventListener('click', function () {
                    const current = root.getAttribute('data-theme') || 'dark';
                    const next = current === 'dark' ? 'light' : 'dark';
                    localStorage.setItem(KEY, next);
                    applyTheme(next);
                });
            }
        })();
    </script>
</body>
</html>
