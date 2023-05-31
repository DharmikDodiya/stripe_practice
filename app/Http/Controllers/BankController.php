<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;

class BankController extends Controller
{
    use ListingApiTrait;
    public function createBank(Request $request,$cid){

        $this->validate($request,[
            'country'           => 'required',
            'name'              => 'required|string',
            'ac_number'         => 'required'
        ]);
        $btoken = $this->stripe->tokens->create([
        'bank_account' => [
            'country' => $request->country,
            'currency' => 'usd',
            'account_holder_name' => $request->name,
            'account_holder_type' => 'individual',
            'routing_number' => '110000000',
            'account_number' => $request->ac_number,
        ],
        ]);
        $bank = $this->stripe->customers->createSource(
        $cid,
        ['source' => $btoken->id]
        );
        return success('Bank Created Successfully',$bank);
    }

    public function listBank($cid){
        $bank = $this->stripe->customers->allSources(
        $cid,
        [
            'object' => 'bank_account',
            ]
        );
        $data = $this->filterSearchPagination($bank,$searchable_fields ?? null);
        return success('Bank List',[
            'Bank'        => $data['query'],
            'count'       => $data['count']
        ]);
    }

    public function verifyBank(Request $request,$cid,$bid){
        try{
            $bank = $this->stripe->customers->verifySource(
            $cid,
            $bid,
            ['amounts' => [32, 45]]
            );
            return success('Bank Verified Successfully',$bank);
        }catch(Exception $ex){
            return error('Bank is Already Verified');
        }
    }

    public function getBank($cid,$bid){
        $bank = $this->stripe->customers->retrieveSource(
        $cid,
        $bid,
        []
        );
        if(isset($bank)){
            return success('Bank Details',$bank);
        }
        return error('Bank Not Found');
    }

    public function retrieveBank($cid,$bid){
        $bank = $this->stripe->customers->retrieveSource(
            $cid,
            $bid,
        []
        );
        if(isset($bank)){
            return success('Retrieve Bank Account',$bank);
        }
            return error('Bank Account Not Found');
    }

    public function updateBank($cid,$bid,Request $request){
        $bank = $this->stripe->customers->updateSource(
        $cid,
        $bid,
        ['metadata' => ['order_id' => '6735','account_holder_name' => $request->account_holder_name , 'account_holder_type' => $request->account_holder_type]]
        );
        return success('Bank Updated Successfully',$bank);
    }

    public function deleteBank($cid,$bid){
        try{
            $this->stripe->customers->deleteSource(
            $cid,
            $bid,
            []
            );
        return success('Bank Deleted Successfully');
        }
        catch(Exception $ex){
            return error('Bank Account Not Found',$ex);
        }
    }
}
