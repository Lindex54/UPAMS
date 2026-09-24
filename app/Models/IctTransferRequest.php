<?php

namespace App\Models;

use App\Models\Concerns\HasRecordProvenance;
use Database\Factories\IctTransferRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['reference', 'asset_id', 'from_campus_id', 'to_campus_id', 'requested_by', 'status', 'reason', 'requested_at', 'created_by', 'updated_by'])]
class IctTransferRequest extends Model
{
    /** @use HasFactory<IctTransferRequestFactory> */
    use HasFactory, HasRecordProvenance;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromCampus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'from_campus_id');
    }

    public function toCampus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'to_campus_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    protected function casts(): array
    {
        return ['requested_at' => 'datetime'];
    }
}
