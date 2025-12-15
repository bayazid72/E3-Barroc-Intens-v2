@can('sales-access')
<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Sales Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Bedrijven --}}
        <a href="{{ route('companies.index') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Bedrijven</h2>
            <p class="text-gray-600 mt-2">
                Bekijk en beheer klanten en contactgegevens.
            </p>
        </a>

        {{-- Notities --}}
        <a href="{{ route('notes.index') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Notities</h2>
            <p class="text-gray-600 mt-2">
                Bekijk en beheer klantnotities.
            </p>
        </a>

        {{-- NIEUW: Offerte maken --}}
        <a href="{{ route('quotes.create') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Nieuwe offerte</h2>
            <p class="text-gray-600 mt-2">
                Stel ter plekke een offerte samen voor een klant.
            </p>
        </a>

        <a href="{{ route('quotes.index') }}"
            class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Offertes</h2>
            <p class="text-gray-600 mt-2">
                Bekijk verzonden en conceptoffertes.
            </p>
        </a>


    </div>
</div>
@endcan
