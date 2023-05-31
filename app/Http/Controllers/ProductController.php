<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
class ProductController extends Controller
{
    use ListingApiTrait;
    public function createProduct(Request $request){
        $product = $this->stripe->products->create([
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
        $product = $this->stripe->products->retrieve(
            $id,
            []
        );
        if($product){
            return success('Product Details',$product,200);
        }
        return error('Product Not Found',404);
    }

    public function listProduct(){
        $products = $this->stripe->products->all();
        if($products){
            $data = $this->filterSearchPagination($products,$searchable_fields ?? null);
            return success('Products List',[
                'product'        => $data['query'],
                'count'          => $data['count']
            ]);
        }
        return error('Product Not Found',404);
    }

    public function updateProduct(Request $request,$id){
        $product = $this->stripe->products->update(
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
        $product = Product::where('product_id',$id)->first();
        if(isset($product)){
            $product->delete();
            $product = $this->stripe->products->delete(
            $id,
            []
            );
            return success('Product deleted Successfully');
        }else{
            return error('Product Not Deleted');
        }
    }
}
