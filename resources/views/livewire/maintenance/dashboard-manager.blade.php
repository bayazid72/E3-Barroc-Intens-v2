@can('access-maintenance')
<div class="max-w-5xl mx-auto py-6">

    <h1 class="text-2xl font-bold mb-6">Maintenance Dashboard</h1>

    {{-- Top statistieken --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="p-6 bg-white shadow rounded text-center">
            <div class="text-3xl font-bold text-red-500">{{ $openstoring }}</div>
            <div class="text-neutral-600">Open storingen</div>
        </div>

        <div class="p-6 bg-white shadow rounded text-center">
            <div class="text-3xl font-bold text-blue-500">{{ $plannedToday }}</div>
            <div class="text-neutral-600">Afspraken vandaag</div>
        </div>

        <div class="p-6 bg-white shadow rounded text-center">
            <div class="text-3xl font-bold text-green-500">{{ $finishedWorkorders }}</div>
            <div class="text-neutral-600">Afgeronde werkbonnen</div>
        </div>

    </div>

    {{-- Navigatiekaarten --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">

        <a href="{{ route('maintenance.malfunctions') }}"
           class="p-6 rounded bg-yellow-100 hover:bg-yellow-200 shadow">
            <h2 class="font-bold">🚨 Storingen</h2>
            <p class="text-neutral-600 mt-2">Ingekomen storingen bekijken en opvolgen.</p>
        </a>

        <a href="{{ route('maintenance.notifications') }}"
            class="p-6 rounded bg-blue-100 hover:bg-blue-200 shadow">
                <h2 class="font-bold">🔔 Notificaties</h2>
                <p class="text-neutral-600 mt-2"> Notificaties beheren</p>
        </a>


        <a href="{{ route('maintenance.workorders') }}"
           class="p-6 rounded bg-green-100 hover:bg-green-200 shadow">
            <h2 class="font-bold">📘 Werkbonnen</h2>
            <p class="text-neutral-600 mt-2">Werkbonnen invullen en controleren.</p>
        </a>
</a>


    </div>

    {{-- Alleen zichtbaar voor MaintenanceManager + Manager --}}
    @can('maintenance-manager')
        <div class="p-6 bg-white shadow rounded border hover:border-yellow-500 transition mt-8">
            <h2 class="text-xl font-semibold mb-2">📝 Afspraken beheren</h2>
            <p class="text-gray-600 mb-3">Nieuwe afspraken aanmaken of bestaande afspraken aanpassen.</p>

            <div class="flex gap-3 mt-3">
                <a href="{{ route('maintenance.create') }}"
                   class="px-3 py-2 bg-yellow-500 text-white rounded text-sm">
                    ➕ Nieuwe afspraak
                </a>

                <a href="{{ route('maintenance.planning') }}"
                   class="px-3 py-2 bg-blue-500 text-white rounded text-sm">
                    📅 Bekijk planning
                </a>
            </div>
        </div>
    @endcan

</div>
@endcan
