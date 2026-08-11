@extends('layouts.app')

@section('content')
<!-- Login Page -->
<div class="mx-auto max-w-md px-4 py-12">
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-xl p-8">
        <header class="text-center mb-8">
            <h1 class="text-2xl md:text-3xl font-serif font-semibold" style="color: var(--color-text);">Sign In</h1>
            <p class="mt-2" style="color: var(--color-text-muted);">Enter your credentials to access your account</p>
        </header>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium mb-1.5" style="color: var(--color-text);">Email</label>
                <input id="email" type="email" name="email" 
                       value="{{ old('email') }}" required autocomplete="email"
                       class="w-full px-4 py-2.5 rounded-lg border bg-[var(--color-bg)]" 
                       style="border-color: var(--color-border); color: var(--color-text);"
                       placeholder="you@example.com">
                @error('email')
                    <p class="mt-1 text-sm" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium mb-1.5" style="color: var(--color-text);">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full px-4 py-2.5 rounded-lg border bg-[var(--color-bg)]"
                       style="border-color: var(--color-border); color: var(--color-text);"
                       placeholder="Enter your password">
                @error('password')
                    <p class="mt-1 text-sm" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" 
                       class="w-4 h-4 rounded border" 
                       style="border-color: var(--color-border); accent-color: var(--color-accent);">
                <label for="remember" class="text-sm" style="color: var(--color-text-muted);">Remember me</label>
            </div>

            <!-- Submit -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-lg font-semibold text-sm transition"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection