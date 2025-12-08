<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Gebruikersbeheer</h1>

    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-2 bg-red-100 border border-red-400 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulier voor create / update --}}
    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3">
            @if ($userIdBeingEdited)
                Gebruiker bewerken
            @else
                Nieuwe gebruiker aanmaken
            @endif
        </h2>

        <form wire:submit.prevent="{{ $userIdBeingEdited ? 'updateUser' : 'createUser' }}" class="space-y-3">

            <div>
                <label class="block text-sm font-medium mb-1">Naam</label>
                <input type="text" wire:model.defer="name" class="w-full border rounded px-2 py-1">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">E-mail</label>
                <input type="email" wire:model.defer="email" class="w-full border rounded px-2 py-1">
                @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Wachtwoord
                        @if ($userIdBeingEdited)
                            <span class="text-xs text-gray-500">(leeg laten = niet wijzigen)</span>
                        @endif
                    </label>
                    <input type="password" wire:model.defer="password" class="w-full border rounded px-2 py-1">
                    @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Wachtwoord bevestigen</label>
                    <input type="password" wire:model.defer="password_confirmation" class="w-full border rounded px-2 py-1">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Rol</label>
                <select wire:model.defer="role_id" class="w-full border rounded px-2 py-1">
                    <option value="">-- kies een rol --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-3 mt-3">
                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">
                    @if ($userIdBeingEdited)
                        Opslaan
                    @else
                        Aanmaken
                    @endif
                </button>

                @if ($userIdBeingEdited)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 border rounded">
                        Annuleren
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel met gebruikers --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-3">Gebruikerslijst</h2>
            <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-4">

                {{-- Zoekveld --}}
                <input type="text"
                    wire:model.defer="searchInput"
                    placeholder="Zoek op naam of e-mail..."
                    class="w-full md:w-1/3 border rounded px-3 py-2">

                {{-- Rol-filter --}}
                <select wire:model.defer="roleFilterInput"
                        class="w-full md:w-1/4 border rounded px-3 py-2">
                    <option value="">Alle rollen</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>

                <button wire:click="applyFilters"
                        class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    Filter toepassen
                </button>

                {{-- Reset --}}
                <button wire:click="resetFilters"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Reset
                </button>

            </div>



        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Naam</th>
                    <th class="py-2">E-mail</th>
                    <th class="py-2">Rol</th>
                    <th class="py-2 text-right">Acties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b">
                        <td class="py-2">{{ $user->name }}</td>
                        <td class="py-2">{{ $user->email }}</td>
                        <td class="py-2">{{ $user->role?->name }}</td>
                        <td class="py-2 text-right space-x-2">
                            <button wire:click="editUser({{ $user->id }})"
                                    class="px-3 py-1 text-xs bg-blue-500 text-white rounded">
                                Bewerken
                            </button>

                            <button wire:click="confirmDelete({{ $user->id }})"
                                    class="px-3 py-1 text-xs bg-red-500 text-white rounded">
                                Verwijderen
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-3 text-center text-gray-500">
                            Geen gebruikers gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Verwijder bevestiging --}}
    @if ($confirmingUserDeletion)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="bg-white rounded shadow p-4 max-w-sm w-full">
                <p class="mb-4">Weet je zeker dat je deze gebruiker wilt verwijderen?</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('confirmingUserDeletion', null)"
                            class="px-3 py-1 border rounded">
                        Annuleren
                    </button>
                    <button wire:click="deleteUser"
                            class="px-3 py-1 bg-red-600 text-white rounded">
                        Verwijderen
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('scrollToTop', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
