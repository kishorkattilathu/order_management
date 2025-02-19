<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    public function all_orders(){
        return view('order.all_orders');
    }
    public function create_orders(){
        $customers = Customers::where('account_status','active')->get();
        $products = Products::where('total_quantity','>', 0)->get();
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
}
