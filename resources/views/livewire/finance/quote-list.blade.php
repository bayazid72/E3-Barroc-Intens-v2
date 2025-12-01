<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <flux:heading size="xl">Offertes</flux:heading>
        <flux:button variant="primary" icon="plus">
            Nieuwe Offerte
        </flux:button>
    </div>

    <!-- Filters -->
    <div class="flex gap-4">
        <flux:input 
            wire:model.live.debounce.300ms="search" 
            placeholder="Zoek op klant of nummer..."
            icon="magnifying-glass"
            class="flex-1"
        />
        
        <flux:select wire:model.live="statusFilter" class="w-48">
            <option value="all">Alle statussen</option>
            <option value="draft">Concept</option>
            <option value="sent">Verzonden</option>
            <option value="accepted">Geaccepteerd</option>
            <option value="rejected">Afgewezen</option>
            <option value="converted">Omgezet</option>
        </flux:select>
    </div>

    <!-- Quotes Table -->
    <flux:card>
        <flux:table>
            <flux:columns>
                <flux:column>Klant</flux:column>
                <flux:column>Datum</flux:column>
                <flux:column>Totaal Bedrag</flux:column>
                <flux:column>Status</flux:column>
                <flux:column>Acties</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse($quotes as $quote)
                    <flux:row wire:key="quote-{{ $quote->id }}">
                        <flux:cell>
                            <div class="font-medium">{{ $quote->company->name }}</div>
                            @if($quote->invoice_number)
                                <div class="text-xs text-zinc-500">{{ $quote->invoice_number }}</div>
                            @endif
                        </flux:cell>

                        <flux:cell>
                            {{ $quote->invoice_date->format('d-m-Y') }}
                        </flux:cell>

                        <flux:cell class="font-semibold">
                            € {{ number_format($quote->total_amount, 2, ',', '.') }}
                        </flux:cell>

                        <flux:cell>
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
                                    'converted' => 'Omgezet',
                                    'open' => 'Open',
                                ];
                            @endphp
                            <flux:badge 
                                :color="$statusColors[$quote->status] ?? 'zinc'"
                                size="sm"
                            >
                                {{ $statusLabels[$quote->status] ?? $quote->status }}
                            </flux:badge>
                        </flux:cell>

                        <flux:cell>
                            <div class="flex gap-2">
                                <flux:button 
                                    size="sm" 
                                    variant="ghost" 
                                    icon="eye"
                                    wire:navigate
                                    href="{{ route('finance.quotes.show', $quote->id) }}"
                                >
                                    Bekijken
                                </flux:button>

                                @if($quote->canBeConverted())
                                    <livewire:finance.convert-quote-to-invoice 
                                        :quoteId="$quote->id" 
                                        :key="'convert-' . $quote->id"
                                    />
                                @endif
                            </div>
                        </flux:cell>
                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="5" class="text-center py-8 text-zinc-500">
                            Geen offertes gevonden
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $quotes->links() }}
        </div>
    </flux:card>
</div>
