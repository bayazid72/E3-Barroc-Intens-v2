<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'amount',
        'price_snapshot',
        'delivery_status',
        'delivery_date',
        'delivery_notes',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected $casts = [
        'delivery_date' => 'date',
    ];

    /**
     * Get delivery status label
     */
    public function getDeliveryStatusLabelAttribute(): string
    {
        return match($this->delivery_status) {
            'delivered' => 'Geleverd',
            'partially_delivered' => 'Deels geleverd',
            'not_delivered' => 'Niet geleverd',
            default => 'Onbekend',
        };
    }

    /**
     * Get delivery status color
     */
    public function getDeliveryStatusColorAttribute(): string
    {
        return match($this->delivery_status) {
            'delivered' => 'green',
            'partially_delivered' => 'yellow',
            'not_delivered' => 'red',
            default => 'zinc',
        };
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(?string $notes = null): void
    {
        $this->update([
            'delivery_status' => 'delivered',
            'delivery_date' => now(),
            'delivery_notes' => $notes,
        ]);
    }

    /**
     * Check if delivered
     */
    public function isDelivered(): bool
    {
        return $this->delivery_status === 'delivered';
    }

    /**
     * Check if in backorder
     */
    public function isBackorder(): bool
    {
        return $this->delivery_status === 'not_delivered' && 
               $this->invoice->isPaid();
    }
}
