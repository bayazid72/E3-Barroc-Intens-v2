<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\Company;
use Livewire\Component;

class InvoiceManager extends Component
{
    public $creating = false;
    public $editing  = false;

    public $editingInvoiceId = null;

    public $company_id;
    public $invoice_date;
    public $total_amount;

    public $companies;
    public $status;


    public function mount()
    {
        $this->companies = Company::orderBy('name')->get();
    }

    /* =============================
     * CREATE
     * ============================= */

    public function startCreate()
    {
        $this->reset(['editing', 'editingInvoiceId']);
        $this->creating = true;
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::withoutGlobalScope('invoice')->findOrFail($id);

        $invoice->markAsPaid();

        session()->flash('success', 'Factuur gemarkeerd als betaald! Inkoop is genotificeerd.');
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
            'invoice_date' => $this->invoice_date,
            'total_amount' => $this->total_amount,
            'status'       => 'open',
            'is_sent'      => false,
            'type'         => 'invoice',
        ]);

        $this->reset(['company_id','invoice_date','total_amount','creating']);

        session()->flash('success', 'Factuur succesvol aangemaakt!');
    }


    /* =============================
     * EDIT
     * ============================= */

    public function editInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);

        $this->editing = true;
        $this->creating = false;

        $this->editingInvoiceId = $invoice->id;

        $this->company_id = $invoice->company_id;
        $this->invoice_date = $invoice->invoice_date;
        $this->total_amount = $invoice->total_amount;
        $this->status = $invoice->status;
    }

    public function updateInvoice()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'invoice_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:open,paid,overdue',

        ]);

        $invoice = Invoice::findOrFail($this->editingInvoiceId);

        $invoice->update([
            'company_id' => $this->company_id,
            'invoice_date' => $this->invoice_date,
            'total_amount' => $this->total_amount,
            'status'       => $this->status,
        ]);

        $this->reset(['editing','editingInvoiceId','company_id','invoice_date','total_amount']);

        session()->flash('success', 'Factuur succesvol bijgewerkt!');
    }


    public function render()
    {
        return view('livewire.finance.invoice', [
            'invoices' => Invoice::with('company')->orderBy('invoice_date', 'desc')->get()
        ]);
    }


    public function deleteInvoice($id)
        {
            $invoice = Invoice::findOrFail($id);

            // verwijder gekoppelde regels (als cascade niet werkt)
            if ($invoice->lines()->count() > 0) {
                $invoice->lines()->delete();
            }

            $invoice->delete();

            session()->flash('success', 'Factuur succesvol verwijderd!');
        }

}
