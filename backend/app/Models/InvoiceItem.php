<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'name',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function calculateTaxAmount(): float
    {
        return ($this->unit_price * $this->quantity) * ($this->tax_rate / 100);
    }

    public function calculateTotalAmount(): float
    {
        $subtotal = $this->unit_price * $this->quantity;
        return $subtotal + $this->calculateTaxAmount();
    }
}