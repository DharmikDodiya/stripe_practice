<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function createCharge(Request $request){

        $token = $this->stripe->tokens->create([
        'card' => [
            'number' => '4242424242424242',
            'exp_month' => 5,
            'exp_year' => 2024,
            'cvc' => '314',
        ],
        ]);
        //dd($token->id);
        $charge = $this->stripe->charges->create([
        'amount' => 200,
        'source' => $token->id,
        'description' => 'My First Test Charge (created for API docs at https://www.stripe.com/docs/api)',
        'currency' => 'usd',
        ]);
        return success('Charge Created Successfully',$charge);
    }
}
