<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function createPrice(Request $request){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $data = $stripe->prices->create([
            'unit_amount' => $request->unit_amount,
            'currency' => 'inr',
            'recurring' => ['interval' => $request->interval],
            'product' => $request->product,
        ]);
        //dd($data);
        $price = Price::create([
            'price_id'  => $data->id,
            'active'    => $data->active,
            'currency'  => $data->currency,
            'type'      => $data->type,
            'amount'    => $data->unit_amount,
            'product_id'=> $data->product
        ]);
        //dd($price);
        return success('price created successfully',$data);
    }

    public function getPrice($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $price = $stripe->prices->retrieve(
            $id,
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
            ['metadata' => ['order_id' => '6735','unit_amount' => $request->unit_amount,'product_id'=> $request->product]]
        );

        $data = Price::where('price_id',$id)->first();
        $data->update($request->only(
            'unit_amount',
            'product'
        ));
        return success('Price Updated Successfully',$price,$data);
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
