<?php


use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::group(['namespace'=>'Admin'],function (){
        Route::post('/login/admin', [AdminController::class, 'loginAdmin']);
        Route::post('logouts/admin', [AdminController::class, 'logoutAdmin']);
        Route::get('/admin-profile', [AdminController::class, 'adminProfile']);
        Route::post('/addAdmin', [AdminController::class, 'addAdmin']);
        Route::post('/deleteAdmin', [AdminController::class, 'deleteAdmin']);
        Route::post('/addUser', [AdminController::class, 'addUser']);
        Route::post('/deleteUser', [AdminController::class, 'deleteUser']);
        Route::get('/getUserCount', [AdminController::class, 'getUserCount']);
        Route::get('getAdminCount', [AdminController::class, 'getAdminCount']);
        Route::get('/getAllUsers', [AdminController::class, 'getAllUsers']);
        Route::get('/getAllAdmins', [AdminController::class, 'getAllAdmins']);
        Route::post('/updateAdmin', [AdminController::class, 'updateAdmin']);


    });

});
Route::group([
], function ($router) {
    Route::get('adminPhoto/{admin_id}', [AdminController::class, 'getAdminPhoto']);
});
Route::any('{url}',function (){
    return response()->json('this url not found', 401);
})->where('url','.*')->middleware('api');
