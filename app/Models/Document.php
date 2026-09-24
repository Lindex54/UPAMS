<?php

namespace App\Models;

use App\Models\Concerns\HasRecordProvenance;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['reference', 'title', 'document_type', 'related_reference', 'file_path', 'asset_id', 'campus_id', 'org_unit_id', 'status', 'issued_at', 'expires_at', 'created_by', 'updated_by'])]
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory, HasRecordProvenance;

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    protected function casts(): array
    {
        return ['issued_at' => 'date', 'expires_at' => 'date'];
    }
}
