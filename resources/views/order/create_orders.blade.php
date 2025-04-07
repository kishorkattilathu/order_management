<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Orders') }}
        </h2>
        <style>
            #result {
                position: absolute;
                background: white;
                border: 1px solid #ddd;
                max-width: 300px;
            }
            .result-item {
                padding: 8px;
                cursor: pointer;
            }
            .result-item:hover {
                background: #f1f1f1;
            }
        </style>
        
    </x-slot>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h4>Product Details</h4>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="products_datatable" class="table table-striped table-bordered table-hover table-sm  mb-0">
                                <thead class="text-gray fs-12">
                                    <tr>
                                        <th class="pl-0">Id</th>
                                        <th data-breakpoints="md">Name</th>
                                        <th data-breakpoints="md">Image</th>
                                        <th data-breakpoints="md">Category</th>
                                        <th data-breakpoints="md">Stock</th>
                                        {{-- <th data-breakpoints="md">Sold</th> --}}
                                        <th data-breakpoints="md">Price</th>
                                        {{-- <th data-breakpoints="md">Status</th> --}}
                                        <th data-breakpoints="md" style="width:190px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-14">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <form id="create_order_form">
                            <div class="py-4">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        {{-- <select name="customer_id" id="customer_id" class="form-select">
                                            <option value="">Select Customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{$customer->id}}">{{$customer->first_name}}</option>
                                            @endforeach
                                        </select> --}}
                                        <select name="customer_id" id="customer_id" class="form-select">
                                            <option value="">Select Customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{$customer->id}}"
                                                    {{session('customer_id') == $customer->id ? 'selected' : ''}}>
                                                    {{$customer->first_name}}</option>
                                            @endforeach
                                        </select>
                                        
                                        <span class="text-danger" id="error-customer_id"></span>
                                    </div>
                                    <div class="col-md-6">
                                        {{-- <input class="form-control" type="text" id="search" placeholder="Search products..." autocomplete="off">
                                        <div id="result"></div> --}}
                                        {{-- <select name="product_id" id="product_id" class="form-select">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{$product->id}}">{{$product->product_name}}</option>
                                            @endforeach
                                        </select> --}}
                                        {{-- <span class="text-danger" id="error-product_ids"></span> --}}
                                        {{-- <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="pre_order_checkbox" name="pre_order"{{ session('pre_orders') == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pre_order_checkbox">Pre Orders</label>
                                        </div> --}}
                                        {{-- <button class="btn btn-primary" id="pre_order_button" type="button">Pre Orders</button> --}}
                                    </div>
                                    {{-- <div class="col-md-4 d-grid">
                                        <button type="button" class="btn btn-primary" id="add_order_btn">Add Product+</button>
                                    </div> --}}
                                </div>
                            
                            </div>
                            <div class="py-4">
                                <div class="row g-3 align-items-end">
                                    <div id="order_list">
                                        <div class="row text-align-center" >
                                            <div class="col-md-3">
                                                <h6 for="">Product Name</h6>
                                            </div>
                                            <div class="col-md-2">
                                                <h6 for="">In Stock</h6>
                                            </div>
                                            <div class="col-md-2">
                                                <h6 for="">Quantity</h6>
                                            </div>
                                            <div class="col-md-3">
                                                <h6 for="">Total Price</h6>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <h6 for="">Action</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                                @php $total=0;@endphp
                                @foreach ($order_items as $key => $product)
                                    {{-- @dd($product_in_session) --}}
                                    <div class="row g-3 align-items-end order-item">
                                        <div class="col-md-3">
                                            <input type="hidden" name="products[][product_id]" class="product-id" value="{{$product['product_id']}}">
                                            <input id="product_price-{{$product['product_id']}}" type="hidden" name="product_price[]" value="{{$product['price']}}">
                                            <p class="">{{$product['name']}}</p>
                                        </div>
                                        <div class="col-md-2">
                                            {{-- <p id="product_stock-${product_id}" name="product_stocks[]"  class=" product_stock">{{$product['stock_quantity']}} </p> --}}
                                            <input id="product_stock-${product_id}" type="text" readonly name="product_stocks[]" value="{{$product['stock_quantity']}}" class="form-control product_stock">
                                            {{-- <div id="stock_quantity-{{$product['product_id']}}"> {{$product['stock_quantity']}} </div> --}}
                                        </div>
                                        <div class="col-md-2">
                                            <input  type="number" onChange="productQtyChange({{$product['product_id']}})" name="product_qty[]" data-product_id="{{$product['product_id']}}" id="product_qty-{{$product['product_id']}}" value="{{$product['qty']}}" min="1" class="form-control product_qty">
                                        </div>
                                       
                                        <div class="col-md-3">
                                            <div id="product_amount-{{$product['product_id']}}">&#8377; {{$product['total_price']}} </div>
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <i class="bi bi-trash remove-product text-danger"></i>
                                        </div>
                                    </div>
                                    @php $total+=$product['total_price'];@endphp
                                @endforeach
                                    <div class="col-md-12 mt-2">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">Tax</label>
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                           
                                            <div  class="col-md-3">
                                                <div>&#8377; 00.00</div>
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-12 mt-2" >
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">Discount </label>
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                           
                                            <div  class="col-md-3">
                                                <div>&#8377; 00.00</div>

                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-12 mt-2" id="grand_total_div">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="">Grand Total</label>
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                           
                                            <div  class="col-md-3">
                                                {{-- <div id="grand_total">&#8377; {{$total}}</div> --}}
                                                <div id="grand_total">&#8377; {{isset($grand_total_price) ? number_format($grand_total_price,2,'.','') : 00.00}}</div>
                                            </div>
                                            <div class="col-md-2">
                                            </div>
                                        </div>
                                        
                                    </div>
                    
                                {{--  --}}
                                <div class="col-md-12 d-grid justify-content-center mt-3" >
                                    <button type="button" class="btn btn-primary" id="create_order_btn">Create Orders+</button>
                                </div>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{asset('js/create_order.js')}}"></script>
    @include('modal.product_modal');

</x-app-layout>






