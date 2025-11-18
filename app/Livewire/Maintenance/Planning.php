<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;

class Planning extends Component
{
    public $selectedDate;

    public function mount()
    {
        $this->selectedDate = today()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.maintenance.planning', [
            'appointments' => Appointment::with(['company', 'technician'])
                ->whereDate('date_planned', $this->selectedDate)
                ->orderBy('date_planned')
                ->get(),
        ]);
    }
}
