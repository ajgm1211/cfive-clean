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
   return redirect('dashboard');
});

Route::get('/home', function () {
   return redirect('dashboard');
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
   Route::get('edit/{id}', 'TermsAndConditionsController@edit')->name('terms.edit');
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
   Route::put('updateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@updateGlobalChar', 'as' => 'update-global-charge']);
   Route::get('deleteGlobalCharge/{id}', ['uses' => 'GlobalChargesController@destroyGlobalCharges', 'as' => 'delete-global-charge']);
   Route::get('editGlobalCharge/{id}', ['uses' => 'GlobalChargesController@editGlobalChar', 'as' => 'edit-global-charge']);
   Route::get('addGlobalCharge', ['uses' => 'GlobalChargesController@addGlobalChar', 'as' => 'add-global-charge']);
});
Route::resource('globalcharges', 'GlobalChargesController')->middleware('auth');

Route::middleware(['auth'])->prefix('contracts')->group(function () {


   //Route::get('add', 'ContractsController@add')->name('contracts.add');
   Route::get('addT', 'ContractsController@add')->name('contracts.add');
   Route::get('msg/{id}', 'ContractsController@destroymsg')->name('contracts.msg');
   Route::get('delete-rates/{rate_id}', ['uses' => 'ContractsController@destroyRates', 'as' => 'delete-rates']);
   Route::get('editLocalCharge/{id}', ['uses' => 'ContractsController@editLocalChar', 'as' => 'edit-local-charge']);
   Route::put('updateLocalCharge/{id}', ['uses' => 'ContractsController@updateLocalChar', 'as' => 'update-local-charge']);
   Route::get('addRate/{id}', ['uses' => 'ContractsController@addRates', 'as' => 'add-rates']);
   Route::post('storeRate/{id}', ['uses' => 'ContractsController@storeRates', 'as' => 'contracts.storeRate']);
   Route::get('editRate/{id}', ['uses' => 'ContractsController@editRates', 'as' => 'edit-rates']);
   Route::put('updateRate/{id}', ['uses' => 'ContractsController@updateRates', 'as' => 'update-rates']);
   Route::get('addLocalCharge/{id}', ['uses' => 'ContractsController@addLocalChar', 'as' => 'add-LocalCharge']);
   Route::post('storeLocalCharge/{id}', ['uses' => 'ContractsController@storeLocalChar', 'as' => 'contracts.storeLocalCharge']);
   Route::get('deleteLocalCharge/{id}', ['uses' => 'ContractsController@destroyLocalCharges', 'as' => 'delete-local-charge']);
   Route::get('deleteContract/{id}', ['uses' => 'ContractsController@deleteContract', 'as' => 'contracts.delete']);
   Route::get('destroyContract/{id}', ['uses' => 'ContractsController@destroyContract', 'as' => 'contracts.destroyContract']);



   //----- developer




   Route::get('FailRatesSurchrgesForNewContracts/{id}','ContractsController@failRatesSurchrgesForNewContracts')->name('Fail.Rates.Surchrges.For.New.Contracts');

   // DATATABLES

   Route::get('eloquent/object-data/{id}', 'ContractsController@data')->name('localchar.table');
   Route::get('eloquent/object-rate/{id}', 'ContractsController@dataRates')->name('rate.table');
   Route::get('eloquent/object-contract', 'ContractsController@contractRates')->name('contract.table');
   Route::get('eloquent/object-contractG', 'ContractsController@contractTable')->name('contract.tableG');
});

Route::middleware(['auth'])->prefix('Requests')->group(function () {
   //New Request Importation
   Route::get('Requestimporfcl','NewContractRequestsController@LoadViewRequestImporContractFcl')->name('Request.importaion.fcl');
   Route::resource('RequestImportation','NewContractRequestsController');
   Route::get('RequestStatus','NewContractRequestsController@UpdateStatusRequest')->name('Request.status');
   Route::get('RequestDestroy/{id}','NewContractRequestsController@destroyRequest')->name('destroy.Request');
});


Route::middleware(['auth'])->prefix('Importation')->group(function () {

   // Importar Contracto
   Route::PUT('UploadFileNewContracts','ImportationController@UploadFileNewContract')->name('Upload.File.New.Contracts');
   Route::get('ProcessContractFcl','ImportationController@ProcessContractFcl')->name('process.contract.fcl');
   Route::get('ProcessContractFclRatSurch','ImportationController@ProcessContractFclRatSurch')->name('process.contract.fcl.Rat.Surch');
   Route::get('RedirectProcessedInformation/','ImportationController@redirectProcessedInformation')->name('redirect.Processed.Information');
   Route::get('RatesListFC/{id}/{bo}','ImportationController@FailedRatesDeveloper')->name('Failed.Rates.Developer.For.Contracts');
   Route::get('ImporFcl','ImportationController@LoadViewImporContractFcl')->name('importaion.fcl');

   // Rates
   Route::put('UploadFileRates','ImportationController@UploadFileRateForContract')->name('Upload.File.Rates.For.Contracts');
   Route::get('EditRatesGoodForContracts/{id}','ImportationController@EditRatesGood')->name('Edit.Rates.Good.For.Contracts');
   Route::get('EditRatesFailForContracts/{id}','ImportationController@EditRatesFail')->name('Edit.Rates.Fail.For.Contracts');
   Route::PUT('CreateRatesFailForContracts/{id}','ImportationController@CreateRates')->name('create.Rates.For.Contracts');
   Route::get('UpdateRatesFailForContracts/{id}','ImportationController@UpdateRatesD')->name('Update.RatesD.For.Contracts');
   Route::get('DestroyRatesFailForContracts/{id}','ImportationController@DestroyRatesF')->name('Destroy.RatesF.For.Contracts');
   Route::get('DestroyRatesGForContracts/{id}','ImportationController@DestroyRatesG')->name('Destroy.RatesG.For.Contracts');

   // Surcharge
   Route::put('UploadFileSubchargeForContracts','ImportationController@UploadFileSubchargeForContract')->name('Upload.File.Subcharge.For.Contracts');
   Route::get('FailSurchargeFC/{id}/{bo}','ImportationController@FailedSurchargeDeveloper')->name('Failed.Surcharge.F.C.D');
   Route::get('EditSurchargersGoodForContracts/{id}','ImportationController@EditSurchargersGood')->name('Edit.Surchargers.Good.For.Contracts');
   Route::get('EditSurchargersFailForContracts/{id}','ImportationController@EditSurchargersFail')->name('Edit.Surchargers.Fail.For.Contracts');
   Route::PUT('CreateSurchargersFailForContracts/{id}','ImportationController@CreateSurchargers')->name('create.Surchargers.For.Contracts');
   Route::get('UpdateSurchargersForContracts/{id}','ImportationController@UpdateSurchargersD')->name('Update.Surchargers.For.Contracts');
   Route::get('DestroySurchargersFForContracts/{id}','ImportationController@DestroySurchargersF')->name('Destroy.SurchargersF.For.Contracts');
   Route::get('DestroySurchargersGForContracts/{id}','ImportationController@DestroySurchargersG')->name('Destroy.SurchargersG.For.Contracts');

   // Reprocesar
   Route::get('/ReprocesarRates/{id}','ImportationController@ReprocesarRates')->name('Reprocesar.Rates');
   Route::get('/ReprocesarSurchargers/{id}','ImportationController@ReprocesarSurchargers')->name('Reprocesar.Surchargers');

   // Datatable Rates Y Surchargers
   Route::get('FailedRatesForContractsDeveloperView/{id}/{ids}','ImportationController@FailedRatesDeveloperLoad')->name('Failed.Rates.Developer.view.For.Contracts');
   Route::get('FailedSurchargeFCDView/{id}/{ids}','ImportationController@FailSurchargeLoad')->name('Failed.Surcharge.V.F.C');

   // DownLoad Files
   Route::get('/DownLoadFiles/{id}','ImportationController@DowLoadFiles')->name('DownLoad.Files');

   // Companies
   Route::Post('/UploadCompany','ImportationController@UploadCompanies')->name('Upload.Company');
   Route::get('/ViewFCompany','ImportationController@FailedCompnaiesView')->name('view.fail.company');
   Route::get('/ListFCompany/{id}','ImportationController@FailedCompnaieslist')->name('list.fail.company');
   Route::get('/DeleteFCompany/{id}','ImportationController@DeleteFailedCompany')->name('delete.fail.company');
   Route::get('/ShowFCompany/{id}','ImportationController@ShowFailCompany')->name('show.fail.company');
   Route::get('/UpdateFCompany/{id}','ImportationController@UpdateFailedCompany')->name('update.fail.company');

   // Contacts
   Route::Post('/UploadContacts','ImportationController@UploadContacts')->name('Upload.Contacts');
   Route::get('/ViewFContact','ImportationController@FailedContactView')->name('view.fail.contact');
   Route::get('/ListFContact/{id}','ImportationController@FailedContactlist')->name('list.fail.contact');
   Route::get('/DeleteFContact/{id}','ImportationController@DeleteFailedContact')->name('delete.fail.contact');
   Route::get('/ShowFContact/{id}','ImportationController@ShowFailContact')->name('show.fail.contact');
   Route::get('/UpdateFContact/{id}','ImportationController@UpdateFailedContact')->name('update.fail.contact');

   // Srucharge for contract
   Route::get('/ProcessImpSurcharge','ImportationController@ProcessSurchargeForContract')->name('process.imp.surcharge');

   // Test
   Route::get('/testExcelImportation','ImportationController@testExcelImportation')->name('testExcelImportation');

});

Route::middleware(['auth'])->prefix('Exportation')->group(function () {
   Route::resource('Exportation','ExportationController');
});

Route::middleware(['auth'])->prefix('Harbors')->group(function () {
   Route::resource('UploadFile','FileHarborsPortsController');
   Route::get('/loadViewAdd','FileHarborsPortsController@loadviewAdd')->name('load.View.Add');
   Route::get('/destroyharbor/{id}','FileHarborsPortsController@destroyharbor')->name('destroy.harbor');
});

Route::resource('contracts', 'ContractsController')->middleware('auth');

//Companies
Route::middleware(['auth'])->prefix('companies')->group(function () {

    Route::get('add', 'CompanyController@add')->name('companies.add');
    Route::get('addM', 'CompanyController@addWithModal')->name('companies.addM'); // with modal
    Route::get('add/owner', 'CompanyController@addOwner')->name('companies.add.owner');
    Route::post('store/owner', 'CompanyController@storeOwner')->name('companies.store.owner');
    Route::get('show/{company_id}', 'PriceController@show')->name('companies.show');
    Route::get('delete/{company_id}', 'CompanyController@delete')->name('companies.delete');
    Route::get('destroy/{company_id}', 'CompanyController@destroy')->name('companies.destroy');
    Route::get('owner/delete/{user_id}', 'CompanyController@deleteOwner')->name('companies.delete.owner');
    Route::post('payments/conditions/update', 'CompanyController@updatePaymentConditions')->name('companies.update.payments');
    Route::get('update/details/name/{company_id}', 'CompanyController@updateName')->name('companies.update.name');
    Route::get('update/details/phone/{company_id}', 'CompanyController@updatePhone')->name('companies.update.phone');
    Route::get('update/details/address/{company_id}', 'CompanyController@updateAddress')->name('companies.update.address');
    Route::get('update/details/email/{company_id}', 'CompanyController@updateEmail')->name('companies.update.email');
    Route::get('update/details/tax/{company_id}', 'CompanyController@updateTaxNumber')->name('companies.update.tax');
    Route::get('update/details/pdf/{company_id}', 'CompanyController@updatePdfLanguage')->name('companies.update.pdf');
    Route::get('update/details/prices/{company_id}', 'CompanyController@updatePriceLevels')->name('companies.update.prices');

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
   Route::get('addCMC/{company_id}', 'ContactController@addWithModalCompanies')->name('contacts.addCMC'); // with modal
   Route::get('addCMMQ', 'ContactController@addWithModalManualQuote')->name('contacts.addCMMQ'); // with modal in manual quote
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
   Route::get('contacts/contact/{company_id}', 'ContactController@getContactsByCompanyId')->name('quotes.contacts.company');
   Route::post('listRate', 'QuoteAutomaticController@listRate')->name('quotes.listRate');
   Route::get('pdf/{quote_id}', 'PdfController@quote')->name('quotes.pdf');
   Route::get('automatic', 'QuoteAutomaticController@automatic')->name('quotes.automatic');
   Route::get('duplicate/{id}', 'QuoteController@duplicate')->name('quotes.duplicate');
   Route::post('send/pdf', 'PdfController@send_pdf_quote')->name('quotes.send_pdf');
   Route::post('test', 'QuoteAutomaticController@test')->name('quotes.test');
   Route::get('terms/{harbor_id}', 'QuoteController@getQuoteTerms')->name('quotes.terms');
   Route::get('terms/{origin_harbor}/{destination_harbor}', 'QuoteController@getQuoteTermsDual')->name('quotes.terms.dual');
   Route::post('update/status/{quote_id}', 'QuoteController@updateStatus')->name('quotes.update.status');
   Route::get('change/status/{id}', 'QuoteController@changeStatus')->name('quotes.change_status');
   Route::get('quoteSchedules/{orig_port?}/{dest_port?}/{date_pick?}','QuoteController@scheduleManual')->name('quotes.schedule');
   Route::post('store/email', 'QuoteController@storeWithEmail')->name('quotes.store.email');
   Route::post('store/pdf', 'QuoteController@storeWithPdf')->name('quotes.store.pdf');
   Route::get('show/pdf/{id}', 'QuoteController@showWithPdf')->name('quotes.show.pdf');
   Route::get('airports/find', 'QuoteController@searchAirports')->name('quotes.show.airports');
   Route::get('payments/{company_id}', 'QuoteController@getCompanyPayments')->name('quotes.show.payments');
   Route::get('IndexDt', 'QuoteController@LoadDatatableIndex')->name('quotes.index.datatable');
   Route::get('contact/email/{contact_id}', 'QuoteController@getContactEmail')->name('quotes.index.contact.email');
});
Route::resource('quotes', 'QuoteController')->middleware('auth');

//Settings
Route::middleware(['auth'])->prefix('settings')->group(function () {

    Route::post('store/profile/company', ['uses' => 'SettingController@store', 'as' => 'settings.store']);
    Route::post('update/pdf/language', ['uses' => 'SettingController@update_pdf_language', 'as' => 'settings.update_pdf_language']);
    Route::post('update/pdf/type', ['uses' => 'SettingController@update_pdf_type', 'as' => 'settings.update_pdf_type']);
    Route::post('update/pdf/ammounts', ['uses' => 'SettingController@update_pdf_ammount', 'as' => 'settings.update_pdf_ammount']);
    Route::get('companies', 'SettingController@list_companies')->name('settings.companies');
    Route::get('delete/company/{company_user_id}', 'SettingController@delete_company_user')->name('settings.delete.companies');
    Route::post('duplicate', 'SettingController@duplicate')->name('settings.duplicate');

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

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
   Route::get('filter', 'DashboardController@filter')->name('dashboard.filter');
});

Route::resource('dashboard', 'DashboardController')->middleware('auth');

Route::prefix('impersonation')->group(function ($router) {
   # Revert route...
   $router->get('revert', 'ImpersonateController@revert')->name('impersonate.revert');
   # Impersonate route...
   $router->get('{user}', 'ImpersonateController@impersonate')->name('impersonate.impersonate');
});

Auth::routes();

