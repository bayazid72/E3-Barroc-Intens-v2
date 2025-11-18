<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'is_employee_only'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
