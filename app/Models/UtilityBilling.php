<?php

namespace App\Models;

use Database\Factories\UtilityBillingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reference', 'utility_meter_id', 'utility_type_id', 'campus_id', 'beneficiary_id',
    'invoice_id', 'billing_period', 'previous_reading', 'current_reading', 'consumption',
    'rate', 'charge', 'payer_name', 'payment_status', 'is_abnormal', 'notes',
    'created_by', 'updated_by',
])]
class UtilityBilling extends Model
{
    /** @use HasFactory<UtilityBillingFactory> */
    use HasFactory;

    public function meter(): BelongsTo
    {
        return $this->belongsTo(UtilityMeter::class, 'utility_meter_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(UtilityType::class, 'utility_type_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'billing_period' => 'date',
            'previous_reading' => 'decimal:4',
            'current_reading' => 'decimal:4',
            'consumption' => 'decimal:4',
            'rate' => 'decimal:4',
            'charge' => 'decimal:2',
            'is_abnormal' => 'boolean',
        ];
    }
}
