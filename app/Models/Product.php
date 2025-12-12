<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'price',
        'product_category_id',
        'supplier',
    ];

    protected $appends = [
        'stock',
        'is_available',
        'availability_label',
    ];

    /* ======================
     * Relaties
     * ====================== */

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function invoiceLines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /* ======================
     * Voorraad / status
     * ====================== */

    /**
     * Huidige voorraad = som van alle movements
     */
    public function getStockAttribute(): int
    {
        $movements = $this->inventoryMovements;

        return (int) $movements->sum(function ($m) {
            return match ($m->type) {
                'purchase'   => $m->quantity,      // voorraad omhoog
                'usage'      => -$m->quantity,     // voorraad omlaag
                'correction' => $m->quantity,
                default      => 0,
            };
        });
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getAvailabilityLabelAttribute(): string
    {
        return $this->is_available
            ? 'Momenteel leverbaar'
            : 'Uit voorraad';
    }

    /* ======================
     * Scopes
     * ====================== */

    /**
     * Alleen producten die klanten mogen zien (categorie is_employee_only = false)
     */
    public function scopeVisibleForCustomers($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('is_employee_only', false);
        });
    }

    /**
     * Alle producten – voor interne medewerkers (Inkoop, Maintenance, etc.)
     * Eventueel uitbreiden per rol.
     */
    public function scopeVisibleForEmployee($query)
    {
        return $query;
    }

    /* ======================
     * Business rules
     * ====================== */

    /**
     * Product mag niet verwijderd worden als het ooit besteld is.
     */
    public function canBeDeleted(): bool
    {
        return ! $this->invoiceLines()->exists();
    }

}
