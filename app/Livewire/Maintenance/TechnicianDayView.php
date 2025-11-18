<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;

class TechnicianDayView extends Component
{
    public $selectedDate;

    public function mount()
    {
        $this->selectedDate = today()->format('Y-m-d');
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.maintenance.tech-day', [
            'appointments' => Appointment::with('company')
                ->where('technician_id', $user->id)
                ->whereDate('date_planned', $this->selectedDate)
                ->orderBy('date_planned')
                ->get(),
        ]);
    }
}
