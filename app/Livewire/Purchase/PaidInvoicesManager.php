<?php

namespace App\Livewire\Purchase;

use App\Models\Invoice;
use Livewire\Component;

class PaidInvoicesManager extends Component
{
    public $search = '';

    public function render()
    {
        $query = Invoice::with(['company', 'invoiceLines.product'])
            ->where('status', 'paid')
            ->orderBy('paid_at', 'desc');

        if ($this->search) {
            $s = '%' . $this->search . '%';

            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', $s)
                  ->orWhereHas('company', function ($qc) use ($s) {
                      $qc->where('name', 'like', $s);
                  });
            });
        }

        $paidInvoices = $query->paginate(15);

        $backorderLines = $paidInvoices
        ->getCollection()              // haal de items uit paginator
        ->flatMap->invoiceLines        // flatten alle invoice lines
        ->filter->isBackorder();       // alleen not_delivered + invoice->isPaid()


        return view('livewire.purchase.paid-invoices-manager', compact('paidInvoices', 'backorderLines'));
    }
}
