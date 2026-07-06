<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin allowlist
    |--------------------------------------------------------------------------
    | Emails listed here are always treated as admins, in addition to any user
    | whose DB `role` column is 'admin'. Set via the ADMIN_EMAILS env var
    | (comma-separated) so the privileged address is never hardcoded in source.
    */
    'admin_emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ADMIN_EMAILS', ''))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Reserved usernames (system)
    |--------------------------------------------------------------------------
    | Route/system words that can never be a public profile handle (they would
    | collide with app routes). These are NOT for sale — they're protected.
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

    /*
    |--------------------------------------------------------------------------
    | Premium usernames (available to buy from admin)
    |--------------------------------------------------------------------------
    | Well-known brands, names and trending words. These can't be claimed for
    | free — the onboarding screen tells the user the handle is available to
    | purchase from the admin. Manage/sell these however you like.
    */
    'premium_usernames' => [
        // Big tech & platforms
        'google', 'facebook', 'meta', 'apple', 'amazon', 'microsoft', 'netflix',
        'spotify', 'youtube', 'instagram', 'tiktok', 'twitter', 'x', 'snapchat',
        'whatsapp', 'telegram', 'discord', 'twitch', 'linkedin', 'pinterest',
        'reddit', 'tumblr', 'github', 'gitlab', 'uber', 'grab', 'foodpanda',
        'airbnb', 'tesla', 'spacex', 'nvidia', 'intel', 'amd', 'samsung', 'huawei',
        'xiaomi', 'sony', 'lg', 'dell', 'hp', 'lenovo', 'oracle', 'ibm', 'adobe',
        'salesforce', 'paypal', 'stripe', 'visa', 'mastercard', 'shopify',
        'wordpress', 'wix', 'squarespace', 'canva', 'figma', 'notion', 'slack',
        'zoom', 'dropbox', 'cloudflare', 'openai', 'chatgpt', 'claude', 'anthropic',
        'gemini', 'grok', 'perplexity', 'midjourney',
        // Brands & lifestyle
        'nike', 'adidas', 'puma', 'gucci', 'prada', 'chanel', 'dior', 'versace',
        'rolex', 'ferrari', 'lamborghini', 'porsche', 'bmw', 'mercedes', 'toyota',
        'honda', 'cocacola', 'pepsi', 'mcdonalds', 'kfc', 'starbucks', 'disney',
        'marvel', 'dc', 'pixar', 'playstation', 'xbox', 'nintendo', 'steam',
        'redbull', 'nestle', 'loreal',
        // Crypto & finance
        'bitcoin', 'btc', 'ethereum', 'eth', 'crypto', 'binance', 'coinbase',
        'nft', 'web3', 'defi', 'solana', 'dogecoin', 'shiba', 'metamask', 'trust',
        'forex', 'trading', 'invest', 'wealth', 'finance', 'bank',
        // Trending / premium single words
        'love', 'money', 'boss', 'king', 'queen', 'prince', 'princess', 'vip',
        'gold', 'diamond', 'official', 'ceo', 'founder', 'business', 'marketing',
        'digital', 'agency', 'studio', 'media', 'news', 'music', 'art', 'design',
        'fashion', 'beauty', 'makeup', 'skincare', 'fitness', 'gym', 'health',
        'food', 'foodie', 'travel', 'photography', 'photo', 'video', 'gaming',
        'gamer', 'tech', 'ai', 'shop', 'store', 'sale', 'deals', 'brand', 'brands',
        'influencer', 'creator', 'viral', 'trending', 'best', 'top', 'world',
        'global', 'prime', 'luxury', 'elite', 'premium', 'star', 'stars',
        'celebrity', 'model', 'models', 'coach', 'guru', 'expert', 'academy',
        'school', 'university', 'church', 'mosque', 'temple', 'charity',
        // Malaysia-focused (primary market)
        'malaysia', 'my', 'kl', 'kualalumpur', 'selangor', 'johor', 'penang',
        'melaka', 'sabah', 'sarawak', 'perak', 'kedah', 'kelantan', 'terengganu',
        'pahang', 'negeri', 'putrajaya', 'cyberjaya', 'myr', 'ringgit', 'halal',
        'muslim', 'islam', 'quran', 'shopee', 'lazada', 'maybank', 'cimb',
        'touchngo', 'tng', 'astro', 'petronas', 'airasia',
        // Globally famous mononyms
        'ronaldo', 'messi', 'neymar', 'mbappe', 'lebron', 'jordan', 'kobe',
        'beyonce', 'rihanna', 'drake', 'eminem', 'adele', 'madonna', 'shakira',
        'oprah', 'elon', 'musk', 'bezos', 'gates', 'zuckerberg', 'ronaldinho',
        'neymarjr', 'blackpink', 'bts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Premium username purchase contact
    |--------------------------------------------------------------------------
    | Shown as the "contact admin to buy" link on the claim screen. Falls back
    | to the first configured admin email.
    */
    'premium_username_contact' => env('PREMIUM_USERNAME_CONTACT')
        ?: (array_values(array_filter(array_map('trim', explode(',', (string) env('ADMIN_EMAILS', '')))))[0] ?? null),

    'username' => [
        'min' => 3,
        'max' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Link icon picker
    |--------------------------------------------------------------------------
    */
    'link_icons' => [
        'Social' => [
            'instagram-logo', 'tiktok-logo', 'youtube-logo', 'facebook-logo',
            'x-logo', 'twitter-logo', 'threads-logo', 'linkedin-logo', 'whatsapp-logo',
            'telegram-logo', 'snapchat-logo', 'pinterest-logo', 'discord-logo',
            'twitch-logo', 'github-logo', 'reddit-logo', 'medium-logo',
            'behance-logo', 'dribbble-logo', 'spotify-logo', 'soundcloud-logo',
            'mastodon-logo', 'butterfly', 'tumblr-logo', 'video-camera',
        ],
        'Music' => [
            'spotify-logo', 'soundcloud-logo', 'apple-logo', 'tidal-logo', 'music-notes',
        ],
        'General' => [
            'link', 'link-simple', 'globe', 'envelope-simple', 'phone', 'chat-circle',
            'shopping-cart', 'shopping-bag', 'storefront', 'wallet', 'calendar-blank',
            'map-pin', 'music-notes', 'play-circle', 'video-camera', 'camera', 'image',
            'file-pdf', 'coffee', 'heart', 'star', 'gift', 'ticket', 'briefcase',
            'graduation-cap', 'newspaper', 'microphone', 'headphones', 'house', 'user-circle',
        ],
        'Work' => [
            'briefcase', 'graduation-cap', 'certificate', 'chart-line-up',
            'currency-dollar', 'storefront', 'receipt', 'handshake',
        ],
        'Creative' => [
            'pencil', 'paint-brush', 'camera', 'image', 'film-strip', 'palette',
            'pen-nib', 'scissors', 'hammer', 'wrench',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme presets
    |--------------------------------------------------------------------------
    */
    'default_theme' => [
        'preset' => 'midnight',
        'bg' => '#0a0a0a',
        'bg_end' => '#18181b',
        'text' => '#ffffff',
        'accent' => '#34d399',
        'button_style' => 'soft',
        'button_radius' => '16px',
        'avatar_shape' => 'circle',
        'font' => 'figtree',
        'bg_animation' => 'none',
        'link_animation' => 'none',
    ],

    'theme_presets' => [
        // Dark Themes
        'midnight' => ['bg' => '#0a0a0a', 'bg_end' => '#18181b', 'text' => '#ffffff', 'accent' => '#34d399', 'label' => 'Midnight'],
        'ocean' => ['bg' => '#0b1120', 'bg_end' => '#0e2a47', 'text' => '#eaf2ff', 'accent' => '#38bdf8', 'label' => 'Ocean'],
        'sunset' => ['bg' => '#2a0e0e', 'bg_end' => '#4a1d1d', 'text' => '#fff5f0', 'accent' => '#fb7185', 'label' => 'Sunset'],
        'grape' => ['bg' => '#160f2e', 'bg_end' => '#2a1a54', 'text' => '#f3ecff', 'accent' => '#a78bfa', 'label' => 'Grape'],
        'forest' => ['bg' => '#0a1710', 'bg_end' => '#12281b', 'text' => '#eafff2', 'accent' => '#4ade80', 'label' => 'Forest'],
        'paper' => ['bg' => '#fafaf9', 'bg_end' => '#f0efec', 'text' => '#1c1917', 'accent' => '#f97316', 'label' => 'Paper'],
        
        // Cool New Themes
        'neon' => ['bg' => '#0a0a0f', 'bg_end' => '#1a0a2e', 'text' => '#ffffff', 'accent' => '#ff00ff', 'label' => 'Neon 💫'],
        'cyberpunk' => ['bg' => '#0d0221', 'bg_end' => '#1a0533', 'text' => '#00ffff', 'accent' => '#ff0080', 'label' => 'Cyberpunk 🤖'],
        'aurora' => ['bg' => '#0a192f', 'bg_end' => '#172a45', 'text' => '#64ffda', 'accent' => '#00b4d8', 'label' => 'Aurora 🌌'],
        'lava' => ['bg' => '#1a0a0a', 'bg_end' => '#2d1010', 'text' => '#ffccd5', 'accent' => '#ff6b6b', 'label' => 'Lava 🌋'],
        'arctic' => ['bg' => '#e8f4f8', 'bg_end' => '#c9e4eb', 'text' => '#1a365d', 'accent' => '#0ea5e9', 'label' => 'Arctic ❄️'],
        'honey' => ['bg' => '#fef3c7', 'bg_end' => '#fde68a', 'text' => '#78350f', 'accent' => '#f59e0b', 'label' => 'Honey 🍯'],
        'emerald' => ['bg' => '#022c22', 'bg_end' => '#064e3b', 'text' => '#ecfdf5', 'accent' => '#10b981', 'label' => 'Emerald 💎'],
        'starlight' => ['bg' => '#0f0f23', 'bg_end' => '#1a1a3e', 'text' => '#f8f8ff', 'accent' => '#fbbf24', 'label' => 'Starlight ⭐'],
        'candy' => ['bg' => '#fef1f8', 'bg_end' => '#fce7f3', 'text' => '#831843', 'accent' => '#ec4899', 'label' => 'Candy 🍬'],
        'mint' => ['bg' => '#ecfdf5', 'bg_end' => '#d1fae5', 'text' => '#064e3b', 'accent' => '#10b981', 'label' => 'Mint 🍃'],
        'lavender' => ['bg' => '#faf5ff', 'bg_end' => '#ede9fe', 'text' => '#4c1d95', 'accent' => '#8b5cf6', 'label' => 'Lavender 💜'],
        'sunrise' => ['bg' => '#fef2f2', 'bg_end' => '#fecaca', 'text' => '#7f1d1d', 'accent' => '#ef4444', 'label' => 'Sunrise 🌅'],
        'midnight-blue' => ['bg' => '#0c4a6e', 'bg_end' => '#0369a1', 'text' => '#f0f9ff', 'accent' => '#38bdf8', 'label' => 'Midnight Blue 🌊'],
        'space' => ['bg' => '#030712', 'bg_end' => '#111827', 'text' => '#f9fafb', 'accent' => '#818cf8', 'label' => 'Space 🚀'],
        'peach' => ['bg' => '#fff7ed', 'bg_end' => '#fed7aa', 'text' => '#7c2d12', 'accent' => '#fb923c', 'label' => 'Peach 🍑'],
        'slate' => ['bg' => '#1e293b', 'bg_end' => '#334155', 'text' => '#f1f5f9', 'accent' => '#38bdf8', 'label' => 'Slate 📐'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Background Animations
    |--------------------------------------------------------------------------
    */
    'bg_animations' => [
        'none' => ['label' => 'Static'],
        'gradient-shift' => ['label' => 'Gradient Shift'],
        'floating-orbs' => ['label' => 'Floating Orbs'],
        'particles' => ['label' => 'Particles'],
        'stars' => ['label' => 'Stars ✨'],
        'aurora' => ['label' => 'Aurora'],
        'waves' => ['label' => 'Waves 🌊'],
        'bubbles' => ['label' => 'Bubbles 🫧'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Link Animations
    |--------------------------------------------------------------------------
    */
    'link_animations' => [
        'none' => ['label' => 'Static'],
        'slide-up' => ['label' => 'Slide Up'],
        'glow' => ['label' => 'Glow on Hover'],
        'scale' => ['label' => 'Scale'],
        'wiggle' => ['label' => 'Wiggle'],
        'bounce' => ['label' => 'Bounce'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fonts
    |--------------------------------------------------------------------------
    */
    'fonts' => [
        'figtree' => ['label' => 'Figtree', 'family' => 'Figtree', 'bunny' => 'figtree:400,500,600,700,800'],
        'inter' => ['label' => 'Inter', 'family' => 'Inter', 'bunny' => 'inter:400,500,600,700'],
        'poppins' => ['label' => 'Poppins', 'family' => 'Poppins', 'bunny' => 'poppins:400,500,600,700'],
        'space-grotesk' => ['label' => 'Space Grotesk', 'family' => 'Space Grotesk', 'bunny' => 'space-grotesk:400,500,600,700'],
        'dm-sans' => ['label' => 'DM Sans', 'family' => 'DM Sans', 'bunny' => 'dm-sans:400,500,600,700'],
        'sora' => ['label' => 'Sora', 'family' => 'Sora', 'bunny' => 'sora:400,500,600,700'],
        'outfit' => ['label' => 'Outfit', 'family' => 'Outfit', 'bunny' => 'outfit:400,500,600,700'],
        'plus-jakarta-sans' => ['label' => 'Plus Jakarta Sans', 'family' => 'Plus Jakarta Sans', 'bunny' => 'plus-jakarta-sans:400,500,600,700'],
        'manrope' => ['label' => 'Manrope', 'family' => 'Manrope', 'bunny' => 'manrope:400,500,600,700,800'],
        'syne' => ['label' => 'Syne', 'family' => 'Syne', 'bunny' => 'syne:400,500,600,700,800'],
    ],

];
