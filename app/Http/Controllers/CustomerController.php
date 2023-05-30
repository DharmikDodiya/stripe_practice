<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function createCustomer(Request $request){
        $customer = $this->stripe->customers->create([
            'description' => $request->description,
            'email'       => $request->email,
        ]);

        return success('Customer Created Successfully',$customer);
    }

    public function getCustomer($id){
        $customer = $this->stripe->customers->retrieve(
            $id,
            []
        );
        if(isset($customer)){
            return success('Customer Details',$customer);
        }
        return error('Customer Not Found');
    }

    public function listCustomer(){
        $customersData = $this->stripe->customers->all();
        if(isset($customersData)){
            return success('List All Customers',$customersData);
        }
        return error('Customers Not Found');
    }

    public function deleteCustomer($id){
        try{
            $this->stripe->customers->delete(
            $id,
        []
            );
            return success('Customer Deleted Successfully');
        }catch(Exception $ex){
            return error('Customer Not Deleted');
        }
    }
}

