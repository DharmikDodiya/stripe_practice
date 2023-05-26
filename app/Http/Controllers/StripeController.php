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
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $user = Auth::user();

        $customer = $stripe->customers->create([
            'name' => 'sarman dasa',
            'email' => 'sarman@gmail.com',
            'description' => 'My First Test Customer (created for API docs)',
          ]);
        //dd($customer->id);
        // $charge = $stripe->charges->create([
        // 'amount' => 300,
        // 'currency' => 'usd',
        // 'source' => $customer->token,
        // 'description' => 'book',
        // 'customer' => $customer,
        // ]);
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

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => 2000,
            'currency' => 'gbp',
            'payment_method_types' => ['card'],
            'customer' => $customer->id
          ]);

        return success('charge created ',$paymentIntent);

    // try{
    //   $token =$this->stripe->tokens->create([
    //         'card' => [
    //             'number' => $request->number,
    //             'exp_month' => $request->exp_month,
    //             'exp_year' => $request->exp_year,
    //             'cvc' => $request->cvc,
    //         ],
    //     ]);
    //     \Stripe\Stripe::setApiKey('sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic');
    //     $charge = \Stripe\Charge::create([
    //         'amount' => 999,
    //         'currency' => 'usd',
    //         'description' => 'Example charge',
    //         'source' => $token->id,
    //       ]);

    //   //dd($token);
    //   $response = $this->stripe->paymentIntents->create([
    //     'amount' => 500,
    //     'currency' => 'gbp',
    //     'source'    => $token->id,
    //     'description' => 'first_payment',
    //   ]);
    //     return response()->json($charge);
    // }
    // catch(Expectation $ex){
    //     return response()->json('error');
    // }
    }
}
























    // public function form(){
    //     return view('stripe.form');
    // }

    // public function makePayment(Request $request){
    //     $data = $request->all();
    //     //dd($data);

    //     Stripe::setApiKey('sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic');
    //     $charge = \Stripe\Charge::create([
    //         'source' => $request->stripeToken,
    //         'description' => "10 cucumbers from Roger's Farm",
    //         'amount' => 2000,
    //         'currency' => 'usd',
    //       ]);
    //       dd($charge);
    // }

    // public function makePayment(Request $request)
    // {
    //     $data = $request->all();

    //     Stripe::setApiKey('sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic');

    //     try {
    //         $charge = \Stripe\Charge::create([
    //             'source' => $request->stripeToken,
    //             'description' => "10 cucumbers from Roger's Farm",
    //             'amount' => 2000,
    //             'currency' => 'usd',
    //         ]);

    //         dd($charge);
    //     } catch (\Stripe\Exception\CardException $e) {
    //         // Handle the exception for failed payments
    //         dd($e->getMessage());
    //     }
    // }


    // public function stripe()
    // {
    //     return view('stripe');
    // }

    // public function stripePost(Request $request)
    // {
    //     Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    //     $data = Stripe\Charge::create ([
    //             "amount" => 100 * 100,
    //             "currency" => "inr",
    //             "source" => $request->stripeToken,
    //             "description" => "Test payment"
    //     ]);

    //     dd($data);
    //     Session::flash('success', 'Payment successful!');

    //     return back();
    // }



    // }catch(Exception $ex){
    //     return response()->json([['response'=> 'error']],500);
    // }
        //dd($request->all());
        // try{
          //   $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
          // //  dd($stripe);
          //   $res = $stripe->tokens->create([
          //       'card' =>[
          //           'number'        => $request->number,
          //           'exp_month'     => $request->exp_month,
          //           'exp_year'      => $request->exp_year,
          //           'cvc'           => $request->cvc,
          //       ],
          //   ]);

          //   dd($res);
            // Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            // //dd($res);
            // $response = $stripe->charges->create([
            //     'amount'        => $request->amount,
            //     'currency'      => 'usd',
            //     'source'        => $res->id,
            //     'description'   => $request->description,
            // ]);

            //new code
          //  $stripe = new \Stripe\StripeClient(
          //       'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
          //     );
          //     $customer =  $stripe->customers->create([
          //       'description' => 'My First Test Customer (created for API docs at https://www.stripe.com/docs/api)',
          //     ]);

          //     dd($customer->id);
          //   $card = $stripe = new \Stripe\StripeClient(
          //       'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
          //     );
          //     $stripe->customers->createSource(
          //       'cus_9s6XWPuHZWFcfK',
          //       ['source' => 'tok_visa']
          //     );

          //   $stripe = new \Stripe\StripeClient(
          //       'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
          //     );
          //     $response=  $stripe->charges->create([
          //       'amount' => 2000,
          //       'currency' => 'inr',
          //       'source' =>  $res->id,
          //       'description' => 'My First Test Charge (created for API docs at https://www.stripe.com/docs/api)',
          //     ]);

          //   $stripe = new \Stripe\StripeClient(
          //       'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
          //     );
          //   $response = $stripe->paymentIntents->create([
          //       'amount' => 2000,
          //       'currency' => 'usd',
          //       'automatic_payment_methods' => [
          //         'enabled' => true,
          //       ],
          //     ]);

          //   //   dd($response);
          //   return $response;
          //   return response()->json([$response],201);

        // }catch(Exception $ex){
        //     return response()->json([['response' => 'Error']],500);
        // }
