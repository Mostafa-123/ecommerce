<?php


use App\Http\Controllers\productController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'api',
    //'prefix' => 'auth'
], function ($router) {

    Route::group(['namespace'=>'Product'],function (){
        Route::post('/addProduct', [productController::class, 'addProduct']);
        Route::post('/updateProduct/{product_id}', [productController::class, 'updateProduct']);
        Route::get('/getAllProducts', [productController::class, 'getAllProducts']);
        Route::get('/getProduct/{product_id}', [productController::class, 'getProduct']);
        Route::post('/destroyProduct/{product_id}', [productController::class, 'destroyProduct']);
        Route::post('/deleteAllProducts', [productController::class, 'deleteAllProducts']);
    });
});







Route::group([
], function ($router) {
    Route::get('productPhoto/{product_id}/{photo_id}', [productController::class, 'getProductPhoto']);
});


Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
