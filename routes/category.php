<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\productController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'api',
    //'prefix' => 'auth'
], function ($router) {

    Route::group(['namespace'=>'Product'],function (){
        Route::post('/addCategory', [CategoryController::class, 'addCategory']);
        Route::post('/updateCategory/{category_id}', [CategoryController::class, 'updateCategory']);
        Route::get('/getAllCategories', [CategoryController::class, 'getAllCategories']);
        Route::get('/getCategory/{category_id}', [CategoryController::class, 'getCategory']);
        Route::post('/destroyCategory/{category_id}', [CategoryController::class, 'destroyCategory']);
        Route::post('/deleteAllCategories', [CategoryController::class, 'deleteAllCategories']);
        Route::get('/getAllProductsInCategory/{category_id}', [CategoryController::class, 'getAllProductsInCategory']);

    });
});








Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
