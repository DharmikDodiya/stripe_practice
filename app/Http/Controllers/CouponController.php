<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
class CouponController extends Controller
{
    use ListingApiTrait;
    public function createCoupon(Request $request){

        $coupon = $this->stripe->coupons->create([
        'percent_off' => $request->percent_off,
        'name'      => $request->name,
        'duration' => 'repeating',
        'duration_in_months' => $request->duration,
        ]);
        if(isset($coupon)){
            return success('Coupon Created Succssfully',$coupon);
        }
            return error('Coupon Not Created');
    }

    public function getCoupon($id){
        $coupon = $this->stripe->coupons->retrieve($id, []);
        if(isset($coupon)){
            return success('Coupon Details',$coupon);
        }
            return error('Coupon Not Found');
    }

    public function updateCoupon($id,Request $request){

        $coupon = $this->stripe->coupons->update(
        $id,
        ['metadata' => ['order_id' => '6735','name' => $request->name, 'percent_off' => $request->percent_off ,  'duration_in_months' => $request->duration]]
        );
        if(isset($coupon)){
            return success('Coupon Updated Successfully',$coupon);
        }
            return error('Coupon Not Updated');
    }

    public function listCoupon(){
        $coupon = $this->stripe->coupons->all();
        if($coupon){
            $data = $this->filterSearchPagination($coupon,$searchable_fields ?? null);
            return success('Coupon List',[
                'coupon'         => $data['query'],
                'count'          => $data['count']
            ]);
        }
            return error('Coupon Not Found');
    }

    public function deleteCoupon($id){
        $this->stripe->coupons->delete($id, []);
        return success('Coupon deleted Successfully');
    }
}
