<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function() {
    Route::middleware('web')->get('sanctum/csrf-cookie', function(){
        return response()->json([], 204);
    });
    // middleware('api_csrf_verify')
    Route::post('login', 'Auth\AuthController@login');
    Route::post('register', 'Auth\AuthController@register');
    // Route::middleware('auth:sanctum')->post('logout', 'Auth\AuthController@logout');
    // Route::post('anonymous', 'Auth\AuthController@GetAnonymousToken');
    Route::get('email/verify/{id}/{hash}', 'Auth\AuthController@EmailVerify')->name('api.verification.verify');
    Route::post('forgot-password', 'Auth\AuthController@ForgotPassword')->name('api.password.email');
    Route::post('reset-password', 'Auth\AuthController@ResetPassword')->name('api.password.reset');
    Route::middleware('auth:sanctum')->post('update-password', 'Auth\AuthController@UpdatePassword')->name('api.password.update');
    Route::middleware('auth:sanctum')->get('user', function(Request $request) {
        return $request->user();
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
