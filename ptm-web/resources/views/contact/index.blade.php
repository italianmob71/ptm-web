@extends('layouts.app')

@section('content')
<style>
    .contact-wrap {
        max-width: 42rem;
        margin: 0 auto;
        padding: 3rem 1.5rem 5rem;
    }
    .contact-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        padding: 2.5rem;
    }
    @media (min-width: 768px) {
        .contact-card { padding: 3rem; }
    }
    .contact-title {
        font-family: var(--font-serif);
        font-size: 2.25rem;
        font-weight: 600;
        margin: 0 0 0.5rem;
        color: var(--color-text);
    }
    .contact-lede {
        color: var(--color-text-muted);
        margin: 0 0 2rem;
        line-height: 1.6;
    }
    .form-field {
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--color-text);
    }
    .form-label .req { color: var(--color-danger); margin-left: 2px; }
    .form-input,
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
        background-color: var(--color-bg);
        color: var(--color-text);
        font-size: 1rem;
        font-family: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--color-accent);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-accent) 25%, transparent);
    }
    .form-textarea {
        min-height: 180px;
        resize: vertical;
    }
    .form-error {
        color: var(--color-danger);
        font-size: 0.8125rem;
        margin-top: 0.375rem;
    }
    .form-actions { margin-top: 1.5rem; }
    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        background: var(--color-accent);
        color: var(--color-text-inv);
        font-weight: 600;
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }
    .btn-submit:hover { background: var(--color-accent-hi); transform: translateY(-1px); }
    .alert {
        padding: 0.875rem 1.125rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        font-size: 0.9375rem;
        line-height: 1.5;
    }
    .alert-success {
        background: color-mix(in srgb, var(--color-success) 15%, var(--color-surface));
        border: 1px solid var(--color-success);
        color: var(--color-text);
    }
    .alert-error {
        background: color-mix(in srgb, var(--color-danger) 15%, var(--color-surface));
        border: 1px solid var(--color-danger);
        color: var(--color-text);
    }
    /* Honeypot — invisible to users, irresistible to bots */
    .hp-field {
        position: absolute;
        left: -9999px;
        top: -9999px;
        width: 1px;
        height: 1px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
        /* display:none breaks some bots' auto-fill; keep it rendered but off-screen */
    }
</style>

<div class="contact-wrap">
    <div class="contact-card">
        <h1 class="contact-title">Contact Us</h1>
        <p class="contact-lede">
            Questions, comments, or manuscript inquiries? Fill out the form below and we will respond as soon as we can.
        </p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('contact.submit') }}" novalidate>
            @csrf

            {{-- Time-trap: server records render time, rejects sub-3-second submissions --}}
            <input type="hidden" name="_t" value="{{ time() }}">

            {{-- Honeypot: invisible to humans, irresistible to bots --}}
            <div class="hp-field" aria-hidden="true">
                <label for="website">Website (leave blank)</label>
                <input type="text"
                       id="website"
                       name="website"
                       tabindex="-1"
                       autocomplete="off"
                       value="">
            </div>

            <div class="form-field">
                <label class="form-label" for="name">Name <span class="req">*</span></label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-input"
                       value="{{ old('name') }}"
                       required
                       maxlength="120">
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="email">Email <span class="req">*</span></label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input"
                       value="{{ old('email') }}"
                       required
                       maxlength="190">
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="subject">Subject <span class="req">*</span></label>
                <input type="text"
                       id="subject"
                       name="subject"
                       class="form-input"
                       value="{{ old('subject') }}"
                       required
                       maxlength="200">
                @error('subject')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="message">Message <span class="req">*</span></label>
                <textarea id="message"
                          name="message"
                          class="form-textarea"
                          required
                          minlength="10"
                          maxlength="5000">{{ old('message') }}</textarea>
                @error('message')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    Send Message <span aria-hidden="true">&rarr;</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
