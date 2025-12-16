<div class="max-w-4xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Werkbon voor bezoek</h1>

    <div class="bg-white shadow rounded p-6 space-y-6">

        <!-- Bezoekgegevens -->
        <div>
            <h2 class="text-lg font-semibold mb-2">Bezoekgegevens</h2>

            <p><strong>Klant:</strong> {{ $appointment->company->name }}</p>
            <p><strong>Type:</strong> {{ $appointment->type }}</p>
            <p><strong>Geplande datum:</strong> {{ $appointment->date_planned }}</p>
        </div>


        <!-- Notes -->
        <div>
            <label class="block font-medium mb-1">Notities (optioneel)</label>
            <textarea wire:model="notes" rows="3" class="w-full border rounded px-2 py-1"></textarea>
        </div>

        <!-- Solution -->
        <div>
            <label class="block font-medium mb-1">Oplossing</label>
            <textarea wire:model="solution" rows="3" class="w-full border rounded px-2 py-1"></textarea>
        </div>

        <!-- Gebruikte Materialen -->
        <div>
            <h2 class="text-lg font-semibold mb-3">Gebruikte materialen</h2>

            @foreach($materials as $i => $mat)
                <div class="flex gap-3 mb-3 items-center">

                    <!-- Product dropdown -->
                    <select wire:model="materials.{{ $i }}.product_id"
                            class="border rounded px-2 py-1 w-1/2">
                        <option value="">-- kies materiaal --</option>

                        @foreach($products as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->name }} (voorraad: {{ $p->stock }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Quantity -->
                    <input type="number"
                           wire:model="materials.{{ $i }}.quantity"
                           class="w-20 border rounded px-2 py-1"
                           min="1">

                    <!-- Verwijderen -->
                    <button wire:click="removeMaterial({{ $i }})"
                            class="px-2 py-1 bg-red-500 text-white rounded">
                        X
                    </button>
                </div>
            @endforeach

            <button wire:click="addMaterial"
                    class="px-3 py-1 bg-blue-500 text-white rounded text-sm">
                + Materiaal toevoegen
            </button>
        </div>

        <button wire:click="save"
                class="px-4 py-2 bg-green-600 text-white rounded">
            Opslaan
        </button>
    </div>

    @if(session('success'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-800 p-2 rounded">
            {{ session('success') }}
        </div>
    @endif

</div>
