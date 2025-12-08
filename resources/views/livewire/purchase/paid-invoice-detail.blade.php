<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <flux:heading size="xl">Betaalde Factuur Details</flux:heading>
            <flux:subheading>{{ $invoice->invoice_number }}</flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:button variant="ghost" icon="arrow-left" wire:navigate href="{{ route('purchase.paid-invoices.index') }}">
                Terug
            </flux:button>
            
            @if($invoice->lines->where('delivery_status', '!=', 'delivered')->count() > 0)
                <flux:button 
                    wire:click="markAllAsDelivered"
                    wire:confirm="Weet je zeker dat alle producten zijn geleverd?"
                    variant="primary"
                    class="bg-green-600 hover:bg-green-700"
                >
                    <flux:icon.check class="size-5" />
                    Alles geleverd
                </flux:button>
            @endif
        </div>
    </div>

    @if(session()->has('success'))
        <flux:banner variant="success">
            {{ session('success') }}
        </flux:banner>
    @endif>

    <!-- Invoice Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

        <!-- Payment Info -->
        <flux:card class="bg-green-50 dark:bg-green-900/20">
            <flux:card.header>
                <flux:heading size="lg" class="text-green-800 dark:text-green-300">
                    <flux:icon.check-circle class="size-6 inline" />
                    Betaalinformatie
                </flux:heading>
            </flux:card.header>
            <flux:card.body class="space-y-3">
                <div>
                    <div class="text-sm text-zinc-500">Betaald op</div>
                    <div class="font-medium">{{ $invoice->paid_at->format('d-m-Y H:i') }}</div>
                    <div class="text-xs text-zinc-500">{{ $invoice->paid_at->diffForHumans() }}</div>
                </div>
                @if($invoice->payment_method)
                    <div>
                        <div class="text-sm text-zinc-500">Betaalmethode</div>
                        <div class="font-medium">{{ $invoice->payment_method }}</div>
                    </div>
                @endif
                @if($invoice->payment_reference)
                    <div>
                        <div class="text-sm text-zinc-500">Referentie</div>
                        <div class="font-mono text-sm">{{ $invoice->payment_reference }}</div>
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

        <!-- Delivery Status Summary -->
        <flux:card>
            <flux:card.header>
                <flux:heading size="lg">Leveringsstatus</flux:heading>
            </flux:card.header>
            <flux:card.body class="space-y-3">
                @php
                    $deliveredCount = $invoice->lines->where('delivery_status', 'delivered')->count();
                    $partialCount = $invoice->lines->where('delivery_status', 'partially_delivered')->count();
                    $notDeliveredCount = $invoice->lines->where('delivery_status', 'not_delivered')->count();
                    $totalCount = $invoice->lines->count();
                @endphp

                <div>
                    <div class="text-sm text-zinc-500">Geleverd</div>
                    <div class="text-2xl font-bold text-green-600">{{ $deliveredCount }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Deels geleverd</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $partialCount }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Niet geleverd</div>
                    <div class="text-2xl font-bold text-red-600">{{ $notDeliveredCount }}</div>
                </div>
                <div class="pt-3 border-t">
                    <div class="text-sm text-zinc-500">Totaal</div>
                    <div class="text-xl font-bold">{{ $totalCount }} producten</div>
                </div>
            </flux:card.body>
        </flux:card>
    </div>

    <!-- Products / Invoice Lines -->
    <flux:card>
        <flux:card.header>
            <flux:heading size="lg">Producten - Levering bijhouden</flux:heading>
        </flux:card.header>
        <flux:card.body>
            <flux:table>
                <flux:columns>
                    <flux:column>Product</flux:column>
                    <flux:column>Aantal</flux:column>
                    <flux:column>Prijs per stuk</flux:column>
                    <flux:column>Subtotaal</flux:column>
                    <flux:column>Leveringsstatus</flux:column>
                    <flux:column>Leverdatum</flux:column>
                    <flux:column>Acties</flux:column>
                </flux:columns>

                <flux:rows>
                    @foreach($invoice->lines as $line)
                        <flux:row wire:key="line-{{ $line->id }}">
                            <flux:cell>
                                <div class="font-medium">{{ $line->product->name }}</div>
                            </flux:cell>
                            <flux:cell class="font-semibold">{{ $line->amount }}</flux:cell>
                            <flux:cell>€ {{ number_format($line->price_snapshot, 2, ',', '.') }}</flux:cell>
                            <flux:cell class="font-semibold">
                                € {{ number_format($line->amount * $line->price_snapshot, 2, ',', '.') }}
                            </flux:cell>
                            <flux:cell>
                                <flux:badge :color="$line->delivery_status_color" size="sm">
                                    {{ $line->delivery_status_label }}
                                </flux:badge>
                            </flux:cell>
                            <flux:cell>
                                @if($line->delivery_date)
                                    <div class="text-sm">{{ $line->delivery_date->format('d-m-Y') }}</div>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </flux:cell>
                            <flux:cell>
                                <flux:button 
                                    size="sm" 
                                    variant="ghost"
                                    wire:click="selectLine({{ $line->id }})"
                                >
                                    Bijwerken
                                </flux:button>
                            </flux:cell>
                        </flux:row>
                    @endforeach
                </flux:rows>
            </flux:table>
        </flux:card.body>
    </flux:card>

    <!-- Update Delivery Modal -->
    <flux:modal wire:model="selectedLine" class="space-y-6 max-w-lg">
        @if($selectedLine)
            @php
                $line = $invoice->lines->find($selectedLine);
            @endphp

            <div>
                <flux:heading size="lg">Leveringsstatus bijwerken</flux:heading>
                <flux:subheading>{{ $line->product->name }}</flux:subheading>
            </div>

            <form wire:submit="updateDeliveryStatus" class="space-y-4">
                <flux:select wire:model="deliveryStatus" label="Leveringsstatus" required>
                    <option value="not_delivered">Niet geleverd</option>
                    <option value="partially_delivered">Deels geleverd</option>
                    <option value="delivered">Geleverd</option>
                </flux:select>

                <flux:input 
                    wire:model="deliveryDate" 
                    type="date" 
                    label="Leverdatum"
                />

                <flux:textarea 
                    wire:model="deliveryNotes" 
                    label="Opmerkingen" 
                    placeholder="Bijv. tracking nummer, verwachte datum, opmerkingen..."
                    rows="3"
                />

                <div class="flex gap-2 justify-end">
                    <flux:button 
                        wire:click="$set('selectedLine', null)" 
                        variant="ghost"
                        type="button"
                    >
                        Annuleren
                    </flux:button>
                    
                    <flux:button 
                        type="submit"
                        variant="primary"
                    >
                        <flux:icon.check class="size-5" />
                        Opslaan
                    </flux:button>
                </div>
            </form>
        @endif
    </flux:modal>
</div>
