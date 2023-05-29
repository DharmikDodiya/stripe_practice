<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function createCharge(Request $request){

        $charge = $this->stripe->charges->create([
        'amount' => 2000,
        'currency' => 'usd',
        'source' => 'tok_visa',
        'description' => 'My First Test Charge (created for API docs at https://www.stripe.com/docs/api)',
        ]);
        return success('Charge Created Successfully',$charge);
    }
}
