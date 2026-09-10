@extends('layouts.app')

@section('content')
<!-- Resources Page Header -->
<header class="py-12 md:py-16" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
    <div class="mx-auto max-w-7xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-serif font-semibold" style="color: var(--color-text); margin-bottom: 0.5rem;">Living Scroll Resources</h1>
    </div>
</header>

<main>
    <div class="mx-auto max-w-7xl px-4 py-12">
        <div class="prose-scholarly max-w-4xl mx-auto" style="color: var(--color-text); font-size: 1.0625rem; line-height: 1.8;">
            <section class="mb-12">
                <ul class="list-disc pl-6" style="color: var(--color-text-muted);">
                    <li><a href="{{ route('blog.index') }}">Living Scroll Blog</a></li>
                    <li><a href="{{ route('articles.index') }}">Articles</a></li>
                    <li><a href="{{ route('books.index') }}">Book Recommendations</a></li>
                </ul>
            </section>
        </div>
    </div>
</main>
@endsection
