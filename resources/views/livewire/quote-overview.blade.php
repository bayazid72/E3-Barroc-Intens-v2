<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Offertes</h1>


    {{-- Tabel --}}
    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Datum</th>
                    <th>Klant</th>
                    <th>Status</th>
                    <th>Totaal</th>
                    <th class="text-right">Acties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotes as $quote)
                    <tr class="border-b">
                        <td>{{ $quote->created_at->format('d-m-Y') }}</td>
                        <td>{{ $quote->company->name }}</td>
                        <td>
                            @if($quote->is_sent)
                                <span class="text-green-600 font-semibold">Verzonden</span>
                            @else
                                <span class="text-gray-600">Concept</span>
                            @endif
                        </td>
                        <td>€{{ number_format($quote->total_amount,2) }}</td>
                        <td class="text-right">
                            <a href="{{ route('quotes.edit', $quote->id) }}"
                                class="text-blue-600 text-sm">
                                Openen
                            </a>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-center text-gray-500">
                            Geen offertes gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $quotes->links() }}
        </div>
    </div>
</div>
