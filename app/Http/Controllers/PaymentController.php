<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Factories\PaymentFactory;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        $gateway = PaymentFactory::make($request->input('type'));
        $response = $gateway->charge($request->input('amount'));

        return response()->json(['status' => 'success', 'charge' => $response]);
    }
}
