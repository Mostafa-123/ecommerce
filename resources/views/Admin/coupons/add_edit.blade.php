@extends('admin.layouts.app')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Coupon infomation</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{route('admin.index')}}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{route('admin.coupons')}}">
                            <div class="text-tiny">Coupons</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">{{isset($coupon) ? "Edit Coupon" : "New Coupon"}}</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <form class="form-new-product form-style-1" method="POST" action="{{isset($coupon) ?  route('admin.coupons.update',['coupon'=>$coupon]) :route('admin.coupons.store') }}">
                    @csrf
                    @if (isset($coupon))
                        @method('PUT')
                    @endif
                    <fieldset class="name">
                        <div class="body-title">Coupon Code <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Coupon Code" name="code"
                            tabindex="0" value="{{old('code',isset($coupon) ? $coupon->code : '') }}" aria-required="true" required>
                    </fieldset>
                    @error('code')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="category">
                        <div class="body-title">Coupon Type</div>
                        <div class="select flex-grow">
                            <select class="" name="type">
                                <option value="">Select</option>
                                <option @if (isset($coupon) && $coupon->type=="fixed")
                                    selected
                                @endif value="fixed">Fixed</option>
                                <option @if (isset($coupon) && $coupon->type=="percent")
                                    selected
                                @endif  value="percent">Percent</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('type')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Value <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="number" placeholder="Coupon Value" name="value"
                            tabindex="0" value="{{old('value',isset($coupon) ? $coupon->value : '') }}" aria-required="true" required="">
                    </fieldset>
                    @error('value')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Cart Value <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="number" placeholder="Cart Value"
                            name="cart_value" tabindex="0" value="{{old('cart_value',isset($coupon) ? $coupon->cart_value : '') }}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('cart_value')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="name">
                        <div class="body-title">Expiry Date <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="date" placeholder="Expiry Date"
                            name="expiry_date" tabindex="0" value="{{old('expiry_date',isset($coupon) ? $coupon->expiry_date : '') }}" aria-required="true"
                            required="">
                    </fieldset>
                    @error('expiry_date')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror

                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
