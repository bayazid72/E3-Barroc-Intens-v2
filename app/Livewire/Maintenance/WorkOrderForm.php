<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\WorkOrder;

class WorkOrderForm extends Component
{
    public $workOrder;
    public $appointment;

    public $notes;
    public $solution;
    public $materials = [];

    public function mount(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder->load('appointment.company');

        $this->appointment = $this->workOrder->appointment;

        $this->notes = $this->workOrder->notes;
        $this->solution = $this->workOrder->solution;
        $this->materials = $this->workOrder->materials_used ?? [];
    }

    public function addMaterial()
    {
        $this->materials[] = [
            'name' => '',
            'quantity' => 1,
        ];
    }

    public function removeMaterial($i)
    {
        unset($this->materials[$i]);
        $this->materials = array_values($this->materials);
    }

    public function save()
    {
        $this->workOrder->update([
            'notes' => $this->notes,
            'solution' => $this->solution,
            'materials_used' => $this->materials,
        ]);

        session()->flash('success', 'Werkbon succesvol opgeslagen!');

        return redirect()->route('maintenance.workorder.form', $this->workOrder->id);
    }

    public function render()
    {
        return view('livewire.maintenance.workorder-form');
    }
}
