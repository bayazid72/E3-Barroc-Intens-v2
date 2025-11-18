<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Productbeheer</h1>

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
                <label class="block text-sm font-medium">Categorie</label>
                <select wire:model.defer="category_id" class="w-full border rounded px-2 py-1">
                    <option value="">-- kies categorie --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Leverancier + Beschrijving</label>
                <textarea wire:model.defer="description" rows="3" class="w-full border rounded px-2 py-1"></textarea>
            </div>

            <button class="px-4 py-2 bg-yellow-500 text-white rounded">
                @if($editingId)
                    Opslaan
                @else
                    Aanmaken
                @endif
            </button>

        </form>

    </div>


    {{-- Producten tabel --}}
    <div class="bg-white shadow rounded p-4">

        <h2 class="text-lg font-semibold mb-4">Productenlijst</h2>

        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Naam</th>
                    <th class="py-2">Prijs</th>
                    <th class="py-2">Categorie</th>
                    <th class="py-2 text-right">Acties</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($products as $product)
                    <tr class="border-b">
                        <td class="py-2">{{ $product->name }}</td>
                        <td class="py-2">€{{ number_format($product->price, 2) }}</td>
                        <td class="py-2">{{ $product->category->name }}</td>

                        <td class="py-2 text-right space-x-2">
                            <button wire:click="edit({{ $product->id }})"
                                    class="px-3 py-1 text-xs bg-blue-500 text-white rounded">
                                Bewerken
                            </button>

                            <button wire:click="delete({{ $product->id }})"
                                    class="px-3 py-1 text-xs bg-red-500 text-white rounded">
                                Verwijderen
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>
