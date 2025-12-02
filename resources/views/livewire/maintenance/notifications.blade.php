<div class="max-w-4xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-6">📨 Werkbon Notificaties</h1>

    @foreach($notifications as $n)
        <div class="bg-white shadow rounded p-4 mb-4 border border-neutral-200">

            <h2 class="text-lg font-bold">{{ $n->title }}</h2>

            <p class="mt-2">
                <strong>Technicus:</strong> {{ $n->data->technician }}<br>
                <strong>Klant:</strong> {{ $n->data->company }}<br>
                <strong>Datum bezoek:</strong> {{ \Carbon\Carbon::parse($n->data->date)->format('d-m-Y H:i') }}<br>
                <strong>Probleem:</strong> {{ $n->data->problem }}<br>
                <strong>Oplossing:</strong> {{ $n->data->solution }}
            </p>

            @if(count($n->data->materials))
                <div class="mt-3">
                    <strong>Gebruikte materialen:</strong>
                    <ul class="list-disc pl-6">
                        @foreach($n->data->materials as $mat)
                            <li>{{ $mat->name }} ({{ $mat->quantity }}x)</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('maintenance.workorder.form', $n->data->workorder_id) }}"
               class="mt-3 inline-block px-3 py-1 bg-yellow-500 text-white rounded">
                Bekijk werkbon
            </a>

            <p class="text-xs text-neutral-500 mt-2">
                {{ $n->created_at->format('d-m-Y H:i') }}
            </p>

        </div>
    @endforeach
</div>
