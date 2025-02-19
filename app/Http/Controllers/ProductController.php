<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use App\Models\ProductStatuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function products(){
        $categories = Categories::all();
        $products_statuses = ProductStatuses::all();
        // dd($categories);
        return view('products.all_products',compact('categories','products_statuses'));
    }

    public function add_product(Request $request){
        $return_array = ['status'=>false, 'message'=>''];
        $userId = Auth::user()->id;
        // dd($userId);


        $rules = [
            'product_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'total_quantity' => 'required|integer',
            'price' => 'required|numeric',
            'product_status_id' => 'required|integer',
            'category_id' => 'required|integer',
            'image_url' => 'nullable|mimes:jpg,png,jpeg|max:2048', 
        ];
        
            $product_name = $request->input('product_name');
            $description = $request->input('description');
            $total_quantity = $request->input('total_quantity');
            $price = $request->input('price');
            $product_status_id = $request->input('product_status_id');
            $category_id = $request->input('category_id');
        
        
        $validation = Validator::make($request->all(),$rules);
        if ($validation->fails()) {
            return response()->json(['status'=>false,'errors'=>$validation->errors()],422);
        }else{
            $product_data = new Products();
            $product_data->product_name = $product_name;
            $product_data->description = $description;
            $product_data->total_quantity = $total_quantity;
            $product_data->price = $price;
            $product_data->product_status_id = $product_status_id;
            $product_data->category_id = $category_id;

            $product_data->created_by = $userId;
            
            if($request->hasfile('image_url')){
                $image = $request->file('image_url');
                $extension = $image->getClientOriginalExtension();
                $file_name = Time().'.'.$extension;
                $image->move('images/categories',$file_name);
                $product_data->image_url = $file_name;
            }
           
            $product_saved = $product_data->save();
            if($product_saved){
                
                $return_array['status'] = true;
                $return_array['message'] = "Product Added Successfully";
                
            }else{
                $return_array['message'] = "Failed Please try again";
            }
        }

        return response()->json($return_array);
    }

    public function get_product_detail(Request $request){
        $return_array = ['status'=>false, 'message'=>''];

        $product_id = $request->input('product_id');
        $products_detail = Products::where('id',$product_id)->first();
        
        if(!empty($products_detail)){
            $return_array['status'] = true;
            $return_array['products_detail'] = $products_detail;
        }

       
        return response()->json($return_array);


    }
}
