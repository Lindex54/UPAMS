<?php

namespace App\Models;

use Database\Factories\AgreementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['reference', 'title', 'campus_id', 'org_unit_id', 'status', 'starts_at', 'expires_at', 'created_by', 'updated_by'])]
class Agreement extends Model
{
    /** @use HasFactory<AgreementFactory> */
    use HasFactory;

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'expires_at' => 'date'];
    }
}
