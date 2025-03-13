<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'product_name', 'description', 'total_quantity', 'sold_quantity', 
        'price', 'product_status_id', 'category_id', 'image_url', 'created_by'
    ];

    protected $dates = ['deleted_at'];

    public function status()
    {
        return $this->belongsTo(ProductStatuses::class, 'product_status_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
