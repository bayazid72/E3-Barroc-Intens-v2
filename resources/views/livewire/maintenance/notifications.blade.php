<div class="max-w-4xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">📨 Werkbon Notificaties</h1>

    <!-- FILTERBALK -->
    <div class="bg-white p-4 rounded shadow mb-6 flex flex-col md:flex-row gap-3">

        <input type="text"
               wire:model.defer="searchInput"
               placeholder="Zoek op klant, probleem, Technicus"
               class="w-full md:w-1/3 border rounded px-3 py-2">

        <select wire:model.defer="technicianFilterInput"
                class="w-full md:w-1/4 border rounded px-3 py-2">
            <option value="">Alle technici</option>

            @php
                $techs = collect($notifications)->pluck('data.technician')->unique()->sort();
            @endphp

            @foreach ($techs as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>

        <button wire:click="applyFilters"
                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            Filter toepassen
        </button>

        <button wire:click="resetFilters"
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
            Reset
        </button>

    </div>

    <!-- NOTIFICATIES -->
    @foreach($notifications as $n)
        <div class="bg-white shadow rounded p-4 mb-4 border border-neutral-200">

            <h2 class="text-lg font-bold">{{ $n->title }}</h2>

            <p class="mt-2">
                <strong>Technicus:</strong> {{ $n->data->technician }}<br>
                <strong>Klant:</strong> {{ $n->data->company }}<br>
                <strong>Datum bezoek:</strong> {{ \Carbon\Carbon::parse($n->data->date)->format('d-m-Y H:i') }}<br>
                <strong>Probleem:</strong> {{ $n->data->problem }}<br>
                <strong>Oplossing:</strong> {{ $n->data->solution }}
            </p>

            @if(count($n->data->materials))
                <div class="mt-3">
                    <strong>Gebruikte materialen:</strong>
                    <ul class="list-disc pl-6">
                        @foreach($n->data->materials as $mat)
                            <li>{{ $mat->name }} ({{ $mat->quantity }}x)</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('maintenance.workorder.form', $n->data->workorder_id) }}"
               class="mt-3 inline-block px-3 py-1 bg-yellow-500 text-white rounded">
                Bekijk werkbon
            </a>

            <p class="text-xs text-neutral-500 mt-2">
                {{ $n->created_at->format('d-m-Y H:i') }}
            </p>

        </div>
    @endforeach

</div>
