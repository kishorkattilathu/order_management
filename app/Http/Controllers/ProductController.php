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

    public function delete_product_by_id(Request $request){
        // dd($_POST);
        $return_array = ['status'=>false, 'message'=>'', 'data' => null];

        $product_id = $request->input('product_id');
        if ($product_id) {
            $delete_product = Products::where('id',$product_id)->first();
            $inactive = 2;
             $delete_product->product_status_id =$inactive;
             $deleted = $delete_product->save();
            if($deleted){
                $return_array['status'] = true;
                $return_array['message'] = 'product deleted successfully';

            }else{
                $return_array['message'] = 'Failed try again';

            }
        }else{
            $return_array['message'] = 'product Not Found';

        }
        return response()->json($return_array);
    }

    public function add_product(Request $request){
        // dd($_POST);
        $return_array = ['status'=>false, 'message'=>''];
        $userId = Auth::user()->id;
        // dd($userId);

        

        $rules = [
            'product_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'total_quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
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

            $product_id = $request->input('product_id');

            if($product_id){
                $product_data = Products::find($product_id);
                
            }else{
                $product_data = new Products();
            }
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
                dd('saved');
                // $product_saved = $product_data->save();
                if($product_saved){
                    
                    $return_array['status'] = true;
                    $return_array['message'] = "Product Saved Successfully";
                    
                }else{
                    $return_array['message'] = "Failed Please try again";
                }
            }
            
            return response()->json($return_array);
        }

    

    public function get_product_detail(Request $request){
        
        $return_array = ['status'=>false, 'message'=>''];

        $product_id = $request->input('product_id');

        $stored_products = session()->get('products', []);
        if(!in_array($product_id, $stored_products)){
            
            $products_detail = Products::where('id',$product_id)->first();
            $stored_products[] =['product_id'=> $product_id,'qty'=> 1,'price' =>$products_detail->price,'name'=>$products_detail->product_name, 'total_price'=>''];
            session()->put('products',$stored_products);
        if($products_detail){
            $return_array['status'] = true;
            $return_array['products_detail'] = $products_detail;
        }else{
            $return_array['message'] = 'Product not found';

        }
        }else{
            $return_array['message'] = 'Product already Added';
        }

        return response()->json($return_array);
    }

    public function removeProductFromSession(Request $request){
        $product_id = $request->input('product_id');
        $stored_products = session('products', []); 
        // dd($stored_products);

        $stored_products = array_filter($stored_products, function ($product) use ($product_id) {
            return $product['product_id'] != $product_id; 
        });

        session()->put('products', array_values($stored_products)); 

        return response()->json([
            'success' => true,
        ]);
    }

    public function update_quantity_in_session(Request $request){
        // dd($_POST);
        $product_id = $request->input('product_id');
        $product_qty = $request->input('product_qty');
        $new_price = $request->input('product_total_amount');
        $sessionProducts  = session('products',[]);
        foreach($sessionProducts as $key=>$product){
            if($product['product_id'] == $product_id){
                $sessionProducts[$key]['total_price'] = $new_price;
                $sessionProducts[$key]['qty'] = $product_qty;
            }
        }
        session(['products'=> $sessionProducts]);
        return response()->json(['status' => true, 'message'=> 'session updated']);

    }

    public function products_datatable(Request $request){

        // dd($_POST);

        $columns = ['id','product_name','category_id','total_quantity','sold_quantity','price','product_status_id','image_url'];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalData = Products::count();
        $totalFiltered = $totalData;
        $status_id = $request->input('product_status_id');
        $category_id = $request->input('category_id');
        if($category_id == 'all'){
            $query = Products::with('status','category')->where('product_status_id', $status_id);
        }else{
            $query = Products::with('status','category')->where([['product_status_id', $status_id],['category_id', $category_id]]);
        }
        
        
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('product_name', 'LIKE', "%{$search}%");    
            });

            // Update filtered data count
        }
        $totalFiltered = $query->count();

        // Apply ordering, limit, and offset
        $products = $query->orderBy($order, $dir)
                            ->offset($start)
                            ->limit($limit)
                            ->get();

        // Prepare data for DataTables
        $data = [];
        foreach ($products as $product) 
        {
            $nestedData['id'] = $product->id ?? 'Not specified';
            $nestedData['product_name'] = $product->product_name ?? 'Not specified';
            $nestedData['total_quantity'] = $product->total_quantity ?? 'Not specified';
            $nestedData['sold_quantity'] = $product->sold_quantity ?? 'Not specified';
            $nestedData['price'] = $product->price ?? 'Not specified';
            $nestedData['product_status_id'] = $product->status->title ?? 'Not specified';
            $nestedData['category_name'] = $product->category->name ?? 'Not specified';
        
            $nestedData['image_url'] =  $product->image_url ?'<img src="'.asset('images/categories/'.$product->image_url).'" class="w-50">' : 'Not specified';

            $nestedData['action'] = '<button class="btn btn-secondary btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0 me-2" title="Edit" OnClick = "open_edit_product_modal('.$product->id.')">Edit</button>';
            
            $nestedData['action'] .=  '<button class="btn btn-danger btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" onClick = "delete_product('.$product->id.')" title="Delete product"> Delete
            </button>';
                        
            $data[] = $nestedData;
        }
        // Return response in JSON format
        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
        return response()->json($json_data);
    }

    public function dumpsession(Request $request){
        $session = $request->session()->all();
        dd($session);
    }
}
