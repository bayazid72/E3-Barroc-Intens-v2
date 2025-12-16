<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class ViewAppointment extends Component
{
    public Appointment $appointment;

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function takeOver(): void
    {
        if ($this->appointment->status !== 'sick') {
            return;
        }

        $this->appointment->update([
            'technician_id' => Auth::id(),
            'status' => 'planned',
        ]);

        session()->flash('success', 'Afspraak overgenomen.');

        redirect()->route('maintenance.planning');
    }

    public function render()
    {
        return view('livewire.maintenance.view-appointment');
    }
}
