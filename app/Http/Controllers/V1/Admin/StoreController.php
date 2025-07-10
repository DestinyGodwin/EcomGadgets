<?php
namespace App\Http\Controllers\V1\Admin;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Admin\AdminStoreResource;
use App\Notifications\V1\Stores\StoreApprovedNotification;
use App\Notifications\V1\Stores\StoreDeclinedNotification;
use App\Notifications\V1\Stores\StoreDeactivatedNotification;
use App\Notifications\V1\Stores\StoreReactivatedNotification;

class StoreController extends Controller
{
    public function approve(Store $store)
    {
        $store->update(['status' => 'approved', 'is_active' => true]);
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

    public function deactivate(Request $request, Store $store)
    {
        $request->validate([
            'message' => ['required', 'string', 'min:5'],
        ]);
        if (! $store->is_active) {
            return response()->json(['message' => 'Store is already deactivated.'], 400);
        }
        $store->update(['is_active' => false, 'status' => 'banned']);

        $store->user->notify(new StoreDeactivatedNotification($store, $request->message));
        return response()->json(['message' => 'Store deactivated and user notified.']);
    }
    public function reactivate(Request $request, Store $store)
    {
        $request->validate([
            'message' => ['required', 'string', 'min:5'],
        ]);
        if ($store->is_active) {
            return response()->json(['message' => 'Store is already active.'], 400);
        }
        $store->update([
            'is_active' => true,
            'status'    => $store->status === 'banned' ? 'approved' : $store->status,
        ]);
        $store->user->notify(new StoreReactivatedNotification($store, $request->message));
        return response()->json(['message' => 'Store has been reactivated and user notified.']);
    }
    public function show(Store $store)
    {
        return new AdminStoreResource($store);
    }
}
