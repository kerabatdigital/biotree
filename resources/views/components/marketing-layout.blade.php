@props([
    'title' => 'BioTree — One link for everything',
    'description' => 'BioTree is a superfast, mobile-first link-in-bio for creators and businesses in Malaysia. One page for all your links, with live analytics and custom themes.',
    'canonical' => null,
    'ogImage' => null,
])
@php($canonical = $canonical ?? url()->current())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="robots" content="index, follow, max-image-preview:large">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="BioTree">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('og-default.png') }}">
    <meta property="og:locale" content="en_MY">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('og-default.png') }}">

    {{-- Per-page structured data / extra head --}}
    {{ $head ?? '' }}

    @include('partials.pwa-head')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-950 text-neutral-100">
    <div class="relative min-h-screen overflow-hidden flex flex-col">
        {{-- ambient background glow --}}
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 h-[36rem] w-[36rem] rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-teal-500/10 blur-3xl"></div>
        </div>

        {{-- header --}}
        <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-bold">
                <x-phosphor-tree-fill class="h-7 w-7 text-emerald-400" />
                <span>{{ config('app.name', 'BioTree') }}</span>
            </a>
            <nav class="flex items-center gap-1 text-sm sm:gap-2">
                <a href="{{ route('pricing') }}" class="hidden rounded-full px-4 py-2 text-neutral-300 transition hover:text-white sm:inline-block">Pricing</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-full bg-emerald-500 px-5 py-2 font-medium text-neutral-950 transition hover:bg-emerald-400">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-neutral-300 transition hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-emerald-500 px-5 py-2 font-medium text-neutral-950 transition hover:bg-emerald-400">Get started</a>
                @endauth
            </nav>
        </header>

        {{-- page content --}}
        <main class="flex-1">
            {{ $slot }}
        </main>

        {{-- footer --}}
        <footer class="mt-16 border-t border-neutral-800/80">
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-4 px-6 py-10 text-sm text-neutral-400 sm:flex-row sm:justify-between">
                <div class="flex items-center gap-2 text-neutral-300">
                    <x-phosphor-tree-fill class="h-5 w-5 text-emerald-400" />
                    <span class="font-semibold">{{ config('app.name', 'BioTree') }}</span>
                    <span class="text-neutral-600">· Made in Malaysia 🇲🇾</span>
                </div>
                <nav class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
                    <a href="{{ route('pricing') }}" class="transition hover:text-white">Pricing</a>
                    <a href="{{ route('terms') }}" class="transition hover:text-white">Terms</a>
                    <a href="{{ route('privacy') }}" class="transition hover:text-white">Privacy</a>
                    <a href="{{ route('register') }}" class="transition hover:text-white">Get started</a>
                </nav>
            </div>
            <div class="pb-8 text-center text-xs text-neutral-600">
                &copy; {{ date('Y') }} {{ config('app.name', 'BioTree') }} · biotree.my
            </div>
        </footer>
    </div>
</body>
</html>
