<div class="max-w-6xl mx-auto py-8">

    <h1 class="text-2xl font-bold mb-4">Bedrijvenbeheer</h1>

    @if (session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Formulier --}}
    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-3">
            {{ $companyIdBeingEdited ? 'Bedrijf bewerken' : 'Nieuw bedrijf' }}
        </h2>

        <form wire:submit.prevent="{{ $companyIdBeingEdited ? 'updateCompany' : 'createCompany' }}" class="space-y-3">

            <div>
                <label class="block text-sm font-medium">Naam</label>
                <input type="text" wire:model.defer="name" class="w-full border rounded px-2 py-1">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Telefoon</label>
                    <input type="text" wire:model.defer="phone" class="w-full border rounded px-2 py-1">
                </div>

                <div>
                    <label class="block text-sm font-medium">Landcode</label>
                    <input type="text" wire:model.defer="country_code" class="w-full border rounded px-2 py-1">
                    @error('country_code') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input wire:model.defer="street" placeholder="Straat" class="border rounded px-2 py-1">
                <input wire:model.defer="house_number" placeholder="Huisnummer" class="border rounded px-2 py-1">
                <input wire:model.defer="city" placeholder="Stad" class="border rounded px-2 py-1">
            </div>

            <div>
                <label class="block text-sm font-medium">Contactpersoon</label>
                <select wire:model.defer="contact_id" class="w-full border rounded px-2 py-1">
                    <option value="">-- geen --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model.defer="bkr_checked">
                <label class="text-sm">BKR gecheckt</label>
            </div>

            <div class="flex gap-3">
                <button class="px-4 py-2 bg-yellow-500 text-white rounded">
                    {{ $companyIdBeingEdited ? 'Opslaan' : 'Aanmaken' }}
                </button>

                @if ($companyIdBeingEdited)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 border rounded">
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
                    <th class="py-2">Naam</th>
                    <th>Contact</th>
                    <th>BKR</th>
                    <th class="text-right">Acties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr class="border-b">
                        <td class="py-2">{{ $company->name }}</td>
                        <td>{{ $company->contact?->name ?? '-' }}</td>
                        <td>{{ $company->bkr_checked ? 'Ja' : 'Nee' }}</td>
                        <td class="text-right space-x-2">
                            <button wire:click="editCompany({{ $company->id }})"
                                    class="px-3 py-1 bg-blue-500 text-white rounded text-xs">
                                Bewerken
                            </button>

                            <button wire:click="confirmDelete({{ $company->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded text-xs">
                                Verwijderen
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-3 text-center text-gray-500">
                            Geen bedrijven gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $companies->links() }}
        </div>
    </div>

    {{-- Delete modal --}}
    @if ($confirmingCompanyDeletion)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="bg-white rounded p-4">
                <p class="mb-4">Bedrijf verwijderen?</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('confirmingCompanyDeletion', null)"
                            class="px-3 py-1 border rounded">Annuleren</button>
                    <button wire:click="deleteCompany"
                            class="px-3 py-1 bg-red-600 text-white rounded">Verwijderen</button>
                </div>
            </div>
        </div>
    @endif
</div>
