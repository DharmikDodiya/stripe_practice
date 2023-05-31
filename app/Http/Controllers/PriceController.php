<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
class PriceController extends Controller
{
    use ListingApiTrait;
    public function createPrice(Request $request){
        $data = $this->stripe->prices->create([
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
        $price = $this->stripe->prices->retrieve(
            $id,
            []
        );
        return success('Price Details',$price);
    }

    public function updatePrice(Request $request,$id){
        $price = $this->stripe->prices->update(
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
        $prices = $this->stripe->prices->all();
        if($prices){
            $data = $this->filterSearchPagination($prices,$searchable_fields ?? null);
            return success('Price List',[
                'price'        => $data['query'],
                'count'        => $data['count']
            ]);
        }
        else{
            return error('Price Not Found');
        }
    }

}
