<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'total_quantity', 'total_amount', 'order_date', 
        'delivered_date', 'order_status_id', 'is_delivered', 'payment_status', 'payment_method'
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatuses::class, 'order_status_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }
}
