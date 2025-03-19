<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Orders') }}
        </h2>
        <div style="display: flex; justify-content: flex-end;">
            <a class="btn btn-success" href="{{url('/create_orders')}}">Create New Order</a>
        </div>
        
    </x-slot>

    <div class="py-12 container">
        <table id="orders_datatable" class="table mb-0">
            <thead class="text-gray fs-12">
                <tr>
                    <th class="pl-0">Id</th>
                    {{-- <th class="pl-0">Type</th> --}}
                    <th data-breakpoints="md">Name</th>
                    <th data-breakpoints="md">Email</th>
                    <th data-breakpoints="md">Quantity</th>
                    <th data-breakpoints="md">Amount</th>
                    <th data-breakpoints="md">Order Date</th>
                    <th data-breakpoints="md">Order Time</th>
                    <th data-breakpoints="md">Status</th>
                    <th data-breakpoints="md" style="width:190px;">Action</th>
                </tr>
            </thead>
            <tbody class="fs-14">
            </tbody>
        </table>
    </div>

    <script src="{{asset('js/create_order.js')}}"></script>
    @include('modal.order_modal');
</x-app-layout>
