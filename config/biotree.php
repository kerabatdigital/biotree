<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reserved usernames
    |--------------------------------------------------------------------------
    | These can never be claimed as a public page (biotree.my/{username}) —
    | they collide with app routes or are held back for the platform.
    */
    'reserved_usernames' => [
        'admin', 'administrator', 'dashboard', 'api', 'login', 'logout', 'register',
        'auth', 'oauth', 'settings', 'setting', 'account', 'accounts', 'links', 'link',
        'appearance', 'analytics', 'onboarding', 'out', 'l', 'u', 'go', 'about',
        'pricing', 'terms', 'privacy', 'policy', 'help', 'support', 'contact', 'blog',
        'app', 'www', 'mail', 'email', 'ftp', 'status', 'billing', 'upgrade', 'pro',
        'me', 'home', 'explore', 'discover', 'search', 'signup', 'sign-up', 'signin',
        'sign-in', 'password', 'verify', 'biotree', 'root', 'system', 'static',
        'assets', 'img', 'images', 'css', 'js', 'fonts', 'storage', 'public', 'null',
        'undefined', 'test', 'demo', 'track', 'up',
    ],

    'username' => [
        'min' => 3,
        'max' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Link icon picker
    |--------------------------------------------------------------------------
    | Curated Phosphor icons offered in the link editor. Names are Phosphor
    | slugs, rendered via @svg('phosphor-'.$name) or <x-phosphor-{name} />.
    */
    'link_icons' => [
        'Social' => [
            'instagram-logo', 'tiktok-logo', 'youtube-logo', 'facebook-logo',
            'x-logo', 'twitter-logo', 'threads-logo', 'linkedin-logo', 'whatsapp-logo',
            'telegram-logo', 'snapchat-logo', 'pinterest-logo', 'discord-logo',
            'twitch-logo', 'github-logo', 'reddit-logo', 'medium-logo',
            'behance-logo', 'dribbble-logo', 'spotify-logo', 'soundcloud-logo',
        ],
        'General' => [
            'link', 'link-simple', 'globe', 'envelope-simple', 'phone', 'chat-circle',
            'shopping-cart', 'shopping-bag', 'storefront', 'wallet', 'calendar-blank',
            'map-pin', 'music-notes', 'play-circle', 'video-camera', 'camera', 'image',
            'file-pdf', 'coffee', 'heart', 'star', 'gift', 'ticket', 'briefcase',
            'graduation-cap', 'newspaper', 'microphone', 'headphones', 'house', 'user-circle',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    | default_theme holds the editable INPUTS. App\Support\ThemeBuilder turns
    | those into the concrete values the public page renders (muted, button
    | colours, etc.), so the editor / preview / public page always agree.
    */
    'default_theme' => [
        'preset' => 'midnight',
        'bg' => '#0a0a0a',
        'bg_end' => '#18181b',
        'text' => '#ffffff',
        'accent' => '#34d399',
        'button_style' => 'soft',   // soft | solid | outline
        'button_radius' => '16px',
        'avatar_shape' => 'circle', // circle | rounded | square
        'font' => 'figtree',
    ],

    'theme_presets' => [
        'midnight' => ['bg' => '#0a0a0a', 'bg_end' => '#18181b', 'text' => '#ffffff', 'accent' => '#34d399'],
        'ocean' => ['bg' => '#0b1120', 'bg_end' => '#0e2a47', 'text' => '#eaf2ff', 'accent' => '#38bdf8'],
        'sunset' => ['bg' => '#2a0e0e', 'bg_end' => '#4a1d1d', 'text' => '#fff5f0', 'accent' => '#fb7185'],
        'grape' => ['bg' => '#160f2e', 'bg_end' => '#2a1a54', 'text' => '#f3ecff', 'accent' => '#a78bfa'],
        'forest' => ['bg' => '#0a1710', 'bg_end' => '#12281b', 'text' => '#eafff2', 'accent' => '#4ade80'],
        'paper' => ['bg' => '#fafaf9', 'bg_end' => '#f0efec', 'text' => '#1c1917', 'accent' => '#f97316'],
    ],

    'fonts' => [
        'figtree' => ['label' => 'Figtree', 'family' => 'Figtree', 'bunny' => 'figtree:400,500,600,700,800'],
        'inter' => ['label' => 'Inter', 'family' => 'Inter', 'bunny' => 'inter:400,500,600,700'],
        'poppins' => ['label' => 'Poppins', 'family' => 'Poppins', 'bunny' => 'poppins:400,500,600,700'],
        'space-grotesk' => ['label' => 'Space Grotesk', 'family' => 'Space Grotesk', 'bunny' => 'space-grotesk:400,500,600,700'],
        'dm-sans' => ['label' => 'DM Sans', 'family' => 'DM Sans', 'bunny' => 'dm-sans:400,500,600,700'],
        'sora' => ['label' => 'Sora', 'family' => 'Sora', 'bunny' => 'sora:400,500,600,700'],
    ],

];
