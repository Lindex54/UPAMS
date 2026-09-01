<?php

namespace App\Console\Commands;

use App\Services\UgandaLocationImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use JsonException;
use Throwable;

#[Signature('locations:import-uganda {path : Path to a reviewed nested JSON location dataset}')]
#[Description('Import a reviewed Uganda administrative-location JSON dataset')]
class ImportUgandaLocations extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(UgandaLocationImporter $importer): int
    {
        $path = (string) $this->argument('path');

        if (! is_file($path) || ! is_readable($path)) {
            $this->error("The location dataset could not be read: {$path}");

            return self::FAILURE;
        }

        try {
            $payload = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            $districts = is_array($payload) ? ($payload['districts'] ?? null) : null;

            if (! is_array($districts)) {
                $this->error('The JSON root must contain a districts array.');

                return self::FAILURE;
            }

            $counts = $importer->import($districts);
        } catch (JsonException $exception) {
            $this->error('The dataset is not valid JSON: '.$exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->error('The import failed and was rolled back: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Imported %d districts, %d counties, %d sub-counties, %d parishes, and %d villages.',
            $counts['districts'], $counts['counties'], $counts['sub_counties'], $counts['parishes'], $counts['villages'],
        ));

        return self::SUCCESS;
    }
}
