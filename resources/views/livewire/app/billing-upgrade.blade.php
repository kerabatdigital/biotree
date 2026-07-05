<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Upgrade to Pro</h1>
            <p class="text-xl text-gray-600">Unlock unlimited links, custom CSS, and more.</p>
        </div>

        <!-- Current Subscription Info -->
        @if ($currentSubscription)
            <div class="mb-12 bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Current Plan</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Current Plan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $currentSubscription->plan->name }}</p>
                    </div>
                    @if ($currentSubscription->expires_at)
                        <div class="text-right">
                            <p class="text-gray-600">Expires</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $currentSubscription->expires_at->format('M d, Y') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Plans Grid -->
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            @foreach ($plans as $plan)
                @php
                    $planPeriod = $plan->monthly_price_cents ? 'monthly' : 'yearly';
                    $planPriceCents = $plan->monthly_price_cents ?? $plan->yearly_price_cents;
                @endphp
                <div @class([
                    'bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow border-2',
                    'border-green-500' => $selectedPlanId === $plan->id,
                    'border-transparent' => $selectedPlanId !== $plan->id,
                ])>
                    <div class="p-8 flex flex-col h-full">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-600 mb-6">{{ $plan->description }}</p>

                        <!-- Price -->
                        <div class="mb-6">
                            @if ($planPriceCents)
                                <div class="text-4xl font-bold text-gray-900">
                                    RM {{ number_format($planPriceCents / 100, 2) }}
                                </div>
                                <p class="text-gray-600">per {{ $planPeriod === 'monthly' ? 'month' : 'year' }}</p>
                            @else
                                <div class="text-4xl font-bold text-gray-900">Free</div>
                                <p class="text-gray-600">forever</p>
                            @endif
                        </div>

                        <!-- Features -->
                        @if ($plan->features && count($plan->features) > 0)
                            <div class="mb-6 flex-1">
                                <h4 class="font-semibold text-gray-900 mb-3">Features</h4>
                                <ul class="space-y-2">
                                    @foreach ($plan->features as $feature)
                                        <li class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Limits -->
                        @if ($plan->limits && count($plan->limits) > 0)
                            <div class="mb-6 text-sm text-gray-600">
                                <h4 class="font-semibold text-gray-900 mb-2">Limits</h4>
                                <ul class="space-y-1">
                                    @foreach ($plan->limits as $limit => $value)
                                        <li>{{ ucfirst(str_replace('_', ' ', $limit)) }}: {{ $value }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Select Plan Button -->
                        @if ($planPriceCents)
                            <button
                                wire:click="selectPlan({{ $plan->id }})"
                                @class([
                                    'w-full py-3 px-4 rounded-lg font-semibold transition-colors',
                                    'bg-blue-600 text-white hover:bg-blue-700' => $selectedPlanId !== $plan->id,
                                    'bg-green-600 text-white hover:bg-green-700' => $selectedPlanId === $plan->id,
                                ])
                            >
                                {{ $selectedPlanId === $plan->id ? 'Selected' : 'Select Plan' }}
                            </button>
                        @else
                            <div class="w-full py-3 px-4 rounded-lg font-semibold text-center bg-gray-100 text-gray-500">
                                Your current default plan
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Checkout Section (always visible once a paid plan is selected) -->
        @if ($selectedPlanId)
            <div class="sticky bottom-4 bg-white rounded-lg shadow-lg border border-gray-200 p-6">
                <div class="grid md:grid-cols-3 gap-4 items-end mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Phone number</label>
                        <input
                            type="tel"
                            wire:model="phone"
                            placeholder="e.g. 0123456789"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        @error('phone')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Have a coupon code?</label>
                        <input
                            type="text"
                            wire:model="couponCode"
                            placeholder="Enter coupon code (optional)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        @error('couponCode')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <button
                        wire:click="proceedToCheckout"
                        wire:loading.attr="disabled"
                        wire:target="proceedToCheckout"
                        class="w-full px-8 py-3 bg-blue-600 text-white text-lg font-semibold rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400"
                    >
                        <span wire:loading.remove wire:target="proceedToCheckout">Proceed to Checkout</span>
                        <span wire:loading wire:target="proceedToCheckout" class="inline-flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-red-900 font-semibold mb-2">Please fix the following errors:</h4>
                <ul class="list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
