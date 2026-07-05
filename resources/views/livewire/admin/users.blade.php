<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Users</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all registered users</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $users->total() }} users
            </span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label for="search" class="sr-only">Search</label>
                <div class="relative">
                    <x-phosphor-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input type="text"
                           id="search"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search name, email, or username..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
            </div>

            {{-- Status Filter --}}
            <div>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>

            {{-- Plan Filter --}}
            <div>
                <select wire:model.live="planFilter"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="all">All Plans</option>
                    <option value="free">Free</option>
                    <option value="pro">Pro</option>
                </select>
            </div>

            {{-- Role Filter --}}
            <div>
                <select wire:model.live="roleFilter"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="all">All Roles</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>

        {{-- Active filters display --}}
        @if($search || $statusFilter !== 'all' || $planFilter !== 'all' || $roleFilter !== 'all')
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-500 dark:text-gray-400">Active filters:</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded">
                        "{{ $search }}"
                        <button wire:click="$set('search', '')" class="hover:text-emerald-900 dark:hover:text-emerald-200">
                            <x-phosphor-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif
                @if($statusFilter !== 'all')
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded">
                        Status: {{ $statusFilter }}
                        <button wire:click="$set('statusFilter', 'all')" class="hover:text-emerald-900 dark:hover:text-emerald-200">
                            <x-phosphor-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif
                @if($planFilter !== 'all')
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded">
                        Plan: {{ $planFilter }}
                        <button wire:click="$set('planFilter', 'all')" class="hover:text-emerald-900 dark:hover:text-emerald-200">
                            <x-phosphor-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif
                @if($roleFilter !== 'all')
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded">
                        Role: {{ $roleFilter }}
                        <button wire:click="$set('roleFilter', 'all')" class="hover:text-emerald-900 dark:hover:text-emerald-200">
                            <x-phosphor-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif
                <button wire:click="clearFilters"
                        class="ml-auto text-sm text-emerald-600 dark:text-emerald-400 hover:underline">
                    Clear all
                </button>
            </div>
        @endif
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

    {{-- Users Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <button wire:click="sortBy('role')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">
                                Role
                                @if($sortBy === 'role')
                                    <x-phosphor-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <button wire:click="sortBy('plan')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">
                                Plan
                                @if($sortBy === 'plan')
                                    <x-phosphor-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <button wire:click="sortBy('created_at')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">
                                Joined
                                @if($sortBy === 'created_at')
                                    <x-phosphor-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                                         alt="{{ $user->name }}"
                                         class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        @if($user->profile)
                                            <a href="{{ route('profile.show', $user->profile->username) }}"
                                               target="_blank"
                                               class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">
                                                @ {{ $user->profile->username }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getRoleBadgeClass($user->role) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getPlanBadgeClass($user->plan) }}">
                                    {{ ucfirst($user->plan) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusBadgeClass($user->status) }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="p-2 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"
                                       title="View details">
                                        <x-phosphor-eye class="w-4 h-4" />
                                    </a>

                                    {{-- Actions dropdown --}}
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open"
                                                @click.away="open = false"
                                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                                title="More actions">
                                            <x-phosphor-dots-three-vertical class="w-4 h-4" />
                                        </button>
                                        <div x-show="open"
                                             x-transition
                                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10">
                                            <div class="py-1">
                                                @if($user->status === 'active')
                                                    <button wire:click="openActionsModal({{ $user->id }}, 'suspend')"
                                                            class="w-full px-4 py-2 text-left text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                        <x-phosphor-prohibit class="w-4 h-4 inline mr-2" />
                                                        Suspend User
                                                    </button>
                                                @else
                                                    <button wire:click="openActionsModal({{ $user->id }}, 'restore')"
                                                            class="w-full px-4 py-2 text-left text-sm text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                                        <x-phosphor-check-circle class="w-4 h-4 inline mr-2" />
                                                        Restore User
                                                    </button>
                                                @endif

                                                @if($user->role === 'admin')
                                                    <button wire:click="openActionsModal({{ $user->id }}, 'remove_admin')"
                                                            class="w-full px-4 py-2 text-left text-sm text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20">
                                                        <x-phosphor-user-minus class="w-4 h-4 inline mr-2" />
                                                        Remove Admin
                                                    </button>
                                                @else
                                                    <button wire:click="openActionsModal({{ $user->id }}, 'make_admin')"
                                                            class="w-full px-4 py-2 text-left text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                        <x-phosphor-user-plus class="w-4 h-4 inline mr-2" />
                                                        Make Admin
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <x-phosphor-users class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                                <p class="text-gray-500 dark:text-gray-400">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
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
                        @elseif($actionType === 'restore')
                            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl">
                                <x-phosphor-check-circle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        @elseif($actionType === 'make_admin')
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-xl">
                                <x-phosphor-shield-check class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        @else
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/50 rounded-xl">
                                <x-phosphor-user-minus class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ ucfirst(str_replace('_', ' ', $actionType)) }} User
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($this->selectedUser)
                                    Are you sure you want to {{ $actionType }} <strong>{{ $this->selectedUser->name }}</strong>?
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Notes field --}}
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
