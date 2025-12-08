<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-4">Facturen</h1>

    {{-- Succesmelding --}}
    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif


    {{-- ========================= --}}
    {{--  NIEUWE FACTUUR AANMAKEN  --}}
    {{-- ========================= --}}
    <button
        wire:click="startCreate"
        class="mb-4 px-4 py-2 bg-yellow-500 text-white rounded">
         Nieuwe factuur
    </button>

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



    {{-- ========================= --}}
    {{--      FACTUUR BEWERKEN     --}}
    {{-- ========================= --}}
    @if ($editing)
        <div class="bg-white shadow rounded p-4 mb-6">
            <h2 class="text-lg font-semibold mb-3">Factuur bewerken</h2>

            <form wire:submit.prevent="updateInvoice" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Klant</label>
                    <select wire:model="company_id" class="w-full border rounded px-2 py-1">
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Factuurdatum</label>
                    <input type="date" wire:model="invoice_date" class="w-full border rounded px-2 py-1">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Totaalbedrag (€)</label>
                    <input type="number" step="0.01" wire:model="total_amount" class="w-full border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select wire:model="status" class="w-full border rounded px-2 py-1">
                        <option value="open">Open</option>
                        <option value="paid">Betaald</option>
                        <option value="overdue">Achterstallig</option>
                    </select>
                </div>


                <div class="flex gap-3">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">Opslaan</button>

                    <button type="button"
                            wire:click="$set('editing', false)"
                            class="px-4 py-2 border rounded">
                        Annuleren
                    </button>
                </div>


            </form>
        </div>
    @endif


    {{-- ========================= --}}
    {{--      FACTUREN TABEL       --}}
    {{-- ========================= --}}
    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Factuurnr</th>
                    <th class="py-2">Klant</th>
                    <th class="py-2">Datum</th>
                    <th class="py-2">Totaal</th>
                    <th class="py-2">Status</th>
                    <th class="py-2 text-right">Acties</th>
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

                        <td class="py-2 text-right">
                            <button
                                wire:click="editInvoice({{ $invoice->id }})"
                                class="px-3 py-1 bg-blue-500 text-white rounded text-xs">
                                Bewerken
                            </button>

                            @if($invoice->status !== 'paid')
                                <button
                                    wire:click="markAsPaid({{ $invoice->id }})"
                                    class="px-3 py-1 bg-green-600 text-white rounded text-xs ml-2">
                                    Betaald
                                </button>
                            @endif
                            <button
                                wire:click="deleteInvoice({{ $invoice->id }})"
                                class="px-3 py-1 bg-red-600 text-white rounded text-xs ml-2">
                                Verwijderen
                            </button>


                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-3 text-center text-gray-500">
                            Geen facturen gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
