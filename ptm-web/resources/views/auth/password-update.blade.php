@extends('layouts.app')

@section('content')
<!-- Password Update Page -->
<div class="mx-auto max-w-md px-4 py-12">
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-xl p-8">
        <header class="text-center mb-8">
            <h1 class="text-2xl md:text-3xl font-serif font-semibold" style="color: var(--color-text);">Update Your Password</h1>
            <p class="mt-2" style="color: var(--color-text-muted);">
                This is your first login. Please set a new secure password.
            </p>
        </header>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium mb-1.5" style="color: var(--color-text);">Current Password</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                       class="w-full px-4 py-2.5 rounded-lg border bg-[var(--color-bg)]" 
                       style="border-color: var(--color-border); color: var(--color-text);"
                       placeholder="Enter current password">
                @error('current_password')
                    <p class="mt-1 text-sm" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium mb-1.5" style="color: var(--color-text);">New Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="w-full px-4 py-2.5 rounded-lg border bg-[var(--color-bg)]"
                       style="border-color: var(--color-border); color: var(--color-text);"
                       placeholder="Enter new password">
                @error('password')
                    <p class="mt-1 text-sm" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium mb-1.5" style="color: var(--color-text);">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="w-full px-4 py-2.5 rounded-lg border bg-[var(--color-bg)]"
                       style="border-color: var(--color-border); color: var(--color-text);"
                       placeholder="Confirm new password">
                @error('password_confirmation')
                    <p class="mt-1 text-sm" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-lg font-semibold text-sm transition"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection