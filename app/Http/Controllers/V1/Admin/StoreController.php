<?php
namespace App\Http\Controllers\V1\Admin;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\StoreUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Services\V1\Stores\StoreService;
use App\Mail\V1\Stores\StoreDeclinedMail;
use App\Mail\V1\Stores\StoreDeactivatedMail;
use App\Mail\V1\Stores\StoreReactivatedMail;
use App\Http\Resources\V1\Stores\StoreResource;
use App\Mail\V1\Stores\StoreUpdateDeclinedMail;
use App\Mail\V1\Vendor\StoreUpdateApprovedMail;
use App\Http\Requests\V1\Stores\StoreSearchRequest;
use App\Http\Resources\V1\Admin\AdminStoreResource;
use App\Notifications\V1\Stores\StoreApprovedNotification;

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

        // $store->update(['status' => 'declined']);

        Mail::to($store->user->email)->send(new StoreDeclinedMail($store, $request->reason));
        $store->forceDelete();
        return response()->json(data: ['message' => 'Store declined with message sent.']);
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
        return StoreResource::collection($stores);    
    }

    public function approveUpdateRequest($id)
{
    $updateRequest = StoreUpdateRequest::findOrFail($id);
    $store = $updateRequest->store;

    $store->update($updateRequest->new_data);
    $updateRequest->status = 'approved';
    $updateRequest->save();
    Mail::to($store->email)->send(new StoreUpdateApprovedMail($store));

    return response()->json(['message' => 'Store update approved and applied.']);
}

public function declineUpdateRequest($id, $reason)
{
    $updateRequest = StoreUpdateRequest::findOrFail($id);
    $updateRequest->status = 'declined';
    $updateRequest->admin_feedback = $reason;
    $updateRequest->save();

    Mail::to($updateRequest->store->email)->send(
        new StoreUpdateDeclinedMail($updateRequest->store, $reason)
    );

    return response()->json(['message' => 'Store update request declined.']);
}
     public function pendingIndex()
    {
        $pendingRequests = StoreUpdateRequest::with('store.user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'data' => $pendingRequests
        ]);
    }

    public function pendingShow($id)
    {
        $updateRequest = StoreUpdateRequest::with('store.user')->findOrFail($id);

        return response()->json([
            'store' => $updateRequest->store,
            'current_data' => [
                'store_name'        => $updateRequest->store->store_name,
                'store_description' => $updateRequest->store->store_description,
                'phone'             => $updateRequest->store->phone,
                'email'             => $updateRequest->store->email,
                'state_id'          => $updateRequest->store->state_id,
                'lga_id'            => $updateRequest->store->lga_id,
                'address'           => $updateRequest->store->address,
                'store_image'       => $updateRequest->store->store_image,
            ],
            'new_data' => $updateRequest->new_data,
            'status'   => $updateRequest->status,
            'requested_at' => $updateRequest->created_at,
        ]);
    }
}
