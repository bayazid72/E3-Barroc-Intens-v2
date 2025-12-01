<x-layouts.app :title="__('Login Logs')">
<div>
    <h1 class="text-xl font-semibold mb-4">Login logs</h1>
    <form method="GET" action="{{ route('admin.login-logs') }}" class="mb-4 flex flex-wrap gap-3 items-center">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Zoek op email of naam..."
        class="border border-gray-300 rounded-md px-3 py-2 text-sm"
    />

    <select
        name="status"
        class="border border-gray-300 rounded-md px-3 py-2 text-sm"
    >
        <option value="">Alle statussen</option>
        <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Succesvol</option>
        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Mislukt</option>
        <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Geblokkeerd</option>
    </select>

    <button
        type="submit"
        class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold px-4 py-2 rounded-md text-sm"
    >
        Filter
    </button>

    @if(request()->has('search') || request()->has('status'))
        <a href="{{ route('admin.login-logs') }}" class="text-sm text-gray-500 underline">
            Reset
        </a>
    @endif
    </form>

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
