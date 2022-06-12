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

// register auth routes
Route::prefix('auth')->group(base_path('routes/api/auth.php'));
// register user routes
Route::prefix('user')->group(base_path('routes/api/user.php'));
// register main routes
Route::prefix('main')->group(base_path('routes/api/main.php'));
// register wallet routes
Route::prefix('wallet')->group(base_path('routes/api/wallet.php'));
