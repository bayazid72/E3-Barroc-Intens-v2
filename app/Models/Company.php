<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name','phone','street','house_number','zip','city',
        'country_code','bkr_checked','contact_id'
    ];

    public function contact()
    {
        return $this->belongsTo(User::class, 'contact_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
