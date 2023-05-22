<?php

namespace App\Http\Controllers;
use App\Models\Plan as ModelsPlan;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Mockery\Expectation;
use Stripe;
use Stripe\Plan;
use Stripe\Stripe as StripeStripe;

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
        return response()->json($plan);
    }


    public function getPlan(){
        $basic = ModelsPlan::where('name','basic')->first();
        $professional = ModelsPlan::where('name','professional')->first();
        $enterprise = ModelsPlan::where('name','enterprise')->first();
        return response()->json([$basic,$professional,$enterprise]);
    }

    public function planCheckout($id,Request $request){
        $plan = ModelsPlan::where('plan_id',$id)->first();
        if(!$plan){
            return response()->json([
                'message' => 'Plan Not Found',
                'status'  => 500
            ]);
        }
        else{
            $user = auth()->user();
            $user->createOrGetStripeCustomer();
            $paymentMethod = null;
            $paymentMethod = $request->payment_method;

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
            //return response()->json(['data'=>$plan,'user'=>auth()->user()->createSetupIntent()]);
        }
    }























    public function stripePayment(Request $request){
    try{
      $stripe = new Stripe\StripeClient(
          env('STRIPE_SECRET')
      );
      $result = $stripe->tokens->create([
        'card' => [
            'number'    => $request->number,
            'exp_month' => $request->exp_month,
            'exp_year'  => $request->exp_year,
            'cvc'       => $request->cvc
        ]
      ]);

      Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
      $response = $stripe->paymentIntents->create([
        'amount' => $request->amount,
        'currency' => 'gbp',
        //'source'    => $result->id,
        'description' => $request->description,
      ]);
        return response()->json($response);
    }
    catch(Expectation $ex){
        return response()->json('error');
    }
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
