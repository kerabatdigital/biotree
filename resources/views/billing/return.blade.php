<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Processing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                    <x-phosphor-clock class="w-8 h-8 text-blue-600 dark:text-blue-300" />
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Payment Processing</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $message ?? 'Your payment is being processed. You will be notified once confirmed.' }}
                </p>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded p-4 text-sm text-blue-800 dark:text-blue-200 mb-6">
                    <strong>Please note:</strong> It may take a few moments for your payment to be confirmed. You can close this page safely.
                </div>

                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:underline">
                    ← Back to dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
