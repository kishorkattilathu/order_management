<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'product_quantity', 'product_amount', 
        'product_total_amount', 'discount', 'tax', 'delivered_date', 'order_status_id'
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatuses::class, 'order_status_id');
    }
}
