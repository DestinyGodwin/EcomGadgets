<?php

namespace App\Services\V1;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    /**
     * Create a new class instance.
     */
    public function initialize(array $data): array
    {
        $reference = 'PS_' . Str::uuid();

        // Create pending transaction
        Transaction::create([
            'reference' => $reference,
            'store_id'   => $data['store_id'],
            'type'      => $data['type'],
            'amount'    => $data['amount'],
            'status'    => 'pending',
            'channel'   => 'paystack',
            'meta'      => $data['metadata'],
        ]);

        // dd($data);
        // Hit Paystack
        $response = Http::withToken(config('services.paystack.secret_key'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $data['email'],
                'amount' =>(int)$data['amount'] * 100,
                'reference' => $reference,
                'metadata' => array_merge($data['metadata'], [
                    'reference' => $reference,
                    'payment_type' => $data['type'],
                ]),
            ])->json();

        return [
            'authorization_url' => $response['data']['authorization_url'],
            'reference' => $reference,
        ];
    }
}
