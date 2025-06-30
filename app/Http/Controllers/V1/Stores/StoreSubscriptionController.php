<?php

namespace App\Http\Controllers\V1\Stores;

use App\Http\Controllers\Controller;
use App\Services\V1\Stores\StoreSubscriptionService;
use App\Http\Resources\V1\Stores\StoreSubscriptionResource;

class StoreSubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(StoreSubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function storeSubscriptions()
    {
        $subscriptions = $this->subscriptionService->getStoreSubscriptions();
        return StoreSubscriptionResource::collection($subscriptions);
    }

    public function storeSubscription(string $subscriptionId)
    {
        $subscription = $this->subscriptionService->getStoreSubscriptionById($subscriptionId);
        return new StoreSubscriptionResource($subscription);
    }

    public function adminSubscriptions()
    {
        $subscriptions = $this->subscriptionService->getStoreAllSubscriptionsForAdmin();
        return StoreSubscriptionResource::collection($subscriptions);
    }

    public function adminSubscription(string $subscriptionId)
    {
        $subscription = $this->subscriptionService->getAStoreSubscriptionForAdmin($subscriptionId);
        return new StoreSubscriptionResource($subscription);
    }

    public function getAll(){
        $subscriptions = $this->subscriptionService->getAllSubscriptions();
         return StoreSubscriptionResource::collection($subscriptions);

    }
}
