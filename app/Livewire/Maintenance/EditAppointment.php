<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\User;

class EditAppointment extends Component
{
    public Appointment $appointment;

    public $companies;
    public $technicians;

    public $company_id;
    public $technician_id;
    public $type;
    public $description;
    public $date_planned;
    public $status;

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;

        // alle bedrijven
        $this->companies = Company::all();

        // "monteurs" = alle MaintenanceManagers (zoals jij wilt)
        $this->technicians = User::whereHas('role', function ($q) {
            $q->where('name', 'MaintenanceManager');
        })->get();

        // formuliervelden vullen vanuit appointment
        $this->company_id              = $appointment->company_id;
        $this->technician_id           = $appointment->technician_id;
        $this->type                    = $appointment->type;
        $this->description             = $appointment->description;
        $this->date_planned            = $appointment->date_planned?->format('Y-m-d\TH:i');
        $this->status                  = $appointment->status;
    }

    public function save()
    {
        $this->validate([
            'company_id'   => 'required',
            'type'         => 'required',
            'date_planned' => 'required|date',
            'status'       => 'required',
        ]);

        $this->appointment->update([
            'company_id'              => $this->company_id,
            'technician_id'           => $this->technician_id,
            'type'                    => $this->type,
            'description'             => $this->description,
            'date_planned'            => $this->date_planned,
            'status'                  => $this->status,
        ]);

        session()->flash('success', 'Afspraak succesvol bijgewerkt.');
        return redirect()->route('maintenance.planning');
    }

    public function render()
    {
        return view('livewire.maintenance.edit-appointment');
    }
}
