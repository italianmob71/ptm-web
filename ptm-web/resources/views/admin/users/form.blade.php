@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        {{ $user->exists ? 'Edit: ' . $user->name : 'Add New User' }}
    </h1>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-danger); color: var(--color-danger);">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif

        <!-- Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   required>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">
                Password {{ $user->exists ? '<span class="text-xs" style="color: var(--color-text-faint);">(leave blank to keep current)</span>' : '*' }}
            </label>
            <input type="password" name="password"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   {{ $user->exists ? '' : 'required' }}>
            @if ($user->exists)
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">Leave blank to keep current password. Minimum 8 characters.</p>
            @endif
        </div>

        <!-- Password Confirmation -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full px-3 py-2 rounded-lg border"
                   style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                   {{ $user->exists ? '' : 'required' }}>
        </div>

        <!-- Security Group -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Security Level *</label>
            <select name="security_group"
                    class="w-full px-3 py-2 rounded-lg border"
                    style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                    required>
                @foreach ($levelLabels as $level => $label)
                    <option value="{{ $level }}" {{ (string) old('security_group', $user->security_group) === (string) $level ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs mt-1" style="color: var(--color-text-faint);">
                0=Public, 1=User, 2=Contributor, 3=Scholar, 4=Power User, 5=Admin, 9=Super Admin
            </p>
        </div>

        <!-- Checkboxes -->
        <div class="flex flex-wrap gap-6 mb-6">
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="force_update" value="1" {{ old('force_update', $user->force_update ?? false) ? 'checked' : '' }}>
                Force Password Update on Next Login
            </label>
            <label class="flex items-center gap-2 text-sm" style="color: var(--color-text);">
                <input type="checkbox" name="email_verified" value="1" {{ old('email_verified', $user->email_verified_at) ? 'checked' : '' }}>
                Email Verified
            </label>
        </div>

        <!-- Hidden field for email_verified_at date -->
        <input type="hidden" name="email_verified_at" value="{{ old('email_verified_at', $user->email_verified_at ? $user->email_verified_at->format('Y-m-d') : '') }}">

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                {{ $user->exists ? 'Update User' : 'Create User' }}
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="text-sm"
               style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>
@endsection