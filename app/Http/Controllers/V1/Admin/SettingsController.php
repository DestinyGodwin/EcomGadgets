<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
     /**
     * List all settings
     */
    public function index()
    {
        return Setting::all();
    }

    /**
     * Show a specific setting by key
     */
    public function show(string $key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            throw ValidationException::withMessages([
                'key' => ['Setting not found.'],
            ]);
        }

        return $setting;
    }

    /**
     * Create a new setting
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'required|string',
        ]);

        $setting = Setting::create([
            'key' => $request->key,
            'value' => $request->value,
        ]);

        return response()->json(['message' => 'Setting created.', 'data' => $setting], 201);
    }

    /**
     * Update an existing setting by key
     */
    public function update(Request $request, string $key)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            throw ValidationException::withMessages([
                'key' => ['Setting not found.'],
            ]);
        }

        $setting->update(['value' => $request->value]);

        return response()->json(['message' => 'Setting updated.', 'data' => $setting]);
    }

    /**
     * Delete a setting by key
     */
    public function destroy(string $key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            throw ValidationException::withMessages([
                'key' => ['Setting not found.'],
            ]);
        }

        $setting->delete();

        return response()->json(['message' => 'Setting deleted.']);
    }
}
