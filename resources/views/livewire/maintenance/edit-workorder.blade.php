<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-4">Werkbon voor bezoek</h1>

    {{-- Afspraakgegevens --}}
    <div class="mb-4 bg-white shadow rounded p-4">
        <h2 class="font-semibold mb-2">Bezoekgegevens</h2>

        <p><strong>Klant:</strong>
            {{ $appointment->company?->name ?? 'Onbekende klant' }}
        </p>

        <p><strong>Type:</strong>
            {{ $appointment->type ? ucfirst($appointment->type) : 'Onbekend' }}
        </p>

        <p><strong>Geplande datum:</strong>
            {{ $appointment->date_planned ? $appointment->date_planned->format('d-m-Y H:i') : 'Nog niet gepland' }}
        </p>

        @if($appointment->malfunction_description)
            <p class="mt-2"><strong>Storing:</strong>
                {{ $appointment->malfunction_description }}
            </p>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 border border-green-300 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Werkbon formulier --}}
    <div class="bg-white shadow rounded p-4">
        <form wire:submit.prevent="save" class="space-y-4">

            <div>
                <label class="block mb-1 font-semibold">Notities</label>
                <textarea wire:model.defer="notes" rows="3"
                          class="w-full border rounded px-2 py-1"></textarea>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Oplossing</label>
                <textarea wire:model.defer="solution" rows="4"
                          class="w-full border rounded px-2 py-1"></textarea>
            </div>

            <div>
                <label class="block mb-2 font-semibold">Gebruikte materialen</label>

                @foreach($materials as $i => $mat)
                    <div class="flex gap-2 mb-2">
                        <input type="text"
                               wire:model.defer="materials.{{ $i }}.name"
                               class="flex-1 border rounded px-2 py-1"
                               placeholder="Naam materiaal">

                        <input type="number"
                               wire:model.defer="materials.{{ $i }}.quantity"
                               min="1"
                               class="w-24 border rounded px-2 py-1"
                               placeholder="Aantal">

                        <button type="button"
                                wire:click="removeMaterial({{ $i }})"
                                class="px-2 py-1 text-xs bg-red-500 text-white rounded">
                            X
                        </button>
                    </div>
                @endforeach

                <button type="button" wire:click="addMaterial"
                        class="mt-1 px-3 py-1 text-sm bg-neutral-200 rounded">
                    + Materiaal toevoegen
                </button>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-yellow-500 text-white rounded">
                Werkbon opslaan
            </button>

        </form>
    </div>

</div>
