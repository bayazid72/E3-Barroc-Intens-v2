<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'company_id','contract_id','invoice_date',
        'total_amount','paid_at','type','is_sent'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }
}
