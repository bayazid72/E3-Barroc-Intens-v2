<div class="max-w-6xl mx-auto py-8">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Mijn bezoeken (week)</h1>

        <div class="space-x-2">
            <button wire:click="previousWeek" class="px-3 py-1 border rounded">&lt; Vorige</button>
            <button wire:click="nextWeek" class="px-3 py-1 border rounded">Volgende &gt;</button>
        </div>
    </div>

    <p class="mb-4 text-sm text-neutral-600">
        Week van {{ $start->format('d-m-Y') }} t/m {{ $start->copy()->endOfWeek()->format('d-m-Y') }}
    </p>

    <div class="bg-white shadow rounded p-4 overflow-x-auto">
        <table class="min-w-full text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    @for($i = 0; $i < 7; $i++)
                        @php $d = $start->copy()->addDays($i); @endphp
                        <th class="px-2 py-1 text-center">
                            {{ $d->format('D d-m') }}
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                <tr>
                    @for($i = 0; $i < 7; $i++)
                        @php $d = $start->copy()->addDays($i)->format('Y-m-d'); @endphp
                        <td class="align-top border px-2 py-2">
                            @forelse($appointments[$d] ?? [] as $a)
                                <div class="mb-2 p-2 border rounded">
                                    <div class="font-semibold text-xs">{{ $a->date_planned?->format('H:i') }}</div>
                                    <div class="text-xs">{{ $a->company->name }}</div>
                                    <div class="text-[11px] text-neutral-500">{{ ucfirst($a->type) }}</div>
                                    <a href="{{ route('maintenance.workorder.form', $a->id) }}"
                                       class="inline-block mt-1 text-[11px] px-2 py-1 bg-yellow-500 text-white rounded">
                                        Werkbon
                                    </a>
                                </div>
                            @empty
                                <div class="text-[11px] text-neutral-400">Geen</div>
                            @endforelse
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>

</div>
