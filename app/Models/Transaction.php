<?php

namespace App\Models;

use App\Enums\V1\TransactionType;
use App\Enums\V1\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
       use HasUuids, SoftDeletes;
       protected $fillable = [
        'reference',
        'store_id',
        'type',
        'amount',
        'status',
        'channel',
        'meta',
    ];
     

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    protected $casts = [
        'meta' => 'array',
        'amount' => 'decimal:2',
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
    ];

    

    public function scopeByType($query, TransactionType|string $type)
    {
        $typeValue = $type instanceof TransactionType ? $type->value : $type;
        return $query->where('type', $typeValue);
    }

    public function scopeByStatus($query, TransactionStatus|string $status)
    {
        $statusValue = $status instanceof TransactionStatus ? $status->value : $status;
        return $query->where('status', $statusValue);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('reference', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhereHas('store', function ($storeQuery) use ($search) {
                  $storeQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    public function getTypeLabel(): string
    {
        return $this->type->label();
    }

    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    public function getStatusColor(): string
    {
        return $this->status->color();
    }

    public function isPending(): bool
    {
        return $this->status === TransactionStatus::PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === TransactionStatus::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === TransactionStatus::FAILED;
    }

    public function isCancelled(): bool
    {
        return $this->status === TransactionStatus::CANCELLED;
    }
}


