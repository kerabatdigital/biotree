<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Billing Plans</h1>
            <button wire:click="newPlan" class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-neutral-950 transition hover:bg-emerald-400">
                <x-phosphor-plus class="h-4 w-4" />
                New Plan
            </button>
        </div>

        {{-- Plans Table --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            @if ($plans->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Monthly</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Yearly</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Subscriptions</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($plans as $plan)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $plan->name }}</span>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $plan->slug }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        @if ($plan->monthly_price_cents)
                                            RM {{ number_format($plan->monthly_price_cents / 100, 2) }}/mo
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        @if ($plan->yearly_price_cents)
                                            RM {{ number_format($plan->yearly_price_cents / 100, 2) }}/yr
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ $plan->subscriptions()->count() }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <button wire:click="toggleActive({{ $plan->id }})" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium transition
                                            @if ($plan->is_active)
                                                bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200
                                            @else
                                                bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200
                                            @endif
                                        ">
                                            @if ($plan->is_active)
                                                <x-phosphor-check-circle class="w-3 h-3" />
                                                Active
                                            @else
                                                <x-phosphor-x-circle class="w-3 h-3" />
                                                Inactive
                                            @endif
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm space-x-2">
                                        <button wire:click="editPlan({{ $plan->id }})" class="inline-flex text-emerald-600 dark:text-emerald-400 hover:underline">
                                            Edit
                                        </button>
                                        @if (!$plan->subscriptions()->exists())
                                            <button wire:click="deletePlan({{ $plan->id }})" class="inline-flex text-red-600 dark:text-red-400 hover:underline">
                                                Delete
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-600 dark:text-gray-400">
                    <p>No plans yet. Create your first plan to get started.</p>
                </div>
            @endif
        </div>

        {{-- Form Modal --}}
        @if ($showForm)
            <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $editingId ? 'Edit Plan' : 'New Plan' }}
                </h2>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" value="Plan Name" />
                            <x-text-input wire:model="name" id="name" type="text" class="mt-1 block w-full" placeholder="Pro Monthly" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="slug" value="Slug" />
                            <div class="mt-1 flex gap-2">
                                <x-text-input wire:model="slug" id="slug" type="text" class="block flex-1" placeholder="pro_monthly" />
                                <button type="button" wire:click="generateSlug" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded text-sm">
                                    Auto-generate
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea wire:model="description" id="description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" rows="2" placeholder="Plan description for users"></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="monthly_price_cents" value="Monthly Price (RM)" />
                            <x-text-input wire:model="monthly_price_cents" id="monthly_price_cents" type="number" class="mt-1 block w-full" placeholder="6.00" step="0.01" />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Leave blank if not offered monthly</p>
                            <x-input-error :messages="$errors->get('monthly_price_cents')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="yearly_price_cents" value="Yearly Price (RM)" />
                            <x-text-input wire:model="yearly_price_cents" id="yearly_price_cents" type="number" class="mt-1 block w-full" placeholder="40.00" step="0.01" />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Leave blank if not offered yearly</p>
                            <x-input-error :messages="$errors->get('yearly_price_cents')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input wire:model="is_active" id="is_active" type="checkbox" class="rounded" />
                        <x-input-label for="is_active" value="Active" class="!m-0" />
                    </div>

                    <div class="flex gap-2 pt-4">
                        <x-primary-button type="submit">
                            {{ $editingId ? 'Update' : 'Create' }} Plan
                        </x-primary-button>
                        <x-secondary-button type="button" wire:click="cancelForm">
                            Cancel
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
