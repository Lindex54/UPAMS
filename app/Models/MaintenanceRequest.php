<?php

namespace App\Models;

use App\Models\Concerns\HasRecordProvenance;
use Database\Factories\MaintenanceRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['reference', 'title', 'fault_description', 'diagnosis', 'repair_notes', 'asset_id', 'campus_id', 'org_unit_id', 'assigned_technician_id', 'priority', 'status', 'reported_at', 'diagnosed_at', 'repair_started_at', 'testing_started_at', 'resolved_at', 'target_completion_at', 'created_by', 'updated_by'])]
class MaintenanceRequest extends Model
{
    /** @use HasFactory<MaintenanceRequestFactory> */
    use HasFactory, HasRecordProvenance;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    protected function casts(): array
    {
        return [
            'target_completion_at' => 'date',
            'reported_at' => 'datetime',
            'diagnosed_at' => 'datetime',
            'repair_started_at' => 'datetime',
            'testing_started_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }
}
