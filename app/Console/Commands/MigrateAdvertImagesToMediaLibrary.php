<?php

namespace App\Console\Commands;

use App\Models\AdvertBooking;
use Illuminate\Console\Command;

class MigrateAdvertImagesToMediaLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
  protected $signature = 'media:migrate-advert-images {--force}';
    protected $description = 'Migrate advert images to Spatie Media Library';

    public function handle(): int
    {
        $this->info('Starting advert image migration...');

        AdvertBooking::chunk(50, function ($adverts) {
            foreach ($adverts as $advert) {

                if (
                    $advert->getMedia('images')->isNotEmpty()
                    && !$this->option('force')
                ) {
                    continue;
                }

                if (empty($advert->image)) {
                    continue;
                }

                $path = storage_path('advert_images/' . $advert->image);

                if (!is_file($path)) {
                    $this->warn("Missing file: {$path}");
                    continue;
                }

                $advert
                    ->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection('images', 'public');
            }
        });

        $this->info('Advert image migration completed.');

        return Command::SUCCESS;
    }
}
