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

Route::get('currency/{value}', function($value) {
    return \App\Currency::find($value);
});

Route::get('currency/alphacode/{value}', function($value) {
    return \App\Currency::where('alphacode',$value)->first();
});

Route::get('airports/', function() {
    return \App\Airport::All();
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'ApiController@login');
    Route::get('test', 'ApiController@test');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'ApiController@logout');
        Route::get('user', 'ApiController@user');
        Route::get('quotes', 'QuoteV2Controller@index');
        Route::get('quotes/{id}', 'QuoteV2Controller@show');
        Route::get('fcl/rates', 'ApiController@rates');
        Route::get('fcl/charges', 'ApiController@charges');
        Route::get('fcl/global/charges', 'ApiController@globalCharges');
        Route::get('companies', 'CompanyController@index');
        Route::get('contacts', 'ContactController@index');
        Route::post('create/company', 'CompanyController@store');
        Route::post('create/contact', 'ContactController@store');
    });
});
