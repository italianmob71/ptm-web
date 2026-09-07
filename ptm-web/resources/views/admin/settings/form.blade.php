@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8">
    <h1 class="font-serif text-3xl font-bold mb-6" style="color: var(--color-text);">
        Edit Setting: {{ $setting->key }}
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

    <form method="POST" action="{{ route('admin.settings.update', $setting) }}">
        @csrf
        @method('PUT')

        <!-- Value -->
        <div class="mb-6">
            <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">Value</label>

            @if (in_array($setting->key, ['maintenance_mode', 'registration_enabled']))
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="value" value="0">
                    <input type="checkbox" name="value" value="1"
                           {{ old('value', $setting->value) === '1' ? 'checked' : '' }}
                           class="w-5 h-5 rounded"
                           style="accent-color: var(--color-accent);">
                    <span class="text-sm" style="color: var(--color-text);">
                        {{ $setting->key === 'maintenance_mode' ? 'Enable maintenance mode' : 'Enable public registration' }}
                    </span>
                </label>
                <p class="text-xs mt-1" style="color: var(--color-text-faint);">
                    @if ($setting->key === 'maintenance_mode')
                        When enabled, public visitors see a maintenance page. Super-admins retain full access to the admin panel.
                    @else
                        When disabled, new accounts must be created by a super-admin.
                    @endif
                </p>
            @elseif ($setting->key === 'site_name')
                <input type="text" name="value" value="{{ old('value', $setting->value) }}"
                       class="w-full px-3 py-2 rounded-lg border"
                       style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                       required maxlength="255">
            @else
                <textarea name="value" rows="3"
                          class="w-full px-3 py-2 rounded-lg border"
                          style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
                          maxlength="255">{{ old('value', $setting->value) }}</textarea>
            @endif
        </div>

        <!-- Description (read-only context, not editable via this form) -->
        <div class="mb-6 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-border-soft);">
            <p class="text-xs font-medium mb-1" style="color: var(--color-text-muted);">Description</p>
            <p class="text-sm" style="color: var(--color-text-muted);">{{ $setting->description ?? '—' }}</p>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-2 text-sm rounded-lg font-medium"
                    style="background-color: var(--color-accent); color: var(--color-text-inv);">
                Save
            </button>
            <a href="{{ route('admin.settings.index') }}"
               class="text-sm"
               style="color: var(--color-text-muted);">Cancel</a>
        </div>
    </form>
</div>
@endsection