<?php

namespace App\Livewire\Purchase;

use App\Models\Product;
use App\Models\InventoryMovement;
use Livewire\Component;

class StockManager extends Component
{
    public $product_id, $quantity;

    public function add()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        InventoryMovement::create([
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'reason' => 'purchase',
        ]);

        $this->reset(['product_id','quantity']);
    }

    public function render()
    {
        return view('livewire.purchase.stock', [
            'products' => Product::all(),
            'movements' => InventoryMovement::latest()->limit(50)->get(),
        ]);
    }
}
