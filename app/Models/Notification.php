<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WorkOrder;

class Notification extends Model
{
    protected $fillable = [
        'user_id',          // kan NULL zijn
        'title',
        'message',
        'type',
        'work_order_id',
    ];

    public function workorder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
