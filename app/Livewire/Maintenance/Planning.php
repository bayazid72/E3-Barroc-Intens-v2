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

    /**
     * Events voor FullCalendar
     */
    public function getEvents(): array
    {
        return Appointment::with(['company', 'technician'])
            ->orderBy('date_planned')
            ->get()
            ->map(function ($a) {
                return [
                    'id'    => $a->id,
                    'title' => $a->company->name . ' - ' . ucfirst($a->type),
                    'start' => optional($a->date_planned)->format('Y-m-d H:i:s'),

                    // 👇 rood bij ziekmelding
                    'backgroundColor' => $a->status === 'sick' ? '#dc2626' : '#2563eb',
                    'borderColor'     => $a->status === 'sick' ? '#dc2626' : '#2563eb',

                    'extendedProps' => [
                        'technician' => $a->technician?->name ?? 'Niet toegewezen',
                        'status'     => $a->status,
                    ],
                ];
            })
            ->values()
            ->toArray();
    }
}
