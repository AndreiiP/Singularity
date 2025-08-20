<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGateway;

class StripeGateway implements PaymentGateway {
    public function charge(float $amount): int {
        // Stripe API
        return $amount * 0.8; 
    }
}