<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use App\Models\ProductStatuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function products(){
        $categories = Categories::all();
        $products_statuses = ProductStatuses::whereIn('id', [1, 2])->get();
        // dd($categories);'
        return view('products.all_products',compact('categories','products_statuses'));
    }

    public function delete_product_by_id(Request $request){
        $return_array = ['status'=>false, 'message'=>'', 'data' => null];

        $product_id = $request->input('product_id');
        if ($product_id) {
            
            $product = Products::find($product_id);
            $deleted = $product->delete();
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

        $rules = [
            'product_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'total_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:1',
            'product_status_id' => 'required|integer',
            'is_pre_order' => 'required',
            'image_url' => 'nullable|mimes:jpg,png,jpeg|max:2048', 
        ];
        
            $product_name = $request->input('product_name');
            $description = $request->input('description');
            $total_quantity = $request->input('total_quantity');
            $is_pre_order = $request->input('is_pre_order');
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
                $product_data->is_pre_order = $is_pre_order;
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
                    $return_array['message'] = "Product Saved Successfully";
                    
                }else{
                    $return_array['message'] = "Failed Please try again";
                }
            }
            return response()->json($return_array);
        }

    

    public function get_product_detail(Request $request){
        // dd($_POST);
        $return_array = ['status'=>false, 'message'=>''];

        $product_id = $request->input('product_id');

        $stored_products = session()->get('products', []);
        // dd($stored_products);
        $stored_product_ids = array_column($stored_products,'product_id');
       // echo $product_id;

        if(!in_array($product_id, $stored_product_ids)){
            
            $products_detail = Products::where('id',$product_id)->first();
           
            $stored_products[] =['product_id'=> $product_id,'qty'=> 1,'price' =>number_format($products_detail->price, 2, '.', ''),'name'=>$products_detail->product_name, 'total_price'=>number_format($products_detail->price, 2, '.', ''),'stock_quantity'=>$products_detail->total_quantity];
            // dd($stored_products);
            session()->put('products',$stored_products);
            $total_price_sum = array_sum(array_column($stored_products, 'total_price'));
            session()->put('total_price_sum', $total_price_sum);
            $get_total_price_sum = session()->get('total_price_sum');
            $stock_quantity = session()->get('stock_quantity');
            // dd($stock_quantity);
        if($products_detail){
            $return_array['status'] = true;
            $return_array['products_detail'] = $products_detail;
            $return_array['total_price_sum'] = $get_total_price_sum;
            $return_array['stock_quantity'] = $stock_quantity;
        }else{
            $return_array['message'] = 'Product not found';
        }
        }else{
            $return_array['message'] = 'Product already Added';
        }

        return response()->json($return_array);
    }

    public function get_product_detail_by_id(Request $request){
        // dd($_POST);
        $return_array = ['status'=>false, 'message'=>''];

        $product_id = $request->input('product_id');
        $products_detail = Products::where('id',$product_id)->first();
        $html="";
        $products_statuses = ProductStatuses::all();
        foreach ($products_statuses as $product_status){
            $html.='<option value="'.$product_status->id.'">'.$product_status->title.'</option>';
        }

        if($products_detail){
            $return_array['status'] = true;
            $return_array['products_detail'] = $products_detail;
            $return_array['option_html'] = $html;
            

        }else{
            $return_array['message'] = 'Product not found';
        }
        return response()->json($return_array);
    }

    public function removeProductFromSession(Request $request){
        // dd($request->all());
        $product_id = $request->input('product_id');
        $stored_products = session('products', []); 
        
        $product_amount = 0;
        $productDeleted = [];
            foreach($stored_products as $product){
                if ($product['product_id'] == $product_id) {
                    $product_amount = $product['total_price'];
                    break;
                }
            }
            $productCount=count($stored_products);
            $total_amount = Session::get('total_price', 0);
            $new_total_amount = max(0, $total_amount - $product_amount);
            Session::put('total_price', $new_total_amount);
            
            $productDeleted = array_filter($stored_products, function ($product) use ($product_id) {
                return $product['product_id'] != $product_id;


        // $stored_products = array_filter($stored_products, function ($product) use ($product_id) {
        //     return $product['product_id'] != $product_id; 
        });
        session()->put('products', array_values($productDeleted)); 
        if($productCount>count($productDeleted)){
            return response()->json([
                'success' => true,'message' => 'Removed product'
            ]);
        }
        else{
            return response()->json([
                'success' => false,'message' => 'Failed product'
            ]);  
        }
        
    }
   

    public function update_quantity_in_session(Request $request){

        $product_id = $request->input('product_id');
        $product_qty = $request->input('product_qty');
       
        $products = Products::find($product_id);
        // dd($product_qty);
        $check_stock=0;
        if(!empty($products)){
            $check_stock = $products->total_quantity;   
        }
        
        if($check_stock < $product_qty && $products->is_pre_order == 0){
            // dd($request->session()->get('product_qty'));
            $sessionProducts = $request->session()->get('products', []);

            $session_qty = 1; 
            foreach ($sessionProducts as $product) {
                if ($product['product_id'] == $product_id) {
                    $session_qty = $product['qty'];
                    break;
                }
            }
            // if($request->session()->get('pre_orders') != 1){
            // dd($session_qty);
                 
                return response()->json(['status'=>false,'product_id'=>$product_id ,'product_qty'=>$session_qty,'message'=> "oops stock is only.$check_stock."]); 

            // }

            
        }
        $new_price = $request->input('product_total_amount');
        $sessionProducts  = session('products',[]);
        foreach($sessionProducts as $key=>$product){
            if($product['product_id'] == $product_id){
                $sessionProducts[$key]['total_price'] =number_format($new_price,2, '.', '') ;
                $sessionProducts[$key]['qty'] = $product_qty;
            }
        }
        session(['products'=> $sessionProducts]);
        $stored_products = session('products',[]);
        $total_price_sum = array_sum(array_column($stored_products, 'total_price'));
        session()->put('total_price_sum', $total_price_sum);
        $get_total_price_sum = session()->get('total_price_sum');
        // dd($get_total_price_sum);
        return response()->json(['status' => true, 'message'=> 'session updated','product_data' => $stored_products,'total_price_sum'=>$get_total_price_sum]);
    }

    public function pre_order_checkbox(Request $request){
    //   dd($_POST);
        $pre_orders = $request->input('pre_order_check');
        // dd($pre_orders);
        if($pre_orders == 1 ){
            $request->session()->forget(['products','total_price_sum','pre_orders']);
            session()->put('pre_orders',$pre_orders);
            $return_array['status'] = true;
        }else{
            $return_array['status'] = true;

            $request->session()->forget(['products','total_price_sum','pre_orders']);
        }
        return response()->json($return_array);
    }

    public function products_datatable(Request $request){
        
        $columns = ['id','product_name','category_id','total_quantity','sold_quantity','price','product_status_id','image_url'];


        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0); 
        $order = $columns[$orderColumnIndex] ?? 'id'; 
        $dir = $request->input('order.3.dir', 'desc');


        $totalData = Products::count();
        $totalFiltered = $totalData;
        $product_status_id = $request->input('product_status_id');
        $category_id = $request->input('category_id');

        $query = Products::with('status', 'category');
        if($request->input('path')){
            $query->where('product_status_id',1);
        }
        if ($category_id !== 'all') {
            $query->where('category_id', $category_id);
        }
        
        if ($product_status_id !== 'all') {
            $query->where('product_status_id', $product_status_id);
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
            // dd($product);
            // dd($product->product_status_id);
            $nestedData['id'] = $product->id ?? 'Not specified';
            $nestedData['product_name'] = $product->product_name ?? 'Not specified';
            if(!$request->input('path')){

            $nestedData['total_quantity'] = $product->total_quantity ?? 'Not specified';
                }
                else{
                    if( $product->is_pre_order == 1){

                        $nestedData['total_quantity'] =  'Pre-Orders';
                    }else{

                        $nestedData['total_quantity'] = $product->total_quantity < 1 ? 'Out of Stock' : 'In stock';
                    }
                }
                if(!$request->input('path')){

                    $nestedData['sold_quantity'] = $product->sold_quantity ?? 'Not specified';
                }
            $nestedData['price'] = isset($product->price) ? '&#8377;'. $product->price :'Not specified';
                if(!$request->input('path')){
                
                    $nestedData['product_status_id'] = $product->status->title ?? 'Not specified';
                }
            $nestedData['category_name'] = $product->category->name ?? 'None';
            $nestedData['image_url'] =  $product->image_url ?'<img src="'.asset('images/categories/'.$product->image_url).'" class="img-fluid rounded shadow" style="width: 150px; height: auto;">' : '<img src="'.asset('images/categories/default.png').'" class="img-fluid rounded shadow" style="width: 150px; height: auto;">';
            $nestedData['action'] = '';
            if(!$request->input('path')){
                

                $nestedData['action'] = '<button class="btn btn-secondary btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0 me-2 edit_btn" title="Edit" OnClick = "open_edit_product_modal('.$product->id.')">Edit</button>';
                
                $nestedData['action'] .=  '<button class="btn btn-danger btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0 delete_btn" onClick = "delete_product('.$product->id.')" title="Delete product"> Delete
                </button>';
            }else{

                // dd($product->product_status_id);
                // dd($product->is_pre_order);
                // dd($product->total_quantity);
                // if($pre_orders == 0){
                    if($product->product_status_id == 1 && $product->is_pre_order == 0 && $product->total_quantity == 0){
                        $nestedData['action'] = '<button disabled
                            class="btn btn-sm  btn-danger add_order_btn" 
                            data-name="" 
                            data-id="">
                            <i class="bi bi-stop"></i>
                        </button>';
                    }
                    elseif($product->product_status_id == 1 && $product->is_pre_order == 1 && $product->total_quantity == 0){
                        $nestedData['action'] = '<button 
                                class="btn btn-sm  btn-primary add_order_btn" 
                                data-name="' . $product->product_name . '" 
                                data-id="' . $product->id . '">
                                <i class="bi bi-plus"></i>
                            </button>'; 
                    }else{
                        $nestedData['action'] = '<button 
                            class="btn btn-sm  btn-primary add_order_btn" 
                            data-name="' . $product->product_name . '" 
                            data-id="' . $product->id . '">
                            <i class="bi bi-plus"></i>
                        </button>'; 
                    }
                      
                 
            }
            
                        
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
