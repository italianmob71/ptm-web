@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">My Profile</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-border); color: var(--color-success);">
            {{ session('status') }}
        </div>
    @endif

    {{-- Profile Info --}}
    <div class="mb-8 p-6 rounded-lg border" style="background-color: var(--color-surface); border-color: var(--color-border);">
        <h2 class="font-semibold text-lg mb-4" style="color: var(--color-text);">Account Information</h2>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required>
                @error('name')
                    <p class="text-sm mt-1" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required>
                @error('email')
                    <p class="text-sm mt-1" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Security Level</label>
                <input type="text" value="{{ $user->security_group }}" disabled
                       class="w-full px-3 py-2 rounded-lg border text-sm font-mono"
                       style="border-color: var(--color-border); background-color: var(--color-surface-2); color: var(--color-text-muted);">
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">Contact an administrator to change your security level.</p>
            </div>

            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                Save Changes
            </button>
        </form>
    </div>

    {{-- Password Change --}}
    <div class="p-6 rounded-lg border" style="background-color: var(--color-surface); border-color: var(--color-border);">
        <h2 class="font-semibold text-lg mb-4" style="color: var(--color-text);">Change Password</h2>
        
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Current Password</label>
                <input type="password" name="current_password"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required autocomplete="current-password">
                @error('current_password')
                    <p class="text-sm mt-1" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">New Password</label>
                <input type="password" name="password"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required autocomplete="new-password">
                @error('password')
                    <p class="text-sm mt-1" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required autocomplete="new-password">
            </div>

            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection