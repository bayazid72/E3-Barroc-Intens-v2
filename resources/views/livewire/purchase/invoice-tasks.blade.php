@php
    use App\Models\Invoice;

    $invoices = Invoice::with(['company', 'invoiceLines.product'])
        ->where('status', Invoice::STATUS_PAID)
        ->whereIn('procurement_status', [
            Invoice::PROC_PENDING,
            Invoice::PROC_ORDERED,
            Invoice::PROC_DELIVERED,
        ])
        ->orderBy('invoice_date', 'desc')
        ->get();
@endphp

<div class="mt-10 bg-white shadow rounded p-6">
    <h2 class="text-xl font-semibold mb-4">
        Betaalde facturen → Actie vereist
    </h2>

    {{-- 🔔 Visuele melding voor Inkoop --}}
    @if($invoices->count() > 0)
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded text-sm">
            Je hebt <strong>{{ $invoices->count() }}</strong> betaalde factuur{{ $invoices->count() === 1 ? '' : 'en' }}
            waarvoor een inkoopactie nodig is.
        </div>
    @else
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded text-sm">
            Er zijn op dit moment geen betaalde facturen die nog een inkoopactie vereisen.
        </div>
    @endif

    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="border-b">
                <th class="py-2 text-left">Factuur</th>
                <th class="text-left">Klant</th>
                <th class="text-left">Producten</th>
                <th class="text-left">Bedrag</th>
                <th class="text-left">Inkoopstatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr class="border-b align-top">
                    <td class="py-2 font-medium">#{{ $invoice->id }}</td>
                    <td>{{ $invoice->company?->name }}</td>
                    <td>
                        @foreach($invoice->invoiceLines as $line)
                            <div>
                                {{ $line->product->name ?? 'Onbekend product' }} — {{ $line->amount }}x
                            </div>
                        @endforeach
                    </td>
                    <td>€ {{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                    <td>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $invoice->procurement_status_color_class }}">
                            {{ $invoice->procurement_status_label }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        Geen betaalde facturen die nog actie vereisen.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
