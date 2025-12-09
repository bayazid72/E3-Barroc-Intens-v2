<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <flux:heading size="xl">Betaalde Facturen - Actie Vereist</flux:heading>
        <flux:badge color="blue" size="lg">
            {{ $backorderLines->count() }} producten wachten op levering
        </flux:badge>
    </div>

    <!-- Filters -->
    <div class="flex gap-4">
        <flux:input 
            wire:model.live.debounce.300ms="search" 
            placeholder="Zoek op klant of factuurnummer..."
            icon="magnifying-glass"
            class="flex-1"
        />
    </div>

    <!-- Backorder Alert -->
    @if($backorderLines->count() > 0)
        <flux:card class="bg-red-50 dark:bg-red-900/20">
            <flux:card.header>
                <div class="flex items-center gap-3">
                    <flux:icon.exclamation-triangle class="size-6 text-red-600 dark:text-red-400" />
                    <flux:heading size="lg" class="text-red-800 dark:text-red-300">
                        Producten wachten op bestelling/levering
                    </flux:heading>
                </div>
            </flux:card.header>
            <flux:card.body>
                <flux:table>
                    <flux:columns>
                        <flux:column>Factuur</flux:column>
                        <flux:column>Klant</flux:column>
                        <flux:column>Product</flux:column>
                        <flux:column>Aantal</flux:column>
                        <flux:column>Betaald op</flux:column>
                        <flux:column>Actie</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($backorderLines as $line)
                            <flux:row wire:key="backorder-{{ $line->id }}">
                                <flux:cell>
                                    <span class="font-mono text-sm">{{ $line->invoice->invoice_number }}</span>
                                </flux:cell>
                                <flux:cell>
                                    {{ $line->invoice->company->name }}
                                </flux:cell>
                                <flux:cell>
                                    <div class="font-medium">{{ $line->product->name }}</div>
                                </flux:cell>
                                <flux:cell class="font-semibold">{{ $line->amount }}x</flux:cell>
                                <flux:cell>
                                    {{ $line->invoice->paid_at->format('d-m-Y') }}
                                </flux:cell>
                                <flux:cell>
                                    <flux:button 
                                        size="sm" 
                                        variant="primary"
                                        wire:navigate
                                        href="{{ route('purchase.paid-invoices.show', $line->invoice->id) }}"
                                    >
                                        Bekijk
                                    </flux:button>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </flux:card.body>
        </flux:card>
    @endif

    <!-- Paid Invoices List -->
    <flux:card>
        <flux:card.header>
            <flux:heading size="lg">Alle Betaalde Facturen</flux:heading>
        </flux:card.header>
        <flux:card.body>
            <flux:table>
                <flux:columns>
                    <flux:column>Factuur #</flux:column>
                    <flux:column>Klant</flux:column>
                    <flux:column>Bedrag</flux:column>
                    <flux:column>Betaald op</flux:column>
                    <flux:column>Leveringstatus</flux:column>
                    <flux:column>Acties</flux:column>
                </flux:columns>

                <flux:rows>
                    @forelse($paidInvoices as $invoice)
                        <flux:row wire:key="invoice-{{ $invoice->id }}">
                            <flux:cell>
                                <span class="font-mono font-medium">{{ $invoice->invoice_number }}</span>
                            </flux:cell>

                            <flux:cell>
                                <div class="font-medium">{{ $invoice->company->name }}</div>
                            </flux:cell>

                            <flux:cell class="font-semibold text-green-600">
                                € {{ number_format($invoice->total_amount, 2, ',', '.') }}
                            </flux:cell>

                            <flux:cell>
                                <div>{{ $invoice->paid_at->format('d-m-Y') }}</div>
                                <div class="text-xs text-zinc-500">{{ $invoice->paid_at->diffForHumans() }}</div>
                            </flux:cell>

                            <flux:cell>
                                @php
                                    $deliveredCount = $invoice->lines->where('delivery_status', 'delivered')->count();
                                    $totalCount = $invoice->lines->count();
                                    $allDelivered = $deliveredCount === $totalCount;
                                @endphp

                                @if($allDelivered)
                                    <flux:badge color="green" size="sm">
                                        <flux:icon.check class="size-4" />
                                        Alles geleverd
                                    </flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">
                                        <flux:icon.exclamation-triangle class="size-4" />
                                        {{ $deliveredCount }}/{{ $totalCount }} geleverd
                                    </flux:badge>
                                @endif
                            </flux:cell>

                            <flux:cell>
                                <flux:button 
                                    size="sm" 
                                    variant="ghost"
                                    wire:navigate
                                    href="{{ route('purchase.paid-invoices.show', $invoice->id) }}"
                                >
                                    Details
                                </flux:button>
                            </flux:cell>
                        </flux:row>
                    @empty
                        <flux:row>
                            <flux:cell colspan="6" class="text-center py-8 text-zinc-500">
                                Geen betaalde facturen gevonden
                            </flux:cell>
                        </flux:row>
                    @endforelse
                </flux:rows>
            </flux:table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $paidInvoices->links() }}
            </div>
        </flux:card.body>
    </flux:card>
</div>
