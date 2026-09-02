<?php

namespace App\Models;

use Database\Factories\ParishFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Parish or ward belonging to a sub-county and containing villages. */
#[Fillable(['sub_county_id', 'name', 'code', 'is_active'])]
class Parish extends Model
{
    /** @use HasFactory<ParishFactory> */
    use HasFactory;

    public function subCounty(): BelongsTo
    {
        return $this->belongsTo(SubCounty::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
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
