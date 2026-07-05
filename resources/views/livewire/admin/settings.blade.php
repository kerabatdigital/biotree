<div>
    {{-- Flash Messages --}}
    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
            <div class="flex items-center gap-3">
                <x-phosphor-check-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                <p class="text-sm text-emerald-800 dark:text-emerald-300">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-center gap-3">
                <x-phosphor-warning class="w-5 h-5 text-red-600 dark:text-red-400" />
                <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Site Settings --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Site Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Basic platform configuration</p>
                </div>
                <div class="p-6 space-y-6">
                    {{-- App Name --}}
                    <div>
                        <label for="appName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Application Name
                        </label>
                        <input type="text"
                               id="appName"
                               wire:model.live="appName"
                               class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                    </div>

                    {{-- Support Email --}}
                    <div>
                        <label for="supportEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Support Email
                        </label>
                        <input type="email"
                               id="supportEmail"
                               wire:model.live="supportEmail"
                               class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                    </div>

                    <button wire:click="saveSiteSettings"
                            class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                        Save Settings
                    </button>
                </div>
            </div>

            {{-- Feature Flags --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Feature Flags</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enable or disable platform features</p>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Maintenance Mode --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                                <x-phosphor-warning class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Maintenance Mode</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Put the site in maintenance mode</p>
                            </div>
                        </div>
                        <button wire:click="toggleMaintenanceMode"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                       {{ $maintenanceMode === 'on' ? 'bg-red-600' : 'bg-gray-200 dark:bg-gray-600' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $maintenanceMode === 'on' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    {{-- New Signups --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                <x-phosphor-user-plus class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">New Signups</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Allow new users to register</p>
                            </div>
                        </div>
                        <button wire:click="toggleNewSignups"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                       {{ $newSignups === 'enabled' ? 'bg-emerald-600' : 'bg-gray-200 dark:bg-gray-600' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $newSignups === 'enabled' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    {{-- Google Login --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                                <x-phosphor-google-logo class="w-5 h-5 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Google Login</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enable Google OAuth login</p>
                            </div>
                        </div>
                        <button wire:click="toggleGoogleLogin"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                       {{ $enableGoogleLogin ? 'bg-emerald-600' : 'bg-gray-200 dark:bg-gray-600' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $enableGoogleLogin ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    {{-- Analytics --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                                <x-phosphor-chart-line class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Analytics</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enable analytics tracking</p>
                            </div>
                        </div>
                        <button wire:click="toggleAnalytics"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                       {{ $enableAnalytics ? 'bg-emerald-600' : 'bg-gray-200 dark:bg-gray-600' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $enableAnalytics ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Reserved Usernames --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reserved Usernames</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">These usernames cannot be claimed by users</p>
                </div>
                <div class="p-6">
                    {{-- Add new --}}
                    <div class="flex gap-3 mb-4">
                        <input type="text"
                               wire:model.live="newReservedUsername"
                               wire:keydown.enter="addReservedUsername"
                               placeholder="Enter username to reserve..."
                               class="flex-1 px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                        <button wire:click="addReservedUsername"
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                            Add
                        </button>
                    </div>

                    {{-- List --}}
                    <div class="flex flex-wrap gap-2">
                        @forelse($reservedUsernames as $username)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                                @ {{ $username }}
                                <button wire:click="removeReservedUsername('{{ $username }}')"
                                        class="ml-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                                    <x-phosphor-x class="w-4 h-4" />
                                </button>
                            </span>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No reserved usernames configured.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Quick Actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button wire:click="clearCache"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <x-phosphor-broom class="w-5 h-5" />
                        Clear Application Cache
                    </button>
                </div>
            </div>

            {{-- Audit Log Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Admin Activity</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $auditLogStats['today'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $auditLogStats['this_week'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">This Week</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $auditLogStats['this_month'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">This Month</p>
                        </div>
                    </div>

                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Recent Activity</h4>
                    <div class="space-y-3">
                        @forelse($recentAuditLogs as $log)
                            <div class="flex items-start gap-3 text-sm">
                                <div class="p-1.5 bg-gray-100 dark:bg-gray-700 rounded">
                                    <x-phosphor-shield-check class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-gray-900 dark:text-white">{{ $log['action_display'] ?? $log['action'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($log['created_at'])->diffForHumans() }}
                                        @if($log['admin'])
                                            by {{ $log['admin']['name'] }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Platform Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Platform Info</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Laravel Version</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ app()->version() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">PHP Version</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ PHP_VERSION }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Environment</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ app()->environment() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Debug Mode</dt>
                            <dd class="text-sm font-medium {{ config('app.debug') ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
