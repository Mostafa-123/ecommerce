<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Website\CartConteoller;
use App\Http\Controllers\Website\CheckoutController;
use App\Http\Controllers\Website\shopController;
use App\Http\Controllers\Website\WishlistController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [shopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [shopController::class, 'product_details'])->name('shop.product.details');

Route::put('cart/increase-quantity/{rowId}',[CartConteoller::class,'increase_item_quantity'])->name('cart.qty.increase');
Route::put('cart/decrease-quantity/{rowId}',[CartConteoller::class,'decrease_item_quantity'])->name('cart.qty.decrease');
Route::delete('cart/remove/{rowId}',[CartConteoller::class,'remove_item'])->name('cart.item.remove');
Route::delete('cart/clear',[CartConteoller::class,'clear_cart'])->name('cart.clear');


Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('wishlist/add',[WishlistController::class,'addTo_removeFrom_wishlist'])->name('wishlist.add');
Route::delete('wishlist/remove/{rowId}',[WishlistController::class,'deleteFromWishlist'])->name('wishlist.item.remove');
Route::delete('wishlist/clear',[WishlistController::class,'clearWishlist'])->name('wishlist.clear');
Route::post('wishlist/move_to_cart/{rowId}',[WishlistController::class,'move_to_cart'])->name('wishlist.move_to_cart');


Route::get('/cart',[CartConteoller::class,'index'])->name('cart.index');
Route::post('cart/add',[CartConteoller::class,'add_to_cart'])->name('cart.add');

Route::post('cart/apply-coupon',[CouponController::class,'apply_coupon'])->name('admin.coupons.apply');
Route::delete('cart/remove-coupon',[CouponController::class,'remove_coupon'])->name('admin.coupons.remove');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/checkout',[CheckoutController::class,'checkout'])->name('cart.checkout');
    Route::post('/order/make',[CheckoutController::class,'make_order'])->name('order.make');
    Route::get('/order/confirm',[CheckoutController::class,'confirm_order'])->name('order.confirm');


});

Route::middleware(['auth',RoleMiddleware::class])->group(function(){
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

    Route::resource('admin/brands', BrandController::class)->names([
        'index' => 'admin.brands',
        'create' => 'admin.brands.create',
        'store' => 'admin.brands.store',
        'edit' => 'admin.brands.edit',
        'update' => 'admin.brands.update',
        'destroy' => 'admin.brands.delete',
    ]);


    Route::resource('admin/categories', CategoryController::class)->names([
        'index' => 'admin.categories',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.delete',
    ]);

    Route::resource('admin/products', ProductController::class)->names([
        'index' => 'admin.products',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.delete',
    ]);



    Route::resource('admin/coupons', CouponController::class)->names([
        'index' => 'admin.coupons',
        'create' => 'admin.coupons.create',
        'store' => 'admin.coupons.store',
        'edit' => 'admin.coupons.edit',
        'update' => 'admin.coupons.update',
        'destroy' => 'admin.coupons.delete',
    ]);




});
