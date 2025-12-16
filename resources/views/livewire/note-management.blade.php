<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-4">Notities</h1>

    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form --}}
    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3">
            {{ $noteIdBeingEdited ? 'Notitie bewerken' : 'Nieuwe notitie' }}
        </h2>

        <form wire:submit.prevent="{{ $noteIdBeingEdited ? 'updateNote' : 'createNote' }}" class="space-y-3">

            {{-- Tekst --}}
            <div>
                <textarea wire:model.defer="note"
                          class="w-full border rounded px-3 py-2"
                          placeholder="Notitie..."></textarea>
                @error('note') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-sm font-medium">Type</label>
                <select wire:model.defer="type" class="w-full border rounded px-2 py-1">
                    <option value="note">Notitie</option>
                    <option value="afspraak">Afspraak</option>
                    <option value="klacht">Klacht</option>
                </select>
                @error('type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Follow-up datum --}}
            @if($type === 'afspraak')
                <div>
                    <label class="block text-sm font-medium">Afspraakdatum</label>
                    <input type="datetime-local"
                           wire:model.defer="follow_up_at"
                           class="w-full border rounded px-2 py-1">
                    @error('follow_up_at') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Bedrijf --}}
            <div>
                <label class="block text-sm font-medium">Bedrijf</label>
                <select wire:model.defer="company_id" class="w-full border rounded px-2 py-1">
                    <option value="">-- kies bedrijf --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Acties --}}
            <div class="flex gap-3">
                <button class="px-4 py-2 bg-yellow-500 text-white rounded">
                    {{ $noteIdBeingEdited ? 'Opslaan' : 'Aanmaken' }}
                </button>

                @if ($noteIdBeingEdited)
                    <button type="button"
                            wire:click="resetForm"
                            class="px-4 py-2 border rounded">
                        Annuleren
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Datum</th>
                    <th>Type</th>
                    <th>Bedrijf</th>
                    <th>Auteur</th>
                    <th class="text-right">Acties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                    <tr class="border-b">
                        <td class="py-2">
                            {{ $note->created_at->format('d-m-Y') }}
                            @if($note->follow_up_at)
                                <div class="text-xs text-gray-500">
                                    Afspraak: {{ $note->follow_up_at->format('d-m-Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="capitalize">{{ $note->type }}</td>
                        <td>{{ $note->company->name }}</td>
                        <td>{{ $note->author->name }}</td>
                        <td class="text-right space-x-2">
                            <button wire:click="editNote({{ $note->id }})"
                                    class="px-3 py-1 bg-blue-500 text-white rounded text-xs">
                                Bewerken
                            </button>

                            <button wire:click="confirmDelete({{ $note->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded text-xs">
                                Verwijderen
                            </button>
                            
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-center text-gray-500">
                            Geen notities gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $notes->links() }}
        </div>
    </div>

    {{-- Delete modal --}}
    @if ($confirmingNoteDeletion)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="bg-white rounded p-4">
                <p class="mb-4">Notitie verwijderen?</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('confirmingNoteDeletion', null)"
                            class="px-3 py-1 border rounded">
                        Annuleren
                    </button>
                    <button wire:click="deleteNote"
                            class="px-3 py-1 bg-red-600 text-white rounded">
                        Verwijderen
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
