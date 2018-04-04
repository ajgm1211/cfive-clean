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
    return view('admin');
});


// Grupo de rutas para administrar Usuarios  Admin / Empresas
Route::group(['prefix' => 'users'], function () {
    Route::get('home', function () {
        return view('users/index');
    });

});

Route::get('/companies', function () {
    return view('companies');
});
