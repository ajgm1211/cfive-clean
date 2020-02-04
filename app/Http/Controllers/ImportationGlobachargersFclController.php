<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use PrvHarbor;
use PrvCarrier;
use App\Harbor;
use App\Region;
use App\Carrier;
use App\Country;
use App\Currency;
use PrvValidation;
use Carbon\Carbon;
use App\Surcharge;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\CalculationType;
use App\GlobalCharCountry;
use App\GlobalCharCarrier;
use App\FailedGlobalcharge;
use App\FileTmpGlobalcharge;
use Illuminate\Http\Request;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\DB;
use App\AccountImportationGlobalcharge;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportationGlobalchargeJob;
use App\Jobs\ReprocessGlobalChargersJob;
use App\NewGlobalchargeRequestFcl as RequestGC;


class ImportationGlobachargersFclController extends Controller
{

	// Reprocesamiento
	public function ReprocesarGlobalchargers(Request $request, $id){
		$countfailglobalchargers = FailedGlobalcharge::where('account_id','=',$id)->count();
		if($countfailglobalchargers <= 150){
			$failglobalchargers = FailedGlobalcharge::where('account_id','=',$id)->get();
			//dd($failglobalchargers);
			$account_idVal = $id;
			foreach($failglobalchargers as $failglobalcharger){

				$company_user_id    = $failglobalcharger->company_user_id;
				$surchargerEX       = '';
				$origenEX           = '';
				$destinyEX          = '';
				$typedestinyEX      = '';
				$calculationtypeEX  = '';
				$ammountEX          = '';
				$currencyEX         = '';
				$carrierEX          = '';
				$validitytoEX       = '';
				$validityfromEX     = '';
				$originResul        = '';
				$originExits        = '';
				$originV            = '';
				$destinResul        = '';
				$destinationExits   = '';
				$destinationV       = '';
				$surchargerV        = '';
				$typedestunyV       = '';
				$calculationtypeV   = '';
				$amountV            = '';
				$currencyV          = '';
				$carrierV           = '';
				$validityfromV      = '';
				$validitytoV        = '';

				$carrierB           = false;
				$calculationtypeB   = false;
				$typedestinyB       = false;
				$originB            = false;
				$destinyB           = false;
				$surcharB           = false;
				$currencyB          = false;
				$validityfromBol    = false;
				$validitytoBol      = false;


				$surchargerEX       = explode('_',$failglobalcharger['surcharge']);
				$originEX           = explode('_',$failglobalcharger['origin']);
				$destinyEX          = explode('_',$failglobalcharger['destiny']);
				$typedestinyEX      = explode('_',$failglobalcharger['typedestiny']);
				$calculationtypeEX  = explode('_',$failglobalcharger['calculationtype']);
				$ammountEX          = explode('_',$failglobalcharger['ammount']);
				$currencyEX         = explode('_',$failglobalcharger['currency']);
				$carrierEX          = explode('_',$failglobalcharger['carrier']);
				$validityfromEX     = explode('_',$failglobalcharger['validityfrom']);
				$validitytoEX       = explode('_',$failglobalcharger['validityto']);

				if(count($surchargerEX) == 1     && count($typedestinyEX) == 1
				   && count($typedestinyEX) == 1 && count($calculationtypeEX) == 1
				   && count($ammountEX) == 1     && count($currencyEX) == 1){

					// Origen Y Destino ------------------------------------------------------------------------
					if($failglobalcharger->differentiator  == 1){
						$resultadoPortOri = PrvHarbor::get_harbor($originEX[0]);
						$originV  = $resultadoPortOri['puerto'];
					} else if($failglobalcharger->differentiator  == 2){
						$resultadoPortOri = PrvHarbor::get_country($originEX[0]);
						$originV  = $resultadoPortOri['country'];
					}
					if($resultadoPortOri['boolean']){
						$originB = true;    
					}

					if($failglobalcharger->differentiator  == 1){
						$resultadoPortDes = PrvHarbor::get_harbor($destinyEX[0]);
						$destinationV  = $resultadoPortDes['puerto'];
					} else if($failglobalcharger->differentiator  == 2){
						$resultadoPortDes = PrvHarbor::get_country($destinyEX[0]);
						$destinationV  = $resultadoPortDes['country'];
					}
					if($resultadoPortDes['boolean']){
						$destinyB = true;    
					}

					//  Surcharge ------------------------------------------------------------------------------

					$surchargerV = Surcharge::where('name','=',$surchargerEX[0])->first();
					if(count($surchargerV) == 1){
						$surcharB = true;
						$surchargerV = $surchargerV['id'];
					}

					//  Type Destiny ---------------------------------------------------------------------------

					$typedestunyV = TypeDestiny::where('description','=',$typedestinyEX[0])->first();
					if(count($typedestunyV) == 1){
						$typedestinyB = true;
						$typedestunyV = $typedestunyV['id'];
					}

					//  Calculation Type -----------------------------------------------------------------------

					$calculationtypeV = CalculationType::where('code','=',$calculationtypeEX[0])->orWhere('name','=',$calculationtypeEX[0])->first();

					if(count($calculationtypeV) == 1){
						$calculationtypeB = true;
						$calculationtypeV = $calculationtypeV['id'];
					}

					//  Amount ---------------------------------------------------------------------------------

					$amountV = floatval($ammountEX[0]);

					//  Currency -------------------------------------------------------------------------------

					$currencyV = Currency::where('alphacode','=',$currencyEX[0])->first();
					if(count($currencyV) == 1){
						$currencyB = true;
						$currencyV = $currencyV['id'];
					}

					//  Carrier -------------------------------------------------------------------------------

					$carrierArr = PrvCarrier::get_carrier($carrierEX[0]);
					$carrierV   = $carrierArr['carrier'];
					$carrierB   = $carrierArr['boolean'];

					//------------------ VALIDITY FROM ------------------------------------------------------

					if(count($validityfromEX) <= 1){
						try{
							$validityfromV = Carbon::parse($validityfromEX[0])->format('Y-m-d');
							$validityfromBol = true;
						} catch (\Exception $err){

						}
					}

					//------------------ VALIDITY TO --------------------------------------------------------				
					if(count($validitytoEX) <= 1){
						try{
							$validitytoV = Carbon::parse($validitytoEX[0])->format('Y-m-d');
							$validitytoBol = true;
						} catch (\Exception $err){

						}
					}
					/*
                    $colleccion = collect([]);
                    $colleccion = [
                        'origen'            =>  $originV,
                        'destiny'           =>  $destinationV,
                        'surcharge'         =>  $surchargerV,
                        'typedestuny'       =>  $typedestunyV,
                        'calculationtypeV'  =>  $calculationtypeV,
                        'amountV'           =>  $amountV,
                        'currencyV'         =>  $currencyV,
                        'carrierV'          =>  $carrierV,
                        'validityfromV'     =>  $validityfromV,
                        'validitytoV'       =>  $validitytoV,
                        'surcharB'          =>  $surcharB,
                        'originB'           =>  $originB,
                        'destinyB'          =>  $destinyB,
                        'calculationtypeB'  =>  $calculationtypeB,
                        'carrierB'          =>  $carrierB,
                        'currencyB'         =>  $currencyB,
                        'typedestinyB'      =>  $typedestinyB,
                        'validityfromBol'   =>  $validityfromBol,
                        'validitytoBol'     =>  $validitytoBol
                    ];

                    dd($colleccion);*/

					if($originB             == true && $destinyB        == true 
					   && $surcharB         == true && $typedestinyB    == true
					   && $calculationtypeB == true && $currencyB       == true
					   && $validityfromBol  == true && $validitytoBol   == true
					   && $carrierB         == true){

						if($failglobalcharger->differentiator  == 1){ //si es puerto verificamos si exite uno creado con country
							$typeplace = 'globalcharport';
						} elseif($failglobalcharger->differentiator  == 2){  //si es country verificamos si exite uno creado con puerto
							$typeplace = 'globalcharcountry';
						}

						$globalChargeArreG = GlobalCharge::where('surcharge_id',$surchargerV)
							->where('typedestiny_id',$typedestunyV)
							->where('company_user_id',$company_user_id)
							->where('calculationtype_id',$calculationtypeV)
							->where('ammount',$amountV)
							->where('validity',$validityfromV)
							->where('expire',$validitytoV)
							->where('currency_id',$currencyV)
							->has($typeplace)
							->first();

						if(count($globalChargeArreG) == 0){

							$globalChargeArreG = GlobalCharge::create([ // tabla GlobalCharge
								'surcharge_id'       						=> $surchargerV,
								'typedestiny_id'     						=> $typedestunyV,
								'account_importation_globalcharge_id'       => $account_idVal,
								'company_user_id'    						=> $company_user_id,
								'calculationtype_id' 						=> $calculationtypeV,
								'ammount'            						=> $amountV,
								'validity' 									=> $validityfromV,
								'expire'					 				=> $validitytoV,
								'currency_id'        						=> $currencyV
							]);
						}

						GlobalCharCarrier::create([ // tabla GlobalCharCarrier
							'carrier_id'      => $carrierV,
							'globalcharge_id' => $globalChargeArreG->id
						]);

						if($failglobalcharger->differentiator  == 1){
							GlobalCharPort::create([ // tabla GlobalCharPort
								'port_orig'      	=> $originV,
								'port_dest'      	=> $destinationV,
								'typedestiny_id' 	=> $typedestunyV,
								'globalcharge_id'   => $globalChargeArreG->id
							]);
						} else if($failglobalcharger->differentiator  == 2){
							GlobalCharCountry::create([ // tabla GlobalCharCountry
								'country_orig'      => $originV,
								'country_dest'      => $destinationV,
								'globalcharge_id'   => $globalChargeArreG->id                                                   
							]);
						}

						$failglobalcharger->delete();
					}
				}

			}

			$account = AccountImportationGlobalcharge::find($id);
			$usersNotifiques = User::where('type','=','admin')->get();
			foreach($usersNotifiques as $userNotifique){
				$message = 'The Global Chargers was Reprocessed. Account: ' . $account['id'].' '.$account['name'] ;
				$userNotifique->notify(new N_general($userNotifique,$message));
			}

		} else {
			if(env('APP_VIEW') == 'operaciones') {
				ReprocessGlobalChargersJob::dispatch($id)->onQueue('operaciones');
			}else {
				ReprocessGlobalChargersJob::dispatch($id);
			}
			$request->session()->flash('message.nivel', 'success');
			$request->session()->flash('message.content', 'The Surchargers are reprocessing in the background');
			return redirect()->route('showview.globalcharge.fcl',[$id,'1']);
		}

		$request->session()->flash('message.nivel', 'success');
		$request->session()->flash('message.content', 'The Global Chargers are being reprocessed');
		$countfailglobalchargers = FailedGlobalcharge::where('account_id','=',$id)->count();

		if($countfailglobalchargers > 0){
			return redirect()->route('showview.globalcharge.fcl',[$id,'1']);
		}else{
			return redirect()->route('showview.globalcharge.fcl',[$id,'0']);
		}
	}

	// precarga la vista para importar rates o rates mas surchargers
	public function index()
	{
		$harbor         = Harbor::all()->pluck('display_name','id');
		$country        = Country::all()->pluck('name','id');
		$carrier        = Carrier::all()->pluck('name','id');
		$region         = Region::all()->pluck('name','id');
		$companysUser   = CompanyUser::all()->pluck('name','id');
		$typedestiny    = TypeDestiny::all()->pluck('description','id');
		return view('ImportationGlobalchargersFcl.index',compact('harbor','region','country','carrier','companysUser','typedestiny'));
	}

	public function indexRequest($id)
	{
		$requestgc      = RequestGC::find($id);
		//dd($requestgc);
		$harbor         = Harbor::all()->pluck('display_name','id');
		$country        = Country::all()->pluck('name','id');
		$carrier        = Carrier::all()->pluck('name','id');
		$region         = Region::all()->pluck('name','id');
		$companysUser   = CompanyUser::all()->pluck('name','id');
		$typedestiny    = TypeDestiny::all()->pluck('description','id');
		return view('ImportationGlobalchargersFcl.indexRequest',compact('harbor','country','region','carrier','companysUser','typedestiny','requestgc'));
	}

	// carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
	public function UploadFileNewContract(Request $request){
		//dd($request->all());
		$now = new \DateTime();
		$now = $now->format('dmY_His');
		$request_id         = $request->request_id;
		$carrierVal         = $request->carrier;
		$typedestinyVal     = $request->typedestiny;
		$validitydateVal    = $request->validitydate;
		$destinyArr         = $request->destiny;
		$originArr          = $request->origin;
		$originCountArr     = $request->originCount;
		$originRegionArr    = $request->originRegion;
		$destinyCountArr    = $request->destinyCount;
		$destinyRegionArr   = $request->destinyRegion;
		$CompanyUserId      = $request->CompanyUserId;
		$statustypecurren   = $request->valuesCurrency;
		$statusPortCountry  = $request->valuesportcountry;

		$carrierBol         = false;
		$destinyBol         = false;
		$originBol          = false;
		$typedestinyBol     = false;
		$datevalidityBol    = false;
		$fortynorBol        = false;
		$fortyfiveBol       = false;
		$filebool           = false;

		$data           = collect([]);
		$typedestiny    = TypeDestiny::all()->pluck('description','id');
		$harbor         = harbor::all()->pluck('display_name','id');
		$country        = Country::all()->pluck('name','id');
		$region         = Region::all()->pluck('name','id');
		$carrier        = carrier::all()->pluck('name','id');


		$file           = $request->file('file');
		$ext            = strtolower($file->getClientOriginalExtension());
		$validator      = \Validator::make(
			array('ext' => $ext),
			array('ext' => 'in:xls,xlsx,csv')
		);
		$Contract_id;
		if ($validator->fails()) {
			$request->session()->flash('message.nivel', 'danger');
			$request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
			return redirect()->route('ImportationGlobalchargeFcl.index');
		}
		//obtenemos el nombre del archivo
		$nombre     = $file->getClientOriginalName();
		$nombre     = $now.'_'.$nombre;
		$filebool   = \Storage::disk('GCImport')->put($nombre,\File::get($file));

		if($filebool){
			\Storage::disk('GCAccount')->put($nombre,\File::get($file));
			$account   = new AccountImportationGlobalcharge();
			$account->name             = $request->name;
			$account->namefile         = $nombre;
			$account->date             = $request->date;
			$account->status           = 'incomplete';
			$account->requestgc_id     = $request_id;
			$account->company_user_id  = $CompanyUserId;
			$account->save(); 

			if(env('APP_VIEW') == 'operaciones') {
				ProcessContractFile::dispatch($account->id,$account->namefile,'gcfcl','account')->onQueue('operaciones');
			} else {
				ProcessContractFile::dispatch($account->id,$account->namefile,'gcfcl','account');
			}

			$account_id = $account->id;
			$fileTmp    = new FileTmpGlobalcharge();
			$fileTmp->account_id = $account_id;
			$fileTmp->name_file   = $nombre;
			$fileTmp->save(); //*/
		} else {
			$request->session()->flash('message.nivel', 'danger');
			$request->session()->flash('message.content', 'Error storage:link!!');
			return redirect()->route('ImportationGlobalchargeFcl.index');
		}


		$targetsArr =[ 
			0 => "Calculation Type",
			1 => "Charge",
			2 => "20'",
			3 => "40'",
			4 => "40'HC"
		];

		// Datftynor Datftyfive - DatOri - DatDes - DatCar, hacen referencia a si fue marcado el checkbox

		if($request->Datftynor == true){
			array_push($targetsArr,"40'NOR");
		} else{
			$fortynorBol = true;
		}

		if($request->Datftyfive == true){
			array_push($targetsArr,"45'");
		} else {
			$fortyfiveBol = true;
		}

		/* si $statusPortCountry es igual a 2, se agrega una columna que diferencia puertos de paises
        , si es 1 el solo se mapean puertos        
        */
		if($statusPortCountry == 2){
			array_push($targetsArr,"Differentiator");
		}

		/* si $statustypecurren es igual a 2, los currencys estan contenidos en la misma columna 
        con los valores, si es uno el currency viene en una colmna aparte        
        */

		if($statustypecurren == 1){
			array_push($targetsArr,"Currency");
		}

		// ------- ORIGIN -------------------------
		if($request->DatOri == false){
			array_push($targetsArr,'Origin');
		}
		else{
			$originBol = true;
			$originArr;
		}

		// ------- DESTINY ------------------------
		if($request->DatDes == false){
			array_push($targetsArr,'Destiny');
		} else {
			$destinyArr;
			$destinyBol = true;
		}

		// ------- CARRIER ------------------------
		if($request->DatCar == false){
			array_push($targetsArr,'Carrier');
		} else {
			$carrierVal;
			$carrierBol = true;
		}

		// ------- TYPE DESTINY -------------------
		if($request->DatTypeDes == false){
			array_push($targetsArr,'Type Destiny');
		} else {
			$typedestinyVal;
			$typedestinyBol = true;
		}

		// ------- DATE VAIDITY -------------------
		if($request->DatDtValid == false){
			array_push($targetsArr,'Validity From');
			array_push($targetsArr,'Validity To');
		} else {
			$validitydateVal;
			$datevalidityBol = true;
		}

		$coordenates = collect([]);

		ini_set('memory_limit', '1024M');

		Excel::selectSheetsByIndex(0)
			->Load(\Storage::disk('GCImport')
				   ->url($nombre),function($reader) use($request,$coordenates) {
					   $reader->takeRows(2);
					   $reader->noHeading = true;
					   $reader->ignoreEmpty();

					   $read = $reader->first();
					   $columna= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V');
					   for($i=0;$i<count($reader->first());$i++){
						   $coordenates->push($columna[$i].' '.$read[$i]);
					   }
				   });

		$boxdinamy = [
			'existorigin'       => $originBol,
			'origin'            => $originArr,
			'existdestiny'      => $destinyBol,
			'destiny'           => $destinyArr,
			'originCount'       => $originCountArr,
			'originRegion'      => $originRegionArr,
			'destinyCount'      => $destinyCountArr,
			'destinyRegion'     => $destinyRegionArr,
			'existcarrier'      => $carrierBol,
			'carrier'           => $carrierVal,            
			'existtypedestiny'  => $typedestinyBol,
			'typedestiny'       => $typedestinyVal,
			'existdatevalidity' => $datevalidityBol,
			'validitydate'      => $validitydateVal,
			'account_id'        => $account_id,
			'date'              => $request->date,
			'name'              => $request->name,
			'existfortynor'     => $fortynorBol,
			'fortynor'          => 0,
			'existfortyfive'    => $fortyfiveBol,
			'fortyfive'         => 0,
			'fileName'          => $nombre,

		];
		$data->push($boxdinamy);
		$countTarges = count($targetsArr);
		//dd($data);

		return view('ImportationGlobalchargersFcl.show',compact('harbor',
																'region',
																'country',
																'data',
																'carrier',
																'targetsArr',
																'account_id',
																'account',
																'coordenates',
																'countTarges',
																'CompanyUserId',
																'statustypecurren',
																'statusPortCountry',
																'typedestiny'));
	}

	// Despachador a job de importacion
	public function create(Request $request)
	{
		$companyUserId = $request->CompanyUserId;
		$UserId =\Auth::user()->id;
		//dd($request->all());

		$requestobj = $request;
		$companyUserIdVal = $companyUserId;
		$errors = 0;
		$NameFile = $requestobj['FileName'];
		$path = \Storage::disk('GCImport')->url($NameFile);

		//FailedGlobalcharge::where('company_user_id',$companyUserIdVal)->delete();
		//GlobalCharge::where('company_user_id',$companyUserIdVal)->delete();

		if(env('APP_VIEW') == 'operaciones') {
			ImportationGlobalchargeJob::dispatch($request->all(),$companyUserId,$UserId)->onQueue('operaciones'); //NO BORRAR!!
		}else {
			ImportationGlobalchargeJob::dispatch($request->all(),$companyUserId,$UserId); //NO BORRAR!!
		}
		$id = $request['account_id'];
		return redirect()->route('ImportationGlobalchargeFcl.show',$id);
	}

	// Lista de cuentas importadas
	public function indexTwo(){
		$accounts = AccountImportationGlobalcharge::with('companyuser')->orderBy('id','desc')->get();
		//dd($accounts);
		return view('ImportationGlobalchargersFcl.indexAccount',compact('accounts'));
	}

	public function store(Request $request)
	{
		//
	}

	// view de informacion despues de despachar el job
	public function show($id)
	{
		$accounts = AccountImportationGlobalcharge::find($id);
		return view('ImportationGlobalchargersFcl.ProcessedInformation',compact('id','accounts'));
	}

	public function edit($id){

		$failglobal = FailedGlobalcharge::find($id);

		$typedestiny    = TypeDestiny::all()->pluck('description','id');
		$surcharge      = Surcharge::where('company_user_id','=', $failglobal['company_user_id'])->pluck('name','id');
		$carrier        = Carrier::all()->pluck('name','id');
		$harbor         = Harbor::all()->pluck('display_name','id');
		$currency       = Currency::all()->pluck('alphacode','id');
		$calculationT   = CalculationType::all()->pluck('name','id');
		$countries      = Country::pluck('name','id');

		$classdorigin           =  'color:green';
		$classddestination      =  'color:green';
		$classtypedestiny       =  'color:green';
		$classcarrier           =  'color:green';
		$classsurcharger        =  'color:green';
		$classcalculationtype   =  'color:green';
		$classammount           =  'color:green';
		$classcurrency          =  'color:green';
		$classvalidity          =  'color:green';

		$surchargeA         =  explode("_",$failglobal['surcharge']);
		$originA            =  explode("_",$failglobal['origin']);
		$destinationA       =  explode("_",$failglobal['destiny']);
		$calculationtypeA   =  explode("_",$failglobal['calculationtype']);
		$ammountA           =  explode("_",$failglobal['ammount']);
		$currencyA          =  explode("_",$failglobal['currency']);
		$carrierA           =  explode("_",$failglobal['carrier']);
		$typedestinyA       =  explode("_",$failglobal['typedestiny']);
		$validitytoA        =  explode("_",$failglobal['validityto']);
		$validityfromA      =  explode("_",$failglobal['validityfrom']);

		// -------------- ORIGIN -------------------------------------------------------------

		if($failglobal->differentiator == 1){
			$originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
				->first();
		} else if($failglobal->differentiator == 2){
			$originOb  = Country::where('variation->type','like','%'.strtolower($originA[0]).'%')
				->first();
		}

		$originC   = count($originA);
		if($originC <= 1){
			$originAIn = [$originOb['id']];
		} else{
			$originAIn = [];
			$classdorigin='color:red';
		}

		// -------------- DESTINATION --------------------------------------------------------
		if($failglobal->differentiator == 1){
			$destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
				->first();
		} else if($failglobal->differentiator == 2){
			$destinationOb  = Country::where('variation->type','like','%'.strtolower($destinationA[0]).'%')
				->first();
		}

		$destinationC   = count($destinationA);
		if($destinationC <= 1){
			$destinationAIn = [$destinationOb->id];

		} else{
			$destinationAIn = '';
			$classddestination='color:red';
		}

		// -------------- SURCHARGE ....-----------------------------------------------------
		$surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',$failglobal['company_user_id'])->first();
		$surchargeC = count($surchargeA);
		if($surchargeC <= 1){
			$surcharAin  = $surchargeOb['id'];
		}
		else{
			$surcharAin  = '';
			$classsurcharger    = 'color:red';
		}

		// -------------- CARRIER -----------------------------------------------------------
		$carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
		$carrierC = count($carrierA);
		if($carrierC <= 1){
			$carrAIn = ['id'=>$carrierOb['id']];
		}
		else{
			$carrAIn = [];
			$classcarrier   ='color:red';
		}

		// -------------- CALCULATION TYPE --------------------------------------------------
		$calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
		$calculationtypeC   = count($calculationtypeA);
		if($calculationtypeC <= 1){
			$calculationtypeAIn = $calculationtypeOb['id'];
		}
		else{
			$calculationtypeAIn = '';
			$classcalculationtype   = 'color:red';
		}

		// -------------- AMMOUNT -----------------------------------------------------------
		$ammountC = count($ammountA);
		if($ammountC <= 1){
			$ammountA = floatval($failglobal['ammount']);
		}
		else{
			$ammountA       = $ammountA[0].' (error)';
			$classammount   = 'color:red';
		}

		// -------------- CURRENCY ----------------------------------------------------------
		$currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
		$currencyC    = count($currencyA);
		if($currencyC <= 1){
			$currencyAIn  = $currencyOb['id'];
		}
		else{
			$currencyAIn  = '';
			$classcurrency  = 'color:red';
		}

		// -------------- TYPE DESTINY -----------------------------------------------------
		//dd($failsurcharge['typedestiny_id']);
		$typedestinyobj    = TypeDestiny::where('description',$typedestinyA[0])->first();
		if(count($typedestinyA) <= 1){
			$typedestinyLB = $typedestinyobj['id'];
		}
		else{
			$typedestinyLB = '';
			$classtypedestiny   = 'color:red';
		}

		// -------------- VALIDITYTO -----------------------------------------------------

		if(count($validitytoA) <= 1){
			$validitytoLB = trim($validitytoA[0]);
		}
		else{
			$validitytoLB = '';
			$classvalidity   = 'color:red';
		}

		// -------------- VALIDITYFROM -----------------------------------------------------

		if(count($validityfromA) <= 1){
			$validityfromLB = trim($validityfromA[0]);
		}
		else{
			$validityfromLB      = '';
			$classvalidity   = 'color:red';
		}

		$validation_expire = $validityfromLB.' / '.$validitytoLB;


		////////////////////////////////////////////////////////////////////////////////////
		$arre = [
			'id'                    => $failglobal['id'],
			'surcharge_id'          => $surcharAin,
			'origin_port'           => $originAIn,
			'destiny_port'          => $destinationAIn,
			'carrier'               => $carrAIn,
			'company_user_id'       => $failglobal['company_user_id'],
			'typedestiny_id'        => $typedestinyLB,
			'ammount'               => $ammountA,
			'calculationtype_id'    => $calculationtypeAIn,
			'currency_id'           => $currencyAIn,
			'validityto'            => $validitytoLB,
			'validityfromLB'        => $validityfromLB,
			'validation_expire'     => $validation_expire,
			'classsurcharge'        => $classsurcharger,
			'classorigin'           => $classdorigin,
			'classdestiny'          => $classddestination,
			'classtypedestiny'      => $classtypedestiny,
			'classcarrier'          => $classcarrier,
			'classcalculationtype'  => $classcalculationtype,
			'classammount'          => $classammount,
			'classcurrency'         => $classcurrency,
			'classvalidity'         => $classvalidity,
			'globalcharcountry'     => [],
		];

		//dd($arre);

		return view('ImportationGlobalchargersFcl.Body-Modal.saveFailToGood', compact('failglobal','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries','arre'));
	}

	// Carga la vista de failed y goog globalchargers
	public function showviewfailedandgood($id,$tab)
	{
		$countfailglobal = FailedGlobalcharge::where('account_id','=',$id)->count();
		$countgoodglobal = GlobalCharge::where('account_importation_globalcharge_id','=',$id)->count();
		$accounts = AccountImportationGlobalcharge::find($id);
		//dd('fallidos'.$countfailglobal);
		return view('ImportationGlobalchargersFcl.showview',compact('id','tab','countfailglobal','accounts','countgoodglobal'));
	}

	// LLena los datatables
	public function FailglobalchargeLoad($id,$selector){

		if($selector == 1){
			$account = AccountImportationGlobalcharge::find($id);
			$objharbor              = new Harbor();
			$objcurrency            = new Currency();
			$objcarrier             = new Carrier();
			$objsurcharge           = new Surcharge();
			$objtypedestiny         = new TypeDestiny();
			$objCalculationType     = new CalculationType();
			$typedestiny            = $objtypedestiny->all()->pluck('description','id');
			$surchargeSelect        = $objsurcharge->where('company_user_id','=', $account['company_user_id'])->pluck('name','id');
			$carrierSelect         = $objcarrier->all()->pluck('name','id');
			$harbor                = $objharbor->all()->pluck('display_name','id');
			$currency              = $objcurrency->all()->pluck('alphacode','id');
			$calculationtypeselect = $objCalculationType->all()->pluck('name','id');
			$failglobalcharges     = FailedGlobalcharge::where('account_id','=',$id)->get();
			$failglobalcoll = collect([]);
			foreach($failglobalcharges as $failglobalcharge){
				$classdorigin           =  'color:green';
				$classddestination      =  'color:green';
				$classtypedestiny       =  'color:green';
				$classcarrier           =  'color:green';
				$classsurcharger        =  'color:green';
				$classcalculationtype   =  'color:green';
				$classammount           =  'color:green';
				$classcurrency          =  'color:green';
				$classvalidityTo        =  'color:green';
				$classvalidityfrom      =  'color:green';
				$surchargeA         =  explode("_",$failglobalcharge['surcharge']);
				$originA            =  explode("_",$failglobalcharge['origin']);
				$destinationA       =  explode("_",$failglobalcharge['destiny']);
				$calculationtypeA   =  explode("_",$failglobalcharge['calculationtype']);
				$ammountA           =  explode("_",$failglobalcharge['ammount']);
				$currencyA          =  explode("_",$failglobalcharge['currency']);
				$carrierA           =  explode("_",$failglobalcharge['carrier']);
				$typedestinyA       =  explode("_",$failglobalcharge['typedestiny']);
				$validitytoA        =  explode("_",$failglobalcharge['validityto']);
				$validityfromA      =  explode("_",$failglobalcharge['validityfrom']);

				// -------------- VALIDITYTO -------------------------------------------------------------

				$validitytoC   = count($validitytoA);
				if($validitytoC <= 1){
					$validitytoA = $validitytoA[0];
				} else{
					$validitytoA = $validitytoA[0].' (error)';
					$classvalidityTo='color:red';
				}

				// -------------- VALIDITYTO -------------------------------------------------------------

				$validityfromC   = count($validityfromA);
				if($validityfromC <= 1){
					$validityfromA = $validityfromA[0];
				} else{
					$validityfromA = $validityfromA[0].' (error)';
					$classvalidityfrom='color:red';
				}

				// -------------- ORIGIN -------------------------------------------------------------


				if($failglobalcharge->differentiator == 1){
					$originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
						->first();
				} else if($failglobalcharge->differentiator == 2){
					$originOb  = Country::where('variation->type','like','%'.strtolower($originA[0]).'%')
						->first();
				}

				$originAIn = $originOb['id'];
				$originC   = count($originA);
				if($originC <= 1 && count($originAIn) == 1){
					$originA = $originOb['name'];
				} else{
					$originA = $originA[0].' (error)';
					$classdorigin='color:red';
				}

				// -------------- DESTINY ------------------------------------------------------------

				if($failglobalcharge->differentiator == 1){
					$destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
						->first();
				} else if($failglobalcharge->differentiator == 2){
					$destinationOb  = Country::where('variation->type','like','%'.strtolower($destinationA[0]).'%')
						->first();
				}

				$destinationAIn = $destinationOb['id'];
				$destinationC   = count($destinationA);
				if($destinationC <= 1 && count($destinationAIn) == 1){
					$destinationA = $destinationOb['name'];
				} else{
					$destinationA = $destinationA[0].' (error)';
					$classddestination='color:red';
				}

				// -------------- SURCHARGE -----------------------------------------------------------

				$surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',\Auth::user()->company_user_id)->first();
				$surcharAin  = $surchargeOb['id'];
				$surchargeC = count($surchargeA);
				if($surchargeC <= 1){
					$surchargeA = $surchargeA[0];
				}
				else{
					$surchargeA         = $surchargeA[0].' (error)';
					$classsurcharger    = 'color:red';
				}

				// -------------- CARRIER -------------------------------------------------------------
				$carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
				$carrAIn = $carrierOb['id'];
				$carrierC = count($carrierA);
				if($carrierC <= 1){
					$carrierA = $carrierA[0];
				}
				else{
					$carrierA       = $carrierA[0].' (error)';
					$classcarrier   ='color:red';
				}

				// -------------- CALCULATION TYPE ----------------------------------------------------
				$calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
				$calculationtypeAIn = $calculationtypeOb['id'];
				$calculationtypeC   = count($calculationtypeA);
				if($calculationtypeC <= 1){
					$calculationtypeA = $calculationtypeA[0];
				}
				else{
					$calculationtypeA       = $calculationtypeA[0].' (error)';
					$classcalculationtype   = 'color:red';
				}

				// -------------- AMMOUNT ------------------------------------------------------------
				$ammountC = count($ammountA);
				if($ammountC <= 1){
					$ammountA = $failglobalcharge->ammount;
				}
				else{
					$ammountA       = $ammountA[0].' (error)';
					$classammount   = 'color:red';
				}

				// -------------- CURRENCY ----------------------------------------------------------
				$currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
				$currencyAIn  = $currencyOb['id'];
				$currencyC    = count($currencyA);
				if($currencyC <= 1){
					$currencyA = $currencyA[0];
				}
				else{
					$currencyA      = $currencyA[0].' (error)';
					$classcurrency  = 'color:red';
				}
				// -------------- TYPE DESTINY -----------------------------------------------------
				//dd($failsurcharge['typedestiny_id']);
				$typedestinyobj    = TypeDestiny::where('description',$typedestinyA[0])->first();
				if(count($typedestinyA) <= 1){
					$typedestinyLB = $typedestinyobj['description'];
				}
				else{
					$typedestinyLB      = $typedestinyA[0].' (error)';
					$classcurrency  = 'color:red';
				}


				////////////////////////////////////////////////////////////////////////////////////
				$arreglo = [
					'id'                    => $failglobalcharge->id,
					'surchargelb'           => $surchargeA,
					'origin_portLb'         => $originA,
					'destiny_portLb'        => $destinationA,
					'carrierlb'             => $carrierA,
					'typedestinylb'         => $typedestinyLB,
					'ammount'               => $ammountA,
					'calculationtypelb'     => $calculationtypeA,
					'currencylb'            => $currencyA,
					'validitytolb'          => $validitytoA,
					'validityfromlb'        => $validityfromA,
					'classsurcharge'        => $classsurcharger,
					'classorigin'           => $classdorigin,
					'classdestiny'          => $classddestination,
					'classtypedestiny'      => $classtypedestiny,
					'classcarrier'          => $classcarrier,
					'classcalculationtype'  => $classcalculationtype,
					'classammount'          => $classammount,
					'classcurrency'         => $classcurrency,
					'classvalidityto'       => $classvalidityTo,
					'classvalidityfrom'     => $classvalidityfrom,
					'operation'             => 1
				];

				//dd($arreglo);
				$failglobalcoll->push($arreglo);

			}
			//dd($failsurchargecoll);
			return DataTables::of($failglobalcoll)->addColumn('action', function ( $failglobalcoll) {
				return '<a href="#" class="" onclick="showModalsavetoglobalcharge('.$failglobalcoll['id'].',1)"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Fail-global" data-id-failglobal="'.$failglobalcoll['id'].'" class=""><i class="la la-remove"></i></a>';
			})
				->editColumn('id', 'ID: {{$id}}')->toJson();

		}else if($selector == 2){

			$globalcharges = DB::select('call select_globalcharge('.$id.')');

			return DataTables::of($globalcharges)
				->editColumn('surchargelb', function ($globalcharges){ 
					return $globalcharges->surcharges;
				})
				->editColumn('origin_portLb', function ($globalcharges){ 
					if(empty($globalcharges->port_orig) != true){
						return $globalcharges->port_orig;
					} else if(empty($globalcharges->country_orig) != true) {
						return $globalcharges->country_orig; 
					}
				})
				->editColumn('destiny_portLb', function ($globalcharges){ 
					if(empty($globalcharges->port_dest) != true){
						return $globalcharges->port_dest;
					} else if(empty($globalcharges->country_dest) != true) {
						return $globalcharges->country_dest; 
					}
				})
				->editColumn('typedestinylb', function ($globalcharges){ 
					return $globalcharges->typedestiny;
				})
				->editColumn('calculationtypelb', function ($globalcharges){ 
					return $globalcharges->calculationtype;
				})
				->editColumn('currencylb', function ($globalcharges){ 
					return $globalcharges->currency;
				})
				->editColumn('carrierlb', function ($globalcharges){ 
					return $globalcharges->carrier;
				})
				->editColumn('validitytolb', function ($globalcharges){ 
					return $globalcharges->expire;
				})
				->editColumn('validityfromlb', function ($globalcharges){ 
					return $globalcharges->validity;
				})
				->addColumn('action', function ( $globalcharges) {
					return '<a href="#" class="" onclick="showModalsavetoglobalcharge('.$globalcharges->id.',2)"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-globalcharge" data-id-globalcharge="'.$globalcharges->id.'" class=""><i class="la la-remove"></i></a>';
				})
				->editColumn('id', 'ID: {{$id}}')->toJson();
		}
	}

	//Editar un global charge good -- precarga de body modal AJAX
	public function editGlobalChar($id){
		$objcarrier = new Carrier();
		$objharbor = new Harbor();
		$objcurrency = new Currency();
		$objtypedestiny = new TypeDestiny();
		$objcalculation = new CalculationType();
		$objsurcharge = new Surcharge();
		$countries = Country::pluck('name','id');

		$globalcharges = GlobalCharge::find($id);
		$calculationT = $objcalculation->all()->pluck('name','id');
		$typedestiny = $objtypedestiny->all()->pluck('description','id');
		$surcharge = $objsurcharge->where('company_user_id','=',$globalcharges['company_user_id'])->pluck('name','id');
		$harbor = $objharbor->all()->pluck('display_name','id');
		$carrier = $objcarrier->all()->pluck('name','id');
		$currency = $objcurrency->all()->pluck('alphacode','id');
		$validation_expire = $globalcharges->validity ." / ". $globalcharges->expire ;
		$globalcharges->setAttribute('validation_expire',$validation_expire);
		return view('ImportationGlobalchargersFcl.Body-Modal.edit', compact('globalcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
	}

	//Agregar Global Charge de fallido a bueno
	public function saveFailToGood(Request $request,$idFail){

		$failglobal                 = FailedGlobalcharge::find($idFail);
		$typerate                   =  $request->input('typeroute');
		$validation                 = explode('/',$request->validation_expire);

		if($typerate == 'port'){ //si es puerto verificamos si exite uno creado con country
			$typeplace = 'globalcharport';
		}elseif($typerate == 'country'){  //si es country verificamos si exite uno creado con puerto
			$typeplace = 'globalcharcountry';
		}

		$amountV             = $request->input('ammount');
		$surchargerV         = $request->input('surcharge_id');
		$typedestinyV        = $request->input('changetype');
		$calculationtypeV    = $request->input('calculationtype_id');
		$currencyV           = $request->input('currency_id');
		$company_userV       = $failglobal['company_user_id'];
		$carrier             = $request->input('carrier_id');

		$global = GlobalCharge::where('surcharge_id',$surchargerV)
			->where('typedestiny_id',$typedestinyV)
			->where('company_user_id',$company_userV)
			->where('calculationtype_id',$calculationtypeV)
			->where('ammount',$amountV)
			->where('validity',$validation[0])
			->where('expire',$validation[1])
			->where('currency_id',$currencyV)
			->has($typeplace)
			->first();

		if(count($global) == 0){
			$global                     = new GlobalCharge();
			$global->validity           = $validation[0];
			$global->expire             = $validation[1];
			$global->surcharge_id       = $surchargerV;
			$global->typedestiny_id     = $typedestinyV;
			$global->calculationtype_id = $calculationtypeV;
			$global->ammount            = $amountV;
			$global->currency_id        = $currencyV;
			$global->company_user_id    = $company_userV;
			$global->account_importation_globalcharge_id    = $failglobal['account_id'];
			$global->save();
		}

		$id = $global->id;

		if($typerate == 'port'){

			$port_orig = $request->input('port_orig');
			$port_dest = $request->input('port_dest');

			foreach($port_orig as  $orig => $valueorig)
			{
				foreach($port_dest as $dest => $valuedest)
				{
					$detailport = new GlobalCharPort();
					$detailport->port_orig          = $valueorig;
					$detailport->port_dest          = $valuedest;
					$detailport->typedestiny_id     = $request->input('changetype');
					$detailport->globalcharge_id    = $id;
					$detailport->save();
				}
			}

		}elseif($typerate == 'country'){

			$detailCountrytOrig =$request->input('country_orig');
			$detailCountryDest = $request->input('country_dest');

			foreach($detailCountrytOrig as $p => $valueC)
			{
				foreach($detailCountryDest as $dest => $valuedestC)
				{
					$detailcountry = new GlobalCharCountry();
					$detailcountry->country_orig = $valueC;
					$detailcountry->country_dest =  $valuedestC;
					$detailcountry->globalcharge()->associate($global);
					$detailcountry->save();
				}
			}
		}

		foreach($carrier as $key)
		{
			$find_carrier   = GlobalCharCarrier::where('carrier_id',$key)->where('globalcharge_id',$id)->get();
			if(count($find_carrier) == 0){
				$detailcarrier = new GlobalCharCarrier();
				$detailcarrier->carrier_id      = $key;
				$detailcarrier->globalcharge_id = $id;
				$detailcarrier->save();
			}
		}

		$failglobal->delete();

		$counfail = FailedGlobalcharge::where('account_id','=',$global->account_importation_globalcharge_id)->count();

		$request->session()->flash('message.nivel', 'success');
		$request->session()->flash('message.content', 'The Global Charge was updated from fail to good');

		if($counfail == 0){
			return redirect()->route('showview.globalcharge.fcl',[$global->account_importation_globalcharge_id,0]);
		}else {
			return redirect()->route('showview.globalcharge.fcl',[$global->account_importation_globalcharge_id,1]);
		}

	}

	//Actualiza el globalcharge good
	public function updateGlobalChar(Request $request, $id)
	{

		$objcarrier = new Carrier();
		$objharbor = new Harbor();
		$objcurrency = new Currency();
		$objcalculation = new CalculationType();
		$objsurcharge = new Surcharge();
		$objtypedestiny = new TypeDestiny();
		$harbor = $objharbor->all()->pluck('display_name','id');
		$carrier = $objcarrier->all()->pluck('name','id');
		$currency = $objcurrency->all()->pluck('alphacode','id');
		$calculationT = $objcalculation->all()->pluck('name','id');
		$typedestiny = $objtypedestiny->all()->pluck('description','id');

		$global = GlobalCharge::find($id);
		$validation = explode('/',$request->validation_expire);
		$global->validity = $validation[0];
		$global->expire = $validation[1];
		$global->surcharge_id = $request->input('surcharge_id');
		$global->typedestiny_id = $request->input('changetype');
		$global->calculationtype_id = $request->input('calculationtype_id');
		$global->ammount = $request->input('ammount');
		$global->currency_id = $request->input('currency_id');

		$carrier = $request->input('carrier_id');
		$deleteCarrier = GlobalCharCarrier::where("globalcharge_id",$id);
		$deleteCarrier->delete();
		$deletePort = GlobalCharPort::where("globalcharge_id",$id);
		$deletePort->delete();
		$deleteCountry = GlobalCharCountry::where("globalcharge_id",$id);
		$deleteCountry->delete();

		$typerate =  $request->input('typeroute');
		if($typerate == 'port'){
			$port_orig = $request->input('port_orig');
			$port_dest = $request->input('port_dest');
			foreach($port_orig as  $orig => $valueorig)
			{
				foreach($port_dest as $dest => $valuedest)
				{
					$detailport = new GlobalCharPort();
					$detailport->port_orig = $valueorig;
					$detailport->port_dest = $valuedest;
					$detailport->typedestiny_id = $request->input('changetype');
					$detailport->globalcharge_id = $id;
					$detailport->save();
				}
			}
		}elseif($typerate == 'country'){

			$detailCountrytOrig =$request->input('country_orig');
			$detailCountryDest = $request->input('country_dest');
			foreach($detailCountrytOrig as $p => $valueC)
			{
				foreach($detailCountryDest as $dest => $valuedestC)
				{
					$detailcountry = new GlobalCharCountry();
					$detailcountry->country_orig = $valueC;
					$detailcountry->country_dest =  $valuedestC;
					$detailcountry->globalcharge()->associate($global);
					$detailcountry->save();
				}
			}
		}

		foreach($carrier as $key)
		{
			$detailcarrier = new GlobalCharCarrier();
			$detailcarrier->carrier_id = $key;
			$detailcarrier->globalcharge_id = $id;
			$detailcarrier->save();
		}

		$global->update();

		$request->session()->flash('message.nivel', 'success');
		$request->session()->flash('message.content', 'The Global Charge was updated');
		return redirect()->route('showview.globalcharge.fcl',[$global->account_importation_globalcharge_id,0]);
	}

	// Elininar glog¿balcharger Good
	public function DestroyGlobalchargeG($id){
		try{
			$globalcharge = GlobalCharge::find($id);
			$globalcharge->delete();
			return 1;
		}catch(\Exception $e){
			return 2;
		}
	}

	// Elininar glog¿balcharger Fail
	public function DestroyGlobalchargeF($id){
		try{
			$globalcharge = FailedGlobalcharge::find($id);
			$globalcharge->forceDelete();
			return 1;
		}catch(\Exception $e){
			return 2;
		}
	}

	public function update(Request $request, $id)
	{
		//
	}

	public function destroy($id)
	{
		//
	}

	public function deleteAccounts($id,$select){
		try{
			$data = PrvValidation::AcountWithJob($id);
			if($data['bool'] == false){
				$account = AccountImportationGlobalcharge::with('FileTmp')->find($id);
				if(count($account)>0){
					if(count($account->FileTmp)>0){
						Storage::disk('UpLoadFile')->delete($account->FileTmp->name_file);
					}
					$account->delete();
				}
			}
			if($select == 1){
				return redirect()->route('ImportationGlobalchargeFcl.index');
			} elseif($select == 2){
				return response()->json(['success' => '1','jobAssociate' => $data['bool']]);			
			}
		} catch(\Exception $e){
			return response()->json(['success' => '2','jobAssociate' => false]);			
		}

	}

	public function Download(Request $request,$id){
		$account    = AccountImportationGlobalcharge::find($id);
		$time       = new \DateTime();
		$now        = $time->format('d-m-y');
		$company    = CompanyUser::find($account->company_user_id);
		$extObj     = new \SplFileInfo($account->namefile);
		$ext        = $extObj->getExtension();
		$name       = $account->id.'-'.$company->name.'_'.$now.'-GCFLC.'.$ext;
		if(empty($account->namefile) != true){
			try{
				return Storage::disk('s3_upload')->download('Account/Global-charges/FCL/'.$account->namefile,$name);
			} catch(\Exception $e){
				return Storage::disk('GCAccount')->download($account->namefile,$name);
			}
		} else {
			$request->session()->flash('message.nivel', 'danger');
			$request->session()->flash('message.content', 'The Global Charge File not exists');
			return redirect()->route('RequestsGlobalchargersFcl.index');
		}
	}

	public function indexAccount(){
		$account = AccountImportationGlobalcharge::with('companyuser')->get();
		return DataTables::of($account)
			->addColumn('status', function ( $account) {
				return  $account->status;

			})
			->addColumn('company_user_id', function ( $account) {
				return  $account->companyuser->name;
			})
			->addColumn('requestgc_id', function ( $account) {
				if(empty($account->requestgc_id) != true){
					return  $account->requestgc_id;
				} else {
					return 'Manual';
				}
			})
			->addColumn('action', function ( $account) {
				return '<a href="/ImportationGlobalchargesFcl/FailedGlobalchargers/'.$account->id.'/1" class="show"  title="Failed-Good" >
                            <samp class="la la-pencil-square-o" style="font-size:20px; color:#031B4E"></samp>
                        </a>
                        &nbsp;
                        &nbsp;
                        <a href="/ImportationGlobalchargesFcl/DownloadAccountgcfcl/'.$account->id.'" class="">
                            <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E" title="Download"></samp>
                        </a>
                        &nbsp; &nbsp; 
                        <a href="#" class="eliminaracount" data-id-acount="'.$account->id.'"  title="Delete" >
                            <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                        </a>';
			})
			->editColumn('id', '{{$id}}')->toJson();
	}


	public function testExcelImportation(){

		$var = 255;
		$var = floatval($var);

		dd($var);
	}


}
