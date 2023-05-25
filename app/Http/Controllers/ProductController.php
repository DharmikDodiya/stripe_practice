<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function createProduct(Request $request){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $product = $stripe->products->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $productData = Product::create([
            'product_id'        => $product->id,
            'name'              => $product->name,
            'description'       => $product->description,
            'type'              => $product->type
        ]);
        if(count($product) > 0){
            return success('Product Created Successfully',$product,$productData,200);
        }
        return error('Product Not Created',401);
    }

    public function getProduct($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $product = $stripe->products->retrieve(
            $id,
            []
        );
        if($product){
            return success('Product Details',$product,200);
        }
        return error('Product Not Found',404);
    }

    public function listProduct(){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $products = $stripe->products->all();
        if($products){
            return success('Product List',$products,200);
        }
        return error('Product Not Found',404);
    }

    public function updateProduct(Request $request,$id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $product = $stripe->products->update(
            $id,
            ['metadata' => ['order_id' => '6735','name' => $request->name,'description' => $request->description]]
        );

        $productData = Product::where('product_id',$id)->first();
        $productData->update($request->only(
            'name',
                'description'
        ));
        if($product){
            return success('Product Update Successfully',$product,200);
        }
        return error('Product Not Updated Successfully');
    }

    public function deleteProduct($id){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N9PI1SA4SjjlNffXOm2HtQ2zzoiol7xYb5YOZo0ifzWyk81AsLmUiM4vkL2SgbbcJ4WRNhrB4gxYRWIAvx1gB6j00pZlqtqic'
        );
        $product = Product::where('product_id',$id)->first();
        if(isset($product)){
            $product->delete();
            $product = $stripe->products->delete(
            $id,
            []
            );
            return success('Product deleted Successfully');
        }else{
            return error('Product Not Deleted');
        }
    }
}
