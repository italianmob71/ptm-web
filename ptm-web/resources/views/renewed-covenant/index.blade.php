@extends('layouts.app')

@section('content')
<style>
    .rc-wrap {
        max-width: 56rem;
        margin: 0 auto;
        padding: 3rem 1.5rem 5rem;
    }
    .rc-title {
        font-family: var(--font-serif);
        font-size: 2.75rem;
        font-weight: 600;
        margin: 0 0 1rem;
        color: var(--color-text);
        line-height: 1.2;
    }
    .rc-section {
        margin: 3rem 0;
    }
    .rc-h2 {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 600;
        margin: 0 0 1rem;
        color: var(--color-text);
    }
    .rc-h3 {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        margin: 2.5rem 0 0.25rem;
        color: var(--color-text);
    }
    .rc-h4 {
        font-family: var(--font-serif);
        font-size: 1.0625rem;
        font-weight: 500;
        margin: 0 0 1rem;
        color: var(--color-text-muted);
        font-style: italic;
        line-height: 1.5;
    }
    .rc-p {
        font-family: var(--font-serif);
        font-size: 1.0625rem;
        line-height: 1.75;
        color: var(--color-text);
        margin: 0 0 1.25rem;
    }
    .rc-video {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        height: 0;
        overflow: hidden;
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-border);
        margin: 1.5rem 0;
        background: var(--color-surface-2);
    }
    .rc-video iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        border: 0;
    }
    .rc-downloads {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: 1.75rem 2rem;
        margin-top: 1.5rem;
    }
    .rc-downloads h3 {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0 0 1rem;
        color: var(--color-text);
    }
    .rc-downloads ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .rc-downloads li {
        padding: 0.5rem 0;
    }
    .rc-downloads a {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        color: var(--color-accent);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.15s ease;
    }
    .rc-downloads a:hover {
        color: var(--color-accent-hi);
        text-decoration: underline;
    }
    .rc-downloads svg {
        width: 1.125rem;
        height: 1.125rem;
        flex-shrink: 0;
    }
    .rc-ketubah-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin: 1.5rem 0;
    }
    @media (min-width: 768px) {
        .rc-ketubah-grid { grid-template-columns: 1fr 1fr; }
    }
    .rc-ketubah-img {
        width: 100%;
        height: auto;
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
        display: block;
    }
    /* Anchor offset so sticky header doesn't cover headings */
    [id]::before {
        content: "";
        display: block;
        height: 5rem;
        margin-top: -5rem;
        visibility: hidden;
    }
</style>

<div class="rc-wrap">
    <h1 class="rc-title">The Renewed Covenant</h1>

    {{-- =========================================== --}}
    {{-- Covenant of Friendship                       --}}
    {{-- =========================================== --}}
    <section class="rc-section" id="covenant-of-friendship">
        <h2 class="rc-h2">Covenant of Friendship</h2>
        <p class="rc-p">
            Renewed Covenant with Elohim: The Covenant of Friendship with Elohim (God) is a renewed covenant of
            Mount Sinai described in Jeremiah 31:31 with the covenant meal in remembrance of Yeshua that the
            people can take today. For these the ones who will never hunger or never thirst. By taking this
            covenant, you become a friend of Elohim.
        </p>

        <div class="rc-video">
            <iframe
                src="https://www.youtube.com/embed/doMGw9INDOc?start=138"
                title="The Renewed Covenant: The Covenant of Friendship with Janice Baca"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>

        <h3 class="rc-h3" id="scroll-of-mysteries">The Scroll of Mysteries: Cochin Hebrew Revelation</h3>
        <h4 class="rc-h4">with Janice F. Baca</h4>
        <h4 class="rc-h4">
            For the full testimony and images of the Miracle storm at Mount Sinai with the Renewed Covenant
            that expeditioners completed on March 14, 2023, hear Janice's testimony at minute mark 2:01:25.
        </h4>

        <div class="rc-video">
            <iframe
                src="https://www.youtube.com/embed/ziP_UZerP4E"
                title="The Scroll of Mysteries: Cochin Hebrew Revelation with Janice Baca"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>

        <h3 class="rc-h3" id="daily-prayer">Daily Prayer</h3>
        <h4 class="rc-h4">with Janice F. Baca</h4>

        <p class="rc-p">
            For those who follow Janice F. Baca's teachings on YouTube, you know the key to defeating the
            dragon and the beast is through Psalms 91. This prayer is a daily prayer of protection and
            deliverance.
        </p>
        <p class="rc-p">
            And for those who have taken the Renewed Covenant (Joel 2:31–34) with us of Mount Sinai, this
            is a daily reminder of the covenant promises of Yehovah when we obey His commands and His voice.
        </p>
        <p class="rc-p">
            To take the covenant with Yehovah, you can go to the following link to join in the marriage
            covenant with Him.
        </p>

        <div class="rc-video">
            <iframe
                src="https://www.youtube.com/embed/PXkFw4sLznk"
                title="Daily Prayer"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>

        <h3 class="rc-h3" id="mount-sinai-storm">Unbelievable Storm at Mount Sinai</h3>
        <h4 class="rc-h4">For additional video footage of the miraculous storm, watch the following video.</h4>

        <div class="rc-video">
            <iframe
                src="https://www.youtube.com/embed/EJ36eNz5Bk8"
                title="Unbelievable Storm at Mount Sinai"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>

        <div class="rc-downloads" id="covenant-downloads">
            <h3>Downloads</h3>
            <ul>
                <li>
                    <a href="https://projecttruthministries.org/app/uploads/2026/05/Renewed-Covenant-with-Elohim_Updated-May-7-2026.pdf"
                       target="_blank" rel="noopener noreferrer">
                        <svg aria-hidden="true" viewBox="0 0 384 512" fill="currentColor">
                            <path d="M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z"/>
                        </svg>
                        Renewed Covenant with Elohim
                    </a>
                </li>
            </ul>
        </div>
    </section>

    {{-- =========================================== --}}
    {{-- Ketubah                                      --}}
    {{-- =========================================== --}}
    <section class="rc-section" id="ketubah">
        <h2 class="rc-h2">Ketubah</h2>
        <p class="rc-p">
            In a traditional Jewish wedding ceremony, the ketubah is signed by two witnesses and traditionally
            read out loud under the chuppah between the erusin and nissuin. Friends or distant relatives are
            invited to witness the ketubah, which is considered an honour; close relatives are prohibited from
            being witnesses. The witnesses must be halakhically valid witnesses, and so cannot be a blood
            relative of the couple. In Orthodox Judaism, women are also not considered to be valid witnesses.
            The ketubah is handed to the bride (or, more commonly, to the bride's mother) for safekeeping.
        </p>

        <h4 class="rc-h4">These beautiful frameable Ketubahs are courtesy of Cheryl Bell.</h4>

        <div class="rc-ketubah-grid">
            <a href="https://projecttruthministries.org/app/uploads/2025/02/Ketubah-Signing.png"
               target="_blank" rel="noopener noreferrer">
                <img src="https://projecttruthministries.org/app/uploads/2025/02/Ketubah-Signing.png"
                     alt="Ketubah Signing"
                     class="rc-ketubah-img"
                     loading="lazy" />
            </a>
            <a href="https://projecttruthministries.org/app/uploads/2025/02/Keubah-Wording.png"
               target="_blank" rel="noopener noreferrer">
                <img src="https://projecttruthministries.org/app/uploads/2025/02/Keubah-Wording.png"
                     alt="Ketubah Wording"
                     class="rc-ketubah-img"
                     loading="lazy" />
            </a>
        </div>

        <div class="rc-downloads">
            <h3>Downloads</h3>
            <ul>
                <li>
                    <a href="https://projecttruthministries.org/app/uploads/2025/02/Keubah-Wording.pdf"
                       target="_blank" rel="noopener noreferrer">
                        <svg aria-hidden="true" viewBox="0 0 384 512" fill="currentColor">
                            <path d="M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z"/>
                        </svg>
                        Ketubah Wording (PDF)
                    </a>
                </li>
            </ul>
        </div>
    </section>
</div>
@endsection
