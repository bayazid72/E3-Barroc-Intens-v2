<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\Company;
use Livewire\Component;
use App\Events\InvoicePaid;


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
        $invoice = Invoice::findOrFail($id);

        $wasPaid = $invoice->status === 'paid';

        $invoice->update([
            'status'             => 'paid',
            'paid_at'            => now(),
            'procurement_status' => 'pending',
        ]);

        if (! $wasPaid) {
            InvoicePaid::dispatch($invoice->fresh('company', 'invoiceLines.product'));
        }

        session()->flash('success', 'Factuur gemarkeerd als betaald!');
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
            'company_id'   => 'required|exists:companies,id',
            'invoice_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'status'       => 'required|in:open,paid,overdue',
        ]);

        $invoice = Invoice::findOrFail($this->editingInvoiceId);

        $oldStatus = $invoice->status;

        $data = [
            'company_id'   => $this->company_id,
            'invoice_date' => $this->invoice_date,
            'total_amount' => $this->total_amount,
            'status'       => $this->status,
        ];

        // als status naar betaald gaat → paid_at + inkoop pending
        if ($oldStatus !== 'paid' && $this->status === 'paid') {
            $data['paid_at']            = now();
            $data['procurement_status'] = 'pending';
        }

        $invoice->update($data);

        // event als hij nu betaald is geworden
        if ($oldStatus !== 'paid' && $this->status === 'paid') {
            InvoicePaid::dispatch($invoice->fresh('company', 'lines.product'));
        }

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
