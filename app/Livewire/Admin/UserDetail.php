<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\LinkClick;
use App\Models\PageView;
use App\Models\Profile;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AccountRestoredNotification;
use App\Notifications\AccountSuspendedNotification;
use Livewire\Component;

class UserDetail extends Component
{
    public User $user;
    public array $stats = [];
    public array $recentActivity = [];
    public bool $showActionsModal = false;
    public string $actionType = '';
    public ?string $actionNotes = null;

    public function mount(User $user): void
    {
        $this->user = $user->load('profile');
        $this->loadStats();
        $this->loadRecentActivity();
    }

    public function render()
    {
        return view('livewire.admin.user-detail')
            ->layout('layouts.admin', ['title' => 'User: ' . $this->user->name]);
    }

    private function loadStats(): void
    {
        $profile = $this->user->profile;

        $totalViews = $profile
            ? PageView::where('profile_id', $profile->id)->count()
            : 0;

        $totalClicks = $profile
            ? LinkClick::where('profile_id', $profile->id)->count()
            : 0;

        $totalLinks = $profile ? $profile->links()->count() : 0;

        $reportsFiled = Report::where('reporter_email', $this->user->email)->count();

        $reportsAgainst = 0;
        if ($profile) {
            $reportsAgainst += Report::where('reportable_type', Profile::class)->where('reportable_id', $profile->id)->count();
        }
        $reportsAgainst += Report::where('reportable_type', User::class)->where('reportable_id', $this->user->id)->count();

        $this->stats = [
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'total_links' => $totalLinks,
            'reports_filed' => $reportsFiled,
            'reports_against' => $reportsAgainst,
            'last_login' => $this->user->last_login_at,
        ];
    }

    private function loadRecentActivity(): void
    {
        $profile = $this->user->profile;

        // Get recent page views
        $views = [];
        if ($profile) {
            $views = PageView::where('profile_id', $profile->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($view) {
                    return [
                        'type' => 'view',
                        'data' => $view,
                        'time' => $view->created_at,
                    ];
                })
                ->toArray();
        }

        // Get recent audit logs for this user
        $logs = AuditLog::where('target_type', User::class)
            ->where('target_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'admin_action',
                    'data' => $log,
                    'time' => $log->created_at,
                ];
            })
            ->toArray();

        // Merge and sort by time
        $this->recentActivity = collect($views)
            ->merge($logs)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
    }

    public function openActionsModal(string $action): void
    {
        $this->actionType = $action;
        $this->actionNotes = null;
        $this->showActionsModal = true;
    }

    public function closeActionsModal(): void
    {
        $this->showActionsModal = false;
        $this->actionType = '';
        $this->actionNotes = null;
    }

    public function confirmAction(): void
    {
        if (!$this->actionType) {
            return;
        }

        $adminId = auth()->id();
        $oldValues = [];

        switch ($this->actionType) {
            case 'suspend':
                $oldValues = ['status' => $this->user->status];
                $this->user->update(['status' => 'suspended']);
                AuditLog::log($adminId, 'suspend_user', $this->user, $oldValues, ['status' => 'suspended'], request()->ip());
                $this->user->notify(new AccountSuspendedNotification);
                session()->flash('message', 'User suspended successfully.');
                break;

            case 'restore':
                $oldValues = ['status' => $this->user->status];
                $this->user->update(['status' => 'active']);
                AuditLog::log($adminId, 'restore_user', $this->user, $oldValues, ['status' => 'active'], request()->ip());
                $this->user->notify(new AccountRestoredNotification);
                session()->flash('message', 'User restored successfully.');
                break;

            case 'make_admin':
                $oldValues = ['role' => $this->user->role];
                $this->user->update(['role' => 'admin']);
                AuditLog::log($adminId, 'make_admin', $this->user, $oldValues, ['role' => 'admin'], request()->ip());
                session()->flash('message', 'User promoted to admin.');
                break;

            case 'remove_admin':
                $oldValues = ['role' => $this->user->role];
                $this->user->update(['role' => 'user']);
                AuditLog::log($adminId, 'remove_admin', $this->user, $oldValues, ['role' => 'user'], request()->ip());
                session()->flash('message', 'Admin role removed.');
                break;

            case 'unpublish':
                if ($this->user->profile) {
                    $oldValues = ['is_published' => $this->user->profile->is_published];
                    $this->user->profile->update(['is_published' => false]);
                    AuditLog::log($adminId, 'unpublish_profile', $this->user->profile, $oldValues, ['is_published' => false], request()->ip());
                    session()->flash('message', 'Profile unpublished.');
                }
                break;

            case 'publish':
                if ($this->user->profile) {
                    $oldValues = ['is_published' => $this->user->profile->is_published];
                    $this->user->profile->update(['is_published' => true]);
                    AuditLog::log($adminId, 'publish_profile', $this->user->profile, $oldValues, ['is_published' => true], request()->ip());
                    session()->flash('message', 'Profile published.');
                }
                break;
        }

        // Refresh data
        $this->user->refresh();
        $this->user->load('profile');
        $this->loadStats();
        $this->loadRecentActivity();

        $this->closeActionsModal();
    }

    public function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'active' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
            'suspended' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        };
    }
}
