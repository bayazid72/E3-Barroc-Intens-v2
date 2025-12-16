<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;

class StockManager extends Component
{
    /* =====================
     | VOORRAAD TOEVOEGEN
     =====================*/
    public $product_id;
    public $quantity;

    /* =====================
     | PRODUCT FILTERS
     =====================*/
    public $search = '';
    public $filterCategory = '';
    public $filterVisibility = '';
    public $stockStatus = 'all';

    /* =====================
     | ACTIES
     =====================*/
    public function add()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        InventoryMovement::create([
            'product_id' => $this->product_id,
            'quantity'   => $this->quantity,
            'type'       => 'purchase',
            'user_id'    => Auth::id(),
        ]);

        session()->flash('success', 'Voorraad toegevoegd!');
        $this->reset(['product_id', 'quantity']);
    }

    public function applyFilters()
    {
        // Livewire her-rendered automatisch
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'filterCategory',
            'filterVisibility',
            'stockStatus',
        ]);
    }

    /* =====================
     | RENDER
     =====================*/
    public function render()
    {
        $query = Product::with('category')->orderBy('name');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterCategory) {
            $query->where('product_category_id', $this->filterCategory);
        }

        if ($this->filterVisibility === 'employee') {
            $query->whereHas('category', fn ($q) =>
                $q->where('is_employee_only', true)
            );
        }

        if ($this->filterVisibility === 'public') {
            $query->whereHas('category', fn ($q) =>
                $q->where('is_employee_only', false)
            );
        }

        $products = $query->get();

        if ($this->stockStatus === 'available') {
            $products = $products->filter->is_available;
        }

        if ($this->stockStatus === 'out') {
            $products = $products->reject->is_available;
        }

        return view('livewire.purchase.stock', [
            'products'   => $products,
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }
}
