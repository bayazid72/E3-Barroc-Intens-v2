<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'company_id',
        'starts_at',
        'ends_at',
        'invoice_type',
        'periodic_interval_months',
        'created_by',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lines()
    {
        return $this->hasMany(ContractLine::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Keur het contract goed en trigger event voor automatische appointment generatie
     */
    public function approve()
    {
        $this->update(['status' => 'active', 'activated_at' => now()]);
        event(new \App\Events\ContractApproved($this));
    }

    public function isApproved(): bool
    {
        return $this->status === 'active';
    }

    public function isDraft(): bool
    {
        return $this->status === 'pending';
    }
}
