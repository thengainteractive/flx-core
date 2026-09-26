<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Installed Modules</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Enable or disable feature modules on this system. When enabled, their Filament resources, navigation items, routes, and migrations become active immediately.
            </p>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
