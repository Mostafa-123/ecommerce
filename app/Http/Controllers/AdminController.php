<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        return view('Admin.index');
    }

    public function search(Request $request){
        $query=$request->input('query');
        $results=Product::where('name','LIKE',"%{$query}%")->get()->take(8);
        return response()->json($results);
    }
}
