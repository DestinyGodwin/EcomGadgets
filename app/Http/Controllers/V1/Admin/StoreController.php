<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\V1\Stores\StoreApprovedNotification;

class StoreController extends Controller
{
    public function approve(Store $store)
{
    $store->update(['is_active' => true]);

    // Optionally notify the vendor
    $store->user->notify(new StoreApprovedNotification($store));

    return response()->json(['message' => 'Store approved successfully.']);
}

public function decline(Store $store)
{
    $store->delete(); // or mark as declined
    return response()->json(['message' => 'Store declined and removed.']);
}

}
