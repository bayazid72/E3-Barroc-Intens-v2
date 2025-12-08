<?php

namespace App\Livewire\Purchase;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Livewire\Component;

class PaidInvoiceDetail extends Component
{
    public Invoice $invoice;
    public $selectedLine = null;
    public $deliveryStatus;
    public $deliveryDate;
    public $deliveryNotes;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['company', 'lines.product']);
    }

    public function selectLine($lineId)
    {
        $line = $this->invoice->lines->find($lineId);
        $this->selectedLine = $lineId;
        $this->deliveryStatus = $line->delivery_status;
        $this->deliveryDate = $line->delivery_date?->format('Y-m-d');
        $this->deliveryNotes = $line->delivery_notes;
    }

    public function updateDeliveryStatus()
    {
        $this->validate([
            'deliveryStatus' => 'required|in:not_delivered,partially_delivered,delivered',
            'deliveryDate' => 'nullable|date',
            'deliveryNotes' => 'nullable|string|max:500',
        ]);

        $line = InvoiceLine::find($this->selectedLine);
        $line->update([
            'delivery_status' => $this->deliveryStatus,
            'delivery_date' => $this->deliveryDate,
            'delivery_notes' => $this->deliveryNotes,
        ]);

        session()->flash('success', 'Leveringsstatus bijgewerkt!');
        $this->selectedLine = null;
        $this->invoice->refresh();
    }

    public function markAllAsDelivered()
    {
        foreach ($this->invoice->lines as $line) {
            $line->markAsDelivered('Automatisch gemarkeerd als geleverd');
        }

        session()->flash('success', 'Alle producten gemarkeerd als geleverd!');
        $this->invoice->refresh();
    }

    public function render()
    {
        return view('livewire.purchase.paid-invoice-detail');
    }
}
