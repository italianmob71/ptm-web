@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Site Settings</h1>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-border); color: var(--color-success);">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg" style="background-color: var(--color-surface-2); border: 1px solid var(--color-danger); color: var(--color-danger);">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border" style="border-color: var(--color-border); background-color: var(--color-surface);">
        <table class="min-w-full text-sm">
            <thead style="background-color: var(--color-surface-2);">
                <tr>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Key</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Value</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Description</th>
                    <th class="text-right px-4 py-3 font-medium" style="color: var(--color-text-muted);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($settings as $setting)
                    <tr style="border-top: 1px solid var(--color-border-soft);">
                        <td class="px-4 py-3 font-mono text-sm" style="color: var(--color-accent);">
                            {{ $setting->key }}
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-text);">
                            {{ $setting->value }}
                            @if (in_array($setting->key, ['maintenance_mode', 'registration_enabled']))
                                @if ($setting->value === '1')
                                    <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium"
                                          style="background-color: var(--color-warning); color: var(--color-text-inv);">Enabled</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium"
                                          style="background-color: var(--color-surface-3); color: var(--color-text-muted);">Disabled</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm" style="color: var(--color-text-muted);">
                            {{ $setting->description }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.settings.edit', $setting) }}"
                               class="inline-block px-3 py-1 text-xs rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center" style="color: var(--color-text-muted);">
                            No settings found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection