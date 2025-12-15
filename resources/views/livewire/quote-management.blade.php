<div class="max-w-5xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">
        @if($quote && !$editMode)
            Offerte bekijken
        @elseif($quote && $editMode)
            Offerte aanpassen
        @else
            Nieuwe offerte
        @endif
    </h1>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Nieuwe offerte --}}
    @if(!$quote)
        <div class="bg-white p-4 rounded shadow mb-6">
            <label class="block text-sm font-medium mb-1">Klant</label>

            <select wire:model="company_id" class="w-full border rounded px-3 py-2">
                <option value="">-- kies klant --</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>

            <button type="button"
                    wire:click="createQuote"
                    class="mt-4 px-6 py-2 bg-yellow-500 text-white rounded">
                Start offerte
            </button>
        </div>
    @endif

    {{-- View mode: knop Offerte aanpassen --}}
    @if($quote && !$editMode)
        <div class="mb-4">
            <button type="button"
                    wire:click="enableEdit"
                    class="px-6 py-2 bg-blue-600 text-white rounded">
                Offerte aanpassen
            </button>
        </div>
    @endif

    {{-- Edit mode: product toevoegen --}}
    @if($quote && $editMode)
        <div class="bg-white p-4 rounded shadow mb-6">
            <h2 class="font-semibold mb-3">Product toevoegen</h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select wire:model="product_id" class="border rounded px-2 py-2 md:col-span-2">
                    <option value="">-- product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }} (€{{ number_format($product->price,2) }})
                        </option>
                    @endforeach
                </select>

                <input type="number" wire:model="amount" min="1" class="border rounded px-2 py-2">

                <button type="button" wire:click="addLine"
                        class="bg-green-600 text-white rounded px-4 py-2">
                    Toevoegen
                </button>
            </div>
        </div>
    @endif

    {{-- Overzicht --}}
    @if($quote)
        <div class="bg-white p-4 rounded shadow">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th>Product</th>
                        <th>Aantal</th>
                        <th>Prijs</th>
                        <th>Totaal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $line)
                        <tr class="border-b">
                            <td>{{ $line->product->name }}</td>
                            <td>{{ $line->amount }}</td>
                            <td>€{{ number_format($line->price_snapshot,2) }}</td>
                            <td>€{{ number_format($line->amount * $line->price_snapshot,2) }}</td>
                            <td class="text-right">
                                @if($editMode)
                                    <button type="button"
                                            wire:click="removeLine({{ $line->id }})"
                                            class="text-red-600 text-xs">
                                        Verwijderen
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-between items-center mt-4">
                <div class="font-bold text-lg">
                    Totaal: €{{ number_format($quote->total_amount,2) }}
                </div>

                @if($editMode && !$quote->is_sent)
                    <button type="button"
                            wire:click="sendQuote"
                            class="px-6 py-2 bg-green-600 text-white rounded">
                        Offerte verzenden
                    </button>
                @elseif($quote->is_sent)
                    <span class="text-green-700 font-semibold">Offerte verzonden</span>
                @endif
            </div>
        </div>
    @endif
</div>
