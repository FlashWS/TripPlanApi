<?php

use App\Http\Controllers\PointController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::post('auth/registration', 'App\Http\Controllers\AuthController@registration')->name('auth.registration');
Route::post('auth/get_code', 'App\Http\Controllers\AuthController@getCode')->name('auth.get_code');
Route::post('auth/get_token', 'App\Http\Controllers\AuthController@getToken')->name('auth.get_token');

Route::group(['middleware' => 'auth:sanctum',], function () {
    Route::get('auth/remove_tokens', 'App\Http\Controllers\AuthController@removeTokens')->name('auth.remove_tokens');
    Route::get('user', 'App\Http\Controllers\UserController@show')->name('user.show');
    Route::post('user', 'App\Http\Controllers\UserController@update')->name('user.update');

    Route::get('trips/{trip}/points', 'App\Http\Controllers\TripPointController@index')->name('trips.points.index');
    Route::post('trips/{trip}/points', 'App\Http\Controllers\TripPointController@store')->name('trips.points.store');
    Route::get('trip_points/{trip_point}', 'App\Http\Controllers\TripPointController@show')->name('trip_points.show');
    Route::patch('trip_points/{trip_point}', 'App\Http\Controllers\TripPointController@update')->name('trip_points.update');
    Route::delete('trip_points/{trip_point}', 'App\Http\Controllers\TripPointController@destroy')->name('trip_points.destroy');

    Route::apiResources([
        'points' => PointController::class,
        'trips' => TripController::class,
        'tags' => TagController::class,
    ]);
});

