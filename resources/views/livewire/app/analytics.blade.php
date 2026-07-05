@php
    $cards = [
        ['label' => 'Views', 'value' => number_format($stats['views']), 'icon' => 'eye'],
        ['label' => 'Unique visitors', 'value' => number_format($stats['uniques']), 'icon' => 'user-circle'],
        ['label' => 'Link clicks', 'value' => number_format($stats['clicks']), 'icon' => 'cursor-click'],
        ['label' => 'Click rate', 'value' => $stats['ctr'].'%', 'icon' => 'chart-line-up'],
    ];
    $ranges = [7 => '7 days', 28 => '28 days', 90 => '90 days'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">

        {{-- header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <a href="{{ url('/'.$profileUsername) }}" target="_blank"
                   class="mt-1 inline-flex items-center gap-1 text-sm text-emerald-600 hover:underline dark:text-emerald-400">
                    biotree.my/{{ $profileUsername }} <x-phosphor-arrow-square-out class="h-4 w-4" />
                </a>
            </div>
            <div class="inline-flex rounded-lg border border-gray-200 p-0.5 dark:border-gray-700">
                @foreach ($ranges as $days => $label)
                    <button wire:click="setRange({{ $days }})" type="button"
                            class="rounded-md px-3 py-1.5 text-sm transition {{ $range === $days ? 'bg-emerald-500 font-semibold text-neutral-950' : 'text-gray-500 hover:text-gray-800 dark:hover:text-gray-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- stat cards --}}
        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($cards as $card)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-2 text-gray-400">
                        @svg('phosphor-'.$card['icon'], 'w-4 h-4')
                        <span class="text-xs font-medium uppercase tracking-wide">{{ $card['label'] }}</span>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- share / qr --}}
        <div class="mt-6 flex flex-wrap items-center gap-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <img src="{{ $qrUrl }}" alt="QR code for {{ $publicUrl }}" class="h-28 w-28 shrink-0 rounded-lg border border-gray-100 dark:border-gray-700">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500">Share your page</p>
                <div x-data="{ copied: false }" class="mt-2 flex flex-wrap items-center gap-2">
                    <input type="text" readonly value="{{ $publicUrl }}"
                           class="w-full max-w-xs rounded-md border-gray-300 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:w-auto"
                           x-ref="publicUrl" onclick="this.select()">
                    <button type="button"
                            @click="navigator.clipboard.writeText($refs.publicUrl.value); copied = true; setTimeout(() => copied = false, 1500)"
                            class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                        <x-phosphor-copy class="h-4 w-4" />
                        <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
                    </button>
                    <a href="{{ $qrDownloadUrl }}" download
                       class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                        <x-phosphor-download-simple class="h-4 w-4" />
                        Download QR
                    </a>
                </div>
            </div>
        </div>

        {{-- chart --}}
        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Views &amp; clicks</h2>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Views</span>
                    <span class="flex items-center gap-1"><span class="h-2 w-3 rounded-full bg-slate-400"></span> Clicks</span>
                </div>
            </div>

            <svg viewBox="0 0 {{ $chart['w'] }} {{ $chart['h'] }}" preserveAspectRatio="none" class="mt-4 h-40 w-full">
                <defs>
                    <linearGradient id="viewsGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="rgb(16 185 129)" stop-opacity="0.35" />
                        <stop offset="100%" stop-color="rgb(16 185 129)" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <path d="{{ $chart['viewsArea'] }}" fill="url(#viewsGrad)" />
                <path d="{{ $chart['viewsLine'] }}" fill="none" stroke="rgb(16 185 129)" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linejoin="round" />
                <path d="{{ $chart['clicksLine'] }}" fill="none" stroke="rgb(148 163 184)" stroke-width="1.5" stroke-dasharray="4 3" vector-effect="non-scaling-stroke" stroke-linejoin="round" />
            </svg>

            <div class="mt-1 flex justify-between text-xs text-gray-400">
                <span>{{ \Illuminate\Support\Carbon::parse($chart['first'])->format('d M') }}</span>
                <span>peak {{ $chart['max'] }}/day</span>
                <span>{{ \Illuminate\Support\Carbon::parse($chart['last'])->format('d M') }}</span>
            </div>
        </div>

        {{-- top links + breakdowns --}}
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Top links</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($topLinks as $link)
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                                @svg('phosphor-'.($link['icon'] ?: 'link-simple'), 'w-4 h-4')
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="truncate font-medium text-gray-800 dark:text-gray-100">{{ $link['title'] }}</span>
                                    <span class="ml-2 shrink-0 text-gray-400">{{ number_format($link['clicks']) }} · {{ $link['ctr'] }}%</span>
                                </div>
                                <div class="mt-1 h-1.5 rounded-full bg-gray-100 dark:bg-gray-700">
                                    <div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ max(4, $link['share']) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <x-phosphor-cursor-click class="mx-auto h-7 w-7 text-gray-300" />
                            <p class="mt-2 text-sm text-gray-400">No clicks yet. Share your BioTree link to get started.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                @include('livewire.app.partials._breakdown', ['title' => 'Top countries', 'icon' => 'globe', 'rows' => $countries])
                @include('livewire.app.partials._breakdown', ['title' => 'Referrers', 'icon' => 'link-simple', 'rows' => $referrers])
                @include('livewire.app.partials._breakdown', ['title' => 'Devices', 'icon' => 'device-mobile', 'rows' => $devices])
            </div>
        </div>
    </div>
</div>
