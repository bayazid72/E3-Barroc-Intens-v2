<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\InventoryMovement;
use App\Models\User;

class ProductOverview extends Component
{
    /* =====================
     | PRODUCT FILTERS
     =====================*/
    public $search = '';
    public $stockStatus = 'all';
    public $forCustomer = false;

    public $filterCategory = "";
    public $filterVisibility = "";
    public $filterPriceMin = "";
    public $filterPriceMax = "";

    /* =====================
     | MUTATIE FILTERS
     =====================*/
    public $filterType = "";
    public $filterUser = "";
    public $dateFrom = "";
    public $dateTo = "";

    public function mount(bool $forCustomer = false)
    {
        $this->forCustomer = $forCustomer;
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'stockStatus',
            'filterCategory',
            'filterVisibility',
            'filterPriceMin',
            'filterPriceMax',
            'filterType',
            'filterUser',
            'dateFrom',
            'dateTo',
        ]);
    }

    public function applyFilters()
    {
        // Livewire herlaadt automatisch
    }

    public function render()
    {
        /* =====================
         | PRODUCT QUERY
         =====================*/
        $productQuery = Product::with('category')->orderBy('name');

        if ($this->forCustomer) {
            $productQuery->visibleForCustomers();
        }

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $productQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('description', 'like', $s)
                  ->orWhere('supplier', 'like', $s);
            });
        }

        if ($this->filterCategory) {
            $productQuery->where('product_category_id', $this->filterCategory);
        }

        if ($this->filterVisibility === 'public') {
            $productQuery->whereHas('category', fn ($q) =>
                $q->where('is_employee_only', false)
            );
        }

        if ($this->filterVisibility === 'employee') {
            $productQuery->whereHas('category', fn ($q) =>
                $q->where('is_employee_only', true)
            );
        }

        if ($this->filterPriceMin !== "") {
            $productQuery->where('price', '>=', $this->filterPriceMin);
        }

        if ($this->filterPriceMax !== "") {
            $productQuery->where('price', '<=', $this->filterPriceMax);
        }

        $products = $productQuery->get();

        if ($this->stockStatus === 'available') {
            $products = $products->filter->is_available;
        }

        if ($this->stockStatus === 'out') {
            $products = $products->reject->is_available;
        }

        /* =====================
         | MUTATIES QUERY
         =====================*/
        $movements = InventoryMovement::with(['product', 'user'])

            ->when($this->search, function ($q) {
                $q->whereHas('product', fn ($p) =>
                    $p->where('name', 'like', "%{$this->search}%")
                );
            })

            ->when($this->filterType, fn ($q) =>
                $q->where('type', $this->filterType)
            )

            ->when($this->filterUser, fn ($q) =>
                $q->where('user_id', $this->filterUser)
            )

            ->when($this->dateFrom, fn ($q) =>
                $q->whereDate('created_at', '>=', $this->dateFrom)
            )

            ->when($this->dateTo, fn ($q) =>
                $q->whereDate('created_at', '<=', $this->dateTo)
            )

            ->latest()
            ->get();

        /* =====================
         | USERS VOOR FILTER
         =====================*/
        $users = User::whereHas('inventoryMovements')
            ->orderBy('name')
            ->get();

        return view('livewire.purchase.product-overview', [
            'products'   => $products,
            'categories' => ProductCategory::all(),
            'movements'  => $movements,
            'users'      => $users,
        ]);
    }
}
