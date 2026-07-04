<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
    <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
        @svg('phosphor-'.$icon, 'w-4 h-4 text-gray-400')
        {{ $title }}
    </h3>
    <div class="mt-3 space-y-2.5">
        @forelse ($rows as $row)
            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="truncate text-gray-600 dark:text-gray-300">{{ $row['label'] }}</span>
                    <span class="ml-2 shrink-0 text-gray-400">{{ number_format($row['count']) }}</span>
                </div>
                <div class="mt-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-700">
                    <div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ max(4, $row['share']) }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">No data yet.</p>
        @endforelse
    </div>
</div>
