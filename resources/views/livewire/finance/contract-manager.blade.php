<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Contractbeheer</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Beheer contracten en genereer automatisch facturen</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- FORM --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 p-6">

        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            @if ($editingContractId)
                 Contract bewerken
            @else
                 Nieuw contract aanmaken
            @endif
        </h2>

        <form wire:submit.prevent="saveContract" class="space-y-5">

            {{-- Klant --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Klant *</label>
                <select wire:model="company_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- kies klant --</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Startdatum --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Startdatum *</label>
                <input type="date" wire:model="starts_at" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Einddatum --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Einddatum (optioneel)</label>
                <input type="date" wire:model="ends_at" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Facturatiemodel --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Facturatiemodel *</label>
                <select wire:model="invoice_type" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- kies model --</option>
                    <option value="monthly"> Maandelijks</option>
                    <option value="periodic"> Periodiek</option>
                </select>
            </div>

            {{-- Interval --}}
            @if($invoice_type === 'periodic')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interval (maanden) *</label>
                    <input type="number" wire:model="periodic_interval_months"
                           min="1" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                </div>
            @endif

            {{-- Regels --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/20">

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> Contractregels</h3>

                @foreach($rulesData as $i => $rule)
                    <div class="border border-gray-200 dark:border-gray-600 p-4 mb-3 rounded-lg bg-white dark:bg-gray-800">

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product *</label>
                        <select wire:model="rulesData.{{ $i }}.product_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 mb-3 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- kies product --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Aantal *</label>
                        <input type="number" min="1"
                               wire:model="rulesData.{{ $i }}.quantity"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 mb-3 focus:ring-2 focus:ring-blue-500">

                        <button type="button"
                                wire:click="removeRule({{ $i }})"
                                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Verwijderen
                        </button>

                    </div>
                @endforeach

                <button type="button"
                        wire:click="addRule"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Regel toevoegen
                </button>

            </div>

            <div class="flex justify-end gap-3 pt-4">
                @if($editingContractId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Annuleren
                    </button>
                @endif
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @if ($editingContractId)
                        Contract Opslaan
                    @else
                        Contract Aanmaken
                    @endif
                </button>
            </div>

        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">📑 Alle contracten</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Klant</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Facturatie</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Start</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Einde</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase text-right">Acties</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $contract->company->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($contract->invoice_type === 'monthly')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    📅 Maandelijks
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                    🔄 Periodiek ({{ $contract->periodic_interval_months }}m)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($contract->starts_at)->format('d-m-Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $contract->ends_at ? \Carbon\Carbon::parse($contract->ends_at)->format('d-m-Y') : '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="generateInvoice({{ $contract->id }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-medium text-white bg-green-600 hover:bg-green-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Factuur
                                </button>

                                <button wire:click="editContract({{ $contract->id }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Bewerk
                                </button>

                                <button wire:click="deleteContract({{ $contract->id }})"
                                        onclick="return confirm('Weet je zeker dat je dit contract wilt verwijderen?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-600 rounded-md text-xs font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Verwijder
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nog geen contracten aangemaakt</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>
