<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\WorkOrder;

class MaintenanceDashboard extends Component
{
    public function render()
    {
        return view('livewire.maintenance.dashboard', [
            'openMalfunctions'   => Appointment::where('type', 'malfunction')->where('status', 'open')->count(),
            'todayAppointments'  => Appointment::whereDate('date_planned', today())->count(),
            'doneWorkOrders'     => WorkOrder::count(),
        ]);
    }
}
