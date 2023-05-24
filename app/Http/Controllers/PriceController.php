<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function createPrice(Request $request){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $data = $stripe->prices->create([
            'unit_amount' => $request->unit_amount,
            'currency' => 'usd',
            'recurring' => $request->recurring,
            'product' => $request->product,
        ]);
        return success('price created successfully',$data);
    }

    public function getPrice(){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $price = $stripe->prices->retrieve(
            'price_1NBDcNSA4SjjlNffTIzDGBkM',
            []
        );
        return success('Price Details',$price);
    }

    public function updatePrice(Request $request,$id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $price = $stripe->prices->update(
            $id,
            ['metadata' => ['order_id' => '6735']]
        );
        return success('Price Updated Successfully',$price);
    }

    public function priceList(){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $prices = $stripe->prices->all();
        if($prices){
            return success('List Of All Price',$prices);
        }
        else{
            return error('Price Not Found');
        }
    }

}
