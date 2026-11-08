<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Str;

class AssetReferenceGenerator
{
    public function generatePropertyReference(string $prefix): string
    {
        do {
            $reference = Str::upper($prefix).'-'.now()->format('Y').'-'.Str::upper(Str::random(8));
        } while (Asset::query()->where('reference', $reference)->exists());

        return $reference;
    }

    public function generateIctReference(): string
    {
        do {
            $reference = 'ICT-'.now()->format('Y').'-'.Str::upper(Str::random(10));
        } while (Asset::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
