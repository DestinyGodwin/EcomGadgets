<?php

namespace App\Services\V1\Payments;

interface PaymentHandlerInterface
{
    public function handleSuccessfulPayment(array $meta): void;
}
