<?php

namespace App\Services\V1;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class TransactionService
{
     public function all(): LengthAwarePaginator
    {
        return Transaction::latest()->paginate(20);
    }

    public function find(string $id): ?Transaction
    {
        return Transaction::findOrFail($id);
    }

    public function byStore(string $storeId): LengthAwarePaginator
    {
        return Transaction::where('store_id', $storeId)->latest()->paginate(20);
    }

    public function byType(string $type): LengthAwarePaginator
    {
        return Transaction::where('type', $type)->latest()->paginate(20);
    }

    public function search(string $term): LengthAwarePaginator
    {
        return Transaction::where(function (Builder $query) use ($term) {
            $query->where('reference', 'like', "%$term%")
                ->orWhere('status', 'like', "%$term%")
                ->orWhere('type', 'like', "%$term%");
        })->latest()->paginate(20);
    }

    public function userStoreTransactions($user): LengthAwarePaginator
    {
        return Transaction::whereHas('store', fn($q) => $q->where('user_id', $user->id))
                          ->latest()->paginate(20);
    }

    public function userTransaction(string $id, $user): ?Transaction
    {
        return Transaction::where('id', $id)
                          ->whereHas('store', fn($q) => $q->where('user_id', $user->id))
                          ->firstOrFail();
    }

    public function userByType(string $type, $user)
    {
        return Transaction::where('type', $type)
                          ->whereHas('store', fn($q) => $q->where('user_id', $user->id))
                          ->latest()->paginate(20);
    }

    public function userSearch(string $term, $user)
    {
        return Transaction::where(function (Builder $query) use ($term) {
                    $query->where('reference', 'like', "%$term%")
                        ->orWhere('status', 'like', "%$term%")
                        ->orWhere('type', 'like', "%$term%");
                })
                ->whereHas('store', fn($q) => $q->where('user_id', $user->id))
                ->latest()->paginate(20);
    }
}