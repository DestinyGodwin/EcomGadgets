<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateProductImagesToMediaLibrary extends Command
{
    protected $signature = 'media:migrate-product-images {--force}';
    protected $description = 'Migrate old product images to Spatie Media Library';

    public function handle(): int
    {
        $this->info('Starting product image migration...');

        Product::with('images')->chunk(50, function ($products) {
            foreach ($products as $product) {

                if ($product->getMedia('images')->isNotEmpty() && !$this->option('force')) {
                    continue;
                }

                foreach ($product->images as $image) {
                    $path = storage_path('app/public/' . $image->image_path);

                    if (!file_exists($path)) {
                        $this->warn("Missing file: {$path}");
                        continue;
                    }

                    $product
    ->addMedia($path)
    ->preservingOriginal()
    ->toMediaCollection('images', 'public');

                }
            }
        });

        $this->info('Migration completed.');
        return Command::SUCCESS;
    }
}
