<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('send', 'App\Http\Controllers\AuthController@send');
Route::post('change', 'App\Http\Controllers\AuthController@change');
Route::post('signup', 'App\Http\Controllers\AuthController@signup');
Route::post('confirm', 'App\Http\Controllers\AuthController@confirm');
Route::post('profile', 'App\Http\Controllers\AuthController@profile');
Route::post('phone', 'App\Http\Controllers\AuthController@phone');
Route::post('two', 'App\Http\Controllers\AuthController@two');
Route::post('reconfirm', 'App\Http\Controllers\AuthController@reconfirm');

Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('logout', 'App\Http\Controllers\AuthController@logout');
});
