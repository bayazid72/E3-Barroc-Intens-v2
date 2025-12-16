<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'related_work_order_id',
        'user_id'
    ];

    public function getSignedQuantityAttribute()
    {
        return match ($this->type) {
            'usage'      => -$this->quantity,
            'purchase'   => $this->quantity,
            'correction' => $this->quantity,
            default      => $this->quantity,
        };
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'related_work_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
