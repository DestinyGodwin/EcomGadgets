<?php

namespace App\Services\V1;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\V1\TransactionController;
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

    public function userByStatus(string $status, $user): LengthAwarePaginator
{
    return Transaction::where('status', $status)
        ->whereHas('store', fn($q) => $q->where('user_id', $user->id))
        ->latest()->paginate(20);
}
public function byStatus(string $status): LengthAwarePaginator
{
    return Transaction::where('status', $status)->latest()->paginate(20);
}

public function filter(array $filters):LengthAwarePaginator
{
    return Transaction::when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
        ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
        ->when($filters['q'] ?? null, function ($q, $search) {
            $q->where(function ($query) use ($search) {
                $query->where('reference', 'like', "%$search%")
                      ->orWhere('type', 'like', "%$search%")
                      ->orWhere('status', 'like', "%$search%");
            });
        })
        ->latest()
        ->paginate(20);
}

public function userFilter(array $filters, $user): LengthAwarePaginator
{
    return Transaction::whereHas('store', fn($q) => $q->where('user_id', $user->id))
        ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
        ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
        ->when($filters['q'] ?? null, function ($q, $search) {
            $q->where(function ($query) use ($search) {
                $query->where('reference', 'like', "%$search%")
                      ->orWhere('type', 'like', "%$search%")
                      ->orWhere('status', 'like', "%$search%");
            });
        })
        ->latest()
        ->paginate(20);
}

}