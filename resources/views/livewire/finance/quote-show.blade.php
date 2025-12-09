<div class="space-y-6">
    <!-- Header with Convert Button -->
    <div class="flex justify-between items-start">
        <div>
            <flux:heading size="xl">Offerte Details</flux:heading>
            <flux:subheading>
                @if($quote->invoice_number)
                    {{ $quote->invoice_number }}
                @else
                    Offerte #{{ $quote->id }}
                @endif
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:button variant="ghost" icon="arrow-left" wire:navigate href="{{ route('finance.quotes.index') }}">
                Terug
            </flux:button>
            
            <livewire:finance.convert-quote-to-invoice :quoteId="$quote->id" />
        </div>
    </div>

    <!-- Quote Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Company Info -->
        <flux:card>
            <flux:card.header>
                <flux:heading size="lg">Klantgegevens</flux:heading>
            </flux:card.header>
            <flux:card.body class="space-y-3">
                <div>
                    <div class="text-sm text-zinc-500">Bedrijfsnaam</div>
                    <div class="font-medium">{{ $quote->company->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Email</div>
                    <div class="font-medium">{{ $quote->company->email ?? 'Niet beschikbaar' }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Telefoon</div>
                    <div class="font-medium">{{ $quote->company->phone ?? 'Niet beschikbaar' }}</div>
                </div>
            </flux:card.body>
        </flux:card>

        <!-- Quote Info -->
        <flux:card>
            <flux:card.header>
                <flux:heading size="lg">Offerte Informatie</flux:heading>
            </flux:card.header>
            <flux:card.body class="space-y-3">
                <div>
                    <div class="text-sm text-zinc-500">Datum</div>
                    <div class="font-medium">{{ $quote->invoice_date->format('d-m-Y') }}</div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Status</div>
                    <div>
                        @php
                            $statusColors = [
                                'draft' => 'zinc',
                                'sent' => 'blue',
                                'accepted' => 'green',
                                'rejected' => 'red',
                                'converted' => 'purple',
                                'open' => 'yellow',
                            ];
                            $statusLabels = [
                                'draft' => 'Concept',
                                'sent' => 'Verzonden',
                                'accepted' => 'Geaccepteerd',
                                'rejected' => 'Afgewezen',
                                'converted' => 'Omgezet naar factuur',
                                'open' => 'Open',
                            ];
                        @endphp
                        <flux:badge :color="$statusColors[$quote->status] ?? 'zinc'">
                            {{ $statusLabels[$quote->status] ?? $quote->status }}
                        </flux:badge>
                    </div>
                </div>
                <div>
                    <div class="text-sm text-zinc-500">Totaal bedrag</div>
                    <div class="text-2xl font-bold text-green-600">
                        € {{ number_format($quote->total_amount, 2, ',', '.') }}
                    </div>
                </div>
            </flux:card.body>
        </flux:card>
    </div>

    <!-- Quote Lines -->
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
                    @foreach($quote->lines as $line)
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
                            € {{ number_format($quote->total_amount, 2, ',', '.') }}
                        </flux:cell>
                    </flux:row>
                </flux:rows>
            </flux:table>
        </flux:card.body>
    </flux:card>
</div>
