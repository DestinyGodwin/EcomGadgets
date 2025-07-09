<?php
namespace App\Services\V1\Stores;

use App\Models\Setting;
use App\Models\Store;
use App\Notifications\V1\Admin\NewStoreAwaitingApprovalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

        $durationDays = (int) Setting::get('store_subscription_duration', 0);
        $expiresAt    = $durationDays > 0 ? now()->addDays($durationDays) : null;

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
            'subscription_expires_at' => $expiresAt,
            'is_active'               => false,
        ]);

        $user->role = 'vendor';
        $user->save();

        // Notify admin
        try {
            Notification::route('mail', config('mail.admin_email'))
                ->notify(new NewStoreAwaitingApprovalNotification($store));
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

}
