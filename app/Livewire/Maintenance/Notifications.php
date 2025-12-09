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
        $query = Notification::where('type', 'workorder')
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
            $n->data = json_decode($n->message);
            return $n;
        });

        return view('livewire.maintenance.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
