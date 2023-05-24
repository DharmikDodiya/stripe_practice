<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function createSubscription(Request $request){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
          );
        $intent = auth()->user()->createSetupIntent;
        //$plan = Plan::find($request->plan);

        $subscription = $stripe->subscriptions->create([
            'customer' => 'cus_NwRQDsuVq0EeS3',
            'items' => [
              ['price' => 'price_1NBAWESA4SjjlNffbRUJGkxv'],
              //'default_payment_method'
            ],
        ]);
        if($subscription){
            return success('Create Subscription Successfully',$subscription);
        }
        else{
            return error('Subscription Not Created Successfully');
        }
    }

    public function getSubscription(Request $request,$id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        if($stripe){
            $subscription =$stripe->subscriptions->retrieve(
                $id,
                []
            );
            return success('Subscription List',$subscription);
        }
        else{
            return error(['message' => 'No Subscription Found',404]);
        }
    }

    public function listSubscription(){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $subscriptions = $stripe->subscriptions->all();
        if($subscriptions){
            return success('Subscription List',$subscriptions,200);
        }
        return error('Subscription Not Found',404);
    }

    public function cancleSubscription($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $subscription = $stripe->subscriptions->cancel(
            $id,
            []
        );
        return success('Subscription Cancle Succssfully',200);
    }

    public function resumeSubscription($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $stripe->subscriptions->resume(
            $id,
            ['billing_cycle_anchor' => 'now']
        );
    }


}
