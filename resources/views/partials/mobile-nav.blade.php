@php
    $tabs = [
        ['route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'squares-four', 'label' => 'Home'],
        ['route' => 'links', 'active' => 'links', 'icon' => 'link', 'label' => 'Links'],
        ['route' => 'appearance', 'active' => 'appearance', 'icon' => 'palette', 'label' => 'Design'],
        ['route' => 'profile', 'active' => 'profile', 'icon' => 'user', 'label' => 'Profile'],
    ];
    if (auth()->user()->isAdmin()) {
        $tabs[] = ['route' => 'admin.dashboard', 'active' => 'admin.*', 'icon' => 'shield-check', 'label' => 'Admin'];
    }
    $isAdmin = auth()->user()->isAdmin();
@endphp

{{-- App-like bottom tab bar for phones. Hidden on sm+ where the top nav takes over. --}}
<nav class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/90 backdrop-blur-md dark:border-gray-700 dark:bg-gray-900/90 sm:hidden"
     style="padding-bottom: env(safe-area-inset-bottom);">
    <div @class([
        'mx-auto grid max-w-md',
        'grid-cols-4' => ! $isAdmin,
        'grid-cols-5' => $isAdmin,
    ])>
        @foreach ($tabs as $tab)
            @php($isActive = request()->routeIs($tab['active']))
            <a href="{{ route($tab['route']) }}" wire:navigate
               class="relative flex flex-col items-center gap-0.5 py-2 text-[11px] font-medium transition-colors {{ $isActive ? 'text-emerald-500' : 'text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300' }}">
                @if ($isActive)
                    <span class="absolute top-0 h-0.5 w-8 rounded-full bg-emerald-500"></span>
                @endif
                @svg('phosphor-'.$tab['icon'], 'h-6 w-6')
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
