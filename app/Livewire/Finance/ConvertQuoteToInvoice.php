<?php

namespace App\Livewire\Finance;

use App\Models\Quote;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;

class ConvertQuoteToInvoice extends Component
{
    public ?Quote $quote = null;
    public bool $showConfirmation = false;
    public bool $converting = false;

    public function mount(?int $quoteId = null)
    {
        if ($quoteId) {
            $this->quote = Quote::with(['company', 'lines.product'])->find($quoteId);
        }
    }

    /**
     * Show confirmation modal
     */
    public function confirmConversion()
    {
        if (!$this->quote || !$this->quote->canBeConverted()) {
            session()->flash('error', 'Deze offerte kan niet worden omgezet naar een factuur.');
            return;
        }

        $this->showConfirmation = true;
    }

    /**
     * Cancel conversion
     */
    public function cancelConversion()
    {
        $this->showConfirmation = false;
    }

    /**
     * Convert quote to invoice
     */
    public function convertToInvoice()
    {
        if (!$this->quote || !$this->quote->canBeConverted()) {
            session()->flash('error', 'Deze offerte kan niet worden omgezet naar een factuur.');
            $this->showConfirmation = false;
            return;
        }

        try {
            $this->converting = true;

            // Convert quote to invoice
            $invoice = $this->quote->convertToInvoice();

            $this->showConfirmation = false;
            $this->converting = false;

            // Flash success message
            session()->flash('success', "Offerte succesvol omgezet naar factuur {$invoice->invoice_number}");

            // Dispatch event for other components to react
            $this->dispatch('quote-converted', invoiceId: $invoice->id);

            // Redirect to invoice view
            return redirect()->route('finance.invoices.show', $invoice->id);

        } catch (\Exception $e) {
            $this->converting = false;
            $this->showConfirmation = false;
            
            session()->flash('error', 'Er is een fout opgetreden bij het omzetten van de offerte: ' . $e->getMessage());
        }
    }

    /**
     * Listen for quote selection changes
     */
    #[On('quote-selected')]
    public function setQuote(int $quoteId)
    {
        $this->quote = Quote::with(['company', 'lines.product'])->find($quoteId);
    }

    public function render()
    {
        return view('livewire.finance.convert-quote-to-invoice');
    }
}
