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
        Route::get('getContract', ['as' => 'getContract.index.v2', 'uses' => 'ApiController@getContract']);

        Route::get('get_rates/{contract}', ['as' => 'search.contract.id', 'uses' => 'ApiController@processSearchByContract']);
        //Calculation types
        Route::get('calculationtypes/{type}', 'ApiCalculationTypeController@index');
    });
});

Route::group(['prefix' => 'v2'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('quotes', 'QuotationApiController@list');
        Route::get('quotes/{id}', 'QuotationApiController@retrieve');

        // Providers
        Route::put('provider/{id}/update/refcode', 'ProvidersController@updateRefCode');
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

$router->get('pdf/{id}',['as' => 'pdf.api', 'uses' => 'ApiController@pdfApi']);

// WHITELABEL ROUTES

Route::group(['prefix' => 'whitelabel'], function () {

    Route::group(['middleware' => 'auth:api'], function () {

        /**Route::get('/users', [App\Http\Controllers\Whitelabel\UsersController::class, 'index']);
        Route::get('/users/{id}', [App\Http\Controllers\Whitelabel\UsersController::class, 'show']);
        Route::post('/users/save', [App\Http\Controllers\Whitelabel\UsersController::class, 'store']);
        Route::put('/users/{id}', [App\Http\Controllers\Whitelabel\UsersController::class, 'update']);
        Route::delete('/users/{id}', [App\Http\Controllers\Whitelabel\UsersController::class, 'destroy']);

        Route::get('/contacts', [App\Http\Controllers\Whitelabel\ContactsController::class, 'index']);
        Route::get('/contacts/{id}', [App\Http\Controllers\Whitelabel\ContactsController::class, 'show']);
        Route::post('/contacts/save', [App\Http\Controllers\Whitelabel\ContactsController::class, 'store']);
        Route::put('/contacts/{id}', [App\Http\Controllers\Whitelabel\ContactsController::class, 'update']);
        Route::delete('/contacts/{id}', [App\Http\Controllers\Whitelabel\ContactsController::class, 'destroy']);

        Route::get('/search', 'SearchApiController@index');
        Route::get('/search/{search}', 'SearchApiController@retrieve');
        Route::get('/search/list', 'SearchApiController@list');
        Route::post('/search/store', 'SearchApiController@store');**/

        Route::post('/settings/save', [App\Http\Controllers\Whitelabel\SettingsController::class, 'store']);
        Route::post('/search/process', 'SearchApiController@processSearch');
    });
});

// NEW PRICE LEVELS ROUTES

Route::group(['prefix'=>'pricelevels','middleware' => 'auth:api'], function () {
    Route::get('data', 'PriceLevelController@data');
    Route::get('list', 'PriceLevelController@list');
    Route::post('store', 'PriceLevelController@store');
    Route::post('{price_level}/update', 'PriceLevelController@update');
    Route::post('{price_level}/duplicate', 'PriceLevelController@duplicate');
    Route::put('{price_level}/delete', 'PriceLevelController@destroy');
    Route::put('deleteAll', 'PriceLevelController@destroyAll');
    Route::get('retrieve/{price_level}', 'PriceLevelController@retrieve');
});

Route::group(['prefix'=>'pricelevels/details','middleware' => 'auth:api'], function () {
    Route::get('{price_level}/list', 'PriceLevelDetailController@list');
    Route::post('{price_level}/store', 'PriceLevelDetailController@store');
    Route::post('{price_level_detail}/update', 'PriceLevelDetailController@update');
    Route::post('{price_level_detail}/duplicate', 'PriceLevelDetailController@duplicate');
    Route::put('{price_level_detail}/destroy', 'PriceLevelDetailController@destroy');
    Route::put('destroyAll', 'PriceLevelDetailController@destroyAll');
});

Route::group(['prefix'=>'pricelevels/groups','middleware' => 'auth:api'], function () {
    Route::get('list', 'CompanyGroupController@list');
    Route::post('store', 'CompanyGroupController@store');
    Route::post('{company_group}/update', 'CompanyGroupController@update');
    Route::post('{company_group}/duplicate', 'CompanyGroupController@duplicate');
    Route::delete('{company_group}/delete', 'CompanyGroupController@destroy');
    Route::delete('deleteAll', 'CompanyGroupController@destroyAll');
});

// API INTEGRATIONS
Route::group(['prefix'=>'apiCredentials','middleware' => 'auth:api'], function () {
    Route::get('companyUsers', 'ApiCredentialsController@listCompanyUsers');
    Route::get('companyUser/{companyUser}', 'ApiCredentialsController@listApiProvidersByCompanyUser');
    Route::get('companyUsers/search/{search}', 'ApiCredentialsController@searchCompanyUsers');
    Route::post('apiProviders', 'ApiCredentialsController@listAvailableApiProviders');
    Route::post('store', 'ApiCredentialsController@store');
    Route::post('update/{apiCredential}', 'ApiCredentialsController@update');
    Route::post('status/{apiCredential}', 'ApiCredentialsController@updateStatus');
    Route::post('companyUser/{companyUser}/deleteApiProvider', 'ApiCredentialsController@deleteApiProviderOfCompanyUser');
});

Route::group(['prefix'=>'companies','middleware' => 'auth:api'], function () {
    Route::get('data', 'CompanyV2Controller@data');
    Route::get('list', 'CompanyV2Controller@list');
    Route::post('store', 'CompanyV2Controller@store');
    Route::post('{company}/update', 'CompanyV2Controller@update');
    Route::post('{company}/duplicate', 'CompanyV2Controller@duplicate');
    Route::put('{company}/delete', 'CompanyV2Controller@destroy');
    Route::put('deleteAll', 'CompanyV2Controller@destroyAll');
    Route::get('retrieve/{company}', 'CompanyV2Controller@retrieve');
    Route::get('template', 'CompanyV2Controller@downloadTemplatefile');
    Route::get('failed/list', 'CompanyV2Controller@failedList');
    Route::get('{company}/contacts', 'CompanyV2Controller@contactByCompanyList');
    Route::post('toWhiteLevel', 'CompanyV2Controller@transferToWhiteLevel');
    Route::get('export/{format}', 'CompanyV2Controller@exportCompanies');
    Route::post('create-massive', 'CompanyV2Controller@createCompaniesMassive');
    
});

Route::group(['prefix'=>'contacts','middleware' => 'auth:api'], function () {
    Route::get('data', 'ContactV2Controller@data');
    Route::get('list', 'ContactV2Controller@list');
    Route::post('store', 'ContactV2Controller@store');
    Route::post('{company}/update', 'ContactV2Controller@update');
    Route::post('{company}/duplicate', 'ContactV2Controller@duplicate');
    Route::put('{company}/delete', 'ContactV2Controller@destroy');
    Route::put('deleteAll', 'ContactV2Controller@destroyAll');
    Route::get('retrieve/{company}', 'ContactV2Controller@retrieve');
});