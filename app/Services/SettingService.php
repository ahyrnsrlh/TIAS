<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SettingService
{
    public function getAllSettingsGrouped(): array
    {
        $all = Setting::all();
        $grouped = [
            'company' => [],
            'general' => [],
            'smtp' => [],
            'invoice_template' => [],
        ];

        foreach ($all as $s) {
            $group = $s->group ?: 'general';
            if (isset($grouped[$group])) {
                $grouped[$group][$s->key] = $s->value;
            } else {
                $grouped['general'][$s->key] = $s->value;
            }
        }

        return $grouped;
    }

    public function updateSettings(array $settings, ?int $userId = null): void
    {
        foreach ($settings as $key => $val) {
            $group = 'general';
            if (str_starts_with($key, 'company_') || str_starts_with($key, 'witel_')) {
                $group = 'company';
            } elseif (str_starts_with($key, 'smtp_') || str_starts_with($key, 'mail_')) {
                $group = 'smtp';
            } elseif (str_starts_with($key, 'invoice_') || str_starts_with($key, 'header_') || str_starts_with($key, 'stamp_')) {
                $group = 'invoice_template';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'group' => $group]
            );
        }

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'SETTINGS_UPDATED',
            'subject_type' => Setting::class,
            'description' => "Updated system settings and parameters",
            'properties' => $settings,
        ]);
    }

    public function testSmtpConnection(string $recipientEmail, ?int $userId = null): void
    {
        Mail::to($recipientEmail)->send(new \App\Mail\TestSmtpMail());

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'SMTP_TEST_SENT',
            'subject_type' => Setting::class,
            'description' => "Triggered SMTP test connection email to {$recipientEmail}",
        ]);
    }
}
