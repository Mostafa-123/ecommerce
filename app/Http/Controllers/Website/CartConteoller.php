<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartConteoller extends Controller
{
    public function index(){
        $items=Cart::instance('cart')->content();
        return view('website.cart',compact('items'));
    }

    public function add_to_cart(Request $request){
        Cart::instance('cart')->add($request->id,$request->name,$request->quantity,$request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_item_quantity($rowId){
        $item=Cart::instance('cart')->get($rowId);
        $qty=$item->qty+1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

    public function decrease_item_quantity($rowId){
        $item=Cart::instance('cart')->get($rowId);
        $qty=$item->qty-1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

    public function remove_item($rowId){
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function clear_cart(){
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }
}
