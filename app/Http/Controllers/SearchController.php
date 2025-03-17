<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = $request->input('query');

        $results = Products::where([['product_name', 'LIKE', "%{$query}%"],['product_status_id',1]]) 
            ->take(10) 
            ->get();
        // dd($results);
        return response()->json($results);
    }
}
