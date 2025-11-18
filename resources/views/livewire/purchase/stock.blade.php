<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Voorraadbeheer</h1>

    {{-- Voorraad aanvullen --}}
    <div class="bg-white shadow rounded p-4 mb-6">

        <h2 class="text-lg font-semibold mb-3">Voorraad aanvullen</h2>

        <form wire:submit.prevent="add" class="space-y-4">

            <div>
                <label class="text-sm block font-medium">Product</label>
                <select wire:model.defer="product_id" class="w-full border rounded px-2 py-1">
                    <option value="">-- kies product --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('product_id') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="text-sm block font-medium">Aantal</label>
                <input type="number" wire:model.defer="quantity" class="w-full border rounded px-2 py-1">
                @error('quantity') <span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <button class="px-4 py-2 bg-yellow-500 text-white rounded">Toevoegen</button>

        </form>
    </div>


    {{-- Voorraadmutaties --}}
    <div class="bg-white shadow rounded p-4">

        <h2 class="text-lg font-semibold mb-4">Laatste mutaties</h2>

        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Product</th>
                    <th class="py-2">Aantal</th>
                    <th class="py-2">Reden</th>
                    <th class="py-2">Datum</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movements as $m)
                    <tr class="border-b">
                        <td class="py-2">{{ $m->product->name }}</td>
                        <td class="py-2">{{ $m->quantity }}</td>
                        <td class="py-2">{{ ucfirst($m->reason) }}</td>
                        <td class="py-2">{{ $m->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>
