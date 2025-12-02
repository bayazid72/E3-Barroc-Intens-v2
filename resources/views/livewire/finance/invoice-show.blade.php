<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start print:hidden mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Factuur Details</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $invoice->invoice_number ?? 'Factuur #' . $invoice->id }}
            </p>
        </div>

        <div class="flex gap-2">
            <a 
                href="{{ route('finance.invoices') }}" 
                wire:navigate
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Terug
            </a>
            
            <button 
                onclick="window.print()"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            
            <button 
                wire:click="downloadPdf"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </button>
        </div>
    </div>

    <!-- Professional Invoice Layout (Print-Ready) -->
    <div class="bg-white rounded-lg shadow-lg p-8 print:shadow-none" id="invoice-content">
        <!-- Invoice Header with Company Info -->
        <div class="flex justify-between items-start mb-8 pb-6 border-b-2 border-zinc-200">
            <div class="flex-1">
                <div class="text-3xl font-bold text-blue-600 mb-2">FACTUUR</div>
                <div class="text-sm text-zinc-600">
                    <div class="font-semibold text-lg text-zinc-800 mb-2">Barroc Intens</div>
                    <div>Lovensdijkstraat 61-63</div>
                    <div>4818 AJ Breda</div>
                    <div>Nederland</div>
                    <div class="mt-2">
                        <div>KVK: 69599068</div>
                        <div>BTW: NL857548888B01</div>
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-xs text-zinc-500 uppercase">Factuurnummer</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $invoice->invoice_number }}</div>
                    <div class="text-xs text-zinc-500 mt-3">Factuurdatum</div>
                    <div class="font-semibold">{{ $invoice->invoice_date->format('d-m-Y') }}</div>
                    @if($invoice->paid_at)
                        <div class="mt-2 text-green-600 font-semibold text-sm">
                            <flux:icon.check-circle class="size-4 inline" />
                            BETAALD
                        </div>
                    @else
                        <div class="text-xs text-zinc-500 mt-3">Vervaldatum</div>
                        <div class="font-semibold text-orange-600">{{ $invoice->invoice_date->addDays(30)->format('d-m-Y') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="mb-8">
            <div class="text-xs text-zinc-500 uppercase font-semibold mb-2">Factuuradres</div>
            <div class="bg-zinc-50 p-4 rounded-lg">
                <div class="font-bold text-lg text-zinc-800">{{ $invoice->company->name }}</div>
                <div class="text-zinc-600 mt-1">
                    @if($invoice->company->address)
                        <div>{{ $invoice->company->address }}</div>
                    @endif
                    @if($invoice->company->postal_code || $invoice->company->city)
                        <div>{{ $invoice->company->postal_code }} {{ $invoice->company->city }}</div>
                    @endif
                    @if($invoice->company->country_code)
                        <div>{{ $invoice->company->country_code }}</div>
                    @endif
                    @if($invoice->company->email)
                        <div class="mt-2">Email: {{ $invoice->company->email }}</div>
                    @endif
                    @if($invoice->company->phone)
                        <div>Tel: {{ $invoice->company->phone }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Items Table -->
        <div class="mb-8">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="text-left py-3 px-4">Omschrijving</th>
                        <th class="text-center py-3 px-4 w-24">Aantal</th>
                        <th class="text-right py-3 px-4 w-32">Prijs p/st</th>
                        <th class="text-right py-3 px-4 w-32">Totaal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->lines as $index => $line)
                        <tr class="{{ $index % 2 === 0 ? 'bg-zinc-50' : 'bg-white' }}">
                            <td class="py-3 px-4">
                                <div class="font-semibold text-zinc-800">{{ $line->product->name }}</div>
                                @if($line->product->description)
                                    <div class="text-sm text-zinc-500 mt-1">{{ $line->product->description }}</div>
                                @endif
                            </td>
                            <td class="text-center py-3 px-4 text-zinc-700">{{ $line->amount }}</td>
                            <td class="text-right py-3 px-4 text-zinc-700">€ {{ number_format($line->price_snapshot, 2, ',', '.') }}</td>
                            <td class="text-right py-3 px-4 font-semibold text-zinc-800">
                                € {{ number_format($line->amount * $line->price_snapshot, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="flex justify-end mb-8">
            <div class="w-80">
                <div class="space-y-2 mb-3">
                    <div class="flex justify-between text-zinc-600">
                        <span>Subtotaal</span>
                        <span>€ {{ number_format($invoice->total_amount / 1.21, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-600">
                        <span>BTW (21%)</span>
                        <span>€ {{ number_format($invoice->total_amount - ($invoice->total_amount / 1.21), 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="border-t-2 border-zinc-300 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-zinc-800">Totaal</span>
                        <span class="text-2xl font-bold text-blue-600">€ {{ number_format($invoice->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="border-t-2 border-zinc-200 pt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm font-semibold text-zinc-700 mb-2">Betaalinformatie</div>
                    <div class="text-sm text-zinc-600 space-y-1">
                        <div><strong>IBAN:</strong> NL91 ABNA 0417 1643 00</div>
                        <div><strong>BIC:</strong> ABNANL2A</div>
                        <div><strong>T.n.v.:</strong> Barroc Intens B.V.</div>
                        <div class="mt-2">
                            <strong>Onder vermelding van:</strong> {{ $invoice->invoice_number }}
                        </div>
                    </div>
                </div>
                
                @if($invoice->paid_at)
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-sm font-semibold text-green-800 mb-2">
                            <flux:icon.check-circle class="size-5 inline" />
                            Status: BETAALD
                        </div>
                        <div class="text-sm text-green-700 space-y-1">
                            <div><strong>Betaald op:</strong> {{ $invoice->paid_at->format('d-m-Y H:i') }}</div>
                            @if($invoice->payment_method)
                                <div><strong>Methode:</strong> {{ $invoice->payment_method }}</div>
                            @endif
                            @if($invoice->payment_reference)
                                <div><strong>Referentie:</strong> {{ $invoice->payment_reference }}</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <div class="text-sm font-semibold text-orange-800 mb-2">
                            <flux:icon.clock class="size-5 inline" />
                            Status: OPENSTAAND
                        </div>
                        <div class="text-sm text-orange-700">
                            <div><strong>Vervaldatum:</strong> {{ $invoice->invoice_date->addDays(30)->format('d-m-Y') }}</div>
                            <div class="mt-2">Gelieve binnen 30 dagen te betalen.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="mt-8 pt-6 border-t border-zinc-200 text-xs text-zinc-500 text-center">
            <p>Bedankt voor uw vertrouwen in Barroc Intens!</p>
            <p class="mt-1">Voor vragen over deze factuur kunt u contact opnemen via finance@barrocintens.nl of +31 76 523 4567</p>
        </div>
    </div>

    <!-- Administrative Information (Non-Printable) -->
    <div class="print:hidden space-y-6">
        <hr class="my-8 border-gray-200 dark:border-gray-700" />
        
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Administratieve Informatie</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status Overzicht</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Betaalstatus</div>
                        @php
                            $status = $invoice->payment_status;
                            $colors = [
                                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'open' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            ];
                            $labels = [
                                'paid' => 'Betaald',
                                'open' => 'Open',
                                'overdue' => 'Te Laat',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 {{ $colors[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $labels[$status] ?? ucfirst($status) }}
                        </span>
                    </div>
                    @if($invoice->contract_id)
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Contract</div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">Gekoppeld</div>
                        </div>
                    @endif
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Aangemaakt</div>
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $invoice->created_at->format('d-m-Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Betaling Tracering</h3>
                </div>
                <div class="p-6 space-y-2">
                    @if($invoice->paid_at)
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Betaald op</div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->paid_at->format('d-m-Y H:i') }}</div>
                        </div>
                        @if($invoice->payment_method)
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Methode</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->payment_method }}</div>
                            </div>
                        @endif
                        @if($invoice->payment_reference)
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Referentie</div>
                                <div class="font-mono text-sm text-gray-900 dark:text-gray-100">{{ $invoice->payment_reference }}</div>
                            </div>
                        @endif
                    @else
                        <div class="text-orange-600 dark:text-orange-400">
                            <div class="text-xs">Nog niet betaald</div>
                            <div class="font-semibold">Vervalt: {{ $invoice->invoice_date->addDays(30)->format('d-m-Y') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Acties</h3>
                </div>
                <div class="p-6 space-y-2">
                    @if(!$invoice->isPaid())
                        <button 
                            wire:click="markAsPaid"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Markeer als betaald
                        </button>
                    @endif
                    <button 
                        wire:click="sendEmail"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email naar klant
                    </button>
                    <button 
                        wire:click="duplicateInvoice"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Dupliceer factuur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #invoice-content, #invoice-content * {
                visibility: visible;
            }
            #invoice-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print\\:hidden {
                display: none !important;
            }
        }
    </style>
</div>
