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
            {{ $appointment->date_planned
                ? $appointment->date_planned->format('d-m-Y H:i')
                : 'Nog niet gepland' }}
        </p>

        @if($appointment->malfunction_description)
            <p class="mt-2"><strong>Storing:</strong>
                {{ $appointment->malfunction_description }}
            </p>
        @endif
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Werkbon formulier --}}
    <div class="bg-white shadow rounded p-4">
        <form wire:submit.prevent="save" class="space-y-4">

            <div>
                <label class="block mb-1 font-semibold">Notities (optioneel)</label>
                <textarea wire:model.defer="notes"
                          rows="3"
                          class="w-full border rounded px-2 py-1"></textarea>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Oplossing (wat heb je gedaan?)</label>
                <textarea wire:model.defer="solution"
                          rows="4"
                          class="w-full border rounded px-2 py-1"></textarea>
                @error('solution')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-2 font-semibold">Gebruikte materialen</label>

                @foreach($materials as $index => $mat)
                    <div class="flex gap-2 mb-2">
                        <input type="text"
                               class="flex-1 border rounded px-2 py-1"
                               placeholder="Naam materiaal"
                               wire:model.defer="materials.{{ $index }}.name">

                        <input type="number"
                               min="1"
                               class="w-24 border rounded px-2 py-1"
                               placeholder="Aantal"
                               wire:model.defer="materials.{{ $index }}.quantity">

                        <button type="button"
                                class="px-2 py-1 bg-red-500 text-white rounded text-xs"
                                wire:click="removeMaterial({{ $index }})">
                            X
                        </button>
                    </div>
                @endforeach

                <button type="button"
                        wire:click="addMaterial"
                        class="mt-1 px-3 py-1 bg-neutral-200 rounded text-sm">
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
