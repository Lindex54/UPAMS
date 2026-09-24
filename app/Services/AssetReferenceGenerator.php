<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Str;

class AssetReferenceGenerator
{
    public function generateIctReference(): string
    {
        do {
            $reference = 'ICT-'.now()->format('Y').'-'.Str::upper(Str::random(10));
        } while (Asset::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
