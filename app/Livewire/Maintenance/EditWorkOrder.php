<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\WorkOrder;

class EditWorkorder extends Component
{
    public $workOrder;
    public $appointment;

    public $notes;
    public $solution;
    public $materials = [];

    public function mount(WorkOrder $workOrder)
    {
        // Relaties laden
        $this->workOrder = $workOrder->load('appointment.company');

        // Afspraak koppelen
        $this->appointment = $this->workOrder->appointment;

        // Velden vullen
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

    public function removeMaterial($index)
    {
        unset($this->materials[$index]);
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

        return redirect()->route('maintenance.workorder', $this->workOrder->id);
    }

    public function render()
    {
        return view('maintenance.edit-workorder');
    }
}
