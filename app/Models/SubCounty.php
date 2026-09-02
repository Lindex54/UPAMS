<?php

namespace App\Models;

use Database\Factories\SubCountyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Sub-county or division belonging to a county and containing parishes. */
#[Fillable(['county_id', 'name', 'code', 'is_active'])]
class SubCounty extends Model
{
    /** @use HasFactory<SubCountyFactory> */
    use HasFactory;

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function parishes(): HasMany
    {
        return $this->hasMany(Parish::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
