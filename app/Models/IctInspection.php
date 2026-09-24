<?php

namespace App\Models;

use App\Models\Concerns\HasRecordProvenance;
use Database\Factories\IctInspectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_id', 'computer_lab_id', 'technician_id', 'condition', 'operational_status', 'findings', 'inspected_at', 'next_due_at', 'created_by', 'updated_by'])]
class IctInspection extends Model
{
    /** @use HasFactory<IctInspectionFactory> */
    use HasFactory, HasRecordProvenance;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function computerLab(): BelongsTo
    {
        return $this->belongsTo(ComputerLab::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    protected function casts(): array
    {
        return ['inspected_at' => 'datetime', 'next_due_at' => 'date'];
    }
}
