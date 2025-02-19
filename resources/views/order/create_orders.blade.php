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
        <div class="col-md-12 d-grid justify-content-center mt-3" >
            <button type="button" class="btn btn-primary" id="create_order_btn">Create+</button>
        </div>
    </div>
</form>

    
    <script src="{{asset('js/create_order.js')}}"></script>

</x-app-layout>
