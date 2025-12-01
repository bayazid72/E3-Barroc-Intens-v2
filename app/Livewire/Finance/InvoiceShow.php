<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use Livewire\Component;

class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['company', 'contract', 'lines.product']);
    }

    public function render()
    {
        return view('livewire.finance.invoice-show');
    }
}
