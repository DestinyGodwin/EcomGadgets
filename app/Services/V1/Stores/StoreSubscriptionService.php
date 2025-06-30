<?php

namespace App\Services\V1\Stores;

use Exception;
use App\Models\StoreSubscription;
use Illuminate\Support\Facades\Auth;

class StoreSubscriptionService
{

     public function getStoreSubscriptions()
    {
        $store = Auth::user()->store;

        if (!$store) {
            throw new Exception('Store does not exist.');
        }

        return $store->subscriptions()->latest()->paginate();
    }

    public function getStoreSubscriptionById(string $subscriptionId)
    {
        $store = Auth::user()->store;

        if (!$store) {
            throw new Exception('Store does not exist.');
        }

        return $store->subscriptions()->where('id', $subscriptionId)->paginate();
    }

    public function getStoreAllSubscriptionsForAdmin()
    {
        return StoreSubscription::with('store.user')->latest()->paginate();
    }

    public function getAStoreSubscriptionForAdmin(string $subscriptionId)
    {
        return StoreSubscription::with('store.user')->findOrFail($subscriptionId);
    }

    public function getAllSubscriptions () {
       return StoreSubscription::paginate();
    }
}
