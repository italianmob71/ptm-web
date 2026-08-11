{{-- Inline SVG icon sprite. Included once in the layout. Referenced via <svg class="icon"><use xlink:href="#icon-name"></use></svg>. --}}
<svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true" focusable="false">
    <defs>
        {{-- Shopping bag icon (for Amazon buy button — clean at small sizes) --}}
        <symbol id="icon-amazon" viewBox="0 0 24 24">
            <path fill="currentColor" d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/>
        </symbol>
        <symbol id="icon-amazon-arrow" viewBox="0 0 56 56">
            <path fill="currentColor" d="M28 8c-5.5 0-10 4.5-10 10s4.5 10 10 10c2 0 3.8-.6 5.3-1.6.5.5 1.3 1.2 1.3 1.2-.4-1.2-.2-2 0-2.8.3-1 .7-1.7.7-2.1 0-.4-.4-.6-.7-.4-.5.3-1.3.9-2.5 1.4-1.2.5-2.6.8-4.1.8-3.9 0-7-3.1-7-7s3.1-7 7-7c2.8 0 5.2 1.6 6.3 4 .2.4.6.6 1 .5.5-.1.7-.5.6-1C35 11.3 31.8 8 28 8z"/>
        </symbol>

        {{-- Lulu logo (stylized "L" book mark) --}}
        <symbol id="icon-lulu" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 2L2 7v10l10 5 10-5V7L12 2zm0 2.18L18.82 7 12 9.82 5.18 7 12 4.18zM4 8.82l7 2.8v7.56l-7-3.5V8.82zm9 10.36v-7.56l7-2.8v6.86l-7 3.5z"/>
        </symbol>

        {{-- Book / ebook icon (open book) --}}
        <symbol id="icon-book" viewBox="0 0 24 24">
            <path fill="currentColor" d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.83 0-3.53.38-5 1-1.47-.62-3.17-1-5-1S3.11 4.65 2 5v14.5c0 .28.22.5.5.5.11 0 .22-.04.3-.1C3.83 19.3 5.32 19 7 19c1.83 0 3.53.38 5 1 1.47-.62 3.17-1 5-1 1.68 0 3.17.3 4.7.9.08.06.19.1.3.1.28 0 .5-.22.5-.5V5c-.28-.28-.5-.5-.5-.5zM12 18c-1.47-.62-3.17-1-5-1-1.16 0-2.32.17-3 .42V6.42C5.68 6.17 6.84 6 8 6c1.83 0 3.53.38 5 1v11z"/>
        </symbol>

        {{-- External link icon --}}
        <symbol id="icon-external" viewBox="0 0 24 24">
            <path fill="currentColor" d="M14 3v2h3.59l-9.8 9.8 1.41 1.41L19 6.41V10h2V3h-7zM5 5c-1.11 0-2 .89-2 2v14c0 1.11.89 2 2 2h14c1.11 0 2-.89 2-2v-9h-2v9H5V7h9V5H5z"/>
        </symbol>

        {{-- Search icon --}}
        <symbol id="icon-search" viewBox="0 0 24 24">
            <path fill="currentColor" d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
        </symbol>

        {{-- Menu / hamburger --}}
        <symbol id="icon-menu" viewBox="0 0 24 24">
            <path fill="currentColor" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </symbol>

        {{-- Chevron down --}}
        <symbol id="icon-chevron-down" viewBox="0 0 24 24">
            <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
        </symbol>

        {{-- User / person --}}
        <symbol id="icon-user" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </symbol>

        {{-- Cart / shopping --}}
        <symbol id="icon-cart" viewBox="0 0 24 24">
            <path fill="currentColor" d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A.996.996 0 0 0 21 6H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
        </symbol>
    </defs>
</svg>
