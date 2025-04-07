<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
    
        if (auth()->check() && auth()->user()->hasRole('superadmin')) {
            print('superadmin');
        }else{
            print('not superadmin');

        }
      
    }

   
}
