<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::post('profile', 'App\Http\Controllers\UserController@profile');
});