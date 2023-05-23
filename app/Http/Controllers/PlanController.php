<?php

namespace App\Http\Controllers;

use App\Models\Plan as ModelsPlan;
use Exception;
use Illuminate\Http\Request;
use Stripe\Plan;

class PlanController extends Controller
{
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

       // dd($plan->paymentMethod);

    //    \Stripe\Subscription::update(
    //         'sub_1NAtsISA4SjjlNffCUdGl7Si',
    //         [
    //         'payment_settings' => [
    //             'payment_method_types' => ['card'],
    //         ],
    //         ]
    //     );
    //    $this->stripe->subscriptions->create([
    //         'customer' => 'cus_NwRQDsuVq0EeS3',
    //         //'billing_method' =>'pi_3NAuPkSA4SjjlNff0ypr59Pg',
    //         'items' => [
    //           ['price' => 'plan_NwnO4XFvfTZyA1'],
    //         ],
    //       ]);

        ModelsPlan::create([
            'plan_id'           => $plan->id,
            'name'              => $request->name,
            'price'             => $plan->amount,
            'billing_method'    => $plan->interval,
            'currency'          => $plan->currency,
            'interval_count'    => $plan->interval_count
        ]);
        }
        catch(Exception $ex){
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


    public function updatePlan(Request $request){
        //dd($request);
        // $stripe = new \Stripe\StripeClient(
        //     'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        //   );
        //$plan = ModelsPlan::where('plan_id',$id)->first();
        $amount = ($request->amount * 100);
          $this->stripe->plans->update(
            'plan_NwoPRge1RRQiuj',
            ['amount'            => $amount,
            'currency'          => $request->currency,
            'interval'          => $request->billing_period,
            'interval_count'    => $request->interval_count,
            'product'           => [
                'name'  => $request->name,
            ]]
          );
    }

    public function deletePlan($id){
        $stripe = new \Stripe\StripeClient(
    'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );

        $plan = ModelsPlan::where('plan_id',$id)->first();
        dd($plan);
        if($plan){
            $stripe->plans->delete(
            $id,
                []
            );

            $plan->delete();
            return response()->json(['message' => 'success',200]);
        }
        else{
            return response()->json(['message'=>'Plan Not deleted',401]);
        }
    }
}
