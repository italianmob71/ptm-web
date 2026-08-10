@extends('layouts.app')

@section('content')
<style>
    .video-hero {
        background-color: #000;
        position: relative;
    }
    html[data-theme="light"] .video-hero {
        background-color: #fff;
    }

    /* Single SVG overlay covering the entire hero */
    .tear-frame {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 2;
        display: block;
    }
    .tear-frame svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    /* Paper fill — matches page background so it looks like the page itself */
    .paper-fill {
        fill: var(--color-bg);
    }

    /* Crisp torn edge line for fiber definition */
    .torn-jag {
        fill: none;
        stroke: rgba(0,0,0,0.50);
        stroke-width: 1.5;
        stroke-linejoin: round;
        stroke-linecap: round;
        vector-effect: non-scaling-stroke;
    }
    html[data-theme="light"] .torn-jag {
        stroke: rgba(0,0,0,0.28);
    }

    /* Soft inner shadow band — cast INTO the video hole from the paper edge */
    .tear-shadow {
        fill: none;
        stroke: rgba(0,0,0,0.35);
        stroke-width: 8;
        stroke-linejoin: round;
        stroke-linecap: round;
        opacity: 0.7;
        vector-effect: non-scaling-stroke;
    }
    html[data-theme="light"] .tear-shadow {
        stroke: rgba(0,0,0,0.20);
        opacity: 0.55;
    }

    /* ===== Feature Cards Grid ===== */
    .feature-cards {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
        padding: 3rem 1.5rem;
    }
    @media (min-width: 768px) {
        .feature-cards {
            grid-template-columns: repeat(5, 1fr);
            gap: 1.25rem;
            padding: 4rem 2rem;
            max-width: 80rem;
            margin: 0 auto;
        }
    }

    .feature-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .feature-card__image {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        background: var(--color-surface-2);
    }

    .feature-card__content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .feature-card__title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 0.75rem;
        color: var(--color-text);
    }

    .feature-card__body {
        font-size: 0.875rem;
        line-height: 1.65;
        color: var(--color-text-muted);
        margin: 0;
        flex: 1;
    }

    /* ===== About PTM Section ===== */
    .about-ptm {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin: 3rem auto 0;
        max-width: 80rem;
        padding: 0;
        display: flex;
        flex-direction: column;
    }
    @media (min-width: 768px) {
        .about-ptm {
            flex-direction: row;
            margin: 4rem auto 0;
        }
    }

    .about-ptm__text {
        flex: 1;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    @media (min-width: 768px) {
        .about-ptm__text {
            padding: 3rem;
            min-width: 0;
        }
    }

    .about-ptm__title {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.3;
        margin: 0 0 1rem;
        color: var(--color-text);
    }
    @media (min-width: 768px) {
        .about-ptm__title {
            font-size: 1.75rem;
        }
    }

    .about-ptm__body {
        font-size: 1rem;
        line-height: 1.7;
        color: var(--color-text-muted);
        margin: 0 0 1.5rem;
    }

    .about-ptm__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--color-accent);
        color: var(--color-text-inv);
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: var(--radius-md);
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.1s ease;
        width: fit-content;
    }
    .about-ptm__btn:hover {
        background: var(--color-accent-hi);
        transform: translateY(-1px);
    }
    .about-ptm__btn:active {
        transform: translateY(0);
    }

    .about-ptm__video {
        flex: 1;
        min-height: 280px;
        position: relative;
        background: #000;
    }
    @media (min-width: 768px) {
        .about-ptm__video {
            min-height: 360px;
            max-width: 50%;
        }
    }
    html[data-theme="light"] .about-ptm__video {
        background: #fff;
    }

    .about-ptm__video video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ===== Blog Section ===== */
    .blog-section {
        padding: 0 1.5rem 4rem;
    }
    @media (min-width: 768px) {
        .blog-section {
            padding: 0 2rem 5rem;
        }
    }

    .blog-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        max-width: 80rem;
        margin: 4rem auto 0;
    }

    .blog-card__header {
        background: var(--color-bg);
        border-bottom: 1px solid var(--color-border);
        padding: 2rem 2.5rem;
    }
    @media (min-width: 768px) {
        .blog-card__header {
            padding: 2.5rem 3rem;
        }
    }

    .blog-card__title-lines {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .blog-card__line1 {
        font-family: var(--font-sans);
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-accent);
    }

    .blog-card__line2 {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 600;
        line-height: 1.2;
        color: var(--color-text);
    }
    @media (min-width: 768px) {
        .blog-card__line2 {
            font-size: 2.5rem;
        }
    }

    .blog-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 2rem;
    }
    @media (min-width: 768px) {
        .blog-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            padding: 2.5rem 3rem 3rem;
        }
    }

    .blog-post {
        background: var(--color-bg);
        border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .blog-post:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .blog-post__image {
        width: 100%;
        aspect-ratio: 16 / 10;
        object-fit: cover;
        background: var(--color-surface-2);
    }

    .blog-post__content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .blog-post__title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 0.75rem;
        color: var(--color-text);
    }

    .blog-post__excerpt {
        font-size: 0.875rem;
        line-height: 1.65;
        color: var(--color-text-muted);
        margin: 0 0 1.5rem;
        flex: 1;
    }

    .blog-post__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: transparent;
        border: 2px solid var(--color-accent);
        color: var(--color-accent);
        font-size: 0.8125rem;
        font-weight: 600;
        letter-spacing: 0.025em;
        border-radius: var(--radius-md);
        text-decoration: none;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
        width: fit-content;
    }
    .blog-post__btn:hover {
        background: var(--color-accent);
        color: var(--color-text-inv);
        transform: translateX(4px);
    }

    /* ===== Truth Topics Section ===== */
    .topics-section {
        padding: 0 1.5rem 4rem;
    }
    @media (min-width: 768px) {
        .topics-section {
            padding: 0 2rem 5rem;
        }
    }

    .topics-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        max-width: 80rem;
        margin: 4rem auto 0;
    }

    .topics-card__header {
        background: var(--color-bg);
        border-bottom: 1px solid var(--color-border);
        padding: 2rem 2.5rem;
    }
    @media (min-width: 768px) {
        .topics-card__header {
            padding: 2.5rem 3rem;
        }
    }

    .topics-card__title-lines {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .topics-card__line1 {
        font-family: var(--font-sans);
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-accent);
    }

    .topics-card__line2 {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 600;
        line-height: 1.2;
        color: var(--color-text);
    }
    @media (min-width: 768px) {
        .topics-card__line2 {
            font-size: 2.5rem;
        }
    }

    .topics-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 2rem;
    }
    @media (min-width: 768px) {
        .topics-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            padding: 2.5rem 3rem 3rem;
        }
    }

    .topic-post {
        background: var(--color-bg);
        border: 1px solid var(--color-border-soft);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .topic-post:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--color-accent);
    }

    .topic-post__image {
        width: 100%;
        aspect-ratio: 16 / 10;
        object-fit: cover;
        background: var(--color-surface-2);
    }

    .topic-post__content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .topic-post__title {
        font-family: var(--font-serif);
        font-size: 1.125rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 0.75rem;
        color: var(--color-text);
    }

    .topic-post__excerpt {
        font-size: 0.875rem;
        line-height: 1.65;
        color: var(--color-text-muted);
        margin: 0 0 1.5rem;
        flex: 1;
    }

    .topic-post__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: transparent;
        border: 2px solid var(--color-accent);
        color: var(--color-accent);
        font-size: 0.8125rem;
        font-weight: 600;
        letter-spacing: 0.025em;
        border-radius: var(--radius-md);
        text-decoration: none;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
        width: fit-content;
    }
    .topic-post__btn:hover {
        background: var(--color-accent);
        color: var(--color-text-inv);
        transform: translateX(4px);
    }
</style>

<div class="video-hero w-full relative overflow-hidden aspect-video md:aspect-auto md:h-[70vh] min-h-[400px]">
    <!-- Video sits BEHIND the torn paper frame -->
    <video
        autoplay muted loop playsinline
        class="absolute inset-0 w-full h-full object-cover z-0"
    >
        <source src="{{ asset('videos/ptm-home.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- ONE continuous SVG frame: solid paper border with a jagged torn hole in the middle -->
    <div class="tear-frame" aria-hidden="true">
        <svg viewBox="0 0 1000 1000" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Paper frame: outer rect minus inner jagged hole (even-odd fill-rule) -->
            <path class="paper-fill" fill-rule="evenodd" d="M 0 0 L 1000 0 L 1000 1000 L 0 1000 Z M 73.6 98.7 L 78.9 127.3 L 59.5 156.0 L 74.1 184.7 L 55.7 213.3 L 64.1 242.0 L 72.9 270.7 L 58.3 299.3 L 73.2 328.0 L 83.8 356.7 L 58.7 385.3 L 73.3 414.0 L 86.8 442.7 L 68.1 471.3 L 65.7 500.0 L 70.5 528.7 L 63.7 557.3 L 67.3 586.0 L 77.1 614.7 L 86.5 643.3 L 86.7 672.0 L 67.8 700.7 L 71.3 729.3 L 66.4 758.0 L 66.0 786.7 L 60.9 815.3 L 78.1 844.0 L 66.0 872.7 L 80.3 901.3 L 70.0 937.8 L 98.7 927.6 L 127.3 918.2 L 156.0 936.1 L 184.7 931.5 L 213.3 942.8 L 242.0 919.1 L 270.7 924.0 L 299.3 936.6 L 328.0 925.7 L 356.7 919.0 L 385.3 920.2 L 414.0 926.2 L 442.7 917.2 L 471.3 928.4 L 500.0 945.1 L 528.7 920.1 L 557.3 932.9 L 586.0 925.5 L 614.7 926.4 L 643.3 929.7 L 672.0 934.5 L 700.7 920.0 L 729.3 918.3 L 758.0 944.2 L 786.7 921.1 L 815.3 943.3 L 844.0 920.0 L 872.7 914.6 L 901.3 938.0 L 937.7 930.0 L 930.3 901.3 L 925.5 872.7 L 944.5 844.0 L 927.4 815.3 L 938.6 786.7 L 932.3 758.0 L 918.6 729.3 L 924.6 700.7 L 935.4 672.0 L 918.1 643.3 L 946.1 614.7 L 944.1 586.0 L 916.8 557.3 L 937.6 528.7 L 920.1 500.0 L 914.9 471.3 L 944.0 442.7 L 924.9 414.0 L 935.6 385.3 L 929.8 356.7 L 921.4 328.0 L 941.3 299.3 L 933.5 270.7 L 939.1 242.0 L 925.5 213.3 L 923.0 184.7 L 918.7 156.0 L 941.8 127.3 L 933.1 98.7 L 930.0 84.8 L 901.3 59.6 L 872.7 62.6 L 844.0 75.0 L 815.3 67.0 L 786.7 64.0 L 758.0 72.7 L 729.3 57.1 L 700.7 56.1 L 672.0 60.7 L 643.3 72.1 L 614.7 80.7 L 586.0 80.2 L 557.3 67.0 L 528.7 74.8 L 500.0 80.4 L 471.3 80.6 L 442.7 55.4 L 414.0 81.5 L 385.3 62.8 L 356.7 80.1 L 328.0 74.7 L 299.3 62.9 L 270.7 74.6 L 242.0 54.3 L 213.3 62.2 L 184.7 64.1 L 156.0 77.7 L 127.3 78.0 L 98.7 61.5 L 70.0 70.1 Z" />

            <!-- Soft shadow band on the torn edge, cast into the video -->
            <path class="tear-shadow" d="M 70.0 70.1 L 98.7 61.5 L 127.3 78.0 L 156.0 77.7 L 184.7 64.1 L 213.3 62.2 L 242.0 54.3 L 270.7 74.6 L 299.3 62.9 L 328.0 74.7 L 356.7 80.1 L 385.3 62.8 L 414.0 81.5 L 442.7 55.4 L 471.3 80.6 L 500.0 80.4 L 528.7 74.8 L 557.3 67.0 L 586.0 80.2 L 614.7 80.7 L 643.3 72.1 L 672.0 60.7 L 700.7 56.1 L 729.3 57.1 L 758.0 72.7 L 786.7 64.0 L 815.3 67.0 L 844.0 75.0 L 872.7 62.6 L 901.3 59.6 L 930.0 84.8 L 933.1 98.7 L 941.8 127.3 L 918.7 156.0 L 923.0 184.7 L 925.5 213.3 L 939.1 242.0 L 933.5 270.7 L 941.3 299.3 L 921.4 328.0 L 929.8 356.7 L 935.6 385.3 L 924.9 414.0 L 944.0 442.7 L 914.9 471.3 L 920.1 500.0 L 937.6 528.7 L 916.8 557.3 L 944.1 586.0 L 946.1 614.7 L 918.1 643.3 L 935.4 672.0 L 924.6 700.7 L 918.6 729.3 L 932.3 758.0 L 938.6 786.7 L 927.4 815.3 L 944.5 844.0 L 925.5 872.7 L 930.3 901.3 L 937.7 930.0 L 901.3 938.0 L 872.7 914.6 L 844.0 920.0 L 815.3 943.3 L 786.7 921.1 L 758.0 944.2 L 729.3 918.3 L 700.7 920.0 L 672.0 934.5 L 643.3 929.7 L 614.7 926.4 L 586.0 925.5 L 557.3 932.9 L 528.7 920.1 L 500.0 945.1 L 471.3 928.4 L 442.7 917.2 L 414.0 926.2 L 385.3 920.2 L 356.7 919.0 L 328.0 925.7 L 299.3 936.6 L 270.7 924.0 L 242.0 919.1 L 213.3 942.8 L 184.7 931.5 L 156.0 936.1 L 127.3 918.2 L 98.7 927.6 L 70.0 937.8 L 80.3 901.3 L 66.0 872.7 L 78.1 844.0 L 60.9 815.3 L 66.0 786.7 L 66.4 758.0 L 71.3 729.3 L 67.8 700.7 L 86.7 672.0 L 86.5 643.3 L 77.1 614.7 L 67.3 586.0 L 63.7 557.3 L 70.5 528.7 L 65.7 500.0 L 68.1 471.3 L 86.8 442.7 L 73.3 414.0 L 58.7 385.3 L 83.8 356.7 L 73.2 328.0 L 58.3 299.3 L 72.9 270.7 L 64.1 242.0 L 55.7 213.3 L 74.1 184.7 L 59.5 156.0 L 78.9 127.3 L 73.6 98.7 Z" />

            <!-- Crisp torn edge line -->
            <path class="torn-jag" d="M 70.0 70.1 L 98.7 61.5 L 127.3 78.0 L 156.0 77.7 L 184.7 64.1 L 213.3 62.2 L 242.0 54.3 L 270.7 74.6 L 299.3 62.9 L 328.0 74.7 L 356.7 80.1 L 385.3 62.8 L 414.0 81.5 L 442.7 55.4 L 471.3 80.6 L 500.0 80.4 L 528.7 74.8 L 557.3 67.0 L 586.0 80.2 L 614.7 80.7 L 643.3 72.1 L 672.0 60.7 L 700.7 56.1 L 729.3 57.1 L 758.0 72.7 L 786.7 64.0 L 815.3 67.0 L 844.0 75.0 L 872.7 62.6 L 901.3 59.6 L 930.0 84.8 L 933.1 98.7 L 941.8 127.3 L 918.7 156.0 L 923.0 184.7 L 925.5 213.3 L 939.1 242.0 L 933.5 270.7 L 941.3 299.3 L 921.4 328.0 L 929.8 356.7 L 935.6 385.3 L 924.9 414.0 L 944.0 442.7 L 914.9 471.3 L 920.1 500.0 L 937.6 528.7 L 916.8 557.3 L 944.1 586.0 L 946.1 614.7 L 918.1 643.3 L 935.4 672.0 L 924.6 700.7 L 918.6 729.3 L 932.3 758.0 L 938.6 786.7 L 927.4 815.3 L 944.5 844.0 L 925.5 872.7 L 930.3 901.3 L 937.7 930.0 L 901.3 938.0 L 872.7 914.6 L 844.0 920.0 L 815.3 943.3 L 786.7 921.1 L 758.0 944.2 L 729.3 918.3 L 700.7 920.0 L 672.0 934.5 L 643.3 929.7 L 614.7 926.4 L 586.0 925.5 L 557.3 932.9 L 528.7 920.1 L 500.0 945.1 L 471.3 928.4 L 442.7 917.2 L 414.0 926.2 L 385.3 920.2 L 356.7 919.0 L 328.0 925.7 L 299.3 936.6 L 270.7 924.0 L 242.0 919.1 L 213.3 942.8 L 184.7 931.5 L 156.0 936.1 L 127.3 918.2 L 98.7 927.6 L 70.0 937.8 L 80.3 901.3 L 66.0 872.7 L 78.1 844.0 L 60.9 815.3 L 66.0 786.7 L 66.4 758.0 L 71.3 729.3 L 67.8 700.7 L 86.7 672.0 L 86.5 643.3 L 77.1 614.7 L 67.3 586.0 L 63.7 557.3 L 70.5 528.7 L 65.7 500.0 L 68.1 471.3 L 86.8 442.7 L 73.3 414.0 L 58.7 385.3 L 83.8 356.7 L 73.2 328.0 L 58.3 299.3 L 72.9 270.7 L 64.1 242.0 L 55.7 213.3 L 74.1 184.7 L 59.5 156.0 L 78.9 127.3 L 73.6 98.7 Z" />
        </svg>
    </div>
</div>

<!-- Feature Cards Section -->
<section aria-labelledby="features-heading">
    <h2 id="features-heading" class="sr-only">Featured Studies</h2>

    <div class="feature-cards">
        <!-- Card 1: Cochin Revelation -->
        <article class="feature-card">
            <img src="{{ asset('images/site/revelation-500x500-2.jpg') }}"
                 alt=""
                 class="feature-card__image"
                 loading="lazy" />
            <div class="feature-card__content">
                <h3 class="feature-card__title">Cochin Revelation</h3>
                <p class="feature-card__body">
                    The Cochin Hebrew Revelation MS Oo.1.16.2 (The Scroll of Mysteries) is a unique Hebrew Revelation with late Second Temple grammar. Discover more through the free translation and videos featuring Janice F. Baca and Bryan S. Williams.
                </p>
            </div>
        </article>

        <!-- Card 2: Cochin New Testament -->
        <article class="feature-card">
            <img src="{{ asset('images/site/new-testament-500x500-1.jpg') }}"
                 alt=""
                 class="feature-card__image"
                 loading="lazy" />
            <div class="feature-card__content">
                <h3 class="feature-card__title">Cochin New Testament</h3>
                <p class="feature-card__body">
                    The Cochin Hebrew New Testament MS Oo.1.32, Oo.1.16 were discovered in Cochin, India in the Malabari Synagogue of the black Jews by Claudius Buchanan in 1806. These manuscripts are being analyzed and mysteries revealed.
                </p>
            </div>
        </article>

        <!-- Card 3: The Renewed Covenant -->
        <article class="feature-card">
            <img src="{{ asset('images/site/renewed-500x500-1.jpg') }}"
                 alt=""
                 class="feature-card__image"
                 loading="lazy" />
            <div class="feature-card__content">
                <h3 class="feature-card__title">The Renewed Covenant</h3>
                <p class="feature-card__body">
                    Discover the deeper meaning of the Renewed Covenant: The Covenant of Friendship described in Jeremiah 31:31–34 through the fuller meaning of the blood covenant. Download your copy of the free research paper.
                </p>
            </div>
        </article>

        <!-- Card 4: Mount Sinai -->
        <article class="feature-card">
            <img src="{{ asset('images/site/mtSinai-500x500-1.jpg') }}"
                 alt=""
                 class="feature-card__image"
                 loading="lazy" />
            <div class="feature-card__content">
                <h3 class="feature-card__title">Mount Sinai</h3>
                <p class="feature-card__body">
                    There is mounting evidence that the real Mount Sinai is located in the region of Midian in Saudi Arabia. See stunning images, video footage, and evidence discovered at what is believed to be the real Mount Sinai.
                </p>
            </div>
        </article>

        <!-- Card 5: Special Studies -->
        <article class="feature-card">
            <img src="{{ asset('images/site/studies-500x500-1.jpg') }}"
                 alt=""
                 class="feature-card__image"
                 loading="lazy" />
            <div class="feature-card__content">
                <h3 class="feature-card__title">Special Studies</h3>
                <p class="feature-card__body">
                    From Biblical truths, archaeological findings, and ancient Hebrew manuscripts, this is the place to find a treasure of unique and intriguing studies.
                </p>
            </div>
        </article>
    </div>
</section>

<!-- About PTM Section -->
<section class="about-ptm" aria-labelledby="about-heading">
    <div class="about-ptm__text">
        <h2 id="about-heading" class="about-ptm__title">About Project Truth Ministries</h2>
        <p class="about-ptm__body">
            At Project Truth Ministries (PTM) we are dedicated to providing you with Biblical truths to include archaeological findings, Biblical research, such as in the Tanakh (Old Testament). We are also dedicated to revealing truths found in the New Testament manuscripts discovered in Hebrew and Aramaic.
        </p>
        <a href="#" class="about-ptm__btn">Learn More <span aria-hidden="true">→</span></a>
    </div>

    <div class="about-ptm__video" aria-label="PTM introduction video">
        <video controls playsinline poster="{{ asset('images/site/studies-500x500-1.jpg') }}">
            <source src="{{ asset('videos/ptm-home.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</section>



<!-- Truths Revealed Blog Section -->
<section class="blog-section" aria-labelledby="blog-heading">
    <div class="blog-card">
        <!-- Section Title Bar -->
        <header class="blog-card__header">
            <div class="blog-card__title-lines">
                <span class="blog-card__line1">Project Truth Ministries &ndash; Presents</span>
                <span class="blog-card__line2">Truths Revealed Blog</span>
            </div>
        </header>

        <!-- Blog Posts Grid -->
        <div class="blog-grid">
            <!-- Post 1 -->
            <article class="blog-post">
                <img src="{{ asset('images/site/revelation-500x500-1.jpg') }}"
                     alt=""
                     class="blog-post__image"
                     loading="lazy" />
                <div class="blog-post__content">
                    <h3 class="blog-post__title">The Scroll of Mysteries Unveiled</h3>
                    <p class="blog-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.
                    </p>
                    <a href="#" class="blog-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>

            <!-- Post 2 -->
            <article class="blog-post">
                <img src="{{ asset('images/site/new-testament-500x500-1.jpg') }}"
                     alt=""
                     class="blog-post__image"
                     loading="lazy" />
                <div class="blog-post__content">
                    <h3 class="blog-post__title">Cochin Manuscripts: New Discoveries</h3>
                    <p class="blog-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.
                    </p>
                    <a href="#" class="blog-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>

            <!-- Post 3 -->
            <article class="blog-post">
                <img src="{{ asset('images/site/renewed-500x500-1.jpg') }}"
                     alt=""
                     class="blog-post__image"
                     loading="lazy" />
                <div class="blog-post__content">
                    <h3 class="blog-post__title">The Covenant of Friendship Explored</h3>
                    <p class="blog-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.
                    </p>
                    <a href="#" class="blog-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>
        </div>
    </div>
</section>



<!-- Truth Topics Section -->
<section class="topics-section" aria-labelledby="topics-heading">
    <div class="topics-card">
        <!-- Section Title Bar -->
        <header class="topics-card__header">
            <div class="topics-card__title-lines">
                <span class="topics-card__line1">Project Truth Ministries &ndash; Presents</span>
                <span class="topics-card__line2">Truth Topics</span>
            </div>
        </header>

        <!-- Topics Grid: 2 rows x 3 cols on desktop, single column on mobile -->
        <div class="topics-grid">
            <!-- Row 1 -->
            <article class="topic-post">
                <img src="{{ asset('images/site/mtSinai-500x500-1.jpg') }}"
                     alt=""
                     class="topic-post__image"
                     loading="lazy" />
                <div class="topic-post__content">
                    <h3 class="topic-post__title">Mount Sinai Evidence</h3>
                    <p class="topic-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </p>
                    <a href="#" class="topic-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>

            <article class="topic-post">
                <img src="{{ asset('images/site/studies-500x500-1.jpg') }}"
                     alt=""
                     class="topic-post__image"
                     loading="lazy" />
                <div class="topic-post__content">
                    <h3 class="topic-post__title">Special Studies Archive</h3>
                    <p class="topic-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </p>
                    <a href="#" class="topic-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>

            <article class="topic-post">
                <img src="{{ asset('images/site/renewed-500x500-1.jpg') }}"
                     alt=""
                     class="topic-post__image"
                     loading="lazy" />
                <div class="topic-post__content">
                    <h3 class="topic-post__title">Renewed Covenant</h3>
                    <p class="topic-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </p>
                    <a href="#" class="topic-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>

            <!-- Row 2 -->
            <article class="topic-post">
                <img src="{{ asset('images/site/revelation-500x500-1.jpg') }}"
                     alt=""
                     class="topic-post__image"
                     loading="lazy" />
                <div class="topic-post__content">
                    <h3 class="topic-post__title">Scroll of Mysteries</h3>
                    <p class="topic-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </p>
                    <a href="#" class="topic-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>

            <article class="topic-post">
                <img src="{{ asset('images/site/new-testament-500x500-1.jpg') }}"
                     alt=""
                     class="topic-post__image"
                     loading="lazy" />
                <div class="topic-post__content">
                    <h3 class="topic-post__title">Cochin NT Manuscripts</h3>
                    <p class="topic-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </p>
                    <a href="#" class="topic-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>

            <article class="topic-post">
                <img src="{{ asset('images/site/mtSinai-500x500-1.jpg') }}"
                     alt=""
                     class="topic-post__image"
                     loading="lazy" />
                <div class="topic-post__content">
                    <h3 class="topic-post__title">Archaeological Discoveries</h3>
                    <p class="topic-post__excerpt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </p>
                    <a href="#" class="topic-post__btn">Read Now <span aria-hidden="true">&rarr;</span></a>
                </div>
            </article>
        </div>
    </div>
</section>

@endsection