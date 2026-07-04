<div {{ $attributes->merge(['class' => 'mt-6']) }}>
    <div class="relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="bg-white px-2 uppercase tracking-wide text-gray-400 dark:bg-gray-800">{{ __('or') }}</span>
        </div>
    </div>

    <a href="{{ route('auth.google') }}"
       class="mt-6 flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
        <x-phosphor-google-logo class="h-5 w-5" />
        {{ __('Continue with Google') }}
    </a>
</div>
