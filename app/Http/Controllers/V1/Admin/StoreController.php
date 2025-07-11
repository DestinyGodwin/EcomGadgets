<?php
namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Stores\StoreSearchRequest;
use App\Http\Resources\V1\Admin\AdminStoreResource;
use App\Http\Resources\V1\Stores\StoreResource;
use App\Mail\V1\Stores\StoreDeactivatedMail;
use App\Mail\V1\Stores\StoreDeclinedMail;
use App\Mail\V1\Stores\StoreReactivatedMail;
use App\Models\Store;
use App\Notifications\V1\Stores\StoreApprovedNotification;
use App\Services\V1\Stores\StoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StoreController extends Controller
{
    public function __construct(protected StoreService $storeService)
    {}
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
        Mail::to($store->user->email)->send(new StoreDeclinedMail($store, $request->reason));
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

        Mail::to($store->user->email)->send(new StoreDeactivatedMail($store, $request->message));
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
        Mail::to($store->user->email)->send(new StoreReactivatedMail($store, $request->message));
        return response()->json(['message' => 'Store has been reactivated and user notified.']);
    }
    public function show(Store $store)
    {
        return new AdminStoreResource($store);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $stores  = Store::paginate($perPage);
        return StoreResource::collection($stores);

    }
    public function search(StoreSearchRequest $request)
    {
        $query = $request->validated()['q'];

        $stores = $this->storeService->searchStores($query);

        return StoreResource::collection($stores);
    }

    public function pending(Request $request)
    {
        $stores = Store::where('status', 'pending')->paginate();
        return StoreResource::collection($stores);        -
    }
}
