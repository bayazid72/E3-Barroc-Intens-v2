<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">

    <h1 class="text-2xl font-bold mb-4">Afspraak details</h1>

    <p><strong>Klant:</strong> {{ $appointment->company->name }}</p>
    <p><strong>Type:</strong> {{ ucfirst($appointment->type) }}</p>
    <p><strong>Monteur:</strong> {{ $appointment->technician?->name ?? 'Niet toegewezen' }}</p>
    <p><strong>Datum:</strong>
        {{ $appointment->date_planned?->format('d-m-Y H:i') ?? 'Nog niet ingepland' }}
    </p>

    <p class="mt-4">
        <strong>Omschrijving:</strong><br>
        {{ $appointment->description ?: 'Geen omschrijving ingevoerd.' }}
    </p>

    @if($appointment->status === 'sick')
        <div class="mt-6">
            <button wire:click="takeOver"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                ✅ Afspraak overnemen
            </button>
        </div>
    @endif

    @can('maintenance-manager')
        <div class="mt-6">
            <a href="{{ route('maintenance.edit', $appointment->id) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded">
                ✏️ Afspraak bewerken
            </a>
        </div>
    @endcan
</div>
