<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\FeaturedProduct;
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
    // public function handle()
    // {
    //     $this->info('Starting featured products refresh...');

    //     $subscriptions = FeaturedProductSubscription::where('is_active', true)
    //         ->where(function ($query) {
    //             $query->whereNull('ends_at')
    //                   ->orWhere('ends_at', '>', now());
    //         })
    //         ->with(['plan', 'products'])
    //         ->get();

    //     $refreshedCount = 0;

    //     foreach ($subscriptions as $subscription) {
    //         if ($subscription->needsRefresh()) {
    //             $this->info("Refreshing subscription {$subscription->id} for plan {$subscription->plan->name}");
                
    //             // Update the updated_at timestamps of products in this subscription
    //             $productIds = $subscription->products()->pluck('products.id');
                
    //             if ($productIds->isNotEmpty()) {
    //                 Product::whereIn('id', $productIds)
    //                     ->update(['updated_at' => now()]);
                    
    //                 $this->info("Updated {$productIds->count()} products");
    //             }

    //             $subscription->update(['last_refreshed_at' => now()]);
    //             $refreshedCount++;
    //         }
    //     }

    //     $this->info("Refresh completed. {$refreshedCount} subscriptions were refreshed.");
    //     return 0;
    // }

     public function handle(): void
    {
        $this->info('=== Featured Products Refresh Started ===');
        $now = Carbon::now();

        $totalChecked = 0;
        $totalRefreshed = 0;
        $subscriptionsAffected = []; 
        $plansRefreshed = [];       
        $skipped = 0;

        FeaturedProduct::with(['subscription.plan'])
            ->chunk(200, function ($featuredProducts) use ($now, &$totalChecked, &$totalRefreshed, &$subscriptionsAffected, &$plansRefreshed, &$skipped) {
                foreach ($featuredProducts as $fp) {
                    $totalChecked++;

                    // defensive checks: keep original behaviour but avoid fatal errors if relationship missing
                    $subscription = $fp->subscription ?? null;
                    $plan = $subscription->plan ?? null;

                    if (! $subscription || ! $plan || empty($plan->refresh_interval_minutes)) {
                        $skipped++;
                        continue;
                    }

                    $interval = (int) $plan->refresh_interval_minutes;

                    // original behaviour: refresh if updated_at older than interval
                    if ($fp->updated_at->diffInMinutes($now) >= $interval) {
                        $fp->touch(); // updates updated_at

                        $totalRefreshed++;

                        // track subscription-level counts
                        $subId = $subscription->id;
                        if (! isset($subscriptionsAffected[$subId])) {
                            $subscriptionsAffected[$subId] = [
                                'plan' => $plan->name ?? 'unknown',
                                'count' => 0,
                            ];
                        }
                        $subscriptionsAffected[$subId]['count']++;

                        // track plan-level totals
                        $planName = $plan->name ?? 'unknown';
                        $plansRefreshed[$planName] = ($plansRefreshed[$planName] ?? 0) + 1;
                    }
                }
            });

        $this->info("Checked {$totalChecked} featured products.");
        $this->info("Refreshed {$totalRefreshed} products across " . count($subscriptionsAffected) . " subscriptions.");

        if (! empty($plansRefreshed)) {
            $this->info('Breakdown by plan:');
            foreach ($plansRefreshed as $planName => $count) {
                $this->line("  - {$planName}: {$count}");
            }
        }

        if ($skipped > 0) {
            $this->warn("Skipped {$skipped} featured products due to missing subscription/plan or missing interval.");
        }

        $this->info('=== Featured Products Refresh Completed at ' . $now->toDateTimeString() . ' ===');
    }
}
