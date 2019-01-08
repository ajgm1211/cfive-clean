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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/v1/quotes','QuoteController@index');

Route::get('currency/{value}', function($value) {
    return \App\Currency::find($value);
});

Route::get('airports/', function() {
    return \App\Airport::All();
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'AuthApiController@login');
    Route::post('signup', 'AuthController@signup');
    Route::get('test', 'ApiController@test');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthApiController@logout');
        Route::get('user', 'AuthApiController@user');
        Route::get('quotes', 'QuoteController@index');
    });
});
