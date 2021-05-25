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

Route::get('currency/{value}', function ($value) {
    return \App\Currency::find($value);
});

Route::get('currency/alphacode/{value}', function ($value) {
    return \App\Currency::where('alphacode', $value)->first();
});

Route::get('airports/', function () {
    return \App\Airport::All();
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'ApiController@login');
    Route::get('test', 'ApiController@test');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'ApiController@logout');
        //User
        Route::get('user', 'ApiController@user');
        Route::put('user/{id}', 'UsersController@update');
        //Quotes
        Route::get('quotes', 'ApiController@quotes');
        Route::get('quotes/{id}', 'ApiController@quoteById');
        //Rates&Charges
        Route::get('fcl/rates', 'ApiController@rates');
        Route::get('fcl/charges', 'ApiController@charges');
        Route::get('fcl/global/charges', 'ApiController@globalCharges');
        //Contracts
        Route::get('_contracts', 'ApiController@contracts');
        Route::post('upload/contract', 'ContractController@processUploadRequest');
        //Companies
        Route::get('companies', 'CompanyController@index');
        Route::post('company', 'CompanyController@store');
        Route::get('company/{id}', 'CompanyController@show');
        Route::put('company/{id}', 'CompanyController@update');
        Route::delete('company/{id}', 'CompanyController@destroy');
        //Contacts
        Route::get('contacts', 'ContactController@index');
        Route::post('contact', 'ContactController@store');
        Route::get('contact/{id}', 'ContactController@show');
        Route::put('contact/{id}', 'ContactController@update');
        Route::delete('contact/{id}', 'ContactController@destroy');
        //Carriers
        Route::get('carriers', 'ApiController@carriers');
        Route::get('airlines', 'ApiController@airlines');
        //surcharges
        Route::get('surcharges', 'ApiController@surcharges');
        Route::post('surcharge', 'SurchargesController@store');
        Route::put('surcharge', 'SurchargesController@update');
        Route::delete('surcharge', 'SurchargesController@destroy');
        //Ports
        Route::get('ports', 'ApiController@ports');
        //Airports
        Route::get('airports', 'ApiController@airports');
        //Rates
        Route::get('rates/{mode}/{code_origin}/{code_destination}/{inicio}/{fin}/{group}/{carrierUrl?}', ['as' => 'search.index.v2', 'uses' => 'ApiController@search']);
        Route::get('rates/lcl/{code_origin}/{code_destination}/{init_date}/{end_date}', ['as' => 'searchLCL.index.v2', 'uses' => 'ApiController@searchLCL']);
        Route::get('getContract/', ['as' => 'getContract.index.v2', 'uses' => 'ApiController@getContract']);

        Route::get('get_rates/{contract}', ['as' => 'search.contract.id', 'uses' => 'ApiController@processSearchByContract']);
        //Calculation types
        Route::get('calculationtypes/{type}', 'ApiCalculationTypeController@index');
    });
});

Route::group(['prefix' => 'v2'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('quotes', 'QuotationApiController@list');
        Route::get('quotes/{id}', 'QuotationApiController@retrieve');
    });
});

Route::group(['prefix' => 'request', 'middleware' => 'auth:api'], function () {
    //Route::group(['prefix' => 'request'], function () {
    Route::post('sendEmail', 'RequestFclV2Controller@sendEmailRequest');
});

Route::group(['prefix' => 'requestLCL', 'middleware' => 'auth:api'], function () {
    //Route::group(['prefix' => 'request'], function () {
    Route::post('sendEmail', 'NewContractRequestLclController@sendEmailRequest');
});
