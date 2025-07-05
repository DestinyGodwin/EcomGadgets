<?php

namespace App\Http\Controllers\V1;

use App\Enums\V1\TransactionType;
use Illuminate\Http\JsonResponse;
use App\Enums\V1\TransactionStatus;
use App\Http\Controllers\Controller;

abstract class TransactionController extends Controller
{
    protected function getEnumOptions(): JsonResponse
    {
        return response()->json([
            'transaction_types' => TransactionType::options(),
            'transaction_statuses' => TransactionStatus::options(),
        ]);
    }
}