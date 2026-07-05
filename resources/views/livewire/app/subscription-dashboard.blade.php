<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">My Subscription</h1>
            <p class="text-gray-600">Manage your BioTree Pro subscription</p>
        </div>

        @if ($subscription)
            <!-- Current Subscription Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Current Plan</h2>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $subscription->plan->name }}</h3>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
                    <!-- Status -->
                    <div>
                        <p class="text-gray-600 text-sm mb-1">Status</p>
                        <div class="flex items-center gap-2">
                            <span @class([
                                'inline-flex px-3 py-1 rounded-full text-sm font-semibold',
                                'bg-green-100 text-green-800' => $subscription->status === 'active',
                                'bg-red-100 text-red-800' => $subscription->status === 'expired',
                                'bg-gray-100 text-gray-800' => $subscription->status === 'cancelled',
                            ])>
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Renewal Information -->
                    <div>
                        <p class="text-gray-600 text-sm mb-1">
                            @if ($subscription->auto_renew)
                                Next Renewal
                            @else
                                Expires
                            @endif
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $subscription->expires_at->format('M d, Y') }}
                        </p>
                        @if ($subscription->auto_renew)
                            <p class="text-xs text-green-600 mt-1">Auto-renewal enabled</p>
                        @else
                            <p class="text-xs text-orange-600 mt-1">Auto-renewal disabled</p>
                        @endif
                    </div>
                </div>

                <!-- Plan Details -->
                @if ($subscription->plan->features && count($subscription->plan->features) > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Features</h4>
                        <ul class="grid md:grid-cols-2 gap-2">
                            @foreach ($subscription->plan->features as $feature)
                                <li class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($subscription->plan->limits && count($subscription->plan->limits) > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Limits</h4>
                        <ul class="space-y-2">
                            @foreach ($subscription->plan->limits as $limit => $value)
                                <li class="flex justify-between text-gray-700">
                                    <span>{{ ucfirst(str_replace('_', ' ', $limit)) }}</span>
                                    <span class="font-semibold">{{ $value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('billing.upgrade') }}" class="flex-1 text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Upgrade or Change Plan
                    </a>
                    <button class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 font-semibold rounded-lg hover:bg-gray-300 transition-colors" title="Contact support to cancel your subscription">
                        Contact Support
                    </button>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-lg shadow p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Payment History</h3>

                @if ($payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Date</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Plan</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Amount</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-4 text-gray-900">
                                            {{ $payment->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-700">
                                            {{ $payment->plan->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-900 font-semibold">
                                            RM {{ number_format($payment->amount / 100, 2) }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <span @class([
                                                'inline-flex px-3 py-1 rounded-full text-sm font-semibold',
                                                'bg-green-100 text-green-800' => $payment->status === 'paid',
                                                'bg-yellow-100 text-yellow-800' => $payment->status === 'pending',
                                                'bg-red-100 text-red-800' => $payment->status === 'failed',
                                                'bg-gray-100 text-gray-800' => $payment->status === 'refunded',
                                            ])>
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($payments->hasPages())
                        <div class="mt-6">
                            {{ $payments->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">No payments yet</p>
                    </div>
                @endif
            </div>
        @else
            <!-- No Subscription State -->
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-4">No Active Subscription</h3>
                <p class="text-gray-600 mb-6">You don't have an active subscription yet. Upgrade to Pro to unlock more features.</p>
                <a href="{{ route('billing.upgrade') }}" class="inline-block px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    Upgrade to Pro
                </a>
            </div>
        @endif
    </div>
</div>
