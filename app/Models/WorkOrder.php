<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = [
        'appointment_id','technician_id','notes',
        'solution','materials_used'
    ];

    protected $casts = [
        'materials_used' => 'array'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'related_work_order_id');
    }
}
