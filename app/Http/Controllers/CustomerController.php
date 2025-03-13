<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function customers(){
        return view('customers.all_customers');
    }

       
    public function update_customer_by_id(Request $request){
            $return_array = ['status'=>false, 'message'=>''];
            $rules = [
                     'f_name'     => ['required', 'string', 'min:2', 'max:50'],
                    'm_name'      => ['nullable', 'string', 'min:1', 'max:50'], 
                    'l_name'      => ['required', 'string', 'min:2', 'max:50'],
                    'updated_email' => ['required', 'email'],
                    'updated_phone' => ['required', 'digits:10'], 
                    'updated_address' => ['required', 'string', 'max:255'],
                    'updated_dob' => ['required', 'date', 'before:today'],
                    'updated_gender'        => ['required'], 

            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                 return response()->json(['status'=>false,'errors'=>$validator->errors()],422);
            }else{
                 $customer_id = $request->input('customer_id')?? '';
                 $first_name = $request->input('f_name')?? '';
                 $middle_name = $request->input('m_name')?? '';
                 $last_name = $request->input('l_name')?? '';
                //  $email = $request->has('updated_email')? $request->input('updated_email') : '';
                 $phone = $request->input('updated_phone')?? '';
                 $address = $request->input('updated_address')?? '';
                 $date_of_birth = $request->input('updated_dob')?? '';
                 $gender = $request->input('updated_gender')?? '';
    
                    $user_data =  Customers::where('id',$customer_id)->first();
                    if (!$user_data) {
                        return response()->json(['status' => false, 'message' => 'Customer not found'], 404);
                    }
                    $user_data->first_name = $first_name;
                    $user_data->middle_name = $middle_name ?: null;
                    $user_data->last_name = $last_name;
                    // $user_data->email = $email;
                    $user_data->phone = $phone;
                    $user_data->address = $address;
                    $user_data->date_of_birth = $date_of_birth;
                    $user_data->gender = $gender;
                    
                    $is_updated = $user_data->save();
    
                    if($is_updated){
                        $return_array['status'] = true;
                        $return_array['message'] = "Updated Successfully";
                        }
                    else{
                        $return_array['message'] = "Failed Try Again";
                    }
            }
            return response()->json($return_array);
        }
    public function get_customer_data_by_id(Request $request){
        $return_array = ['status'=>false, 'message'=>''];
        // dd($_POST);
        $customer_id = $request->input('customer_id') ?? '';
        // dd($customer_id);
        if($customer_id){
            $customer_data = Customers::find($customer_id);
            // dd($customer_data);
            $return_array['status'] = true;
            $return_array['data'] = $customer_data;
        }else{
            $return_array['message'] = "User does not exist";

        }
        return response()->json($return_array);
    }

    public function get_all_customers(Request $request){

        $columns = ['id','first_name','middle_name','last_name','email','phone','address','date_of_birth','gender','account_status'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalData = Customers::count();
        $totalFiltered = $totalData;

        $status = $request->input('customers_status');
        // dd($status);
        if($status == null || $status == 'active'){

            $query = Customers::where('account_status','active');
        }else{
            $query = Customers::where('account_status','inactive');

        }
        
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('middle_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('gender', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
                   
            });

        }
        // Update filtered data count
        $totalFiltered = $query->count();

        // Apply ordering, limit, and offset
        $Customers = $query->orderBy($order, $dir)
                            ->offset($start)
                            ->limit($limit)
                            ->get();

        // Prepare data for DataTables
        $data = [];
        foreach ($Customers as $customer) {
            // $nestedData['id'] = $customer->id;
        
            $nestedData['id'] = $customer->id ?? 'Not specified';
            $nestedData['first_name'] = $customer->first_name ?? 'Not specified';
            $nestedData['email'] = $customer->email ?? 'Not specified';
            $nestedData['phone'] = $customer->phone ?? 'Not specified';
            $nestedData['date_of_birth'] = Carbon::parse($customer->date_of_birth)->format('m-d-Y') ?? 'Not specified';
            $nestedData['gender'] = ucfirst($customer->gender ?? 'Not specified');

            $nestedData['account_status'] = $customer->account_status ?? 'Not specified';
           

            $nestedData['action'] = '<button class="btn btn-secondary btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0 me-2" title="Edit customer" OnClick = "open_edit_customer_modal('.$customer->id.')">Edit</button>';
            
            $nestedData['action'] .=  '<button class="btn btn-danger btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" onClick = "delete_customer('.$customer->id.')" title="Delete Customer"> Delete
            </button>';

            // $nestedData['action'] .= '<button class="btn btn-danger btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0 delete-btn" 
            // data-bs-toggle="modal" 
            // data-bs-target="#deleteModal"
            // data-id="'.$customer->id.'" 
            // title="Delete Customer"> 
            // Delete
            // </button>';
                        
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

    public function add_customers(Request $request){
        $return_array = ['status'=>false, 'message'=>''];

        $customer_inactive = Customers::where([['email',$request->input('email')],['account_status','inactive']])->first();
        if($customer_inactive){
            $return_array['message'] = "Customer already addedd and its inactive";

        }else{
                    $rules = [
                    'first_name'    => ['required', 'string', 'min:2', 'max:50'],
                    'middle_name'   => ['nullable', 'string', 'min:2', 'max:50'], 
                    'last_name'     => ['required', 'string', 'min:2', 'max:50'],
                    'email'         => ['required', 'email', 'unique:customers,email'],
                    'phone'         => ['required', 'digits:10', 'unique:customers,phone'], 
                    'address'       => ['required', 'string', 'max:255'],
                    'dob' => ['required', 'date', 'before:today'],
                    'gender'        => ['required'], 
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                    return response()->json(['status'=>false,'errors'=>$validator->errors()],422);
            }else{
                //    dd('checking');
                    $first_name = $request->input('first_name')?? '';
                    $middle_name = $request->input('middle_name')?? '';
                    $last_name = $request->input('middle_name')?? '';
                    $email = $request->has('email')? $request->input('email') : '';
                    $phone = $request->input('phone')?? '';
                    $address = $request->input('address')?? '';
                    $date_of_birth = $request->input('dob')?? '';
                    $gender = $request->input('gender')?? '';

                    $email_exist = Customers::where('email',$email)->exists();
                    if (!$email_exist) {
                    $user_data = new Customers();
                    $user_data->first_name = $first_name;
                    $user_data->middle_name = $middle_name;
                    $user_data->last_name = $last_name;
                    $user_data->email = $email;
                    $user_data->phone = $phone;
                    $user_data->address = $address;
                    $user_data->date_of_birth = $date_of_birth;
                    $user_data->gender = $gender;
                    
                    $is_added = $user_data->save();
                    // $is_added = 1;

                    if($is_added){
                        $return_array['status'] = true;
                        //    return redirect()->back()->with('success', 'Your action was successful!');
                        $return_array['message'] = "Customer Added Successfully";
                        }
                    else{
                        $return_array['message'] = "Failed Try Again";
                    }
                    }else{
                    $return_array['message'] = "Email Already Exist";
                    }
                
            }
        }
        
        return response()->json($return_array);
    }

    

    public function delete_customer_by_id(Request $request){
        $return_array = ['status'=>false, 'message'=>'', 'data' => null];

        $customer_id = $request->input('customer_id');
        if ($customer_id) {
            // $delete_user = Customers::where('id',$customer_id)->first();
            // $inactive = 'inactive';
            //  $delete_user->account_status =$inactive;
            //  $deleted = $delete_user->save();
            $customer = Customers::find($customer_id);
            $deleted = $customer->delete();
            if($deleted){
                $return_array['status'] = true;
                $return_array['message'] = 'Customer deleted successfully';

            }else{
                $return_array['message'] = 'Customer Not Found';

            }
        }else{
            $return_array['message'] = 'Customer Not Found';

        }
        return response()->json($return_array);

    }

   
}
