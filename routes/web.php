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
    Route::resource('users', 'UsersController'); // recibe dos parametros el primero el nombre  el segundo es el 
    Route::get('home', 'UsersController@datahtml')->name('users.home');
    Route::get('add', 'UsersController@add')->name('users.add');
    Route::get('msg/{user_id}', 'UsersController@destroymsg')->name('users.msg');
    Route::put('delete-user/{user_id}', ['uses' => 'UsersController@destroyUser', 'as' => 'delete-user']);

});



Route::get('/companies', function () {
    return view('companies');
});

Auth::routes();