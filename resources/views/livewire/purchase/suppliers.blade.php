<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Leveranciers</h1>

    {{-- Leveranciers overzicht --}}
    <div class="bg-white shadow rounded p-4">

        <h2 class="text-lg font-semibold mb-4">Lijst van leveranciers</h2>

        @if (count($suppliers) === 0)
            <p class="text-neutral-500">Geen leveranciers gevonden</p>
        @else
            <ul class="space-y-2">
                @foreach ($suppliers as $s)
                    <li class="p-3 border rounded bg-neutral-50">
                        <strong>{{ $s }}</strong>

                        <ul class="ml-4 mt-2 list-disc text-sm">
                            @foreach ($products->filter(fn($p) => str_contains($p->description, $s)) as $p)
                                <li>{{ $p->name }}</li>
                            @endforeach
                        </ul>

                    </li>
                @endforeach
            </ul>
        @endif

    </div>

</div>
