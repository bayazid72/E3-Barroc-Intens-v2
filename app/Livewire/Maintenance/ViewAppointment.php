<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;

class ViewAppointment extends Component
{
    public Appointment $appointment;

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function render()
    {
        return view('livewire.maintenance.view-appointment');
    }
}
