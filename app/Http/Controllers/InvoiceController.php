<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $invoice = $stripe->invoices->create([
            'customer' => $request->customer_id,
            'description' => $request->description,
            'discounts'   => $request->discount,
        ]);

        $invoiceData = Invoice::create([
            'invoice_id'    => $invoice->id,
            'currency'      => $invoice->currency,
            'customer_name' => $invoice->customer_name,
            'customer_email'=> $invoice->customer_email,
            'description'   => $invoice->description,
            'status'        => $invoice->status
        ]);

        if($invoice){
            return success('Invoice Created Successfully',$invoice,$invoiceData);
        }
        else{
            return error('Invoice Not Created');
        }
    }

    public function getInvoice($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        try{
        $invoice = $stripe->invoices->retrieve(
        $id,
            []
        );
            return success('Invoice Details',$invoice);
        }catch(Exception $ex){
            return error('Invoice Not Found',$ex);
        }
    }

    public function updateInvoice(Request $request,$id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        try{
        $invoice = $stripe->invoices->update(
            $id,
            ['metadata' => ['order_id' => '6735', 'customer' => $request->customer_id,
            'description' => $request->description]]
        );
            $invoiceData = Invoice::where('invoice_id',$id)->first();
            $invoiceData->update($request->only($request->customer_id,$request->description));
            return success('Invoice Updated Successfully',$invoice);
        }catch(Exception $ex){
            return error('Invocie Not Found',$ex);
        }
    }

    public function listInvoice(){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        try{
        $invoice = $stripe->invoices->all();
            return success('Invoice List',$invoice);
        }catch(Exception $ex){
            return error('Invoice Not Found',$ex);
        }
    }

    public function deleteInvoice($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
          );
            $invoice = Invoice::where('invoice_id',$id)->first();
            if(isset($invoice)){
                $invoice->delete();
                $invoice = $stripe->invoices->delete(
                $id,
                []
            );
            return error('Invoice Deleted Successfully');
        }else{
            return error('Invoice Not Deleted');
        }
    }

}
