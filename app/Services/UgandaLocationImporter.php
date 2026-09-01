<?php

namespace App\Services;

use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use App\Models\Village;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UgandaLocationImporter
{
    /**
     * @param  array<int, array<string, mixed>>  $districts
     * @return array{districts: int, counties: int, sub_counties: int, parishes: int, villages: int}
     */
    public function import(array $districts): array
    {
        $counts = ['districts' => 0, 'counties' => 0, 'sub_counties' => 0, 'parishes' => 0, 'villages' => 0];

        DB::transaction(function () use ($districts, &$counts): void {
            foreach ($districts as $districtData) {
                $district = District::updateOrCreate(
                    $this->identity($districtData),
                    $this->attributes($districtData),
                );
                $counts['districts']++;

                foreach ($this->children($districtData, 'counties') as $countyData) {
                    $county = County::updateOrCreate(
                        ['district_id' => $district->id, ...$this->identity($countyData)],
                        $this->attributes($countyData),
                    );
                    $counts['counties']++;

                    foreach ($this->children($countyData, 'sub_counties') as $subCountyData) {
                        $subCounty = SubCounty::updateOrCreate(
                            ['county_id' => $county->id, ...$this->identity($subCountyData)],
                            $this->attributes($subCountyData),
                        );
                        $counts['sub_counties']++;

                        foreach ($this->children($subCountyData, 'parishes') as $parishData) {
                            $parish = Parish::updateOrCreate(
                                ['sub_county_id' => $subCounty->id, ...$this->identity($parishData)],
                                $this->attributes($parishData),
                            );
                            $counts['parishes']++;

                            foreach ($this->children($parishData, 'villages') as $villageData) {
                                Village::updateOrCreate(
                                    ['parish_id' => $parish->id, ...$this->identity($villageData)],
                                    $this->attributes($villageData),
                                );
                                $counts['villages']++;
                            }
                        }
                    }
                }
            }
        });

        return $counts;
    }

    /** @param array<string, mixed> $location */
    private function identity(array $location): array
    {
        $name = trim((string) ($location['name'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException('Every location entry must have a non-empty name.');
        }

        $code = $this->nullableString($location['code'] ?? null);

        return $code === null ? ['name' => $name] : ['code' => $code];
    }

    /** @param array<string, mixed> $location */
    private function attributes(array $location): array
    {
        return [
            'name' => trim((string) $location['name']),
            'code' => $this->nullableString($location['code'] ?? null),
            'is_active' => filter_var($location['is_active'] ?? true, FILTER_VALIDATE_BOOL),
        ];
    }

    /**
     * @param  array<string, mixed>  $location
     * @return array<int, array<string, mixed>>
     */
    private function children(array $location, string $key): array
    {
        $children = $location[$key] ?? [];

        if (! is_array($children)) {
            throw new InvalidArgumentException("The {$key} value must be an array.");
        }

        foreach ($children as $child) {
            if (! is_array($child)) {
                throw new InvalidArgumentException("Every {$key} entry must be an object.");
            }
        }

        return $children;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
