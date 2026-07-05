<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Coupons</h1>
            <button wire:click="newCoupon" class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-neutral-950 transition hover:bg-emerald-400">
                <x-phosphor-plus class="h-4 w-4" />
                New Coupon
            </button>
        </div>

        {{-- Coupons Table --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            @if ($coupons->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Discount</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Applies To</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $coupon->code }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        @if ($coupon->discount_percent)
                                            {{ $coupon->discount_percent }}%
                                        @else
                                            RM {{ number_format($coupon->discount_fixed_cents / 100, 2) }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                            {{ $coupon->applies_to === 'first_purchase' ? 'First Purchase' : 'All Renewals' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        @if ($coupon->max_uses)
                                            {{ $coupon->used_count }}/{{ $coupon->max_uses }}
                                        @else
                                            {{ $coupon->used_count }}/∞
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <button wire:click="toggleActive({{ $coupon->id }})" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium transition
                                            @if ($coupon->is_active)
                                                bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200
                                            @else
                                                bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200
                                            @endif
                                        ">
                                            @if ($coupon->is_active)
                                                <x-phosphor-check-circle class="w-3 h-3" />
                                                Active
                                            @else
                                                <x-phosphor-x-circle class="w-3 h-3" />
                                                Inactive
                                            @endif
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm space-x-2">
                                        <button wire:click="editCoupon({{ $coupon->id }})" class="text-emerald-600 dark:text-emerald-400 hover:underline">
                                            Edit
                                        </button>
                                        <button wire:click="deleteCoupon({{ $coupon->id }})" class="text-red-600 dark:text-red-400 hover:underline">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-600 dark:text-gray-400">
                    <p>No coupons yet. Create your first coupon to start offering discounts.</p>
                </div>
            @endif
        </div>

        {{-- Form Modal --}}
        @if ($showForm)
            <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $editingId ? 'Edit Coupon' : 'New Coupon' }}
                </h2>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="code" value="Code" />
                            <x-text-input wire:model="code" id="code" type="text" class="mt-1 block w-full" placeholder="WELCOME20" />
                            <x-input-error :messages="$errors->get('code')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="applies_to" value="Applies To" />
                            <select wire:model="applies_to" id="applies_to" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="first_purchase">First Purchase Only</option>
                                <option value="all_renewals">All Renewals</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="discount_percent" value="Discount %" />
                            <x-text-input wire:model="discount_percent" id="discount_percent" type="number" class="mt-1 block w-full" placeholder="20" min="0" max="100" />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">OR fixed amount below</p>
                        </div>

                        <div>
                            <x-input-label for="discount_fixed_cents" value="Discount (RM)" />
                            <x-text-input wire:model="discount_fixed_cents" id="discount_fixed_cents" type="number" class="mt-1 block w-full" placeholder="5.00" step="0.01" min="0" />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Use either % or fixed amount</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="max_uses" value="Max Uses" />
                            <x-text-input wire:model="max_uses" id="max_uses" type="number" class="mt-1 block w-full" placeholder="100" min="0" />
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Leave blank for unlimited</p>
                        </div>

                        <div>
                            <x-input-label for="valid_from" value="Valid From" />
                            <x-text-input wire:model="valid_from" id="valid_from" type="date" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="valid_until" value="Valid Until" />
                            <x-text-input wire:model="valid_until" id="valid_until" type="date" class="mt-1 block w-full" />
                        </div>

                        <div class="flex items-center pt-6">
                            <input wire:model="is_active" id="is_active" type="checkbox" class="rounded" />
                            <x-input-label for="is_active" value="Active" class="!m-0 ml-2" />
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4">
                        <x-primary-button type="submit">
                            {{ $editingId ? 'Update' : 'Create' }} Coupon
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
