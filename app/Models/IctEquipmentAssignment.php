<?php

namespace App\Models;

use App\Models\Concerns\HasRecordProvenance;
use Database\Factories\IctEquipmentAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_id', 'computer_lab_id', 'custodian', 'assigned_by', 'assigned_at', 'returned_at', 'notes', 'created_by', 'updated_by'])]
class IctEquipmentAssignment extends Model
{
    /** @use HasFactory<IctEquipmentAssignmentFactory> */
    use HasFactory, HasRecordProvenance;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function computerLab(): BelongsTo
    {
        return $this->belongsTo(ComputerLab::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'returned_at' => 'datetime'];
    }
}
