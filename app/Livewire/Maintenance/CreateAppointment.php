<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CreateAppointment extends Component
{
    public $company_id;
    public $technician_id;
    public $type = 'routine';
    public $description;
    public $date_planned;

    public $companies = [];
    public $technicians = [];

    public function mount()
    {
        $this->companies   = Company::orderBy('name')->get();
        $this->technicians = User::whereHas('role', fn($q) =>
            $q->whereIn('name', ['Maintenance', 'MaintenanceManager'])
        )->get();
    }

   public function save()
{
    $this->validate([
        'company_id'   => 'required|exists:companies,id',
        'type'         => 'required|in:storing,routine,installation',
        'date_planned' => 'required|date',
    ]);

    $appointment = Appointment::create([
    'company_id'              => $this->company_id,
    'technician_id'           => $this->technician_id,
    'type'                    => $this->type,
    'description' => $this->description,
    'date_planned'            => $this->date_planned,
    'date_added'              => now(),
    'status'                  => $this->technician_id ? 'planned' : 'open',
]);

// FIX → koppel werkbon aan afspraak
\App\Models\WorkOrder::create([
    'appointment_id' => $appointment->id,
    'technician_id'  => $appointment->technician_id,
]);
    session()->flash('success', 'Afspraak succesvol opgeslagen!');

    return redirect()->route('maintenance.planning');
}


    public function render()
    {
        return view('livewire.maintenance.create-appointment');
    }
}
