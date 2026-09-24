<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable([
    'reference', 'beneficiary_id', 'campus_id', 'property_reference', 'asset_reference',
    'agreement_reference', 'issue_date', 'due_date', 'description', 'subtotal',
    'tax_amount', 'total_amount', 'status', 'cancellation_reason', 'cancelled_at',
    'created_by', 'updated_by',
])]
class Invoice extends Model
{
    public const AGING_BUCKETS = [
        'Under 30 Days',
        '30–60 Days',
        '60–90 Days',
        'Over 90 Days',
    ];

    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function validPayments(): HasMany
    {
        return $this->payments()->valid();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function paidAmount(): float
    {
        return (float) ($this->payments_sum_amount
            ?? $this->validPayments()->sum('amount'));
    }

    public function balance(): float
    {
        return max(0, (float) $this->total_amount - $this->paidAmount());
    }

    public function daysOverdue(): int
    {
        return max(0, (int) $this->due_date->diffInDays(today(), false));
    }

    public function agingBucket(): string
    {
        return match (true) {
            $this->daysOverdue() < 30 => self::AGING_BUCKETS[0],
            $this->daysOverdue() <= 60 => self::AGING_BUCKETS[1],
            $this->daysOverdue() <= 90 => self::AGING_BUCKETS[2],
            default => self::AGING_BUCKETS[3],
        };
    }

    public function paymentStatus(): string
    {
        if (in_array($this->status, ['Draft', 'Cancelled', 'Archived'], true)) {
            return $this->status;
        }

        if ($this->balance() <= 0) {
            return 'Paid';
        }

        if ($this->paidAmount() > 0) {
            return 'Partially Paid';
        }

        return $this->due_date->lt(today()) ? 'Overdue' : 'Issued';
    }

    protected static function booted(): void
    {
        static::deleting(fn (): never => throw new LogicException('Invoices must be cancelled or archived, not deleted.'));
    }

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'last_payment_at' => 'datetime',
        ];
    }
}
