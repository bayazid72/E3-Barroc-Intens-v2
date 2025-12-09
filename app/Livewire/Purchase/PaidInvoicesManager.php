<?php

namespace App\Livewire\Purchase;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Livewire\Component;
use Livewire\WithPagination;

class PaidInvoicesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $actionFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'actionFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActionFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $paidInvoices = Invoice::with(['company', 'lines.product'])
            ->where('status', 'paid')
            ->when($this->search, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy('paid_at', 'desc')
            ->paginate(15);

        $backorderLines = InvoiceLine::with(['invoice.company', 'product'])
            ->whereHas('invoice', function ($query) {
                $query->where('status', 'paid');
            })
            ->where('delivery_status', 'not_delivered')
            ->get();

        return view('livewire.purchase.paid-invoices-manager', [
            'paidInvoices' => $paidInvoices,
            'backorderLines' => $backorderLines,
        ]);
    }
}
