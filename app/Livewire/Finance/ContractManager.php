<?php

namespace App\Livewire\Finance;

use App\Models\Contract;
use App\Models\ContractLine;
use App\Models\Product;
use App\Models\Company;
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

        // regels laden
        $this->rulesData = [];
        foreach ($contract->lines as $line) {
            $this->rulesData[] = [
                'product_id' => $line->product_id,
                'quantity' => $line->amount,
                'beans_per_month' => $line->beans_per_month,
            ];
        }
    }

    public function deleteContract($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->lines()->delete();
        $contract->delete();

        session()->flash('success', 'Contract verwijderd.');
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
            'contracts' => Contract::with('company')->orderBy('created_at', 'desc')->get(),
            'companies' => Company::all(),
            'products' => Product::all(),
        ]);
    }
}
