<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/customers', [CustomerController::class, 'customers'])->name('customers');
    Route::post('/add_customers', [CustomerController::class, 'add_customers'])->name('add_customers');
    Route::post('/get_all_customers', [CustomerController::class, 'get_all_customers'])->name('get_all_customers');
    Route::post('/get_customer_data_by_id', [CustomerController::class, 'get_customer_data_by_id'])->name('get_customer_data_by_id');
    Route::post('/update_customer_by_id', [CustomerController::class, 'update_customer_by_id'])->name('update_customer_by_id');
    Route::post('/delete_customer_by_id', [CustomerController::class, 'delete_customer_by_id'])->name('delete_customer_by_id');


    Route::get('/products', [ProductController::class, 'products'])->name('products');
    Route::post('/add_product', [ProductController::class, 'add_product'])->name('add_product');
    Route::post('/get_product_detail', [ProductController::class, 'get_product_detail'])->name('get_product_detail');
    
    
    
    Route::get('/create_orders', [OrderController::class, 'create_orders'])->name('create_orders');
    Route::post('/create_final_order', [OrderController::class, 'create_final_order'])->name('create_final_order');
    Route::get('/all_orders', [OrderController::class, 'all_orders'])->name('all_orders');

});

require __DIR__.'/auth.php';
