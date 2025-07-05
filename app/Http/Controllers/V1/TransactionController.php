<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V1\TransactionService;
use App\Http\Resources\V1\TransactionResource;

class TransactionController extends Controller
{
   public function __construct(protected TransactionService $service) {}

    public function index(Request $request)
    {
        return TransactionResource::collection($this->service->userStoreTransactions($request->user()));
    }

    public function show(string $id, Request $request)
    {
        return new TransactionResource($this->service->userTransaction($id, $request->user()));
    }

    public function type(string $type, Request $request)
    {
        return TransactionResource::collection($this->service->userByType($type, $request->user()));
    }

    public function search(Request $request)
    {
        $term = $request->get('q');
        return TransactionResource::collection($this->service->userSearch($term, $request->user()));
    }
}
