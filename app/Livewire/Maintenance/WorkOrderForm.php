<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Models\Product;
use App\Models\InventoryMovement;
use App\Events\ProductUsed;
use Illuminate\Support\Facades\Auth;

class WorkOrderForm extends Component
{
    public $workOrder;
    public $appointment;

    public $notes;
    public $solution;

    /**
     * [
     *   ['product_id' => 3, 'quantity' => 2],
     *   ...
     * ]
     */
    public $materials = [];

    public function mount(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder->load('appointment.company');
        $this->appointment = $this->workOrder->appointment;

        $this->notes = $this->workOrder->notes;
        $this->solution = $this->workOrder->solution;

        // Convert saved materials to correct format
        $this->materials = collect($this->workOrder->materials_used ?? [])
            ->map(fn($m) => [
                'product_id' => $m['product_id'] ?? null,
                'quantity'   => $m['quantity'] ?? 1,
            ])
            ->toArray();
    }

    public function addMaterial()
    {
        $this->materials[] = [
            'product_id' => null,
            'quantity'   => 1,
        ];
    }

    public function removeMaterial($i)
    {
        unset($this->materials[$i]);
        $this->materials = array_values($this->materials);
    }

    public function save()
    {
        // 1 - VALIDATIE
        foreach ($this->materials as $i => $mat) {
            if ($mat['product_id']) {
                $this->validate([
                    "materials.$i.product_id" => 'required|exists:products,id',
                    "materials.$i.quantity"   => 'required|integer|min:1',
                ]);
            }
        }

        // 2 - Werkbon opslaan
        $this->workOrder->update([
            'notes'          => $this->notes,
            'solution'       => $this->solution,
            'materials_used' => $this->materials,
        ]);

        // 3 - Voorraad verminderen
        foreach ($this->materials as $mat) {

            if (!$mat['product_id']) {
                continue;
            }

            $product = Product::find($mat['product_id']);

            $qty = (int)$mat['quantity'];

            // Voorraadbeweging
            $movement = InventoryMovement::create([
                'product_id'             => $product->id,
                'quantity'               => $qty,
                'type'                   => 'usage',
                'related_work_order_id'  => $this->workOrder->id,
                'user_id'                => auth()->id(),
            ]);

            // Event → inkoop melding
            ProductUsed::dispatch($movement);
        }

        session()->flash('success', 'Werkbon succesvol opgeslagen!');
        return redirect()->route('maintenance.workorder.form', $this->workOrder->id);
    }

    public function render()
    {
        return view('livewire.maintenance.workorder-form', [
            'products' => Product::whereHas('category', fn($q) =>
                $q->where('is_employee_only', true)
            )
            ->orderBy('name')
            ->get(),
        ]);
    }

}
