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
    Route::get('reset-password/{user_id}', 'UsersController@resetPass')->name('users.password');
    Route::put('delete-user/{user_id}', ['uses' => 'UsersController@destroyUser', 'as' => 'delete-user']);
    Route::get('logout', 'UsersController@logout')->name('users.logout');
});



Route::group(['prefix' => 'surcharges'], function () {

    Route::get('add', 'SurchargesController@add')->name('surcharges.add');
    Route::get('msg/{surcharge_id}', 'SurchargesController@destroymsg')->name('surcharges.msg');
    Route::put('delete-surcharges/{surcharge_id}', ['uses' => 'SurchargesController@destroySubcharge', 'as' => 'delete-surcharges']);


});
Route::resource('surcharges', 'SurchargesController'); 


Route::group(['prefix' => 'contracts'], function () {

    //Route::get('add', 'ContractsController@add')->name('contracts.add');
    Route::get('addT', 'ContractsController@add')->name('contracts.add');
    Route::get('msg/{id}', 'ContractsController@destroymsg')->name('contracts.msg');
    Route::put('delete-rates/{rate_id}', ['uses' => 'ContractsController@destroyRates', 'as' => 'delete-rates']);
    
});
Route::resource('contracts', 'ContractsController'); 


Route::get('/companies', function () {
    return view('companies');
});

Auth::routes();