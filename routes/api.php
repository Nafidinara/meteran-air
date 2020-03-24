<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('v1')->group(function () {

    Route::prefix('user')->group(function () {
        Route::post('register','UserController@register');
        Route::post('login','UserController@login');
        Route::post('edit/{user_id}','UserController@update')->middleware('check.session');
    });

    Route::prefix('pelanggan')->group(function () {
        Route::post('add','DataController@create');
        Route::post('update_tagihan/{pelanggan_id}','DataController@create_tagihan');
        Route::get('all','DataController@index');
    });

    Route::prefix('master')->group(function () {
        Route::get('all','DataController@all_data_master');
        Route::post('update','DataController@update_data_master');
    });
});
