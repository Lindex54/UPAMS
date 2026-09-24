<?php

namespace App\Models;

use App\Models\Concerns\HasRecordProvenance;
use Database\Factories\ComputerLabFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'campus_id', 'org_unit_id', 'building', 'room', 'capacity', 'responsible_technician_id', 'created_by', 'updated_by'])]
class ComputerLab extends Model
{
    /** @use HasFactory<ComputerLabFactory> */
    use HasFactory, HasRecordProvenance;

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function responsibleTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_technician_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(IctInspection::class);
    }
}
