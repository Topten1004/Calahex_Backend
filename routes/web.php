<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['get.menu']], function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('admin');

    Route::group(['middleware' => ['role:user']], function () {
        Route::get('/404', function () {        return view('dashboard.404'); });
        Route::get('/500', function () {        return view('dashboard.500'); });
    });
    Auth::routes();

    Route::resource('resource/{table}/resource', 'ResourceController')->names([
        'index'     => 'resource.index',
        'create'    => 'resource.create',
        'store'     => 'resource.store',
        'show'      => 'resource.show',
        'edit'      => 'resource.edit',
        'update'    => 'resource.update',
        'destroy'   => 'resource.destroy'
    ]);

    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('news', 'App\Http\Controllers\admin\NewsController');
        Route::resource('news_titles', 'App\Http\Controllers\admin\NewsTitlesController');
        Route::resource('notifications', 'App\Http\Controllers\admin\NotificationsController');
        Route::resource('tokens', 'App\Http\Controllers\admin\TokensController');
        Route::get('/tokens/whitepaper/{id}', 'App\Http\Controllers\admin\TokensController@whitepaper')->name('tokens.whitepaper');
        Route::put('/tokens/{id}/approve', 'App\Http\Controllers\admin\TokensController@approve')->name('tokens.approve');
        Route::put('/tokens/{id}/block', 'App\Http\Controllers\admin\TokensController@block')->name('tokens.block');
        Route::resource('users',        'App\Http\Controllers\admin\UsersController');
        Route::post('users/search',        'App\Http\Controllers\admin\UsersController@search')->name('users.search');
        Route::post('users/manage',        'App\Http\Controllers\admin\UsersController@manage')->name('users.manage');
        Route::post('/users/setBalance', 'App\Http\Controllers\admin\UsersController@setBalance')->name('users.setBalance');
        Route::put('/users/block/{id}', 'App\Http\Controllers\admin\UsersController@block')->name('users.block');
        Route::put('/users/unblock/{id}', 'App\Http\Controllers\admin\UsersController@unblock')->name('users.unblock');
        Route::prefix('menu/element')->group(function () {
            Route::get('/',             'App\Http\Controllers\admin\MenuElementController@index')->name('menu.index');
            Route::get('/move-up',      'App\Http\Controllers\admin\MenuElementController@moveUp')->name('menu.up');
            Route::get('/move-down',    'App\Http\Controllers\admin\MenuElementController@moveDown')->name('menu.down');
            Route::get('/create',       'App\Http\Controllers\admin\MenuElementController@create')->name('menu.create');
            Route::post('/store',       'App\Http\Controllers\admin\MenuElementController@store')->name('menu.store');
            Route::get('/get-parents',  'App\Http\Controllers\admin\MenuElementController@getParents');
            Route::get('/edit',         'App\Http\Controllers\admin\MenuElementController@edit')->name('menu.edit');
            Route::post('/update',      'App\Http\Controllers\admin\MenuElementController@update')->name('menu.update');
            Route::get('/show',         'App\Http\Controllers\admin\MenuElementController@show')->name('menu.show');
            Route::get('/delete',       'App\Http\Controllers\admin\MenuElementController@delete')->name('menu.delete');
        });
    });
});
