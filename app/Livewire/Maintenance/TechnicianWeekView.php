<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Appointment;
use Carbon\Carbon;

class TechnicianWeekView extends Component
{
    public $weekStart;

    public function mount()
    {
        $this->weekStart = today()->startOfWeek()->format('Y-m-d');
    }

    public function previousWeek()
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->format('Y-m-d');
    }

    public function nextWeek()
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->format('Y-m-d');
    }

    public function render()
    {
        $user = auth()->user();
        $start = Carbon::parse($this->weekStart)->startOfWeek();
        $end   = (clone $start)->endOfWeek();

        $appointments = Appointment::with('company')
            ->where('technician_id', $user->id)
            ->whereBetween('date_planned', [$start, $end])
            ->orderBy('date_planned')
            ->get()
            ->groupBy(fn($a) => $a->date_planned->format('Y-m-d'));

        return view('livewire.maintenance.tech-week', [
            'start'        => $start,
            'appointments' => $appointments,
        ]);
    }
}
