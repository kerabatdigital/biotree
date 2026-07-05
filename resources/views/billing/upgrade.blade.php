<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upgrade to Pro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($isPro)
                <div class="mb-8 p-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                    <p class="text-emerald-800 dark:text-emerald-200">
                        ✓ You have an active Pro subscription
                        @if ($planExpiresAt)
                            until <strong>{{ $planExpiresAt->format('M d, Y') }}</strong>
                        @endif
                    </p>
                    <a href="{{ route('billing.dashboard') }}" class="mt-2 inline-flex text-sm text-emerald-600 dark:text-emerald-300 hover:underline">
                        View billing dashboard →
                    </a>
                </div>
            @endif

            <div class="grid md:grid-cols-2 gap-8">
                {{-- Free Plan --}}
                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-8 bg-white dark:bg-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Free</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Perfect to start</p>
                    <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Free</p>

                    <ul class="mt-6 space-y-3">
                        <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <x-phosphor-check class="w-5 h-5 text-emerald-600" />
                            <span>Up to 5 links</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <x-phosphor-check class="w-5 h-5 text-emerald-600" />
                            <span>Basic theme presets</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <x-phosphor-check class="w-5 h-5 text-emerald-600" />
                            <span>Basic analytics</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-500 dark:text-gray-500 line-through">
                            <x-phosphor-x class="w-5 h-5" />
                            <span>Custom CSS</span>
                        </li>
                    </ul>

                    @unless ($currentPlan === 'free')
                        <form method="POST" action="{{ route('billing.checkout') }}" class="mt-8">
                            @csrf
                            <input type="hidden" name="plan" value="pro_monthly">
                            <button type="button" disabled class="w-full py-2 px-4 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-lg cursor-not-allowed">
                                Your current plan
                            </button>
                        </form>
                    @endunless
                </div>

                {{-- Pro Plan --}}
                <div class="border-2 border-emerald-600 dark:border-emerald-500 rounded-lg p-8 bg-white dark:bg-gray-800 relative">
                    <div class="absolute -top-4 left-6 bg-emerald-600 dark:bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        MOST POPULAR
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pro</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Unlock full potential</p>
                    <div class="mt-4 space-y-1">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">RM 6<span class="text-lg font-normal text-gray-600 dark:text-gray-400">/mo</span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">or RM 40/year (save 33%)</p>
                    </div>

                    <ul class="mt-6 space-y-3">
                        <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <x-phosphor-check class="w-5 h-5 text-emerald-600" />
                            <span>Unlimited links</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <x-phosphor-check class="w-5 h-5 text-emerald-600" />
                            <span>All theme presets</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <x-phosphor-check class="w-5 h-5 text-emerald-600" />
                            <span>Advanced analytics</span>
                        </li>
                        <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <x-phosphor-check class="w-5 h-5 text-emerald-600" />
                            <span>Custom CSS editor</span>
                        </li>
                    </ul>

                    <div class="mt-8 space-y-2">
                        @if ($isPro)
                            <p class="text-center text-sm text-emerald-600 dark:text-emerald-400">✓ Your current plan</p>
                        @else
                            <form method="POST" action="{{ route('billing.checkout') }}">
                                @csrf
                                <input type="hidden" name="plan" value="pro_monthly">
                                <button type="submit" class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                                    Upgrade Now — RM 6/month
                                </button>
                            </form>

                            <form method="POST" action="{{ route('billing.checkout') }}">
                                @csrf
                                <input type="hidden" name="plan" value="pro_annual">
                                <button type="submit" class="w-full py-2 px-4 border-2 border-emerald-600 dark:border-emerald-500 text-emerald-600 dark:text-emerald-400 rounded-lg font-medium transition hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                    RM 40/year (save 33%)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-12 p-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                <h4 class="font-semibold text-blue-900 dark:text-blue-100">Questions about plans?</h4>
                <p class="mt-2 text-sm text-blue-800 dark:text-blue-200">Email us at support@biotree.my or visit our help center.</p>
            </div>

        </div>
    </div>
</x-app-layout>
