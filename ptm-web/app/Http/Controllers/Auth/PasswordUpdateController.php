<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordUpdateController extends Controller
{
    /**
     * Display the password update form.
     */
    public function create(): View
    {
        return view('auth.password-update');
    }

    /**
     * Handle the password update request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = $request->password;
        $user->force_update = false;
        $user->save();

        return redirect()->intended(route('home'))->with('status', 'password-updated');
    }
}