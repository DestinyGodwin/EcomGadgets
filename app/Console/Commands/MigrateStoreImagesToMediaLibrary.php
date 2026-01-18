<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;

class MigrateStoreImagesToMediaLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'media:migrate-store-images {--force}';
    protected $description = 'Migrate old store images to Spatie Media Library';

    public function handle(): int
    {
        $this->info('Starting store image migration...');

        Store::chunk(50, function ($stores) {
            foreach ($stores as $store) {

                if (
                    $store->getMedia('images')->isNotEmpty()
                    && !$this->option('force')
                ) {
                    continue;
                }

                if (empty($store->image)) {
                    continue;
                }

                $path = storage_path('stores/' . $store->image);

                if (!is_file($path)) {
                    $this->warn("Missing file: {$path}");
                    continue;
                }

                $store
                    ->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection('images', 'public');
            }
        });

        $this->info('Store image migration completed.');

        return Command::SUCCESS;
    }
}
