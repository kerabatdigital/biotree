{{-- Animated Background Component --}}
@props(['theme' => [], 'animation' => 'none'])

@php
$animation = $theme['bg_animation'] ?? 'none';
$bg = $theme['bg'] ?? '#0a0a0a';
$bgEnd = $theme['bg_end'] ?? $bg;
$accent = $theme['accent'] ?? '#34d399';
@endphp

{{-- Gradient base --}}
<div class="fixed inset-0 -z-10" style="background: linear-gradient(135deg, {{ $bg }} 0%, {{ $bgEnd }} 100%);"></div>

{{-- Animation Layers --}}
@switch($animation)
    @case('gradient-shift')
        <div class="fixed inset-0 -z-10 opacity-30 animate-gradient-shift" 
             style="background: linear-gradient(45deg, {{ $bg }}, {{ $bgEnd }}, {{ $accent }}, {{ $bg }});">
        </div>
        @break

    @case('floating-orbs')
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute w-64 h-64 rounded-full opacity-20 animate-float-1" style="background: {{ $accent }}; top: 10%; left: 10%; filter: blur(60px);"></div>
            <div class="absolute w-48 h-48 rounded-full opacity-15 animate-float-2" style="background: {{ $bgEnd }}; top: 60%; right: 10%; filter: blur(40px);"></div>
            <div class="absolute w-32 h-32 rounded-full opacity-10 animate-float-3" style="background: {{ $accent }}; bottom: 20%; left: 30%; filter: blur(30px);"></div>
        </div>
        @break

    @case('stars')
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" id="stars-container">
            @for($i = 0; $i < 100; $i++)
                <div class="absolute w-1 h-1 rounded-full animate-twinkle" 
                     style="
                        left: {{ rand(0, 100) }}%;
                        top: {{ rand(0, 100) }}%;
                        animation-delay: {{ rand(0, 5) }}s;
                        animation-duration: {{ rand(2, 5) }}s;
                        background: white;
                        opacity: {{ rand(30, 100) / 100 }};
                     ">
                </div>
            @endfor
        </div>
        @break

    @case('aurora')
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute inset-0 animate-aurora opacity-30" 
                 style="background: 
                     radial-gradient(ellipse at 20% 50%, {{ $accent }}88 0%, transparent 50%),
                     radial-gradient(ellipse at 80% 50%, {{ $bgEnd }}88 0%, transparent 50%),
                     radial-gradient(ellipse at 50% 100%, {{ $accent }}44 0%, transparent 40%);
                 ">
            </div>
        </div>
        @break

    @case('waves')
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <svg class="absolute bottom-0 w-full animate-wave" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="{{ $accent }}" fill-opacity="0.1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
        @break

    @case('bubbles')
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            @for($i = 0; $i < 15; $i++)
                <div class="absolute rounded-full animate-rise" 
                     style="
                        width: {{ rand(10, 30) }}px;
                        height: {{ rand(10, 30) }}px;
                        left: {{ rand(0, 100) }}%;
                        bottom: -50px;
                        background: {{ $accent }};
                        opacity: {{ rand(10, 30) / 100 }};
                        animation-delay: {{ rand(0, 10) }}s;
                        animation-duration: {{ rand(8, 15) }}s;
                     ">
                </div>
            @endfor
        </div>
        @break

    @case('particles')
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" id="particles-container"></div>
        @break
@endswitch

@push('styles')
<style>
@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
.animate-gradient-shift {
    background-size: 400% 400%;
    animation: gradient-shift 15s ease infinite;
}

@keyframes float-1 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}
@keyframes float-2 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(-40px, 40px) scale(1.2); }
}
@keyframes float-3 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -20px) scale(0.8); }
}
.animate-float-1 { animation: float-1 20s ease-in-out infinite; }
.animate-float-2 { animation: float-2 15s ease-in-out infinite; }
.animate-float-3 { animation: float-3 18s ease-in-out infinite; }

@keyframes twinkle {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.5); }
}
.animate-twinkle {
    animation: twinkle 3s ease-in-out infinite;
}

@keyframes aurora {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
    50% { transform: translateY(-20px) rotate(5deg); opacity: 0.5; }
}
.animate-aurora {
    animation: aurora 10s ease-in-out infinite;
}

@keyframes wave {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-wave {
    animation: wave 25s linear infinite;
}

@keyframes rise {
    0% { transform: translateY(0) scale(1); opacity: 0.3; }
    50% { opacity: 0.6; }
    100% { transform: translateY(-100vh) scale(0.5); opacity: 0; }
}
.animate-rise {
    animation: rise 15s ease-in infinite;
}
</style>
@endpush

@if($animation === 'particles')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('particles-container');
    if (!container) return;
    
    const colors = ['{{ $accent }}', '{{ $bgEnd }}', 'rgba(255,255,255,0.5)'];
    
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.style.cssText = `
            position: absolute;
            width: ${Math.random() * 5 + 2}px;
            height: ${Math.random() * 5 + 2}px;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            border-radius: 50%;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            opacity: ${Math.random() * 0.5 + 0.1};
            animation: particle-float ${Math.random() * 20 + 10}s linear infinite;
            animation-delay: ${Math.random() * 10}s;
        `;
        container.appendChild(particle);
    }
});
</script>
<style>
@keyframes particle-float {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
}
</style>
@endpush
@endif
