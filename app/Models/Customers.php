<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone', 
        'address', 'date_of_birth', 'gender', 'account_status'
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class);
    }
}
