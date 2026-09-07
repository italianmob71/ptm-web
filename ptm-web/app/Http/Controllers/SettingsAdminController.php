<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsAdminController extends Controller
{
    /**
     * Settings dashboard — single-card layout showing all key/value rows.
     */
    public function index()
    {
        $settings = Setting::orderBy('key')->get();

        return view('admin.settings.index', [
            'title'    => 'Site Settings',
            'settings' => $settings,
        ]);
    }

    /**
     * Show the edit form for a single setting.
     */
    public function edit(Setting $setting)
    {
        return view('admin.settings.form', [
            'title'   => 'Edit Setting: ' . $setting->key,
            'setting' => $setting,
        ]);
    }

    /**
     * Update a setting value.
     */
    public function update(Request $request, Setting $setting)
    {
        $isBool = in_array($setting->key, ['maintenance_mode', 'registration_enabled'], true);

        $validated = $request->validate([
            'value'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        // Force boolean keys to '0' or '1'
        if ($isBool) {
            $validated['value'] = $request->has('value') ? '1' : '0';
        }

        $setting->update($validated);

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Setting "' . $setting->key . '" updated successfully.');
    }
}