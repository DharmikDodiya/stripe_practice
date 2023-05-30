<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function createSubscription(Request $request){

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $request->customer_id,
            'items' => [
              ['price' => $request->price,'payment_settings' => 'card'],
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
            $subscription = $this->stripe->subscriptions->retrieve(
                $id,
                []
            );
            if(isset($subscription)){
            return success('Subscription List',$subscription);
            }
            return error(['message' => 'No Subscription Found',404]);
    }

    public function listSubscription(){
        $subscriptions = $this->stripe->subscriptions->all();
        if($subscriptions){
            return success('Subscription List',$subscriptions,200);
        }
        return error('Subscription Not Found',404);
    }

    public function cancleSubscription($id){
        $subscription = $this->stripe->subscriptions->cancel(
            $id,
            []
        );
        return success('Subscription Cancle Succssfully',200);
    }

    public function resumeSubscription($id){
        $this->stripe->subscriptions->resume(
            $id,
            ['billing_cycle_anchor' => 'now']
        );
    }


}
