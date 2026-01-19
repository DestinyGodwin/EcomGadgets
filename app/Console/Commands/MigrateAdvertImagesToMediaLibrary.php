<?php

namespace App\Console\Commands;

use App\Models\AdvertBooking;
use Illuminate\Console\Command;
use Throwable;

class MigrateAdvertImagesToMediaLibrary extends Command
{
    protected $signature = 'media:migrate-advert-images {--force}';
    protected $description = 'Migrate advert images to Spatie Media Library';

    public function handle(): int
    {
        $total = AdvertBooking::count();

        $this->info("Starting advert image migration ({$total} records)");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $stats = [
            'migrated' => 0,
            'skipped'  => 0,
            'missing'  => 0,
            'errors'   => 0,
        ];

        AdvertBooking::chunkById(50, function ($adverts) use (&$stats, $bar) {
            foreach ($adverts as $advert) {
                try {
                    if (
                        $advert->getMedia('images')->isNotEmpty()
                        && !$this->option('force')
                    ) {
                        $stats['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    if (empty($advert->image)) {
                        $stats['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    $path = storage_path('advert_images/' . basename($advert->image));

                    if (!is_file($path)) {
                        $stats['missing']++;
                        $this->warn("Missing file for advert {$advert->id}");
                        $bar->advance();
                        continue;
                    }

                    $advert
                        ->addMedia($path)
                        ->preservingOriginal()
                        ->toMediaCollection('images', 'public');

                    $stats['migrated']++;
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->error("Error on advert {$advert->id}: {$e->getMessage()}");
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

        $this->info('Advert image migration completed.');

        return Command::SUCCESS;
    }
}
