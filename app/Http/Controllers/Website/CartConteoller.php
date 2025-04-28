<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartConteoller extends Controller
{
    public function index(){
        $items=Cart::instance('cart')->content();
        return view('website.cart',compact('items'));
    }

    public function add_to_cart(Request $request){
        Cart::instance('cart')->add($request->id,$request->name,$request->quantity,$request->price)->associate('App\Models\Product');
        $this->calculateDiscount();
        return redirect()->back();
    }

    public function increase_item_quantity($rowId){
        $item=Cart::instance('cart')->get($rowId);
        $qty=$item->qty+1;
        Cart::instance('cart')->update($rowId,$qty);
        $this->calculateDiscount();
        return redirect()->back();
    }

    public function decrease_item_quantity($rowId){
        $item=Cart::instance('cart')->get($rowId);
        $qty=$item->qty-1;
        Cart::instance('cart')->update($rowId,$qty);
        $this->calculateDiscount();
        return redirect()->back();
    }

    public function remove_item($rowId){
        Cart::instance('cart')->remove($rowId);
        $this->calculateDiscount();
        return redirect()->back();
    }

    public function clear_cart(){
        Cart::instance('cart')->destroy();
        Session::forget(['coupon','discounts']);
        return redirect()->back();
    }


    public function calculateDiscount(){
        $discount=0;
        if(Session::has('coupon')){
            if(Session::get('coupon')['type']=='fixed'){
                $discount=Session::get('coupon')['value'];
            }else{
                $discount=(Cart::instance('cart')->subtotal() * Session::get('coupon')['value'])/100;
            }
            $subtotalAfterDiscount=floatval(str_replace(',', '', Cart::instance('cart')->subtotal())) - $discount;
            $taxAfterDiscount=($subtotalAfterDiscount * config('cart.tax'))/100;
            $totalAfterDiscount=$subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts',[
                'discount' => number_format(floatval($discount),2,'.',''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount),2,'.',''),
                'tax' => number_format(floatval($taxAfterDiscount),2,'.',''),
                'total' => number_format(floatval($totalAfterDiscount),2,'.','')
            ]);
        }
    }
}
