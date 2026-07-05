<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Reports</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Moderation queue for reported content</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-6 text-sm">
                <div class="text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $counts['open'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Open</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $counts['reviewed'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Reviewed</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $counts['total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Status tabs --}}
            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button wire:click="$set('statusFilter', 'open')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors
                               {{ $statusFilter === 'open' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Open
                    @if($counts['open'] > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-xs bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-full">{{ $counts['open'] }}</span>
                    @endif
                </button>
                <button wire:click="$set('statusFilter', 'reviewed')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors
                               {{ $statusFilter === 'reviewed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Reviewed
                </button>
                <button wire:click="$set('statusFilter', 'actioned')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors
                               {{ $statusFilter === 'actioned' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Actioned
                </button>
                <button wire:click="$set('statusFilter', 'dismissed')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors
                               {{ $statusFilter === 'dismissed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    Dismissed
                </button>
                <button wire:click="$set('statusFilter', 'all')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors
                               {{ $statusFilter === 'all' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    All
                </button>
            </div>

            <div class="flex-1"></div>

            {{-- Reason filter --}}
            <select wire:model.live="reasonFilter"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="all">All Reasons</option>
                @foreach(\App\Models\Report::REASONS as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
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

    {{-- Reports List --}}
    <div class="space-y-4">
        @forelse($reports as $report)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        {{-- Type Icon --}}
                        <div class="flex-shrink-0">
                            @if($report->reportable_type === 'App\\Models\\Profile')
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-xl">
                                    <x-phosphor-user-circle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                            @elseif($report->reportable_type === 'App\\Models\\Link')
                                <div class="p-3 bg-orange-100 dark:bg-orange-900/50 rounded-xl">
                                    <x-phosphor-link class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                                </div>
                            @else
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-xl">
                                    <x-phosphor-user class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getReasonBadgeClass($report->reason) }}">
                                    {{ \App\Models\Report::REASONS[$report->reason] ?? $report->reason }}
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusBadgeClass($report->status) }}">
                                    {{ \App\Models\Report::STATUSES[$report->status] ?? $report->status }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-900 dark:text-white mb-2">
                                <strong>Reported:</strong>
                                {{ class_basename($report->reportable_type) }} -
                                {{ $report->reportable_display_name }}
                            </p>

                            @if($report->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    <strong>Description:</strong> {{ $report->description }}
                                </p>
                            @endif

                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                @if($report->reporter_email)
                                    <span>Reporter: {{ $report->reporter_email }}</span>
                                @endif
                                <span>Submitted: {{ $report->created_at->format('M d, Y H:i') }}</span>
                                @if($report->handler)
                                    <span>Handled by: {{ $report->handler->name }}</span>
                                @endif
                            </div>

                            @if($report->admin_notes)
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Admin Notes:</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $report->admin_notes }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        @if($report->status === 'open')
                            <div class="flex flex-col gap-2">
                                <button wire:click="openActionModal({{ $report->id }}, 'action')"
                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                    Take Action
                                </button>
                                <button wire:click="openActionModal({{ $report->id }}, 'review')"
                                        class="px-4 py-2 text-sm font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900 transition-colors">
                                    Mark Reviewed
                                </button>
                                <button wire:click="openActionModal({{ $report->id }}, 'dismiss')"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    Dismiss
                                </button>
                            </div>
                        @elseif($report->status === 'reviewed')
                            <div class="flex flex-col gap-2">
                                <button wire:click="openActionModal({{ $report->id }}, 'action')"
                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                    Take Action
                                </button>
                                <button wire:click="openActionModal({{ $report->id }}, 'dismiss')"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    Dismiss
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                <x-phosphor-flag class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No reports found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if($statusFilter === 'open')
                        All reports have been handled!
                    @else
                        Try adjusting your filters.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($reports->hasPages())
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @endif

    {{-- Action Confirmation Modal --}}
    @if($showActionModal && $this->selectedReport)
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
                        @if($actionType === 'action')
                            <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-xl">
                                <x-phosphor-warning class="w-6 h-6 text-red-600 dark:text-red-400" />
                            </div>
                        @elseif($actionType === 'dismiss')
                            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-xl">
                                <x-phosphor-x-circle class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                            </div>
                        @else
                            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/50 rounded-xl">
                                <x-phosphor-eye class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ ucfirst($actionType) }} Report
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($actionType === 'action')
                                    This will take action on the reported content (unpublish/suspend).
                                @elseif($actionType === 'dismiss')
                                    This will dismiss the report without taking any action.
                                @else
                                    This will mark the report as reviewed.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Admin Notes
                        </label>
                        <textarea wire:model.live="actionNotes"
                                 rows="4"
                                 placeholder="Add notes about this decision..."
                                 class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="closeActionModal"
                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="confirmAction"
                                class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors
                                       {{ $actionType === 'action' ? 'bg-red-600 hover:bg-red-700' : ($actionType === 'dismiss' ? 'bg-gray-600 hover:bg-gray-700' : 'bg-yellow-600 hover:bg-yellow-700') }}">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
