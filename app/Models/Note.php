<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'note',
        'type',
        'follow_up_at',
        'date',
        'company_id',
        'author_id'
    ];

    protected $casts = [
        'follow_up_at' => 'datetime',
        'date'         => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
