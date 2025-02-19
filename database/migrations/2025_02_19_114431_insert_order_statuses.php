<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('order_statuses')->insert([
            ['id' => 1, 'title' => 'Pending'],
            ['id' => 2, 'title' => 'Confirmed'],
            ['id' => 3, 'title' => 'Processing'],
            ['id' => 4, 'title' => 'Shipped'],
            ['id' => 5, 'title' => 'Delivered'],
            ['id' => 6, 'title' => 'Cancelled'],
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('order_statuses')->whereIn('title', ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'])->delete();
    }
};
