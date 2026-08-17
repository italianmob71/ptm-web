<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    /**
     * Security level labels for the dropdown.
     */
    protected array $levelLabels = [
        0 => '0 — Public',
        1 => '1 — User',
        2 => '2 — Contributor',
        3 => '3 — Scholar',
        4 => '4 — Power User',
        5 => '5 — Admin',
        9 => '9 — Super Admin',
    ];

    /**
     * Users dashboard — list all users ordered by security_group desc, then name.
     */
    public function index()
    {
        $users = User::orderByDesc('security_group')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.users.index', [
            'title' => 'Users Dashboard',
            'users' => $users,
            'levelLabels' => $this->levelLabels,
        ]);
    }

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        return view('admin.users.form', [
            'title' => 'Add New User',
            'user' => new User(),
            'levelLabels' => $this->levelLabels,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
            'security_group'   => ['required', 'integer', 'in:0,1,2,3,4,5,9'],
            'force_update'     => ['boolean'],
            'email_verified'   => ['boolean'],
            'email_verified_at' => ['nullable', 'date'],
        ]);

        $validated['force_update'] = $request->has('force_update');
        $validated['password'] = Hash::make($validated['password']);

        // Handle email_verified checkbox -> set email_verified_at
        if ($request->has('email_verified')) {
            $validated['email_verified_at'] = $validated['email_verified_at'] ?? now();
        } else {
            $validated['email_verified_at'] = null;
        }

        // New users with password set don't need force_update
        if ($validated['force_update'] && !empty($validated['password'])) {
            $validated['force_update'] = false;
        }

        // Remove the checkbox field
        unset($validated['email_verified']);

        $user = User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User \"{$user->name}\" created successfully.");
    }

    /**
     * Show the form to edit an existing user.
     */
    public function edit(User $user)
    {
        return view('admin.users.form', [
            'title' => "Edit: {$user->name}",
            'user' => $user,
            'levelLabels' => $this->levelLabels,
        ]);
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
            'security_group'   => ['required', 'integer', 'in:0,1,2,3,4,5,9'],
            'force_update'     => ['boolean'],
            'email_verified'   => ['boolean'],
            'email_verified_at' => ['nullable', 'date'],
        ]);

        $validated['force_update'] = $request->has('force_update');

        // Handle email_verified checkbox -> set email_verified_at
        if ($request->has('email_verified')) {
            $validated['email_verified_at'] = $validated['email_verified_at'] ?? now();
        } else {
            $validated['email_verified_at'] = null;
        }

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
            // When password is changed via admin, clear force_update
            $validated['force_update'] = false;
        } else {
            unset($validated['password']);
        }

        // Remove the checkbox field
        unset($validated['email_verified']);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User \"{$user->name}\" updated successfully.");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User \"{$name}\" deleted.");
    }
}