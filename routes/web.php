<?php

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

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/home', function () {
    return redirect('users/home');
});
Route::get('verify/{token}', 'Auth\RegisterController@verifyUser');
// Grupo de rutas para administrar Usuarios  Admin / Empresas
Route::group(['prefix' => 'users', 'middleware' => ['auth']], function () {
    Route::resource('users', 'UsersController'); 
    Route::get('home', 'UsersController@datahtml')->name('users.home');
    Route::get('add', 'UsersController@add')->name('users.add');
    Route::get('msg/{user_id}', 'UsersController@destroymsg')->name('users.msg');
    Route::get('msgreset/{user_id}', 'UsersController@resetmsg')->name('users.msgreset');
    Route::put('reset-password/{user_id}', ['uses' => 'UsersController@resetPass'  , 'as' =>'reset-password']);
    Route::put('delete-user/{user_id}', ['uses' => 'UsersController@destroyUser', 'as' => 'delete-user']);
    Route::get('activate/{user_id}', ['as' => 'users.activate', 'uses' => 'UsersController@activate']);
    Route::get('logout', 'UsersController@logout')->name('users.logout');
});

Route::group(['prefix' => 'surcharges', 'middleware' => ['auth']], function () {
    Route::get('add', 'SurchargesController@add')->name('surcharges.add');
    Route::get('msg/{surcharge_id}', 'SurchargesController@destroymsg')->name('surcharges.msg');
    Route::put('delete-surcharges/{surcharge_id}', ['uses' => 'SurchargesController@destroySubcharge', 'as' => 'delete-surcharges']);
});
Route::resource('surcharges', 'SurchargesController')->middleware('auth');


Route::group(['prefix' => 'globalcharges', 'middleware' => ['auth']], function () {

    Route::get('add', 'GlobalChargesController@add')->name('globalcharges.add');
    Route::get('updateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@updateGlobalChar', 'as' => 'update-global-charge']);
    Route::get('deleteGlobalCharge/{id}', ['uses' => 'GlobalChargesController@destroyGlobalCharges', 'as' => 'delete-global-charge']);


});
Route::resource('globalcharges', 'GlobalChargesController')->middleware('auth');

Route::group(['prefix' => 'contracts', 'middleware' => ['auth']], function () {
    //Route::get('add', 'ContractsController@add')->name('contracts.add');
    Route::get('addT', 'ContractsController@add')->name('contracts.add');
    Route::get('msg/{id}', 'ContractsController@destroymsg')->name('contracts.msg');
    Route::put('delete-rates/{rate_id}', ['uses' => 'ContractsController@destroyRates', 'as' => 'delete-rates']);
    Route::get('updateLocalCharge/{id}', ['uses' => 'ContractsController@updateLocalChar', 'as' => 'update-local-charge']);
    Route::get('updateRate/{id}', ['uses' => 'ContractsController@updateRates', 'as' => 'update-rates']);
    Route::get('deleteLocalCharge/{id}', ['uses' => 'ContractsController@destroyLocalCharges', 'as' => 'delete-local-charge']);
});
Route::resource('contracts', 'ContractsController')->middleware('auth');

Route::group(['prefix' => 'companies', 'middleware' => ['auth']], function () {
    Route::get('add', 'CompanyController@add')->name('companies.add');
    Route::get('show/{company_id}', 'PriceController@show')->name('companies.show');
    Route::get('delete/{company_id}', 'CompanyController@delete')->name('companies.delete');
});
Route::resource('companies', 'CompanyController')->middleware('auth');

Route::group(['prefix' => 'prices', 'middleware' => ['auth']], function () {
    Route::get('add', 'PriceController@add')->name('prices.add');
    Route::get('delete/{company_id}', 'PriceController@delete')->name('prices.delete');
});
Route::resource('prices', 'PriceController')->middleware('auth');

Route::group(['prefix' => 'contacts', 'middleware' => ['auth']], function () {
    Route::get('add', 'ContactController@add')->name('contacts.add');
    Route::get('delete/{contact_id}', 'ContactController@destroy')->name('contacts.delete');
});

Route::resource('contacts', 'ContactController')->middleware('auth');

Route::group(['prefix' => 'inlands'], function () {
    Route::get('add', 'InlandsController@add')->name('inlands.add');
    Route::get('updateDetails/{id}', ['uses' => 'InlandsController@updateDetails', 'as' => 'updateDetails']);
    Route::get('deleteDetails/{id}', ['uses' => 'InlandsController@deleteDetails', 'as' => 'delete-inland']);
    Route::get('deleteInland/{id}', ['uses' => 'InlandsController@deleteInland', 'as' => 'delete-inland']);
});
Route::resource('inlands', 'InlandsController');

Route::group(['prefix' => 'quotes', 'middleware' => ['auth']], function () {
    Route::get('delete/{contact_id}', 'QuoteController@destroy')->name('quotes.delete');
    Route::post('listRate', 'QuoteController@listRate')->name('quotes.listRate');
});
Route::resource('quotes', 'QuoteController');

Auth::routes();