<?php

namespace App\Console\Commands;

use App\Services\UgandaLocationCsvImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Signature('locations:sync-uganda {--keep-file : Keep the downloaded CSV in local storage after import}')]
#[Description('Download and import the verified Uganda administrative-location hierarchy')]
class SyncUgandaLocations extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(UgandaLocationCsvImporter $importer): int
    {
        $sourceUrl = (string) config('uganda_locations.dataset_url');
        $expectedHash = (string) config('uganda_locations.sha256');
        $storagePath = 'imports/uganda-locations-ec-july-2022.csv';

        $this->removeLegacyDevelopmentPlaceholder();
        $this->components->info('Downloading the verified Uganda administrative-location dataset...');

        try {
            $response = Http::withOptions(['verify' => config('uganda_locations.verify_tls')])
                ->connectTimeout(10)
                ->timeout(120)
                ->retry([500, 1000, 2000])
                ->get($sourceUrl)
                ->throw();
            $contents = $response->body();

            if (! hash_equals($expectedHash, hash('sha256', $contents))) {
                $this->components->error('The downloaded dataset failed its SHA-256 integrity check. Nothing was imported.');

                return self::FAILURE;
            }

            Storage::disk('local')->put($storagePath, $contents);
            $counts = $importer->import(Storage::disk('local')->path($storagePath));
        } catch (Throwable $exception) {
            report($exception);
            $this->components->error('Uganda location synchronization failed: '.$exception->getMessage());

            return self::FAILURE;
        } finally {
            if (! $this->option('keep-file')) {
                Storage::disk('local')->delete($storagePath);
            }
        }

        $this->components->info(sprintf(
            'Synchronized %d districts/cities, %d counties/constituencies, %d sub-counties/divisions, %d parishes/wards, and %d new villages/cells.',
            $counts['districts'], $counts['counties'], $counts['sub_counties'], $counts['parishes'], $counts['villages'],
        ));
        $this->line('Official source: '.config('uganda_locations.official_source_url'));
        $this->line('Dataset version: '.config('uganda_locations.version'));

        return self::SUCCESS;
    }

    private function removeLegacyDevelopmentPlaceholder(): void
    {
        DB::transaction(function (): void {
            DB::table('villages')->where('code', 'DEV-VILLAGE')->delete();
            DB::table('parishes')->where('code', 'DEV-PARISH')->delete();
            DB::table('sub_counties')->where('code', 'DEV-SUBCOUNTY')->delete();
            DB::table('counties')->where('code', 'DEV-COUNTY')->delete();
            DB::table('districts')->where('code', 'DEV-DISTRICT')->delete();
        });
    }
}
