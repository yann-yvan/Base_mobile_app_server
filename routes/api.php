<?php

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

Route::get('test', function () {
    return response()->json([
        'status' => true,
        'message' => 'online',
    ]);
});

/**********************************************************************************************
 **********                        AUTH      ROUTE                                        *****
 **********************************************************************************************/
Route::POST('login', "Mobile\Auth\LoginController@login");
Route::POST('register', "Mobile\Auth\RegisterController@register");

Route::GET('confirmation/link/{token}', 'Mobile\Auth\VerificationController@activateByToken');
Route::POST('confirmation/code', 'Mobile\Auth\VerificationController@activateByCode');
Route::GET('verification/index', 'Mobile\Auth\VerificationController@index');
Route::POST('verification/resend', 'Mobile\Auth\VerificationController@resendCodeWeb');

Route::POST('password/forgot', 'Mobile\Auth\ForgetPasswordController@sendResetCode');
Route::GET('password/reset/link/{token}', 'Mobile\Auth\ResetPasswordController@index');
Route::POST('password/reset', 'Mobile\Auth\ResetPasswordController@store');
Route::POST('password/mobile/reset', 'Mobile\Auth\ResetPasswordController@resetByCode');

/**********************************************************************************************
 **********                        AUTHENTICATED      ROUTE                               *****
 **********************************************************************************************/

Route::middleware(\App\Http\Middleware\VerifyJWTToken::class)->group(function () {
    Route::GET('greet', "Mobile\AuthTestController@greet");
});