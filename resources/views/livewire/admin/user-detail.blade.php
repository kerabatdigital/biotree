<div>
    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('admin.users') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">Users</a>
            <x-phosphor-caret-right class="w-4 h-4" />
            <span class="text-gray-900 dark:text-white">{{ $user->name }}</span>
        </nav>
    </div>

    {{-- Flash Message --}}
    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
            <div class="flex items-center gap-3">
                <x-phosphor-check-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                <p class="text-sm text-emerald-800 dark:text-emerald-300">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: User Info --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- User Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col items-center text-center">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                             alt="{{ $user->name }}"
                             class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-600 mb-4" />
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>

                        <div class="flex items-center gap-2 mt-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->role === 'admin' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->plan === 'pro' ? 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                {{ ucfirst($user->plan) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusBadgeClass($user->status) }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_views']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Views</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($stats['total_clicks']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Clicks</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['total_links']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Links</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ $user->profile ? route('profile.show', $user->profile->username) : '#' }}"
                           target="_blank"
                           class="flex-1 px-3 py-2 text-sm font-medium text-center text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-900 transition-colors">
                            <x-phosphor-eye class="w-4 h-4 inline mr-1" />
                            View Profile
                        </a>

                        @if($user->status === 'active')
                            <button wire:click="openActionsModal('suspend')"
                                    class="flex-1 px-3 py-2 text-sm font-medium text-center text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/50 rounded-lg hover:bg-red-200 dark:hover:bg-red-900 transition-colors">
                                <x-phosphor-prohibit class="w-4 h-4 inline mr-1" />
                                Suspend
                            </button>
                        @else
                            <button wire:click="openActionsModal('restore')"
                                    class="flex-1 px-3 py-2 text-sm font-medium text-center text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-900 transition-colors">
                                <x-phosphor-check-circle class="w-4 h-4 inline mr-1" />
                                Restore
                            </button>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2 mt-2">
                        @if($user->role === 'admin')
                            <button wire:click="openActionsModal('remove_admin')"
                                    class="flex-1 px-3 py-2 text-sm font-medium text-center text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-900/50 rounded-lg hover:bg-orange-200 dark:hover:bg-orange-900 transition-colors">
                                <x-phosphor-user-minus class="w-4 h-4 inline mr-1" />
                                Remove Admin
                            </button>
                        @else
                            <button wire:click="openActionsModal('make_admin')"
                                    class="flex-1 px-3 py-2 text-sm font-medium text-center text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900 transition-colors">
                                <x-phosphor-user-plus class="w-4 h-4 inline mr-1" />
                                Make Admin
                            </button>
                        @endif

                        @if($user->profile)
                            @if($user->profile->is_published)
                                <button wire:click="openActionsModal('unpublish')"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <x-phosphor-eye-slash class="w-4 h-4 inline mr-1" />
                                    Unpublish
                                </button>
                            @else
                                <button wire:click="openActionsModal('publish')"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <x-phosphor-eye class="w-4 h-4 inline mr-1" />
                                    Publish
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- User Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Details</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">User ID</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">#{{ $user->id }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[180px]">{{ $user->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Google ID</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[180px]">{{ $user->google_id ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Joined</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Last Login</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Reports Filed</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['reports_filed'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Reports Against</dt>
                        <dd class="text-sm font-medium text-red-600 dark:text-red-400">{{ $stats['reports_against'] }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Profile Preview --}}
            @if($user->profile)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Profile</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Username</dt>
                            <dd class="text-sm font-medium text-emerald-600 dark:text-emerald-400">@ {{ $user->profile->username }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Display Name</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->profile->display_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Published</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($user->profile->is_published)
                                    <span class="text-emerald-600 dark:text-emerald-400">Yes</span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">No</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Verified</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($user->profile->is_verified)
                                    <span class="text-blue-600 dark:text-blue-400">Yes</span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">No</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-center py-4">
                        <x-phosphor-user-circle class="w-12 h-12 mx-auto text-gray-400 mb-2" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">No profile created yet</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Activity & Links --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Links Table --}}
            @if($user->profile && $user->profile->links->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Links</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($user->profile->links as $link)
                            <div class="px-6 py-4 flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    @if($link->icon)
                                        <x-dynamic-component component="phosphor-{{ $link->icon }}" class="w-5 h-5 text-gray-400" />
                                    @else
                                        <x-phosphor-link class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $link->title }}</p>
                                    @if($link->url)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Str::limit($link->url, 40) }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $link->is_active ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                        {{ $link->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">{{ number_format($link->clicks_count) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">clicks</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent Activity --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentActivity as $activity)
                        <div class="px-6 py-4 flex items-start gap-4">
                            @if($activity['type'] === 'view')
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                                    <x-phosphor-eye class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900 dark:text-white">Page view recorded</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $activity['data']['country'] ?? 'Unknown' }} -
                                        {{ $activity['data']['device'] ?? 'Unknown' }}
                                    </p>
                                </div>
                            @elseif($activity['type'] === 'admin_action')
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                    <x-phosphor-shield-check class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $activity['data']['action_display'] ?? $activity['data']['action'] }}</p>
                                    @if($activity['data']['admin_notes'])
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['data']['admin_notes'] }}</p>
                                    @endif
                                </div>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <x-phosphor-clock class="w-12 h-12 mx-auto text-gray-400 mb-2" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Confirmation Modal --}}
    @if($showActionsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             x-data="{ show: true }"
             x-show="show"
             x-on:keydown.escape.window="show = false"
             x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full"
                 x-show="show"
                 x-transition>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        @if($actionType === 'suspend')
                            <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-xl">
                                <x-phosphor-prohibit class="w-6 h-6 text-red-600 dark:text-red-400" />
                            </div>
                        @elseif(in_array($actionType, ['restore', 'publish']))
                            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl">
                                <x-phosphor-check-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        @elseif($actionType === 'unpublish')
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-xl">
                                <x-phosphor-eye-slash class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                            </div>
                        @else
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-xl">
                                <x-phosphor-shield-check class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Confirm Action
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Are you sure you want to <strong>{{ str_replace('_', ' ', $actionType) }}</strong> this user?
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes (optional)
                        </label>
                        <textarea wire:model.live="actionNotes"
                                 rows="3"
                                 placeholder="Add any notes about this action..."
                                 class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="closeActionsModal"
                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="confirmAction"
                                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
