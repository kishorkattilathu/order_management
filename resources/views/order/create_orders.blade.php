<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Orders') }}
        </h2>
        
        
    </x-slot>

    <form id="create_order_form">
    <div class="container py-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <select name="customer_id" id="customer_id" class="form-select">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{$customer->id}}">{{$customer->email}}</option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="error-customer_id"></span>
                </div>
                <div class="col-md-4">
                    <select name="product_id" id="product_id" class="form-select">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{$product->id}}">{{$product->product_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-grid">
                    <button type="button" class="btn btn-primary" id="add_order_btn">Add+</button>
                </div>
            </div>
        
    </div>
    <div class="container py-4">
        <h2>Creating Order</h2>
        <div class="row g-3 align-items-end">
            <div id="order_list">
                <div class="row text-align-center" >
                    <div class="col-md-3">
                        <h4 for="">Product Name</h4>
                    </div>
                    <div class="col-md-3">
                        <h4 for="">Product Quantity</h4>
                    </div>
                    <div class="col-md-3">
                        <h4 for="">Total Price</h4>
                    </div>
                    <div class="col-md-3">
                        <h4 for="">Action</h4>
                    </div>
                </div>
            </div>
            
           
        </div>

        {{--  --}}
        @foreach ($product_in_session as $key => $product)
            {{-- @dd($product) --}}
            <div class="row g-3 align-items-end order-item">
                <div class="col-md-3">
                    <input type="hidden" name="product_ids[]" class="product-id" value="{{$product['product_id']}}">
                    <input id="product_price-{{$product['product_id']}}" type="hidden" name="product_price[]" value="{{$product['price']}}">
                    <p class="">{{$product['name']}}</p>
                </div>
                <div class="col-md-3">
                    <input  type="number" onChange="productQtyChange({{$product['product_id']}})" name="product_qty[]" data-product_id="{{$product['product_id']}}" id="product_qty-{{$product['product_id']}}" value="{{$product['qty']}}" min="1" class="form-control product_qty">
                </div>
                <div class="col-md-3">
                    <div id="product_amount-{{$product['product_id']}}">{{$product['total_price']}} </div>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="button" class="btn btn-danger remove-product">Remove</button>
                </div>
            </div>
        @endforeach
        
        {{--  --}}
        <div class="col-md-12 d-grid justify-content-center mt-3" >
            <button type="button" class="btn btn-primary" id="create_order_btn">Create+</button>
        </div>
    </div>
</form>

    
    <script src="{{asset('js/create_order.js')}}"></script>

</x-app-layout>
