<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Productbeheer</h1>

    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-2 bg-red-100 border border-red-400 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Product formulier --}}
    <div class="bg-white shadow rounded p-4 mb-6">

        <h2 class="text-lg font-semibold mb-3">
            @if ($editingId)
                Product bewerken
            @else
                Nieuw product toevoegen
            @endif
        </h2>

        <form wire:submit.prevent="save" class="space-y-4">

            <div>
                <label class="block text-sm font-medium">Naam</label>
                <input type="text" wire:model.defer="name" class="w-full border rounded px-2 py-1">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Prijs (€)</label>
                <input type="number" step="0.01" wire:model.defer="price" class="w-full border rounded px-2 py-1">
                @error('price') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Zichtbaarheid</label>
                <select wire:model.defer="visibility" class="w-full border rounded px-2 py-1">
                    <option value="">-- kies --</option>
                    <option value="public">Zichtbaar voor iedereen</option>
                    <option value="employee">Alleen medewerkers</option>
                </select>
                @error('visibility') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Leverancier</label>
                <input type="text" wire:model.defer="supplier" class="w-full border rounded px-2 py-1">
                @error('supplier') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Beschrijving</label>
                <textarea wire:model.defer="description" rows="3" class="w-full border rounded px-2 py-1"></textarea>
                @error('description') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <button class="px-4 py-2 bg-yellow-500 text-white rounded">
                @if($editingId)
                    Opslaan
                @else
                    Aanmaken
                @endif
            </button>

            @if($errorMessage)
                <div class="mt-2 text-red-600 text-sm">
                    {{ $errorMessage }}
                </div>
            @endif
        </form>

    </div>



    {{-- Producten tabel --}}
    <div class="bg-white shadow rounded p-4">


        <h2 class="text-lg font-semibold mb-4">Productenlijst</h2>
        {{-- FILTERBAR --}}
            <div class="p-4 bg-gray-50 border rounded mb-6 flex flex-col md:flex-row gap-3">

                {{-- Zoekveld --}}
                <input
                    type="text"
                    wire:model.defer="search"
                    placeholder="Zoek op naam"
                    class="w-full md:w-1/4 border rounded px-3 py-2"
                >

                {{-- Categorie --}}
                <select wire:model.defer="filterCategory" class="w-full md:w-1/4 border rounded px-3 py-2">
                    <option value="">Alle categorieën</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                {{-- Zichtbaarheid --}}
                <select wire:model.defer="filterVisibility" class="w-full md:w-1/4 border rounded px-3 py-2">
                    <option value="">Alle zichtbaarheid</option>
                    <option value="public">Zichtbaar voor iedereen</option>
                    <option value="employee">Alleen medewerkers</option>
                </select>

                {{-- Prijs min/max --}}
                <div class="flex gap-2 w-full md:w-1/4">
                    <input
                        type="number"
                        wire:model.defer="filterPriceMax"
                        placeholder="Max €"
                        min="0"
                        step="0.01"
                        class="w-1/2 border rounded px-2 py-2"
                    >
                </div>

                <button wire:click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Filter toepassen
                </button>

                <button wire:click="resetFilters" class="px-4 py-2 bg-gray-300 rounded">
                    Reset
                </button>
            </div>

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
                <th class="py-2">Leverancier</th>
                <th class="py-2 text-right">Acties</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($products as $product)
                <tr class="border-b">
                    <td class="py-2">{{ $product->name }}</td>

                    <td class="py-2">
                        {{ $product->category?->name ?? '-' }}
                    </td>

                    <td class="py-2">
                        €{{ number_format($product->price, 2, ',', '.') }}
                    </td>

                    <td class="py-2">
                        {{ $product->stock ?? '-' }}
                    </td>

                    <td class="py-2">
                        @if($product->is_available)
                            <span class="inline-flex px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                Momenteel leverbaar
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                Uit voorraad
                            </span>
                        @endif
                    </td>

                    <td class="py-2">
                        @if($product->category?->is_employee_only)
                            <span class="inline-flex px-2 py-1 rounded-full text-xs bg-zinc-200">
                                Alleen medewerkers
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                Zichtbaar voor iedereen
                            </span>
                        @endif
                    </td>

                    <td class="py-2">
                        {{ $product->supplier ?? '-' }}
                    </td>


                    <td class="py-2 text-right space-x-2">
                        <button
                            wire:click="edit({{ $product->id }})"
                            class="px-3 py-1 text-xs bg-blue-500 text-white rounded"
                        >
                            Bewerken
                        </button>

                        <button
                            onclick="confirm('Weet je zeker dat je dit product wilt verwijderen?') || event.stopImmediatePropagation()"
                            wire:click="deleteProduct({{ $product->id }})"
                            class="px-3 py-1 text-xs bg-red-500 text-white rounded"
                        >
                            Verwijderen
                        </button>


                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="py-4 text-center text-zinc-500">
                        Geen producten gevonden.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>



    </div>

</div>
