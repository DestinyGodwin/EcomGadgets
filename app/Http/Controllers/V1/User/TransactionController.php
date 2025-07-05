<?php

namespace App\Http\Controllers\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;


class TransactionController extends BaseTransactionController
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function index(TransactionFilterRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $transactions = $this->transactionService->getUserStoreTransactions(
            auth()->id(),
            $filters
        );

        return TransactionResource::collection($transactions);
    }

    public function show(string $id): JsonResponse
    {
        $transaction = $this->transactionService->getUserStoreTransaction(
            auth()->id(),
            $id
        );

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'data' => new TransactionResource($transaction)
        ]);
    }

    public function byType(TransactionFilterRequest $request, string $type): AnonymousResourceCollection
    {
        // Validate the type parameter
        $transactionType = TransactionType::tryFrom($type);
        if (!$transactionType) {
            return response()->json([
                'message' => 'Invalid transaction type. Must be one of: ' . implode(', ', TransactionType::values())
            ], 422);
        }

        $filters = $request->validated();
        $transactions = $this->transactionService->getUserTransactionsByType(
            auth()->id(),
            $transactionType,
            $filters
        );

        return TransactionResource::collection($transactions);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $filters = $request->only([
            'status', 'type', 'channel', 'date_from', 'date_to',
            'amount_min', 'amount_max', 'per_page'
        ]);

        $transactions = $this->transactionService->searchUserTransactions(
            auth()->id(),
            $request->q,
            $filters
        );

        return TransactionResource::collection($transactions);
    }

    public function options(): JsonResponse
    {
        return $this->getEnumOptions();
    }
}