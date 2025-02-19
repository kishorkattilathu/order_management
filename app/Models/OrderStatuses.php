<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatuses extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function orders()
    {
        return $this->hasMany(Orders::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
