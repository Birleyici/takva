<?php

use App\Services\Legacy\LegacyImportService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('legacy:import {--only=} {--fresh : Truncate target tables before import} {--dry-run : Only validate the legacy connection}', function (LegacyImportService $importer) {
    $only = $this->option('only');
    $sections = [];

    if (is_string($only) && trim($only) !== '') {
        $sections = array_values(array_filter(array_map('trim', preg_split('/[,;]+/', $only))));
    }

    $fresh = (bool) $this->option('fresh');
    $dryRun = (bool) $this->option('dry-run');

    try {
        if ($dryRun) {
            $stats = $importer->testConnection();
            $this->info('Legacy database connection looks good.');
            foreach ($stats as $section => $count) {
                $this->line(' - '.ucfirst($section).": {$count}");
            }

            return;
        }

        $this->info('Starting legacy import...');

        $results = $importer->import($sections, $fresh, function (string $message): void {
            $this->line($message);
        });

        $this->info('Legacy import completed.');
        foreach ($results as $section => $count) {
            $this->line(' - '.ucfirst($section).": {$count}");
        }
    } catch (\Throwable $exception) {
        $this->error('Legacy import failed: '.$exception->getMessage());
        throw $exception;
    }
})->purpose('Import content from the legacy Takva site dump');
