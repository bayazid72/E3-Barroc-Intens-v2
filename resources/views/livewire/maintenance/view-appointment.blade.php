<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">

    <h1 class="text-2xl font-bold mb-4">Afspraak Details</h1>

    <p><strong>Klant:</strong>
        {{ $appointment->company->name }}
    </p>

    <p><strong>Type:</strong>
        {{ ucfirst($appointment->type) }}
    </p>

    <p><strong>Monteur:</strong>
        {{ $appointment->technician?->name ?? 'Niet toegewezen' }}
    </p>

    <p><strong>Datum:</strong>
        {{ $appointment->date_planned?->format('d-m-Y H:i') ?? 'Nog niet ingepland' }}
    </p>

    <p class="mt-4"><strong>Omschrijving:</strong><br>
        {{ $appointment->description ?: 'Geen omschrijving ingevoerd.' }}
    </p>

</div>
