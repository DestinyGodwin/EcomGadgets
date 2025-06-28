<?php

namespace App\Services\V1\Stores;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Dotenv\Exception\ValidationException;

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
        $imagePath = $request->file('store_image')->store('stores', 'public');

        $store = $user->store()->create([
            'lga_id' => $request->lga_id,
            'state_id' => $request->state_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'store_name' => $request->store_name,
            'store_description' => $request->store_description,
            'store_image' => $imagePath,
            'subscription_expires_at' => now()->addMonth(),
        ]);
        $user->role = 'vendor';
        $user->save();
        try {

            $user->notify((new StoreCreatedNotification($store))->delay(now()->addSeconds(5)));
        } catch (\Throwable $e) {

            Log::error('Failed to send store creation notification: ' . $e->getMessage(), [
                'store_id' => $store->id,
                'user_id' => $user->id,
            ]);
        }
        return $store;
    }
    public function update($request, $store)
{
    $user = Auth::user();

    if (!$user->isVendor() || $user->store->id !== $store->id) {
        throw ValidationException::withMessages([
            'store' => ['Unauthorized to update this store.'],
        ]);
    }
    $data = $request->only([
        'lga_id', 'state_id', 'address', 'phone', 'email', 'store_name', 'store_description'
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
    if (!$user->isVendor() || $user->store->id !== $store->id) {
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
