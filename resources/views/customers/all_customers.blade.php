<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customers') }}
        </h2>
       
        <div style="display: flex; justify-content: flex-end; gap: 10px; align-items: center;">
            <select name="customers_selection_by_status" style="width: 100px" id="customers_selection_by_status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button id="open_customer_modal" class="btn btn-success">Add+</button>
        </div>
        
    </x-slot>

    <div class="py-12 container">
        <table id="all_customers_table" class="table mb-0">
            <thead class="text-gray fs-12">
                <tr>
                    <th class="pl-0">Id</th>
                    <th data-breakpoints="md">First Name</th>
                    <th data-breakpoints="md">Email</th>
                    <th data-breakpoints="md">Phone</th>
                    {{-- <th data-breakpoints="md">Address</th> --}}
                    <th data-breakpoints="md">DOB</th>
                    <th data-breakpoints="md">Gender</th>
                    <th data-breakpoints="md">Status</th>
                    <th data-breakpoints="md" style="width:190px;">Action</th>
                </tr>
            </thead>
            <tbody class="fs-14">
            </tbody>
        </table>
    </div>
<script src="{{asset('js/customer.js')}}"></script>
@include('modal.add_customer_modal');
@include('modal.edit_customer_modal');
</x-app-layout>
