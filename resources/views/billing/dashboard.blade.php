<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Billing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Current Plan Card --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Plan</h3>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Plan Name</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white capitalize">
                            {{ $currentPlan }}
                        </p>
                    </div>

                    @if ($isPro && $planExpiresAt)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Expires</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $planExpiresAt->format('M d, Y') }}
                            </p>
                        </div>
                    @endif
                </div>

                @if (!$isPro)
                    <a href="{{ route('billing.upgrade') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                        <x-phosphor-arrow-up-right class="w-4 h-4" />
                        Upgrade to Pro
                    </a>
                @endif
            </div>

            {{-- Payment History --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment History</h3>
                </div>

                @if ($payments->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $payment->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            RM {{ number_format($payment->amount / 100, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @switch($payment->status)
                                                @case('paid')
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                                        <x-phosphor-check-circle class="w-4 h-4" />
                                                        Paid
                                                    </span>
                                                    @break
                                                @case('pending')
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                                        <x-phosphor-clock class="w-4 h-4" />
                                                        Pending
                                                    </span>
                                                    @break
                                                @case('failed')
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                                        <x-phosphor-x-circle class="w-4 h-4" />
                                                        Failed
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $payment->bill_code ?? '—' }}</code>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                        <p>No payments yet.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
