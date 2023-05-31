<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
class PaymentIntentController extends Controller
{
    use ListingApiTrait;
    public function createPaymentIntent(Request $request){

        $paymentIntent = $this->stripe->paymentIntents->create([
        'amount' => $request->amount,
        'currency' => 'usd',
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
        ]);
        return success('PaymentIntent Created Successfully',$paymentIntent);
    }

    public function getPaymentIntent($id){
        $paymentIntent = $this->stripe->paymentIntents->retrieve(
        $id,
        []
        );
        return success('Payment-Intent Details',$paymentIntent);
    }

    public function listPaymentIntent(){
        $paymentIntents = $this->stripe->paymentIntents->all();
        if(count($paymentIntents)){
            $data = $this->filterSearchPagination($paymentIntents,$searchable_fields ?? null);
            return success('PaymentIntent List',[
                'PaymnetIntent'        => $data['query'],
                'count'                => $data['count']
            ]);
        }
            return error('Payment-Intent List Not Found');
    }

    public function updatePaymentIntent(Request $request,$id){
    //dd($request->amount);
        $paymentIntent = $this->stripe->paymentIntents->update(
        $id,
        ['metadata' => ['order_id' => '6735' , 'amount' => $request->amount]]
        );
        if(isset($paymentIntent)){
            return success('PaymentIntent Updated Successfully',$paymentIntent);
        }
        return error('PaymentIntent Not Updated');
    }

    public function capturePaymentIntent($id){
        $paymentIntent = $this->stripe->paymentIntents->capture(
        $id,
        []
        );
        if(isset($paymentIntent)){
            return success('PaymentIntent Capture Successfully',$paymentIntent);
        }
        return error('PaymentIntent Not Captured');
    }
}
