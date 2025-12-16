<?php

namespace App\Livewire\Maintenance;
use Illuminate\Support\Facades\Auth;

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



        public function markSick(int $appointmentId): void
        {
            $appointment = Appointment::where('id', $appointmentId)
                ->where('technician_id', Auth::id()) // alleen eigen afspraak
                ->where('status', 'planned')
                ->firstOrFail();

            $appointment->update([
                'status' => 'sick',
                'technician_id' => null, // afspraak vrijgeven
            ]);

            session()->flash('success', 'Je bent ziek gemeld. De afspraak is vrijgegeven.');
        }

}
