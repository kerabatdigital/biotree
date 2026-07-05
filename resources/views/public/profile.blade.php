@php
    $theme = $profile->publicTheme();
    $avatarRadius = ['circle' => '50%', 'rounded' => '22px', 'square' => '10px'][$theme['avatar_shape'] ?? 'circle'] ?? '50%';
    $displayName = $profile->display_name ?: '@'.$profile->username;
    $description = $profile->seo_description ?: ($profile->tagline ?: $profile->bio ?: 'My links on BioTree');
    $socials = $links->where('type', 'social');
    $items = $links->whereIn('type', ['link', 'header', 'embed']);
    $avatarUrl = $profile->avatar_path ? asset('storage/'.$profile->avatar_path) : null;
    $fontDef = config('biotree.fonts')[$theme['font']] ?? config('biotree.fonts')['figtree'];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ $theme['bg'] }}">
    <title>{{ $profile->seo_title ?: $displayName }} · BioTree</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($description, 160) }}">
    <link rel="canonical" href="{{ url('/'.$profile->username) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="profile">
    <meta property="og:site_name" content="BioTree">
    <meta property="og:title" content="{{ $displayName }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($description, 160) }}">
    <meta property="og:url" content="{{ url('/'.$profile->username) }}">
    <meta property="og:image" content="{{ $avatarUrl ?: asset('og-default.png') }}">
    <meta name="twitter:card" content="{{ $avatarUrl ? 'summary' : 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $displayName }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit($description, 160) }}">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $displayName,
            'url' => url('/'.$profile->username),
            'sameAs' => $socials->pluck('url')->filter()->values()->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family={{ $fontDef['bunny'] }}" rel="stylesheet">

    <style>
        :root {
            --bg: {{ $theme['bg'] }};
            --bg-end: {{ $theme['bg_end'] }};
            --text: {{ $theme['text'] }};
            --muted: {{ $theme['muted'] }};
            --btn-bg: {{ $theme['button_bg'] }};
            --btn-text: {{ $theme['button_text'] }};
            --btn-border: {{ $theme['button_border'] }};
            --btn-radius: {{ $theme['button_radius'] }};
            --accent: {{ $theme['accent'] }};
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: '{{ $fontDef['family'] }}', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: linear-gradient(180deg, var(--bg), var(--bg-end)) fixed;
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        .wrap { max-width: 640px; margin: 0 auto; padding: 56px 20px 40px; }
        .avatar, .avatar-fallback {
            width: 96px; height: 96px; border-radius: {{ $avatarRadius }};
            margin: 0 auto; display: block; object-fit: cover;
            box-shadow: 0 8px 30px rgba(0,0,0,.35);
        }
        .avatar-fallback {
            display: flex; align-items: center; justify-content: center;
            background: color-mix(in srgb, var(--accent) 22%, transparent);
            color: var(--accent);
        }
        .name { text-align: center; font-size: 22px; font-weight: 800; margin-top: 18px; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .name .verified { color: var(--accent); }
        .tagline { text-align: center; color: var(--muted); margin-top: 6px; font-size: 15px; }
        .socials { display: flex; flex-wrap: wrap; justify-content: center; gap: 18px; margin: 22px 0 6px; }
        .socials a { color: var(--text); opacity: .85; transition: opacity .15s, transform .15s; }
        .socials a:hover { opacity: 1; transform: translateY(-2px); }
        .links { margin-top: 26px; }
        .header { text-align: center; text-transform: uppercase; letter-spacing: .07em; font-size: 12px; font-weight: 700; color: var(--muted); margin: 26px 0 12px; }
        .btn {
            display: flex; align-items: center; gap: 12px; width: 100%;
            padding: 15px 18px; margin-bottom: 14px;
            background: var(--btn-bg); color: var(--btn-text);
            border-radius: var(--btn-radius); text-decoration: none; font-weight: 600;
            transition: transform .12s ease, filter .12s ease;
            border: 1px solid var(--btn-border);
        }
        .btn:hover { transform: translateY(-2px); filter: brightness(1.08); }
        .btn .label { flex: 1; text-align: center; }
        .btn .spacer { width: 20px; }
        .empty { text-align: center; color: var(--muted); margin-top: 40px; font-size: 14px; }
        .footer { text-align: center; margin-top: 44px; }
        .footer a {
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--muted); text-decoration: none; font-size: 13px; font-weight: 600;
            padding: 8px 14px; border-radius: 999px; border: 1px solid var(--btn-border);
        }
        .footer a:hover { color: var(--text); }
    </style>
    @if ($profile->custom_css)
        <style>{!! $profile->custom_css !!}</style>
    @endif
</head>
<body>
    <main class="wrap">
        @if ($avatarUrl)
            <img class="avatar" src="{{ $avatarUrl }}" alt="{{ $displayName }}">
        @else
            <div class="avatar-fallback">@svg('phosphor-tree-fill', ['width' => 48, 'height' => 48])</div>
        @endif

        <h1 class="name">
            {{ $displayName }}
            @if ($profile->is_verified)
                <span class="verified" title="Verified">@svg('phosphor-seal-check-fill', ['width' => 18, 'height' => 18])</span>
            @endif
        </h1>

        @if ($profile->tagline)
            <p class="tagline">{{ $profile->tagline }}</p>
        @endif

        @if ($socials->isNotEmpty())
            <nav class="socials" aria-label="Social links">
                @foreach ($socials as $social)
                    <a href="{{ route('link.out', $social) }}" rel="noopener" aria-label="{{ $social->title }}">
                        @svg('phosphor-'.($social->icon ?: 'link-simple'), ['width' => 26, 'height' => 26])
                    </a>
                @endforeach
            </nav>
        @endif

        <div class="links">
            @forelse ($items as $link)
                @if ($link->type === 'header')
                    <p class="header">{{ $link->title }}</p>
                @else
                    <a class="btn" href="{{ route('link.out', $link) }}" rel="noopener">
                        @if ($link->icon)
                            @svg('phosphor-'.$link->icon, ['width' => 20, 'height' => 20])
                        @else
                            <span class="spacer"></span>
                        @endif
                        <span class="label">{{ $link->title ?: 'Link' }}</span>
                        <span class="spacer"></span>
                    </a>
                @endif
            @empty
                @if ($socials->isEmpty())
                    <p class="empty">No links yet.</p>
                @endif
            @endforelse
        </div>

        <footer class="footer">
            <a href="{{ url('/') }}" rel="noopener">
                @svg('phosphor-tree-fill', ['width' => 15, 'height' => 15])
                Join {{ $profile->username }} on BioTree
            </a>
        </footer>
    </main>

    <script>
        (function () {
            try {
                var d = new FormData();
                d.append('profile', @json($profile->id));
                navigator.sendBeacon(@json(route('track.view')), d);
            } catch (e) {}
        })();
    </script>
</body>
</html>
