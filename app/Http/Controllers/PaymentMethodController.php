<?php

namespace App\Http\Controllers;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    use ListingApiTrait;
    public function createPaymentMethod(Request $request){
        $paymentMethod = $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => $request->number,
                'exp_month' => $request->exp_month,
                'exp_year' => $request->exp_year,
                'cvc' => $request->cvc,
            ],
        ]);
        return success('PaymentMethod Created Successfully',$paymentMethod);
    }

    public function listPaymentMethod($id){
        $paymentMethods = $this->stripe->paymentMethods->all([
        'customer' => $id,
        'type' => 'card',
        ]);
        if(isset($paymentMethods)){
            $data = $this->filterSearchPagination($paymentMethods,$searchable_fields ?? null);
            return success('PaymentMethods List',[
                'PaymentMethods'        => $data['query'],
                'count'                => $data['count']
            ]);
        }else{
            return error('Payment Method Not found');
        }
    }

    public function getPaymentMethod($id){
        $paymentMethod = $this->stripe->paymentMethods->retrieve(
        $id,
        []
        );
        if(isset($paymentMethod)){
            return success('Payment Method Details',$paymentMethod);
        }else{
            return error('Payment Method Not Found');
        }
    }

    public function detachPaymentMethod($id){
        $this->stripe->paymentMethods->detach(
            $id,
            []
        );
        return success('PaymentMethod Detach Successfully');
    }



    public function createToken(Request $request){
        $token =$this->stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 5,
                'exp_year' => 2024,
                'cvc' => '314',
            ],
        ]);
        return success('Token Created Successfully',$token);
    }

    public function createCard(Request $request,$id){
        $card = $this->stripe->customers->createSource(
            $id,
            ['source' => $request->token]
        );
        return success('Card Created Successfully',$card);
    }

    public function attachPaymentMethod(Request $request,$id){
        $payment = $this->stripe->paymentMethods->attach(
            $id,
            ['customer' => $request->cus_id]
        );
        return success('PaymentMethod Attach to Customer successfully',$payment);
    }
}
