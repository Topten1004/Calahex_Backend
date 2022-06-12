<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'auth:api'
], function() {
    
    
});

// Create a new pending tokens requested from client and send email into platform admin
Route::post('createToken', 'App\Http\Controllers\TokenController@create');

// Upload image from client to server
Route::get('uploadImage', 'App\Http\Controllers\UploadController@uploadImage');

// Upload image from client to server
Route::get('uploadPdf', 'App\Http\Controllers\UploadController@uploadPdf');

// Get listed tokens on the platform
Route::post('tokenList', 'App\Http\Controllers\TokenController@list');

// Get latest exchange order list on the platform
Route::post('exchangeOrder', 'App\Http\Controllers\ExchangeController@exchangeOrder');

// Get latest trade history list on the platform
Route::post('exchangeTrade', 'App\Http\Controllers\ExchangeController@exchangeTrade');

// Get all crypto currency pairs on the platform
Route::post('exchangeCryptoPair', 'App\Http\Controllers\ExchangeController@exchangeCryptoPair');

// Get all ERC-20 based token pairs on the platform
Route::post('exchangeTokenPair', 'App\Http\Controllers\ExchangeController@exchangeTokenPair');

Route::post('exchangeInfo', 'App\Http\Controllers\ExchangeController@exchangeInfo');
Route::post('exchangeLimitAmount','App\Http\Controllers\ExchangeController@exchangeLimitAmount');
Route::post('exchangeFeeAmount','App\Http\Controllers\ExchangeController@exchangeFeeAmount');
Route::post('buyAmount','App\Http\Controllers\ExchangeController@buyAmount');
Route::post('addOrder','App\Http\Controllers\ExchangeController@addOrder');

Route::post('exchangeOrderCancel','App\Http\Controllers\ExchangeController@userOrderCancel');
Route::post('exchangeOrderClear','App\Http\Controllers\ExchangeController@userOrderClear');
Route::post('userOrderList','App\Http\Controllers\ExchangeController@userOrderList');
Route::post('userTradeList','App\Http\Controllers\ExchangeController@userTradeList');
// Get latest futures order list on the platform
Route::post('futuresOrder', 'App\Http\Controllers\FuturesController@futuresOrder');

// Get latest futures trade list on the platform
Route::post('futuresTrade', 'App\Http\Controllers\FuturesController@futuresTrade');

// Get latest futures collateralized list on the platform
Route::post('futuresList', 'App\Http\Controllers\FuturesController@futuresList');

// Get 30-days total volume
Route::post('order30balance', 'App\Http\Controllers\ExchangeController@order30balance');




// Get ongoing news list
Route::post('news', 'App\Http\Controllers\SupportController@news');

// Get ongoing notification list
Route::post('notification', 'App\Http\Controllers\SupportController@notification');

// Execute open orders automatically
Route::post('executeOpenOrder', 'App\Http\Controllers\ExchangeController@executeOpenOrder');