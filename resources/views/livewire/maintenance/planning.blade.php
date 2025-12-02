<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Planning</h1>

    <div class="mb-4">
        <label class="block mb-1 font-semibold">Datum</label>

        <div class="flex items-center justify-between gap-2">
            <input
                type="date"
                wire:model.live="selectedDate"
                class="border rounded px-2 py-1"
            >

            <a href="{{ route('maintenance.create') }}"
            class="px-3 py-2 bg-yellow-500 text-white rounded text-sm">
                ➕ Nieuwe afspraak maken
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th>Tijd</th>
                    <th>Klant</th>
                    <th>Type</th>
                    <th>Monteur</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $a)
                    <tr class="border-b">
                        <td>{{ $a->date_planned?->format('H:i') ?? '-' }}</td>
                        <td>{{ $a->company->name }}</td>
                        <td>{{ ucfirst($a->type) }}</td>
                        <td>{{ $a->technician?->name ?? 'Niet toegewezen' }}</td>
                        <td>{{ ucfirst($a->status) }}</td>
                        <td>

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-3">Geen afspraken</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <hr class="my-6">

    {{-- FullCalendar wrapper --}}
    <h1 class="text-2xl font-bold mb-6">Planning kalender</h1>
    <div id="planning-component" wire:ignore>
        <div id="calendar"></div>
    </div>
    <script>
    function initPlanningCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        // voorkom dubbele initialisatie
        if (calendarEl.dataset.fcInitialized === '1') return;
        calendarEl.dataset.fcInitialized = '1';

        // We zitten hier in een Livewire view, dus @this is geldig
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',

            events(fetchInfo, successCallback, failureCallback) {
                @this.call('getEvents')
                    .then(events => successCallback(events))
                    .catch(error => {
                        console.error(error);
                        if (failureCallback) failureCallback(error);
                    });
            },

            eventClick(info) {
                window.location.href = "/maintenance/view/" + info.event.id;
            }
        });

        calendar.render();
    }

    // Livewire 3 events
    document.addEventListener('livewire:initialized', initPlanningCalendar);
    document.addEventListener('livewire:navigated', initPlanningCalendar);
</script>


</div>
