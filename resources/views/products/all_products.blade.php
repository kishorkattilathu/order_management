<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
        <div style="display: flex; justify-content: flex-end;">
            <select name="categories" style="width: 150px" id="categories" class="form-control">
                <option value="all">All Selected</option>
                @foreach ($categories as $category){
                    <option value="{{$category->id}}">{{$category->name}}</option>
                }
                @endforeach
            </select>
            <select name="product_status" style="width: 150px" id="product_status" class="form-control">
                    <option value="all">All Selected</option>
                @foreach ($products_statuses as $product_status){
                    <option value="{{$product_status->id}}">{{$product_status->title}}</option>
                }
                @endforeach
            </select>
            <button id="open_add_product_modal" class="btn btn-success">Add Products+</button>
        </div>
        
    </x-slot>

    <div class="py-12 container">
        <table id="products_datatable" class="table mb-0">
            <thead class="text-gray fs-12">
                <tr>
                    <th class="pl-0">Id</th>
                    <th data-breakpoints="md">Name</th>
                    <th data-breakpoints="md">Image</th>
                    <th data-breakpoints="md">Category</th>
                    <th data-breakpoints="md">Stock</th>
                    <th data-breakpoints="md">Sold</th>
                    <th data-breakpoints="md">Price</th>
                    <th data-breakpoints="md">Status</th>
                    <th data-breakpoints="md" style="width:190px;">Action</th>
                </tr>
            </thead>
            <tbody class="fs-14">
            </tbody>
        </table>
    </div>
<script src="{{asset('js/product.js')}}"></script>
@include('modal.add_product_modal');

</x-app-layout>
