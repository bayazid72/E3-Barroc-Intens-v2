<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">

    <h1 class="text-2xl font-bold mb-4">Afspraak bewerken</h1>

    <form wire:submit.prevent="save" class="space-y-4">

        {{-- Klant --}}
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

        {{-- Monteur --}}
        <div>
            <label class="font-semibold">Monteur</label>
            <select wire:model="technician_id" class="w-full border rounded px-2 py-1">
                <option value="">-- Geen monteur --</option>
                @foreach($technicians as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Type afspraak --}}
        <div>
            <label class="font-semibold">Type</label>
            <select wire:model="type" class="w-full border rounded px-2 py-1">
                <option value="malfunction">Storing</option>
                <option value="routine">Routinebezoek</option>
                <option value="installation">Installatie</option>
            </select>
        </div>

        {{-- Omschrijving storing --}}
        @if($type === 'malfunction')
            <div>
                <label class="font-semibold">Omschrijving storing</label>
                <textarea wire:model="malfunction_description"
                          class="w-full border rounded px-2 py-1"></textarea>
            </div>
        @endif

        {{-- Algemene omschrijving --}}
        <div>
            <label class="font-semibold">Omschrijving</label>
            <textarea wire:model="description"
                      class="w-full border rounded px-2 py-1"></textarea>
        </div>

        {{-- Datum & tijd --}}
        <div>
            <label class="font-semibold">Datum & tijd</label>
            <input type="datetime-local" wire:model="date_planned"
                   class="w-full border rounded px-2 py-1">
        </div>

        {{-- Status --}}
        <div>
            <label class="font-semibold">Status</label>
            <select wire:model="status" class="w-full border rounded px-2 py-1">
                <option value="open">Open</option>
                <option value="planned">Gepland</option>
                <option value="done">Afgerond</option>
                <option value="cancelled">Geannuleerd</option>
            </select>
        </div>

        <button class="px-4 py-2 bg-blue-600 text-white rounded">
            Opslaan
        </button>

    </form>

</div>
