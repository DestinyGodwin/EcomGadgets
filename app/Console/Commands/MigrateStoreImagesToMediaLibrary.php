<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Throwable;

class MigrateStoreImagesToMediaLibrary extends Command
{
    protected $signature = 'media:migrate-store-images {--force}';
    protected $description = 'Migrate old store images to Spatie Media Library';

    public function handle(): int
    {
        $total = Store::count();

        $this->info("Starting store image migration ({$total} records)");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $stats = [
            'migrated' => 0,
            'skipped'  => 0,
            'missing'  => 0,
            'errors'   => 0,
        ];

        Store::chunkById(50, function ($stores) use (&$stats, $bar) {
            foreach ($stores as $store) {
                try {
                    if (
                        $store->getMedia('store_image')->isNotEmpty()
                        && !$this->option('force')
                    ) {
                        $stats['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    if (empty($store->store_image)) {
                        $stats['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    $path = storage_path('app/public/' . $store->store_image);

                    if (!is_file($path)) {
                        $stats['missing']++;
                        $this->warn("Missing file for store {$store->id}");
                        $bar->advance();
                        continue;
                    }

                    $store
                        ->addMedia($path)
                        ->preservingOriginal()
                        ->toMediaCollection('store_image', 'public');

                    $stats['migrated']++;
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->error("Error on store {$store->id}: {$e->getMessage()}");
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Result', 'Count'],
            [
                ['Migrated', $stats['migrated']],
                ['Skipped',  $stats['skipped']],
                ['Missing',  $stats['missing']],
                ['Errors',   $stats['errors']],
            ]
        );

        $this->info('Store image migration completed.');

        return Command::SUCCESS;
    }
}
