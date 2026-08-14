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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased flex flex-col" style="background-color: var(--color-bg); color: var(--color-text);">

    {{-- Global SVG icon sprite — hidden, zero layout cost. Use via <svg class="icon"><use xlink:href="#icon-name"></use></svg> --}}
    @include('partials.icon-sprite')

    <header class="sticky top-0 z-50 border-b shrink-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
        <div class="mx-auto max-w-7xl px-4 h-16 flex items-center">
            <div class="flex items-center flex-shrink-0">
                <a href="{{ route('home') }}" class="block" aria-label="Project Truth Ministries">
                    <img src="{{ asset('images/site/ptm-dark-menu.png') }}" alt="PTM" class="header-logo header-logo-dark" />
                    <img src="{{ asset('images/site/ptm-light-menu.png') }}" alt="PTM" class="header-logo header-logo-light" />
                </a>
            </div>

            <nav class="hidden md:flex flex-1 justify-center gap-8 text-sm" aria-label="Main navigation">
                <x-nav-links :showDropdowns="true" variant="desktop" />
            </nav>

            <div class="flex items-center gap-3 ml-auto md:ml-0 shrink-0">
                {{-- Mobile hamburger button --}}
                <button id="mobileMenuToggle"
                        class="md:hidden p-2 rounded-lg border"
                        style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                        aria-label="Toggle menu"
                        aria-expanded="false">
                    <svg class="w-6 h-6 block" id="menu-icon-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="w-6 h-6 hidden" id="menu-icon-close" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <button id="themeToggle"
                        class="px-3 py-1.5 text-sm rounded-full border flex items-center gap-2"
                        style="border-color: var(--color-border); background-color: var(--color-surface);"
                        aria-label="Toggle theme">
                    <span id="themeIcon">🌙</span>
                    <span id="themeLabel" class="hidden sm:inline">Dark</span>
                </button>

                {{-- User menu (desktop) --}}
                <span class="hidden md:block">
                    <x-nav-links variant="usermenu" />
                </span>
            </div>
        </div>

        {{-- Mobile menu panel (slides down) --}}
        <div id="mobile-menu-container"
             class="md:hidden overflow-hidden"
             style="display:none; border-top: 1px solid var(--color-border); background-color: var(--color-surface);">
            <div class="px-4 py-2">
                <x-nav-links :showDropdowns="true" variant="mobile" />
                {{-- User menu (mobile) --}}
                <div style="border-bottom: 1px solid var(--color-border); padding-top: 0.5rem; padding-bottom: 0.5rem;">
                    <x-nav-links variant="usermenu" />
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="shrink-0" style="background-color: var(--color-footer-bg); border-top: 1px solid var(--color-footer-border);">
        <div class="mx-auto max-w-7xl px-4 py-3">
            <!-- Footer nav - compact, wraps on mobile -->
            <div class="flex flex-wrap justify-center gap-4 text-xs mb-2">
                <x-nav-links :showDropdowns="false" variant="desktop" />
            </div>

            <!-- Copyright - inline with nav on mobile, separate line on desktop -->
            <div class="text-center text-xs" style="color: var(--color-text-muted);">
                © 2024 - {{ date('Y') }} Project Truth Ministries
            </div>
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

        // Mobile menu toggle
        (function () {
            const toggle = document.getElementById('mobileMenuToggle');
            const panel = document.getElementById('mobile-menu-container');
            const iconOpen = document.getElementById('menu-icon-open');
            const iconClose = document.getElementById('menu-icon-close');
            if (!toggle || !panel) return;

            toggle.addEventListener('click', function () {
                const isOpen = panel.style.display !== 'none';
                if (isOpen) {
                    panel.style.display = 'none';
                    iconOpen.classList.remove('hidden');
                    iconOpen.classList.add('block');
                    iconClose.classList.remove('block');
                    iconClose.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    panel.style.display = 'block';
                    iconOpen.classList.remove('block');
                    iconOpen.classList.add('hidden');
                    iconClose.classList.remove('hidden');
                    iconClose.classList.add('block');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        })();
    </script>
</body>
</html>
