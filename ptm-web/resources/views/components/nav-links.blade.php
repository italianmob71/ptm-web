@php
    $links = [
        ['label' => 'Home', 'href' => route('home')],
        [
            'label' => 'About',
            'href' => route('about'),
            'children' => [
                ['label' => 'Our Mission', 'href' => route('about')],
                ['label' => 'Team', 'href' => route('team')],
            ],
        ],
        [
            'label' => 'Resources',
            'href' => route('resources'),
            'children' => [
                ['label' => 'Truths Revealed Blog', 'href' => route('blog.index')],
                ['label' => 'Articles', 'href' => route('articles.index')],
                ['label' => 'Truth Topics', 'href' => route('topics.index')],
                ['label' => 'Book Recommendations', 'href' => route('books.index')],
            ],
        ],
        ['label' => 'Studies', 'href' => '#studies', 'children' => [
            ['label' => 'Cochin Hebrew New Testament', 'href' => '#cochin', 'children' => [
                ['label' => 'Matthew', 'href' => '#cochin-matthew'],
                ['label' => 'James', 'href' => '#cochin-james'],
                ['label' => 'Revelation', 'href' => '#cochin-revelation'],
            ]],
            ['label' => 'Renewed Covenant', 'href' => '#renewed-covenant'],
            ['label' => "Bryan's Travel Notes", 'href' => route('travel-notes.index')],
        ]],
        ['label' => 'Events', 'href' => route('events')],
        ['label' => 'Contact', 'href' => '#contact'],
    ];

    // Super-admin only links (security level 9)
    if (auth()->check() && auth()->user()->isSuperAdmin()) {
        $links[] = [
            'label' => 'Admin',
            'href' => '#',
            'children' => [
                ['label' => 'Books', 'href' => route('admin.books.index')],
                ['label' => 'Authors', 'href' => route('admin.authors.index')],
                ['label' => 'Blog Posts', 'href' => route('admin.blog.index')],
                ['label' => 'Images', 'href' => route('admin.images.index')],
                ['label' => 'Articles', 'href' => route('admin.articles.index')],
                ['label' => 'PDFs', 'href' => route('admin.pdfs.index')],
                ['label' => 'Videos', 'href' => route('admin.videos.index')],
                ['label' => 'Travel Notes', 'href' => route('admin.travel-notes.index')],
            ],
        ];
    }

    $showDropdowns = $showDropdowns ?? true;
@endphp

@php
    // Determine which view to render: 'desktop' or 'mobile'
    $variant = $variant ?? 'desktop';
@endphp

@if ($variant === 'desktop')
{{-- ===================================================== --}}
{{-- DESKTOP NAV (hidden on mobile)                         --}}
{{-- ===================================================== --}}
<nav class="hidden md:flex flex-1 justify-center gap-8 text-sm" aria-label="Main navigation">
    @foreach ($links as $link)
        @if (isset($link['children']) && $showDropdowns)
            <div class="relative" x-data="{ open: false }">
                <button
                    class="hover:opacity-75 flex items-center gap-1"
                    style="color: var(--color-text-muted);"
                    @click="open = !open"
                    @keydown.escape="open = false"
                    aria-haspopup="true"
                    :aria-expanded="open.toString()"
                    aria-label="{{ $link['label'] }} menu"
                >
                    {{ $link['label'] }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 mt-2 min-w-[220px] rounded-lg shadow-lg border z-50"
                    style="background-color: var(--color-surface); border-color: var(--color-border);"
                    @click.outside="open = false"
                >
                    @foreach ($link['children'] as $child)
                        @if (isset($child['children']))
                            {{-- Nested dropdown (2-level deep) --}}
                            <div class="relative" x-data="{ subopen: false }">
                                <button
                                    class="w-full text-left px-4 py-2 flex items-center justify-between hover:bg-opacity-10"
                                    style="color: var(--color-text);"
                                    @click="subopen = !subopen"
                                    @mouseenter="subopen = true"
                                    aria-haspopup="true"
                                >
                                    {{ $child['label'] }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <div
                                    x-show="subopen"
                                    x-transition
                                    class="absolute left-full top-0 min-w-[180px] rounded-lg shadow-lg border z-50"
                                    style="background-color: var(--color-surface); border-color: var(--color-border);"
                                    @click.outside="subopen = false"
                                >
                                    @foreach ($child['children'] as $grandchild)
                                        <a href="{{ $grandchild['href'] }}"
                                           class="block px-4 py-2 hover:bg-opacity-10"
                                           style="color: var(--color-text);">
                                            {{ $grandchild['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $child['href'] }}"
                               class="block px-4 py-2 hover:bg-opacity-10"
                               style="color: var(--color-text);">
                                {{ $child['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <a href="{{ $link['href'] }}" class="hover:opacity-75" style="color: var(--color-text-muted);">{{ $link['label'] }}</a>
        @endif
    @endforeach
</nav>
@elseif ($variant === 'mobile')
{{-- ===================================================== --}}
{{-- MOBILE NAV (accordion, shown inside mobile panel)      --}}
{{-- ===================================================== --}}
<nav aria-label="Mobile navigation">
    @foreach ($links as $link)
        @if (isset($link['children']))
            <div x-data="{ open: false }" style="border-bottom: 1px solid var(--color-border);">
                <button
                    class="w-full text-left py-3 flex items-center justify-between"
                    style="color: var(--color-text);"
                    @click="open = !open"
                    aria-haspopup="true"
                >
                    <span>{{ $link['label'] }}</span>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse style="padding-left: 1rem; padding-bottom: 0.5rem;">
                    @foreach ($link['children'] as $child)
                        @if (isset($child['children']))
                            <div x-data="{ subopen: false }">
                                <button
                                    class="w-full text-left py-2 flex items-center justify-between"
                                    style="color: var(--color-text-muted); font-size: 0.875rem;"
                                    @click="subopen = !subopen"
                                >
                                    <span>{{ $child['label'] }}</span>
                                    <svg class="w-3 h-3 transition-transform" :class="subopen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="subopen" x-collapse style="padding-left: 1rem;">
                                    @foreach ($child['children'] as $grandchild)
                                        <a href="{{ $grandchild['href'] }}"
                                           class="block py-2"
                                           style="color: var(--color-text-muted); font-size: 0.8125rem;">
                                            {{ $grandchild['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $child['href'] }}"
                               class="block py-2"
                               style="color: var(--color-text-muted); font-size: 0.875rem;">
                                {{ $child['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div style="border-bottom: 1px solid var(--color-border);">
                <a href="{{ $link['href'] }}"
                   class="block py-3"
                   style="color: var(--color-text);">
                    {{ $link['label'] }}
                </a>
            </div>
        @endif
    @endforeach
</nav>
@elseif ($variant === 'usermenu')
{{-- ===================================================== --}}
{{-- USER MENU (login/logout — both desktop and mobile)     --}}
{{-- ===================================================== --}}
@if (auth()->check())
    <div class="relative" x-data="{ open: false }">
        <button
            class="flex items-center gap-2 px-3 py-1.5 text-sm rounded-full border hover:opacity-75"
            style="color: var(--color-text); border-color: var(--color-border); background-color: var(--color-surface);"
            @click="open = !open"
            @keydown.escape="open = false"
            aria-haspopup="true"
            :aria-expanded="open.toString()"
            aria-label="User menu"
        >
            <span class="hidden sm:inline" style="color: var(--color-text);">{{ auth()->user()->name }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 mt-2 min-w-[180px] rounded-lg shadow-lg border z-50"
            style="background-color: var(--color-surface); border-color: var(--color-border);"
            @click.outside="open = false"
        >
            <div class="px-4 py-2 border-b" style="border-color: var(--color-border);">
                <p class="text-sm font-medium" style="color: var(--color-text);">{{ auth()->user()->name }}</p>
                <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">{{ auth()->user()->email }}</p>
            </div>
            <a href="#" class="block px-4 py-2 hover:bg-opacity-10" style="color: var(--color-text);">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-opacity-10" style="color: var(--color-danger);">
                    Logout {{ auth()->user()->name }}
                </button>
            </form>
        </div>
    </div>
@else
    <a href="{{ route('login') }}"
       class="px-3 py-1.5 text-sm rounded-full border flex items-center gap-2"
       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);">
        Sign In
    </a>
@endif
@endif
