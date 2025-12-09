<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use App\Models\Invoice;

class InvoiceTasks extends Component
{
    public function render()
    {
        $invoices = Invoice::with(['company', 'invoiceLines.product'])
            ->where('status', Invoice::STATUS_PAID)
            ->whereIn('procurement_status', [
                Invoice::PROC_PENDING,
                Invoice::PROC_ORDERED,
                Invoice::PROC_DELIVERED,
            ])
            ->orderBy('invoice_date', 'desc')
            ->get();

        return view('livewire.purchase.invoice-tasks', [
            'invoices' => $invoices,
        ]);
    }
}
