<div>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Users --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl">
                    <x-phosphor-users class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="flex items-center text-sm font-medium {{ $stats['total_users']['change'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                    @if($stats['total_users']['change'] >= 0)
                        <x-phosphor-trend-up class="w-4 h-4 mr-1" />
                    @else
                        <x-phosphor-trend-down class="w-4 h-4 mr-1" />
                    @endif
                    {{ $stats['total_users']['change'] }}%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_users']['value']) }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Total Users
                <span class="text-emerald-600 dark:text-emerald-400">+{{ $stats['total_users']['new'] }} this period</span>
            </p>
        </div>

        {{-- Profiles --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-xl">
                    <x-phosphor-user-circle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="px-2 py-1 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded-full">
                    {{ number_format($stats['total_profiles']['published']) }} published
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_profiles']['value']) }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Total Profiles
                <span class="text-blue-600 dark:text-blue-400">+{{ $stats['total_profiles']['new'] }} this period</span>
            </p>
        </div>

        {{-- Page Views --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/50 rounded-xl">
                    <x-phosphor-eye class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="flex items-center text-sm font-medium {{ ($stats['total_views']['change'] ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                    @if(($stats['total_views']['change'] ?? 0) >= 0)
                        <x-phosphor-trend-up class="w-4 h-4 mr-1" />
                    @else
                        <x-phosphor-trend-down class="w-4 h-4 mr-1" />
                    @endif
                    {{ $stats['total_views']['change'] ?? 0 }}%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->totalViews) }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Total Page Views
                <span class="text-purple-600 dark:text-purple-400">+{{ number_format($stats['total_views']['period'] ?? 0) }} this period</span>
            </p>
        </div>

        {{-- Link Clicks --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/50 rounded-xl">
                    <x-phosphor-cursor-click class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <span class="flex items-center text-sm font-medium {{ ($stats['total_clicks']['change'] ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                    @if(($stats['total_clicks']['change'] ?? 0) >= 0)
                        <x-phosphor-trend-up class="w-4 h-4 mr-1" />
                    @else
                        <x-phosphor-trend-down class="w-4 h-4 mr-1" />
                    @endif
                    {{ $stats['total_clicks']['change'] ?? 0 }}%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->totalClicks) }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Total Link Clicks
                <span class="text-orange-600 dark:text-orange-400">+{{ number_format($stats['total_clicks']['period'] ?? 0) }} this period</span>
            </p>
        </div>
    </div>

    {{-- Time Range Selector --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Growth Overview</h2>
        <div class="flex gap-2">
            <button wire:click="setDaysRange(7)"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                           {{ $daysRange === 7 ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                7 Days
            </button>
            <button wire:click="setDaysRange(30)"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                           {{ $daysRange === 30 ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                30 Days
            </button>
            <button wire:click="setDaysRange(90)"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                           {{ $daysRange === 90 ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                90 Days
            </button>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- User Growth Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">New Users</h3>
            <div class="h-48 flex items-end gap-1">
                @foreach($userGrowth as $data)
                    @php
                        $maxCount = collect($userGrowth)->max('count') ?: 1;
                        $height = $data['count'] > 0 ? max(4, ($data['count'] / $maxCount) * 100) : 4;
                    @endphp
                    <div class="flex-1 group relative">
                        <div class="bg-emerald-200 dark:bg-emerald-800 rounded-t transition-all hover:bg-emerald-300 dark:hover:bg-emerald-700"
                             style="height: {{ $height }}%"></div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                            {{ $data['date'] }}: {{ $data['count'] }} users
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @if($stats['open_reports']['value'] > 0)
                    <a href="{{ route('admin.reports') }}"
                       class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <x-phosphor-flag class="w-5 h-5 text-red-600 dark:text-red-400" />
                            <span class="font-medium text-red-900 dark:text-red-300">{{ $stats['open_reports']['value'] }} Open Reports</span>
                        </div>
                        <x-phosphor-arrow-right class="w-5 h-5 text-red-600 dark:text-red-400" />
                    </a>
                @endif
                <a href="{{ route('admin.users') }}"
                   class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                    <div class="flex items-center gap-3">
                        <x-phosphor-users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <span class="font-medium text-blue-900 dark:text-blue-300">Manage Users</span>
                    </div>
                    <x-phosphor-arrow-right class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </a>
                <a href="{{ route('admin.settings') }}"
                   class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                    <div class="flex items-center gap-3">
                        <x-phosphor-gear class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        <span class="font-medium text-purple-900 dark:text-purple-300">Platform Settings</span>
                    </div>
                    <x-phosphor-arrow-right class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </a>
            </div>
        </div>
    </div>

    {{-- Tables Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Signups --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Signups</h3>
                <a href="{{ route('admin.users') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentSignups as $user)
                    <div class="px-6 py-4 flex items-center gap-4">
                        <img src="{{ $user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) }}"
                             alt="{{ $user['name'] }}"
                             class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user['email'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($user['created_at'])->diffForHumans() }}</p>
                            @if(isset($user['profile']['username']))
                                <a href="{{ route('profile.show', $user['profile']['username']) }}"
                                   target="_blank"
                                   class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">
                                    @ {{ $user['profile']['username'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <x-phosphor-users class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>No users yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Top Profiles --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Top Profiles by Clicks</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($topProfiles as $profile)
                    <div class="px-6 py-4 flex items-center gap-4">
                        <img src="{{ $profile['avatar_path'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($profile['display_name'] ?? '') }}"
                             alt="{{ $profile['display_name'] }}"
                             class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600 object-cover" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $profile['display_name'] ?? $profile['username'] }}</p>
                            <a href="{{ route('profile.show', $profile['username']) }}"
                               target="_blank"
                               class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">
                                @ {{ $profile['username'] }}
                            </a>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                                {{ number_format($profile['links_sum_clicks_count'] ?? 0) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">clicks</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <x-phosphor-chart-line class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>No profile data yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
