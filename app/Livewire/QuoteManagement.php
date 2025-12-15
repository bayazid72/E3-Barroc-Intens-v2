<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Company;
use App\Models\Product;
use Livewire\Component;

class QuoteManagement extends Component
{
    public $company_id;
    public $product_id;
    public $amount = 1;

    public ?Invoice $quote = null;

    public bool $editMode = false;

    protected $rules = [
        'company_id' => 'required|exists:companies,id',
        'product_id' => 'required|exists:products,id',
        'amount'     => 'required|integer|min:1',
    ];

    public function mount(?int $quoteId = null): void
    {
        if ($quoteId) {
            $this->quote = Invoice::withoutGlobalScope('invoice')
                ->where('type', 'quote')
                ->findOrFail($quoteId);

            $this->company_id = $this->quote->company_id;
            $this->editMode = false;
        }
    }

    public function createQuote(): void
    {
        $this->validateOnly('company_id');

        $this->quote = Invoice::withoutGlobalScope('invoice')->create([
            'company_id'   => $this->company_id,
            'invoice_date' => now(),
            'total_amount' => 0,
            'status'       => 'open',
            'type'         => 'quote',
            'is_sent'      => false,
        ]);

        $this->editMode = true;
    }

    /**
     * FIX: Offerte aanpassen moet echt editMode aanzetten.
     * Als je verzonden offertes NIET wilt aanpassen, geef dan een melding.
     */
    public function enableEdit(): void
    {
        if (!$this->quote) {
            return;
        }

        if ($this->quote->is_sent) {
            session()->flash('success', 'Deze offerte is al verzonden en kan niet meer aangepast worden.');
            return;
        }

        $this->editMode = true;
    }

    public function addLine(): void
    {
        if (!$this->quote || !$this->editMode) {
            return;
        }

        $this->validate();

        $product = Product::findOrFail($this->product_id);

        InvoiceLine::create([
            'invoice_id'     => $this->quote->id,
            'product_id'     => $product->id,
            'amount'         => $this->amount,
            'price_snapshot' => $product->price,
        ]);

        $this->recalculateTotal();

        $this->reset(['product_id', 'amount']);
        $this->amount = 1;
    }

    public function removeLine(int $lineId): void
    {
        if (!$this->quote || !$this->editMode) {
            return;
        }

        InvoiceLine::where('invoice_id', $this->quote->id)
            ->where('id', $lineId)
            ->delete();

        $this->recalculateTotal();
    }

    protected function recalculateTotal(): void
    {
        if (!$this->quote) {
            return;
        }

        $total = $this->quote->lines
            ->sum(fn ($line) => $line->amount * $line->price_snapshot);

        $this->quote->update([
            'total_amount' => $total,
        ]);
    }

    public function sendQuote(): void
    {
        if (!$this->quote || $this->quote->lines()->count() === 0) {
            return;
        }

        $this->quote->update([
            'is_sent' => true,
            'status'  => 'open',
        ]);

        $this->editMode = false;

        session()->flash('success', 'Offerte is verzonden.');
    }

    public function render()
    {
        return view('livewire.quote-management', [
            'companies' => Company::orderBy('name')->get(),
            'products'  => Product::orderBy('name')->get(),
            'lines'     => $this->quote?->lines ?? collect(),
        ]);
    }
}
