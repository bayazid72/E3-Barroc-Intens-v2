<?php

namespace App\Livewire\Purchase;

use App\Models\Product;
use Livewire\Component;

class SupplierManager extends Component
{
    public function getSuppliersProperty()
    {
        return Product::select('description')
            ->whereNotNull('description')
            ->pluck('description')
            ->map(function ($desc) {
                if (preg_match('/Supplier:\s*(.+)/i', $desc, $m)) {
                    return trim($m[1]);
                }
                return null;
            })
            ->filter()
            ->unique()
            ->values();
    }

    public function render()
    {
        return view('livewire.purchase.suppliers', [
            'suppliers' => $this->suppliers,
            'products' => Product::all(),
        ]);
    }
}
