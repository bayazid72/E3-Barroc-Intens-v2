<?php

namespace App\Livewire\Purchase;

use App\Models\Product;
use Livewire\Component;

class SupplierManager extends Component
{
    public function getSuppliersProperty()
    {
        return Product::whereNotNull('supplier')
            ->where('supplier', '!=', '')
            ->pluck('supplier')
            ->unique()
            ->values();
    }

    public function render()
    {
        return view('livewire.purchase.suppliers', [
            'suppliers' => $this->suppliers,
            'products'  => Product::all(),
        ]);
    }
}
