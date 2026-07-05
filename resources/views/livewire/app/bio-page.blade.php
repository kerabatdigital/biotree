<?php
$theme = $profile->publicTheme();
$isDark = App\Support\ThemeBuilder::isDark($theme['bg']);
@endphp

<!DOCTYPE html>
<html lang="en" class="{{ $isDark ? 'dark' : '' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $profile->display_name ?? $profile->username }} | BioTree</title>
    <meta name="description" content="{{ $profile->tagline ?? 'Check out my bio links!' }}">
    
    @if($profile->avatar_path)
    <link rel="icon" href="{{ asset("storage/{$profile->avatar_path}") }}">
    @endif
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', config("biotree.fonts.{$theme['font']}.bunny", 'Figtree') }}&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased" style="font-family: {{ config("biotree.fonts.{$theme['font']}.family", 'Figtree') }};">
    
    <!-- Animated Background -->
    <x-bio.animated-background :theme="$theme" />
    
    <div class="relative min-h-screen flex flex-col items-center px-4 py-12 gap-8">
        
        <!-- Profile Header -->
        <div class="flex flex-col items-center text-center max-w-lg w-full">
            
            <!-- Avatar -->
            @if($profile->avatar_path)
            <div class="relative mb-4">
                <img src="{{ asset("storage/{$profile->avatar_path}") }}" 
                     alt="{{ $profile->display_name }}"
                     class="{{ $theme['avatar_shape'] === 'circle' ? 'rounded-full' : ($theme['avatar_shape'] === 'rounded' ? 'rounded-2xl' : 'rounded-lg') }} 
                            w-28 h-28 md:w-32 md:h-32 object-cover border-4 shadow-2xl"
                     style="border-color: {{ $theme['accent'] }};">
                
                @if($profile->is_verified)
                <div class="absolute -bottom-1 -right-1 bg-blue-500 text-white rounded-full p-1.5 shadow-lg">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Display Name & Verified Badge -->
            <h1 class="text-2xl md:text-3xl font-bold" style="color: {{ $theme['text'] }};">
                {{ $profile->display_name ?? $profile->username }}
                
                @if($profile->is_verified)
                <span class="inline-flex items-center justify-center w-6 h-6 ml-1 bg-blue-500 text-white rounded-full align-middle">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </span>
                @endif
            </h1>
            
            <!-- Username -->
            <p class="text-sm mt-1" style="color: {{ $theme['muted'] }};">
                @{{ $profile->username }}
            </p>
            
            <!-- Tagline -->
            @if($profile->tagline)
            <p class="mt-3 text-base md:text-lg" style="color: {{ $theme['muted'] }};">
                {{ $profile->tagline }}
            </p>
            @endif
            
            <!-- Bio -->
            @if($profile->bio)
            <p class="mt-3 text-sm max-w-sm" style="color: {{ $theme['text'] }};">
                {{ $profile->bio }}
            </p>
            @endif
            
            <!-- Stats Bar -->
            @if($showStats ?? false)
            <div class="flex items-center gap-6 mt-4 py-3 px-6 rounded-full" 
                 style="background: {{ $theme['button_bg'] }};">
                <div class="text-center">
                    <div class="text-lg font-bold" style="color: {{ $theme['text'] }};">{{ $links->sum('click_count') }}</div>
                    <div class="text-xs" style="color: {{ $theme['muted'] }};">Clicks</div>
                </div>
                <div class="w-px h-8" style="background: {{ $theme['button_border'] }};"></div>
                <div class="text-center">
                    <div class="text-lg font-bold" style="color: {{ $theme['text'] }};">{{ $links->count() }}</div>
                    <div class="text-xs" style="color: {{ $theme['muted'] }};">Links</div>
                </div>
                <div class="w-px h-8" style="background: {{ $theme['button_border'] }};"></div>
                <div class="text-center">
                    <div class="text-lg font-bold" style="color: {{ $theme['text'] }};">{{ $views ?? 0 }}</div>
                    <div class="text-xs" style="color: {{ $theme['muted'] }};">Views</div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Links -->
        <div class="flex flex-col gap-3 w-full max-w-md">
            @foreach($links as $link)
            <div class="group relative">
                <!-- Link Card -->
                <a href="{{ $link->url }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   data-link-id="{{ $link->id }}"
                   data-link-url="{{ $link->url }}"
                   class="block w-full p-4 rounded-2xl shadow-lg backdrop-blur-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-xl active:scale-[0.98]"
                   style="
                        background: {{ $theme['button_bg'] }};
                        border: 1px solid {{ $theme['button_border'] }};
                        --tw-shadow-color: {{ $theme['accent_glow'] }};
                    "
                   x-data="{ showQr: false }">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($link->icon)
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                 style="background: {{ $theme['accent'] }}20; color: {{ $theme['accent'] }};">
                                <x-phosphor-{{ $link->icon }} class="w-5 h-5" />
                            </div>
                            @endif
                            
                            <div>
                                <div class="font-semibold" style="color: {{ $theme['text'] }};">
                                    {{ $link->title }}
                                </div>
                                @if($link->description)
                                <div class="text-xs mt-0.5" style="color: {{ $theme['muted'] }};">
                                    {{ Str::limit($link->description, 50) }}
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- QR Button -->
                        @if($showQr ?? true)
                        <button @click.prevent="showQr = !showQr; $dispatch('track-click', {id: {{ $link->id }}, url: '{{ $link->url }}')"
                                class="p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
                                style="background: {{ $theme['accent'] }}20; color: {{ $theme['accent'] }};"
                                title="Show QR Code">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </a>
                
                <!-- QR Code Modal -->
                <div x-show="showQr" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-full left-0 right-0 mt-2 p-4 rounded-xl shadow-xl z-10"
                     style="background: {{ $theme['bg'] }}; border: 1px solid {{ $theme['button_border'] }};">
                    <div class="flex flex-col items-center gap-3">
                        <img src="{{ App\Services\QrCodeService::generate(request()->url() . '/go/' . $link->id) }}" 
                             alt="QR Code" 
                             class="w-40 h-40 rounded-lg">
                        <p class="text-xs" style="color: {{ $theme['muted'] }};">Scan to open</p>
                        <a href="{{ App\Services\QrCodeService::download(request()->url() . '/go/' . $link->id) }}"
                           class="text-sm font-medium px-4 py-2 rounded-lg"
                           style="background: {{ $theme['accent'] }}; color: white;">
                            Download QR
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Footer -->
        <footer class="mt-auto pt-8 text-center">
            <p class="text-xs" style="color: {{ $theme['muted'] }};">
                Made with <span style="color: {{ $theme['accent'] }};">♥</span> on 
                <a href="{{ config('app.url') }}" class="font-medium hover:underline" style="color: {{ $theme['accent'] }};">BioTree</a>
            </p>
        </footer>
    </div>
    
    @push('scripts')
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('trackClick', () => ({
            init() {
                this.$el.addEventListener('click', () => {
                    const linkId = this.$el.dataset.linkId;
                    const linkUrl = this.$el.dataset.linkUrl;
                    if (linkId) {
                        fetch(`/api/track/click/${linkId}`, { method: 'POST', keepalive: true });
                    }
                });
            }
        }));
    });
    </script>
    @endpush
</body>
</html>
