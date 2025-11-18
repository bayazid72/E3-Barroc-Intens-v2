<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Contractbeheer</h1>

    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM --}}
    <div class="bg-white shadow rounded p-4 mb-8">

        <h2 class="text-lg font-semibold mb-4">
            @if ($editingContractId)
                Contract bewerken
            @else
                Nieuw contract aanmaken
            @endif
        </h2>

        <form wire:submit.prevent="saveContract" class="space-y-5">

            {{-- Klant --}}
            <div>
                <label class="block text-sm font-bold mb-1">Klant</label>
                <select wire:model="company_id" class="w-full border rounded px-2 py-1">
                    <option value="">-- kies klant --</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Startdatum --}}
            <div>
                <label class="block text-sm font-bold mb-1">Startdatum</label>
                <input type="date" wire:model="starts_at" class="w-full border rounded px-2 py-1">
            </div>

            {{-- Einddatum --}}
            <div>
                <label class="block text-sm font-bold mb-1">Einddatum (optioneel)</label>
                <input type="date" wire:model="ends_at" class="w-full border rounded px-2 py-1">
            </div>

            {{-- Facturatiemodel --}}
            <div>
                <label class="block text-sm font-bold mb-1">Facturatiemodel</label>
                <select wire:model="invoice_type" class="w-full border rounded px-2 py-1">
                    <option value="">-- kies model --</option>
                    <option value="monthly">Maandelijks</option>
                    <option value="periodic">Periodiek</option>
                </select>
            </div>

            {{-- Interval --}}
            @if($invoice_type === 'periodic')
                <div>
                    <label class="block text-sm font-bold mb-1">Interval (months)</label>
                    <input type="number" wire:model="periodic_interval_months"
                           min="1" class="w-full border rounded px-2 py-1">
                </div>
            @endif

            {{-- Regels --}}
            <div class="border rounded p-4 bg-neutral-50">

                <h3 class="text-lg font-semibold mb-3">Contractregels</h3>

                @foreach($rulesData as $i => $rule)
                    <div class="border p-3 mb-3 rounded bg-white">

                        <label class="block text-sm">Product</label>
                        <select wire:model="rulesData.{{ $i }}.product_id"
                                class="w-full border rounded px-2 py-1 mb-2">
                            <option value="">-- kies product --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>

                        <label class="block text-sm">Aantal</label>
                        <input type="number" min="1"
                               wire:model="rulesData.{{ $i }}.quantity"
                               class="w-full border rounded px-2 py-1 mb-2">

                        <button type="button"
                                wire:click="removeRule({{ $i }})"
                                class="px-3 py-1 bg-red-500 text-white rounded text-xs">
                            Verwijderen
                        </button>

                    </div>
                @endforeach

                <button type="button"
                        wire:click="addRule"
                        class="px-4 py-2 bg-blue-500 text-white rounded">
                    + Regel toevoegen
                </button>

            </div>

            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">
                @if ($editingContractId)
                    Opslaan
                @else
                    Aanmaken
                @endif
            </button>

        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded p-4">

        <h2 class="text-lg font-semibold mb-4">Alle contracten</h2>

        <table class="w-full text-left text-sm border-collapse">
            <thead>
            <tr class="border-b">
                <th>Klant</th>
                <th>Facturatie</th>
                <th>Start</th>
                <th>Einde</th>
                <th class="text-right">Acties</th>
            </tr>
            </thead>

            <tbody>
            @foreach($contracts as $contract)
                <tr class="border-b">
                    <td>{{ $contract->company->name }}</td>
                    <td>{{ $contract->invoice_type === 'monthly' ? 'Maandelijks' : 'Periodiek' }}</td>
                    <td>{{ $contract->starts_at }}</td>
                    <td>{{ $contract->ends_at ?? '-' }}</td>

                    <td class="text-right">
                        <button wire:click="editContract({{ $contract->id }})"
                                class="px-3 py-1 bg-blue-500 text-white rounded text-xs">Bewerken</button>

                        <button wire:click="deleteContract({{ $contract->id }})"
                                class="px-3 py-1 bg-red-500 text-white rounded text-xs">Verwijderen</button>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>

    </div>

</div>
