<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <flux:heading size="xl">Factuur Details</flux:heading>
            <flux:subheading>
                {{ $invoice->invoice_number ?? 'Factuur #' . $invoice->id }}
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:button variant="ghost" icon="arrow-left" wire:navigate href="{{ route('finance.invoices') }}">
                Terug
            </flux:button>
            
            <flux:button variant="primary" icon="printer">
                Print
            </flux:button>
        </div>
    </div>

    <!-- Invoice Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Company Info -->
        <flux:card>
            <flux:card.header>
                <flux:heading size="lg">Klantgegevens</flux:heading>
            </flux:card.header>
            <flux:card.body class="space-y-3">
                <div>
                    <div class="text-sm text-zinc-500">Bedrijfsnaam</div>
                    <div class="font-medium">{{ $invoice->company->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Email</div>
                    <div class="font-medium">{{ $invoice->company->email ?? 'Niet beschikbaar' }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Telefoon</div>
                    <div class="font-medium">{{ $invoice->company->phone ?? 'Niet beschikbaar' }}</div>
                </div>
            </flux:card.body>
        </flux:card>

        <!-- Invoice Info -->
        <flux:card>
            <flux:card.header>
                <flux:heading size="lg">Factuur Informatie</flux:heading>
            </flux:card.header>
            <flux:card.body class="space-y-3">
                <div>
                    <div class="text-sm text-zinc-500">Factuurdatum</div>
                    <div class="font-medium">{{ $invoice->invoice_date->format('d-m-Y') }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Betaalstatus</div>
                    <div>
                        <flux:badge :color="$invoice->status_color" size="lg">
                            @if($invoice->payment_status === 'paid')
                                <flux:icon.check-circle class="size-4" />
                            @elseif($invoice->payment_status === 'overdue')
                                <flux:icon.exclamation-triangle class="size-4" />
                            @else
                                <flux:icon.clock class="size-4" />
                            @endif
                            {{ $invoice->status_label }}
                        </flux:badge>
                    </div>
                </div>
                @if($invoice->paid_at)
                    <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded">
                        <div class="text-sm text-zinc-500 mb-2">Betaalinformatie</div>
                        <div class="space-y-2">
                            <div>
                                <div class="text-xs text-zinc-500">Betaald op</div>
                                <div class="font-medium">{{ $invoice->paid_at->format('d-m-Y H:i') }}</div>
                                <div class="text-xs text-zinc-500">{{ $invoice->paid_at->diffForHumans() }}</div>
                            </div>
                            @if($invoice->payment_method)
                                <div>
                                    <div class="text-xs text-zinc-500">Methode</div>
                                    <div class="font-medium">{{ $invoice->payment_method }}</div>
                                </div>
                            @endif
                            @if($invoice->payment_reference)
                                <div>
                                    <div class="text-xs text-zinc-500">Referentie</div>
                                    <div class="font-mono text-sm">{{ $invoice->payment_reference }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <div>
                    <div class="text-sm text-zinc-500">Totaal bedrag</div>
                    <div class="text-2xl font-bold text-green-600">
                        € {{ number_format($invoice->total_amount, 2, ',', '.') }}
                    </div>
                </div>
            </flux:card.body>
        </flux:card>
    </div>

    <!-- Invoice Lines -->
    <flux:card>
        <flux:card.header>
            <flux:heading size="lg">Producten</flux:heading>
        </flux:card.header>
        <flux:card.body>
            <flux:table>
                <flux:columns>
                    <flux:column>Product</flux:column>
                    <flux:column>Aantal</flux:column>
                    <flux:column>Prijs per stuk</flux:column>
                    <flux:column>Subtotaal</flux:column>
                </flux:columns>

                <flux:rows>
                    @foreach($invoice->lines as $line)
                        <flux:row wire:key="line-{{ $line->id }}">
                            <flux:cell>
                                <div class="font-medium">{{ $line->product->name }}</div>
                                @if($line->product->description)
                                    <div class="text-xs text-zinc-500">{{ $line->product->description }}</div>
                                @endif
                            </flux:cell>
                            <flux:cell>{{ $line->amount }}</flux:cell>
                            <flux:cell>€ {{ number_format($line->price_snapshot, 2, ',', '.') }}</flux:cell>
                            <flux:cell class="font-semibold">
                                € {{ number_format($line->amount * $line->price_snapshot, 2, ',', '.') }}
                            </flux:cell>
                        </flux:row>
                    @endforeach

                    <!-- Total Row -->
                    <flux:row>
                        <flux:cell colspan="3" class="text-right font-semibold">Totaal:</flux:cell>
                        <flux:cell class="font-bold text-lg text-green-600">
                            € {{ number_format($invoice->total_amount, 2, ',', '.') }}
                        </flux:cell>
                    </flux:row>
                </flux:rows>
            </flux:table>
        </flux:card.body>
    </flux:card>
</div>
