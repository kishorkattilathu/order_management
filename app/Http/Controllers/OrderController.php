<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Categories;
use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\OrderStatuses;
use App\Models\Products;
use App\Models\ProductStatuses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{

   

    public function storeCustomerSession(Request $request){
        $customer_id = $request->input('customer_id');
        Session::put('customer_id', $customer_id);
        return response()->json(['status'=> true, 'message'=> 'Customer selected']);
    }

    public function all_orders(Request $request){
        $request->session()->forget(['products','total_price_sum','customer_id','pre_orders']);
        $order_status = OrderStatuses::all();
        return view('order.all_orders',compact('order_status'));
    }

    public function get_all_orders(){
       
        $orders = Orders::with(['customer', 'orderDetails.product'])->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function create_orders(Request $request){
       
        $session = $request->session()->get('products');
        $total_price_sum = $request->session()->get('total_price_sum');
        if (!empty($session)) {
           
            $order_items = $session; 
            
            $grand_total_price = $total_price_sum ?? 0; 

        } else {
            $order_items = collect();
            $grand_total_price = $total_price_sum ?? 0; 

        }
        $categories = Categories::all();
        $products_statuses = ProductStatuses::all();

        $customers = Customers::where('account_status','active')->get();
        $products = Products::where([['total_quantity','>', 0],['product_status_id',1]])->get();
        return view('order.create_orders',compact('customers','products','categories','products_statuses','order_items','grand_total_price'));
    }
    public function create_final_order(Request $request){
        // dd($_POST);
        $return_array = ['status'=>false, 'message'=>''];
        $rules = [ 'customer_id'  => ['required'], 

        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
             return response()->json(['status'=>false,'errors'=>$validator->errors()],422);
        }else{

            $customer_id = $request->input('customer_id')?? '';
        }
        $customer = Customers::where('id',$customer_id)->first();
        $stored_products = session('products', []); 
        // echo "<pre>";print_r($stored_products);echo"</pre>";die('stored_products');
        foreach ($stored_products as $key => $product) 
            {
                $product_data = Products::find($product['product_id']);
                $is_pre_order = $product_data->is_pre_order;
                $requested_qty = $product['qty'];
                $qty_in_stock = $product_data->total_quantity;
                if(!$is_pre_order && $qty_in_stock < $requested_qty ){
                    // $return_array['message'] = 'Stock is less then you order';
                    return response()->json(['status'=>false, 'message'=>'Stock is less then you order']);
                }
            }
            // echo "<pre>";print_r($is_pre_order);echo"</pre>";die('stored_products');
        
        // dd($stored_products);
        $order = new Orders();
        $order->customer_id = $customer_id;
        // $order->total_quantity = 
        $qty = $order->total_quantity = array_sum(array: array_column($stored_products,'qty'));
        // $order->total_amount = 
        $total = $order->total_amount = array_sum(array: array_column($stored_products,'total_price'));
        // dd($qty);
        // dd($total);
        $order->order_date = date('Y-m-d H:i:s');
        $order->order_status_id = 1;
        // dd($order);
        // echo "<pre>";print_r($order);echo"</pre>";die('order');

        $order->save();
        if($order->id){
            foreach ($stored_products as $key => $product) 
            {

                $order_detail = new OrderDetails();
                $order_detail->order_id = $order->id;
                $order_detail->product_id = $product['product_id'];
                $order_detail->product_quantity = $product['qty'];
                $order_detail->product_amount = $product['price'];
                $order_detail->product_total_amount = $product['qty']*$product['price'];
                $order_detail->order_status_id = 1;
                $order_detail->save();
                $product_detail = Products::find($product['product_id']);
                if(!$product_detail->is_pre_order){
                    if ($product_detail->total_quantity >=$product['qty']) {
                        $product_detail->total_quantity -= $product['qty'];
                        $product_detail->sold_quantity += $product['qty'];
                        $product_detail->save();
                    }else{
                        $return_array['message'] = 'Stock is less then you order';

                    }

                }
                else{
                    $in_stock = $product_detail->total_quantity;
                        $calculated_stock = $in_stock -  $product['qty'];
                        $product_detail->total_quantity = max(0, $calculated_stock);
                        $product_detail->sold_quantity +=  $product['qty'];
                        $product_detail->save();
                }
            }
            Mail::to($customer->email)->queue(new OrderConfirmationMail($order));
                
            $return_array['status'] = true;
            $return_array['message'] = 'Order Created Successfully';
            $return_array['redirect_url'] = url('/all_orders');
        }
    else{
        $return_array['message'] = 'Failed to add in order details';

    }
      
    return response()->json($return_array);
    }
    

    public function orders_datatable(Request $request){

        $columns = ['id','total_quantity','total_amount','order_date','order_status_id'];

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0); 
        $order = $columns[$orderColumnIndex] ?? 'id'; 
        $dir = $request->input('order.3.dir', 'desc');

        $totalData = Orders::count();
        $totalFiltered = $totalData;

        $query = Orders::with('status','customer');
        
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%");
            });

            // Update filtered data count
            $totalFiltered = $query->count();
        }

        // Apply ordering, limit, and offset
        $orders = $query->orderBy($order, $dir)
                            ->offset($start)
                            ->limit($limit)
                            ->get();

        // Prepare data for DataTables
        $data = [];
        foreach ($orders as $order) 
        {
            $nestedData['id'] = $order->id ?? 'Not specified';
            // $nestedData['order_type'] = $order->order_type ?? 'Not specified';
            // $nestedData['order_type'] = $order->order_type == 1 ? 'Regular' : 'Pre-Order';
            $nestedData['customer_name'] = $order->customer->first_name ?? 'Not specified';
            $nestedData['customer_email'] = $order->customer->email ?? 'Not specified';
            $nestedData['total_quantity'] = $order->total_quantity ?? 'Not specified';
            $nestedData['total_amount'] = '₹' .$order->total_amount ?? 'Not specified';
            $nestedData['order_date'] = $order->order_date ? Carbon::parse($order->order_date)->format('m/d/Y') : 'Not specified';
            $nestedData['order_time'] = $order->order_date ? Carbon::parse($order->order_date)->format('h:i A') : 'Not specified';
            $nestedData['order_status'] = $order->status->title ?? 'Not specified';

            if($order->order_status_id == 6 ){
                $nestedData['action'] =  '<button class="btn btn-primary btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0"  title="Cancel order"> Cancelled</button>';
            }
            elseif($order->order_status_id == 5 ){
                $nestedData['action'] =  '<button class="btn btn-secondary type="button" onClick = "open_order_modal('.$order->id.')" style="margin-left: 5px;" btn-icon btn-circle btn-sm hov-svg-white mt-2  mt-sm-0"  title="View order"> View</button>';
               
            }else{
                $nestedData['action'] =  '<button class="btn btn-secondary type="button" onClick = "open_order_modal('.$order->id.')" style="margin-left: 5px;" btn-icon btn-circle btn-sm hov-svg-white mt-2  mt-sm-0"  title="View order"> View</button>';

                $nestedData['action'] .=  '<button class="btn btn-danger btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" onClick = "cancel_order('.$order->id.')" title="Cancel order"> Cancel</button>';
                
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

    public function cancel_order(Request $request){

        $return_array = ['status'=>false, 'message'=>''];
        $order_id = $request->input('order_id');
        // dd($order_id);

        if($order_id){
            $order_data = Orders::find($order_id);
            // dd($order_data);
            $order_status_id = $order_data->order_status_id;
            if($order_status_id != 5){
                $order_data->order_status_id = 6;
                $updated_order_status = $order_data->save();
                // $updated_order_status = 1;
                if($updated_order_status){

                    $order_details = OrderDetails::where('order_id',$order_id)->get();
                    // dd($order_details);
                    if ($order_details->isNotEmpty()) {
                        foreach ($order_details as $order_detail) {
                            

                            $product_id = $order_detail->product_id;
                            $used_quantity = $order_detail->product_quantity;

                            $product_details = Products::find($product_id);


                            $product_details->total_quantity += $used_quantity;
                            $product_details->sold_quantity = max(0, $product_details->sold_quantity - $used_quantity);

                            $data_reversed = $product_details->save();
                            if($data_reversed){
                                $return_array['message'] = 'Product Cancelled Successfully';
                                $return_array['status'] = true;

                            }else{
                                $return_array['message'] = 'Failed to update';
                            }
                        }
                    }
                }else{
                    $return_array['message'] = 'Failed try again';

                }
            }else{
                $return_array['message'] = 'product delivered and cant cancel';
                
            }
        }else{
            $return_array['message'] = 'Order not found';
        }
        return response()->json($return_array);

    }

    public function testMail(){
        $order['id'] = 1;
        $order['total_amount'] = 100;
        $is_sent = Mail::to('kk26july@gmail.com')->queue(new OrderConfirmationMail($order));
        if($is_sent){
            return response()->json(['message'=> 'Mail Sent', 'status'=>true]);
        }else{
            return response()->json(['message'=> 'Failed to send meail', 'status'=>false]);

        }
    }

    public function get_order_detail_by_id(Request $request){
        $return_array = ['status'=> false, 'message'=>''];
        $order_id = $request->input('order_id');
    
        $order_detail = Orders::select('orders.*', 'customers.first_name as customer_name','products.product_name'
        ,'order_details.product_id','order_details.product_quantity','order_details.product_amount','order_details.product_total_amount','order_statuses.title','order_statuses.id as order_status_id')
        ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
        ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
        ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
        ->leftJoin('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
        ->where('orders.id', $order_id)
        ->get();
        if($order_detail){
            $return_array['status'] = true;
            $return_array['message'] = "Data Fetched Successfully";
            $return_array['data'] = $order_detail;
        }else{
            $return_array['message'] = "Order Not Found";

        }

        return response()->json($return_array);
    }

    public function update_order_status(Request $request){
        $return_array = ['status'=>false, 'message'=>''];

        $order_status_id = $request->input('order_status_id');
        $order_id = $request->input('order_id');

        $order_data = Orders::find($order_id);
        $status = $order_data->order_status_id;
        $order_type = $order_data->order_type;
        if($status == 5 ){
            return response()->json(['message' => 'Order already delivered cant update']);
        }

        if($order_type == 2){
            // return response()->json(['message' => 'Product is out of stock']);
            $order_details = OrderDetails::where('order_id',$order_id)->get();
            // dd($order_details);
            foreach($order_details as $order){
                $product_id = $order->product_id;
                $check_product_stock = Products::find($product_id);
                $check_stock = $check_product_stock->product_quantity;
                dd($check_product_stock);
            }
            
        }
       
        
        
        return response()->json($return_array);
    }
}
