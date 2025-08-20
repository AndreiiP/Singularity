<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGateway;

class PaypalGateway implements PaymentGateway {
    public function charge(float $amount): int {
        // Paypal API
        return $amount * 0.5;
    }
}