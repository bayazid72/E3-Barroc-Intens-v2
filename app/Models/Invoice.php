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
        'invoice_number',
        'payment_method',
        'payment_reference',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate invoice number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!isset($model->type)) {
                $model->type = 'invoice';
            }
            
            if ($model->type === 'invoice' && !isset($model->invoice_number)) {
                $model->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    /**
     * Global scope to only retrieve invoices (not quotes)
     */
    protected static function booted()
    {
        static::addGlobalScope('invoice', function ($builder) {
            $builder->where('type', 'invoice');
        });
    }

    /**
     * Generate next invoice number
     * 
     * @return string
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = self::withoutGlobalScope('invoice')
            ->where('type', 'invoice')
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice && preg_match('/INV-' . $year . '-(\d+)/', $lastInvoice->invoice_number ?? '', $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        return sprintf('INV-%s-%04d', $year, $number);
    }

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

    /**
     * Get the payment status for display
     * 
     * @return string
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'paid' || $this->paid_at !== null) {
            return 'paid';
        }

        // Check if overdue (30 days after invoice date)
        if ($this->invoice_date->addDays(30)->isPast()) {
            return 'overdue';
        }

        return 'open';
    }

    /**
     * Get the status color for display
     * 
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'green',
            'overdue' => 'red',
            'open' => 'yellow',
            default => 'zinc',
        };
    }

    /**
     * Get the status label for display
     * 
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'Betaald',
            'overdue' => 'Te Laat',
            'open' => 'Open',
            default => 'Onbekend',
        };
    }

    /**
     * Check if invoice is paid
     * 
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if invoice is overdue
     * 
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->payment_status === 'overdue';
    }

    /**
     * Mark invoice as paid
     * 
     * @param string|null $paymentMethod
     * @param string|null $paymentReference
     * @return void
     */
    public function markAsPaid(?string $paymentMethod = null, ?string $paymentReference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);

        // Dispatch event for notifications
        event(new \App\Events\InvoicePaid($this));
    }
}
