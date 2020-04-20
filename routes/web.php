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
	if(\Session::has('impersonate') || env('APP_VIEW') == 'local' 
	   || env('APP_VIEW') == 'prod' || env('APP_VIEW') == 'dev'){
		return redirect()->route('quotes-v2.search');
	} elseif(env('APP_VIEW') == 'operaciones') {
		return redirect()->route('RequestFcl.index');
	}

});

Route::get('/home', function () {
	if(\Session::has('impersonate') || env('APP_VIEW') == 'local' 
	   || env('APP_VIEW') == 'prod' || env('APP_VIEW') == 'dev'){
		return redirect()->route('quotes-v2.search');
	} elseif(env('APP_VIEW') == 'operaciones') {
		return redirect()->route('RequestImportation.index');
	}
});

Route::get('verify/{token}', 'Auth\RegisterController@verifyUser');

Route::middleware(['auth'])->prefix('oauth')->group(function () {
	Route::get('list', 'ApiController@index')->name('oauth.tokens');
	Route::get('create/token/{user_id}', 'ApiController@createToken')->name('create.token');
	Route::get('delete/token/{id}', 'ApiController@deleteToken')->name('delete.token');
	Route::get('create-passport-client', 'ApiController@createAccessToken')->name('create.passport.client');
});

Route::middleware(['auth'])->prefix('api')->group(function () {
	Route::get('settings', 'ApiIntegrationController@index')->name('api.settings');
	Route::get('enable', 'ApiIntegrationController@enable')->name('api.enable');
	Route::get('store/key', 'ApiIntegrationController@store')->name('api.store');
	Route::get('get/companies', 'ApiIntegrationController@getCompanies')->name('api.companies');
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
	Route::get('verify/{user_id}', ['as' => 'users.verify', 'uses' => 'UsersController@verify']);
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

Route::prefix('globalcharges')->group(function () {
	Route::get('add', 'GlobalChargesController@add')->name('globalcharges.add')->middleware(['auth']);
	Route::post('destroyArr', 'GlobalChargesController@destroyArr')->name('globalcharges.destroyArr')->middleware(['auth']);
	Route::put('updateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@updateGlobalChar', 'as' => 'update-global-charge'])->middleware(['auth']);
	Route::get('deleteGlobalCharge/{id}', ['uses' => 'GlobalChargesController@destroyGlobalCharges', 'as' => 'delete-global-charge'])->middleware(['auth']);
	Route::get('editGlobalCharge/{id}', ['uses' => 'GlobalChargesController@editGlobalChar', 'as' => 'edit-global-charge'])->middleware(['auth']);
	Route::get('addGlobalCharge', ['uses' => 'GlobalChargesController@addGlobalChar', 'as' => 'add-global-charge'])->middleware(['auth']);
	Route::get('duplicateGlobalCharge/{id}', ['uses' => 'GlobalChargesController@duplicateGlobalCharges', 'as' => 'duplicate-global-charge'])->middleware(['auth']);

	// CRUD Global Charge Administrator FCL -------------------------------------------------------------------------------------------------

	Route::get('indexAdm','GlobalChargesController@indexAdm')->name('gcadm.index')->middleware(['auth','role:administrator|data_entry']);
	Route::get('createAdm','GlobalChargesController@createAdm_proc')->name('gcadm.create')->middleware(['auth','role:administrator|data_entry']);
	Route::post('addAdm','GlobalChargesController@addAdm')->name('gcadm.add')->middleware(['auth','role:administrator|data_entry']);
	Route::get('typeChargeAdm/{id}','GlobalChargesController@typeChargeAdm')->name('gcadm.typeCharge')->middleware(['auth','role:administrator|data_entry']);
	Route::post('StoreAdm','GlobalChargesController@storeAdm')->name('gcadm.store')->middleware(['auth','role:administrator|data_entry']);
	Route::post('ShowAdm/{id}','GlobalChargesController@showAdm')->name('gcadm.show')->middleware(['auth','role:administrator|data_entry']);
	Route::PUT('UpdateAdm/{id}','GlobalChargesController@updateAdm')->name('gcadm.update')->middleware(['auth','role:administrator|data_entry']);
	Route::post('DupicateAdm/{id}','GlobalChargesController@dupicateAdm')->name('gcadm.dupicate')->middleware(['auth','role:administrator|data_entry']);
	Route::POST('ArrDupicateAdm/','GlobalChargesController@dupicateArrAdm')->name('gcadm.dupicate.Array')->middleware(['auth','role:administrator|data_entry']);
	Route::POST('StoreArrayDupicateAdm/','GlobalChargesController@storeArrayAdm')->name('gcadm.store.array')->middleware(['auth','role:administrator|data_entry']);
	Route::POST('EditDateAdm','GlobalChargesController@editDateArrAdm')->name('gcadm.edit.dates.Array')->middleware(['auth','role:administrator|data_entry']);
	Route::POST('ArrUpdateDateAdm/','GlobalChargesController@updateDateArrAdm')->name('gcadm.update.dates.Array')->middleware(['auth','role:administrator|data_entry']);
	Route::POST('loadSelectForRegion/','GlobalChargesController@loadSelectForRegion')->name('gcadm.load.select.region')->middleware(['auth','role:administrator|data_entry']);
});
Route::resource('globalcharges', 'GlobalChargesController')->middleware('auth');

/************************************************************
*               Global Charges Api Routes                   *
*************************************************************/ 
Route::resource('globalchargesapi', 'GlobalChargesApiController')->middleware(['auth', 'role:administrator|data_entry']);

Route::post('globalchargesapi/destroyArr', 'GlobalChargesApiController@destroyArr')->name('globalchargesapi.destroyArr')->middleware(['auth', 'role:administrator|data_entry']);

/************************************************************
*              End Global Charges Api Routes                   *
*************************************************************/ 

Route::middleware(['auth'])->prefix('contracts')->group(function () {
	//Route::get('add', 'ContractsController@add')->name('contracts.add');
	Route::get('ShowContractEdit/{id}', 'ContractsController@showContractRequest')->name('show.contract.edit');
	Route::put('UpdContractEdit/{id}', 'ContractsController@updateContractRequest')->name('update.contract.edit');
	Route::get('addT', 'ContractsController@add')->name('contracts.add');
	Route::get('msg/{id}', 'ContractsController@destroymsg')->name('contracts.msg');
	Route::get('delete-rates/{rate_id}', ['uses' => 'ContractsController@destroyRates', 'as' => 'delete-rates']);
	Route::get('editLocalCharge/{id}', ['uses' => 'ContractsController@editLocalChar', 'as' => 'edit-local-charge']);
	Route::put('updateLocalCharge/{id}', ['uses' => 'ContractsController@updateLocalChar', 'as' => 'update-local-charge']);
	Route::get('addRate/{id}', ['uses' => 'ContractsController@addRates', 'as' => 'add-rates']);
	Route::post('storeRate/{id}', ['uses' => 'ContractsController@storeRates', 'as' => 'contracts.storeRate']);
	Route::post('storeMedia/', ['uses' => 'ContractsController@storeMedia', 'as' => 'contracts.storeMedia']);
	Route::get('editRate/{id}', ['uses' => 'ContractsController@editRates', 'as' => 'edit-rates']);
	Route::put('updateRate/{id}', ['uses' => 'ContractsController@updateRates', 'as' => 'update-rates']);
	Route::get('duplicateRate/{id}', ['uses' => 'ContractsController@duplicateRates', 'as' => 'duplicate-rates']);
	Route::get('addLocalCharge/{id}', ['uses' => 'ContractsController@addLocalChar', 'as' => 'add-LocalCharge']);
	Route::post('storeLocalCharge/{id}', ['uses' => 'ContractsController@storeLocalChar', 'as' => 'contracts.storeLocalCharge']);
	Route::get('deleteLocalCharge/{id}', ['uses' => 'ContractsController@destroyLocalCharges', 'as' => 'delete-local-charge']);
	Route::get('duplicateLocalCharge/{id}', ['uses' => 'ContractsController@duplicateLocalChar', 'as' => 'duplicate-local-charge']);
	Route::get('deleteContract/{id}', ['uses' => 'ContractsController@deleteContract', 'as' => 'contracts.delete']);
	Route::get('destroyContract/{id}', ['uses' => 'ContractsController@destroyContract', 'as' => 'contracts.destroyContract']);
	Route::get('excel/{id}', 'ContractsController@getMediaSimple')->name('contracts.excel');
	Route::get('excelzip/{id}', 'ContractsController@getMediaAll')->name('contracts.excelZip');
	Route::get('excel-delete/{id}/{id_contract}', ['uses' => 'ContractsController@deleteMedia', 'as' => 'contracts.exceldelete']);

	//----- developer

	Route::get('FailRatesSurchrgesForNewContracts/{id}','ContractsController@failRatesSurchrgesForNewContracts')->name('Fail.Rates.Surchrges.For.New.Contracts');

	// DATATABLES

	Route::get('eloquent/object-data/{id}', 'ContractsController@data')->name('localchar.table');
	Route::get('eloquent/object-rate/{id}', 'ContractsController@dataRates')->name('rate.table');
	Route::get('eloquent/object-contract', 'ContractsController@contractRates')->name('contract.table');
	Route::get('eloquent/object-contractG', 'ContractsController@contractTable')->name('contract.tableG');

	// Duplicated contracts
	Route::get('duplicated/contract-fcl/{id}', 'ContractsController@duplicatedContractShow')->name('contract.duplicated');
	Route::get('selectRequestFcl', 'ContractsController@selectRequest')->name('select.request.fcl.dp');
	Route::post('Store-duplicated/contract-fcl/{id}', 'ContractsController@duplicatedContractStore')->name('contract.duplicated.store');
	Route::post('Store-duplicated-FromRq/contract-fcl/{id}', 'ContractsController@duplicatedContractFromRequestStore')->name('contract.duplicated.from.request.store');
	Route::get('duplicatedOC/contract-fcl/{id}/{request_id}', 'ContractsController@duplicatedContractOtherCompanyShow')->name('contract.duplicated.other.company')->middleware(['auth','role:administrator|data_entry']);

});

Route::prefix('Requests')->group(function () {
	//New Request Importation

	Route::get('SimilarContracts/{id}','NewContractRequestsController@similarcontracts')->name('Similar.Contracts.Request')
		->middleware(['auth','role:administrator|company|subuser|data_entry']);

	Route::get('test','NewContractRequestsController@test')->name('RequestImportation.test');


	Route::resource('RequestImportation','NewContractRequestsController')->middleware(['auth','role:administrator|data_entry']);

	Route::get('Requestimporfcl','NewContractRequestsController@LoadViewRequestImporContractFcl')->name('Request.importaion.fcl')
		->middleware(['auth','role:administrator|company|subuser|data_entry']);
	Route::get('StatusRquestFCL/{id}','NewContractRequestsController@showStatus')->name('show.status.Request')
		->middleware(['auth','role:administrator|data_entry']);
	Route::POST('RequestImportation/two','NewContractRequestsController@store2')->name('RequestImportation.store2')
		->middleware(['auth','role:administrator|company|subuser|data_entry']);

	Route::get('RequestStatus','NewContractRequestsController@UpdateStatusRequest')->name('Request.status')
		->middleware(['auth','role:administrator|data_entry']);

	Route::get('getdataRequest/{id}','NewContractRequestsController@getdataRequest')->name('get.request.fcl')
		->middleware(['auth','role:administrator|data_entry']);

	Route::get('RequestDestroy/{id}','NewContractRequestsController@destroyRequest')->name('destroy.Request')
		->middleware(['auth','role:administrator|data_entry']);
	Route::post('RequestExport/','NewContractRequestsController@export')->name('export.Request')
		->middleware(['auth','role:administrator|data_entry']);
});


Route::prefix('Importation')->group(function () {

	//Importar desde request
	Route::get('RequestProccessFCL/{id}/{selector}/{idrqex}','ImportationController@requestProccess')->name('process.request.fcl')
		->middleware(['auth','role:administrator|data_entry']);

	// Importar Contracto
	Route::POST('UploadFileNewContracts','ImportationController@UploadFileNewContract')->name('Upload.File.New.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('ProcessContractFcl','ImportationController@ProcessContractFcl')->name('process.contract.fcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('ProcessContractFclRatSurch','ImportationController@ProcessContractFclRatSurch')->name('process.contract.fcl.Rat.Surch')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('RedirectProcessedInformation/{id}','ImportationController@redirectProcessedInformation')->name('redirect.Processed.Information')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('fcl/rate/{id}/{bo}','ImportationController@FailedRatesDeveloper')->name('Failed.Rates.Developer.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('ImporFcl','ImportationController@LoadViewImporContractFcl')->name('importaion.fcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('ValidateCompany/{id}','ImportationController@ValidateCompany')->name('validate.import')
		->middleware(['auth','role:administrator|data_entry']);

	// Account FCL
	Route::get('AccountCFCL/','ImportationController@indexAccount')->name('index.Account.import.fcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('DestroyAccountcfcl/{id}','ImportationController@DestroyAccount')->name('Destroy.account.cfcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('DownloadAccountcfcl/{id}','ImportationController@Download')->name('Download.Account.cfcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('ShowRqDpAccountcfcl/{id}','ImportationController@ShowRequestDp')->name('show.request.dp.cfcl')
		->middleware(['auth','role:administrator|data_entry']);

	// Rates
	Route::put('UploadFileRates','ImportationController@UploadFileRateForContract')->name('Upload.File.Rates.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('EditRatesGoodForContracts/{id}','ImportationController@EditRatesGood')->name('Edit.Rates.Good.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('EditRatesFailForContracts/{id}','ImportationController@EditRatesFail')->name('Edit.Rates.Fail.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::PUT('CreateRatesFailForContracts/{id}','ImportationController@CreateRates')->name('create.Rates.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('UpdateRatesFailForContracts/{id}','ImportationController@UpdateRatesD')->name('Update.RatesD.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('DestroyRatesFailForContracts/{id}','ImportationController@DestroyRatesF')->name('Destroy.RatesF.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('DestroyRatesGForContracts/{id}','ImportationController@DestroyRatesG')->name('Destroy.RatesG.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);

	// Surcharge
	Route::put('UploadFileSubchargeForContracts','ImportationController@UploadFileSubchargeForContract')->name('Upload.File.Subcharge.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('fcl/surcharge/{id}/{bo}','ImportationController@FailedSurchargeDeveloper')->name('Failed.Surcharge.F.C.D')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('EditSurchargersGoodForContracts/{id}','ImportationController@EditSurchargersGood')->name('Edit.Surchargers.Good.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('EditSurchargersFailForContracts/{id}','ImportationController@EditSurchargersFail')->name('Edit.Surchargers.Fail.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::PUT('CreateSurchargersFailForContracts/{id}','ImportationController@CreateSurchargers')->name('create.Surchargers.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('UpdateSurchargersForContracts/{id}','ImportationController@UpdateSurchargersD')->name('Update.Surchargers.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('DestroySurchargersFForContracts/{id}','ImportationController@DestroySurchargersF')->name('Destroy.SurchargersF.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('DestroySurchargersGForContracts/{id}','ImportationController@DestroySurchargersG')->name('Destroy.SurchargersG.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);

	// Reprocesar
	Route::get('/ReprocesarRates/{id}','ImportationController@ReprocesarRates')->name('Reprocesar.Rates')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('/ReprocesarSurchargers/{id}','ImportationController@ReprocesarSurchargers')->name('Reprocesar.Surchargers')
		->middleware(['auth','role:administrator|data_entry']);

	// Datatable Rates Y Surchargers
	Route::get('FailedRatesForContractsDeveloperView/{id}/{ids}','ImportationController@FailedRatesDeveloperLoad')->name('Failed.Rates.Developer.view.For.Contracts')
		->middleware(['auth','role:administrator|data_entry']);
	Route::post('StoreMultFailRatesFCL/','ImportationController@StoreFailRatesMultiples')->name('store.Multiples.Rates.Fcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::post('EditMultFailRatesFCL/','ImportationController@EdicionRatesMultiples')->name('Edicion.Multiples.Rates.Fcl')
		->middleware(['auth','role:administrator|data_entry']);
	//editar rates fallidos por detalles y con opcion multiple


	Route::post('ShMulRatesFaByFCL/','ImportationController@showRatesMultiplesPorDetalles')->name('Show.Multiples.Rates.por.detalles.Fcl')
		->middleware(['auth','role:administrator|data_entry']);

	Route::post('LoMulRatesFaByFCL/','ImportationController@loadArrayEditMult')->name('load.arr.Rates.por.detalles.Fcl')
		->middleware(['auth','role:administrator|data_entry']);

	Route::post('StorMulRatesFaByFCL/','ImportationController@StoreFailRatesMultiplesByDetalls')->name('store.multi.rates.fails')
		->middleware(['auth','role:administrator|data_entry']);

	Route::get('FailedSurchargeFCDView/{id}/{ids}','ImportationController@FailSurchargeLoad')->name('Failed.Surcharge.V.F.C')
		->middleware(['auth','role:administrator|data_entry']);

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
	Route::post('/storeMediaIFCL','ImportationController@storeMedia')->name('importation.storeMedia.fcl')
		->middleware(['auth','role:administrator|data_entry']);

	// Test
	Route::get('/testExcelImportation','ImportationController@testExcelImportation')->name('testExcelImportation')->middleware(['auth','role:administrator|data_entry']);
    
    // Test
	Route::get('/testExcelImportation','ImportationController@testExcelImportation')->name('testExcelImportation')->middleware(['auth','role:administrator|data_entry']);

});
//New Request Importation Lcl
Route::prefix('RequestsLcl')->group(function () {

	Route::get('SimilarContractsLcl/{id}','NewContractRequestLclController@similarcontracts')->name('Similar.Contracts.Request.Lcl')
		->middleware(['auth','role:administrator|company|subuser']);

	Route::get('RequestImportationLcl/indexListClient','NewContractRequestLclController@indexListClient')->name('RequestImportationLcl.indexListClient')
		->middleware(['auth','role:administrator|company|subuser|data_entry']);

	Route::get('RequestImportationLcl/listClient/{id}','NewContractRequestLclController@listClient')->name('RequestImportationLcl.listClient')
		->middleware(['auth','role:administrator|company|subuser|data_entry']);

	Route::resource('RequestImportationLcl','NewContractRequestLclController')->middleware(['auth','role:administrator|data_entry']);

	Route::get('StatusRquestLCL/{id}','NewContractRequestLclController@showStatus')->name('show.status.Request.lcl')
		->middleware(['auth','role:administrator|data_entry']);    
	Route::POST('RequestImportationLcl/two','NewContractRequestLclController@store2')->name('RequestImportationLcl.store2')
		->middleware(['auth','role:administrator|company|subuser|data_entry']);
	Route::get('Requestimporlcl','NewContractRequestLclController@LoadViewRequestImporContractLcl')->name('Request.importaion.lcl')
		->middleware(['auth','role:administrator|company|subuser|data_entry']);
	Route::get('RequestLclStatus','NewContractRequestLclController@UpdateStatusRequest')->name('RequestLcl.status')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('RequestLclDestroy/{id}','NewContractRequestLclController@destroyRequest')->name('destroy.RequestLcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::post('RequestLclExport/','NewContractRequestLclController@export')->name('export.RequestLcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('testLcl/','NewContractRequestLclController@test')->name('test.RequestLcl')
		->middleware(['auth','role:administrator|data_entry']);
});


// Importation LCL 
Route::middleware(['auth','role:administrator|data_entry'])->prefix('ImportationLCL')->group(function () {

	//Importar desde request
	Route::get('RequestProccessLCL/{id}/{selector}/{idrqex}','ImportationLclController@indexRequest')->name('process.request.lcl')
		->middleware(['auth','role:administrator|data_entry']);

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

	Route::post('EditMultFailRatesLCL/','ImportationLclController@EdicionRatesMultiples')->name('Edicion.Multiples.Rates.Lcl')
		->middleware(['auth','role:administrator|data_entry']);

	Route::post('StoreMultFailRatesLCL/','ImportationLclController@StoreFailRatesMultiples')->name('store.Multiples.Rates.Lcl')
		->middleware(['auth','role:administrator|data_entry']);
	//editar rates fallidos por detalles y con opcion multiple
	Route::post('ShMulRatesFaByLCL/','ImportationLclController@showRatesMultiplesPorDetalles')->name('Show.Multiples.Rates.por.detalles.Lcl')
		->middleware(['auth','role:administrator|data_entry']);

	Route::post('LoMulRatesFaByLCL/','ImportationLclController@loadArrayEditMult')->name('load.arr.Rates.por.detalles.lcl')
		->middleware(['auth','role:administrator|data_entry']);

	Route::post('StorMulRatesFaByLCL/','ImportationLclController@StoreFailRatesMultiplesByDetalls')->name('store.multi.rates.fails.lcl')
		->middleware(['auth','role:administrator|data_entry']);
});

Route::middleware(['auth'])->prefix('Exportation')->group(function () {
	Route::resource('Exportation','ExportationController');
});

// Harbors
Route::middleware(['auth','role:administrator|data_entry'])->prefix('Harbors')->group(function () {
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
	Route::get('api', 'CompanyController@apiCompanies')->name('companies.api');

});
Route::resource('companies', 'CompanyController')->middleware('auth');

//Prices
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

	Route::get('delete/{id}', 'QuoteController@destroy')->name('quotes.destroy');
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
	Route::get('/show2/{id}', function(){
        return view('quotesv2.show2');
    });
	Route::get('delete/{id}', 'QuoteV2Controller@destroy')->name('quotes-v2.destroy');
	Route::post('/update/{id}', 'QuoteV2Controller@update')->name('quotes-v2.update');
	Route::post('/charges/update', 'QuoteV2Controller@updateQuoteCharges')->name('quotes-v2.update.charges');
	Route::post('/info/update', 'QuoteV2Controller@updateQuoteInfo')->name('quotes-v2.update.info');
	Route::post('rate/charges/update', 'QuoteV2Controller@updateRateCharges')->name('quotes-v2.update.rate.charges');
	Route::post('lcl/charges/update', 'QuoteV2Controller@updateQuoteChargesLcl')->name('quotes-v2.update.charges.lcl');
	Route::post('/update/payments/{id}', 'QuoteV2Controller@updatePaymentConditions')->name('quotes-v2.update.payments');
	Route::post('/update/terms/{id}', 'QuoteV2Controller@updateTerms')->name('quotes-v2.update.terms');
	Route::post('/update/remarks/{id}', 'QuoteV2Controller@updateRemarks')->name('quotes-v2.update.remarks');
	Route::get('/duplicate/{id}', 'QuoteV2Controller@duplicate')->name('quotes-v2.duplicate');
	Route::get('datatable', 'QuoteV2Controller@LoadDatatableIndex')->name('quotes-v2.index.datatable');
	Route::post('send', 'PdfV2Controller@send_pdf_quote')->name('quotes-v2.send_pdf');
	Route::post('send/lcl', 'PdfV2Controller@send_pdf_quote_lcl')->name('quotes-v2.send_pdf_lcl');
	Route::post('send/air', 'PdfV2Controller@send_pdf_quote_air')->name('quotes-v2.send_pdf_air');
	Route::get('search', 'QuoteV2Controller@search')->name('quotes-v2.search');
	Route::post('processSearch', 'QuoteV2Controller@processSearch')->name('quotes-v2.processSearch');
	Route::post('/store', 'QuoteV2Controller@store')->name('quotes-v2.store');
	Route::post('/storeLCL', 'QuoteV2Controller@storeLCL')->name('quotes-v2.storeLCL');
	Route::get('delete/rate/{id}', 'QuoteV2Controller@delete')->name('quotes-v2.pdf.delete.rate');
	Route::get('delete/charge/{id}', 'QuoteV2Controller@deleteCharge')->name('quotes-v2.pdf.delete.charge');
	Route::get('lcl/delete/charge/{id}', 'QuoteV2Controller@deleteChargeLclAir')->name('quotes-v2.pdf.delete.charge.lcl');
	Route::get('delete/inland/{id}', 'QuoteV2Controller@deleteInland')->name('quotes-v2.pdf.delete.inland');
	Route::post('store/charge', 'QuoteV2Controller@storeCharge')->name('quotes-v2.store.charge');
	Route::post('store/sale/charge', 'SaleTermV2Controller@storeSaleCharge')->name('quotes-v2.store.sale.charge');
	Route::post('lcl/store/charge', 'QuoteV2Controller@storeChargeLclAir')->name('quotes-v2.store.charge.lcl');
	Route::post('lcl/inland/charge/update', 'QuoteV2Controller@updateInlandChargeLcl')->name('quotes-v2.update.inland.charge.lcl');
	Route::post('inland/update', 'QuoteV2Controller@updateInlandCharges')->name('quotes-v2.update.charge.inland');
	Route::post('rates/store', 'QuoteV2Controller@storeRates')->name('quotes-v2.rates.store');
	Route::get('rates/edit/{id}', 'QuoteV2Controller@editRates')->name('quotes-v2.rates.edit');
	Route::post('rates/update/{id}', 'QuoteV2Controller@updateRates')->name('quotes-v2.rates.update');
	Route::get('inlands/edit/{id}', 'QuoteV2Controller@editInlands')->name('quotes-v2.inlands.edit');
	Route::get('lcl/inlands/edit/{id}', 'QuoteV2Controller@editInlandsLcl')->name('quotes-v2.inlands.lcl.edit');
	Route::post('inlands/update/{id}', 'QuoteV2Controller@updateInlands')->name('quotes-v2.inlands.update');
	Route::post('inlands/store', 'QuoteV2Controller@storeInlands')->name('quotes-v2.inlands.store');
	Route::get('html/{quote_id}', 'QuoteV2Controller@html')->name('quotes-v2.html');
	Route::get('excel/{id}/{id2}/{id3}', 'QuoteV2Controller@excelDownload')->name('quotes-v2.excel');
	Route::get('excelLcl/{id2}/{id3}', 'QuoteV2Controller@excelDownloadLCL')->name('quotes-v2.excel-lcl');
	Route::get('export', 'QuoteV2Controller@downloadQuotes')->name('quotes-v2.download');
	//Sale terms
	Route::post('store/saleterm', 'SaleTermV2Controller@store')->name('quotes-v2.saleterm.store');
	Route::post('sale/charges/update', 'SaleTermV2Controller@updateSaleCharges')->name('quotes-v2.saleterm.update.charges');
	Route::get('sale/edit/{sale_id}', 'SaleTermV2Controller@editSaleTerm')->name('quotes-v2.saleterm.edit');
	Route::post('sale/update', 'SaleTermV2Controller@updateSaleTerm')->name('quotes-v2.saleterm.update');
	Route::get('delete/saleterm/{id}', 'SaleTermV2Controller@destroy')->name('quotes-v2.delete.saleterm');
	Route::get('delete/saleterm/charge/{id}', 'SaleTermV2Controller@destroyCharge')->name('quotes-v2.delete.saleterm.charge');
	//LCL 
	Route::post('processSearchLCL', 'QuoteV2Controller@processSearchLCL')->name('quotes-v2.processSearchLCL');
	//PDF
	Route::get('/pdf/{quote_id}', 'PdfV2Controller@pdf')->name('quotes-v2.pdf');
	Route::get('/lcl/air/pdf/{quote_id}', 'PdfV2Controller@pdfLclAir')->name('quotes-v2.pdf.lcl.air');
	Route::get('/air/pdf/{quote_id}', 'PdfV2Controller@pdfAir')->name('quotes-v2.pdf.air');
	Route::post('feature/pdf/update', 'PdfV2Controller@updatePdfFeature')->name('quotes-v2.pdf.update.feature');
	Route::get('html/pdf/{quote_id}', 'PdfController@test')->name('pdf.html');
	//Company
	Route::get('company/companies', 'CompanyController@getCompanies')->name('quotes-v2.companies');
	//Contacts
	Route::get('contacts/contact', 'ContactController@getContacts')->name('quotes-v2.contacts');
	Route::get('contacts/contact/{company_id}', 'ContactController@getContactsByCompanyId')->name('quotes-v2.contacts.company');
	//Chargeable weight
	Route::post('update/chargeable/{id}', 'QuoteV2Controller@updateChargeable')->name('quotes-v2.update.chargeable');
	//Cost page
	Route::get('cost/page/{quote_id}', 'ExcelController@costPageQuote')->name('quotes-v2.cost.page');
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
	Route::get('ShowContractEditLCL/{id}', 'ContractsLclController@showContractRequest')->name('show.contract.edit.lcl');
	Route::put('UpdContractEditLCL/{id}', 'ContractsLclController@updateContractRequest')->name('update.contract.edit.lcl');


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
	// Duplicateds
	Route::get('duplicated/contract-lcl/{id}', 'ContractsLclController@duplicatedContractShow')->name('contractlcl.duplicated');
	Route::post('Store-duplicated/contract-lcl/{id}', 'ContractsLclController@duplicatedContractStore')->name('contractlcl.duplicated.store');

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
		->middleware(['auth','role:administrator|data_entry']);    

	Route::get('RequestsGlobalchargersFcl/create/','NewGlobalchargeRequestControllerFcl@create')->name('RequestsGlobalchargersFcl.create')
		->middleware(['auth','role:administrator|company|subuser']);

	Route::get('RequestsGlobalchargersFcl/create2/','NewGlobalchargeRequestControllerFcl@create2')->name('RequestsGlobalchargersFcl.create2')
		->middleware(['auth','role:administrator']);

	Route::POST('RequestsGlobalchargersFcl/','NewGlobalchargeRequestControllerFcl@store')->name('RequestsGlobalchargersFcl.store')
		->middleware(['auth','role:administrator|company|subuser']);
	Route::GET('RequestsGlobalchargersFcl/','NewGlobalchargeRequestControllerFcl@index')->name('RequestsGlobalchargersFcl.index')
		->middleware(['auth','role:administrator|data_entry']);
	Route::GET('RequestsGlobalchargersFcl/{id}','NewGlobalchargeRequestControllerFcl@show')->name('RequestsGlobalchargersFcl.show')
		->middleware(['auth','role:administrator|data_entry']);
	Route::PUT('RequestsGlobalchargersFcl/{id}','NewGlobalchargeRequestControllerFcl@update')->name('RequestsGlobalchargersFcl.update')
		->middleware(['auth','role:administrator|data_entry']);
	Route::DELETE('RequestsGlobalchargersFcl/{id}','NewGlobalchargeRequestControllerFcl@destroy')->name('RequestsGlobalchargersFcl.destroy')
		->middleware(['auth','role:administrator|data_entry']);
	Route::GET('RequestsGlobalchargersFcl/{id}/edit','NewGlobalchargeRequestControllerFcl@edit')->name('RequestsGlobalchargersFcl.edit')
		->middleware(['auth','role:administrator|data_entry']);

	Route::get('RGlobalCDestroy/{id}','NewGlobalchargeRequestControllerFcl@destroyRequest')->name('destroy.GlobalC')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('RequestGCStatus','NewGlobalchargeRequestControllerFcl@UpdateStatusRequest')->name('Request.GlobalC.status.fcl')
		->middleware(['auth','role:administrator|data_entry']);
});

// IMPORTATION GLOBALCHARGE FCL
Route::middleware(['auth','role:administrator|data_entry'])->prefix('ImportationGlobalchargesFcl')->group(function () {

	Route::get('AccountGC/','ImportationGlobachargersFclController@indexAccount')->name('index.Account.import.gc');
	//Importar desde request
	Route::get('RequestProccessGC/{id}','ImportationGlobachargersFclController@indexRequest')->name('process.request.gc')
		->middleware(['auth','role:administrator|data_entry']);

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

	Route::get('/testExcelImportation','ImportationGlobachargersFclController@testExcelImportation')->name('testExcelImportation.GC')->middleware(['auth','role:administrator|data_entry']);

});
// GLOBAL CHARGES LCL 
Route::prefix('globalchargeslcl')->group(function () {

	Route::post('destroyArr', 'GlobalChargesLclController@destroyArr')->name('globalchargeslcl.destroyArr')->middleware(['auth']);
	Route::put('updateGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@updateGlobalChar', 'as' => 'update-global-charge-lcl'])->middleware(['auth']);
	Route::get('deleteGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@destroyGlobalCharges', 'as' => 'delete-global-charge-lcl'])->middleware(['auth']);
	Route::get('editGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@editGlobalChar', 'as' => 'edit-global-charge-lcl'])->middleware(['auth']);
	Route::get('addGlobalChargeLcl', ['uses' => 'GlobalChargesLclController@addGlobalChar', 'as' => 'add-global-charge-lcl'])->middleware(['auth']);
	Route::get('duplicateGlobalChargeLcl/{id}', ['uses' => 'GlobalChargesLclController@duplicateGlobalCharges', 'as' => 'duplicate-global-charge-lcl'])->middleware(['auth']);

	// CRUD Administrator LCL -------------------------------------------------------------------------------------------------

	Route::get('indexLclAdm','GlobalChargesLclController@indexAdm')->name('gclcladm.index')->middleware(['auth','role:administrator|data_entry']);
	Route::get('createLclAdm','GlobalChargesLclController@createAdm')->name('gclcladm.create')->middleware(['auth','role:administrator|data_entry']);
	Route::post('addLclAdm','GlobalChargesLclController@addAdm')->name('gclcladm.add')->middleware(['auth','role:administrator|data_entry']);
	Route::get('typeChargeLclAdm/{id}','GlobalChargesLclController@typeChargeAdm')->name('gclcladm.typeCharge')->middleware(['auth','role:administrator|data_entry']);
	Route::post('StoreLclAdm','GlobalChargesLclController@storeAdm')->name('gclcladm.store')->middleware(['auth','role:administrator|data_entry']);
	Route::post('ShowLclAdm/{id}','GlobalChargesLclController@showAdm')->name('gclcladm.show')->middleware(['auth','role:administrator|data_entry']);
	Route::PUT('UpdateLclAdm/{id}','GlobalChargesLclController@updateAdm')->name('gclcladm.update')->middleware(['auth','role:administrator|data_entry']);
	Route::post('DuplicateLclAdm/{id}','GlobalChargesLclController@duplicateAdm')->name('gclcladm.duplicate')->middleware(['auth','role:administrator|data_entry']);
	Route::POST('ArrLclDuplicateAdm/','GlobalChargesLclController@duplicateArrAdm')->name('gclcladm.duplicate.Array')->middleware(['auth','role:administrator|data_entry']);
	Route::POST('StoreLclArrayDupicateAdm/','GlobalChargesLclController@storeArrayAdm')->name('gclcladm.store.array')->middleware(['auth','role:administrator|data_entry']);

	Route::POST('LclEditDateAdm/','GlobalChargesLclController@editDateArrAdm')->name('gclcladm.edit.dates.Array')->middleware(['auth','role:administrator|data_entry']);

	Route::POST('ArrUpdateDateAdmLcl/','GlobalChargesLclController@updateDateArrAdm')->name('gclcladm.update.dates.Array')->middleware(['auth','role:administrator|data_entry']);

});
Route::resource('globalchargeslcl', 'GlobalChargesLclController')->middleware('auth');

Route::middleware(['auth'])->prefix('Region')->group(function () {
	Route::resource('Region','RegionController');
	Route::get('/LoadViewRegion','RegionController@LoadViewAdd')->name('load.View.add.region');
});


Route::middleware(['auth'])->prefix('RegionP')->group(function () {

	Route::get('/LoadViewRegion','RegionHarborController@LoadViewAdd')->name('add-regionP');
});
Route::resource('RegionP','RegionHarborController');

//Manager Carriers

Route::middleware(['auth','role:administrator|data_entry'])->prefix('ManagerCarriers')->group(function(){
	Route::resource('managercarriers', 'CarriersController');
	Route::get('synchronousCarrier','CarriersController@synchronous')->name('synchronous.carrier');
});

Route::group(['prefix' => 'search', 'middleware' => ['auth']], function () {

	Route::get('list', 'SearchController@listar')->name('search.list');

});

Route::resource('search', 'SearchController')->middleware('auth');

// Nuevos terminos y condiciones 

Route::group(['prefix' => 'termsv2', 'middleware' => ['auth']], function () {

	Route::resource('termsv2', 'TermsAndConditionV2sController');
	Route::get('list', 'TermsAndConditionV2sController@index')->name('termsv2.list');
	Route::get('add', 'TermsAndConditionV2sController@add')->name('termsv2.add');
	Route::get('edit/{id}', 'TermsAndConditionV2sController@edit')->name('termsv2.edit');
	Route::get('delete/{id}', 'TermsAndConditionV2sController@destroy')->name('termsv2.delete');
	Route::get('msg/{id}', 'TermsAndConditionV2sController@destroymsg')->name('termsv2.msg');
	Route::put('delete-term/{id}', ['uses' => 'TermsAndConditionsController@destroyTerm', 'as' => 'delete-term']);

});

// Remarks Harbors

Route::group(['prefix' => 'remarks', 'middleware' => ['auth']], function () {

	Route::resource('remarks', 'RemarkConditionsController');
	Route::get('list', 'RemarkConditionsController@index')->name('remarks.list');
	Route::get('add', 'RemarkConditionsController@add')->name('remarks.add');
	Route::get('edit/{id}', 'RemarkConditionsController@edit')->name('remarks.edit');
	Route::get('delete/{id}', 'RemarkConditionsController@destroy')->name('remarks.delete');
	Route::get('msg/{id}', 'RemarkConditionsController@destroymsg')->name('remarks.msg');
	Route::put('delete-term/{id}', ['uses' => 'TermsAndConditionsController@destroyTerm', 'as' => 'delete-term']);

});

// User Configuration 

Route::group(['prefix' => 'UserConfiguration'], function (){
	Route::resource('UserConfiguration','UserConfigurationsController');
});


// Inlands Locations
Route::group(['prefix' => 'inlandL', 'middleware' => ['auth']], function () {
	Route::get('add', 'InlandLocationController@add')->name('inlandL.add');
	Route::get('delete/{inlandl_id}', ['uses' => 'InlandLocationController@destroy', 'as' => 'delete-inlandl']);
});
Route::resource('inlandL', 'InlandLocationController')->middleware('auth');

// Inlands Distances
Route::group(['prefix' => 'inlandD', 'middleware' => ['auth']], function () {
	Route::get('add', 'InlandDistanceController@add')->name('inlandD.add');
	Route::get('delete/{inlandd_id}', ['uses' => 'InlandDistanceController@destroy', 'as' => 'delete-inlandd']);
});
Route::resource('inlandD', 'InlandDistanceController')->middleware('auth');

// Importation Automatic Companies 
Route::group(['prefix' => 'CarrierImportation','middleware' => ['auth','role:administrator']],function(){
	route::resource('CarrierImportation','CarriersImportationController');

	route::get('Add','CarriersImportationController@add')->name('CarrierImportation.add');
	route::get('AddFiltro/{id}','CarriersImportationController@addFiltro')->name('surcherger.filtro.add');
	route::post('StoreFiltro/','CarriersImportationController@storeFiltro')->name('surcherger.filtro.store');
	route::get('EditFiltro/{id}','CarriersImportationController@editFiltro')->name('surcherger.filtro.edit');
	route::put('UpdateFiltro/{id}','CarriersImportationController@UpdateFiltro')->name('surcherger.filtro.update');
	route::delete('DestroyFiltro/{id}','CarriersImportationController@DestroyFiltro')->name('surcherger.filtro.destroy');
	route::get('IndexFiltro/','CarriersImportationController@indexFiltro')->name('surcherger.filtro.index');
	route::get('ShowFiltro/','CarriersImportationController@show2')->name('surcherger.filtro.show');
	route::get('ShowModalForward/','CarriersImportationController@ShowModalForward')->name('forward.modal.show');
	route::post('RequestsForward/','CarriersImportationController@forwardRequest')->name('forward.request');
	route::get('test','CarriersImportationController@test')->name('test.carrier.autoimport');
});

// IMPORTATION GLOBALCHARGE LCL
Route::middleware(['auth','role:administrator|data_entry'])->prefix('ImportationGlobalChargerLcl')->group(function () {
	Route::get('RequestProccessGCLCL/{id}','ImportationGlobalChargerLclController@indexRequest')->name('process.request.gc.lcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::resource('ImportationGlobalChargerLcl','ImportationGlobalChargerLclController');
	Route::get('AccountGCLcl/','ImportationGlobalChargerLclController@indexAccount')->name('index.Account.import.gc.lcl');
	Route::PUT('UploadFileGlobalchargesLcl','ImportationGlobalChargerLclController@UploadFileNewContract')->name('Upload.File.Globalcharges.Lcl');
	Route::get('DownloadAccountgcLcl/{id}','ImportationGlobalChargerLclController@Download')->name('Download.Account.gclcl');
	//Failed and Good GCLCL
	Route::get('FailedGlobalchargerslcl/{id}/{tab}','ImportationGlobalChargerLclController@showviewfailedandgood')->name('showview.globalcharge.lcl');
	Route::get('/FailglobalchargeLoadlcl/{id}/{selector}','ImportationGlobalChargerLclController@FailglobalchargeLoad')->name('Fail.Load.globalcharge.lcl');
	Route::get('saveTofailToGoddGCLCL/{id}','ImportationGlobalChargerLclController@saveFailToGood')->name('save.fail.good.globalcharge.lcl');
	Route::get('DestroyglobalchargeFailLcl/{id}','ImportationGlobalChargerLclController@DestroyGlobalchargeF')->name('Destroy.globalcharge.Fail.lcl');
	Route::get('DestroyglobalchargeGoodLcl/{id}','ImportationGlobalChargerLclController@DestroyGlobalchargeG')->name('Destroy.globalcharge.good.lcl');
	Route::get('editGlobalChargeMDLCL/{id}','ImportationGlobalChargerLclController@editGlobalChar')->name('edit.globalcharge.modal.lcl');
	Route::put('updateGlobalChargeMDLCL/{id}','ImportationGlobalChargerLclController@updateGlobalChar')->name('update.globalcharge.modal.lcl');
	Route::get('RedirectProcessedInformationLcl/{id}','ImportationGlobalChargerLclController@redirectProcessedInformation')->name('redirect.Processed.Information.lcl');
	Route::get('DeleteAccountsGlobalchargesLcl/{id}/{select}','ImportationGlobalChargerLclController@deleteAccounts')->name('delete.Accounts.Globalcharges.Lcl');
	// Reprocesar
	Route::get('/ReprocesarGlobalchargersLcl/{id}','ImportationGlobalChargerLclController@ReprocesarGlobalchargers')->name('Reprocesar.globalcharge.lcl');
});

// REQUEST IMPORTATION GLOBALCHARGE LCL
Route::prefix('RequestsGlobalchargersLcl')->group(function () {

	/*
    Route::get('RequestsGlobalchargersLcl/indexListClient','NewRequestGlobalChargerLclController@indexListClient')->name('RequestsGlobalchargersFcl.indexListClient')
        ->middleware(['auth','role:administrator|company|subuser']);

    Route::get('RequestsGlobalchargersLcl/listClient/{id}','NewRequestGlobalChargerLclController@listClient')->name('RequestsGlobalchargersFcl.listClient')
        ->middleware(['auth','role:administrator|company|subuser']);
*/
	Route::get('StatusRquestGCLCL/{id}','NewRequestGlobalChargerLclController@showStatus')->name('show.status.Request.gc.lcl')
		->middleware(['auth','role:administrator|data_entry']);    

	Route::get('RequestsGlobalchargersLcl/create/','NewRequestGlobalChargerLclController@create')->name('RequestsGlobalchargersLcl.create')
		->middleware(['auth','role:administrator|company|subuser']);
	Route::get('RequestsGlobalchargersLcl/create2/','NewRequestGlobalChargerLclController@create2')->name('RequestsGlobalchargersLcl.create2')
		->middleware(['auth','role:administrator']);

	Route::POST('RequestsGlobalchargersLcl/','NewRequestGlobalChargerLclController@store')->name('RequestsGlobalchargersLcl.store')
		->middleware(['auth','role:administrator|company|subuser']);
	Route::GET('RequestsGlobalchargersLcl/','NewRequestGlobalChargerLclController@index')->name('RequestsGlobalchargersLcl.index')
		->middleware(['auth','role:administrator|data_entry']);
	Route::GET('RequestsGlobalchargersLcl/{id}','NewRequestGlobalChargerLclController@show')->name('RequestsGlobalchargersLcl.show')
		->middleware(['auth','role:administrator|data_entry']);
	Route::PUT('RequestsGlobalchargersLcl/{id}','NewRequestGlobalChargerLclController@update')->name('RequestsGlobalchargersLcl.update')
		->middleware(['auth','role:administrator|data_entry']);
	Route::DELETE('RequestsGlobalchargersLcl/{id}','NewRequestGlobalChargerLclController@destroy')->name('RequestsGlobalchargersLcl.destroy')
		->middleware(['auth','role:administrator|data_entry']);
	Route::GET('RequestsGlobalchargersLcl/{id}/edit','NewRequestGlobalChargerLclController@edit')->name('RequestsGlobalchargersLcl.edit')
		->middleware(['auth','role:administrator|data_entry']);

	Route::get('RGlobalCDestroy/{id}','NewRequestGlobalChargerLclController@destroyRequest')->name('destroy.GlobalC.lcl')
		->middleware(['auth','role:administrator|data_entry']);
	Route::get('RequestGCStatusLcl','NewRequestGlobalChargerLclController@UpdateStatusRequest')->name('Request.GlobalC.status.lcl')
		->middleware(['auth','role:administrator|data_entry']);
});

Auth::routes();

$router->get('/APP_ENV', function() {
	return env('APP_ENV');
	//return App\User::where('email','admin@example.com')->first();
})->middleware(['auth','role:administrator|company|subuser']);

// Grupos de Sruchargers 
Route::group(['prefix' => 'GruopSurcharger','middleware' => ['auth','role:administrator']],function(){
	route::resource('gruopSurcharger','GroupSurchargerController');
	//route::get('SendJob/{user}/{request}','TestController@sendJob')->name('send.job.testapp');
	route::post('GSSAdd','GroupSurchargerController@showAdd')->name('group.surcharger.showAdd');
});

// Alertas Y Grupos de Globals Duplicados
Route::group(['prefix' => 'GlobalDuplicated','middleware' => ['auth','role:administrator']],function(){
	route::resource('globalsduplicated','AlertsDuplicatedsGlobalFclController');
	route::get('showStatusAlert/{id}','AlertsDuplicatedsGlobalFclController@showStatus')->name('show.status.alert.dp');
	route::get('SearchDupicatedAlert/','AlertsDuplicatedsGlobalFclController@searchDuplicateds')->name('search.alert.dp');
	route::post('updateStatusAlert/{id}','AlertsDuplicatedsGlobalFclController@updateStatus')->name('change.status.alert.dp');
	route::get('testAlert/','AlertsDuplicatedsGlobalFclController@test')->name('tets.alert.dp');

	//Groups
	route::resource('groupglobalsduplicated','GroupGlobalsCompanyUserController');
	route::get('showStatusGroup/{id}','GroupGlobalsCompanyUserController@showStatus')->name('show.status.alert.group');
	route::post('updateStatusAGroup/{id}','GroupGlobalsCompanyUserController@updateStatus')->name('change.status.alert.group');
	//route::get('SendJob/{user}/{request}','TestController@sendJob')->name('send.job.testapp');

	//Groups
	route::resource('GlobalsDuplicatedEspecific','GlobalsDuplicatedFclController');
	route::get('GCDPESPShow/{id}/{grupo_id}','GlobalsDuplicatedFclController@showAdm')->name('gc.duplicated.especific.show');
});

// Test Controller 
Route::group(['prefix' => 'TestApp','middleware' => ['auth','role:administrator']],function(){
	route::resource('TestApp','TestController');
	route::get('SendJob/{user}/{request}','TestController@sendJob')->name('send.job.testapp');
});

Route::get('/testRoute',function(){
	dd(explode(',',env('LOGGING_CHANNELS')));
})->name('test.route');

// RequestFcl V2
Route::group(['prefix' => 'RequestFcl','middleware' => 'auth'],function(){
	route::get('index','RequestFclV2Controller@index')->name('RequestFcl.index')->middleware(['role:administrator|data_entry']);
	route::get('create','RequestFclV2Controller@create')->name('RequestFcl.create')->middleware(['role:administrator|data_entry']);
	route::post('store','RequestFclV2Controller@store')->name('RequestFcl.store')->middleware(['role:administrator|company|subuser|data_entry']);
	route::get('show/{id}','RequestFclV2Controller@show')->name('RequestFcl.show')->middleware(['role:administrator|data_entry']);
	route::get('edit/{id}','RequestFclV2Controller@edit')->name('RequestFcl.edit')->middleware(['role:administrator|data_entry']);
	route::put('update/{id}','RequestFclV2Controller@update')->name('RequestFcl.update')->middleware(['role:administrator|company|subuser|data_entry']);
	route::delete('destroy/{id}','RequestFclV2Controller@destroy')->name('RequestFcl.destroy')->middleware(['role:administrator|company|subuser|data_entry']);
	route::get('NewRqFcl','RequestFclV2Controller@newRequest')->name('request.fcl.new.request')->middleware(['role:administrator|company|subuser|data_entry']);
	route::get('getContainers','RequestFclV2Controller@getContainers')->name('request.fcl.get.containers');
	route::post('storeMediaRqFcl','RequestFclV2Controller@storeMedia')->name('request.fcl.storeMedia');
	route::get('donwloadFilesRFCL/{id}/{selector}','RequestFclV2Controller@donwloadFiles')->name('RequestFcl.donwload.files');
    Route::get('StatusRFCL','RequestFclV2Controller@UpdateStatusRequest')->name('request.fcl.status')
		->middleware(['auth','role:administrator|data_entry']);
	Route::post('ExportRFCL/','RequestFclV2Controller@export')->name('export.request.fcl.v2')
		->middleware(['auth','role:administrator|data_entry']);
});

Route::prefix('ContainerCalculation')->group(function () {
	Route::resource('ContainerCalculation','ContainerCalculationController')->middleware(['role:administrator|data_entry']);
	route::get('AddCCalculationT','ContainerCalculationController@loadBodymodalAdd')->name('add.conatiner.calculation')->middleware(['role:administrator|data_entry']);
});

Route::prefix('CalculationType')->group(function () {
	Route::resource('CalculationType','CalculationTypeController')->middleware(['role:administrator|data_entry']);
});


Route::prefix('Container')->group(function () {
	Route::resource('Container','ContainerController')->middleware(['role:administrator|data_entry']);
});

/** Contracts V2 routes **/
Route::get('api/contracts', 'ContractController@index');
Route::get('api/v2/contracts', 'ContractController@list');
Route::get('api/v2/contracts/data', 'ContractController@data');
Route::get('api/contracts/create', 'ContractController@create');
Route::post('api/v2/contracts/store', 'ContractController@store');
Route::get('api/v2/contracts/{contract}', 'ContractController@retrieve');
Route::get('api/contracts/{contract}/edit', 'ContractController@edit');
Route::post('api/v2/contracts/{contract}/update', 'ContractController@update');

Route::get('api/v2/contracts/{contract}/ocean_freight', 'OceanFreightController@list');
Route::get('api/v2/contracts/{contract}/ocean_freight/store', 'OceanFreightController@store');
Route::post('api/v2/contracts/{contract}/ocean_freight/{rate}/update', 'OceanFreightController@update');
Route::get('api/v2/contracts/{contract}/ocean_freight/{rate}', 'OceanFreightController@retrieve');
/** End Contracts V2 routes **/
