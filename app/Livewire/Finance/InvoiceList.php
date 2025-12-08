<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function markAsPaid($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoice->markAsPaid();

        session()->flash('success', 'Factuur ' . $invoice->invoice_number . ' gemarkeerd als betaald!');
    }

    public function sendEmail($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        
        // TODO: Implement email sending
        session()->flash('success', 'Email verzonden naar ' . $invoice->company->email);
    }

    public function render()
    {
        $query = Invoice::with(['company', 'lines'])
            ->when($this->search, function ($q) {
                $q->whereHas('company', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            });

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where(function($q) {
                if ($this->statusFilter === 'paid') {
                    $q->where('status', 'paid')->orWhereNotNull('paid_at');
                } elseif ($this->statusFilter === 'open') {
                    $q->where('status', 'open')
                      ->whereNull('paid_at')
                      ->where('invoice_date', '>', now()->subDays(30));
                } elseif ($this->statusFilter === 'overdue') {
                    $q->where('status', '!=', 'paid')
                      ->whereNull('paid_at')
                      ->where('invoice_date', '<=', now()->subDays(30));
                }
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);

        // Calculate stats
        $paidInvoices = Invoice::where('status', 'paid')->orWhereNotNull('paid_at');
        $openInvoices = Invoice::where('status', 'open')
            ->whereNull('paid_at')
            ->where('invoice_date', '>', now()->subDays(30));
        $overdueInvoices = Invoice::where('status', '!=', 'paid')
            ->whereNull('paid_at')
            ->where('invoice_date', '<=', now()->subDays(30));

        return view('livewire.finance.invoice-list', [
            'invoices' => $invoices,
            'paidCount' => $paidInvoices->count(),
            'paidTotal' => $paidInvoices->sum('total_amount'),
            'openCount' => $openInvoices->count(),
            'openTotal' => $openInvoices->sum('total_amount'),
            'overdueCount' => $overdueInvoices->count(),
            'overdueTotal' => $overdueInvoices->sum('total_amount'),
            'totalCount' => Invoice::count(),
            'totalAmount' => Invoice::sum('total_amount'),
        ]);
    }
}
