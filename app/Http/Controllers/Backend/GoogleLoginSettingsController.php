<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GoogleLoginSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage global settings,admin');
    }

    public function index()
    {
        $settings = GlobalSetting::getAllSettings();

        // Google OAuth Settings
        $googleSettings = [
            'google_client_id' => $settings['google_client_id'] ?? config('services.google.client_id'),
            'google_client_secret' => $settings['google_client_secret'] ?? config('services.google.client_secret'),
            'google_redirect' => $settings['google_redirect'] ?? config('services.google.redirect', env('APP_URL') . '/auth/google/callback'),
        ];

        return view('backend.settings.google_login_settings', compact('googleSettings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_client_id' => 'required|string|max:255',
            'google_client_secret' => 'required|string|max:255',
            'google_redirect' => 'required|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update Google OAuth settings
            GlobalSetting::setValue('google_client_id', $request->input('google_client_id'));
            GlobalSetting::setValue('google_client_secret', $request->input('google_client_secret'));
            GlobalSetting::setValue('google_redirect', $request->input('google_redirect'));

            Log::info('Google login settings updated', [
                'client_id' => substr($request->input('google_client_id'), 0, 20) . '...',
            ]);

            return redirect()->route('admin.google-login.settings')
                ->with('success', 'Google login settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update Google login settings: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update settings. Please try again.')
                ->withInput();
        }
    }
}

