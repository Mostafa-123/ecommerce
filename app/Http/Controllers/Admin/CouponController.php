<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CouponController extends Controller
{
    public function index(){
        $coupons=Coupon::orderBy('expiry_date','DESC')->paginate(10);
        return view('admin.coupons.coupons',compact('coupons'));
    }

    public function create(){
        return view('admin.coupons.add_edit');
    }

    public function store(Request $request){
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->created_by = Auth::user()->id;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon Has Been Added Successfully');
    }

    public function edit(Coupon $coupon){
        return view('admin.coupons.add_edit',compact('coupon'));
    }

    public function update(Request $request,Coupon $coupon){
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->created_by = Auth::user()->id;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon Has Been Updated Successfully');
    }

    public function destroy(Coupon $coupon){

        $coupon->deleted_by=Auth::user()->id;
        $coupon->save();
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon Has Been Deleted Successfully');

    }

    public function apply_coupon(Request $request){
        $coupon_code=$request->coupon_code;
        $subtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
        //dd($subtotal);
        if(isset($coupon_code)){
            $coupon=Coupon::where('code',$coupon_code)->where('expiry_date','>=',Carbon::today())->first();
            if(!$coupon){
            return redirect()->back()->with('error','invalid coupon code');
            }else if($coupon->cart_value > $subtotal){
            return redirect()->back()->with('error','minimum value for the products value must be at least '.$coupon->cart_value.'$');
            }else{
                Session::put('coupon',[
                    'code'=>$coupon->code,
                    'type'=>$coupon->type,
                    'value'=>$coupon->value,
                    'cart_value'=>$coupon->cart_value,
                ]);
                $this->calculateDiscount();
            return redirect()->back()->with('success','coupon has been applied');
            }
        }else{
            return redirect()->back()->with('error','invalid coupon code');
        }
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

    public function remove_coupon(){
        Session::forget(['coupon','discounts']);
        return redirect()->back()->with('success','coupon has been removed');
    }

}
