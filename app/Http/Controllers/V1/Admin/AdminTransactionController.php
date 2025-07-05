<?php

namespace App\Http\Controllers\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V1\TransactionService;
use App\Http\Resources\V1\TransactionResource;

class AdminTransactionController extends Controller
{
    public function __construct(protected TransactionService $service) {}

    public function index()
    {
        return TransactionResource::collection($this->service->all());
    }

    public function show(string $id)
    {
        return new TransactionResource($this->service->find($id));
    }

    public function storeTransactions(string $storeId)
    {
        return TransactionResource::collection($this->service->byStore($storeId));
    }

    public function type(string $type)
    {
        return TransactionResource::collection($this->service->byType($type));
    }

    public function search(Request $request)
    {
        $term = $request->get('q');
        return TransactionResource::collection($this->service->search($term));
    }
}
