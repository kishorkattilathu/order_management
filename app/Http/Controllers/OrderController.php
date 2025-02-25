<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\OrderStatuses;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    public function all_orders(){
        
        return view('order.all_orders');
    }

    public function get_all_orders(){
        $orders = Orders::with(['customer', 'orderDetails.product'])->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function create_orders(){
        $customers = Customers::where('account_status','active')->get();
        // $products = Products::where('total_quantity','>', 0)->get();
        $products = Products::where([['total_quantity','>', 0],['product_status_id',1]])->get();
        return view('order.create_orders',compact('customers','products'));
    }

    public function create_final_order(Request $request){
        $return_array = ['status'=>false, 'message'=>''];
        // dd($_POST);
        $rules = [
                'customer_id'        => ['required'], 
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
             return response()->json(['status'=>false,'errors'=>$validator->errors()],422);
        }else{
             $customer_id = $request->input('customer_id')?? '';
             $product_ids = $request->input('product_ids')?? '';
             $product_prices = $request->input('product_price')?? '';
             $product_qtys = $request->input('product_qty')?? '';

            $customer = Customers::where('id',$customer_id)->first();

            $order = new Orders();
            $order->customer_id = $customer_id;
            $order->total_quantity = array_sum($product_qtys);
            $order->total_amount = array_sum($product_prices);
            $order->order_date = date('Y-m-d');
            $order->order_status_id = 1;
            $order->save();
            $order_id = $order->id;
            
            if($order_id){
                foreach ($product_ids as $key => $product_id) 
                {
                    $product_amount =  $product_prices[$key];
                    $product_quantity =  $product_qtys[$key];
                    $product_total_amount = (float)$product_amount * (int)$product_quantity;


                    $order_detail = new OrderDetails();
                    $order_detail->order_id = $order_id;
                    $order_detail->product_id = $product_id;
                    $order_detail->product_quantity = $product_quantity;
                    $order_detail->product_amount = $product_amount;
                    $order_detail->product_total_amount = $product_total_amount;
                    $order_detail->order_status_id = 1;
                    $order_detail_saved = $order_detail->save();

                    if($order_detail_saved){
                        $product_detail = Products::where('id',$product_id)->first();
                        $product_detail->total_quantity = (int)$product_detail->total_quantity - (int)$product_quantity;
                        $product_detail->sold_quantity = (int)$product_detail->sold_quantity + (int)$product_quantity;
                        $product_detail->save();

                        
                    }
                }
                
                Mail::to($customer->email)->queue(new OrderConfirmationMail($order));
                
                $return_array['status'] = true;
                $return_array['message'] = 'Order Created Successfully';
                $return_array['redirect_url'] = url('/all_orders');

            }
           
            
        }
        return response()->json($return_array);
    }

    public function orders_datatable(Request $request){

        $columns = ['id','total_quantity','total_amount','order_date','order_status_id'];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir','desc');

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
            $nestedData['customer_name'] = $order->customer->first_name ?? 'Not specified';
            $nestedData['customer_email'] = $order->customer->email ?? 'Not specified';
            $nestedData['total_quantity'] = $order->total_quantity ?? 'Not specified';
            $nestedData['total_amount'] = $order->total_amount ?? 'Not specified';
            $nestedData['order_date'] = $order->order_date ?? 'Not specified';
            $nestedData['order_status'] = $order->status->title ?? 'Not specified';

            if($order->order_status_id != 6 ){
                $nestedData['action'] =  '<button class="btn btn-danger btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" onClick = "cancel_order('.$order->id.')" title="Cancel order"> Cancel</button>';
            }else{
                $nestedData['action'] =  '<button class="btn btn-primary btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0"  title="Cancel order"> Cant cancel</button>';
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

        if($order_id){
            $order_data = Orders::find($order_id);
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
}
