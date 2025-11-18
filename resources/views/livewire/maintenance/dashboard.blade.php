<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Maintenance Dashboard</h1>

    {{-- INFO meldingen --}}
    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tegels --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Storingen --}}
        <a href="{{ route('maintenance.malfunctions') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Storingen</h2>
            <p class="text-gray-600 mt-2">Ingekomen storingen bekijken en opvolgen.</p>
        </a>

        {{-- Planning --}}
        <a href="{{ route('maintenance.planning') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Planning</h2>
            <p class="text-gray-600 mt-2">Bekijk je ingeplande afspraken in een kalender.</p>
        </a>

        {{-- Werkbonnen --}}
        <a href="{{ route('maintenance.workorders') }}"
           class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition block">
            <h2 class="text-xl font-semibold">Werkbonnen</h2>
            <p class="text-gray-600 mt-2">Bekijk en vul werkbonnen in voor je bezoeken.</p>
        </a>

    </div>

</div>
