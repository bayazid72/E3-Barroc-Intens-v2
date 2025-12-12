<?php

namespace App\Events;

use App\Models\InventoryMovement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUsed
{
    use Dispatchable, SerializesModels;

    public InventoryMovement $movement;

    public function __construct(InventoryMovement $movement)
    {
        $this->movement = $movement;
    }
}
