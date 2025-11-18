<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;

class Malfunctions extends Component
{
    public function render()
    {
        return view('livewire.maintenance.malfunctions', [
            'malfunctions' => Appointment::with('company')
                ->where('type', 'malfunction')
                ->orderBy('date_added', 'desc')
                ->get(),
        ]);
    }
}
