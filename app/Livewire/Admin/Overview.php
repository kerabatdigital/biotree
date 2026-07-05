<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Profile;
use App\Models\Link;
use App\Models\Report;
use App\Models\LinkClick;
use App\Models\PageView;
use Livewire\Component;
use Carbon\Carbon;

class Overview extends Component
{
    public array $stats = [];
    public array $userGrowth = [];
    public array $recentSignups = [];
    public array $topProfiles = [];
    public int $daysRange = 30;

    public function mount(): void
    {
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.admin.overview')
            ->layout('layouts.admin', ['title' => 'Overview']);
    }

    public function setDaysRange(int $days): void
    {
        $this->daysRange = $days;
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $startDate = Carbon::now()->subDays($this->daysRange);
        $previousStartDate = Carbon::now()->subDays($this->daysRange * 2);

        // Current period stats
        $currentUsers = User::where('created_at', '>=', $startDate)->count();
        $previousUsers = User::whereBetween('created_at', [$previousStartDate, $startDate])->count();

        $currentProfiles = Profile::where('created_at', '>=', $startDate)->count();
        $currentPublished = Profile::where('is_published', true)->count();

        $currentLinks = Link::where('created_at', '>=', $startDate)->count();

        $currentViews = PageView::where('created_at', '>=', $startDate)->count();
        $previousViews = PageView::whereBetween('created_at', [$previousStartDate, $startDate])->count();

        $currentClicks = LinkClick::where('created_at', '>=', $startDate)->count();
        $previousClicks = LinkClick::whereBetween('created_at', [$previousStartDate, $startDate])->count();

        $openReports = Report::where('status', 'open')->count();

        // Calculate percentage changes
        $userChange = $previousUsers > 0 ? round((($currentUsers - $previousUsers) / $previousUsers) * 100, 1) : ($currentUsers > 0 ? 100 : 0);
        $viewChange = $previousViews > 0 ? round((($currentViews - $previousViews) / $previousViews) * 100, 1) : ($currentViews > 0 ? 100 : 0);
        $clickChange = $previousClicks > 0 ? round((($currentClicks - $previousClicks) / $previousClicks) * 100, 1) : ($currentClicks > 0 ? 100 : 0);

        $this->stats = [
            'total_users' => [
                'value' => User::count(),
                'change' => $userChange,
                'new' => $currentUsers,
            ],
            'total_profiles' => [
                'value' => Profile::count(),
                'published' => $currentPublished,
                'new' => $currentProfiles,
            ],
            'total_links' => [
                'value' => Link::count(),
                'new' => $currentLinks,
            ],
            'total_views' => [
                'value' => $this->stats['total_views']['value'] ?? PageView::count(),
                'period' => $currentViews,
                'change' => $viewChange,
            ],
            'total_clicks' => [
                'value' => $this->stats['total_clicks']['value'] ?? LinkClick::count(),
                'period' => $currentClicks,
                'change' => $clickChange,
            ],
            'open_reports' => [
                'value' => $openReports,
            ],
        ];

        // User growth data (last 30 days)
        $this->loadUserGrowth();

        // Recent signups
        $this->recentSignups = User::with('profile')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        // Top profiles by clicks
        $this->topProfiles = Profile::with('user')
            ->where('is_published', true)
            ->withSum('links', 'clicks_count')
            ->orderBy('links_sum_clicks_count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function loadUserGrowth(): void
    {
        $days = 30;
        $growth = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = User::whereDate('created_at', $date->toDateString())->count();
            $growth[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }

        $this->userGrowth = $growth;
    }

    public function getTotalViewsProperty(): int
    {
        return PageView::count();
    }

    public function getTotalClicksProperty(): int
    {
        return LinkClick::count();
    }
}
