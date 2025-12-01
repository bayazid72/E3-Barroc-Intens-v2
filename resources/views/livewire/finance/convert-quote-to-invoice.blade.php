<div>
    @if($quote)
        <!-- Convert Button -->
        <div class="mb-4">
            @if($quote->canBeConverted())
                <flux:button 
                    wire:click="confirmConversion" 
                    variant="primary"
                    icon="document-check"
                    class="bg-green-600 hover:bg-green-700"
                >
                    Maak Factuur
                </flux:button>
            @else
                <flux:badge color="zinc" variant="outline">
                    @if($quote->status === 'converted')
                        Reeds omgezet naar factuur
                    @else
                        Kan niet worden omgezet
                    @endif
                </flux:badge>
            @endif
        </div>

        <!-- Confirmation Modal -->
        <flux:modal wire:model="showConfirmation" class="space-y-6 max-w-lg">
            <div>
                <flux:heading size="lg">Offerte omzetten naar factuur</flux:heading>
                <flux:subheading>
                    Weet je zeker dat je deze offerte wilt omzetten naar een factuur?
                </flux:subheading>
            </div>

            <!-- Quote Summary -->
            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Klant:</span>
                    <span class="text-sm font-semibold">{{ $quote->company->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Totaal bedrag:</span>
                    <span class="text-sm font-semibold">€ {{ number_format($quote->total_amount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Aantal regels:</span>
                    <span class="text-sm font-semibold">{{ $quote->lines->count() }}</span>
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex gap-3">
                    <flux:icon.information-circle class="size-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-blue-800 dark:text-blue-300">
                        <p class="font-medium mb-1">De volgende gegevens worden overgenomen:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Alle producten en aantallen</li>
                            <li>Prijzen per product</li>
                            <li>Klantgegevens</li>
                            <li>Contractinformatie</li>
                        </ul>
                        <p class="mt-2 font-medium">Een uniek factuurnummer wordt automatisch gegenereerd.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button 
                    wire:click="cancelConversion" 
                    variant="ghost"
                    :disabled="$converting"
                >
                    Annuleren
                </flux:button>
                
                <flux:button 
                    wire:click="convertToInvoice" 
                    variant="primary"
                    :disabled="$converting"
                    class="bg-green-600 hover:bg-green-700"
                >
                    @if($converting)
                        <flux:icon.arrow-path class="size-5 animate-spin" />
                        Bezig met omzetten...
                    @else
                        <flux:icon.check class="size-5" />
                        Ja, maak factuur
                    @endif
                </flux:button>
            </div>
        </flux:modal>
    @endif

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <flux:banner variant="success" class="mb-4">
            {{ session('success') }}
        </flux:banner>
    @endif

    @if(session()->has('error'))
        <flux:banner variant="danger" class="mb-4">
            {{ session('error') }}
        </flux:banner>
    @endif
</div>
