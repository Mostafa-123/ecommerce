<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
