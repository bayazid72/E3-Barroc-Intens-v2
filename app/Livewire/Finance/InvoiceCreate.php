<?php

namespace App\Livewire\Finance;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceCreate extends Component
{
    use WithPagination;

    public $company_id = '';
    public $contract_id = '';
    public $invoice_date;
    public $description = '';
    
    // Line items
    public $lines = [];
    
    // Search
    public $searchCompany = '';
    public $searchContract = '';

    public function mount()
    {
        $this->invoice_date = now()->format('Y-m-d');
        $this->addLine();
    }

    public function addLine()
    {
        $this->lines[] = [
            'product_id' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
    }

    public function loadContractLines()
    {
        if (!$this->contract_id) {
            return;
        }

        $contract = Contract::with('lines.product')->find($this->contract_id);
        
        if ($contract) {
            $this->lines = [];
            foreach ($contract->lines as $contractLine) {
                $this->lines[] = [
                    'product_id' => $contractLine->product_id,
                    'description' => $contractLine->product->name ?? '',
                    'quantity' => $contractLine->quantity,
                    'unit_price' => $contractLine->price,
                ];
            }
        }
    }

    public function updatedProductId($value, $index)
    {
        $product = Product::find($value);
        if ($product) {
            $this->lines[$index]['description'] = $product->name;
            $this->lines[$index]['unit_price'] = $product->price ?? 0;
        }
    }

    public function createInvoice()
    {
        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'invoice_date' => 'required|date',
            'lines' => 'required|array|min:1',
            'lines.*.description' => 'required|string',
            'lines.*.quantity' => 'required|numeric|min:1',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ], [
            'company_id.required' => 'Selecteer een klant',
            'lines.required' => 'Voeg minimaal één regel toe',
            'lines.*.description.required' => 'Omschrijving is verplicht',
            'lines.*.quantity.required' => 'Aantal is verplicht',
            'lines.*.unit_price.required' => 'Prijs is verplicht',
        ]);

        try {
            // Calculate total
            $totalAmount = collect($this->lines)->sum(function ($line) {
                return $line['quantity'] * $line['unit_price'];
            });

            // Create invoice
            $invoice = Invoice::create([
                'type' => 'invoice',
                'company_id' => $this->company_id,
                'contract_id' => $this->contract_id ?: null,
                'invoice_date' => $this->invoice_date,
                'total_amount' => $totalAmount,
                'status' => 'open',
            ]);

            // Create lines
            foreach ($this->lines as $line) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $line['product_id'] ?: null,
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total_price' => $line['quantity'] * $line['unit_price'],
                    'delivery_status' => 'not_delivered',
                ]);
            }

            session()->flash('success', 'Factuur ' . $invoice->invoice_number . ' is aangemaakt!');
            return redirect()->route('finance.invoices.show', $invoice);
        } catch (\Exception $e) {
            session()->flash('error', 'Fout bij aanmaken factuur: ' . $e->getMessage());
        }
    }

    public function generateFromContract($contractId)
    {
        $this->contract_id = $contractId;
        $this->loadContractLines();
    }

    public function render()
    {
        $companies = Company::when($this->searchCompany, function ($query) {
            $query->where('name', 'like', '%' . $this->searchCompany . '%');
        })->limit(10)->get();

        $contracts = Contract::with('company')
            ->when($this->searchContract, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchContract . '%');
                });
            })
            ->limit(10)
            ->get();

        $products = Product::all();

        return view('livewire.finance.invoice-create', [
            'companies' => $companies,
            'contracts' => $contracts,
            'products' => $products,
        ]);
    }
}
