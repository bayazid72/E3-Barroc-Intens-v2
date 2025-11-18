<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Werkbonnen</h1>

    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th>Klant</th>
                    <th>Monteur</th>
                    <th>Notities</th>
                    <th>Oplossing</th>
                    <th>Materialen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                    <tr class="border-b">
                        <td>{{ $o->appointment?->company?->name ?? 'Onbekend' }}</td>
                        <td>{{ $o->technician?->name ?? 'Geen' }}</td>

                        <td>{{ \Illuminate\Support\Str::limit($o->notes ?? '-', 30) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($o->solution ?? '-', 30) }}</td>

                        <td>
                            @if($o->materials_used)
                                {{ collect($o->materials_used)
                                    ->map(fn($m) => ($m['name'] ?? '?') . ' x ' . ($m['quantity'] ?? 1))
                                    ->join(', ') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">Geen werkbonnen</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
