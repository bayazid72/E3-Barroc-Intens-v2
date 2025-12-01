<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Quote extends Model
{
    protected $table = 'invoices';
    
    protected $fillable = [
        'company_id',
        'contract_id',
        'invoice_date',
        'total_amount',
        'status',
        'type',
        'is_sent',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Boot method to set default type to 'quote'
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!isset($model->type)) {
                $model->type = 'quote';
            }
        });
    }

    /**
     * Global scope to only retrieve quotes
     */
    protected static function booted()
    {
        static::addGlobalScope('quote', function ($builder) {
            $builder->where('type', 'quote');
        });
    }

    /**
     * Relationships
     */
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
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    /**
     * Convert this quote to an invoice
     * 
     * @return Invoice
     */
    public function convertToInvoice(): Invoice
    {
        return DB::transaction(function () {
            // Create new invoice with quote data
            $invoice = Invoice::create([
                'company_id' => $this->company_id,
                'contract_id' => $this->contract_id,
                'invoice_date' => now(),
                'total_amount' => $this->total_amount,
                'status' => 'open',
                'type' => 'invoice',
                'is_sent' => false,
            ]);

            // Copy all quote lines to invoice lines
            foreach ($this->lines as $quoteLine) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $quoteLine->product_id,
                    'amount' => $quoteLine->amount,
                    'price_snapshot' => $quoteLine->price_snapshot,
                ]);
            }

            // Mark quote as converted (update status)
            $this->update([
                'status' => 'converted',
            ]);

            return $invoice;
        });
    }

    /**
     * Check if quote can be converted
     * 
     * @return bool
     */
    public function canBeConverted(): bool
    {
        return $this->status !== 'converted' && $this->lines()->exists();
    }

    /**
     * Generate next invoice number
     * 
     * @return string
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = Invoice::withoutGlobalScope('quote')
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
}
