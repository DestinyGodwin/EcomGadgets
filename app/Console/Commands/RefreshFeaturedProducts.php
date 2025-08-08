<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use App\Models\FeaturedProductSubscription;

class RefreshFeaturedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-featured-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh featured products updated_at timestamps based on their subscription refresh intervals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting featured products refresh...');

        $subscriptions = FeaturedProductSubscription::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->with(['plan', 'products'])
            ->get();

        $refreshedCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($subscription->needsRefresh()) {
                $this->info("Refreshing subscription {$subscription->id} for plan {$subscription->plan->name}");
                
                // Update the updated_at timestamps of products in this subscription
                $productIds = $subscription->products()->pluck('products.id');
                
                if ($productIds->isNotEmpty()) {
                    Product::whereIn('id', $productIds)
                        ->update(['updated_at' => now()]);
                    
                    $this->info("Updated {$productIds->count()} products");
                }

                $subscription->update(['last_refreshed_at' => now()]);
                $refreshedCount++;
            }
        }

        $this->info("Refresh completed. {$refreshedCount} subscriptions were refreshed.");
        return 0;
    }
}
