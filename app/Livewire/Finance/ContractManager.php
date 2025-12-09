<?php

namespace App\Livewire\Finance;

use App\Models\Contract;
use App\Models\ContractLine;
use App\Models\Product;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use Livewire\Component;

class ContractManager extends Component
{
    public $company_id;
    public $starts_at;
    public $ends_at;
    public $invoice_type;
    public $periodic_interval_months;
    public $editingContractId = null;

    public $rulesData = [];
    public $searchInput = '';
    public $modelFilterInput = '';

    public $search = '';
    public $modelFilter = '';


    public function mount()
    {
        $this->rulesData = [
            ['product_id' => '', 'quantity' => 1, 'beans_per_month' => null],
        ];
    }

    public function addRule()
    {
        $this->rulesData[] = ['product_id' => '', 'quantity' => 1, 'beans_per_month' => null];
    }

    public function removeRule($index)
    {
        unset($this->rulesData[$index]);
        $this->rulesData = array_values($this->rulesData);
    }

    public function saveContract()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'invoice_type' => 'required|in:monthly,periodic',
            'starts_at' => 'required|date',
        ]);

        if ($this->invoice_type === 'periodic') {
            $this->validate([
                'periodic_interval_months' => 'required|integer|min:1',
            ]);
        }

        if ($this->editingContractId) {
            $contract = Contract::findOrFail($this->editingContractId);
            $contract->update([
                'company_id' => $this->company_id,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'invoice_type' => $this->invoice_type,
                'periodic_interval_months' => $this->invoice_type === 'periodic'
                    ? $this->periodic_interval_months
                    : null,
                'created_by' => auth()->id(),
            ]);

            // eerst oude regels verwijderen
            $contract->lines()->delete();

        } else {
            // nieuw contract
            $contract = Contract::create([
                'company_id' => $this->company_id,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'invoice_type' => $this->invoice_type,
                'periodic_interval_months' => $this->invoice_type === 'periodic'
                    ? $this->periodic_interval_months
                    : null,
                'created_by' => auth()->id(),
            ]);
        }

        // regels opslaan
        foreach ($this->rulesData as $rule) {
            $product = Product::find($rule['product_id']);

            $contract->lines()->create([
                'product_id' => $rule['product_id'],
                'amount' => $rule['quantity'] ?? 1,
                'price_snapshot' => $product?->price ?? 0,
                'beans_per_month' => $rule['beans_per_month'] ?? null,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Contract opgeslagen!');
    }

    public function editContract($id)
    {
        $contract = Contract::with('lines')->findOrFail($id);

        $this->editingContractId = $contract->id;
        $this->company_id = $contract->company_id;
        $this->starts_at = $contract->starts_at;
        $this->ends_at = $contract->ends_at;
        $this->invoice_type = $contract->invoice_type;
        $this->periodic_interval_months = $contract->periodic_interval_months;

        $this->rulesData = [];
        foreach ($contract->lines as $line) {
            $this->rulesData[] = [
                'product_id'       => $line->product_id,
                'quantity'         => $line->amount,
                'beans_per_month'  => $line->beans_per_month,
            ];
        }

        $this->dispatch('scrollToTop');
    }


    public function deleteContract($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->lines()->delete();
        $contract->delete();

        session()->flash('success', 'Contract verwijderd.');
    }

    public function generateInvoice($contractId)
    {
        try {
            $contract = Contract::with(['company', 'lines.product'])->findOrFail($contractId);

            // Check if contract has lines
            if ($contract->lines->isEmpty()) {
                session()->flash('error', 'Contract heeft geen regels om te factureren.');
                return;
            }

            // Calculate total
            $totalAmount = $contract->lines->sum(function ($line) {
                return $line->amount * $line->price_snapshot;
            });

            // Create invoice
            $invoice = Invoice::create([
                'type' => 'invoice',
                'company_id' => $contract->company_id,
                'contract_id' => $contract->id,
                'invoice_date' => now(),
                'total_amount' => $totalAmount,
                'status' => 'open',
            ]);

            // Create invoice lines from contract lines
            foreach ($contract->lines as $contractLine) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $contractLine->product_id,
                    'amount' => $contractLine->amount,
                    'price_snapshot' => $contractLine->price_snapshot,
                    'delivery_status' => 'not_delivered',
                ]);
            }

            session()->flash('success', 'Factuur ' . $invoice->invoice_number . ' aangemaakt vanuit contract!');
            return redirect()->route('finance.invoices.show', $invoice);
        } catch (\Exception $e) {
            session()->flash('error', 'Fout bij genereren factuur: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->editingContractId = null;
        $this->company_id = '';
        $this->starts_at = '';
        $this->ends_at = '';
        $this->invoice_type = '';
        $this->periodic_interval_months = '';

        $this->rulesData = [
            ['product_id' => '', 'quantity' => 1, 'beans_per_month' => null],
        ];
    }

    public function render()
    {
        return view('livewire.finance.contract-manager', [
        'contracts' => Contract::with('company')
            ->when($this->search, function ($q) {
                $q->whereHas('company', function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->modelFilter, function ($q) {
                $q->where('invoice_type', $this->modelFilter);
            })
            ->orderBy('created_at', 'desc')
            ->get(),
            'companies' => Company::all(),
            'products' => Product::all(),
        ]);
    }

    public function applyFilters()
    {
        $this->search = $this->searchInput;
        $this->modelFilter = $this->modelFilterInput;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->modelFilter = '';

        $this->searchInput = '';
        $this->modelFilterInput = '';
    }

}
