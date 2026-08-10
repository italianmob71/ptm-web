@extends("layouts.app")

@section("content")
<div class="relative">
    <!-- Hero Section -->
    <section class="relative bg-[color:var(--color-surface)] py-20 sm:py-32">
        <div class="mx-auto max-w-4xl px-4 text-center">
            <h1 class="font-serif text-4xl sm:text-6xl font-bold text-[color:var(--color-text)] leading-tight mb-6">
                Project Truth Ministries<br>
                <span class="text-[color:var(--color-accent)]">Hebrew Texts & Conservative Analysis</span>
            </h1>
            <p class="mt-6 text-lg text-[color:var(--color-text-muted)] leading-relaxed max-w-2xl mx-auto">
                Preserving and analyzing the Cochin Hebrew New Testament manuscripts. 
                Rigorous textual criticism grounded in primary manuscript evidence.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="#resources" class="inline-flex items-center justify-center h-12 px-6 rounded-md bg-[color:var(--color-accent)] text-[color:var(--color-text-inv)] font-semibold hover:bg-[color:var(--color-accent-hi)] transition-colors">
                    Explore Resources
                </a>
                <a href="#about" class="inline-flex items-center justify-center h-12 px-6 rounded-md border border-[color:var(--color-border)] text-[color:var(--color-text)] hover:bg-[color:var(--color-surface-2)] transition-colors">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16" id="resources">
        <div class="mx-auto max-w-7xl px-4">
            <div class="text-center mb-12">
                <h2 class="font-serif text-3xl font-semibold text-[color:var(--color-text)] mb-4">
                    What We Offer
                </h2>
                <p class="text-lg text-[color:var(--color-text-muted)] max-w-2xl mx-auto">
                    Scholarly resources for biblical text study and analysis.
                </p>
            </div>
            
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="rounded-lg border border-[color:var(--color-border)] bg-[color:var(--color-surface)] p-6">
                    <h3 class="font-serif text-xl font-semibold text-[color:var(--color-text)] mb-2">Cochin Hebrew NT</h3>
                    <p class="text-[color:var(--color-text-muted)]">
                        Access to the Cochin Hebrew New Testament manuscripts with interlinear tables and critical notes.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="rounded-lg border border-[color:var(--color-border)] bg-[color:var(--color-surface)] p-6">
                    <h3 class="font-serif text-xl font-semibold text-[color:var(--color-text)] mb-2">Biblia Hebraica</h3>
                    <p class="text-[color:var(--color-text-muted)]">
                        Ancient Hebrew texts with critical apparatus and linguistic analysis.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="rounded-lg border border-[color:var(--color-border)] bg-[color:var(--color-surface)] p-6">
                    <h3 class="font-serif text-xl font-semibold text-[color:var(--color-text)] mb-2">Lexicon</h3>
                    <p class="text-[color:var(--color-text-muted)]">
                        Comprehensive Hebrew lexicon database for word studies and lexical analysis.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 bg-[color:var(--color-surface-2)]" id="about">
        <div class="mx-auto max-w-4xl px-4">
            <div class="text-center mb-12">
                <h2 class="font-serif text-3xl font-semibold text-[color:var(--color-text)] mb-4">
                    About Project Truth Ministries
                </h2>
                <p class="text-lg text-[color:var(--color-text-muted)] max-w-2xl mx-auto">
                    Our mission is to preserve and analyze ancient Hebrew texts through rigorous scholarly work.
                </p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16" id="contact">
        <div class="mx-auto max-w-2xl px-4 text-center">
            <h2 class="font-serif text-3xl font-semibold text-[color:var(--color-text)] mb-6">
                Stay Connected
            </h2>
            <p class="text-lg text-[color:var(--color-text-muted)] mb-8">
                Get updates on new research and publications.
            </p>
            <div class="flex justify-center">
                <a href="#" class="inline-flex items-center justify-center h-12 px-6 rounded-md bg-[color:var(--color-accent)] text-[color:var(--color-text-inv)] font-semibold hover:bg-[color:var(--color-accent-hi)] transition-colors">
                    Subscribe to Newsletter
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
