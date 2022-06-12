<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'auth:api'
], function() {
});

Route::post('exchangeWallet','App\Http\Controllers\Wallet\ExchangeController@exchangeInfo');
Route::post('accountInfo','App\Http\Controllers\Wallet\AccountController@accountInfo');
Route::post('verifyInfo','App\Http\Controllers\Wallet\AccountController@verifyInfo');
Route::post('activateState','App\Http\Controllers\Wallet\AccountController@activateState');
Route::post('amountTransfer','App\Http\Controllers\Wallet\AccountController@amountTransfer');
Route::post('exchangeConvert','App\Http\Controllers\Wallet\ExchangeController@exchangeConvert');

Route::post('withdrawConfirm','App\Http\Controllers\Wallet\ExchangeController@withdrawConfirm');
Route::post('exchangeDepositHistory','App\Http\Controllers\Wallet\ExchangeController@depositHistory');
Route::post('exchangeWithdrawHistory','App\Http\Controllers\Wallet\ExchangeController@withdrawHistory');

// Run sql query for crypto_payment table
Route::post('runSql','App\Http\Controllers\PaymentController@run');
// Get latest order id
Route::post('getOrder','App\Http\Controllers\PaymentController@getOrder');
// Create payment request
Route::post('createPayment','App\Http\Controllers\PaymentController@setCrypto');
// Get payment notification
Route::post('getCrypto','App\Http\Controllers\PaymentController@getCrypto');
// Get payment notification result
Route::post('getCryptoResult','App\Http\Controllers\PaymentController@getResult');
// Get payment notification result Mail to admin
Route::post('getCryptoResultMail','App\Http\Controllers\PaymentController@getResultMail');
// Withdraw
Route::post('withdraw','App\Http\Controllers\PaymentController@withdraw');
// Get Limit amount for selected token payment
Route::post('getLimit','App\Http\Controllers\PaymentController@getLimit');
// Make Fiat deposit
Route::post('fiatdeposit','App\Http\Controllers\PaymentController@fiatdeposit');
// Fiat deposit Check
Route::post('fiatdepositcheck','App\Http\Controllers\PaymentController@fiatdepositcheck');
//Deposit Fiat Check
Route::post('depositfiatconfirm','App\Http\Controllers\PaymentController@depositfiatconfirm');

