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



// Grupo de rutas para administrar Usuarios  Admin / Empresas
Route::group(['prefix' => 'users'], function () {
    Route::resource('users', 'UsersController'); 
    Route::get('home', 'UsersController@datahtml')->name('users.home');
    Route::get('add', 'UsersController@add')->name('users.add');
    Route::get('msg/{user_id}', 'UsersController@destroymsg')->name('users.msg');
    Route::get('msgreset/{user_id}', 'UsersController@resetmsg')->name('users.msgreset');
    Route::put('reset-password/{user_id}', ['uses' => 'UsersController@resetPass'  , 'as' =>'reset-password']);
    Route::put('delete-user/{user_id}', ['uses' => 'UsersController@destroyUser', 'as' => 'delete-user']);
    Route::get('logout', 'UsersController@logout')->name('users.logout');
    Route::get('verify/{token}', 'Auth\RegisterController@verifyUser');
});



Route::group(['prefix' => 'surcharges'], function () {

    Route::get('add', 'SurchargesController@add')->name('surcharges.add');
    Route::get('msg/{surcharge_id}', 'SurchargesController@destroymsg')->name('surcharges.msg');
    Route::put('delete-surcharges/{surcharge_id}', ['uses' => 'SurchargesController@destroySubcharge', 'as' => 'delete-surcharges']);


});
Route::resource('surcharges', 'SurchargesController'); 



Route::group(['prefix' => 'globalcharges'], function () {

    Route::get('add', 'GlobalChargesController@add')->name('globalcharges.add');
    Route::get('updateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@updateGlobalChar', 'as' => 'update-global-charge']);
    Route::get('deleteGlobalCharge/{id}', ['uses' => 'GlobalChargesController@destroyGlobalCharges', 'as' => 'delete-global-charge']);


});
Route::resource('globalcharges', 'GlobalChargesController'); 


Route::group(['prefix' => 'contracts'], function () {

    //Route::get('add', 'ContractsController@add')->name('contracts.add');
    Route::get('addT', 'ContractsController@add')->name('contracts.add');
    Route::get('msg/{id}', 'ContractsController@destroymsg')->name('contracts.msg');
    Route::put('delete-rates/{rate_id}', ['uses' => 'ContractsController@destroyRates', 'as' => 'delete-rates']);
    Route::get('updateLocalCharge/{id}', ['uses' => 'ContractsController@updateLocalChar', 'as' => 'update-local-charge']);
    Route::get('updateRate/{id}', ['uses' => 'ContractsController@updateRates', 'as' => 'update-rates']);
    Route::get('deleteLocalCharge/{id}', ['uses' => 'ContractsController@destroyLocalCharges', 'as' => 'delete-local-charge']);

});
Route::resource('contracts', 'ContractsController'); 


Route::group(['prefix' => 'inlands'], function () {

    Route::get('add', 'InlandsController@add')->name('inlands.add');
    Route::get('updateDetails/{id}', ['uses' => 'InlandsController@updateDetails', 'as' => 'updateDetails']);
    Route::get('deleteDetails/{id}', ['uses' => 'InlandsController@deleteDetails', 'as' => 'delete-inland']);


});
Route::resource('inlands', 'InlandsController'); 



Route::get('/companies', function () {
    return view('companies');
});


Auth::routes();