<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'description',
        'additional_notes',
        'issue_date',
        'due_date',
        'total_amount',
        'status',
        'attachment_path',
        'archivo_generado_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'status' => 'string',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get the PDF URL if it exists
     */
    public function getPdfUrl(): ?string
    {
        if ($this->archivo_generado_path) {
            $disk = config('filesystems.default', 'local');
            return \Storage::disk($disk)->url($this->archivo_generado_path);
        }
        return null;
    }

    /**
     * Check if PDF exists
     */
    public function hasPdf(): bool
    {
        if (!$this->archivo_generado_path) {
            return false;
        }
        
        $disk = config('filesystems.default', 'local');
        return \Storage::disk($disk)->exists($this->archivo_generado_path);
    }

    /**
     * Get PDF file size in human readable format
     */
    public function getPdfSize(): ?string
    {
        if (!$this->hasPdf()) {
            return null;
        }

        $disk = config('filesystems.default', 'local');
        $bytes = \Storage::disk($disk)->size($this->archivo_generado_path);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}