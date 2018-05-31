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
    return redirect('users/home');
});

Route::get('/home', function () {
    return redirect('users/home');
});

Route::get('verify/{token}', 'Auth\RegisterController@verifyUser');
// Grupo de rutas para administrar Usuarios  Admin / Empresas
Route::middleware(['auth'])->prefix('users')->group(function () {
    Route::resource('users', 'UsersController'); 
    Route::get('home', 'UsersController@datahtml')->name('users.home');
    Route::get('add', 'UsersController@add')->name('users.add');
    Route::get('msg/{user_id}', 'UsersController@destroymsg')->name('users.msg');
    Route::get('msgreset/{user_id}', 'UsersController@resetmsg')->name('users.msgreset');
    Route::put('reset-password/{user_id}', ['uses' => 'UsersController@resetPass'  , 'as' =>'reset-password']);
    Route::put('delete-user/{user_id}', ['uses' => 'UsersController@destroyUser', 'as' => 'delete-user']);
    Route::get('activate/{user_id}', ['as' => 'users.activate', 'uses' => 'UsersController@activate']);
});

Route::group(['prefix' => 'terms', 'middleware' => ['auth']], function () {
    Route::resource('terms', 'TermsAndConditionsController');
    Route::get('list', 'TermsAndConditionsController@index')->name('terms.list');
    Route::get('add', 'TermsAndConditionsController@add')->name('terms.add');
    Route::get('edit{id}', 'TermsAndConditionsController@edit')->name('terms.edit');
    Route::get('msg/{id}', 'TermsAndConditionsController@destroymsg')->name('terms.msg');
    Route::put('delete-term/{id}', ['uses' => 'TermsAndConditionsController@destroyTerm', 'as' => 'delete-term']);
});
Route::group(['prefix' => 'mail-templates', 'middleware' => ['auth']], function () {
    Route::resource('mail-templates', 'EmailsTemplateController');
    Route::get('list', 'EmailsTemplateController@index')->name('emails-template.list');
    Route::get('edit{id}', 'EmailsTemplateController@edit')->name('emails-template.edit');
    Route::get('add', 'EmailsTemplateController@add')->name('emails-template.add');
    Route::get('msg/{id}', 'EmailsTemplateController@destroymsg')->name('emails-template.msg');
    Route::get('show/{id}', 'EmailsTemplateController@show')->name('emails-template.show');
    Route::put('delete-emails-template/{id}', ['uses' => 'EmailsTemplateController@destroyTemplate', 'as' => 'delete-emails-template']);
    
});
Route::group(['prefix' => 'preferences', 'middleware' => ['auth']], function(){
    Route::resource('preferences', 'CompanyBrandingController');
    Route::get('config', 'CompanyBrandingController@edit')->name('company-brands.edit');
});
Route::middleware(['auth'])->prefix('surcharges')->group(function () {
    Route::get('add', 'SurchargesController@add')->name('surcharges.add');
    Route::get('msg/{surcharge_id}', 'SurchargesController@destroymsg')->name('surcharges.msg');
    Route::put('delete-surcharges/{surcharge_id}', ['uses' => 'SurchargesController@destroySubcharge', 'as' => 'delete-surcharges']);
});
Route::resource('surcharges', 'SurchargesController')->middleware('auth');

Route::middleware(['auth'])->prefix('globalcharges')->group(function () {
    Route::get('add', 'GlobalChargesController@add')->name('globalcharges.add');
    Route::get('updateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@updateGlobalChar', 'as' => 'update-global-charge']);
    Route::get('deleteGlobalCharge/{id}', ['uses' => 'GlobalChargesController@destroyGlobalCharges', 'as' => 'delete-global-charge']);
});
Route::resource('globalcharges', 'GlobalChargesController')->middleware('auth');

Route::middleware(['auth'])->prefix('contracts')->group(function () {
    //Route::get('add', 'ContractsController@add')->name('contracts.add');
    Route::get('addT', 'ContractsController@add')->name('contracts.add');
    Route::get('msg/{id}', 'ContractsController@destroymsg')->name('contracts.msg');
    Route::put('delete-rates/{rate_id}', ['uses' => 'ContractsController@destroyRates', 'as' => 'delete-rates']);
    Route::get('updateLocalCharge/{id}', ['uses' => 'ContractsController@updateLocalChar', 'as' => 'update-local-charge']);
    Route::get('updateRate/{id}', ['uses' => 'ContractsController@updateRates', 'as' => 'update-rates']);
    Route::get('deleteLocalCharge/{id}', ['uses' => 'ContractsController@destroyLocalCharges', 'as' => 'delete-local-charge']);
    Route::put('UploadFileRates','ContractsController@UploadFileRateForContract')->name('Upload.File.Rates.For.Contracts');
    Route::get('FailedRatesForContracts/{id}','ContractsController@FailedRates')->name('Failed.Rates.For.Contracts');
    Route::get('CorrectedRateForContracts','ContractsController@SaveCorrectedRate')->name('Corrected.Rate.For.Contracts');


});


/*Route::middleware(['auth'])->prefix('uploadfile')->group(function () {

    Route::get('ShowUploadFile',function(){  return view('contracts.UploadFile');  })->name('Show.Upload.File');

});*/

Route::resource('UploadFileRates','ImportationRatesController');

Route::resource('contracts', 'ContractsController')->middleware('auth');

Route::middleware(['auth'])->prefix('companies')->group(function () {
    Route::get('add', 'CompanyController@add')->name('companies.add');
    Route::get('show/{company_id}', 'PriceController@show')->name('companies.show');
    Route::get('delete/{company_id}', 'CompanyController@delete')->name('companies.delete');
    Route::get('destroy/{company_id}', 'CompanyController@destroy')->name('companies.destroy');
});
Route::resource('companies', 'CompanyController')->middleware('auth');

Route::middleware(['auth'])->prefix('prices')->group(function () {
    Route::get('add', 'PriceController@add')->name('prices.add');
    Route::get('delete/{company_id}', 'PriceController@delete')->name('prices.delete');
});
Route::resource('prices', 'PriceController')->middleware('auth');


Route::middleware(['auth'])->prefix('contacts')->group(function () {
    Route::get('add', 'ContactController@add')->name('contacts.add');
    Route::get('delete/{contact_id}', 'ContactController@destroy')->name('contacts.delete');
});

Route::resource('contacts', 'ContactController')->middleware('auth');

Route::middleware(['auth'])->prefix('inlands')->group(function () {
    Route::get('add', 'InlandsController@add')->name('inlands.add');
    Route::get('updateDetails/{id}', ['uses' => 'InlandsController@updateDetails', 'as' => 'updateDetails']);
    Route::get('deleteDetails/{id}', ['uses' => 'InlandsController@deleteDetails', 'as' => 'delete-inland']);
    Route::get('deleteInland/{id}', ['uses' => 'InlandsController@deleteInland', 'as' => 'delete-inland']);
});
Route::resource('inlands', 'InlandsController')->middleware('auth');

Route::middleware(['auth'])->prefix('quotes')->group(function () {
    Route::get('delete/{contact_id}', 'QuoteController@destroy')->name('quotes.destroy');
    Route::get('get/harbor/id/{harbor_id}', 'QuoteController@getHarborName')->name('quotes.harbor_name');
    Route::get('company/price/id/{company_id}', 'CompanyController@getCompanyPrice')->name('quotes.company.price');
    Route::get('company/contact/id/{company_id}', 'CompanyController@getCompanyContact')->name('quotes.company.contact');
    Route::post('listRate', 'QuoteController@listRate')->name('quotes.listRate');
    Route::get('pdf/{quote_id}', 'PdfController@quote')->name('quotes.pdf');
    Route::get('automatic', 'QuoteController@automatic')->name('quotes.automatic');
    Route::get('duplicate/{id}', 'QuoteController@duplicate')->name('quotes.duplicate');
    Route::get('send/pdf/{id}', 'PdfController@send_pdf_quote')->name('quotes.send_pdf');
    Route::post('test', 'QuoteController@test')->name('quotes.test');
});
Route::resource('quotes', 'QuoteController')->middleware('auth');

Route::middleware(['auth'])->prefix('settings')->group(function () {
    Route::post('store/profile/company', ['uses' => 'SettingController@store', 'as' => 'settings.store']);
});
Route::resource('settings', 'SettingController')->middleware('auth');

Auth::routes();

