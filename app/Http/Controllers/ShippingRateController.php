<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
class ShippingRateController extends Controller
{
    use ListingApiTrait;
    public function createShippingRate(Request $request){
        $shipping_rate = $this->stripe->shippingRates->create([
        'display_name' => $request->name,
        'type' => 'fixed_amount',
        'fixed_amount' => [
            'amount' => $request->amount,
            'currency' => 'usd',
        ],
        ]);
        if($shipping_rate){
            return success('ShippingRate Created Successfully',$shipping_rate);
        }
            return error('Shipping Rate Not Created');
    }

    public function listShippingRate(){
        $shipping_rate = $this->stripe->shippingRates->all();
        if($shipping_rate){
            $data = $this->filterSearchPagination($shipping_rate,$searchable_fields ?? null);
            return success('ShippingRate List',[
                'shipping_rate'        => $data['query'],
                'count'                => $data['count']
            ]);
        }
            return error('Shipping Rate Not Found');
    }

    public function getShippingRate($id){
        $shipping_rate = $this->stripe->shippingRates->retrieve(
        $id,
        []
        );
        if($shipping_rate){
            return success('Shippint Rate Details',$shipping_rate);
        }
            return error('Shipping Rate Not Found');
    }

    public function updateShippingRate(Request $request,$id){
        $shipping_rate = $this->stripe->shippingRates->update(
        $id,
        ['metadata' => ['order_id' => '6735','display_name' => $request->name,
            'amount' => $request->amount,
        ]]
        );
        if($shipping_rate){
            return success('Shipping Rate Updated Successfully',$shipping_rate);
        }
            return error('Shipping Rate Not Updated');
    }
}
