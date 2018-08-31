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
  Route::get('notifications', 'UsersController@notifications');
  Route::get('notifications_read', 'UsersController@notifications_read');
  Route::get('updatenot', 'UsersController@updateNotifications');
});

Route::group(['prefix' => 'terms', 'middleware' => ['auth']], function () {
  Route::resource('terms', 'TermsAndConditionsController');
  Route::get('list', 'TermsAndConditionsController@index')->name('terms.list');
  Route::get('add', 'TermsAndConditionsController@add')->name('terms.add');
  Route::get('edit{id}', 'TermsAndConditionsController@edit')->name('terms.edit');
  Route::get('msg/{id}', 'TermsAndConditionsController@destroymsg')->name('terms.msg');
  Route::put('delete-term/{id}', ['uses' => 'TermsAndConditionsController@destroyTerm', 'as' => 'delete-term']);
});

Route::group(['prefix' => 'templates', 'middleware' => ['auth']], function () {
  Route::get('edit/{id}', 'EmailsTemplateController@edit')->name('emails-template.edit');
  Route::get('add', 'EmailsTemplateController@add')->name('emails-template.add');
  Route::get('preview', 'EmailsTemplateController@preview')->name('emails-template.preview');
  Route::get('msg/{id}', 'EmailsTemplateController@destroymsg')->name('emails-template.msg');
  Route::get('show/{id}', 'EmailsTemplateController@show')->name('emails-template.show');
  Route::get('update/{id}', 'EmailsTemplateController@update')->name('emails-template.update');
  Route::put('delete-emails-template/{id}', ['uses' => 'EmailsTemplateController@destroyTemplate', 'as' => 'delete-emails-template']);

});
Route::resource('templates', 'EmailsTemplateController')->middleware('auth');

Route::group(['prefix' => 'preferences', 'middleware' => ['auth']], function(){
  Route::resource('preferences', 'CompanyBrandingController');
  Route::get('config', 'CompanyBrandingController@edit')->name('company-brands.edit');
});

Route::group(['prefix' => 'mail', 'middleware' => ['auth']], function(){
  Route::resource('mail', 'MailSendController');
  Route::get('list', 'MailSendController@index')->name('mail.list');
  Route::get('send{id}', 'MailSendController@send')->name('mail.send');
});

Route::middleware(['auth'])->prefix('surcharges')->group(function () {
  Route::get('add', 'SurchargesController@add')->name('surcharges.add');
  Route::get('msg/{surcharge_id}', 'SurchargesController@destroymsg')->name('surcharges.msg');
  Route::get('delete/{surcharge_id}', ['uses' => 'SurchargesController@destroy', 'as' => 'delete-surcharges']);
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
    Route::get('delete-rates/{rate_id}', ['uses' => 'ContractsController@destroyRates', 'as' => 'delete-rates']);
    Route::get('editLocalCharge/{id}', ['uses' => 'ContractsController@editLocalChar', 'as' => 'edit-local-charge']);
    Route::put('updateLocalCharge/{id}', ['uses' => 'ContractsController@updateLocalChar', 'as' => 'update-local-charge']);
    Route::get('editRate/{id}', ['uses' => 'ContractsController@editRates', 'as' => 'edit-rates']);
    Route::put('updateRate/{id}', ['uses' => 'ContractsController@updateRates', 'as' => 'update-rates']);
    Route::get('deleteLocalCharge/{id}', ['uses' => 'ContractsController@destroyLocalCharges', 'as' => 'delete-local-charge']);
    Route::get('deleteContract/{id}', ['uses' => 'ContractsController@deleteContract', 'as' => 'contracts.delete']);
    Route::get('destroyContract/{id}', ['uses' => 'ContractsController@destroyContract', 'as' => 'contracts.destroyContract']);

    // Rates

    Route::put('UploadFileRates','ContractsController@UploadFileRateForContract')->name('Upload.File.Rates.For.Contracts');

    Route::get('FailedRatesForContractsDeveloperView/{id}/{ids}','ContractsController@FailedRatesDeveloperLoad')->name('Failed.Rates.Developer.view.For.Contracts');
    Route::get('FailedRatesForContractsDeveloper/{id}/{bo}','ContractsController@FailedRatesDeveloper')->name('Failed.Rates.Developer.For.Contracts');
    Route::get('EditRatesGoodForContracts/{id}','ContractsController@EditRatesGood')->name('Edit.Rates.Good.For.Contracts');
    Route::get('EditRatesFailForContracts/{id}','ContractsController@EditRatesFail')->name('Edit.Rates.Fail.For.Contracts');
    Route::PUT('CreateRatesFailForContracts/{id}','ContractsController@CreateRates')->name('create.Rates.For.Contracts');
    Route::get('UpdateRatesFailForContracts/{id}','ContractsController@UpdateRatesD')->name('Update.RatesD.For.Contracts');

    Route::get('DestroyRatesFailForContracts/{id}','ContractsController@DestroyRatesF')->name('Destroy.RatesF.For.Contracts');
    Route::get('DestroyRatesGForContracts/{id}','ContractsController@DestroyRatesG')->name('Destroy.RatesG.For.Contracts');

    // Surcharge
    Route::put('UploadFileSubchargeForContracts','ContractsController@UploadFileSubchargeForContract')->name('Upload.File.Subcharge.For.Contracts');

    //----- developer
    Route::get('FailSurchargeFCD/{id}/{bo}','ContractsController@FailedSurchargeDeveloper')->name('Failed.Surcharge.F.C.D');
    Route::get('FailedSurchargeFCDView/{id}/{ids}','ContractsController@FailSurchargeLoad')->name('Failed.Surcharge.V.F.C');
    Route::get('EditSurchargersGoodForContracts/{id}','ContractsController@EditSurchargersGood')->name('Edit.Surchargers.Good.For.Contracts');
    Route::get('EditSurchargersFailForContracts/{id}','ContractsController@EditSurchargersFail')->name('Edit.Surchargers.Fail.For.Contracts');
    Route::PUT('CreateSurchargersFailForContracts/{id}','ContractsController@CreateSurchargers')->name('create.Surchargers.For.Contracts');
    Route::get('UpdateSurchargersForContracts/{id}','ContractsController@UpdateSurchargersD')->name('Update.Surchargers.For.Contracts');
    Route::get('DestroySurchargersFForContracts/{id}','ContractsController@DestroySurchargersF')->name('Destroy.SurchargersF.For.Contracts');
    Route::get('DestroySurchargersGForContracts/{id}','ContractsController@DestroySurchargersG')->name('Destroy.SurchargersG.For.Contracts');

    //Contract FCL Importation

    Route::get('imporfcl','ContractsController@LoadViewImporContractFcl')->name('importaion.fcl');
    Route::get('ProcessContractFcl','ContractsController@ProcessContractFcl')->name('process.contract.fcl');
    Route::get('ProcessContractFclRatSurch','ContractsController@ProcessContractFclRatSurch')->name('process.contract.fcl.Rat.Surch');
    Route::PUT('UploadFileNewContracts','ContractsController@UploadFileNewContract')->name('Upload.File.New.Contracts');
    Route::get('FailRatesSurchrgesForNewContracts/{id}','ContractsController@failRatesSurchrgesForNewContracts')->name('Fail.Rates.Surchrges.For.New.Contracts');
    Route::get('RedirectProcessedInformation/','ContractsController@redirectProcessedInformation')->name('redirect.Processed.Information');

    //New Request Importation
    Route::get('Requestimporfcl','ContractsController@LoadViewRequestImporContractFcl')->name('Request.importaion.fcl');

    Route::resource('RequestImportation','NewContractRequestsController');
    Route::get('RequestStatus','NewContractRequestsController@UpdateStatusRequest')->name('Request.status');

    //Developer Datatables



    // DATATABLES

    Route::get('eloquent/object-data/{id}', 'ContractsController@data')->name('localchar.table');
    Route::get('eloquent/object-rate/{id}', 'ContractsController@dataRates')->name('rate.table');
    Route::get('eloquent/object-contract', 'ContractsController@contractRates')->name('contract.table');
    Route::get('eloquent/object-contractG', 'ContractsController@contractTable')->name('contract.tableG');
});

Route::resource('UploadFile','FileHarborsPortsController');

Route::resource('contracts', 'ContractsController')->middleware('auth');

//Companies
Route::middleware(['auth'])->prefix('companies')->group(function () {
  Route::get('add', 'CompanyController@add')->name('companies.add');
  Route::get('addM', 'CompanyController@addWithModal')->name('companies.addM'); // with modal
  Route::get('show/{company_id}', 'PriceController@show')->name('companies.show');
  Route::get('delete/{company_id}', 'CompanyController@delete')->name('companies.delete');
  Route::get('destroy/{company_id}', 'CompanyController@destroy')->name('companies.destroy');
});
Route::resource('companies', 'CompanyController')->middleware('auth');

//Pricees
Route::middleware(['auth'])->prefix('prices')->group(function () {
  Route::get('add', 'PriceController@add')->name('prices.add');
  Route::get('delete/{company_id}', 'PriceController@delete')->name('prices.delete');
  Route::get('destroy/{price_id}', 'PriceController@destroy')->name('prices.destroy');
});
Route::resource('prices', 'PriceController')->middleware('auth');

//Contacts
Route::middleware(['auth'])->prefix('contacts')->group(function () {
  Route::get('add', 'ContactController@add')->name('contacts.add');
  Route::get('addCM', 'ContactController@addWithModal')->name('contacts.addCM'); // with modal
  Route::get('delete/{contact_id}', 'ContactController@destroy')->name('contacts.delete');
});
Route::resource('contacts', 'ContactController')->middleware('auth');

//Inlands
Route::middleware(['auth'])->prefix('inlands')->group(function () {
  Route::get('add', 'InlandsController@add')->name('inlands.add');
  Route::get('updateDetails/{id}', ['uses' => 'InlandsController@updateDetails', 'as' => 'updateDetails']);
  Route::get('deleteDetails/{id}', ['uses' => 'InlandsController@deleteDetails', 'as' => 'delete-inland']);
  Route::get('deleteInland/{id}', ['uses' => 'InlandsController@deleteInland', 'as' => 'delete-inland']);
});
Route::resource('inlands', 'InlandsController')->middleware('auth');

//Quotes
Route::middleware(['auth'])->prefix('quotes')->group(function () {
  Route::get('delete/{contact_id}', 'QuoteController@destroy')->name('quotes.destroy');
  Route::get('get/harbor/id/{harbor_id}', 'QuoteController@getHarborName')->name('quotes.harbor_name');
  Route::get('get/airport/id/{airport_id}', 'QuoteController@getAirportName')->name('quotes.airport_name');
  Route::get('company/price/id/{company_id}', 'CompanyController@getCompanyPrice')->name('quotes.company.price');
  Route::get('company/contact/id/{company_id}', 'CompanyController@getCompanyContact')->name('quotes.company.contact');
  Route::get('company/companies', 'CompanyController@getCompanies')->name('quotes.companies');
  Route::get('contacts/contact', 'ContactController@getContacts')->name('quotes.contacts');
  Route::post('listRate', 'QuoteController@listRate')->name('quotes.listRate');
  Route::get('pdf/{quote_id}', 'PdfController@quote')->name('quotes.pdf');
  Route::get('automatic', 'QuoteController@automatic')->name('quotes.automatic');
  Route::get('duplicate/{id}', 'QuoteController@duplicate')->name('quotes.duplicate');
  Route::post('send/pdf', 'PdfController@send_pdf_quote')->name('quotes.send_pdf');
  Route::post('test', 'QuoteController@test')->name('quotes.test');
  Route::get('terms/{harbor_id}', 'QuoteController@getQuoteTerms')->name('quotes.terms');
  Route::post('update/status/{quote_id}', 'QuoteController@updateStatus')->name('quotes.update.status');
  Route::get('change/status/{id}', 'QuoteController@changeStatus')->name('quotes.change_status');
  Route::get('quoteSchedules/{orig_port?}/{dest_port?}/{date_pick?}','QuoteController@scheduleManual')->name('quotes.schedule');
  Route::post('store/email', 'QuoteController@storeWithEmail')->name('quotes.store.email');
  Route::post('store/pdf', 'QuoteController@storeWithPdf')->name('quotes.store.pdf');
  Route::get('show/pdf/{id}', 'QuoteController@showWithPdf')->name('quotes.show.pdf');
});
Route::resource('quotes', 'QuoteController')->middleware('auth');

//Settings
Route::middleware(['auth'])->prefix('settings')->group(function () {
  Route::post('store/profile/company', ['uses' => 'SettingController@store', 'as' => 'settings.store']);
});
Route::resource('settings', 'SettingController')->middleware('auth');

//SaleTerms
Route::middleware(['auth'])->prefix('saleterms')->group(function () {
  Route::get('create', 'SaleTermController@create')->name('saleterms.create');
  Route::get('msg/{sale_term_id}', 'SaleTermController@destroymsg')->name('saleterms.msg');
  Route::get('delete/{sale_term_id}', ['uses' => 'SaleTermController@destroy', 'as' => 'saleterms.delete']);
  Route::get('edit/{sale_term_id}', ['uses' => 'SaleTermController@destroy', 'as' => 'saleterms.edit']);
});

Route::resource('saleterms', 'SaleTermController')->middleware('auth');

Auth::routes();

