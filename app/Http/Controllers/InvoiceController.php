<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request){

        $invoice = $this->stripe->invoices->create([
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
        try{
        $invoice = $this->stripe->invoices->retrieve(
        $id,
            []
        );
            return success('Invoice Details',$invoice);
        }catch(Exception $ex){
            return error('Invoice Not Found',$ex);
        }
    }

    public function updateInvoice(Request $request,$id){
        try{
        $invoice = $this->stripe->invoices->update(
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
        try{
        $invoice = $this->stripe->invoices->all();
            return success('Invoice List',$invoice);
        }catch(Exception $ex){
            return error('Invoice Not Found',$ex);
        }
    }

    public function deleteInvoice($id){
            $invoice = Invoice::where('invoice_id',$id)->first();
            if(isset($invoice)){
                $invoice->delete();
                $invoice = $this->stripe->invoices->delete(
                $id,
                []
            );
            return error('Invoice Deleted Successfully');
        }else{
            return error('Invoice Not Deleted');
        }
    }

}
