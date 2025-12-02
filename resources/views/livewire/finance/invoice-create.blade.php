<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nieuwe Factuur Aanmaken</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Maak een nieuwe factuur aan of genereer vanuit een contract</p>
        </div>

        <a 
            href="{{ route('finance.invoices') }}" 
            wire:navigate
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Terug
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-3">Genereer factuur vanuit contract</h3>
        <div class="flex gap-4">
            <div class="flex-1">
                <select 
                    wire:model="contract_id"
                    wire:change="loadContractLines"
                    class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                >
                    <option value="">Selecteer een contract...</option>
                    @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}">
                            {{ $contract->company->name ?? 'Onbekend' }} - Contract #{{ $contract->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button 
                wire:click="loadContractLines"
                @disabled(!$contract_id)
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Laad Contract Regels
            </button>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="createInvoice" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Klant *
                    </label>
                    <select 
                        id="company_id"
                        wire:model="company_id"
                        class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    >
                        <option value="">Selecteer een klant...</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="invoice_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Factuurdatum *
                    </label>
                    <input 
                        type="date" 
                        id="invoice_date"
                        wire:model="invoice_date"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    />
                    @error('invoice_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Line Items -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Factuurregels</h3>
                    <button 
                        type="button"
                        wire:click="addLine"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-medium text-white bg-green-600 hover:bg-green-700"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Regel Toevoegen
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach($lines as $index => $line)
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Product (optioneel)</label>
                                    <select 
                                        wire:model="lines.{{ $index }}.product_id"
                                        wire:change="updatedProductId($event.target.value, {{ $index }})"
                                        class="block w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="">Selecteer...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 md:col-span-4">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Omschrijving *</label>
                                    <input 
                                        type="text" 
                                        wire:model="lines.{{ $index }}.description"
                                        class="block w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="Beschrijving van product/dienst"
                                    />
                                    @error('lines.' . $index . '.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-4 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Aantal *</label>
                                    <input 
                                        type="number" 
                                        wire:model="lines.{{ $index }}.quantity"
                                        min="1"
                                        step="1"
                                        class="block w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    />
                                </div>

                                <div class="col-span-4 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Prijs (€) *</label>
                                    <input 
                                        type="number" 
                                        wire:model="lines.{{ $index }}.unit_price"
                                        min="0"
                                        step="0.01"
                                        class="block w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    />
                                </div>

                                <div class="col-span-4 md:col-span-1 flex items-end">
                                    <button 
                                        type="button"
                                        wire:click="removeLine({{ $index }})"
                                        class="w-full inline-flex items-center justify-center px-3 py-2 border border-red-300 dark:border-red-600 rounded-md text-sm font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-2 text-right">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Subtotaal: </span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    €{{ number_format(($line['quantity'] ?? 0) * ($line['unit_price'] ?? 0), 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Total -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end">
                        <div class="w-64">
                            @php
                                $subtotal = collect($lines)->sum(function($line) {
                                    return ($line['quantity'] ?? 0) * ($line['unit_price'] ?? 0);
                                });
                                $btw = $subtotal * 0.21;
                                $total = $subtotal + $btw;
                            @endphp
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Subtotaal:</span>
                                <span class="text-gray-900 dark:text-white">€{{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600 dark:text-gray-400">BTW (21%):</span>
                                <span class="text-gray-900 dark:text-white">€{{ number_format($btw, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                                <span class="text-gray-900 dark:text-white">Totaal:</span>
                                <span class="text-blue-600 dark:text-blue-400">€{{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a 
                href="{{ route('finance.invoices') }}" 
                wire:navigate
                class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
                Annuleren
            </a>
            <button 
                type="submit"
                class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Factuur Aanmaken
            </button>
        </div>
    </form>
</div>
