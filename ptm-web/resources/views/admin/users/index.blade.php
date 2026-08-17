@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-3xl font-bold" style="color: var(--color-text);">Users Dashboard</h1>
        <a href="{{ route('admin.users.create') }}"
           class="px-4 py-2 text-sm rounded-lg font-medium"
           style="background-color: var(--color-accent); color: var(--color-text-inv);">
            + Add New User
        </a>
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
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Name</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Email</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Security Level</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Force Update</th>
                    <th class="text-left px-4 py-3 font-medium" style="color: var(--color-text-muted);">Verified</th>
                    <th class="text-right px-4 py-3 font-medium" style="color: var(--color-text-muted);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr style="border-top: 1px solid var(--color-border-soft);">
                        <td class="px-4 py-3" style="color: var(--color-text);">
                            {{ $user->name }}
                        </td>
                        <td class="px-4 py-3 font-mono text-sm" style="color: var(--color-text-muted);">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-mono font-medium"
                                  style="background-color: var(--color-surface-3); color: var(--color-text);">
                                {{ $levelLabels[$user->security_group] ?? $user->security_group }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->force_update)
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-warning); color: var(--color-text-inv);">Yes</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-surface-3); color: var(--color-text-muted);">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->email_verified_at)
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-success); color: var(--color-text-inv);">Yes</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: var(--color-surface-3); color: var(--color-text-muted);">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="inline-block px-3 py-1 text-xs rounded border"
                               style="border-color: var(--color-border); color: var(--color-text);">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  class="inline-block"
                                  onsubmit="return confirm('Delete this user? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 text-xs rounded border"
                                        style="border-color: var(--color-danger); color: var(--color-danger);">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center" style="color: var(--color-text-muted);">
                            No users yet. Click "Add New User" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection