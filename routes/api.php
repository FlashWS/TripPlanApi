<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;

Route::post('auth/registration', 'App\Http\Controllers\AuthController@registration')->name('auth.registration');
Route::post('auth/get_code', 'App\Http\Controllers\AuthController@getCode')->name('auth.get_code');
Route::post('auth/get_token', 'App\Http\Controllers\AuthController@getToken')->name('auth.get_token');

Route::group(['middleware' => 'auth:sanctum',], function () {
    Route::get('auth/remove_tokens', 'App\Http\Controllers\AuthController@removeTokens')->name('auth.remove_tokens');
    Route::get('user', 'App\Http\Controllers\UserController@show')->name('user.show');
    Route::post('user', 'App\Http\Controllers\UserController@update')->name('user.update');

    Route::apiResources([
        'trips' => TripController::class,
    ]);
});

