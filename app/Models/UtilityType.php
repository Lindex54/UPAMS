<?php

namespace App\Models;

use Database\Factories\UtilityTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'unit', 'default_rate', 'is_active', 'created_by', 'updated_by'])]
class UtilityType extends Model
{
    /** @use HasFactory<UtilityTypeFactory> */
    use HasFactory;

    public function meters(): HasMany
    {
        return $this->hasMany(UtilityMeter::class);
    }

    protected function casts(): array
    {
        return [
            'default_rate' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }
}
