<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use SoftDeletes;

    use HasFactory;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone', 
        'address', 'date_of_birth', 'gender', 'account_status','deleted_at'
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class);
    }
}
