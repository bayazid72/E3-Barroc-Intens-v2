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
                    <th class="w-40">Acties</th>
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

                        <!-- ⭐ Actieknoppen -->
                        <td class="flex gap-2 py-2">

                            <!-- Werkbon invullen (formulier) -->
                            <a href="{{ route('maintenance.workorder.form', $o->id) }}"
                               class="px-2 py-1 bg-green-600 text-white rounded text-xs">
                                Invullen
                            </a>

                            <!-- Bewerken (alleen manager) -->
                            @can('maintenance-manager')
                                <a href="{{ route('maintenance.workorder.edit', $o->id) }}"
                                   class="px-2 py-1 bg-yellow-500 text-white rounded text-xs">
                                    Bewerken
                                </a>
                            @endcan

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-3">Geen werkbonnen</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
