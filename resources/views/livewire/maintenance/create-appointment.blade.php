<div class="max-w-xl mx-auto p-6 bg-white rounded shadow">

    <h1 class="text-2xl font-bold mb-4">Nieuwe afspraak inplannen</h1>

    @if(session('success'))
        <div class="p-2 bg-green-200 text-green-800 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">

        <div>
            <label class="font-semibold">Klant</label>
            <select wire:model="company_id" class="w-full border rounded px-2 py-1">
                <option value="">-- Kies klant --</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            @error('company_id') <p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="font-semibold">Monteur</label>
            <select wire:model="technician_id" class="w-full border rounded px-2 py-1">
                <option value="">-- Geen monteur --</option>
                @foreach($technicians as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="font-semibold">Type</label>
            <select wire:model="type" class="w-full border rounded px-2 py-1">
                <option value="storing">Storing</option>
                <option value="routine">Routinebezoek</option>
                <option value="installation">Installatie</option>
            </select>
        </div>

        <div>
            <label class="font-semibold">Omschrijving</label>
            <textarea wire:model="description"
                      class="w-full border rounded px-2 py-1"
                      rows="3"></textarea>
            @error('description') <p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="font-semibold">Datum & tijd</label>
            <input type="datetime-local" wire:model="date_planned"
                   class="w-full border rounded px-2 py-1">
        </div>

        <button class="px-4 py-2 bg-yellow-500 text-white rounded">
            Opslaan
        </button>

    </form>

</div>
