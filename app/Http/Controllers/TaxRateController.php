<?php

namespace App\Http\Controllers;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    use ListingApiTrait;
    public function createTaxRate(Request $request){
        $this->validate($request,[
            'display_name'      => 'required|in:VAT,GST,Custom',
            'description'       => 'required',
            'jurisdiction'      => 'nullable',
            'percentage'        => 'required',
            'inclusive'         => 'required|boolean'
        ]);

        $flag = $request->inclusive ? true:false;
        $taxrate = $this->stripe->taxRates->create([
            'display_name' => $request->display_name,
            'description' => $request->description,
            'jurisdiction' => $request->jurisdiction ?? 'DE',
            'percentage' => $request->percentage,
            'inclusive' => $flag,
        ]);
        if($taxrate){
            return success('TaxRate Created Successfully',$taxrate);
        }
        return error('TaxRate Not Created');
    }

    public function retrieveTaxRate($id){
        $taxrate = $this->stripe->taxRates->retrieve(
            $id,
        []
        );
        if($taxrate){
            return success('TaxRate Retrieve Successfully',$taxrate);
        }
            return error('TaxRate Not Found');
    }

    public function listTaxRate(){
        $taxrates = $this->stripe->taxRates->all();
        if($taxrates){
            $data = $this->filterSearchPagination($taxrates,$searchable_fields ?? null);
            return success('TaxRate List',[
                'TaxRate'        => $data['query'],
                'count'          => $data['count']
            ]);
        }
    }

    public function updateTaxRate(Request $request,$id){
        $this->validate($request,[
            'active'    => 'nullable'
        ]);
        $taxrate = $this->stripe->taxRates->retrieve(
            $id,
        []
        );
        if($request->active){
            //$flag = $request->active ? true:false;
            $taxrate = $this->stripe->taxRates->update(
                $id,
                ['active' => $request->active]
                );
        }
        if($taxrate){
            return success('TaxRate Updated Successfully',$taxrate);
        }
            return error('TaxRate Not Updated');
    }

}

