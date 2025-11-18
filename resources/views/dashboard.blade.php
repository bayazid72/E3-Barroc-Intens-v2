<x-layouts.app :title="__('Dashboard')">





        {{-- BOTTOM WIDE TILE — ALLEEN MANAGER --}}
        @can('manage-users')
            <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">

                <h2 class="text-xl font-semibold mb-4">Statistieken</h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                    <div class="bg-white/70 dark:bg-neutral-800 p-4 rounded-lg shadow text-center">
                        <div class="text-2xl font-bold text-yellow-500">{{ \App\Models\User::count() }}</div>
                        <div class="text-sm text-neutral-600 dark:text-neutral-300">Gebruikers</div>
                    </div>

                    <div class="bg-white/70 dark:bg-neutral-800 p-4 rounded-lg shadow text-center">
                        <div class="text-2xl font-bold text-yellow-500">{{ \App\Models\Company::count() }}</div>
                        <div class="text-sm text-neutral-600 dark:text-neutral-300">Klanten</div>
                    </div>

                    <div class="bg-white/70 dark:bg-neutral-800 p-4 rounded-lg shadow text-center">
                        <div class="text-2xl font-bold text-yellow-500">{{ \App\Models\Contract::count() }}</div>
                        <div class="text-sm text-neutral-600 dark:text-neutral-300">Contracten</div>
                    </div>

                    <div class="bg-white/70 dark:bg-neutral-800 p-4 rounded-lg shadow text-center">
                        <div class="text-2xl font-bold text-yellow-500">{{ \App\Models\Invoice::count() }}</div>
                        <div class="text-sm text-neutral-600 dark:text-neutral-300">Facturen</div>
                    </div>

                </div>

            </div>
        @endcan

    </div>

</x-layouts.app>
