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
        ['label' => 'Studies', 'href' => '#studies'],
        ['label' => 'Resources', 'href' => route('resources')],
        ['label' => 'Events', 'href' => route('events')],
        ['label' => 'Contact', 'href' => '#contact'],
    ];
    $showDropdowns = $showDropdowns ?? true;
@endphp

<nav class="hidden md:flex flex-1 justify-center gap-8 text-sm" aria-label="Main navigation">
    @foreach ($links as $link)
        @if (isset($link['children']) && $showDropdowns)
            <!-- Dropdown wrapper -->
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
                    class="absolute left-0 mt-2 min-w-[180px] rounded-lg shadow-lg border z-50"
                    style="background-color: var(--color-surface); border-color: var(--color-border);"
                    @click.outside="open = false"
                >
                    @foreach ($link['children'] as $child)
                        <a href="{{ $child['href'] }}" 
                           class="block px-4 py-2 hover:bg-opacity-10"
                           style="color: var(--color-text);">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <a href="{{ $link['href'] }}" class="hover:opacity-75" style="color: var(--color-text-muted);">{{ $link['label'] }}</a>
        @endif
    @endforeach
</nav>