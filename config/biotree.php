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

];
