<div class="py-12">
    <div class="mx-auto max-w-md px-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-2 text-emerald-500">
                <x-phosphor-tree-fill class="h-8 w-8" />
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ config('app.name', 'BioTree') }}</span>
            </div>

            <h1 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Claim your link</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Pick a username — this becomes your public page.
            </p>

            <form wire:submit="claim" class="mt-6 space-y-5">
                <div>
                    <x-input-label for="display_name" :value="__('Display name')" />
                    <x-text-input wire:model="display_name" id="display_name" class="mt-1 block w-full" type="text" required />
                    <x-input-error :messages="$errors->get('display_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="username" :value="__('Username')" />
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">biotree.my/</span>
                        <input wire:model.live.debounce.400ms="username" id="username" type="text"
                               class="block w-full rounded-none rounded-r-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                               autocomplete="off" autofocus />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />

                    @if ($username && $errors->isEmpty())
                        <p class="mt-2 flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400">
                            <x-phosphor-check-circle class="h-4 w-4" /> biotree.my/{{ $username }} is available
                        </p>
                    @endif
                </div>

                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-full bg-emerald-500 px-6 py-3 font-semibold text-neutral-950 transition hover:bg-emerald-400">
                    <x-phosphor-rocket-launch class="h-5 w-5" />
                    <span wire:loading.remove wire:target="claim">Create my page</span>
                    <span wire:loading wire:target="claim">Creating…</span>
                </button>
            </form>
        </div>
    </div>
</div>
