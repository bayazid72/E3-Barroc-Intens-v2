<?php

namespace App\Notifications;

use App\Models\InventoryMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductUsedNotification extends Notification
{
    use Queueable;

    public function __construct(public InventoryMovement $movement) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Product gebruikt door Maintenance")
            ->line("Een monteur heeft materialen gebruikt.")
            ->line("Product: " . $this->movement->product->name)
            ->line("Aantal: " . $this->movement->quantity)
            ->line("Werkbon ID: " . $this->movement->related_work_order_id)
            ->line("Door gebruiker: " . $this->movement->user->name)
            ->line("Datum: " . $this->movement->created_at->format('d-m-Y H:i'));
    }

    public function toArray($notifiable)
    {
        return [
            'product' => $this->movement->product->name,
            'quantity' => $this->movement->quantity,
            'workorder' => $this->movement->related_work_order_id,
        ];
    }
}
