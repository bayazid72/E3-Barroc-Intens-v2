<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\Company;
use Livewire\Component;

class InvoiceManager extends Component
{
    public $creating = false;

    public $company_id;
    public $invoice_date;
    public $total_amount;

    public $companies;

    public function mount()
    {
        $this->companies = Company::orderBy('name')->get();
    }

    public function startCreate()
    {
        $this->creating = true;
    }

    public function createInvoice()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'invoice_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
        ]);

        Invoice::create([
            'company_id'   => $this->company_id,
            'invoice_date' => $this->invoice_date, // LET OP: moet bestaan in DB
            'total_amount' => $this->total_amount,
            'status'       => 'open',
            'is_sent'      => false,
            'type'         => 'invoice',
        ]);

        $this->reset(['company_id', 'invoice_date', 'total_amount', 'creating']);

        session()->flash('success', 'Factuur succesvol aangemaakt!');
    }

    public function render()
    {
        return view('livewire.finance.invoice', [
            'invoices' => Invoice::with('company')->orderBy('invoice_date', 'desc')->get()
        ]);
    }
}
