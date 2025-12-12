{{-- Voorraadmutaties --}}
    <div class="bg-white shadow rounded p-4">

        <h2 class="text-lg font-semibold mb-4">Laatste mutaties</h2>

            {{--FILTERBALK--------}}
            <div class="p-4 bg-gray-50 border rounded mb-6 flex flex-col md:flex-row gap-3">

                {{-- Zoek op productnaam --}}
                <input
                    type="text"
                    wire:model.defer="search"
                    placeholder="Zoek op productnaam..."
                    class="w-full md:w-1/4 border rounded px-3 py-2"
                >

                {{-- Filter op type --}}
                <select wire:model.defer="filterType" class="w-full md:w-1/4 border rounded px-3 py-2">
                    <option value="">Alle types</option>
                    <option value="purchase">Aankoop</option>
                    <option value="usage">Gebruik</option>
                </select>

                {{-- Filter op user --}}
                <select wire:model.defer="filterUser" class="w-full md:w-1/4 border rounded px-3 py-2">
                    <option value="">Alle gebruikers</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>

                {{-- Datum van-tot --}}
                <div class="flex gap-2 w-full md:w-1/4">
                    <input
                        type="date"
                        wire:model.defer="dateFrom"
                        class="w-1/2 border rounded px-3 py-2"
                    >
                    <input
                        type="date"
                        wire:model.defer="dateTo"
                        class="w-1/2 border rounded px-3 py-2"
                    >
                </div>

                <button wire:click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Filter toepassen
                </button>

                <button wire:click="resetFilters" class="px-4 py-2 bg-gray-300 rounded">
                    Reset
                </button>
            </div>



        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Product</th>
                    <th class="py-2">Aantal</th>
                    <th class="py-2">Type</th>
                    <th class="py-2">Ingevoerd door</th>
                    <th class="py-2">Datum</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movements as $m)
                    <tr class="border-b">
                        <td class="py-2">{{ $m->product?->name ?? 'Onbekend product' }}</td>

                        {{-- getSignedQuantityAttribute uit het model --}}
                        <td class="py-2 {{ $m->type === 'usage' ? 'text-red-600' : 'text-green-600' }}">
                            {{ $m->signed_quantity ?? $m->quantity }}
                        </td>

                        <td class="py-2">
                            @if($m->type === 'purchase')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Aankoop</span>
                            @elseif($m->type === 'usage')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Gebruik</span>
                            @elseif($m->type === 'correction')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Correctie</span>
                            @else
                                <span class="px-2 py-1 bg-zinc-100 text-zinc-700 rounded text-xs">{{ $m->type }}</span>
                            @endif
                        </td>

                        <td class="py-2">{{ $m->user?->name ?? '-' }}</td>
                        <td class="py-2">{{ $m->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
