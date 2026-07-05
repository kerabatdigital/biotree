@php
    $avatarRadius = ['circle' => '50%', 'rounded' => '18px', 'square' => '8px'][$avatar_shape] ?? '50%';
    $previewAvatar = ($avatar && $avatar->isPreviewable())
        ? $avatar->temporaryUrl()
        : ($profile->avatar_path ? asset('storage/'.$profile->avatar_path) : null);
    $socials = $links->where('type', 'social');
    $items = $links->whereIn('type', ['link', 'header', 'embed']);
    $radiusOptions = ['0px' => 'Sharp', '16px' => 'Rounded', '999px' => 'Pill'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">

            {{-- ============================ CONTROLS ============================ --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Appearance</h1>
                    <div class="flex items-center gap-3">
                        <span x-data="{ show: false }" x-on:saved.window="show = true; setTimeout(() => show = false, 2200)"
                              x-show="show" x-cloak x-transition
                              class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                            <x-phosphor-check-circle class="h-4 w-4" /> Saved
                        </span>
                        <button wire:click="save" type="button"
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-neutral-950 transition hover:bg-emerald-400">
                            <span wire:loading.remove wire:target="save">Save</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </div>

                {{-- Profile --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Profile</h2>

                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-emerald-500/15 text-emerald-500">
                            @if ($previewAvatar)
                                <img src="{{ $previewAvatar }}" class="h-full w-full object-cover" alt="Avatar">
                            @else
                                <x-phosphor-tree-fill class="h-8 w-8" />
                            @endif
                        </div>
                        <div class="text-sm">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-gray-300 px-3 py-2 font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                                <x-phosphor-upload-simple class="h-4 w-4" />
                                <span wire:loading.remove wire:target="avatar">Upload</span>
                                <span wire:loading wire:target="avatar">Uploading…</span>
                                <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                            </label>
                            @if ($profile->avatar_path || $avatar)
                                <button wire:click="removeAvatar" type="button" class="ml-2 text-xs text-gray-400 hover:text-red-500">Remove</button>
                            @endif
                            <p class="mt-1 text-xs text-gray-400">PNG/JPG, max 2MB.</p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('avatar')" class="mt-2" />

                    <div class="mt-5 space-y-4">
                        <div>
                            <x-input-label for="dn" value="Display name" />
                            <x-text-input wire:model.live.debounce.400ms="display_name" id="dn" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('display_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="tg" value="Tagline" />
                            <x-text-input wire:model.live.debounce.400ms="tagline" id="tg" type="text" class="mt-1 block w-full" placeholder="A short line about you" />
                            <x-input-error :messages="$errors->get('tagline')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="bio" value="Bio" />
                            <textarea wire:model.live.debounce.500ms="bio" id="bio" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></textarea>
                            <x-input-error :messages="$errors->get('bio')" class="mt-1" />
                        </div>
                    </div>
                </section>

                {{-- Presets --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Presets</h2>
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                        @foreach ($presets as $key => $p)
                            <button wire:click="applyPreset('{{ $key }}')" type="button" title="{{ ucfirst($key) }}"
                                    class="group flex flex-col items-center gap-1">
                                <span class="h-12 w-full rounded-lg border-2 {{ $preset === $key ? 'border-emerald-500' : 'border-transparent' }}"
                                      style="background: linear-gradient(140deg, {{ $p['bg'] }}, {{ $p['bg_end'] }});">
                                    <span class="mx-auto mt-4 block h-2 w-2 rounded-full" style="background: {{ $p['accent'] }};"></span>
                                </span>
                                <span class="text-[11px] capitalize text-gray-500 dark:text-gray-400">{{ $key }}</span>
                            </button>
                        @endforeach
                    </div>
                </section>

                {{-- Colours --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Colours</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach (['bg' => 'Background', 'bg_end' => 'Background (bottom)', 'text' => 'Text', 'accent' => 'Accent'] as $field => $label)
                            <label class="flex items-center justify-between gap-2 rounded-lg border border-gray-200 px-3 py-2 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $label }}</span>
                                <input type="color" wire:model.live="{{ $field }}" class="h-8 w-10 cursor-pointer rounded border-0 bg-transparent p-0">
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- Style --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Style</h2>

                    <div class="space-y-5">
                        <div>
                            <p class="mb-2 text-sm text-gray-600 dark:text-gray-300">Button style</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['soft' => 'Soft', 'solid' => 'Solid', 'outline' => 'Outline'] as $val => $label)
                                    <button wire:click="$set('button_style', '{{ $val }}')" type="button"
                                            class="rounded-lg border px-3 py-2 text-sm font-medium transition {{ $button_style === $val ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-gray-300 text-gray-600 dark:border-gray-700 dark:text-gray-300' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="mb-2 text-sm text-gray-600 dark:text-gray-300">Button corners</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ($radiusOptions as $val => $label)
                                    <button wire:click="$set('button_radius', '{{ $val }}')" type="button"
                                            class="rounded-lg border px-3 py-2 text-sm font-medium transition {{ $button_radius === $val ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-gray-300 text-gray-600 dark:border-gray-700 dark:text-gray-300' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="mb-2 text-sm text-gray-600 dark:text-gray-300">Avatar shape</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['circle' => 'Circle', 'rounded' => 'Rounded', 'square' => 'Square'] as $val => $label)
                                    <button wire:click="$set('avatar_shape', '{{ $val }}')" type="button"
                                            class="rounded-lg border px-3 py-2 text-sm font-medium transition {{ $avatar_shape === $val ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-gray-300 text-gray-600 dark:border-gray-700 dark:text-gray-300' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <x-input-label for="font" value="Font" />
                            <select wire:model.live="font" id="font"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach ($fonts as $key => $f)
                                    <option value="{{ $key }}">{{ $f['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                {{-- Advanced --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-1 text-sm font-semibold uppercase tracking-wide text-gray-500">Advanced</h2>
                    <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                        Custom CSS applies only to your own public page. Written by you — use at your own risk.
                    </p>
                    <x-input-label for="custom_css" value="Custom CSS" />
                    <textarea wire:model="custom_css" id="custom_css" rows="6"
                              class="mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                              placeholder="body { --tw-gradient-from: #000; }"></textarea>
                    <x-input-error :messages="$errors->get('custom_css')" class="mt-1" />
                </section>
            </div>

            {{-- ============================ LIVE PREVIEW ============================ --}}
            <div class="lg:sticky lg:top-8 lg:self-start">
                <p class="mb-3 flex items-center justify-center gap-2 text-xs font-medium uppercase tracking-wide text-gray-400">
                    <x-phosphor-device-mobile class="h-4 w-4" /> Live preview
                </p>

                <link href="https://fonts.bunny.net/css?family={{ $fonts[$font]['bunny'] ?? 'figtree:400,600,700' }}" rel="stylesheet">

                <style>
                    .ap-prev { font-family: var(--ap-font), ui-sans-serif, system-ui, sans-serif; background: linear-gradient(180deg, var(--bg), var(--bg-end)); color: var(--text); }
                    .ap-prev .av { width: 76px; height: 76px; border-radius: var(--ap-avatar); margin: 0 auto; display: flex; align-items: center; justify-content: center; overflow: hidden; background: color-mix(in srgb, var(--accent) 22%, transparent); color: var(--accent); }
                    .ap-prev .av img { width: 100%; height: 100%; object-fit: cover; }
                    .ap-prev .nm { text-align: center; font-weight: 800; margin-top: 12px; font-size: 18px; }
                    .ap-prev .tg { text-align: center; color: var(--muted); font-size: 13px; margin-top: 4px; }
                    .ap-prev .soc { display: flex; justify-content: center; gap: 14px; margin: 14px 0 4px; color: var(--text); }
                    .ap-prev .hd { text-align: center; text-transform: uppercase; letter-spacing: .06em; font-size: 11px; font-weight: 700; color: var(--muted); margin: 16px 0 8px; }
                    .ap-prev .bt { display: flex; align-items: center; gap: 10px; padding: 12px 14px; margin-bottom: 10px; background: var(--btn-bg); color: var(--btn-text); border: 1px solid var(--btn-border); border-radius: var(--btn-radius); font-weight: 600; font-size: 14px; }
                    .ap-prev .bt .lb { flex: 1; text-align: center; }
                    .ap-prev .sp { width: 18px; }
                </style>

                <div class="mx-auto max-w-[300px] rounded-[2.5rem] border-[10px] border-gray-900 bg-gray-900 shadow-2xl dark:border-gray-700">
                    <div class="ap-prev h-[540px] overflow-y-auto rounded-[1.8rem] px-5 py-7"
                         style="--bg: {{ $theme['bg'] }}; --bg-end: {{ $theme['bg_end'] }}; --text: {{ $theme['text'] }}; --muted: {{ $theme['muted'] }}; --accent: {{ $theme['accent'] }}; --btn-bg: {{ $theme['button_bg'] }}; --btn-text: {{ $theme['button_text'] }}; --btn-border: {{ $theme['button_border'] }}; --btn-radius: {{ $theme['button_radius'] }}; --ap-avatar: {{ $avatarRadius }}; --ap-font: '{{ $fonts[$font]['family'] ?? 'Figtree' }}';">
                        <div class="av">
                            @if ($previewAvatar)
                                <img src="{{ $previewAvatar }}" alt="">
                            @else
                                @svg('phosphor-tree-fill', ['width' => 38, 'height' => 38])
                            @endif
                        </div>
                        <p class="nm">{{ $display_name ?: '@'.$profile->username }}</p>
                        @if ($tagline)
                            <p class="tg">{{ $tagline }}</p>
                        @endif

                        @if ($socials->isNotEmpty())
                            <div class="soc">
                                @foreach ($socials as $s)
                                    @svg('phosphor-'.($s->icon ?: 'link-simple'), ['width' => 22, 'height' => 22])
                                @endforeach
                            </div>
                        @endif

                        <div style="margin-top: 18px;">
                            @forelse ($items as $link)
                                @if ($link->type === 'header')
                                    <p class="hd">{{ $link->title }}</p>
                                @else
                                    <div class="bt">
                                        @if ($link->icon) @svg('phosphor-'.$link->icon, ['width' => 18, 'height' => 18]) @else <span class="sp"></span> @endif
                                        <span class="lb">{{ $link->title ?: 'Link' }}</span>
                                        <span class="sp"></span>
                                    </div>
                                @endif
                            @empty
                                @if ($socials->isEmpty())
                                    <p class="tg" style="margin-top: 30px;">Add links to see them here ✨</p>
                                @endif
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
