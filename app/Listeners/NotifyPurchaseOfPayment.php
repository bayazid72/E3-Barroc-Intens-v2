<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InvoicePaidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyPurchaseOfPayment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice->load(['company', 'lines.product']);

        // Get all users with purchase role
        $purchaseUsers = User::whereHas('role', function ($query) {
            $query->where('name', 'inkoop');
        })->get();

        // Send notification to each purchase user
        foreach ($purchaseUsers as $user) {
            $user->notify(new InvoicePaidNotification($invoice));
        }
    }
}
