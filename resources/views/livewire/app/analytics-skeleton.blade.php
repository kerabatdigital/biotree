<div class="py-8" wire:key="analytics-skeleton">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 animate-pulse">

        {{-- header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="space-y-2">
                <div class="h-7 w-40 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                <div class="h-4 w-48 rounded bg-gray-200 dark:bg-gray-700"></div>
            </div>
            <div class="h-9 w-48 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
        </div>

        {{-- stat cards --}}
        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @for ($i = 0; $i < 4; $i++)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="h-3 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="mt-3 h-7 w-16 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                </div>
            @endfor
        </div>

        {{-- share / qr --}}
        <div class="mt-6 flex flex-wrap items-center gap-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <div class="h-28 w-28 shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
            <div class="min-w-0 flex-1 space-y-3">
                <div class="h-3 w-28 rounded bg-gray-200 dark:bg-gray-700"></div>
                <div class="flex gap-2">
                    <div class="h-9 w-48 rounded-md bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-9 w-24 rounded-md bg-gray-200 dark:bg-gray-700"></div>
                </div>
            </div>
        </div>

        {{-- chart --}}
        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <div class="h-3 w-32 rounded bg-gray-200 dark:bg-gray-700"></div>
            <div class="mt-4 h-40 w-full rounded-xl bg-gray-100 dark:bg-gray-700/50"></div>
        </div>

        {{-- top links + breakdowns --}}
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
                <div class="h-3 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
                <div class="mt-4 space-y-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 w-2/3 rounded bg-gray-200 dark:bg-gray-700"></div>
                                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-700"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="space-y-6">
                @for ($b = 0; $b < 3; $b++)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <div class="h-3 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
                        <div class="mt-4 space-y-3">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="h-3 w-full rounded bg-gray-200 dark:bg-gray-700"></div>
                            @endfor
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
