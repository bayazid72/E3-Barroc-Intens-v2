<?php

namespace App\Livewire\Purchase;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;

class ProductManager extends Component
{
    public $name, $price, $category_id, $description;
    public $editingId = null;

    public function save()
    {
        $data = $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'nullable',
        ]);

        $data['product_category_id'] = $data['category_id'];
        unset($data['category_id']);

        if ($this->editingId) {
            Product::find($this->editingId)->update($data);
        } else {
            Product::create($data);
        }

        $this->reset(['name','price','description','category_id','editingId']);
    }

    public function edit($id)
    {
        $p = Product::findOrFail($id);
        $this->editingId = $id;
        $this->name = $p->name;
        $this->price = $p->price;
        $this->description = $p->description;
        $this->category_id = $p->product_category_id;
    }

    public function delete($id)
    {
        Product::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.purchase.products', [
            'products' => Product::with('category')->get(),
            'categories' => ProductCategory::all(),
        ]);
    }
}
