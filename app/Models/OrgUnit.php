<?php

namespace App\Models;

use Database\Factories\OrgUnitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class OrgUnit extends Model
{
    /** @use HasFactory<OrgUnitFactory> */
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function computerLabs(): HasMany
    {
        return $this->hasMany(ComputerLab::class);
    }
}
