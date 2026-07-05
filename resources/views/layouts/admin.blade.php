<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin' }} - {{ config('app.name', 'BioTree') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- PWA --}}
        @include('partials.pwa-head')

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <div class="flex">
                {{-- Admin Sidebar --}}
                <aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-transform -translate-x-full lg:translate-x-0">
                    {{-- Logo --}}
                    <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                            <x-phosphor-tree-logo class="w-8 h-8 text-emerald-600" weight="duotone" />
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">BioTree</span>
                            <span class="px-2 py-0.5 text-xs font-medium bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 rounded">Admin</span>
                        </a>
                    </div>

                    {{-- Navigation --}}
                    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors
                                  {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <x-phosphor-chart-line-up class="w-5 h-5" />
                            Overview
                        </a>

                        <a href="{{ route('admin.users') }}"
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors
                                  {{ request()->routeIs('admin.users*') ? 'bg-emerald-50 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <x-phosphor-users class="w-5 h-5" />
                            Users
                            @php
                                $totalUsers = \App\Models\User::count();
                            @endphp
                            <span class="ml-auto px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">{{ number_format($totalUsers) }}</span>
                        </a>

                        <a href="{{ route('admin.reports') }}"
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors
                                  {{ request()->routeIs('admin.reports*') ? 'bg-emerald-50 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <x-phosphor-flag class="w-5 h-5" />
                            Reports
                            @php
                                $openReports = \App\Models\Report::where('status', 'open')->count();
                            @endphp
                            @if($openReports > 0)
                                <span class="ml-auto px-2 py-0.5 text-xs font-medium bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full">{{ $openReports }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.settings') }}"
                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors
                                  {{ request()->routeIs('admin.settings*') ? 'bg-emerald-50 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <x-phosphor-gear class="w-5 h-5" />
                            Settings
                        </a>
                    </nav>

                    {{-- Footer --}}
                    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <img src="{{ Auth::user()->avatar }}"
                                 alt="{{ Auth::user()->name }}"
                                 class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-600" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('dashboard') }}"
                               class="flex-1 text-center px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                App
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/50 rounded-lg hover:bg-red-100 dark:hover:bg-red-900 transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                {{-- Main Content --}}
                <div class="flex-1 lg:ml-64">
                    {{-- Top Header --}}
                    <header class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center gap-4">
                                {{-- Mobile menu button --}}
                                <button x-on:click="$dispatch('toggle-sidebar')"
                                        class="lg:hidden p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <x-phosphor-list class="w-5 h-5" />
                                </button>

                                {{-- Page title --}}
                                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $title ?? 'Admin Dashboard' }}
                                </h1>
                            </div>

                            <div class="flex items-center gap-4">
                                {{-- Quick stats --}}
                                <div class="hidden md:flex items-center gap-6 text-sm">
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format(\App\Models\User::count()) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Users</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format(\App\Models\Profile::count()) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Profiles</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format(\App\Models\Report::where('status', 'open')->count()) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Open Reports</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    {{-- Page Content --}}
                    <main class="p-4 sm:p-6 lg:p-8">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        @include('partials.pwa-scripts')

        @stack('scripts')
    </body>
</html>
