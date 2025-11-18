<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Inkoop Dashboard</h1>

    {{-- INFO meldingen --}}
    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- 3 hoofd tegels --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Productbeheer --}}
        <a href="{{ route('purchase.products') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Productbeheer</h2>
            <p class="text-gray-600 mt-2">Producten toevoegen, wijzigen en categoriseren.</p>
        </a>

        {{-- Voorraad --}}
        <a href="{{ route('purchase.stock') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Voorraadbeheer</h2>
            <p class="text-gray-600 mt-2">Voorraad aanvullen en voorraadmutaties bekijken.</p>
        </a>

        {{-- Leveranciers --}}
        <a href="{{ route('purchase.suppliers') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Leveranciers</h2>
            <p class="text-gray-600 mt-2">Bekijk producten per leverancier.</p>
        </a>

    </div>

</div>
