<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Subscriptions</h1>

        {{-- Filters --}}
        <div class="mb-6 space-y-4">
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="search" value="Search by name, email, or username" />
                    <x-text-input wire:model.live="search" id="search" type="text" class="mt-1 block w-full" placeholder="Search..." />
                </div>

                <div>
                    <x-input-label for="statusFilter" value="Status" />
                    <select wire:model.live="statusFilter" id="statusFilter" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="all">All</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Subscriptions Table --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            @if ($subscriptions->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Expires</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscriptions as $sub)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $sub->user->name }}</span>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $sub->user->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ $sub->plan->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ $sub->expires_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex px-2 py-1 rounded text-xs font-medium
                                            @if ($sub->status === 'active')
                                                bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300
                                            @elseif ($sub->status === 'expired')
                                                bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300
                                            @else
                                                bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                            @endif
                                        ">
                                            {{ ucfirst($sub->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm space-x-2">
                                        <button wire:click="openActionModal({{ $sub->id }}, 'upgrade')" class="text-emerald-600 dark:text-emerald-400 hover:underline">
                                            Change Plan
                                        </button>
                                        @if ($sub->status === 'active')
                                            <button wire:click="openActionModal({{ $sub->id }}, 'cancel')" class="text-red-600 dark:text-red-400 hover:underline">
                                                Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $subscriptions->links() }}
                </div>
            @else
                <div class="p-8 text-center text-gray-600 dark:text-gray-400">
                    <p>No subscriptions found.</p>
                </div>
            @endif
        </div>

        {{-- Action Modal --}}
        @if ($showActionModal)
            <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ match($actionType) { 'upgrade', 'downgrade' => 'Change Plan', 'cancel' => 'Cancel Subscription', default => '' } }}
                </h2>

                <form wire:submit="performAction" class="space-y-4">
                    @if ($actionType !== 'cancel')
                        <div>
                            <x-input-label for="actionPlanId" value="Select Plan" />
                            <select wire:model="actionPlanId" id="actionPlanId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Choose a plan --</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('actionPlanId')" class="mt-1" />
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">This will cancel the subscription immediately.</p>
                    @endif

                    <div class="flex gap-2 pt-4">
                        <x-primary-button type="submit">
                            Confirm
                        </x-primary-button>
                        <x-secondary-button type="button" wire:click="closeActionModal">
                            Cancel
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
