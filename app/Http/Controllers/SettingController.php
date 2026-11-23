<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display system settings portal.
     */
    public function index(): Response
    {
        $settings = $this->settingService->getAllSettingsGrouped();

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
        ]);

        $this->settingService->updateSettings($validated['settings'], $request->user()?->id);

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Send test SMTP connection email.
     */
    public function testSmtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient' => ['required', 'email'],
        ]);

        $this->settingService->testSmtpConnection($validated['recipient'], $request->user()?->id);

        return redirect()->back()->with('success', "Test SMTP email sent to {$validated['recipient']}.");
    }
}
