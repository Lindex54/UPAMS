<?php

namespace App\Models;

use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'reference', 'name', 'campus_id', 'org_unit_id', 'asset_category_id', 'asset_type_id',
    'status', 'condition', 'acquired_at', 'next_service_at', 'calibration_due_at',
    'is_ict', 'serial_number', 'make', 'model', 'processor', 'ram', 'storage', 'specifications', 'computer_lab_id',
    'building', 'room', 'latitude', 'longitude', 'custodian', 'operational_status', 'purchase_cost', 'supplier',
    'warranty_expires_at', 'created_by', 'updated_by',
])]
class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::updating(function (Asset $asset): void {
            if ($asset->is_ict && $asset->isDirty('reference')) {
                $asset->reference = $asset->getOriginal('reference');
            }
        });
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function computerLab(): BelongsTo
    {
        return $this->belongsTo(ComputerLab::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(IctEquipmentAssignment::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(IctInspection::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(IctTransferRequest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'is_ict' => 'boolean',
            'acquired_at' => 'date',
            'next_service_at' => 'date',
            'calibration_due_at' => 'date',
            'purchase_cost' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'warranty_expires_at' => 'date',
            'specifications' => 'array',
        ];
    }
}
