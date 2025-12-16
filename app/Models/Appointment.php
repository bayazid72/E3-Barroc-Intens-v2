<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'company_id',
        'technician_id',
        'type',
        'description',
        'date_planned',
        'date_added',
        'status',
    ];

    protected $casts = [
        'date_planned' => 'datetime',
        'date_added' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function isSick(): bool
    {
        return $this->status === 'sick';
    }

    public function isPlanned(): bool
    {
        return $this->status === 'planned';
    }


    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
