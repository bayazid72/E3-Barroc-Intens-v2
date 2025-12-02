<div class="max-w-5xl mx-auto py-6">

    <h1 class="text-2xl font-bold mb-6">Mijn Monteurs Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="{{ route('maintenance.visits.day') }}"
           class="p-6 rounded bg-purple-100 hover:bg-purple-200 shadow">
            <h2 class="font-bold">📆 Mijn bezoeken (dag)</h2>
            <p class="text-neutral-600 mt-2">Overzicht van vandaag.</p>
        </a>

        <a href="{{ route('maintenance.visits.week') }}"
           class="p-6 rounded bg-indigo-100 hover:bg-indigo-200 shadow">
            <h2 class="font-bold">🗓️ Mijn bezoeken (week)</h2>
            <p class="text-neutral-600 mt-2">Weekplanning.</p>
        </a>

        <a href="{{ route('maintenance.workorders') }}"
           class="p-6 rounded bg-green-100 hover:bg-green-200 shadow">
            <h2 class="font-bold">📘 Werkbonnen</h2>
            <p class="text-neutral-600 mt-2">Bekijk of vul werkbonnen in.</p>
        </a>

    </div>

</div>
