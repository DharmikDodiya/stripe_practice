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
            return error($ex);
        }
        return success('Plan Created Successfully',$plan);
    }

    public function getPlan($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $plan = $stripe->plans->retrieve(
            $id,
            []
        );
        if(isset($plan)){
            return success('Plan Details',$plan,200);
        }
            return error('Plan Not Found',404);
    }


    public function updatePlan(Request $request,$id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $plan = $stripe->plans->update(
            $id,
            ['metadata' => ['order_id' => '6735']]
        );
        if($plan){
            return success('Plan Updated Successfully',$plan);
        }
        return error('Plan Not Updated Successfully');
    }

    public function deletePlan($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
          );
        $plan = $stripe->plans->delete(
            $id,
            []
        );
        if($plan){
            return success('Plan Deleted Successfully');
        }
        return error('Plan Not Deleted Successfully');
    }

    public function listPlan(){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );

        $plan = $stripe->plans->all();
        if(count($plan) > 0){
            return success('Plan List',$plan,200);
        }
        return error('Plan Not Found',404);
    }
}
