<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeaturedProduct;
use Carbon\Carbon;

class RemoveExpiredFeaturedProducts extends Command
{
    protected $signature = 'app:remove-expired-featured-products';
    protected $description = 'Remove featured products whose subscription has expired';

    public function handle(): void
    {
        $this->info('=== Removing Expired Featured Products Started ===');

        $now = Carbon::now();
        $totalChecked = 0;
        $totalRemoved = 0;

        FeaturedProduct::with(['subscription'])
            ->chunk(200, function ($featuredProducts) use ($now, &$totalChecked, &$totalRemoved) {
                foreach ($featuredProducts as $fp) {
                    $totalChecked++;

                    $subscription = $fp->subscription ?? null;

                    if (! $subscription) {
                        // No subscription = delete immediately
                        $fp->delete();
                        $totalRemoved++;
                        continue;
                    }

                    if ($subscription->ends_at && $subscription->ends_at->lte($now)) {
                        // Subscription expired
                        $fp->delete();
                        $totalRemoved++;
                    }
                }
            });

        $this->info("Checked {$totalChecked} featured products.");
        $this->info("Removed {$totalRemoved} expired featured products.");
        $this->info('=== Removing Expired Featured Products Completed ===');
    }
}
