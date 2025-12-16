<div class="max-w-6xl mx-auto py-8 space-y-6">

    <h1 class="text-2xl font-bold">Betaalde Facturen - Leveringsoverzicht</h1>

    <div>
        <input
            type="text"
            wire:model.debounce.300ms="search"
            placeholder="Zoek op klant of factuurnummer..."
            class="border rounded px-3 py-2 w-full"
        >
    </div>

    {{-- Backorders --}}
    @if ($backorderLines->count() > 0)
        <div class="p-4 bg-red-50 border border-red-200 rounded">
            <h2 class="font-semibold text-red-700 mb-2">
                Producten wachten op levering
            </h2>

            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b">
                        <th>Factuur</th>
                        <th>Klant</th>
                        <th>Product</th>
                        <th>Aantal</th>
                        <th>Betaald op</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($backorderLines as $line)
                        <tr class="border-b">
                            <td>{{ $line->invoice->invoice_number }}</td>
                            <td>{{ $line->invoice->company->name }}</td>
                            <td>{{ $line->product->name }}</td>
                            <td>{{ $line->amount }}</td>
                            <td>{{ $line->invoice->paid_at->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Alle betaalde facturen --}}
    <div class="bg-white shadow rounded p-4">

        <h2 class="text-lg font-semibold mb-4">Alle betaalde facturen</h2>

        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b">
                    <th>Factuur #</th>
                    <th>Klant</th>
                    <th>Bedrag</th>
                    <th>Betaald op</th>
                    <th>Levering</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($paidInvoices as $invoice)
                @php
                    $delivered = $invoice->invoiceLines->where('delivery_status','delivered')->count();
                    $total = $invoice->invoiceLines->count();
                @endphp

                <tr class="border-b">
                    <td class="py-2">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->company->name }}</td>
                    <td>€ {{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                    <td>{{ $invoice->paid_at->format('d-m-Y') }}</td>
                    <td>
                        @if($delivered === $total)
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                Alles geleverd
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                {{ $delivered }}/{{ $total }} geleverd
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $paidInvoices->links() }}
        </div>
    </div>

</div>
