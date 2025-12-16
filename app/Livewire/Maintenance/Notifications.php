<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Notification;

class Notifications extends Component
{
    public $searchInput = '';
    public $technicianFilterInput = '';

    public $search = '';
    public $technicianFilter = '';

    public function applyFilters()
    {
        $this->search = $this->searchInput;
        $this->technicianFilter = $this->technicianFilterInput;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->technicianFilter = '';

        $this->searchInput = '';
        $this->technicianFilterInput = '';
    }

    public function render()
    {
        $query = Notification::with([
                'workorder.technician',
                'workorder.appointment.company',
            ])
            ->where('type', 'workorder')
            ->orderBy('created_at', 'desc');

        // Zoekfilter op titel, klant, probleem, oplossing, technicus
        if ($this->search !== '') {
            $query->where('message', 'like', "%{$this->search}%");
        }

        // Filteren op technicus
        if ($this->technicianFilter !== '') {
            $query->where('message', 'like', '%"technician":"' . $this->technicianFilter . '%');
        }

        // Notificaties ophalen + JSON decoderen
        $notifications = $query->get()->map(function ($n) {
            $decoded = json_decode($n->message ?? '', false);

            if (! $decoded) {
                $decoded = (object)[];
            }

            // Normalize to object so property access stays safe in the view
            if (is_array($decoded)) {
                $decoded = (object)$decoded;
            }

            if (! isset($decoded->materials) || ! is_array($decoded->materials)) {
                $decoded->materials = [];
            }

            $decoded->technician   = $decoded->technician   ?? $n->workorder?->technician?->name;
            $decoded->company      = $decoded->company      ?? $n->workorder?->appointment?->company?->name;
            $decoded->date         = $decoded->date         ?? optional($n->workorder?->appointment?->date_planned ?? $n->created_at)->toDateTimeString();
            $decoded->problem      = $decoded->problem      ?? $n->message;
            $decoded->solution     = $decoded->solution     ?? $n->workorder?->solution;
            $decoded->workorder_id = $decoded->workorder_id ?? $n->work_order_id;

            $n->data = $decoded;
            return $n;
        });

        return view('livewire.maintenance.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
