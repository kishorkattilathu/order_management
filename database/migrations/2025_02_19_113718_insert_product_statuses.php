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
        DB::table('product_statuses')->insert([

            ['id' => 1, 'title' => 'active'],
            ['id' => 2, 'title' => 'inactive'],
            ['id' => 3, 'title' => 'out of stock'],
            ['id' => 4, 'title' => 'coming soon'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_statuses')->whereIn('title', ['active', 'inactive', 'out of stock', 'coming soon'])->delete();
    }
};
