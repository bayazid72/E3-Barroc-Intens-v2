<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Storingen</h1>

    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th>Klant</th>
                    <th>Monteur</th>
                    <th>Status</th>
                    <th>Aangemeld</th>
                </tr>
            </thead>
            <tbody>
                @forelse($malfunctions as $m)
                    <tr class="border-b">
                        <td>{{ $m->company->name }}</td>
                        <td>{{ $m->technician?->name ?? 'Geen monteur' }}</td>
                        <td>{{ ucfirst($m->status) }}</td>
                        <td>{{ $m->date_added?->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-3">Geen storingen</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
