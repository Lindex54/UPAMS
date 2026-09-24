<?php

namespace App\Models;

use Database\Factories\AssetCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class AssetCategory extends Model
{
    /** @use HasFactory<AssetCategoryFactory> */
    use HasFactory;

    public function types(): HasMany
    {
        return $this->hasMany(AssetType::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
