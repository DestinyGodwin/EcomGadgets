<?php

namespace App\Http\Controllers\V1\Stores;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\V1\Stores\StoreService;
use App\Http\Resources\V1\Stores\StoreResource;
use App\Http\Requests\V1\Stores\CreateStoreRequest;
use App\Http\Requests\V1\Stores\StoreSearchRequest;
use App\Http\Requests\V1\Stores\UpdateStoreRequest;

class StoreController extends Controller
{
        public function __construct(protected StoreService $storeService) {}

    public function store(CreateStoreRequest $request){
       $store =  $this->storeService->create($request);
       return new StoreResource($store);
    }
     public function show(Store $store)
    {
        return new StoreResource($store);
    }
    public function index(Request $request)
    {
        $stores = Store::paginate(15);
        return StoreResource::collection($stores);
    }
    public function update(UpdateStoreRequest $request, Store $store)
{
    $updatedStore = $this->storeService->update($request, $store);
    return new StoreResource($updatedStore);
}

public function destroy(Store $store)
{
    $this->storeService->delete($store);
    return response()->json(['message' => 'Store deleted successfully.'], 200);
}

     public function mystore(){
        $store = Auth::user()->store;
        return response()->json($store);
     }

    
public function search(StoreSearchRequest $request)
{
    $query = $request->validated()['q'];

    $stores = $this->storeService->searchStores($query);

    return StoreResource::collection($stores);
}

public function getStoresByState(string $stateId)
{
    $stores = $this->storeService->getStoresByState($stateId);
    return StoreResource::collection($stores);
}

public function getStoresByLga(string $lgaId)
{
    $stores = $this->storeService->getStoresByLga($lgaId);
    return StoreResource::collection($stores);
}
public function getStore(string $storeId)
{
    $store = $this->storeService->getStoreById($storeId);

    if (!$store) {
        return response()->json(['message' => 'Store not found.'], 404);
    }

    return response()->json($store);
}
}