@php
    $links = [
        ['label' => 'Home', 'href' => route('home')],
        ['label' => 'About', 'href' => '#about'],
        ['label' => 'Studies', 'href' => '#studies'],
        ['label' => 'Resources', 'href' => '#resources'],
        ['label' => 'Events', 'href' => '#events'],
        ['label' => 'Contact', 'href' => '#contact'],
    ];
@endphp
@foreach ($links as $link)
    <a href="{{ $link['href'] }}" class="hover:opacity-75" style="color: var(--color-text-muted);">{{ $link['label'] }}</a>
@endforeach
