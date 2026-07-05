<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BioTree') }} — One link for everything</title>
    <meta name="description" content="BioTree is a superfast, mobile-first link-in-bio for creators and businesses in Malaysia. One page for all your links, with live analytics and custom themes.">

    <meta property="og:type" content="website">
    <meta property="og:title" content="BioTree — One link for everything">
    <meta property="og:description" content="A superfast, mobile-first link-in-bio for creators and businesses in Malaysia.">
    <meta property="og:image" content="{{ asset('og-default.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    @include('partials.pwa-head')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-950 text-neutral-100">
    <div class="relative min-h-screen overflow-hidden">
        {{-- ambient background glow --}}
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 h-[36rem] w-[36rem] rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-teal-500/10 blur-3xl"></div>
        </div>

        {{-- nav --}}
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <a href="/" class="flex items-center gap-2 text-lg font-bold">
                <x-phosphor-tree-fill class="h-7 w-7 text-emerald-400" />
                <span>{{ config('app.name', 'BioTree') }}</span>
            </a>
            <nav class="flex items-center gap-2 text-sm">
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-full bg-emerald-500 px-5 py-2 font-medium text-neutral-950 transition hover:bg-emerald-400">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-neutral-300 transition hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-emerald-500 px-5 py-2 font-medium text-neutral-950 transition hover:bg-emerald-400">Get started</a>
                @endauth
            </nav>
        </header>

        {{-- hero --}}
        <main class="mx-auto max-w-3xl px-6 pb-24 pt-16 text-center sm:pt-24">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1.5 text-xs font-medium text-emerald-300">
                <x-phosphor-sparkle class="h-4 w-4" /> Made in Malaysia 🇲🇾
            </span>

            <h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-6xl">
                One link for <span class="text-emerald-400">everything</span> you are.
            </h1>

            <p class="mx-auto mt-6 max-w-xl text-lg text-neutral-400">
                BioTree puts all your links on one superfast, beautiful page — your own theme, deep
                analytics, and a clean URL like <span class="text-neutral-200">biotree.my/you</span>.
            </p>

            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-7 py-3 font-semibold text-neutral-950 transition hover:bg-emerald-400">
                        <x-phosphor-house class="h-5 w-5" /> Go to dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-7 py-3 font-semibold text-neutral-950 transition hover:bg-emerald-400">
                        <x-phosphor-rocket-launch class="h-5 w-5" /> Claim your link
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full border border-neutral-700 px-7 py-3 font-semibold text-neutral-200 transition hover:border-neutral-500">
                        <x-phosphor-sign-in class="h-5 w-5" /> Log in
                    </a>
                @endauth
            </div>

            {{-- feature chips --}}
            <div class="mt-16 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="flex flex-col items-center gap-2 rounded-2xl border border-neutral-800 bg-neutral-900/50 px-4 py-5">
                    <x-phosphor-palette class="h-6 w-6 text-emerald-400" />
                    <span class="text-sm text-neutral-300">Custom themes</span>
                </div>
                <div class="flex flex-col items-center gap-2 rounded-2xl border border-neutral-800 bg-neutral-900/50 px-4 py-5">
                    <x-phosphor-chart-line-up class="h-6 w-6 text-emerald-400" />
                    <span class="text-sm text-neutral-300">Live analytics</span>
                </div>
                <div class="flex flex-col items-center gap-2 rounded-2xl border border-neutral-800 bg-neutral-900/50 px-4 py-5">
                    <x-phosphor-lightning class="h-6 w-6 text-emerald-400" />
                    <span class="text-sm text-neutral-300">Superfast</span>
                </div>
                <div class="flex flex-col items-center gap-2 rounded-2xl border border-neutral-800 bg-neutral-900/50 px-4 py-5">
                    <x-phosphor-link-simple class="h-6 w-6 text-emerald-400" />
                    <span class="text-sm text-neutral-300">biotree.my/you</span>
                </div>
            </div>
        </main>

        <footer class="mx-auto max-w-6xl px-6 pb-10 text-center text-sm text-neutral-600">
            &copy; {{ date('Y') }} {{ config('app.name', 'BioTree') }} · biotree.my
        </footer>
    </div>
</body>
</html>
