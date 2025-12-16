<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Inkoop Dashboard</h1>

    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="{{ route('purchase.products') }}" class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Productbeheer</h2>
            <p class="text-gray-600 mt-2">Producten toevoegen, wijzigen en categoriseren.</p>
        </a>

        <a href="{{ route('purchase.stock') }}"
            class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Voorraadbeheer</h2>
            <p class="text-gray-600 mt-2">
                Voorraad aanvullen en voorraadstatus controleren.
            </p>
        </a>

        <a href="{{ route('purchase.products.overview') }}"
            class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Voorraadmutaties</h2>
            <p class="text-gray-600 mt-2">
                Voorraadmutaties bekijken, zoeken en filteren.
            </p>
        </a>


        <a href="{{ route('purchase.suppliers') }}" class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Leveranciers</h2>
            <p class="text-gray-600 mt-2">Bekijk alle producten per leverancier.</p>
        </a>

        {{--<a href="{{ route('purchase.paid-invoices') }}" class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Backorder & Leveringen</h2>
            <p class="text-gray-600 mt-2">Inzicht in openstaande leveringen van betaalde facturen.</p>
        </a>--}}



    </div>

    {{-- 🔥 Hier komt de factuurtakenlijst --}}
    <livewire:purchase.invoice-tasks />

</div>
