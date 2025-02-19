<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Orders') }}
        </h2>
        <div style="display: flex; justify-content: flex-end;">
            <a class="btn btn-success" href="{{url('/create_orders')}}">Add</a>
        </div>
        
    </x-slot>

    <div class="py-12 container">
        
    </div>

</x-app-layout>
