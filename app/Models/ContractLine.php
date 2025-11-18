<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractLine extends Model
{
    protected $fillable = [
        'contract_id',
        'product_id',
        'amount',
        'price_snapshot',
        'beans_per_month'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
