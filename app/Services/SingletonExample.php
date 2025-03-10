<?php 

namespace App\Services;

class SingletonExample{

    private static ?SingletonExample $instance = null;

    private function __construct(){
        
    }
}