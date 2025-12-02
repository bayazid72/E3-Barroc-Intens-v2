<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['company', 'contract', 'lines.product']);
    }

    public function markAsPaid()
    {
        if (!$this->invoice->isPaid()) {
            $this->invoice->markAsPaid();
            session()->flash('success', 'Factuur ' . $this->invoice->invoice_number . ' is gemarkeerd als betaald!');
            $this->invoice->refresh();
        }
    }

    public function downloadPdf()
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $this->invoice->invoice_number . '.pdf');
    }    public function sendEmail()
    {
        try {
            if (!$this->invoice->company->email) {
                session()->flash('error', 'Klant heeft geen email adres.');
                return;
            }

            Mail::to($this->invoice->company->email)->send(new InvoiceMail($this->invoice));
            session()->flash('success', 'Factuur is verstuurd naar ' . $this->invoice->company->email);
        } catch (\Exception $e) {
            session()->flash('error', 'Fout bij versturen email: ' . $e->getMessage());
        }
    }

    public function duplicateInvoice()
    {
        try {
            // Create new invoice
            $newInvoice = $this->invoice->replicate();
            $newInvoice->invoice_number = null; // Will be auto-generated
            $newInvoice->invoice_date = now();
            $newInvoice->status = 'open';
            $newInvoice->paid_at = null;
            $newInvoice->payment_method = null;
            $newInvoice->payment_reference = null;
            $newInvoice->save();

            // Duplicate invoice lines
            foreach ($this->invoice->lines as $line) {
                $newLine = $line->replicate();
                $newLine->invoice_id = $newInvoice->id;
                $newLine->delivery_status = 'not_delivered';
                $newLine->delivery_date = null;
                $newLine->delivery_notes = null;
                $newLine->save();
            }

            session()->flash('success', 'Factuur gedupliceerd! Nieuwe factuur: ' . $newInvoice->invoice_number);
            return redirect()->route('finance.invoices.show', $newInvoice);
        } catch (\Exception $e) {
            session()->flash('error', 'Fout bij dupliceren: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.finance.invoice-show');
    }
}
