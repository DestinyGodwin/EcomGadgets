<?php
namespace App\Services\V1\Stores;

use App\Models\Store;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\V1\Stores\StoreUnderReviewMail;
use Illuminate\Validation\ValidationException;
use App\Mail\V1\Admin\NewStoreAwaitingApprovalMail;

class StoreService
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function create($request)
    {
        $user = Auth::user();

        if ($user->isVendor() && $user->store) {
            throw ValidationException::withMessages([
                'store' => ['You already have a store and cannot create another.'],
            ]);
        }
        // $durationDays = (int) Setting::get('store_subscription_duration', 0);
        // $expiresAt    = $durationDays > 0 ? now()->addDays($durationDays) : null;

        $storeImagePath = $request->file('store_image')->store('stores', 'public');
        $cacImagePath   = $request->file('store_cac_image')->store('stores/cac', 'public');
        $idImagePath    = $request->file('store_id_image')->store('stores/id', 'public');

        $store = $user->store()->create([
            'lga_id'                  => $request->lga_id,
            'state_id'                => $request->state_id,
            'address'                 => $request->address,
            'phone'                   => $request->phone,
            'email'                   => $request->email,
            'store_name'              => $request->store_name,
            'store_description'       => $request->store_description,
            'store_image'             => $storeImagePath,
            'store_cac_image'         => $cacImagePath,
            'store_id_image'          => $idImagePath,
            'subscription_expires_at' => null,
            'is_active'               => false,
            'status'                  => "pending",
        ]);

        $user->role = 'vendor';
        $user->save();

        try {
            // Notification::route('mail', route: config('mail.admin_email'))
            //     ->notify(new NewStoreAwaitingApprovalNotification($store));
            Mail::to(config('mail.admin_email'))->send(new NewStoreAwaitingApprovalMail($store));

            Mail::to($user->email)->send(new StoreUnderReviewMail($store));

        } catch (\Throwable $e) {
            Log::error('Failed to notify admin about new store: ' . $e->getMessage(), [
                'store_id' => $store->id,
            ]);
        }

        return $store;
    }

    public function update($request, $store)
    {
        $user = Auth::user();

        if (! $user->isVendor() || $user->store->id !== $store->id) {
            throw ValidationException::withMessages([
                'store' => ['Unauthorized to update this store.'],
            ]);
        }
        $data = $request->only([
            'lga_id', 'state_id', 'address', 'phone', 'email', 'store_name', 'store_description',
        ]);

        if ($request->hasFile('store_image')) {
            if ($store->store_image && Storage::disk('public')->exists($store->store_image)) {
                Storage::disk('public')->delete($store->store_image);
            }
            $data['store_image'] = $request->file('store_image')->store('stores', 'public');
        }
        $store->update($data);
        return $store;
    }

    public function delete($store)
    {
        $user = Auth::user();
        if (! $user->isVendor() || $user->store->id !== $store->id) {
            throw ValidationException::withMessages([
                'store' => ['Unauthorized to delete this store.'],
            ]);
        }

        if ($store->store_image && Storage::disk('public')->exists($store->store_image)) {
            Storage::disk('public')->delete($store->store_image);
        }

        $store->delete();
        $user->role = 'customer';
        $user->save();

        return true;
    }
    public function getStoresByState(string $stateId)
    {
        return Store::where('state_id', $stateId)->paginate(50);
    }

    public function getStoresByLga(string $lgaId)
    {
        return Store::where('lga_id', $lgaId)->paginate(50);
    }
    public function searchStores(string $query)
    {
        return Store::where(function ($q) use ($query) {
            $q->where('store_name', 'like', "%$query%")
                ->orWhere('slug', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->orWhere('id', $query);
        })
            ->paginate(50);
    }

    public function getStoreById(string $storeId)
    {
        return Store::find($storeId);
    }

    // public function resubmit(Store $store, array $data): Store

    
    // {
    //        if ($store->store_image) {
    //     Storage::disk('public')->delete($store->store_image);
    // }

    // if ($store->store_cac_image) {
    //     Storage::disk('public')->delete($store->store_cac_image);
    // }

    // if ($store->store_id_image) {
    //     Storage::disk('public')->delete($store->store_id_image);
    // }
    //     $storeImagePath = $data['store_image']->store('stores', 'public');
    //     $cacImagePath   = $data['store_cac_image']->store('stores/cac', 'public');
    //     $idImagePath    = $data['store_id_image']->store('stores/id', 'public');
    //     $updateData     = [
    //         'store_name'        => $data['store_name'],
    //         'store_description' => $data['store_description'],
    //         'email'             => $data['email'],
    //         'phone'             => $data['phone'],
    //         'state_id'          => $data['state_id'],
    //         'lga_id'            => $data['lga_id'],
    //         'address'           => $data['address'],
    //         'store_image'       => $storeImagePath,
    //         'store_cac_image'   => $cacImagePath,
    //         'store_id_image'    => $idImagePath,
    //         'status'            => 'pending',
    //         'is_active'         => false,
    //     ];

    //     $store->update($updateData);
    //      Mail::to(config('mail.admin_email'))->send(new NewStoreAwaitingApprovalMail($store));


    //     return $store;

    // }

    public function requestUpdate($request)
{
    $user = Auth::user();
    $store = $user->store;
    if (!$store) {
        throw ValidationException::withMessages(['store' => ['No store found.']]);
    }
    $pendingRequest = $store->updateRequests()->where('status', 'pending')->first();
    if ($pendingRequest) {
        throw ValidationException::withMessages([
            'update_request' => ['You already have a pending store update request.'],
        ]);
    }
    $data = $request->only([
        'store_name', 'store_description', 'phone', 'email',
        'state_id', 'lga_id', 'address',
    ]);
    if ($request->hasFile('store_image')) {
        if ($store->store_image) {
            Storage::disk('public')->delete($store->store_image);
        }
        $data['store_image'] = $request->file('store_image')->store('stores', 'public');
    }
    if ($request->hasFile('store_cac_image')) {
        if ($store->store_cac_image) {
            Storage::disk('public')->delete($store->store_cac_image);
        }
        $data['store_cac_image'] = $request->file('store_cac_image')->store('stores/cac', 'public');
    }

    if ($request->hasFile('store_id_image')) {
        if ($store->store_id_image) {
            Storage::disk('public')->delete($store->store_id_image);
        }
        $data['store_id_image'] = $request->file('store_id_image')->store('stores/id', 'public');
    }

    $updateRequest = $store->updateRequests()->create([
        'new_data' => $data,
    ]);
    try {
        Mail::to(config('mail.admin_email'))->send(new StoreEditAwaitingApprovalMail($store, $updateRequest));
    } catch (\Throwable $e) {
        Log::error('Failed to notify admin about store edit: ' . $e->getMessage());
    }

    return $updateRequest;
}




}
