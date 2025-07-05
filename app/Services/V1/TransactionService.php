<?php

namespace App\Services\V1;
use App\Models\Transaction;
use App\Enums\V1\TransactionType;
use App\Enums\V1\TransactionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;


class TransactionService
{
    public function getAllTransactions(array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::with('store');

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getTransactionById(string $id): ?Transaction
    {
        return Transaction::with('store')->find($id);
    }

    public function getStoreTransactions(string $storeId, array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::with('store')->where('store_id', $storeId);

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getUserStoreTransactions(string $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::with('store')
            ->whereHas('store', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getUserStoreTransaction(string $userId, string $transactionId): ?Transaction
    {
        return Transaction::with('store')
            ->whereHas('store', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->find($transactionId);
    }

    public function getTransactionsByType(TransactionType|string $type, array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::with('store')->byType($type);

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getUserTransactionsByType(string $userId, TransactionType|string $type, array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::with('store')
            ->byType($type)
            ->whereHas('store', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function searchTransactions(string $search, array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::with('store')->search($search);

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function searchUserTransactions(string $userId, string $search, array $filters = []): LengthAwarePaginator
    {
        $query = Transaction::with('store')
            ->search($search)
            ->whereHas('store', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getTransactionStats(): array
    {
        return [
            'total_transactions' => Transaction::count(),
            'pending_transactions' => Transaction::byStatus(TransactionStatus::PENDING)->count(),
            'completed_transactions' => Transaction::byStatus(TransactionStatus::COMPLETED)->count(),
            'failed_transactions' => Transaction::byStatus(TransactionStatus::FAILED)->count(),
            'cancelled_transactions' => Transaction::byStatus(TransactionStatus::CANCELLED)->count(),
            'total_amount' => Transaction::byStatus(TransactionStatus::COMPLETED)->sum('amount'),
            'by_type' => [
                'subscription' => Transaction::byType(TransactionType::SUBSCRIPTION)->count(),
                'feature' => Transaction::byType(TransactionType::FEATURE)->count(),
                'advertisement' => Transaction::byType(TransactionType::ADVERTISEMENT)->count(),
            ],
        ];
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $status = $filters['status'] instanceof TransactionStatus 
                ? $filters['status'] 
                : TransactionStatus::tryFrom($filters['status']);
            if ($status) {
                $query->byStatus($status);
            }
        }

        if (!empty($filters['type'])) {
            $type = $filters['type'] instanceof TransactionType 
                ? $filters['type'] 
                : TransactionType::tryFrom($filters['type']);
            if ($type) {
                $query->byType($type);
            }
        }

        if (!empty($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (!empty($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        return $query;
    }
}