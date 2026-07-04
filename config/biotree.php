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
        'undefined', 'test', 'demo',
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

];
