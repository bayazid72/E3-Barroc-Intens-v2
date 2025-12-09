<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /* ===========================
     * STATUS DEFINITIES
     * =========================== */
    public const STATUS_OPEN    = 'open';
    public const STATUS_PAID    = 'paid';
    public const STATUS_OVERDUE = 'overdue';

    public const PROC_PENDING   = 'pending';
    public const PROC_ORDERED   = 'ordered';
    public const PROC_DELIVERED = 'delivered';
    public const PROC_DONE      = 'completed';

    /* ===========================
     * MASS ASSIGNMENT
     * =========================== */
    protected $fillable = [
        'company_id',
        'contract_id',
        'invoice_date',
        'total_amount',
        'status',
        'paid_at',
        'type',
        'is_sent',
        'procurement_status',
        'stock_processed',
    ];

    /* ===========================
     * CASTS
     * =========================== */
    protected $casts = [
        'invoice_date'    => 'datetime',
        'paid_at'         => 'datetime',
        'stock_processed' => 'boolean',
    ];

    /* ===========================
     * RELATIES
     * =========================== */

    // BELANGRIJK — deze miste in jouw model!
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function invoiceLines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /* ===========================
     * COMPUTED LABELS
     * =========================== */

    // Label en kleur voor Inkoop-status
    public function getProcurementStatusLabelAttribute(): string
    {
        return match ($this->procurement_status) {
            self::PROC_PENDING   => 'Actie vereist',
            self::PROC_ORDERED   => 'In bestelling',
            self::PROC_DELIVERED => 'Geleverd',
            self::PROC_DONE      => 'Afgehandeld',
            default              => 'Onbekend',
        };
    }

    public function getProcurementStatusColorClassAttribute(): string
    {
        return match ($this->procurement_status) {
            self::PROC_PENDING   => 'bg-red-100 text-red-800',
            self::PROC_ORDERED   => 'bg-yellow-100 text-yellow-800',
            self::PROC_DELIVERED => 'bg-blue-100 text-blue-800',
            self::PROC_DONE      => 'bg-green-100 text-green-800',
            default              => 'bg-gray-100 text-gray-800',
        };
    }
}
