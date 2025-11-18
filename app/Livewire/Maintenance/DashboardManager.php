<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\WorkOrder;

class DashboardManager extends Component
{
    public int $openMalfunctions = 0;
    public int $plannedToday = 0;
    public int $finishedWorkorders = 0;

    public function mount()
    {
        // 1) Open storingen (type = malfunction + status != done)
        $this->openMalfunctions = Appointment::where('type', 'malfunction')
            ->where('status', '!=', 'done')
            ->count();

        // 2) Afspraken die vandaag gepland staan
        $this->plannedToday = Appointment::whereDate('date_planned', today())->count();

        // 3) Afgehandelde werkbonnen
        $this->finishedWorkorders = WorkOrder::whereNotNull('solution')->count();
    }

    public function render()
    {
        return view('livewire.maintenance.dashboard-manager');
    }
}
