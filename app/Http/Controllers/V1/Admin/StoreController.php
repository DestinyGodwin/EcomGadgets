<?php

namespace App\Http\Controllers\V1\Admin;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\V1\Stores\StoreApprovedNotification;
use App\Notifications\V1\Stores\StoreDeclinedNotification;

class StoreController extends Controller
{
    public function approve(Store $store)
{
   $store->update(['status' => 'approved', 'is_active' => true, ]); 
    $store->user->notify(new StoreApprovedNotification($store));
    return response()->json(['message' => 'Store approved successfully.']);
}

public function decline(Request $request, Store $store)
{
    $request->validate([
        'reason' => ['required', 'string', 'min:5'],
    ]);

    $store->update(['status' => 'declined']);
    $store->user->notify(new StoreDeclinedNotification($store, $request->reason));
    return response()->json(['message' => 'Store declined with message sent.']);

}
}