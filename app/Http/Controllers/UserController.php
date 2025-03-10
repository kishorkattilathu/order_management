<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
    //         @if(auth()->check() && auth()->user()->hasRole('admin'))
    //     echo"admin";// <a href="{{ url('/admin') }}">Admin Dashboard</a>
    // @endif

    // @if(auth()->check() && auth()->user()->hasRole('superadmin'))
    //     echo"superadmin";// <a href="{{ url('/technician') }}">Technician Dashboard</a>
    // @endif

    // @if(auth()->check() && auth()->user()->hasRole('customer'))
    //     echo"customer";// <a href="{{ url('/customer') }}">Customer Dashboard</a>
    // @endif
   
        // if (auth()->check() && auth()->user()->hasRole('admin')) {
        //     print('admin');
        // }
        if (auth()->check() && auth()->user()->hasRole('superadmin')) {
            print('superadmin');
        }else{
            print('not superadmin');

        }
        // if (auth()->check() && auth()->user()->hasRole('customer')) {
        //     print('customer');
        // }
    }

   
}
