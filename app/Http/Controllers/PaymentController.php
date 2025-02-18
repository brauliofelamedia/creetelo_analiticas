<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\Payments;

class PaymentController extends Controller
{
    public function get()
    {
        $payments = new Payments();
        return $payments->Transactions(0);
    }
}
