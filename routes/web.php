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

Route::middleware(['auth'])->prefix('oauth')->group(function () {
  Route::get('list', 'ApiController@index')->name('oauth.tokens');
  Route::get('create/token/{user_id}', 'ApiController@createToken')->name('create.token');
  Route::get('delete/token/{id}', 'ApiController@deleteToken')->name('delete.token');
  Route::get('create-passport-client', 'ApiController@createAccessToken')->name('create.passport.client');
});
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
  Route::get('delete/{id}', 'TermsAndConditionsController@destroy')->name('terms.delete');
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
  Route::post('destroyArr', 'GlobalChargesController@destroyArr')->name('globalcharges.destroyArr');
  Route::put('updateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@updateGlobalChar', 'as' => 'update-global-charge']);
  Route::get('deleteGlobalCharge/{id}', ['uses' => 'GlobalChargesController@destroyGlobalCharges', 'as' => 'delete-global-charge']);
  Route::get('editGlobalCharge/{id}', ['uses' => 'GlobalChargesController@editGlobalChar', 'as' => 'edit-global-charge']);
  Route::get('addGlobalCharge', ['uses' => 'GlobalChargesController@addGlobalChar', 'as' => 'add-global-charge']);
  Route::get('duplicateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@duplicateGlobalCharges', 'as' => 'duplicate-global-charge']);
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
  Route::get('duplicateRate/{id}', ['uses' => 'ContractsController@duplicateRates', 'as' => 'duplicate-rates']);
  Route::get('addLocalCharge/{id}', ['uses' => 'ContractsController@addLocalChar', 'as' => 'add-LocalCharge']);
  Route::post('storeLocalCharge/{id}', ['uses' => 'ContractsController@storeLocalChar', 'as' => 'contracts.storeLocalCharge']);
  Route::get('deleteLocalCharge/{id}', ['uses' => 'ContractsController@destroyLocalCharges', 'as' => 'delete-local-charge']);
  Route::get('duplicateLocalCharge/{id}', ['uses' => 'ContractsController@duplicateLocalChar', 'as' => 'duplicate-local-charge']);
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

Route::prefix('Requests')->group(function () {
    //New Request Importation

  Route::get('SimilarContracts/{id}','NewContractRequestsController@similarcontracts')->name('Similar.Contracts.Request')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('test','NewContractRequestsController@test')->name('RequestImportation.test');

  Route::get('RequestImportation/indexListClient','NewContractRequestsController@indexListClient')->name('RequestImportation.indexListClient')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('RequestImportation/listClient/{id}','NewContractRequestsController@listClient')->name('RequestImportation.listClient')
  ->middleware(['auth','role:administrator|company|subuser']);
  Route::resource('RequestImportation','NewContractRequestsController')->middleware(['auth','role:administrator']);

  Route::get('Requestimporfcl','NewContractRequestsController@LoadViewRequestImporContractFcl')->name('Request.importaion.fcl')
  ->middleware(['auth','role:administrator|company|subuser']);
  Route::get('StatusRquestFCL/{id}','NewContractRequestsController@showStatus')->name('show.status.Request')
  ->middleware(['auth','role:administrator']);
  Route::POST('RequestImportation/two','NewContractRequestsController@store2')->name('RequestImportation.store2')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('RequestStatus','NewContractRequestsController@UpdateStatusRequest')->name('Request.status')
  ->middleware(['auth','role:administrator']);
  Route::get('RequestDestroy/{id}','NewContractRequestsController@destroyRequest')->name('destroy.Request')
  ->middleware(['auth','role:administrator']);
});


Route::prefix('Importation')->group(function () {
    //Importar desde request
  Route::get('RequestProccessFCL/{id}/{selector}/{idrqex}','ImportationController@requestProccess')->name('process.request.fcl')
  ->middleware(['auth','role:administrator']);

    // Importar Contracto
  Route::PUT('UploadFileNewContracts','ImportationController@UploadFileNewContract')->name('Upload.File.New.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('ProcessContractFcl','ImportationController@ProcessContractFcl')->name('process.contract.fcl')
  ->middleware(['auth','role:administrator']);
  Route::get('ProcessContractFclRatSurch','ImportationController@ProcessContractFclRatSurch')->name('process.contract.fcl.Rat.Surch')
  ->middleware(['auth','role:administrator']);
  Route::get('RedirectProcessedInformation/{id}','ImportationController@redirectProcessedInformation')->name('redirect.Processed.Information')
  ->middleware(['auth','role:administrator']);
  Route::get('fcl/rate/{id}/{bo}','ImportationController@FailedRatesDeveloper')->name('Failed.Rates.Developer.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('ImporFcl','ImportationController@LoadViewImporContractFcl')->name('importaion.fcl')
  ->middleware(['auth','role:administrator']);
  Route::get('ValidateCompany/{id}','ImportationController@ValidateCompany')->name('validate.import')
  ->middleware(['auth','role:administrator']);

    // Account FCL
  Route::get('AccountCFCL/','ImportationController@indexAccount')->name('index.Account.import.fcl')
  ->middleware(['auth','role:administrator']);
  Route::get('DestroyAccountcfcl/{id}','ImportationController@DestroyAccount')->name('Destroy.account.cfcl')
  ->middleware(['auth','role:administrator']);
  Route::get('DownloadAccountcfcl/{id}','ImportationController@Download')->name('Download.Account.cfcl')
  ->middleware(['auth','role:administrator']);

    // Rates
  Route::put('UploadFileRates','ImportationController@UploadFileRateForContract')->name('Upload.File.Rates.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('EditRatesGoodForContracts/{id}','ImportationController@EditRatesGood')->name('Edit.Rates.Good.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('EditRatesFailForContracts/{id}','ImportationController@EditRatesFail')->name('Edit.Rates.Fail.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::PUT('CreateRatesFailForContracts/{id}','ImportationController@CreateRates')->name('create.Rates.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('UpdateRatesFailForContracts/{id}','ImportationController@UpdateRatesD')->name('Update.RatesD.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('DestroyRatesFailForContracts/{id}','ImportationController@DestroyRatesF')->name('Destroy.RatesF.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('DestroyRatesGForContracts/{id}','ImportationController@DestroyRatesG')->name('Destroy.RatesG.For.Contracts')
  ->middleware(['auth','role:administrator']);

    // Surcharge
  Route::put('UploadFileSubchargeForContracts','ImportationController@UploadFileSubchargeForContract')->name('Upload.File.Subcharge.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('fcl/surcharge/{id}/{bo}','ImportationController@FailedSurchargeDeveloper')->name('Failed.Surcharge.F.C.D')
  ->middleware(['auth','role:administrator']);
  Route::get('EditSurchargersGoodForContracts/{id}','ImportationController@EditSurchargersGood')->name('Edit.Surchargers.Good.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('EditSurchargersFailForContracts/{id}','ImportationController@EditSurchargersFail')->name('Edit.Surchargers.Fail.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::PUT('CreateSurchargersFailForContracts/{id}','ImportationController@CreateSurchargers')->name('create.Surchargers.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('UpdateSurchargersForContracts/{id}','ImportationController@UpdateSurchargersD')->name('Update.Surchargers.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('DestroySurchargersFForContracts/{id}','ImportationController@DestroySurchargersF')->name('Destroy.SurchargersF.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('DestroySurchargersGForContracts/{id}','ImportationController@DestroySurchargersG')->name('Destroy.SurchargersG.For.Contracts')
  ->middleware(['auth','role:administrator']);

    // Reprocesar
  Route::get('/ReprocesarRates/{id}','ImportationController@ReprocesarRates')->name('Reprocesar.Rates')
  ->middleware(['auth','role:administrator']);
  Route::get('/ReprocesarSurchargers/{id}','ImportationController@ReprocesarSurchargers')->name('Reprocesar.Surchargers')
  ->middleware(['auth','role:administrator']);

    // Datatable Rates Y Surchargers
  Route::get('FailedRatesForContractsDeveloperView/{id}/{ids}','ImportationController@FailedRatesDeveloperLoad')->name('Failed.Rates.Developer.view.For.Contracts')
  ->middleware(['auth','role:administrator']);
  Route::get('FailedSurchargeFCDView/{id}/{ids}','ImportationController@FailSurchargeLoad')->name('Failed.Surcharge.V.F.C')
  ->middleware(['auth','role:administrator']);

    // DownLoad Files
  Route::get('/DownLoadFiles/{id}','ImportationController@DowLoadFiles')->name('DownLoad.Files')->middleware(['auth']);

    // Companies
  Route::Post('/UploadCompany','ImportationController@UploadCompanies')->name('Upload.Company')->middleware(['auth']);
  Route::get('/ViewFCompany','ImportationController@FailedCompnaiesView')->name('view.fail.company')->middleware(['auth']);
  Route::get('/ListFCompany/{id}','ImportationController@FailedCompnaieslist')->name('list.fail.company')->middleware(['auth']);
  Route::get('/DeleteFCompany/{id}','ImportationController@DeleteFailedCompany')->name('delete.fail.company')->middleware(['auth']);
  Route::get('/ShowFCompany/{id}','ImportationController@ShowFailCompany')->name('show.fail.company')->middleware(['auth']);
  Route::get('/UpdateFCompany/{id}','ImportationController@UpdateFailedCompany')->name('update.fail.company')->middleware(['auth']);

    // Contacts
  Route::Post('/UploadContacts','ImportationController@UploadContacts')->name('Upload.Contacts')->middleware(['auth']);
  Route::get('/ViewFContact','ImportationController@FailedContactView')->name('view.fail.contact')->middleware(['auth']);
  Route::get('/ListFContact/{id}','ImportationController@FailedContactlist')->name('list.fail.contact')->middleware(['auth']);
  Route::get('/DeleteFContact/{id}','ImportationController@DeleteFailedContact')->name('delete.fail.contact')->middleware(['auth']);
  Route::get('/ShowFContact/{id}','ImportationController@ShowFailContact')->name('show.fail.contact')->middleware(['auth']);
  Route::get('/UpdateFContact/{id}','ImportationController@UpdateFailedContact')->name('update.fail.contact')->middleware(['auth']);

    // Srucharge for contract
  Route::get('/ProcessImpSurcharge','ImportationController@ProcessSurchargeForContract')->name('process.imp.surcharge')
  ->middleware(['auth','role:administrator']);

    // Test
  Route::get('/testExcelImportation','ImportationController@testExcelImportation')->name('testExcelImportation')->middleware(['auth','role:administrator']);

});
//New Request Importation Lcl
Route::prefix('RequestsLcl')->group(function () {

  Route::get('SimilarContractsLcl/{id}','NewContractRequestLclController@similarcontracts')->name('Similar.Contracts.Request.Lcl')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('RequestImportationLcl/indexListClient','NewContractRequestLclController@indexListClient')->name('RequestImportationLcl.indexListClient')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('RequestImportationLcl/listClient/{id}','NewContractRequestLclController@listClient')->name('RequestImportationLcl.listClient')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::resource('RequestImportationLcl','NewContractRequestLclController')->middleware(['auth','role:administrator']);

  Route::get('StatusRquestLCL/{id}','NewContractRequestLclController@showStatus')->name('show.status.Request.lcl')
  ->middleware(['auth','role:administrator']);    
  Route::POST('RequestImportationLcl/two','NewContractRequestLclController@store2')->name('RequestImportationLcl.store2')
  ->middleware(['auth','role:administrator|company|subuser']);
  Route::get('Requestimporlcl','NewContractRequestLclController@LoadViewRequestImporContractLcl')->name('Request.importaion.lcl')
  ->middleware(['auth','role:administrator|company|subuser']);
  Route::get('RequestLclStatus','NewContractRequestLclController@UpdateStatusRequest')->name('RequestLcl.status')
  ->middleware(['auth','role:administrator']);
  Route::get('RequestLclDestroy/{id}','NewContractRequestLclController@destroyRequest')->name('destroy.RequestLcl')
  ->middleware(['auth','role:administrator']);
});


// Importation LCL 
Route::middleware(['auth','role:administrator'])->prefix('ImportationLCL')->group(function () {

    //Importar desde request
  Route::get('RequestProccessLCL/{id}/{selector}/{idrqex}','ImportationLclController@indexRequest')->name('process.request.lcl')
  ->middleware(['auth','role:administrator']);

  Route::PUT('UploadFileLCL','ImportationLclController@UploadFileNewContract')->name('Upload.File.LCL.New');

  Route::PUT('UploadFileLCL','ImportationLclController@UploadFileNewContract')->name('Upload.File.LCL.New');

  // Account FCL
  Route::get('AccountCLCL/','ImportationLclController@indexAccount')->name('index.Account.import.lcl');
  Route::get('DestroyAccountclcl/{id}','ImportationLclController@DestroyAccount')->name('Destroy.account.clcl');
  Route::get('DownloadAccountclcl/{id}','ImportationLclController@Download')->name('Download.Account.clcl');

  //Rates 
  Route::get('EditRatesFailLcl/{id}','ImportationLclController@EditRatesFail')->name('Edit.Rates.Fail.Lcl');
  Route::PUT('CreateRatesFailLcl/{id}','ImportationLclController@CreateRates')->name('Create.Rates.Lcl');
  Route::get('DestroyRatesFailLcl/{id}','ImportationLclController@DestroyRatesF')->name('Destroy.RatesF.Lcl');
  Route::get('EditRatesGoodLcl/{id}','ImportationLclController@EditRatesGood')->name('Edit.RatesG.Lcl');
  Route::get('UpdateRatesFailLcl/{id}','ImportationLclController@UpdateRatesD')->name('Update.RatesG.Lcl');
  Route::get('DestroyRatesGLcl/{id}','ImportationLclController@DestroyRatesG')->name('Destroy.RatesG.Lcl');
  Route::get('lcl/rates/{id}/{bo}','ImportationLclController@FailedRatesView')->name('Failed.Rates.lcl.view');
  Route::get('lclDT/rates/{id}/{ids}','ImportationLclController@FailedRatesDT')->name('Failed.Rates.Lcl.datatable');
  Route::resource('ImportationLCL','ImportationLclController');
  Route::get('/ReprocesarRatesLcl/{id}','ImportationLclController@reprocessRatesLcl')->name('Reprocesar.Rates.lcl');

});

Route::middleware(['auth'])->prefix('Exportation')->group(function () {
  Route::resource('Exportation','ExportationController');
});

Route::middleware(['auth'])->prefix('Harbors')->group(function () {
  Route::resource('UploadFile','FileHarborsPortsController');
  Route::get('/loadViewAdd','FileHarborsPortsController@loadviewAdd')->name('load.View.Add');
  Route::get('/destroyharbor/{id}','FileHarborsPortsController@destroyharbor')->name('destroy.harbor');
});

Route::middleware(['auth'])->prefix('Countries')->group(function () {
  Route::resource('Countries','CountryController');
  Route::get('/loadViewAdd','CountryController@loadviewAdd')->name('load.View.Add.country');
  Route::get('/destroyharbor/{id}','CountryController@destroycountrie')->name('destroy.countrie');
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
  Route::get('listRate', 'QuoteAutomaticController@listRate')->name('quotes.listRate');
  Route::get('pdf/{quote_id}', 'PdfController@quote')->name('quotes.pdf');
  Route::get('pdf/new/{quote_id}', 'PdfController@quote_2')->name('quotes.pdf.2');
  Route::get('automatic', 'QuoteAutomaticController@automatic')->name('quotes.automatic');
  Route::get('duplicate/{id}', 'QuoteController@duplicate')->name('quotes.duplicate');
  Route::post('send/pdf', 'PdfController@send_pdf_quote')->name('quotes.send_pdf');
  Route::post('test', 'QuoteAutomaticController@test')->name('quotes.test');
  Route::get('terms/{harbor_id}', 'QuoteController@getQuoteTerms')->name('quotes.terms');
  Route::get('terms/{origin_harbor}/{destination_harbor}', 'QuoteController@getQuoteTermsDual')->name('quotes.terms.dual');
  Route::post('update/status/{quote_id}', 'QuoteController@updateStatus')->name('quotes.update.status');
  Route::get('change/status/{id}', 'QuoteController@changeStatus')->name('quotes.change_status');
  Route::get('quoteSchedules/{carrier?}/{orig_port?}/{dest_port?}/{date_pick?}','QuoteController@scheduleManual')->name('quotes.schedule');
  Route::post('store/email', 'QuoteController@storeWithEmail')->name('quotes.store.email');
  Route::post('store/pdf', 'QuoteController@storeWithPdf')->name('quotes.store.pdf');
  Route::get('show/pdf/{id}', 'QuoteController@showWithPdf')->name('quotes.show.pdf');
  Route::get('airports/find', 'QuoteController@searchAirports')->name('quotes.show.airports');
  Route::get('payments/{company_id}', 'QuoteController@getCompanyPayments')->name('quotes.show.payments');
  Route::get('IndexDt', 'QuoteController@LoadDatatableIndex')->name('quotes.index.datatable');
  Route::get('contact/email/{contact_id}', 'QuoteController@getContactEmail')->name('quotes.index.contact.email');
  Route::post('carrier/visibility', ['uses' => 'QuoteController@updateCarrierVisibility', 'as' => 'quotes.carrier.visibility']);
  Route::get('export', 'QuoteController@downloadQuotes')->name('quotes.download');
  // LCL
  Route::post('listRateLcl', 'QuoteAutomaticLclController@index')->name('quotes.listRateLcl');

});
Route::resource('quotes', 'QuoteController')->middleware('auth');

//Quotes V2
Route::middleware(['auth'])->prefix('v2/quotes')->group(function () {
  Route::get('/', 'QuoteV2Controller@index')->name('quotes-v2.index');
  Route::get('/show/{id}', 'QuoteV2Controller@show')->name('quotes-v2.show');
  Route::post('/update/{id}', 'QuoteV2Controller@update')->name('quotes-v2.update');
  Route::post('/charges/update', 'QuoteV2Controller@updateQuoteCharges')->name('quotes-v2.update.charges');
  Route::post('rate/charges/update', 'QuoteV2Controller@updateRateCharges')->name('quotes-v2.update.rate.charges');
  Route::post('lcl/charges/update', 'QuoteV2Controller@updateQuoteChargesLcl')->name('quotes-v2.update.charges.lcl');
  Route::post('/update/payments/{id}', 'QuoteV2Controller@updatePaymentConditions')->name('quotes-v2.update.payments');
  Route::post('/update/terms/{id}', 'QuoteV2Controller@updateTerms')->name('quotes-v2.update.terms');
  Route::post('/update/remarks/{id}', 'QuoteV2Controller@updateRemarks')->name('quotes-v2.update.remarks');
  Route::get('/duplicate/{id}', 'QuoteV2Controller@duplicate')->name('quotes-v2.duplicate');
  Route::get('datatable', 'QuoteV2Controller@LoadDatatableIndex')->name('quotes-v2.index.datatable');
  Route::post('send', 'QuoteV2Controller@send_pdf_quote')->name('quotes-v2.send_pdf');
  Route::get('search', 'QuoteV2Controller@search')->name('quotes-v2.search');
  Route::post('processSearch', 'QuoteV2Controller@processSearch')->name('quotes-v2.processSearch');
  Route::post('/store', 'QuoteV2Controller@store')->name('quotes-v2.store');
  Route::get('/pdf/{quote_id}', 'QuoteV2Controller@pdf')->name('quotes-v2.pdf');
  Route::post('feature/pdf/update', 'QuoteV2Controller@updatePdfFeature')->name('quotes-v2.pdf.update.feature');
  Route::get('delete/rate/{id}', 'QuoteV2Controller@delete')->name('quotes-v2.pdf.delete.rate');
  Route::get('delete/charge/{id}', 'QuoteV2Controller@deleteCharge')->name('quotes-v2.pdf.delete.charge');
  Route::get('lcl/delete/charge/{id}', 'QuoteV2Controller@deleteChargeLclAir')->name('quotes-v2.pdf.delete.charge.lcl');
  Route::post('store/charge', 'QuoteV2Controller@storeCharge')->name('quotes-v2.store.charge');
  Route::post('lcl/store/charge', 'QuoteV2Controller@storeChargeLclAir')->name('quotes-v2.store.charge.lcl');
  Route::post('inland/update', 'QuoteV2Controller@updateInlandCharges')->name('quotes-v2.update.charge.inland');
  Route::post('rates/store', 'QuoteV2Controller@storeRates')->name('quotes-v2.rates.store');
  Route::get('company/companies', 'CompanyController@getCompanies')->name('quotes-v2.companies');
  Route::get('contacts/contact', 'ContactController@getContacts')->name('quotes-v2.contacts');
  Route::get('contacts/contact/{company_id}', 'ContactController@getContactsByCompanyId')->name('quotes-v2.contacts.company');
});

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
//Contracts LCL

Route::middleware(['auth'])->prefix('contractslcl')->group(function () {

  //Contract LCL 
  Route::get('addlcl', 'ContractsLclController@add')->name('contractslcl.add');
  Route::get('deleteContractlcl/{id}', ['uses' => 'ContractsLclController@deleteContract', 'as' => 'contractslcl.delete']);
  Route::get('destroyContractlcl/{id}', ['uses' => 'ContractsLclController@destroyContract', 'as' => 'contractslcl.destroyContract']);


  //Rates 
  Route::get('addRatelcl/{id}', ['uses' => 'ContractsLclController@addRates', 'as' => 'add-rates-lcl']);
  Route::post('storeRatelcl/{id}', ['uses' => 'ContractsLclController@storeRates', 'as' => 'contractslcl.storeRate']);
  Route::get('editRatelcl/{id}', ['uses' => 'ContractsLclController@editRates', 'as' => 'edit-rates-lcl']);
  Route::put('updateRatelcl/{id}', ['uses' => 'ContractsLclController@updateRates', 'as' => 'update-rates-lcl']);
  Route::get('deleteRateslcl/{rate_id}', ['uses' => 'ContractsLclController@deleteRates', 'as' => 'delete-rates-lcl']);
  Route::get('duplicateRatelcl/{id}', ['uses' => 'ContractsLclController@duplicateRates', 'as' => 'duplicate-rates-lcl']);

  // LocalCharges
  Route::get('addLocalChargelcl/{id}', ['uses' => 'ContractsLclController@addLocalChar', 'as' => 'add-LocalCharge-lcl']);
  Route::post('storeLocalChargeLcl/{id}', ['uses' => 'ContractsLclController@storeLocalChar', 'as' => 'contracts.storeLocalChargeLcl']);
  Route::get('editLocalChargeLcl/{id}', ['uses' => 'ContractsLclController@editLocalChar', 'as' => 'edit-local-charge-lcl']);
  Route::put('updateLocalChargeLcl/{id}', ['uses' => 'ContractsLclController@updateLocalChar', 'as' => 'update-local-charge-lcl']);
  Route::get('deleteLocalChargeLcl/{id}', ['uses' => 'ContractsLclController@deleteLocalCharges', 'as' => 'delete-local-charge-lcl']);
  Route::get('duplicateLocalChargeLcl/{id}', ['uses' => 'ContractsLclController@duplicateLocalCharges', 'as' => 'duplicate-local-charge-lcl']);

  // DATATABLES LCL
  Route::get('eloquent/object-contractlclG', 'ContractsLclController@contractlclTable')->name('contractlcl.tableG');
  Route::get('eloquent/object-contractlcl', 'ContractsLclController@contractLclRates')->name('contractlcl.table');
  Route::get('eloquent/object-ratelcl/{id}', 'ContractsLclController@dataRatesLcl')->name('ratelcl.table');
  Route::get('eloquent/object-datalcl/{id}', 'ContractsLclController@dataLcl')->name('localcharlcl.table');


});

Route::resource('contractslcl', 'ContractsLclController')->middleware('auth');

// REQUEST IMPORTATION GLOBALCHARGE FCL
Route::prefix('RequestsGlobalchargers')->group(function () {

  //Route::resource('RequestsGlobalchargersFcl','NewGlobalchargeRequestControllerFcl')->middleware(['auth','role:administrator']);
  Route::get('RequestsGlobalchargersFcl/indexListClient','NewGlobalchargeRequestControllerFcl@indexListClient')->name('RequestsGlobalchargersFcl.indexListClient')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('RequestsGlobalchargersFcl/listClient/{id}','NewGlobalchargeRequestControllerFcl@listClient')->name('RequestsGlobalchargersFcl.listClient')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('StatusRquestGC/{id}','NewGlobalchargeRequestControllerFcl@showStatus')->name('show.status.Request.gc')
  ->middleware(['auth','role:administrator']);    

  Route::get('RequestsGlobalchargersFcl/create/','NewGlobalchargeRequestControllerFcl@create')->name('RequestsGlobalchargersFcl.create')
  ->middleware(['auth','role:administrator|company|subuser']);

  Route::get('RequestsGlobalchargersFcl/create2/','NewGlobalchargeRequestControllerFcl@create2')->name('RequestsGlobalchargersFcl.create2')
  ->middleware(['auth','role:administrator']);

  Route::POST('RequestsGlobalchargersFcl/','NewGlobalchargeRequestControllerFcl@store')->name('RequestsGlobalchargersFcl.store')
  ->middleware(['auth','role:administrator|company|subuser']);
  Route::GET('RequestsGlobalchargersFcl/','NewGlobalchargeRequestControllerFcl@index')->name('RequestsGlobalchargersFcl.index')
  ->middleware(['auth','role:administrator']);
  Route::GET('RequestsGlobalchargersFcl/{id}','NewGlobalchargeRequestControllerFcl@show')->name('RequestsGlobalchargersFcl.show')
  ->middleware(['auth','role:administrator']);
  Route::PUT('RequestsGlobalchargersFcl/{id}','NewGlobalchargeRequestControllerFcl@update')->name('RequestsGlobalchargersFcl.update')
  ->middleware(['auth','role:administrator']);
  Route::DELETE('RequestsGlobalchargersFcl/{id}','NewGlobalchargeRequestControllerFcl@destroy')->name('RequestsGlobalchargersFcl.destroy')
  ->middleware(['auth','role:administrator']);
  Route::GET('RequestsGlobalchargersFcl/{id}/edit','NewGlobalchargeRequestControllerFcl@edit')->name('RequestsGlobalchargersFcl.edit')
  ->middleware(['auth','role:administrator']);

  Route::get('RGlobalCDestroy/{id}','NewGlobalchargeRequestControllerFcl@destroyRequest')->name('destroy.GlobalC')
  ->middleware(['auth','role:administrator']);
  Route::get('RequestGCStatus','NewGlobalchargeRequestControllerFcl@UpdateStatusRequest')->name('Request.GlobalC.status')
  ->middleware(['auth','role:administrator']);
});

// IMPORTATION GLOBALCHARGE FCL
Route::middleware(['auth','role:administrator'])->prefix('ImportationGlobalchargesFcl')->group(function () {

  Route::get('AccountGC/','ImportationGlobachargersFclController@indexAccount')->name('index.Account.import.gc');
    //Importar desde request
  Route::get('RequestProccessGC/{id}','ImportationGlobachargersFclController@indexRequest')->name('process.request.gc')
  ->middleware(['auth','role:administrator']);

  Route::PUT('UploadFileGlobalchargesFcl','ImportationGlobachargersFclController@UploadFileNewContract')->name('Upload.File.Globalcharges.Fcl');
  Route::get('DeleteAccountsGlobalchargesFcl/{id}/{select}','ImportationGlobachargersFclController@deleteAccounts')->name('delete.Accounts.Globalcharges.Fcl'); 
  Route::get('indexTwo','ImportationGlobachargersFclController@indexTwo')->name('indextwo.globalcharge.fcl');
  Route::get('FailedGlobalchargers/{id}/{tab}','ImportationGlobachargersFclController@showviewfailedandgood')->name('showview.globalcharge.fcl');
  Route::resource('ImportationGlobalchargeFcl','ImportationGlobachargersFclController');

  //Importar desde request
  Route::get('RequestProccessGC/{id}','ImportationGlobachargersFclController@indexRequest')->name('process.request.gc')
  ->middleware(['auth','role:administrator']);

  Route::PUT('UploadFileGlobalchargesFcl','ImportationGlobachargersFclController@UploadFileNewContract')->name('Upload.File.Globalcharges.Fcl');
  Route::get('DeleteAccountsGlobalchargesFcl/{id}/{select}','ImportationGlobachargersFclController@deleteAccounts')->name('delete.Accounts.Globalcharges.Fcl'); 
  Route::get('indexTwo','ImportationGlobachargersFclController@indexTwo')->name('indextwo.globalcharge.fcl');
  Route::get('FailedGlobalchargers/{id}/{tab}','ImportationGlobachargersFclController@showviewfailedandgood')->name('showview.globalcharge.fcl');
  Route::resource('ImportationGlobalchargeFcl','ImportationGlobachargersFclController');

  //Account

  Route::get('DownloadAccountgcfcl/{id}','ImportationGlobachargersFclController@Download')->name('Download.Account.gcfcl');

  //failed and good
  Route::get('/FailglobalchargeLoad/{id}/{selector}','ImportationGlobachargersFclController@FailglobalchargeLoad')->name('Fail.Load.globalcharge.fcl');
  Route::get('DestroyglobalchargeGoodFcl/{id}','ImportationGlobachargersFclController@DestroyGlobalchargeG')->name('Destroy.globalcharge.good.fcl');
  Route::get('DestroyglobalchargeFailFcl/{id}','ImportationGlobachargersFclController@DestroyGlobalchargeF')->name('Destroy.globalcharge.Fail.fcl');

  Route::get('editGlobalChargeMDFCL/{id}','ImportationGlobachargersFclController@editGlobalChar')->name('edit.globalcharge.modal.fcl');
  Route::put('updateGlobalChargeMDFCL/{id}','ImportationGlobachargersFclController@updateGlobalChar')->name('update.globalcharge.modal.fcl');
  Route::get('saveTofailToGoddGCFCL/{id}','ImportationGlobachargersFclController@saveFailToGood')->name('save.fail.good.globalcharge.fcl');

    // Reprocesar
  Route::get('/ReprocesarGlobalchargers/{id}','ImportationGlobachargersFclController@ReprocesarGlobalchargers')->name('Reprocesar.globalcharge.fcl');

  Route::get('/testExcelImportation','ImportationGlobachargersFclController@testExcelImportation')->name('testExcelImportation.GC')->middleware(['auth','role:administrator']);

});
// GLOBAL CHARGES LCL 
Route::middleware(['auth'])->prefix('globalchargeslcl')->group(function () {
  Route::post('destroyArr', 'GlobalChargesLclController@destroyArr')->name('globalchargeslcl.destroyArr');
  Route::put('updateGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@updateGlobalChar', 'as' => 'update-global-charge-lcl']);
  Route::get('deleteGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@destroyGlobalCharges', 'as' => 'delete-global-charge-lcl']);
  Route::get('editGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@editGlobalChar', 'as' => 'edit-global-charge-lcl']);
  Route::get('addGlobalChargeLcl', ['uses' => 'GlobalChargesLclController@addGlobalChar', 'as' => 'add-global-charge-lcl']);
  Route::get('duplicateGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@duplicateGlobalCharges', 'as' => 'duplicate-global-charge-lcl']);
});
Route::resource('globalchargeslcl', 'GlobalChargesLclController')->middleware('auth');

Route::middleware(['auth'])->prefix('Region')->group(function () {
  Route::resource('Region','RegionController');
  Route::get('/LoadViewRegion','RegionController@LoadViewAdd')->name('load.View.add.region');
});

Route::resource('search', 'SearchController')->middleware('auth');

Auth::routes();

