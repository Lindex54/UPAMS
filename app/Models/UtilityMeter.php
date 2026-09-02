<?php

namespace App\Models;

use Database\Factories\UtilityMeterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'reference', 'utility_type_id', 'campus_id', 'beneficiary_id', 'property_reference',
    'meter_number', 'unit', 'rate', 'abnormal_threshold', 'status', 'created_by', 'updated_by',
])]
class UtilityMeter extends Model
{
    /** @use HasFactory<UtilityMeterFactory> */
    use HasFactory;

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

    public function billings(): HasMany
    {
        return $this->hasMany(UtilityBilling::class);
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
            'rate' => 'decimal:4',
            'abnormal_threshold' => 'decimal:4',
        ];
    }
}
