<?php

namespace App\Livewire\Purchase;

use App\Models\Product;
use Livewire\Component;

class PurchaseDashboard extends Component
{
    public function render()
    {
        return view('livewire.purchase.dashboard', [
            'products' => Product::with('category')->get()
        ]);
    }
}
