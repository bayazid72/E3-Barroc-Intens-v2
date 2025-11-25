<x-layouts.app :title="__('Login Logs')">
<div>
    <h1 class="text-xl font-semibold mb-4">Login logs</h1>

    <table class="min-w-full border text-sm">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="py-2 px-3 text-left">Tijd</th>
                <th class="py-2 px-3 text-left">Email</th>
                <th class="py-2 px-3 text-left">Naam</th>
                <th class="py-2 px-3 text-left">IP</th>
                <th class="py-2 px-3 text-left">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr class="border-b">
                    <td class="py-2 px-3">{{ $log->created_at }}</td>
                    <td class="py-2 px-3">{{ $log->email }}</td>
                    <td class="py-2 px-3">
                        {{ optional($log->user)->name ?? 'Onbekend' }}
                    </td>
                    <td class="py-2 px-3">{{ $log->ip_address }}</td>
                    <td class="py-2 px-3">
                        @if($log->blocked)
                            <span class="text-red-600 font-semibold">Geblokkeerd</span>
                        @elseif($log->successful)
                            <span class="text-green-600 font-semibold">Succesvol</span>
                        @else
                            <span class="text-orange-500 font-semibold">Mislukt</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
</x-layouts.app>
