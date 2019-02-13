<?php

namespace App\Http\Controllers;

use Excel;
use PrvHarbor;
use App\Harbor;
use App\Carrier;
use App\Currency;
use Carbon\Carbon;
use App\Surcharge;
use App\CompanyUser;
use App\TypeDestiny;
use App\CalculationType;
use App\FileTmpGlobalcharge;
use Illuminate\Http\Request;
use App\AccountImportationGlobalcharge;
use Illuminate\Support\Facades\Storage;

class ImportationGlobachargersFclController extends Controller
{


	// precarga la vista para importar rates o rates mas surchargers
	public function index()
	{
		$harbor         = Harbor::all()->pluck('display_name','id');
		$carrier        = Carrier::all()->pluck('name','id');
		$companysUser   = CompanyUser::all()->pluck('name','id');
		$typedestiny    = TypeDestiny::all()->pluck('description','id');
		return view('ImportationGlobalchargersFcl.index',compact('harbor','carrier','companysUser','typedestiny'));
	}

	// carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
	public function UploadFileNewContract(Request $request){
		//dd($request->all());
		$now = new \DateTime();
		$now = $now->format('dmY_His');
		$carrierVal     = $request->carrier;
		$typedestinyVal = $request->typedestiny;
		$destinyArr     = $request->destiny;
		$originArr      = $request->origin;
		$CompanyUserId  = $request->CompanyUserId;
		$carrierBol     = false;
		$destinyBol     = false;
		$originBol      = false;
		$typedestinyBol = false;
		$fortynorBol    = false;
		$fortyfiveBol   = false;
		$filebool       = false;

		$data           = collect([]);
		$typedestiny    = TypeDestiny::all()->pluck('description','id');
		$harbor         = harbor::all()->pluck('display_name','id');
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
		$filebool   = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

		if($filebool){
			$account   = new AccountImportationGlobalcharge();
			$account->name             = $request->name;
			$account->date             = $request->date;
			$account->status           = 'incomplete';
			$account->company_user_id  = $CompanyUserId;
			$account->save(); 

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
			0 => "20'",
			1 => "40'",
			2 => "40'HC",
			3 => "Calculation Type",
			4 => "Charge",
			5 => "Validity From",
			6 => "Validity To"
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

		/* si $statustypecurren es igual a 2, los currencys estan contenidos en la misma columna 
        con los valores, si es uno el currency viene en una colmna aparte        
        */

		$statustypecurren = $request->valuesCurrency;

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

		$coordenates = collect([]);

		ini_set('memory_limit', '1024M');

		Excel::selectSheetsByIndex(0)
			->Load(\Storage::disk('UpLoadFile')
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
			'existcarrier'      => $carrierBol,
			'carrier'           => $carrierVal,            
			'existtypedestiny'  => $typedestinyBol,
			'typedestiny'       => $typedestinyVal,
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
																				  'data',
																				  'carrier',
																				  'targetsArr',
																				  'account_id',
																				  'coordenates',
																				  'countTarges',
																				  'CompanyUserId',
																				  'statustypecurren',
																				  'typedestiny'));
	}


	public function create(Request $request)
	{
		$companyUserId = $request->CompanyUserId;
		$UserId =\Auth::user()->id;
		//dd($request->all());

		$requestobj         = $request->all();
		$NameFile           = $request->FileName;
		$companyUserIdVal   = $companyUserId;
		$errors = 0;
		$path = public_path(\Storage::disk('UpLoadFile')->url($NameFile));
		Excel::selectSheetsByIndex(0)
			->Load($path,function($reader) use($requestobj,$errors,$NameFile,$companyUserIdVal) {
				$reader->noHeading = true;
				//$reader->ignoreEmpty();

				$currency               = "Currency";
				$twenty                 = "20'";
				$forty                  = "40'";
				$fortyhc                = "40'HC";
				$fortynor               = "40'NOR";
				$fortyfive              = "45'";
				$origin                 = "origin";
				$originExc              = "Origin";
				$destiny                = "destiny";
				$destinyExc             = "Destiny";
				$carrier                = "Carrier";
				$CalculationType        = "Calculation_Type";
				$Charge                 = "Charge";
				$statustypecurren       = "statustypecurren";
				$typedestiny            = "Type_Destiny";
				$validityfrom           = "Validity_From";
				$validityto             = "Validity_To";

				$account_id             = $requestobj['account_id'];
				$statusexistfortynor    = $requestobj['existfortynor'];
				$statusexistfortyfive   = $requestobj['existfortyfive'];

				$caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

				$ratescollection         = collect([]);
				$ratesFailcollection     = collect([]);
				$surcharcollection       = collect([]);
				$surcharFailcollection   = collect([]);


				$i = 1;
				$falli =0;
				foreach($reader->get() as $read){
					$carrierVal          = '';
					$typedestinyVal      = '';
					$originVal           = '';
					$destinyVal          = '';
					$origenFL            = '';
					$destinyFL           = '';
					$currencyVal         = '';
					$currencyValtwen     = '';
					$currencyValfor      = '';
					$currencyValforHC    = '';
					$currencyValfornor   = '';
					$currencyValforfive  = '';
					$calculationtypeVal  = '';
					$surchargelist       = '';
					$surchargeVal        = '';
					$validityfromVal		= '';
					$validitytoVal			= '';
					$account_idVal       = $account_id;

					$calculationtypeValfail  = '';
					$currencResultwen        = '';
					$currencResulfor         = '';
					$currencResulforhc       = '';
					$currencResulfornor      = '';
					$currencResulforfive     = '';
					$currencResul            = '';

					$twentyArr;
					$fortyArr;
					$fortyhcArr;
					$fortynorArr;
					$fortyfiveArr;
					$twentyVal           = '';
					$fortyVal            = '';
					$fortyhcVal          = '';
					$fortynorVal         = '';
					$fortyfiveVal        = '';

					$originBol               = false;
					$origExiBol              = false;
					$destinyBol              = false;
					$destiExitBol            = false;
					$typedestinyExitBol      = false;
					$typedestinyBol          = false;
					$carriExitBol            = false;
					$curreExiBol             = false;
					$curreExitBol            = false;
					$curreExitwenBol         = false;
					$curreExiforBol          = false;
					$curreExiforHCBol        = false;
					$curreExifornorBol       = false;
					$curreExiforfiveBol      = false;
					$twentyExiBol            = false;
					$fortyExiBol             = false;
					$fortyhcExiBol           = false;
					$fortynorExiBol          = false;
					$fortyfiveExiBol         = false;
					$carriBol                = false;
					$calculationtypeExiBol   = false;
					$variantecurrency        = false;
					$typeExiBol              = false;
					$twentyArrBol            = false;
					$fortyArrBol             = false;
					$fortyhcArrBol           = false;
					$fortynorArrBol          = false;
					$fortyfiveArrBol         = false;
					$validityfromExiBol		 = false;
					$validitytoExiBol		 	 = false;
					$values                  = true;

					//--------------------------------------------------------
					if($i != 1){

						//--------------- Type Destiny ------------------------------------------------------------

						if($requestobj['existtypedestiny'] == 1){
							$typedestinyExitBol = true;
							$typedestinyBol     = true;
							$typedestinyVal     = $requestobj['typedestiny']; // es cuando se indica que no posee type destiny 
						} else {
							$typedestinyVal      = $read[$requestobj[$typedestiny]]; // cuando el type destiny  existe en el excel
							$typedestinyResul    = str_replace($caracteres,'',$typedestinyVal);
							$typedestinyobj      = TypeDestiny::where('description','=',$typedestinyResul)->first();
							if(empty($typedestinyobj->id) != true){
								$typedestinyExitBol = true;
								$typedestinyVal = $typedestinyobj->id;
							}else{
								$typedestinyVal = $typedestinyVal.'_E_E';
							}
						}

						//--------------- CARRIER -----------------------------------------------------------------
						if($requestobj['existcarrier'] == 1){
							$carriExitBol = true;
							$carriBol     = true;
							$carrierVal = $requestobj['carrier']; // cuando se indica que no posee carrier 
						} else {
							$carrierVal = $read[$requestobj['Carrier']]; // cuando el carrier existe en el excel
							$carrierResul = str_replace($caracteres,'',$carrierVal);
							$carrier = Carrier::where('name','=',$carrierResul)->first();
							if(empty($carrier->id) != true){
								$carriExitBol = true;
								$carrierVal = $carrier->id;
							}else{
								$carrierVal = $carrierVal.'_E_E';
							}
						}

						//--------------- ORIGEN MULTIPLE O SIMPLE ------------------------------------------------

						if($requestobj['existorigin'] == 1){
							$originBol = true;
							$origExiBol = true; //segundo boolean para verificar campos errados
							$randons = $requestobj[$origin];
						} else {
							$originVal = $read[$requestobj[$originExc]];// hacer validacion de puerto en DB
							$resultadoPortOri = PrvHarbor::get_harbor($originVal);
							if($resultadoPortOri['boolean']){
								$origExiBol = true;    
							}
							$originVal  = $resultadoPortOri['puerto'];

						}
						//---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
						if($requestobj['existdestiny'] == 1){
							$destinyBol = true;
							$destiExitBol = true; //segundo boolean para verificar campos errados
							$randons = $requestobj[$destiny];
						} else {
							$destinyVal = $read[$requestobj[$destinyExc]];// hacer validacion de puerto en DB
							$resultadoPortDes = PrvHarbor::get_harbor($destinyVal);
							if($resultadoPortDes['boolean']){
								$destiExitBol = true;    
							}
							$destinyVal  = $resultadoPortDes['puerto'];
						}

						//---------------- CURRENCY VALUES ------------------------------------------------------

						if(empty($read[$requestobj[$twenty]]) != true){ //Primero valido si el campo viene lleno, en caso contrario lo lleno manuelamene
							$twentyArrBol = true;
							$twentyArr      = explode(' ',trim($read[$requestobj[$twenty]]));
						} else {
							$twentyArr = ['0']; 
						}

						if(empty($read[$requestobj[$forty]]) != true){
							$fortyArrBol = true;
							$fortyArr       = explode(' ',trim($read[$requestobj[$forty]]));
						} else {
							$fortyArr = ['0'];
						}

						if(empty($read[$requestobj[$fortyhc]]) != true){
							$fortyhcArrBol  = true;
							$fortyhcArr     = explode(' ',trim($read[$requestobj[$fortyhc]]));
						} else {
							$fortyhcArr = ['0'];
						}


						if($statusexistfortynor == 1){ // si el selecionado en la vista que posee el campo 40'NOr o 45' hacemos lo mismo

							if(empty($read[$requestobj[$fortynor]]) != true){
								$fortynorArrBol  = true;
								$fortynorArr     = explode(' ',trim($read[$requestobj[$fortynor]]));
							} else {
								$fortynorArr = ['0'];
							}

						}

						if($statusexistfortyfive == 1){

							if(empty($read[$requestobj[$fortyfive]]) != true){
								$fortyfiveArrBol  = true;
								$fortyfiveArr     = explode(' ',trim($read[$requestobj[$fortyfive]]));
							} else {
								$fortyfiveArr = ['0'];
							}

						}

						// ----------------------- Validacion de comapos vacios--------------------------------------
						if($requestobj[$statustypecurren] == 2){ // se verifica si el valor viene junto con el currency para no llenar el valor del currency arreglo[posicion 2] -> ($twentyArr[1])
							// ------- 20'
							if($twentyArrBol == false){ // Cargamos el arreglo[1] para que el Rate se pueda registrar, y para que se validen los PER_DOC

								if($fortyArrBol == true){
									array_push($twentyArr,$fortyArr[1]);

								} elseif($fortyhcArrBol == true){
									array_push($twentyArr,$fortyhcArr[1]);

								} elseif($fortynorArrBol == true && $statusexistfortynor == 1){
									array_push($twentyArr,$fortynorArr[1]);

								} elseif($fortyfiveArrBol == true && $statusexistfortyfive == 1){
									array_push($twentyArr,$fortyfiveArr[1]);

								} else {
									array_push($twentyArr,'');
								}
							}

							// ------- 40'
							if($fortyArrBol == false){ // Cargamos el arreglo[1] para que el Rate se pueda registrar, y para que se validen los PER_DOC

								if($twentyArrBol == true){
									array_push($fortyArr,$twentyArr[1]);

								} elseif($fortyhcArrBol == true){
									array_push($fortyArr,$fortyhcArr[1]);

								} elseif($fortynorArrBol == true && $statusexistfortynor == 1){
									array_push($fortyArr,$fortynorArr[1]);

								} elseif($fortyfiveArrBol == true && $statusexistfortyfive == 1){
									array_push($fortyArr,$fortyfiveArr[1]);
								} else {
									array_push($fortyArr,'');
								}
							}

							// ------- 40'HC
							if($fortyhcArrBol == false){ // Cargamos el arreglo[1] para que el Rate se pueda registrar, y para que se validen los PER_DOC

								if($twentyArrBol == true){
									array_push($fortyhcArr,$twentyArr[1]);

								} elseif($fortyArrBol == true){
									array_push($fortyhcArr,$fortyArr[1]);

								} elseif($fortynorArrBol == true && $statusexistfortynor == 1){
									array_push($fortyhcArr,$fortynorArr[1]);

								} elseif($fortyfiveArrBol == true && $statusexistfortyfive == 1){
									array_push($fortyhcArr,$fortyfiveArr[1]);
								} else {
									array_push($fortyhcArr,'');
								}
							}

							// ------- 40'NOR
							if($fortynorArrBol == false && $statusexistfortynor == 1){ // Cargamos el arreglo[1] para que el Rate se pueda registrar, y para que se validen los PER_DOC

								if($twentyArrBol == true){
									array_push($fortynorArr,$twentyArr[1]);

								} elseif($fortyArrBol == true){
									array_push($fortynorArr,$fortyArr[1]);

								} elseif($fortyhcArrBol == true){
									array_push($fortynorArr,$fortyhcArr[1]);

								} elseif($fortyfiveArrBol == true && $statusexistfortyfive == 1){
									array_push($fortynorArr,$fortyfiveArr[1]);
								} else {
									array_push($fortynorArr,'');
								}
							}

							// ------- 45'
							if($fortyfiveArrBol == false && $statusexistfortyfive == 1){ // Cargamos el arreglo[1] para que el Rate se pueda registrar, y para que se validen los PER_DOC

								if($twentyArrBol == true){
									array_push($fortyfiveArr,$twentyArr[1]);

								} elseif($fortyArrBol == true){
									array_push($fortyfiveArr,$fortyArr[1]);

								} elseif($fortyhcArrBol == true){
									array_push($fortyfiveArr,$fortyhcArr[1]);

								} elseif($fortynorArrBol == true && $statusexistfortynor == 1){
									array_push($fortyfiveArr,$fortynorArr[1]);
								} else {
									array_push($fortyfiveArr,'');
								}
							}
						}

						//---------------- 20' ------------------------------------------------------------------

						if(empty($twentyArr[0]) != true || (int)$twentyArr[0] == 0){
							$twentyExiBol = true;
							$twentyVal   = (int)$twentyArr[0];
						}  else{
							$twentyVal = $twentyArr[0].'_E_E';
						}

						//----------------- 40' -----------------------------------------------------------------

						if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
							$fortyExiBol = true;
							$fortyVal   = (int)$fortyArr[0];
						}  else{
							$fortyVal = $fortyArr[0].'_E_E';
						}

						//----------------- 40'HC --------------------------------------------------------------

						if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
							$fortyhcExiBol = true;
							$fortyhcVal   = (int)$fortyhcArr[0];
						}   else{
							$fortyhcVal = $fortyhcArr[0].'_E_E';
						}

						//----------------- 40'NOR -------------------------------------------------------------
						if($statusexistfortynor == 1){

							if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
								$fortynorExiBol = true;
								$fortynorVal    = (int)$fortynorArr[0];
							} else{
								$fortynorVal = $fortynorArr[0].'_E_E';
							}
						} else {
							$fortynorExiBol = true;
							$fortynorVal = 0;
						}

						//----------------- 45' ----------------------------------------------------------------
						if($statusexistfortyfive == 1){
							if(empty($fortyfiveArr[0]) != true || (int)$fortyfiveArr[0] == 0){
								$fortyfiveExiBol = true;
								$fortyfiveVal    = (int)$fortyfiveArr[0];
							} else{
								$fortyfiveVal = $fortyfiveArr[0].'_E_E';
							}
						} else {
							$fortyfiveExiBol = true;
							$fortyfiveVal = 0;
						}

						if($twentyVal == 0
							&& $fortyVal == 0
							&& $fortyhcVal == 0
							&& $fortynorVal == 0
							&& $fortyfiveVal == 0){
							$values = false;
						}

						//---------------- CURRENCY ------------------------------------------------------------

						if($requestobj[$statustypecurren] == 2){ // se verifica si el valor viene junto con el currency

							// cargar  columna con el  valor y currency  juntos, se descompone

							//---------------- CURRENCY 20' + value ---------------------------------------------

							if(count($twentyArr) > 1){
								$currencResultwen = str_replace($caracteres,'',$twentyArr[1]);
							} else {
								$currencResultwen = '';
							}

							$currenctwen = Currency::where('alphacode','=',$currencResultwen)->first();

							if(empty($currenctwen->id) != true){
								$curreExitwenBol = true;
								$currencyValtwen =  $currenctwen->id;
							} else{
								if(count($twentyArr) > 1){
									$currencyValtwen = $twentyArr[1].'_E_E';
								} else{
									$currencyValtwen = '_E_E';
								}
							}

							//---------------- CURRENCY 40'------------------------------------------------------

							if(count($fortyArr) > 1){
								$currencResulfor = str_replace($caracteres,'',$fortyArr[1]);
							} else{
								$currencResulfor = '';
							}

							$currencfor = Currency::where('alphacode','=',$currencResulfor)->first();

							if(empty($currencfor->id) != true){
								$curreExiforBol = true;
								$currencyValfor =  $currencfor->id;
							} else{
								if(count($fortyArr) > 1){
									$currencyValfor = $fortyArr[1].'_E_E';
								} else {
									$currencyValfor = '_E_E';
								}
							}

							//---------------- CURRENCY 40'HC----------------------------------------------------

							if(count($fortyhcArr) > 1){
								$currencResulforhc = str_replace($caracteres,'',$fortyhcArr[1]);
							} else {
								$currencResulforhc = '';
							}

							$currencforhc = Currency::where('alphacode','=',$currencResulforhc)->first();

							if(empty($currencforhc->id) != true){
								$curreExiforHCBol = true;
								$currencyValforHC =  $currencforhc->id;
							}
							else{
								if(count($fortyhcArr) > 1){
									$currencyValforHC = $fortyhcArr[1].'_E_E';
								} else{
									$currencyValforHC = '';
								}
							}

							//---------------- CURRENCY 40'NOR -------------------------------------------------
							if($statusexistfortynor == 1){
								if(count($fortynorArr) > 1){
									$currencResulfornor = str_replace($caracteres,'',$fortynorArr[1]);
								} else {
									$currencResulfornor = '';
								}

								$currencfornor = Currency::where('alphacode','=',$currencResulfornor)->first();

								if(empty($currencfornor->id) != true){
									$curreExifornorBol = true;
									$currencyValfornor =  $currencfornor->id;
								} else{
									if(count($fortynorArr) > 1){
										$currencyValfornor = $fortynorArr[1].'_E_E';
									} else{
										$currencyValfornor = '';
									}
								} 
							} else {
								if($curreExitwenBol == true){                                    
									$currencyValfornor = $currencyValtwen;
									$curreExifornorBol = true;
								} else if($curreExiforBol == true){
									$currencyValfornor = $currencyValfor;
									$curreExifornorBol = true;
								} else if($curreExiforHCBol == true){
									$currencyValfornor = $currencyValforHC;
									$curreExifornorBol = true;
								} else {
									$currencyValfornor = '_E_E';
								}
							}

							//---------------- CURRENCY 45  ----------------------------------------------------
							if($statusexistfortyfive == 1){
								if(count($fortyfiveArr) > 1){
									$currencResulforfive = str_replace($caracteres,'',$fortyfiveArr[1]);
								} else {
									$currencResulforfive = '';
								}

								$currencforfive = Currency::where('alphacode','=',$currencResulforfive)->first();

								if(empty($currencforfive->id) != true){
									$curreExiforfiveBol = true;
									$currencyValforfive =  $currencforfive->id;
								}
								else{
									if(count($fortyfiveArr) > 1){
										$currencyValforfive = $fortyfiveArr[1].'_E_E';
									} else{
										$currencyValforfive = '';
									}
								}
							} else {
								if($curreExitwenBol == true){                                    
									$currencyValforfive = $currencyValtwen;
									$curreExiforfiveBol = true;
								} else if($curreExiforBol == true){
									$currencyValforfive = $currencyValfor;
									$curreExiforfiveBol = true;
								} else if($curreExiforHCBol == true){
									$currencyValforfive = $currencyValforHC;
									$curreExiforfiveBol = true;
								} else {
									$currencyValforfive = '_E_E';
								}

							}

							if($curreExitwenBol == true && $curreExiforBol == true 
								&& $curreExiforHCBol == true && $curreExifornorBol == true 
								&& $curreExiforfiveBol == true){
								$variantecurrency = true;
							}

						} else {

							if(empty($read[$requestobj[$currency]]) != true){
								$currencResul= str_replace($caracteres,'',$read[$requestobj[$currency]]);
								$currenc = Currency::where('alphacode','=',$currencResul)->first();
								if(empty($currenc->id) != true){    
									$curreExitBol = true;
									$currencyVal =  $currenc->id;
								} else{
									$currencyVal = $read[$requestobj[$currency]].'_E_E';                                    
								}
							}
							else{
								$currencyVal = $read[$requestobj[$currency]].'_E_E';
							}

							if($curreExitBol == true ){
								$variantecurrency = true;
							}
						}

						//------------------ CALCULATION TYPE ---------------------------------------------------
						$calculationvalvaration = '';
						if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_DOC') == 0){
							$calculationvalvaration = 'Per Shipment';
						} else if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_CONTAINER') == 0){
							$calculationvalvaration = 'Per Container';
						} else if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_TON') == 0){
							$calculationvalvaration = 'Per TON';
						} else if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_BL') == 0){
							$calculationvalvaration = 'Per BL';
						} else{
							$calculationvalvaration = $read[$requestobj[$CalculationType]];
						}

						$calculationtype = CalculationType::where('name','=',$calculationvalvaration)->first();
						if(empty($calculationtype) != true){
							$calculationtypeExiBol = true;
							$calculationtypeVal = $calculationtype['id'];
						} else{
							$calculationtypeVal = $read[$requestobj[$CalculationType]].'_E_E';
						}

						//------------------ TYPE ---------------------------------------------------------------

						if(empty($read[$requestobj[$Charge]]) != true){
							$typeExiBol = true;

							$surchargelist = Surcharge::where('name','=', $read[$requestobj[$Charge]])
								->where('company_user_id','=', $companyUserIdVal)
								->first();
							if(empty($surchargelist) != true){
								$surchargeVal = $surchargelist['id'];
							} 	else{
								$companyUserId = $companyUserIdVal;
								$surchargelist = Surcharge::create([
									'name'              => $read[$requestobj[$Charge]],
									'description'       => $read[$requestobj[$Charge]],
									'company_user_id'   => $companyUserId
								]);
								$surchargeVal = $surchargelist->id;
							}

						} else {
							$surchargeVal = $read[$requestobj[$Charge]].'_E_E';
						}

						//------------------ VALIDITY FROM ------------------------------------------------------
						$validityfromVal = $read[$requestobj[$validityfrom]];

						try{
							$validityfromVal = Carbon::parse($validityfromVal)->format('Y-m-d');
							$validityfromExiBol = true;
						} catch (\Exception $err){
							$validityfromVal = $validityfromVal.'_E_E';
						}


						//------------------ VALIDITY TO --------------------------------------------------------				
						$validitytoVal = $read[$requestobj[$validityto]];

						try{
							$validitytoVal = Carbon::parse($validitytoVal)->format('Y-m-d');
							$validitytoExiBol = true;
						} catch (\Exception $err){
							$validitytoVal = $validitytoVal.'_E_E';
						}


						//////////////////////////////////////////////////////////////////////////////////////////////////////

						$prueba = collect([]);

						$prueba = [
							'$carriExitBol'           => $carriExitBol,
							'$origExiBol'             => $origExiBol,
							'$destiExitBol'           => $destiExitBol,
							'$twentyExiBol'           => $twentyExiBol,
							'$fortyExiBol'            => $fortyExiBol,
							'$fortyhcExiBol'          => $fortyhcExiBol,
							'$fortynorExiBol'         => $fortynorExiBol,
							'$fortyfiveExiBol'        => $fortyfiveExiBol,
							'$calculationtypeExiBol'  => $calculationtypeExiBol,
							'$variantecurrency'       => $variantecurrency,
							'$typeExiBol'             => $typeExiBol,
							'$values'                 => $values,
							'$carrierVal'             => $carrierVal,
							'$originVal'              => $originVal,
							'$destinyVal'             => $destinyVal,
							'$currencyVal'            => $currencyVal,
							'$currencyValtwen'        => $currencyValfor,
							'$currencyValfor'         => $currencyValfor,
							'$currencyValforHC'       => $currencyValforHC,
							'$currencyValfornor'      => $currencyValfornor,
							'$currencyValforfive'     => $currencyValforfive,
							'$calculationtypeVal'     => $calculationtypeVal,
							'$surchargeVal'           => $surchargeVal,
							'$twentyArr'              => $twentyArr,
							'$fortyArr'               => $fortyArr,
							'$fortyhcArr'             => $fortyhcArr,                 
							'$twentyVal'              => $twentyVal,
							'$fortyVal'               => $fortyVal,
							'$fortyhcVal'             => $fortyhcVal,
							'$fortynorVal'            => $fortynorVal,
							'$fortyfiveVal'           => $fortyfiveVal,
							'$validityfromVal'        => $validityfromVal,
							'$validityfromExiBol'     => $validityfromExiBol,
							'$validitytoVal'          => $validitytoVal,
							'$validitytoExiBol'       => $validitytoExiBol
						];

						if($statusexistfortynor == 1){
							$prueba['$fortynorArr'] = $fortynorArr;
						}

						if($statusexistfortyfive == 1){
							$prueba['$fortyfiveArr'] = $fortyfiveArr;
						}

						dd($prueba);

						if($carriExitBol            	== true
							&& $origExiBol           	== true
							&& $destiExitBol         	== true
							&& $twentyExiBol         	== true
							&& $fortyExiBol          	== true
							&& $fortyhcExiBol        	== true
							&& $fortynorExiBol       	== true
							&& $fortyfiveExiBol      	== true
							&& $calculationtypeExiBol 	== true
							&& $variantecurrency     	== true
							&& $typeExiBol           	== true
							&& $typedestinyExitBol   	== true
							&& $values 						== true ){


							if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_CONTAINER') == 0){
								//dd($read[$request->$twenty]);
								// se verifica si los valores son iguales 
								if($statusexistfortynor == 1){
									$fortynorif =  $read[$requestobj[$fortynor]];
								} else {
									$fortynorif = $read[$requestobj[$twenty]];
								}

								if($statusexistfortyfive == 1){ 
									$fortyfiveif = $read[$requestobj[$fortyfive]];
								}else {
									$fortyfiveif = $read[$requestobj[$twenty]];
								}

								if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
									$read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
									$read[$requestobj[$fortyhc]] == $fortynorif &&
									$fortynorif == $fortyfiveif){

									// evaluamos si viene el valor con el currency juntos

									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValtwen;
									}

									$ammount = $twentyVal;
									if($ammount != 0){
										$globalChargeArreG = GlobalCharge::create([ // tabla GlobalCharge
											'surcharge_id'       						=> $surchargeVal,
											'typedestiny_id'     						=> $typedestinyVal,
											'account_importation_globalcharge_id'  => $account_idVal,
											'company_user_id'    						=> $companyUserIdVal,
											'calculationtype_id' 						=> $calculationtypeVal,
											'ammount'            						=> $ammount,
											'currency_id'        						=> $currencyVal
										]);

										//---------------------------------- CAMBIAR POR ID -----------------------------------------------------------
										GlobalCharCarrier::create([ // tabla GlobalCharCarrier
											'carrier_id'      => $carrierVal,
											'globalcharge_id' => $globalChargeArreG->id
										]);
										//-------------------------------------------------------------------------------------------------------------

										if($originBol == true || $destinyBol == true){
											foreach($randons as  $rando){
												//insert por arreglo de puerto
												if($originBol == true ){
													$originVal = $rando;
												} else {
													$destinyVal = $rando;
												}

												//---------------------------------- CAMBIAR POR ID -------------------------------

												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeArreG->id
												]);
												//---------------------------------------------------------------------------------

											} 
										}else {
											// fila por puerto, sin expecificar origen ni destino manualmente
											GlobalCharPort::create([ // tabla GlobalCharPort
												'port_orig'      	=> $originVal,
												'port_dest'      	=> $destinyVal,
												'typedestiny_id' 	=> $typedestinyVal,
												'globalcharge_id' => $globalChargeArreG->id
											]);
										}
										//echo $i;
										//dd($globalChargeArreG);
									}
								} else {
									// dd('llega No iguales');
									// se crea un registro por cada carga o valor
									// se valida si el currency viene junto con el valor

									if($requestobj[$statustypecurren] == 2){
										// cargar valor y currency  juntos, se trae la descomposicion
										// ----------------------- CARGA 20' -------------------------------------------
										if($twentyVal != 0){
											$globalChargeTWArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 2,
												'ammount'            						=> $twentyVal,
												'currency_id'        						=> $currencyValtwen
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeTWArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeTWArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeTWArreG->id
												]);
											}
										}
										//---------------------- CARGA 40' ----------------------------------------------------

										if($fortyVal != 0){
											$globalChargeFORArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 1,
												'ammount'            						=> $fortyVal,
												'currency_id'        						=> $currencyValfor
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORArreG->id
													]);

												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORArreG->id
												]);

											}
										}

										// --------------------- CARGA 40'HC --------------------------------------------------

										if($fortyhcVal != 0){
											$globalChargeFORHCArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 3,
												'ammount'            						=> $fortyhcVal,
												'currency_id'        						=> $currencyValforHC
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORHCArreG->id
											]);


											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORHCArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORHCArreG->id
												]);
											}

											//echo $i;
											//dd($globalChargeFORHCArreG);
										}

										// --------------------- CARGA 40'NOR -------------------------------------------------

										if($fortynorVal != 0){
											$globalChargeFORNORArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 7,
												'ammount'            						=> $fortynorVal,
												'currency_id'        						=> $currencyValfornor
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORNORArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORNORArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORNORArreG->id
												]);
											}

											//echo $i;
											//dd($globalChargeFORNORArreG);
										}

										// --------------------- CARGA 45' ----------------------------------------------------

										if($fortyfiveVal != 0){

											$globalChargeFORfiveArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 8,
												'ammount'            						=> $fortyfiveVal,
												'currency_id'        						=> $currencyValforfive
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORfiveArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORfiveArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORfiveArreG->id
												]);
											}

											//echo $i;
											//dd($globalChargeFORfiveArreG);
										}

										//---------------------
									} else{

										// cargar el currency ya descompuesto, ahora es un solo registro (currency ) de los tres campos que existen

										// ----------------------- CARGA 20' -------------------------------------------

										if($twentyVal != 0){
											$globalChargeTWArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 2,
												'ammount'            						=> $twentyVal,
												'currency_id'        						=> $currencyVal
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeTWArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeTWArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeTWArreG->id
												]);
											}
										}

										//---------------------- CARGA 40' -----------------------------------------------

										if($fortyVal != 0){
											$globalChargeFORArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 1,
												'ammount'            						=> $fortyVal,
												'currency_id'        						=> $currencyVal
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORArreG->id
												]);
											}
										}

										// --------------------- CARGA 40'HC ---------------------------------------------

										if($fortyhcVal != 0){

											$globalChargeFORHCArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 3,
												'ammount'            						=> $fortyhcVal,
												'currency_id'        						=> $currencyVal
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORHCArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORHCArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORHCArreG->id
												]);
											}
											//echo $i;
											//dd($globalChargeFORHCArreG);
										}

										// --------------------- CARGA 40'NOR --------------------------------------------

										if($fortynorVal != 0){
											$globalChargeFORNORArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 7,
												'ammount'            						=> $fortynorVal,
												'currency_id'        						=> $currencyVal
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORNORArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORNORArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORNORArreG->id
												]);
											}
											//echo $i;
											//dd($globalChargeFORNORArreG);
										}

										// --------------------- CARGA 45' -----------------------------------------------

										if($fortyfiveVal != 0){
											$globalChargeFORfiveArreG = GlobalCharge::create([ // tabla GlobalCharge
												'surcharge_id'       						=> $surchargeVal,
												'typedestiny_id'     						=> $typedestinyVal,
												'account_importation_globalcharge_id'  => $account_idVal,
												'company_user_id'    						=> $companyUserIdVal,
												'calculationtype_id' 						=> 8,
												'ammount'            						=> $fortyfiveVal,
												'currency_id'        						=> $currencyVal
											]);

											GlobalCharCarrier::create([ // tabla GlobalCharCarrier
												'carrier_id'      => $carrierVal,
												'globalcharge_id' => $globalChargeFORfiveArreG->id
											]);

											if($originBol == true || $destinyBol == true){
												foreach($randons as  $rando){
													//insert por arreglo de puerto
													if($originBol == true ){
														$originVal = $rando;
													} else {
														$destinyVal = $rando;
													}

													GlobalCharPort::create([ // tabla GlobalCharPort
														'port_orig'      	=> $originVal,
														'port_dest'      	=> $destinyVal,
														'typedestiny_id' 	=> $typedestinyVal,
														'globalcharge_id' => $globalChargeFORfiveArreG->id
													]);
												} 

											} else {
												// fila por puerto, sin expecificar origen ni destino manualmente
												GlobalCharPort::create([ // tabla GlobalCharPort
													'port_orig'      	=> $originVal,
													'port_dest'      	=> $destinyVal,
													'typedestiny_id' 	=> $typedestinyVal,
													'globalcharge_id' => $globalChargeFORfiveArreG->id
												]);
											}
											//echo $i;
											//dd($globalChargeFORfiveArreG);
										}
										//_____-----
									}

								}

							} 
							else if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_DOC') == 0){
								//per_shipment
								if($twentyVal != 0){
									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValtwen;
									} 
									$ammount = $twentyVal;

								} else if ($fortyVal != 0){
									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValfor;
									} 
									$ammount = $fortyVal;

								}else if ($fortyhcVal != 0){

									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValforHC;
									} 
									$ammount = $fortyhcVal;

								}else if ($fortynorVal != 0){
									if($statusexistfortynor == 1){
										if($requestobj[$statustypecurren] == 2){
											$currencyVal = $currencyValfornor;
										} 
									}
									$ammount = $fortynorVal;

								}else if ($fortyfiveVal != 0){
									if($statusexistfortyfive == 1){
										if($requestobj[$statustypecurren] == 2){
											$currencyVal = $currencyValforfive;
										} 
									}
									$ammount = $fortyfiveVal;
								}


								if($ammount != 0){

									$globalChargePERArreG = GlobalCharge::create([ // tabla GlobalCharge
										'surcharge_id'       						=> $surchargeVal,
										'typedestiny_id'     						=> $typedestinyVal,
										'account_importation_globalcharge_id'  => $account_idVal,
										'company_user_id'    						=> $companyUserIdVal,
										'calculationtype_id' 						=> $calculationtypeVal,
										'ammount'            						=> $ammount,
										'currency_id'        						=> $currencyVal
									]);

									GlobalCharCarrier::create([ // tabla GlobalCharCarrier
										'carrier_id'      => $carrierVal,
										'globalcharge_id' => $globalChargePERArreG->id
									]);

									if($originBol == true || $destinyBol == true){
										foreach($randons as  $rando){
											//insert por arreglo de puerto
											if($originBol == true ){
												$originVal = $rando;
											} else {
												$destinyVal = $rando;
											}

											GlobalCharPort::create([ // tabla GlobalCharPort
												'port_orig'      	=> $originVal,
												'port_dest'      	=> $destinyVal,
												'typedestiny_id' 	=> $typedestinyVal,
												'globalcharge_id' => $globalChargePERArreG->id
											]);
										} 

									} else {
										// fila por puerto, sin expecificar origen ni destino manualmente
										GlobalCharPort::create([ // tabla GlobalCharPort
											'port_orig'      	=> $originVal,
											'port_dest'      	=> $destinyVal,
											'typedestiny_id' 	=> $typedestinyVal,
											'globalcharge_id' => $globalChargePERArreG->id
										]);
									}
								}
								// echo $i;
								// dd($globalChargePERArreG);
							}
							else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_BL') == 0){
								//per_shipment
								if($twentyVal != 0){
									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValtwen;
									} 
									$ammount = $twentyVal;

								} else if ($fortyVal != 0){
									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValfor;
									} 
									$ammount = $fortyVal;

								}else if ($fortyhcVal != 0){

									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValforHC;
									} 
									$ammount = $fortyhcVal;

								}else if ($fortynorVal != 0){
									if($statusexistfortynor == 1){
										if($requestobj[$statustypecurren] == 2){
											$currencyVal = $currencyValfornor;
										} 
									}
									$ammount = $fortynorVal;

								}else if ($fortyfiveVal != 0){
									if($statusexistfortyfive == 1){
										if($requestobj[$statustypecurren] == 2){
											$currencyVal = $currencyValforfive;
										} 
									}
									$ammount = $fortyfiveVal;
								}


								if($ammount != 0){

									$globalChargeBLArreG = GlobalCharge::create([ // tabla GlobalCharge
										'surcharge_id'       						=> $surchargeVal,
										'typedestiny_id'     						=> $typedestinyVal,
										'account_importation_globalcharge_id'  => $account_idVal,
										'company_user_id'    						=> $companyUserIdVal,
										'calculationtype_id' 						=> $calculationtypeVal,
										'ammount'            						=> $ammount,
										'currency_id'        						=> $currencyVal
									]);

									GlobalCharCarrier::create([ // tabla GlobalCharCarrier
										'carrier_id'      => $carrierVal,
										'globalcharge_id' => $globalChargeBLArreG->id
									]);

									if($originBol == true || $destinyBol == true){
										foreach($randons as  $rando){
											//insert por arreglo de puerto
											if($originBol == true ){
												$originVal = $rando;
											} else {
												$destinyVal = $rando;
											}

											GlobalCharPort::create([ // tabla GlobalCharPort
												'port_orig'      	=> $originVal,
												'port_dest'      	=> $destinyVal,
												'typedestiny_id' 	=> $typedestinyVal,
												'globalcharge_id' => $globalChargeBLArreG->id
											]);
										} 

									} else {
										// fila por puerto, sin expecificar origen ni destino manualmente
										GlobalCharPort::create([ // tabla GlobalCharPort
											'port_orig'      	=> $originVal,
											'port_dest'      	=> $destinyVal,
											'typedestiny_id' 	=> $typedestinyVal,
											'globalcharge_id' => $globalChargeBLArreG->id
										]);
									}
								}
								// echo $i;
								// dd($globalChargeBLArreG);
							}
							else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TON') == 0){
								//per_shipment
								if($twentyVal != 0){
									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValtwen;
									} 
									$ammount = $twentyVal;

								} else if ($fortyVal != 0){
									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValfor;
									} 
									$ammount = $fortyVal;

								}else if ($fortyhcVal != 0){

									if($requestobj[$statustypecurren] == 2){
										$currencyVal = $currencyValforHC;
									} 
									$ammount = $fortyhcVal;

								}else if ($fortynorVal != 0){
									if($statusexistfortynor == 1){
										if($requestobj[$statustypecurren] == 2){
											$currencyVal = $currencyValfornor;
										} 
									}
									$ammount = $fortynorVal;

								}else if ($fortyfiveVal != 0){
									if($statusexistfortyfive == 1){
										if($requestobj[$statustypecurren] == 2){
											$currencyVal = $currencyValforfive;
										} 
									}
									$ammount = $fortyfiveVal;
								}


								if($ammount != 0){
									$globalChargeTONArreG = GlobalCharge::create([ // tabla GlobalCharge
										'surcharge_id'       						=> $surchargeVal,
										'typedestiny_id'     						=> $typedestinyVal,
										'account_importation_globalcharge_id'  => $account_idVal,
										'company_user_id'    						=> $companyUserIdVal,
										'calculationtype_id' 						=> $calculationtypeVal,
										'ammount'            						=> $ammount,
										'currency_id'        						=> $currencyVal
									]);

									GlobalCharCarrier::create([ // tabla GlobalCharCarrier
										'carrier_id'      => $carrierVal,
										'globalcharge_id' => $globalChargeTONArreG->id
									]);

									if($originBol == true || $destinyBol == true){
										foreach($randons as  $rando){
											//insert por arreglo de puerto
											if($originBol == true ){
												$originVal = $rando;
											} else {
												$destinyVal = $rando;
											}

											GlobalCharPort::create([ // tabla GlobalCharPort
												'port_orig'      	=> $originVal,
												'port_dest'      	=> $destinyVal,
												'typedestiny_id' 	=> $typedestinyVal,
												'globalcharge_id' => $globalChargeTONArreG->id
											]);
										} 

									} else {
										// fila por puerto, sin expecificar origen ni destino manualmente
										GlobalCharPort::create([ // tabla GlobalCharPort
											'port_orig'      	=> $originVal,
											'port_dest'      	=> $destinyVal,
											'typedestiny_id' 	=> $typedestinyVal,
											'globalcharge_id' => $globalChargeTONArreG->id
										]);
									}
								}
								// echo $i;
								// dd($globalChargeTONArreG);
							}

						} /*else {
                            // van los fallidos

                            //---------------------------- TYPE DESTINY  ----------------------------------------------------

                            if($typedestinyExitBol == true){
                                if($typedestinyBol == true){
                                    $typedestinyobj = TypeDestiny::find($typedestinyVal);
                                    $typedestinyVal = $typedestinyobj->description;
                                } else {
                                    $typedestinyVal  = $read[$requestobj[$typedestiny]];
                                }
                            }

                            //---------------------------- CARRIER  ---------------------------------------------------------

                            if($carriExitBol == true){
                                if($carriBol == true){
                                    $carrier = Carrier::find($requestobj['carrier']); 
                                    $carrierVal = $carrier['name'];  
                                }else{
                                    $carrier = Carrier::where('name','=',$read[$requestobj['Carrier']])->first(); 
                                    $carrierVal = $carrier['name']; 
                                }
                            }

                            //---------------------------- VALUES CURRENCY ---------------------------------------------------

                            if($curreExiBol == true){
                                $currencyVal = $read[$requestobj[$currency]];
                            }

                            if( $twentyExiBol == true){
                                if(empty($read[$requestobj[$twenty]]) == true){
                                    $twentyVal = '0';
                                } else{
                                    $twentyVal = $read[$requestobj[$twenty]];

                                }
                            }

                            if( $fortyExiBol == true){
                                if(empty($read[$requestobj[$forty]]) == true){
                                    $fortyVal = '0';
                                } else{
                                    $fortyVal = $read[$requestobj[$forty]];
                                }
                            }

                            if( $fortyhcExiBol == true){
                                if(empty($read[$requestobj[$fortyhc]]) == true){
                                    $fortyhcVal = '0';
                                } else{                                  
                                    $fortyhcVal = $read[$requestobj[$fortyhc]];
                                }
                            }

                            if( $fortynorExiBol == true){
                                if($statusexistfortynor == 1){
                                    if(empty($read[$requestobj[$fortynor]]) == true){
                                        $fortynorVal = '0';
                                    } else {
                                        $fortynorVal = $read[$requestobj[$fortynor]];
                                    }
                                }
                            }

                            if( $fortyfiveExiBol == true){
                                if($statusexistfortyfive == 1){
                                    if(empty($read[$requestobj[$fortyfive]]) == true){
                                        $fortyfiveVal = '0';
                                    } else {
                                        $fortyfiveVal = $read[$requestobj[$fortyfive]];
                                    }
                                }
                            }

                            if( $variantecurrency == true){
                                if($requestobj[$statustypecurren] == 2){
                                    //------------ PARA RATES ------------------------
                                    $currencyobj = Currency::find($currencyValtwen);
                                    $currencyVal = $currencyobj['alphacode'];

                                    //------------- PARA SURCHARGERS -----------------

                                    if($curreExitwenBol == true){
                                        $currencyTWobj   = Currency::find($currencyValtwen);
                                        $currencyValtwen = $currencyTWobj['alphacode'];
                                    }

                                    if($curreExiforBol == true){
                                        $currencyFORobj  = Currency::find($currencyValfor);
                                        $currencyValfor  = $currencyFORobj['alphacode'];
                                    }

                                    if($curreExiforHCBol == true){
                                        $currencyFORHCobj  = Currency::find($currencyValforHC);
                                        $currencyValforHC  = $currencyFORHCobj['alphacode'];
                                    }

                                    if($curreExifornorBol == true){
                                        $currencyFORnorobj  = Currency::find($currencyValfornor);
                                        $currencyValfornor  = $currencyFORnorobj['alphacode'];
                                    }

                                    if($curreExiforfiveBol == true){
                                        $currencyFORfiveobj  = Currency::find($currencyValforfive);
                                        $currencyValforfive  = $currencyFORfiveobj['alphacode'];
                                    }

                                } else {
                                    $currencyobj = Currency::find($currencyVal);
                                    $currencyVal = $currencyobj['alphacode'];
                                }
                            } 

                            //---------------------------- CALCULATION TYPE -------------------------------------------------

                            if($calculationtypeExiBol == true){
                                $calculationType = CalculationType::find($calculationtypeVal);
                                $calculationtypeVal = $calculationType['name'];
                            }

                            //---------------------------- TYPE -------------------------------------------------------------

                            if($typeExiBol == true){
                                $Surchargeobj = Surcharge::find($surchargeVal);
                                $surchargeVal = $Surchargeobj['name'];
                            }

                            //////////////////////////////////////////////////////////////////////////////////////////////
                            if($read[$requestobj[$Charge]] == $chargeVal){
                                // Rates Fallidos
                                if($values == true){
                                    // si todos los valores son iguales a cero

                                    if($originBol == true || $destinyBol == true){
                                        foreach($randons as  $rando){
                                            //insert por arreglo de puerto
                                            if($originBol == true ){
                                                $originerr = Harbor::find($rando);
                                                $originVal = $originerr['name'];
                                                if($destiExitBol == true){    
                                                    $destinyVal = $read[$requestobj[$destinyExc]];
                                                }
                                            } else {
                                                $destinyerr = Harbor::find($rando);
                                                $destinyVal = $destinyerr['name'];
                                                if($origExiBol == true){
                                                    $originVal = $read[$requestobj[$originExc]];                                      
                                                }
                                            }
                                            FailRate::create([
                                                'origin_port'   => $originVal,
                                                'destiny_port'  => $destinyVal,
                                                'carrier_id'    => $carrierVal,
                                                'contract_id'   => $contractIdVal,
                                                'twuenty'       => $twentyVal,
                                                'forty'         => $fortyVal,
                                                'fortyhc'       => $fortyhcVal,
                                                'fortynor'      => $fortynorVal,
                                                'fortyfive'     => $fortyfiveVal,
                                                'currency_id'   => $currencyVal,
                                            ]);

                                        }
                                    } else {
                                        if($origExiBol == true){
                                            $originExits = Harbor::find($originVal);
                                            $originVal = $originExits->name;                                       
                                        }
                                        if($destiExitBol == true){  
                                            $destinyExits = Harbor::find($destinyVal);
                                            $destinyVal = $destinyExits->name;
                                        }
                                        FailRate::create([
                                            'origin_port'   => $originVal,
                                            'destiny_port'  => $destinyVal,
                                            'carrier_id'    => $carrierVal,
                                            'contract_id'   => $contractIdVal,
                                            'twuenty'       => $twentyVal,
                                            'forty'         => $fortyVal,
                                            'fortyhc'       => $fortyhcVal,
                                            'fortynor'      => $fortynorVal,
                                            'fortyfive'     => $fortyfiveVal,
                                            'currency_id'   => $currencyVal,
                                        ]);

                                    } 
                                }
                                //////-------------------////////////////////////////////////-----------------------------
                            } else {
                                // Surcharges Fallidos
                                if($calculationtypeExiBol == true){
                                    //
                                    if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_CONTAINER') == 0){
                                        // son tres cargas Per 20, Per 40, Per 40'HC

                                        if($originBol == true || $destinyBol == true){
                                            foreach($randons as  $rando){
                                                //insert por arreglo de puerto
                                                if($originBol == true ){
                                                    $originerr = Harbor::find($rando);
                                                    $originVal = $originerr['name'];
                                                    if($destiExitBol == true){    
                                                        $destinyVal = $read[$requestobj[$destinyExc]];
                                                    }
                                                } else {
                                                    $destinyerr = Harbor::find($rando);
                                                    $destinyVal = $destinyerr['name'];
                                                    if($origExiBol == true){
                                                        $originVal = $read[$requestobj[$originExc]];                                      
                                                    }
                                                }
                                                // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                                if($statusexistfortynor == 1){
                                                    $fortynorif =  $read[$requestobj[$fortynor]];
                                                } else {
                                                    $fortynorif = $read[$requestobj[$twenty]];
                                                }

                                                if($statusexistfortyfive == 1){ 
                                                    $fortyfiveif = $read[$requestobj[$fortyfive]];
                                                }else {
                                                    $fortyfiveif = $read[$requestobj[$twenty]];
                                                }
                                                if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                                   $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                                   $read[$requestobj[$fortyhc]] == $fortynorif &&
                                                   $fortynorif == $fortyfiveif){

                                                    // -------- PER_CONTAINER -------------------------
                                                    // se almacena uno solo porque todos los valores son iguales

                                                    $calculationtypeValfail = 'Per Container';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }
                                                    if($twentyArr[0] != 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeValfail,  //////
                                                            'ammount'            => $twentyVal, //////
                                                            'currency_id'        => $currencyVal, //////
                                                            'carrier_id'         => $carrierVal
                                                        ]);
                                                    }
                                                    //$ratescollection->push($ree);

                                                } else{


                                                    // -------- 20' ---------------------------------

                                                    $calculationtypeValfail = 'Per 20 "';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }
                                                    if($twentyArr[0] != 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeValfail,  //////
                                                            'ammount'            => $twentyVal, //////
                                                            'currency_id'        => $currencyVal, //////
                                                            'carrier_id'         => $carrierVal
                                                        ]);
                                                    }
                                                    // $ratescollection->push($ree);

                                                    // -------- 40' ---------------------------------

                                                    $calculationtypeValfail = 'Per 40 "';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    }

                                                    if($fortyArr[0] != 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeValfail,  //////
                                                            'ammount'            => $fortyVal, //////
                                                            'currency_id'        => $currencyVal, //////
                                                            'carrier_id'         => $carrierVal
                                                        ]);
                                                    }
                                                    // $ratescollection->push($ree);

                                                    // -------- 40'HC -------------------------------

                                                    $calculationtypeValfail = 'Per 40 HC';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    }

                                                    if($fortyhcArr[0] != 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeValfail,  //////
                                                            'ammount'            => $fortyhcVal, //////
                                                            'currency_id'        => $currencyVal, //////
                                                            'carrier_id'         => $carrierVal
                                                        ]);
                                                    }
                                                    //$ratescollection->push($ree);

                                                    // -------- 40'NOR -------------------------------

                                                    $calculationtypeValfail = 'Per 40 NOR';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfornor;
                                                    }

                                                    if($fortyhcArr[0] != 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeValfail,  //////
                                                            'ammount'            => $fortynorVal, //////
                                                            'currency_id'        => $currencyVal, //////
                                                            'carrier_id'         => $carrierVal
                                                        ]);
                                                    }
                                                    //$ratescollection->push($ree);

                                                    // -------- 45' ---------------------------------

                                                    $calculationtypeValfail = 'Per 45';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforfive;
                                                    }

                                                    if($fortyhcArr[0] != 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeValfail,  //////
                                                            'ammount'            => $fortyfiveVal, //////
                                                            'currency_id'        => $currencyVal, //////
                                                            'carrier_id'         => $carrierVal
                                                        ]);
                                                    }
                                                    //$ratescollection->push($ree);

                                                }
                                            }
                                        } else {
                                            if($origExiBol == true){
                                                $originExits = Harbor::find($originVal);
                                                $originVal = $originExits->name;                                       
                                            }
                                            if($destiExitBol == true){  
                                                $destinyExits = Harbor::find($destinyVal);
                                                $destinyVal = $destinyExits->name;
                                            }

                                            // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                            if($statusexistfortynor == 1){
                                                $fortynorif =  $read[$requestobj[$fortynor]];
                                            } else {
                                                $fortynorif = $read[$requestobj[$twenty]];
                                            }

                                            if($statusexistfortyfive == 1){ 
                                                $fortyfiveif = $read[$requestobj[$fortyfive]];
                                            }else {
                                                $fortyfiveif = $read[$requestobj[$twenty]];
                                            }
                                            if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                               $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                               $read[$requestobj[$fortyhc]] == $fortynorif &&
                                               $fortynorif == $fortyfiveif){

                                                // -------- PER_CONTAINER -------------------------
                                                // se almacena uno solo porque todos los valores son iguales

                                                $calculationtypeValfail = 'Per Container';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }
                                                if($twentyArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $twentyVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);
                                                }

                                            } else{

                                                // -------- 20' ---------------------------------

                                                $calculationtypeValfail = 'Per 20 "';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }

                                                if($twentyArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $twentyVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);
                                                }
                                                // -------- 40' ---------------------------------

                                                $calculationtypeValfail = 'Per 40 "';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValfor;
                                                }

                                                if($fortyArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortyVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    // $ratescollection->push($ree);
                                                }

                                                // -------- 40'HC -------------------------------

                                                $calculationtypeValfail = 'Per 40 HC';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValforHC;
                                                }

                                                if($fortyhcArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortyhcVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //  $ratescollection->push($ree);
                                                }
                                                // -------- 40'NOR ------------------------------

                                                $calculationtypeValfail = 'Per 40 NOR';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValfornor;
                                                }

                                                if($fortyhcArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortynorVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //  $ratescollection->push($ree);
                                                }

                                                // -------- 45' ---------------------------------

                                                $calculationtypeValfail = 'Per 45';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValforfive;
                                                }

                                                if($fortyhcArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortyfiveVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //  $ratescollection->push($ree);
                                                }
                                            }
                                        }

                                    } 
                                    else if (strnatcasecmp($read[$requestobj[$CalculationType]],'PER_DOC') == 0 
                                             || strnatcasecmp($read[$requestobj[$CalculationType]],'Per Shipment') == 0){
                                        // es una sola carga Per Shipment

                                        // multiples puertos o por seleccion
                                        if($originBol == true || $destinyBol == true){
                                            foreach($randons as  $rando){
                                                //insert por arreglo de puerto
                                                if($originBol == true ){
                                                    $originerr = Harbor::find($rando);
                                                    $originVal = $originerr['name'];
                                                    if($destiExitBol == true){    
                                                        $destinyVal = $read[$requestobj[$destinyExc]];
                                                    }
                                                } else {
                                                    $destinyerr = Harbor::find($rando);
                                                    $destinyVal = $destinyerr['name'];
                                                    if($origExiBol == true){
                                                        $originVal = $read[$requestobj[$originExc]];                                      
                                                    }
                                                }

                                                $calculationtypeValfail = 'Per Shipment';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }

                                                if($twentyVal != 0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    } 
                                                    $ammount = $twentyVal;

                                                } else if ($fortyVal != 0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    } 
                                                    $ammount = $fortyVal;

                                                }else if($fortyhcVal != 0){

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    } 
                                                    $ammount = $fortyhcVal;

                                                }else if($fortynorVal != 0){

                                                    if($statusexistfortynor == 1){
                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfornor;
                                                        } 
                                                    }
                                                    $ammount = $fortynorVal;

                                                }else {
                                                    if($statusexistfortyfive == 1){
                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforfive;
                                                        } 
                                                    }
                                                    $ammount = $fortyfiveVal;
                                                }

                                                if($ammount != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $ammount, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);                    
                                                }

                                            }
                                        } else {
                                            // puertos leidos del excel
                                            if($origExiBol == true){
                                                $originExits = Harbor::find($originVal);
                                                $originVal = $originExits->name;                                       
                                            }
                                            if($destiExitBol == true){  
                                                $destinyExits = Harbor::find($destinyVal);
                                                $destinyVal = $destinyExits->name;
                                            }

                                            $calculationtypeValfail = 'Per Shipment';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValtwen;
                                            }
                                            if($twentyArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $twentyVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //  $ratescollection->push($ree);
                                            }
                                        }

                                    }
                                    else if (strnatcasecmp($read[$requestobj[$CalculationType]],'Per_BL') == 0){
                                        // es una sola carga Per Shipment

                                        // multiples puertos o por seleccion
                                        if($originBol == true || $destinyBol == true){
                                            foreach($randons as  $rando){
                                                //insert por arreglo de puerto
                                                if($originBol == true ){
                                                    $originerr = Harbor::find($rando);
                                                    $originVal = $originerr['name'];
                                                    if($destiExitBol == true){    
                                                        $destinyVal = $read[$requestobj[$destinyExc]];
                                                    }
                                                } else {
                                                    $destinyerr = Harbor::find($rando);
                                                    $destinyVal = $destinyerr['name'];
                                                    if($origExiBol == true){
                                                        $originVal = $read[$requestobj[$originExc]];                                      
                                                    }
                                                }

                                                $calculationtypeValfail = 'Per BL';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }

                                                if($twentyVal != 0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    } 
                                                    $ammount = $twentyVal;

                                                } else if ($fortyVal != 0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    } 
                                                    $ammount = $fortyVal;

                                                }else if($fortyhcVal != 0){

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    } 
                                                    $ammount = $fortyhcVal;

                                                }else if($fortynorVal != 0){

                                                    if($statusexistfortynor == 1){
                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfornor;
                                                        } 
                                                    }
                                                    $ammount = $fortynorVal;

                                                }else {
                                                    if($statusexistfortyfive == 1){
                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforfive;
                                                        } 
                                                    }
                                                    $ammount = $fortyfiveVal;
                                                }

                                                if($ammount != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $ammount, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);                    
                                                }

                                            }
                                        } else {
                                            // puertos leidos del excel
                                            if($origExiBol == true){
                                                $originExits = Harbor::find($originVal);
                                                $originVal = $originExits->name;                                       
                                            }
                                            if($destiExitBol == true){  
                                                $destinyExits = Harbor::find($destinyVal);
                                                $destinyVal = $destinyExits->name;
                                            }

                                            $calculationtypeValfail = 'Per BL';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValtwen;
                                            }
                                            if($twentyArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $twentyVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //  $ratescollection->push($ree);
                                            }
                                        }

                                    }
                                    else if (strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TON') == 0){
                                        // es una sola carga Per Shipment

                                        // multiples puertos o por seleccion
                                        if($originBol == true || $destinyBol == true){
                                            foreach($randons as  $rando){
                                                //insert por arreglo de puerto
                                                if($originBol == true ){
                                                    $originerr = Harbor::find($rando);
                                                    $originVal = $originerr['name'];
                                                    if($destiExitBol == true){    
                                                        $destinyVal = $read[$requestobj[$destinyExc]];
                                                    }
                                                } else {
                                                    $destinyerr = Harbor::find($rando);
                                                    $destinyVal = $destinyerr['name'];
                                                    if($origExiBol == true){
                                                        $originVal = $read[$requestobj[$originExc]];                                      
                                                    }
                                                }

                                                $calculationtypeValfail = 'Per TON';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }

                                                if($twentyVal != 0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    } 
                                                    $ammount = $twentyVal;

                                                } else if ($fortyVal != 0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    } 
                                                    $ammount = $fortyVal;

                                                }else if($fortyhcVal != 0){

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    } 
                                                    $ammount = $fortyhcVal;

                                                }else if($fortynorVal != 0){

                                                    if($statusexistfortynor == 1){
                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfornor;
                                                        } 
                                                    }
                                                    $ammount = $fortynorVal;

                                                }else {
                                                    if($statusexistfortyfive == 1){
                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforfive;
                                                        } 
                                                    }
                                                    $ammount = $fortyfiveVal;
                                                }

                                                if($ammount != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $ammount, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);                    
                                                }

                                            }
                                        } else {
                                            // puertos leidos del excel
                                            if($origExiBol == true){
                                                $originExits = Harbor::find($originVal);
                                                $originVal = $originExits->name;                                       
                                            }
                                            if($destiExitBol == true){  
                                                $destinyExits = Harbor::find($destinyVal);
                                                $destinyVal = $destinyExits->name;
                                            }

                                            $calculationtypeValfail = 'Per TON';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValtwen;
                                            }
                                            if($twentyArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $twentyVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //  $ratescollection->push($ree);
                                            }
                                        }

                                    }
                                } else{
                                    // se deconoce si es PER_CONTAINER O PER_DOC

                                    if($originBol == true || $destinyBol == true){
                                        foreach($randons as  $rando){
                                            //insert por arreglo de puerto
                                            if($originBol == true ){
                                                $originerr = Harbor::find($rando);
                                                $originVal = $originerr['name'];
                                                if($destiExitBol == true){    
                                                    $destinyVal = $read[$requestobj[$destinyExc]];
                                                }
                                            } else {
                                                $destinyerr = Harbor::find($rando);
                                                $destinyVal = $destinyerr['name'];
                                                if($origExiBol == true){
                                                    $originVal = $read[$requestobj[$originExc]];                                      
                                                }
                                            }
                                            // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                            if($statusexistfortynor == 1){
                                                $fortynorif =  $read[$requestobj[$fortynor]];
                                            } else {
                                                $fortynorif = $read[$requestobj[$twenty]];
                                            }

                                            if($statusexistfortyfive == 1){ 
                                                $fortyfiveif = $read[$requestobj[$fortyfive]];
                                            }else {
                                                $fortyfiveif = $read[$requestobj[$twenty]];
                                            }
                                            if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                               $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                               $read[$requestobj[$fortyhc]] == $fortynorif &&
                                               $fortynorif == $fortyfiveif){

                                                // -------- PER_CONTAINER -------------------------
                                                // se almacena uno solo porque todos los valores son iguales

                                                $calculationtypeValfail = 'Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }

                                                if($twentyArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $twentyVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    // $ratescollection->push($ree);
                                                }

                                            } else{

                                                // -------- 20' ---------------------------------

                                                $calculationtypeValfail = 'Per 20 "Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }
                                                if($twentyArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $twentyVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    // $ratescollection->push($ree);
                                                }
                                                // -------- 40' ---------------------------------

                                                $calculationtypeValfail = 'Per 40 "Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValfor;
                                                }

                                                if($fortyArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortyVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);
                                                }

                                                // -------- 40'HC -------------------------------

                                                $calculationtypeValfail = '40HC Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValforHC;
                                                }

                                                if($fortyhcArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortyhcVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);
                                                }

                                                // -------- 40'NOR ------------------------------

                                                $calculationtypeValfail = '40\'NOR Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValfornor;
                                                }

                                                if($fortyhcArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortynorVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);
                                                }

                                                // -------- 45'  -------------------------------

                                                $calculationtypeValfail = '45\' Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValforfive;
                                                }

                                                if($fortyhcArr[0] != 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                        'ammount'            => $fortyfiveVal, //////
                                                        'currency_id'        => $currencyVal, //////
                                                        'carrier_id'         => $carrierVal
                                                    ]);
                                                    //$ratescollection->push($ree);
                                                }
                                            }
                                        }
                                    } else {
                                        if($origExiBol == true){
                                            $originExits = Harbor::find($originVal);
                                            $originVal = $originExits->name;                                       
                                        }
                                        if($destiExitBol == true){  
                                            $destinyExits = Harbor::find($destinyVal);
                                            $destinyVal = $destinyExits->name;
                                        }

                                        // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                        if($statusexistfortynor == 1){
                                            $fortynorif =  $read[$requestobj[$fortynor]];
                                        } else {
                                            $fortynorif = $read[$requestobj[$twenty]];
                                        }

                                        if($statusexistfortyfive == 1){ 
                                            $fortyfiveif = $read[$requestobj[$fortyfive]];
                                        }else {
                                            $fortyfiveif = $read[$requestobj[$twenty]];
                                        }
                                        if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                           $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                           $read[$requestobj[$fortyhc]] == $fortynorif &&
                                           $fortynorif == $fortyfiveif){

                                            // -------- PER_CONTAINER -------------------------
                                            // se almacena uno solo porque todos los valores son iguales

                                            $calculationtypeValfail = 'Error fila '.$i.'_E_E';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValtwen;
                                            }

                                            if($twentyArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $twentyVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //$ratescollection->push($ree);
                                            }


                                        } else{

                                            // -------- 20' ---------------------------------

                                            $calculationtypeValfail = 'Per 20 "Error fila '.$i.'_E_E';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValtwen;
                                            }

                                            if($twentyArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $twentyVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //$ratescollection->push($ree);
                                            }

                                            // -------- 40' ---------------------------------

                                            $calculationtypeValfail = 'Per 40 "Error fila '.$i.'_E_E';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValfor;
                                            }

                                            if($fortyArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $fortyVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //$ratescollection->push($ree);
                                            }

                                            // -------- 40'HC -------------------------------

                                            $calculationtypeValfail = '40HC Error fila '.$i.'_E_E';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValforHC;
                                            }

                                            if($fortyhcArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $fortyhcVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //$ratescollection->push($ree);
                                            }

                                            // -------- 40'NOR -------------------------------

                                            $calculationtypeValfail = '40\'NOR Error fila '.$i.'_E_E';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValfornor;
                                            }

                                            if($fortyhcArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $fortynorVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //$ratescollection->push($ree);
                                            }

                                            // -------- 45' ---------------------------------

                                            $calculationtypeValfail = '45\' Error fila '.$i.'_E_E';

                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValforfive;
                                            }

                                            if($fortyhcArr[0] != 0){
                                                FailSurCharge::create([
                                                    'surcharge_id'       => $surchargeVal,
                                                    'port_orig'          => $originVal,
                                                    'port_dest'          => $destinyVal,
                                                    'typedestiny_id'     => $typedestinyVal,
                                                    'contract_id'        => $contractIdVal,
                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                    'ammount'            => $fortyfiveVal, //////
                                                    'currency_id'        => $currencyVal, //////
                                                    'carrier_id'         => $carrierVal
                                                ]);
                                                //$ratescollection->push($ree);
                                            }
                                        }
                                    }
                                }

                            }
                            $falli++;
                            //echo $i;
                            //dd($ratescollection);

                        }*/
					}
					//-------------------------- fin distinto del primer ciclo
					$i++;
				}

				//dd('Todo se cargo, surcharges o rates fallidos: '.$falli);
			});



		/*ImportationRatesSurchargerJob::dispatch($request->all(),$companyUserId,$UserId); //NO BORRAR!!
        $id = $request['account_id'];
        return redirect()->route('redirect.Processed.Information',$id);*/
	}

	public function store(Request $request)
	{
		//
	}

	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show($id)
	{
		//
	}

	/**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function edit($id)
	{
		//
	}

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, $id)
	{
		//
	}

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id)
	{
		//
	}

	public function deleteAccounts($id){
		$account = AccountImportationGlobalcharge::with('FileTmp')->find($id);
		Storage::disk('UpLoadFile')->delete($account->FileTmp->name_file);
		$account->delete();
		return redirect()->route('ImportationGlobalchargeFcl.index');

	}
}
