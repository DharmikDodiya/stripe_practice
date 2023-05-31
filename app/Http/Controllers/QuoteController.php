<?php

namespace App\Http\Controllers;

use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    use ListingApiTrait;
    public function createQuote(Request $request){
        $this->validate($request,[
            'cusid'         => 'required',
            'priceid'       => 'required',
            'quantity'      => 'required'
        ]);
        $quote = $this->stripe->quotes->create([
        'customer' => $request->cusid,
        'line_items' => [
            [
            'price' => $request->priceid,
            'quantity' => $request->quantity,
            ],
        ],
        ]);
        if($quote){
            return success('Quote Created Successfully',$quote);
        }
            return error('Quote Not Created');
    }

    public function retrieveQuote($id){
        $quote = $this->stripe->quotes->retrieve(
        $id,
        []
        );
        if($quote){
            return success('Quote Details',$quote);
        }
            return error('Quote Not Found');
    }

    public function updateQuote(Request $request,$id){
        $quote = $this->stripe->quotes->update(
            $id,
        ['metadata' => ['order_id' => '6735','customer' => $request->cusid,'price' => $request->priceid, 'quantity' => $request->quantity]]
        );
        if($quote){
            return success('Quote Updated Successfully',$quote);
        }
            return error('Quote Not Updated');
    }

    public function finalizeQuote($id){
        $quote = $this->stripe->quotes->finalizeQuote(
            $id,
        []
        );
        if($quote){
            return success('Quote Finalize Successfully',$quote);
        }
            return error('Quote Not Finalize');
    }

    public function acceptQuote($id){
        $quote = $this->stripe->quotes->accept(
            $id,
        []
        );
        if($quote){
            return success('Quote Accept Successfully',$quote);
        }
            return error('Quote Not Accepted');
    }

    public function downloadPdf($id){
        $myfile = fopen("/tmp/tmp.pdf", "w");
        $pdf = $this->stripe->quotes->pdf($id, function ($chunk) use (&$myfile) {
            fwrite($myfile, $chunk);
        });
        //end
        fclose($myfile);
        return success('File Downloaded Successfully',$pdf);
    }

    public function listQuote(){
        $quote = $this->stripe->quotes->all();
        $searchable_fields = null;
        $data = $this->filterSearchPagination($quote,$searchable_fields);
        return success('Quote List',[
            'Quote'        => $data['query'],
            'count'          => $data['count']
        ]);
    }

    public function QuoteLineItem($id){
        $line_items = $this->stripe->quotes->allLineItems($id, );
        if($line_items){
            $data = $this->filterSearchPagination($line_items,$searchable_fields ?? null);
            return success('Quote LineItem List',[
                'QuoteLineItem'  => $data['query'],
                'count'          => $data['count']
            ]);
        }
            return error('ListItem Not Found');
    }

    public function QuoteUpfrontLineItem($id){
        $upfront_line_items = $this->stripe->quotes->allComputedUpfrontLineItems($id);
        if($upfront_line_items){
            $data = $this->filterSearchPagination($upfront_line_items,$searchable_fields ?? null);
            return success('Quote UpFrontLineItem List',[
                'QuoteUpfrontLineItem'        => $data['query'],
                'count'                       => $data['count']
            ]);
        }
            return error('Quote Upfront LineItem Not Found');
    }

    public function cancleQuote($id){
        $this->stripe->quotes->cancel(
            $id,
        []
        );
        return success('Quote Cancle Successfully');
    }

}
