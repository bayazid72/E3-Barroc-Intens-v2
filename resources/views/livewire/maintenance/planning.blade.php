<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">Planning</h1>

    {{-- Datum + nieuwe afspraak --}}
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Datum</label>

        <div class="flex items-center justify-between gap-2">
            <input
                type="date"
                wire:model.live="selectedDate"
                class="border rounded px-2 py-1"
            >

            @can('maintenance-appointments')
                <a href="{{ route('maintenance.create') }}"
                class="px-3 py-2 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                    ➕ Nieuwe afspraak maken
                </a>
            @endcan

        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-2">Tijd</th>
                    <th>Klant</th>
                    <th>Type</th>
                    <th>Monteur</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $a)
                    <tr class="border-b
                        {{ $a->isSick() ? 'bg-red-50 text-red-800' : '' }}
                    ">
                        {{-- Tijd --}}
                        <td class="py-2">
                            {{ $a->date_planned?->format('H:i') ?? '-' }}
                        </td>

                        {{-- Klant --}}
                        <td>
                            {{ $a->company->name }}
                        </td>

                        {{-- Type --}}
                        <td>
                            {{ ucfirst($a->type) }}
                        </td>

                        {{-- Monteur --}}
                        <td class="{{ $a->technician ? '' : 'text-gray-400 italic' }}">
                            {{ $a->technician?->name ?? 'Niet toegewezen' }}
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($a->status === 'sick')
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">
                                    Ziek
                                </span>
                            @elseif($a->status === 'planned')
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-700">
                                    Gepland
                                </span>
                            @elseif($a->status === 'done')
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">
                                    Afgerond
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700">
                                    {{ ucfirst($a->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3 text-gray-500">
                            Geen afspraken
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <hr class="my-8">

    {{-- Kalender --}}
    <h2 class="text-2xl font-bold mb-6">Planning kalender</h2>

    <div id="planning-component" wire:ignore>
        <div id="calendar"></div>
    </div>

    <script>
        function initPlanningCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl || calendarEl.dataset.fcInitialized === '1') return;

            calendarEl.dataset.fcInitialized = '1';

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },

                buttonText: {
                    today: 'Vandaag',
                    month: 'Maand',
                    week: 'Week',
                    day: 'Dag',
                    list: 'Lijst'
                },

                events(fetchInfo, successCallback, failureCallback) {
                    @this.call('getEvents')
                        .then(events => successCallback(events))
                        .catch(err => failureCallback(err));
                },

                eventClick(info) {
                    window.location.href = "/maintenance/view/" + info.event.id;
                }
            });

            calendar.render();
        }

        document.addEventListener('livewire:initialized', initPlanningCalendar);
        document.addEventListener('livewire:navigated', initPlanningCalendar);
    </script>

</div>
