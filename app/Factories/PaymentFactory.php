<?php

namespace App\Factories;

use App\Contracts\PaymentGateway;
use App\Services\Payment\StripeGateway;
use App\Services\Payment\PaypalGateway;

class PaymentFactory
{
    public static function make(string $type): PaymentGateway
    {
        return match ($type) {
            'stripe' => new StripeGateway(),
            'paypal' => new PaypalGateway(),
            default => throw new \Exception("Unsupported payment type: $type"),
        };
    }
}
