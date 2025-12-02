<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\Notification;

class Notifications extends Component
{
    public function render()
    {
        $notifications = Notification::where('type', 'workorder')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                $n->data = json_decode($n->message);
                return $n;
            });

        return view('livewire.maintenance.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
