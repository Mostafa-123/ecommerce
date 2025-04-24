<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishlistController extends Controller
{

    public function index(){
        $items=Cart::instance('wishlist')->content();
        return view('website.wishlist',compact('items'));
    }

    public function addTo_removeFrom_wishlist(Request $request){
        Cart::instance('wishlist')->add($request->id,$request->name,$request->quantity,$request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    public function deleteFromWishlist($rowId){
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }

    public function clearWishlist(){
        Cart::instance('wishlist')->destroy();
        return redirect()->back();
    }

    public function move_to_cart($rowId){
        $item=Cart::instance('wishlist')->get($rowId);
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($rowId->id,$rowId->name,$rowId->quantity,$rowId->price)->associate('App\Models\Product');
        return redirect()->back();
    }
}
