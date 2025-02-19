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
        DB::table('categories')->insert([
            ['name' => 'Electronics'],
            ['name' => 'Clothing'],
            ['name' => 'Home & Kitchen'],
            ['name' => 'Beauty & Personal Care'],
            ['name' => 'Sports & Outdoors'],
            ['name' => 'Books'],
            ['name' => 'Toys & Games'],
            ['name' => 'Automotive'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->whereIn('name', [
            'Electronics', 'Clothing', 'Home & Kitchen', 'Beauty & Personal Care',
            'Sports & Outdoors', 'Books', 'Toys & Games', 'Automotive'
        ])->delete();
    }
};
