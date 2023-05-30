<?php

namespace App\Http\Controllers;
use Exception;
use Stripe\Plan;

use Stripe\Token;
use Stripe\Charge;
use Stripe\Stripe;
use Mockery\Expectation;
use Illuminate\Http\Request;
use App\Models\Plan as ModelsPlan;
use Stripe\Stripe as StripeStripe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class StripeController extends Controller{


    public function singleCharge(Request $request){
        $amount = $request->amount;
        $amount = $amount * 100;
        $paymentMethod = $request->paymeny_method;

        $user = auth()->user();
        $user->createOrGetStripeCustomer();

        $paymentMethod = $user->addPaymentMethod($paymentMethod);
        $user->charge($amount,$paymentMethod->id);
    }

    public function storePlan(Request $request){
        try{
        $amount = ($request->amount * 100);
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $plan = Plan::create([
            'amount'            => $amount,
            'currency'          => $request->currency,
            'interval'          => $request->billing_period,
            'interval_count'    => $request->interval_count,
            'product'           => [
                'name'  => $request->name,
            ],
        ]);

        $sub = $this->stripe->subscriptions->create([
            'customer' => 'cus_NwRQDsuVq0EeS3',
            //'billing_method' =>'pi_3NAuPkSA4SjjlNff0ypr59Pg',
            'items' => [
              ['price' => 'plan_NwnO4XFvfTZyA1'],
              'default_payment_method'
            ],
          ]);

        ModelsPlan::create([
            'plan_id'           => $plan->id,
            'name'              => $request->name,
            'price'             => $plan->amount,
            'billing_method'    => $plan->interval,
            'currency'          => $plan->currency,
            'interval_count'    => $plan->interval_count
        ]);
        }
        catch(Expectation $ex){
            return response()->json($ex);
        }
        return response()->json($sub);
    }


    public function getPlan(){
        $basic = ModelsPlan::where('name','basic')->first();
        $professional = ModelsPlan::where('name','professional')->first();
        $enterprise = ModelsPlan::where('name','enterprise')->first();
        return response()->json([$basic,$professional,$enterprise]);
    }

    public function planCheckout($id,Request $request){
        $plan = ModelsPlan::where('plan_id',$id)->first();
        $data = auth()->user()->createSetupIntent();

        return $data;

        if(!$plan){
            return response()->json([
                'message' => 'Plan Not Found',
                'status'  => 500
            ]);
        }
        else{
            $user = auth()->user();
            $user->createOrGetStripeCustomer();
            $plan = ModelsPlan::where('plan_id',$id)->first();
            $paymentMethod = null;
            $paymentMethod = $plan->billing_method;

            if($paymentMethod != null){
                $paymentMethod = $user->addPaymentMethod($paymentMethod);
            }
            $plan = $request->plan_id;

            try{
                $user->newSubscription(
                    'default',$plan
                )->create($paymentMethod != null ? $paymentMethod->id: '' );
            }catch(Exception $ex){
                return response()->json($ex->getMessage());
            }
            return response()->json('success',200);
        }
    }

    public function createPayment(Request $request){

        $data = $this->stripe->paymentIntents->create([
            'amount' => 2000,
            'currency' => 'usd',
            'automatic_payment_methods' => [
              'enabled' => true,
            ],
        ]);
        return success('Payment Created Successfully',$data);
    }

    public function stripePayment(Request $request){
        $user = Auth::user();

        $customer = $this->stripe->customers->create([
            'name' => 'sarman dasa',
            'email' => 'sarman@gmail.com',
            'description' => 'My First Test Customer (created for API docs)',
          ]);

        $paymentMethod = $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 8,
                'exp_year' => 2024,
                'cvc' => '314',
            ],
        ]);

        $token =$this->stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 5,
                'exp_year' => 2024,
                'cvc' => '314',
            ],
        ]);

        $card = $this->stripe->customers->createSource(
            $customer->id,
            ['source' => $token->id]
        );

        $payment = $this->stripe->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $customer->id]
            );

        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => 2000,
            'currency' => 'gbp',
            'payment_method_types' => ['card'],
            'customer' => $customer->id
          ]);

        return success('charge created ',$paymentIntent);
    }
}
























