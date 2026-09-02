<?php

namespace App\Services;

use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use SplFileObject;

/**
 * Import a flat CSV into the District-to-Village relational hierarchy.
 *
 * The importer resolves or creates every parent before its child so each database row
 * retains the correct administrative lineage.
 */
class UgandaLocationCsvImporter
{
    /**
     * Import readable, verified location rows in one atomic database transaction.
     *
     * @return array{districts: int, counties: int, sub_counties: int, parishes: int, villages: int}
     */
    public function import(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException("The Uganda location CSV is not readable: {$path}");
        }

        return DB::transaction(function () use ($path): array {
            // Cache resolved IDs by their parent path to avoid querying the same hierarchy repeatedly.
            $districtIds = [];
            $countyIds = [];
            $subCountyIds = [];
            $parishIds = [];
            $villageBuffer = [];
            $insertedVillages = 0;
            $timestamp = now();
            $file = new SplFileObject($path);
            $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
            $header = $file->fgetcsv();

            if (! is_array($header)) {
                throw new InvalidArgumentException('The Uganda location CSV does not contain a header row.');
            }

            $header[0] = ltrim((string) $header[0], "\xEF\xBB\xBF");
            $requiredColumns = ['district', 'county', 'constituency', 'subcounty', 'parish', 'village', 'confidence'];
            $missingColumns = array_diff($requiredColumns, $header);

            if ($missingColumns !== []) {
                throw new InvalidArgumentException('The Uganda location CSV is missing columns: '.implode(', ', $missingColumns));
            }

            while (! $file->eof()) {
                $values = $file->fgetcsv();

                if (! is_array($values) || $values === [null]) {
                    continue;
                }

                $values = array_pad($values, count($header), null);
                $row = array_combine($header, array_slice($values, 0, count($header)));

                if (! is_array($row)) {
                    throw new InvalidArgumentException('A Uganda location CSV row could not be parsed.');
                }

                $districtName = $this->requiredName($row, 'district', $file->key());
                $countyName = $this->countyName($row, $file->key());
                $subCountyName = $this->requiredName($row, 'subcounty', $file->key());
                $parishName = $this->requiredName($row, 'parish', $file->key());
                $villageName = $this->requiredName($row, 'village', $file->key());

                // Unverified source rows are intentionally excluded from operational dropdowns.
                if (($row['confidence'] ?? null) !== 'verified') {
                    continue;
                }

                $districtId = $districtIds[$districtName] ??= District::firstOrCreate(
                    ['name' => $districtName],
                    ['code' => null, 'is_active' => true],
                )->id;
                $countyKey = "{$districtId}|{$countyName}";
                $countyId = $countyIds[$countyKey] ??= County::firstOrCreate(
                    ['district_id' => $districtId, 'name' => $countyName],
                    ['code' => null, 'is_active' => true],
                )->id;
                $subCountyKey = "{$countyId}|{$subCountyName}";
                $subCountyId = $subCountyIds[$subCountyKey] ??= SubCounty::firstOrCreate(
                    ['county_id' => $countyId, 'name' => $subCountyName],
                    ['code' => null, 'is_active' => true],
                )->id;
                $parishKey = "{$subCountyId}|{$parishName}";
                $parishId = $parishIds[$parishKey] ??= Parish::firstOrCreate(
                    ['sub_county_id' => $subCountyId, 'name' => $parishName],
                    ['code' => null, 'is_active' => true],
                )->id;

                $villageBuffer[] = [
                    'parish_id' => $parishId,
                    'name' => $villageName,
                    'code' => null,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];

                // Batch leaf records to keep memory and database round trips bounded for the large village set.
                if (count($villageBuffer) === 500) {
                    $insertedVillages += DB::table('villages')->insertOrIgnore($villageBuffer);
                    $villageBuffer = [];
                }
            }

            if ($villageBuffer !== []) {
                $insertedVillages += DB::table('villages')->insertOrIgnore($villageBuffer);
            }

            return [
                'districts' => count($districtIds),
                'counties' => count($countyIds),
                'sub_counties' => count($subCountyIds),
                'parishes' => count($parishIds),
                'villages' => $insertedVillages,
            ];
        });
    }

    /**
     * Use the constituency name when the source has no separate county value.
     *
     * @param  array<string, string|null>  $row
     */
    private function countyName(array $row, int $line): string
    {
        $county = trim((string) ($row['county'] ?? ''));

        return $county !== '' ? $county : $this->requiredName($row, 'constituency', $line);
    }

    /**
     * Normalize a required source value and identify its CSV line when invalid.
     *
     * @param  array<string, string|null>  $row
     */
    private function requiredName(array $row, string $column, int $line): string
    {
        $name = trim((string) ($row[$column] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException("The {$column} value is missing on CSV line {$line}.");
        }

        return $name;
    }
}
