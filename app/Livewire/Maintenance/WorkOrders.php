<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\WorkOrder;

class WorkOrders extends Component
{
    public function render()
    {
        return view('livewire.maintenance.work-orders', [
            'orders' => WorkOrder::with(['appointment.company', 'technician'])
                ->latest()
                ->get(),
        ]);
    }
}
