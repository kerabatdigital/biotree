@php
    $faqs = [
        ['q' => 'Is BioTree free to use?', 'a' => 'Yes. The Free plan lets you create your biotree.my page, add links, pick a theme and see click analytics — no card required.'],
        ['q' => 'What do I get with Pro?', 'a' => 'Pro unlocks unlimited links, custom CSS, advanced analytics, link scheduling and removes BioTree branding from your page.'],
        ['q' => 'How do I pay?', 'a' => 'Payments are processed securely by toyyibPay, supporting Malaysian FPX online banking and cards. Prices are in Malaysian Ringgit (RM).'],
        ['q' => 'Can I cancel anytime?', 'a' => 'Yes. Your Pro features stay active until the end of your billing period, then your page reverts to the Free plan.'],
    ];
@endphp

<x-marketing-layout
    title="Pricing — BioTree | Free & Pro link-in-bio for Malaysia"
    description="Simple pricing for BioTree, the mobile-first link-in-bio for Malaysia. Start free, or go Pro from RM6/month for unlimited links, custom themes and advanced analytics.">

    <x-slot name="head">
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => 'BioTree Pro',
            'description' => 'Link-in-bio for creators and businesses in Malaysia — unlimited links, custom themes, and advanced analytics.',
            'brand' => ['@type' => 'Brand', 'name' => 'BioTree'],
            'offers' => $plans->whereNotNull('monthly_price_cents')->merge($plans->whereNotNull('yearly_price_cents'))->map(fn ($p) => [
                '@type' => 'Offer',
                'price' => number_format(($p->monthly_price_cents ?? $p->yearly_price_cents) / 100, 2, '.', ''),
                'priceCurrency' => 'MYR',
                'name' => $p->name,
                'url' => url('/pricing'),
            ])->values()->all(),
        ], JSON_UNESCAPED_SLASHES) !!}
        </script>
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
            ])->all(),
        ], JSON_UNESCAPED_SLASHES) !!}
        </script>
    </x-slot>

    <div class="mx-auto max-w-5xl px-6 py-12 sm:py-16">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">Simple, honest pricing</h1>
            <p class="mx-auto mt-4 max-w-xl text-lg text-neutral-400">
                Start free. Upgrade when you're ready for more. Prices in Ringgit (RM), billed via toyyibPay.
            </p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach ($plans as $plan)
                @php
                    $isPro = $plan->monthly_price_cents || $plan->yearly_price_cents;
                    $priceCents = $plan->monthly_price_cents ?? $plan->yearly_price_cents;
                    $period = $plan->monthly_price_cents ? '/month' : ($plan->yearly_price_cents ? '/year' : '');
                    $highlight = $plan->slug === 'pro_monthly';
                @endphp
                <div @class([
                    'relative flex flex-col rounded-3xl border p-6',
                    'border-emerald-500/60 bg-emerald-500/5 shadow-lg shadow-emerald-500/10' => $highlight,
                    'border-neutral-800 bg-neutral-900/50' => ! $highlight,
                ])>
                    @if ($highlight)
                        <span class="absolute -top-3 left-6 rounded-full bg-emerald-500 px-3 py-1 text-xs font-bold text-neutral-950">Most popular</span>
                    @endif
                    <h2 class="text-lg font-bold text-white">{{ $plan->name }}</h2>
                    <p class="mt-1 text-sm text-neutral-400">{{ $plan->description }}</p>
                    <div class="mt-5">
                        @if ($priceCents)
                            <span class="text-4xl font-extrabold text-white">RM{{ number_format($priceCents / 100, 0) }}</span>
                            <span class="text-neutral-400">{{ $period }}</span>
                        @else
                            <span class="text-4xl font-extrabold text-white">Free</span>
                        @endif
                    </div>

                    <ul class="mt-6 space-y-3 text-sm">
                        @foreach (($plan->features ?? []) as $feature)
                            <li class="flex items-start gap-2 text-neutral-300">
                                <x-phosphor-check-circle-fill class="mt-0.5 h-4 w-4 shrink-0 text-emerald-400" />
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-8 pt-2">
                        @if ($isPro)
                            <a href="{{ auth()->check() ? route('billing.upgrade') : route('register') }}"
                               class="block rounded-full bg-emerald-500 px-6 py-3 text-center font-semibold text-neutral-950 transition hover:bg-emerald-400">
                                {{ auth()->check() ? 'Upgrade to Pro' : 'Get Pro' }}
                            </a>
                        @else
                            <a href="{{ auth()->check() ? url('/dashboard') : route('register') }}"
                               class="block rounded-full border border-neutral-700 px-6 py-3 text-center font-semibold text-neutral-200 transition hover:border-neutral-500">
                                {{ auth()->check() ? 'Go to dashboard' : 'Start free' }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FAQ --}}
        <div class="mx-auto mt-20 max-w-2xl">
            <h2 class="text-center text-2xl font-bold text-white">Frequently asked questions</h2>
            <div class="mt-8 space-y-4">
                @foreach ($faqs as $faq)
                    <details class="group rounded-2xl border border-neutral-800 bg-neutral-900/50 p-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-neutral-100">
                            {{ $faq['q'] }}
                            <x-phosphor-plus class="h-4 w-4 text-neutral-500 transition group-open:rotate-45" />
                        </summary>
                        <p class="mt-3 text-sm text-neutral-400">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>

        <div class="mt-16 text-center">
            <a href="{{ auth()->check() ? route('billing.upgrade') : route('register') }}"
               class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-8 py-3.5 font-semibold text-neutral-950 transition hover:bg-emerald-400">
                <x-phosphor-rocket-launch class="h-5 w-5" /> {{ auth()->check() ? 'Upgrade now' : 'Claim your link' }}
            </a>
        </div>
    </div>
</x-marketing-layout>
