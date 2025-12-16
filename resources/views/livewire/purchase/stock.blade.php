<div class="max-w-6xl mx-auto py-8 space-y-6">
    <h1 class="text-2xl font-bold">
        Voorraadbeheer
    </h1>

    @if (session('success'))
        <div class="p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- =====================
     | VOORRAAD TOEVOEGEN
     ===================== --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="font-semibold mb-3">
            Voorraad aanvullen
        </h2>

        <form wire:submit.prevent="add" class="space-y-3">
            <select
                wire:model.defer="product_id"
                class="border rounded px-2 py-1 w-full"
            >
                <option value="">
                    -- kies product --
                </option>

                @foreach ($products as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>

            <input
                type="number"
                wire:model.defer="quantity"
                min="1"
                class="border rounded px-2 py-1 w-full"
            >

            <button class="bg-yellow-500 text-white px-4 py-2 rounded">
                Toevoegen
            </button>
        </form>
    </div>

    {{-- =====================
     | PRODUCT FILTERS
     ===================== --}}
    <div class="bg-white shadow rounded p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
            wire:model.defer="search"
            placeholder="Zoek product..."
            class="border px-2 py-1 rounded"
        >

        <select
            wire:model.defer="filterCategory"
            class="border px-2 py-1 rounded"
        >
            <option value="">
                Alle categorieën
            </option>

            @foreach ($categories as $c)
                <option value="{{ $c->id }}">
                    {{ $c->name }}
                </option>
            @endforeach
        </select>

        <select
            wire:model.defer="filterVisibility"
            class="border px-2 py-1 rounded"
        >
            <option value="">
                Alles
            </option>
            <option value="public">
                Iedereen
            </option>
            <option value="employee">
                Medewerkers
            </option>
        </select>

        <select
            wire:model.defer="stockStatus"
            class="border px-2 py-1 rounded"
        >
            <option value="all">
                Alle
            </option>
            <option value="available">
                Leverbaar
            </option>
            <option value="out">
                Uit voorraad
            </option>
        </select>

        {{-- FILTER KNOPPEN --}}
        <div class="col-span-1 md:col-span-4 flex gap-2">
            <button
                wire:click="applyFilters"
                class="px-4 py-2 bg-blue-600 text-white rounded"
            >
                Filter toepassen
            </button>

            <button
                wire:click="resetFilters"
                class="px-4 py-2 bg-gray-300 rounded"
            >
                Reset
            </button>
        </div>
    </div>

    {{-- =====================
     | PRODUCTENTABEL
     ===================== --}}
    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Naam</th>
                    <th class="py-2">Categorie</th>
                    <th class="py-2">Prijs</th>
                    <th class="py-2">Voorraad</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Zichtbaarheid</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b">
                        <td class="py-2">
                            {{ $product->name }}
                        </td>
                        <td class="py-2">
                            {{ $product->category?->name ?? '-' }}
                        </td>
                        <td class="py-2">
                            € {{ number_format($product->price, 2, ',', '.') }}
                        </td>
                        <td class="py-2">
                            {{ $product->stock }}
                        </td>
                        <td class="py-2">
                            @if ($product->is_available)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                                    Leverbaar
                                </span>
                            @else
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">
                                    Uit voorraad
                                </span>
                            @endif
                        </td>
                        <td class="py-2">
                            {{ $product->category?->is_employee_only ? 'Medewerkers' : 'Klant & medewerker' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-zinc-500">
                            Geen producten gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
