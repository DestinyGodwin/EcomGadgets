<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckFeaturedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-featured-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable featured status for expired products';

    /**
     * Execute the console command.
     */
     public function handle()
    {
        $count = Product::query()
            ->where('is_featured', true)
            ->where('featured_expires_at', '<=', now())
            ->update(['is_featured' => false]);

        $this->info("Disabled featured status for {$count} products.");
        Log::info("Featured products check: Disabled {$count} expired features.");
    }
}
