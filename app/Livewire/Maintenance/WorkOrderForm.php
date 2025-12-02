<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

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
        $this->materials[] = ['name' => '', 'quantity' => 1];
    }

    public function removeMaterial($i)
    {
        unset($this->materials[$i]);
        $this->materials = array_values($this->materials);
    }

    public function save()
    {
        $this->workOrder->update([
            'notes'          => $this->notes,
            'solution'       => $this->solution,
            'materials_used' => $this->materials,
        ]);

        // COMPLETE notificatie
        Notification::create([
            'user_id'       => null, // zichtbaar voor alle managers/planners
            'title'         => 'Werkbon afgerond',
            'message'       => json_encode([
                'workorder_id'  => $this->workOrder->id,
                'company'       => $this->appointment->company->name,
                'date'          => $this->appointment->date_planned,
                'problem'       => $this->appointment->description,
                'solution'      => $this->solution,
                'materials'     => $this->materials,
                'technician'    => Auth::user()->name,
            ]),
            'type'          => 'workorder',
            'work_order_id' => $this->workOrder->id,
        ]);

        session()->flash('success', 'Werkbon succesvol opgeslagen!');
        return redirect()->route('maintenance.workorder.form', $this->workOrder->id);
    }

    public function render()
    {
        return view('livewire.maintenance.workorder-form');
    }
}
