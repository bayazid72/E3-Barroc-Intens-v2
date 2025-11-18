<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-4">Facturen</h1>

    {{-- Succesmelding --}}
    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Knop Factuur aanmaken --}}
    <button
        wire:click="startCreate"
        class="mb-4 px-4 py-2 bg-yellow-500 text-white rounded">
        ➕ Nieuwe factuur
    </button>

    {{-- Formulier --}}
    @if ($creating)
        <div class="bg-white shadow rounded p-4 mb-6">
            <h2 class="text-lg font-semibold mb-3">Nieuwe factuur aanmaken</h2>

            <form wire:submit.prevent="createInvoice" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Klant</label>
                    <select wire:model="company_id" class="w-full border rounded px-2 py-1">
                        <option value="">-- kies klant --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Factuurdatum</label>
                    <input type="date" wire:model="invoice_date" class="w-full border rounded px-2 py-1">
                    @error('invoice_date') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Totaalbedrag (€)</label>
                    <input type="number" step="0.01" wire:model="total_amount" class="w-full border rounded px-2 py-1">
                    @error('total_amount') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="flex gap-3">
                    <button class="px-4 py-2 bg-yellow-500 text-white rounded">Opslaan</button>

                    <button type="button"
                            wire:click="$set('creating', false)"
                            class="px-4 py-2 border rounded">
                        Annuleren
                    </button>
                </div>

            </form>
        </div>
    @endif


    {{-- Facturen Tabel --}}
    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Factuurnr</th>
                    <th class="py-2">Klant</th>
                    <th class="py-2">Datum</th>
                    <th class="py-2">Totaal</th>
                    <th class="py-2">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($invoices as $invoice)
                    <tr class="border-b">
                        <td class="py-2">{{ $invoice->id }}</td>
                        <td class="py-2">{{ $invoice->company->name }}</td>
                        <td class="py-2">{{ $invoice->invoice_date }}</td>
                        <td class="py-2">€{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="py-2">
                            @if($invoice->status === 'open')
                                <span class="text-yellow-600">Open</span>
                            @elseif($invoice->status === 'paid')
                                <span class="text-green-600">Betaald</span>
                            @else
                                <span class="text-red-600">Achterstallig</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-center text-gray-500">
                            Geen facturen gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
