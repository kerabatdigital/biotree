<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Settings extends Component
{
    // Site Settings
    public string $appName = '';
    public string $supportEmail = '';
    public string $maintenanceMode = 'off';
    public string $newSignups = 'enabled';

    // Reserved Usernames
    public array $reservedUsernames = [];
    public string $newReservedUsername = '';

    // Feature Flags
    public bool $enableGoogleLogin = true;
    public bool $enableAnalytics = true;
    public bool $enablePublicProfiles = true;

    // Stats
    public array $auditLogStats = [];
    public array $recentAuditLogs = [];

    public function mount(): void
    {
        $this->loadSettings();
        $this->loadAuditLogs();
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('layouts.admin', ['title' => 'Settings']);
    }

    private function loadSettings(): void
    {
        $this->appName = config('app.name', 'BioTree');
        $this->supportEmail = config('biotree.support_email', 'support@biotree.my');
        $this->maintenanceMode = app()->isDownForMaintenance() ? 'on' : 'off';
        $this->newSignups = config('biotree.new_signups_enabled', true) ? 'enabled' : 'disabled';
        $this->reservedUsernames = config('biotree.reserved_usernames', []);

        // Feature flags from cache/config
        $this->enableGoogleLogin = config('services.google.enabled', true);
        $this->enableAnalytics = config('biotree.features.analytics', true);
        $this->enablePublicProfiles = config('biotree.features.public_profiles', true);
    }

    private function loadAuditLogs(): void
    {
        $this->auditLogStats = [
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => AuditLog::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        $this->recentAuditLogs = AuditLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function saveSiteSettings(): void
    {
        $this->validate([
            'appName' => 'required|string|max:255',
            'supportEmail' => 'required|email|max:255',
        ]);

        // Update config/database/env in production, use Cache for now
        Cache::put('biotree.app_name', $this->appName);
        Cache::put('biotree.support_email', $this->supportEmail);

        // Log the change
        AuditLog::log(
            auth()->id(),
            'update_settings',
            new \stdClass(),
            ['key' => 'site_settings'],
            ['app_name' => $this->appName, 'support_email' => $this->supportEmail],
            request()->ip(),
            request()->userAgent()
        );

        session()->flash('message', 'Site settings saved successfully.');
    }

    public function toggleMaintenanceMode(): void
    {
        if ($this->maintenanceMode === 'on') {
            // Enable maintenance mode
            \Artisan::call('down');
            $this->maintenanceMode = 'on';
        } else {
            // Disable maintenance mode
            \Artisan::call('up');
            $this->maintenanceMode = 'off';
        }

        AuditLog::log(
            auth()->id(),
            'toggle_maintenance',
            new \stdClass(),
            ['maintenance' => $this->maintenanceMode === 'on' ? 'off' : 'on'],
            ['maintenance' => $this->maintenanceMode],
            request()->ip(),
            request()->userAgent()
        );

        session()->flash('message', 'Maintenance mode ' . ($this->maintenanceMode === 'on' ? 'enabled' : 'disabled') . '.');
    }

    public function toggleNewSignups(): void
    {
        $this->newSignups = $this->newSignups === 'enabled' ? 'disabled' : 'enabled';
        Cache::put('biotree.new_signups_enabled', $this->newSignups === 'enabled');

        AuditLog::log(
            auth()->id(),
            'toggle_feature',
            new \stdClass(),
            ['new_signups' => $this->newSignups === 'disabled' ? 'enabled' : 'disabled'],
            ['new_signups' => $this->newSignups],
            request()->ip(),
            request()->userAgent()
        );

        session()->flash('message', 'New signups ' . ($this->newSignups === 'enabled' ? 'enabled' : 'disabled') . '.');
    }

    public function toggleGoogleLogin(): void
    {
        $this->enableGoogleLogin = !$this->enableGoogleLogin;

        AuditLog::log(
            auth()->id(),
            'toggle_feature',
            new \stdClass(),
            ['enable_google_login' => !$this->enableGoogleLogin],
            ['enable_google_login' => $this->enableGoogleLogin],
            request()->ip(),
            request()->userAgent()
        );

        session()->flash('message', 'Google login ' . ($this->enableGoogleLogin ? 'enabled' : 'disabled') . '.');
    }

    public function toggleAnalytics(): void
    {
        $this->enableAnalytics = !$this->enableAnalytics;

        AuditLog::log(
            auth()->id(),
            'toggle_feature',
            new \stdClass(),
            ['enable_analytics' => !$this->enableAnalytics],
            ['enable_analytics' => $this->enableAnalytics],
            request()->ip(),
            request()->userAgent()
        );

        session()->flash('message', 'Analytics ' . ($this->enableAnalytics ? 'enabled' : 'disabled') . '.');
    }

    public function addReservedUsername(): void
    {
        $username = strtolower(trim($this->newReservedUsername));

        if (empty($username)) {
            return;
        }

        if (!preg_match('/^[a-z0-9_]+$/', $username)) {
            session()->flash('error', 'Username can only contain letters, numbers, and underscores.');
            return;
        }

        if (in_array($username, $this->reservedUsernames)) {
            session()->flash('error', 'Username is already reserved.');
            return;
        }

        $oldReserved = $this->reservedUsernames;
        $this->reservedUsernames[] = $username;
        sort($this->reservedUsernames);

        AuditLog::log(
            auth()->id(),
            'update_reserved_usernames',
            new \stdClass(),
            ['reserved_usernames' => $oldReserved],
            ['reserved_usernames' => $this->reservedUsernames, 'added' => $username],
            request()->ip(),
            request()->userAgent()
        );

        $this->newReservedUsername = '';
        session()->flash('message', "Username '$username' added to reserved list.");
    }

    public function removeReservedUsername(string $username): void
    {
        $oldReserved = $this->reservedUsernames;
        $this->reservedUsernames = array_values(array_filter($this->reservedUsernames, fn($u) => $u !== $username));

        AuditLog::log(
            auth()->id(),
            'update_reserved_usernames',
            new \stdClass(),
            ['reserved_usernames' => $oldReserved],
            ['reserved_usernames' => $this->reservedUsernames, 'removed' => $username],
            request()->ip(),
            request()->userAgent()
        );

        session()->flash('message', "Username '$username' removed from reserved list.");
    }

    public function clearCache(): void
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        AuditLog::log(
            auth()->id(),
            'update_settings',
            new \stdClass(),
            [],
            ['action' => 'clear_cache'],
            request()->ip(),
            request()->userAgent()
        );

        session()->flash('message', 'Application cache cleared.');
    }
}
