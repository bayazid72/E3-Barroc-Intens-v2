<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',   // <--- TOEGEVOEGD
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * User initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /* =========================================================================
     |  RELATIES
     ========================================================================= */

    /** User → Role (Finance, Sales, Inkoop, Maintenance, Manager) */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /** User → Notes (author) */
    public function notes()
    {
        return $this->hasMany(Note::class, 'author_id');
    }

    /** User → Companies (contactpersoon) */
    public function companies()
    {
        return $this->hasMany(Company::class, 'contact_id');
    }

    /** User → Appointments (als technicus) */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'technician_id');
    }

    /** User → Work orders (als monteur die ze uitvoert) */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'technician_id');
    }

    /** User → Inventory movements (wie voorraad heeft bijgewerkt) */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
