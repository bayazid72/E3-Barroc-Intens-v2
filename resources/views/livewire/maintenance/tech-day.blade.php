<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Mijn bezoeken (dag)</h1>

    <div class="mb-4">
        <label class="block mb-1 font-semibold">Datum</label>
        <input type="date" wire:model.live="selectedDate"
               class="border rounded px-2 py-1">
    </div>

    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th>Tijd</th>
                    <th>Klant</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $a)
                    <tr class="border-b">
                        <td>{{ $a->date_planned?->format('H:i') }}</td>
                        <td>{{ $a->company->name }}</td>
                        <td>{{ ucfirst($a->type) }}</td>
                        <td>{{ ucfirst($a->status) }}</td>
                        <td class="text-right space-x-2">
                           <td class="text-right space-x-2">

                            <a href="{{ route('maintenance.view', $a->id) }}"
                            class="text-xs px-3 py-1 bg-blue-500 text-white rounded">
                                Details
                            </a>

                            <a href="{{ route('maintenance.workorder.form', $a->id) }}"
                            class="text-xs px-3 py-1 bg-yellow-500 text-white rounded">
                                Werkbon
                            </a>

                            {{-- 🤒 Ziek melden: alleen eigen geplande afspraak --}}
                            @if($a->technician_id === auth()->id() && $a->status === 'planned')
                                <button wire:click="markSick({{ $a->id }})"
                                        class="text-xs px-3 py-1 bg-red-600 text-white rounded">
                                    Ziek melden
                                </button>
                            @endif

                        </td>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">Geen bezoeken</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
