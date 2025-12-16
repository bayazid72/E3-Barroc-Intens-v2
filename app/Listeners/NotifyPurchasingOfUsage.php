<?php

namespace App\Listeners;

use App\Events\ProductUsed;
use App\Services\PurchasingNotifier;

class NotifyPurchasingOfUsage
{
    public function handle(ProductUsed $event)
    {
        PurchasingNotifier::notify($event->movement);
    }
}
