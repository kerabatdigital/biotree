<div class="py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">

            {{-- ============================ EDITOR ============================ --}}
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Links</h1>
                        <a href="{{ url('/'.$profileUsername) }}" target="_blank"
                           class="mt-1 inline-flex items-center gap-1 text-sm text-emerald-600 hover:underline dark:text-emerald-400">
                            biotree.my/{{ $profileUsername }}
                            <x-phosphor-arrow-square-out class="h-4 w-4" />
                        </a>
                    </div>
                </div>

                {{-- add buttons --}}
                <div class="mt-6 flex flex-wrap gap-2">
                    <button wire:click="newLink('link')" type="button"
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-neutral-950 transition hover:bg-emerald-400">
                        <x-phosphor-plus class="h-4 w-4" /> Add link
                    </button>
                    <button wire:click="newLink('header')" type="button"
                            class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        <x-phosphor-text-t class="h-4 w-4" /> Header
                    </button>
                    <button wire:click="newLink('social')" type="button"
                            class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        <x-phosphor-share-network class="h-4 w-4" /> Social
                    </button>
                </div>

                {{-- add/edit form --}}
                @if ($showForm)
                    <div wire:key="link-form"
                         class="mt-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                {{ $editingId ? 'Edit' : 'New' }} {{ $type }}
                            </h2>
                            <button wire:click="cancelForm" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <x-phosphor-x class="h-5 w-5" />
                            </button>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div>
                                <x-input-label for="l-title" :value="$type === 'header' ? 'Header text' : 'Title'" />
                                <x-text-input wire:model="title" id="l-title" type="text" class="mt-1 block w-full"
                                              placeholder="{{ $type === 'header' ? 'Section title' : 'e.g. My YouTube channel' }}" />
                                <x-input-error :messages="$errors->get('title')" class="mt-1" />
                            </div>

                            @if ($type !== 'header')
                                <div>
                                    <x-input-label for="l-url" value="URL" />
                                    <x-text-input wire:model="url" id="l-url" type="url" class="mt-1 block w-full" placeholder="https://…" />
                                    <x-input-error :messages="$errors->get('url')" class="mt-1" />
                                </div>

                                {{-- scheduling --}}
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <x-input-label for="l-start-at" value="Show from (optional)" />
                                        <x-text-input wire:model="start_at" id="l-start-at" type="datetime-local" class="mt-1 block w-full" />
                                        <x-input-error :messages="$errors->get('start_at')" class="mt-1" />
                                    </div>
                                    <div>
                                        <x-input-label for="l-end-at" value="Hide after (optional)" />
                                        <x-text-input wire:model="end_at" id="l-end-at" type="datetime-local" class="mt-1 block w-full" />
                                        <x-input-error :messages="$errors->get('end_at')" class="mt-1" />
                                    </div>
                                </div>

                                {{-- icon picker --}}
                                <div x-data="{ open: false, q: '' }" class="relative">
                                    <x-input-label value="Icon" />
                                    <div class="mt-1 flex items-center gap-2">
                                        <button type="button" @click="open = !open"
                                                class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                                            @if ($icon)
                                                @svg('phosphor-'.$icon, 'w-5 h-5 text-gray-700 dark:text-gray-200')
                                                <span class="text-gray-700 dark:text-gray-200">{{ $icon }}</span>
                                            @else
                                                <x-phosphor-smiley class="h-5 w-5 text-gray-400" />
                                                <span class="text-gray-400">Choose icon</span>
                                            @endif
                                        </button>
                                        @if ($icon)
                                            <button type="button" wire:click="selectIcon(null)" class="text-xs text-gray-400 hover:text-red-500">clear</button>
                                        @endif
                                    </div>

                                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                                         class="absolute z-30 mt-2 w-80 rounded-xl border border-gray-200 bg-white p-3 shadow-xl dark:border-gray-700 dark:bg-gray-800">
                                        <div class="relative">
                                            <x-phosphor-magnifying-glass class="pointer-events-none absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
                                            <input x-model="q" type="text" placeholder="Search…"
                                                   class="mb-2 w-full rounded-md border-gray-300 pl-8 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                        </div>
                                        <div class="max-h-56 overflow-y-auto">
                                            @foreach ($iconGroups as $group => $icons)
                                                <p class="px-1 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ $group }}</p>
                                                <div class="grid grid-cols-6 gap-1">
                                                    @foreach ($icons as $name)
                                                        <button type="button" title="{{ $name }}"
                                                                x-show="'{{ $name }}'.includes(q.toLowerCase().replaceAll(' ', ''))"
                                                                @click="$wire.set('icon', '{{ $name }}'); open = false; q = ''"
                                                                class="flex items-center justify-center rounded-md p-2 text-gray-700 transition hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                                            @svg('phosphor-'.$name, 'w-5 h-5')
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-end gap-2 pt-1">
                                <button wire:click="cancelForm" type="button" class="rounded-md px-4 py-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-200">Cancel</button>
                                <button wire:click="save" type="button"
                                        class="inline-flex items-center gap-2 rounded-md bg-emerald-500 px-5 py-2 text-sm font-semibold text-neutral-950 transition hover:bg-emerald-400">
                                    <x-phosphor-check class="h-4 w-4" /> Save
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- link list --}}
                @if ($links->isEmpty())
                    <div class="mt-8 rounded-2xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">
                        <x-phosphor-list class="mx-auto h-8 w-8 text-gray-300" />
                        <p class="mt-3 text-sm text-gray-500">No links yet. Add your first one above.</p>
                    </div>
                @else
                    <ul wire:key="links-{{ $links->count() }}" x-data="linkSortable" class="mt-6 space-y-3">
                        @foreach ($links as $link)
                            <li wire:key="link-{{ $link->id }}" data-id="{{ $link->id }}"
                                class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 {{ $link->is_active ? '' : 'opacity-60' }}">
                                <button type="button" class="drag-handle cursor-grab text-gray-300 hover:text-gray-500 active:cursor-grabbing">
                                    <x-phosphor-dots-six-vertical class="h-5 w-5" />
                                </button>

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                    @if ($link->type === 'header')
                                        <x-phosphor-text-t class="h-5 w-5" />
                                    @elseif ($link->icon)
                                        @svg('phosphor-'.$link->icon, 'w-5 h-5')
                                    @else
                                        <x-phosphor-link-simple class="h-5 w-5" />
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $link->title ?: '(untitled)' }}</p>
                                    @if ($link->type !== 'header')
                                        <p class="truncate text-xs text-gray-400">{{ $link->url }}</p>
                                    @else
                                        <p class="text-xs text-gray-400">Section header</p>
                                    @endif
                                </div>

                                <button wire:click="toggleActive({{ $link->id }})" type="button" title="{{ $link->is_active ? 'Visible' : 'Hidden' }}"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    @if ($link->is_active)
                                        <x-phosphor-eye class="h-5 w-5" />
                                    @else
                                        <x-phosphor-eye-slash class="h-5 w-5" />
                                    @endif
                                </button>
                                <button wire:click="editLink({{ $link->id }})" type="button" class="text-gray-400 hover:text-emerald-600">
                                    <x-phosphor-pencil-simple class="h-5 w-5" />
                                </button>
                                <button wire:click="deleteLink({{ $link->id }})" wire:confirm="Delete this link?" type="button" class="text-gray-400 hover:text-red-500">
                                    <x-phosphor-trash class="h-5 w-5" />
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- ============================ LIVE PREVIEW ============================ --}}
            <div class="lg:sticky lg:top-8 lg:self-start">
                <p class="mb-3 flex items-center justify-center gap-2 text-xs font-medium uppercase tracking-wide text-gray-400">
                    <x-phosphor-device-mobile class="h-4 w-4" /> Live preview
                </p>
                <div class="mx-auto max-w-[300px] rounded-[2.5rem] border-[10px] border-gray-900 bg-gray-900 shadow-2xl dark:border-gray-700">
                    <div class="h-[560px] overflow-y-auto rounded-[1.8rem] bg-gradient-to-b from-neutral-950 to-neutral-900 p-5">
                        <div class="flex flex-col items-center pt-4 text-center">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
                                <x-phosphor-tree-fill class="h-10 w-10" />
                            </div>
                            <p class="mt-3 font-semibold text-white">{{ auth()->user()->profile->display_name ?: '@'.$profileUsername }}</p>
                            <p class="text-xs text-neutral-400">biotree.my/{{ $profileUsername }}</p>
                        </div>

                        <div class="mt-6 space-y-3">
                            @forelse ($links->where('is_active', true) as $link)
                                @if ($link->type === 'header')
                                    <p class="pt-2 text-center text-xs font-semibold uppercase tracking-wide text-neutral-400">{{ $link->title }}</p>
                                @else
                                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 text-sm font-medium text-white backdrop-blur">
                                        @if ($link->icon)
                                            @svg('phosphor-'.$link->icon, 'w-5 h-5 shrink-0')
                                        @endif
                                        <span class="flex-1 truncate text-center">{{ $link->title ?: 'Link' }}</span>
                                    </div>
                                @endif
                            @empty
                                <p class="pt-10 text-center text-xs text-neutral-500">Your links appear here ✨</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
