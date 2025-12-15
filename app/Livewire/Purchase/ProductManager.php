<?php

namespace App\Livewire\Purchase;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;

class ProductManager extends Component
{
    /* -------------------------------------------------
     | FORM FIELDS
     ------------------------------------------------- */
    public $name;
    public $price;
    public $category_id;
    public $supplier;
    public $description;
    public $visibility;
    public $editingId = null;
    public $errorMessage;

    /* -------------------------------------------------
     | FILTER FIELDS
     ------------------------------------------------- */
    public $search = "";
    public $filterCategory = "";
    public $filterVisibility = "";
    public $filterPriceMax = "";

    public function rules()
    {
        return [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'visibility'  => 'required|in:public,employee',
            'supplier'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /* -------------------------------------------------
     | FILTERED PRODUCTS QUERY
     ------------------------------------------------- */
    public function getFilteredProductsProperty()
    {
        return Product::query()
            ->with('category')

            // Zoek op naam, beschrijving of leverancier
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('supplier', 'like', "%{$this->search}%");
                });
            })

            // Filter categorie
            ->when($this->filterCategory, function ($q) {
                $q->where('product_category_id', $this->filterCategory);
            })

            // Filter zichtbaarheid
            ->when($this->filterVisibility, function ($q) {
                if ($this->filterVisibility === 'employee') {
                    $q->whereHas('category', fn($c) => $c->where('is_employee_only', true));
                } elseif ($this->filterVisibility === 'public') {
                    $q->whereHas('category', fn($c) => $c->where('is_employee_only', false));
                }
            })


            // Filter max. prijs
            ->when($this->filterPriceMax !== "", function ($q) {
                $q->where('price', '<=', $this->filterPriceMax);
            })

            ->orderBy('name')
            ->get();
    }

    public function applyFilters()
    {
        // Livewire updates automatisch via computed property
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterCategory', 'filterVisibility', 'filterPriceMax']);
    }

    /* -------------------------------------------------
     | EDIT PRODUCT
     ------------------------------------------------- */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $this->editingId   = $id;
        $this->name        = $product->name;
        $this->price       = $product->price;
        $this->category_id = $product->product_category_id;
        $this->visibility  = $product->category?->is_employee_only ? 'employee' : 'public';
        $this->supplier    = $product->supplier;
        $this->description = $product->description;
    }

    /* -------------------------------------------------
     | SAVE PRODUCT
     ------------------------------------------------- */
    public function save()
    {
        $data = $this->validate();

        // Category bepalen op basis van zichtbaarheid
        $category = ProductCategory::where('is_employee_only', $data['visibility'] === 'employee')->first();

        if (!$category) {
            $this->errorMessage = "Geen categorie gevonden voor deze zichtbaarheid.";
            return;
        }

        $data['product_category_id'] = $category->id;

        if ($this->editingId) {
            Product::find($this->editingId)->update($data);
            session()->flash('success', 'Product bijgewerkt.');
        } else {
            Product::create($data);
            session()->flash('success', 'Product aangemaakt.');
        }

        $this->reset([
            'name', 'price', 'description', 'supplier', 'visibility',
            'category_id', 'editingId', 'errorMessage'
        ]);
    }

    public function render()
    {
        return view('livewire.purchase.products', [
            'products'   => $this->filteredProducts,
            'categories' => ProductCategory::all(),
        ]);
    }
    public function deleteProduct($id)
    {
        $product = Product::withCount('inventoryMovements')->findOrFail($id);

        if ($product->inventory_movements_count > 0) {
            session()->flash(
                'error',
                'Dit product is al gebruikt/besteld en kan niet worden verwijderd.'
            );
            return;
        }

        $product->delete(); // soft delete
        session()->flash('success', 'Product verwijderd.');
    }

}

