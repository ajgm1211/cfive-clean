<?php

namespace App\Http\Controllers;

use Excel;
use PrvHarbor;
use App\Region;
use App\Harbor;
use PrvCarrier;
use App\Country;
use App\Carrier;
use App\Currency;
use PrvValidation;
use App\Surcharge;
use Carbon\Carbon;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalChargeLcl;
use App\GlobalCharPortLcl;
use App\CalculationTypeLcl;
use Illuminate\Http\Request;
use App\GlobalCharCarrierLcl;
use App\GlobalCharCountryLcl;
use App\FailedGlobalchargerLcl;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\AccountImportationGlobalChargerLcl;
use App\Jobs\ImportationGlobalchargerLclJob;
use App\NewRequestGlobalChargerLcl as RequestGCLCL;

class ImportationGlobalChargerLclController extends Controller
{

    public function index(){
        $harbor         = Harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('importationGlobalChargerLcl.index',compact('harbor','region','country','carrier','companysUser','typedestiny'));
    }

    // precarga la vista para importar globals
    public function indexRequest($id){
        $requestgc      = RequestGCLCL::find($id);
        //dd($requestgc);
        $harbor         = Harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('importationGlobalChargerLcl.indexRequest',compact('harbor','country','region','carrier','companysUser','typedestiny','requestgc'));
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
            return redirect()->route('ImportationGlobalChargerLcl.index');
        }
        //obtenemos el nombre del archivo
        $nombre     = $file->getClientOriginalName();
        $nombre     = $now.'_'.$nombre;
        $filebool   = \Storage::disk('GCImportLcl')->put($nombre,\File::get($file));

        if($filebool){
            Storage::disk('GCAccountLcl')->put($nombre,\File::get($file));
            $account                   = new AccountImportationGlobalChargerLcl();
            $account->name             = $request->name;
            $account->date             = $request->date;
            $account->namefile         = $nombre;
            $account->company_user_id  = $CompanyUserId;
            $account->requestgclcl_id     = $request_id;
            $account->status           = 'incomplete';
            $account->save(); 

            ProcessContractFile::dispatch($account->id,$account->namefile,'gclcl','account');

            $account_id = $account->id;
            /*$fileTmp    = new FileTmpGlobalcharge();
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
            2 => "Amount",
            3 => "Minimun"
        ];

        // DatOri - DatDes - DatCar, hacen referencia a si fue marcado el checkbox

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

        //ini_set('memory_limit', '1024M');

        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('GCImportLcl')
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
            'fileName'          => $nombre,

        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);

        return view('importationGlobalChargerLcl.show',compact('harbor',
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

    // se despacha el job
    public function create(Request $request){
        $companyUserId = $request->CompanyUserId;
        $UserId =\Auth::user()->id;
        /*dd($request->all());/*

        $requestobj         = $request;
        $companyUserIdVal   = $companyUserId;
        $errors             = 0;
        $NameFile           = $requestobj['FileName'];
        $path               = \Storage::disk('GCImportLcl')->url($NameFile);*/

        ImportationGlobalchargerLclJob::dispatch($request->all(),$companyUserId,$UserId);
        return redirect()->route('redirect.Processed.Information.lcl',$request['account_id']);
        return 'excel despacahdo cpn exito';

    }

    //redirecciona a la vista de infomation
    public function redirectProcessedInformation($id){
        return view('importationGlobalChargerLcl.ProcessedInformation',compact('id'));
    }

    public function store(Request $request){
        //
    }

    public function show($id){
        //
    }

    // Carga la vista de failed y goog globalchargers
    public function showviewfailedandgood($id,$tab){
        $countfailglobal = FailedGlobalchargerLcl::where('account_imp_gclcl_id','=',$id)->count();
        $countgoodglobal = GlobalChargeLcl::where('account_imp_gclcl_id','=',$id)->count();
        $accounts = AccountImportationGlobalChargerLcl::find($id);
        //dd('fallidos'.$countfailglobal);
        return view('importationGlobalChargerLcl.showview',compact('id','tab','countfailglobal','accounts','countgoodglobal'));
    }

    // LLena los datatables
    public function FailglobalchargeLoad($id,$selector){

        if($selector == 1){
            $account = AccountImportationGlobalChargerLcl::find($id);
            $harbor                 = Harbor::pluck('display_name','id');
            $currency               = Currency::pluck('alphacode','id');
            $carrierSelect          = Carrier::pluck('name','id');
            $surchargeSelect        = Surcharge::where('company_user_id','=', $account['company_user_id'])->pluck('name','id');
            $typedestiny            = TypeDestiny::pluck('description','id');
            $calculationtypeselect  = CalculationTypeLcl::pluck('name','id');

            $failglobalcharges     = FailedGlobalchargerLcl::where('account_imp_gclcl_id','=',$id)->get();
            $failglobalcoll = collect([]);
            //dd($failglobalcharges);
            foreach($failglobalcharges as $failglobalcharge){
                $classdorigin           =  'color:green';
                $classddestination      =  'color:green';
                $classtypedestiny       =  'color:green';
                $classcarrier           =  'color:green';
                $classsurcharger        =  'color:green';
                $classcalculationtype   =  'color:green';
                $classammount           =  'color:green';
                $classminimun           =  'color:green';
                $classcurrency          =  'color:green';
                $classvalidityTo        =  'color:green';
                $classvalidityfrom      =  'color:green';
                $surchargeA         =  explode("_",$failglobalcharge['surcharge']);
                $originA            =  explode("_",$failglobalcharge['origin']);
                $destinationA       =  explode("_",$failglobalcharge['destiny']);
                $calculationtypeA   =  explode("_",$failglobalcharge['calculationtypelcl']);
                $ammountA           =  explode("_",$failglobalcharge['ammount']);
                $minimumA           =  explode("_",$failglobalcharge['minimum']);
                $currencyA          =  explode("_",$failglobalcharge['currency']);
                $carrierA           =  explode("_",$failglobalcharge['carrier']);
                $typedestinyA       =  explode("_",$failglobalcharge['typedestiny']);
                $validitytoA        =  explode("_",$failglobalcharge['expire']);
                $validityfromA      =  explode("_",$failglobalcharge['validity']);

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
                } else{
                    $surchargeA         = $surchargeA[0].' (error)';
                    $classsurcharger    = 'color:red';
                }

                // -------------- CARRIER -------------------------------------------------------------
                $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
                $carrAIn = $carrierOb['id'];
                $carrierC = count($carrierA);
                if($carrierC <= 1){
                    $carrierA = $carrierA[0];
                } else{
                    $carrierA       = $carrierA[0].' (error)';
                    $classcarrier   ='color:red';
                }

                // -------------- CALCULATION TYPE ----------------------------------------------------
                $calculationtypeOb  = CalculationTypeLcl::where('name','=',$calculationtypeA[0])->first();
                $calculationtypeAIn = $calculationtypeOb['id'];
                $calculationtypeC   = count($calculationtypeA);
                if($calculationtypeC <= 1){
                    $calculationtypeA = $calculationtypeA[0];
                } else{
                    $calculationtypeA       = $calculationtypeA[0].' (error)';
                    $classcalculationtype   = 'color:red';
                }

                // -------------- AMMOUNT ------------------------------------------------------------
                $ammountC = count($ammountA);
                if($ammountC <= 1){
                    $ammountA = $failglobalcharge->ammount;
                } else{
                    $ammountA       = $ammountA[0].' (error)';
                    $classammount   = 'color:red';
                }

                // -------------- MINIMUNT ----------------------------------------------------------
                $minimumC = count($minimumA);
                if($minimumC <= 1){
                    $minimumA = $failglobalcharge->minimum;
                } else{
                    $minimumA       = $minimumA[0].' (error)';
                    $classminimun   = 'color:red';
                }

                // -------------- CURRENCY ----------------------------------------------------------
                $currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
                $currencyAIn  = $currencyOb['id'];
                $currencyC    = count($currencyA);
                if($currencyC <= 1){
                    $currencyA = $currencyA[0];
                } else{
                    $currencyA      = $currencyA[0].' (error)';
                    $classcurrency  = 'color:red';
                }
                // -------------- TYPE DESTINY -----------------------------------------------------
                //dd($failsurcharge['typedestiny_id']);
                $typedestinyobj    = TypeDestiny::where('description',$typedestinyA[0])->first();
                if(count($typedestinyA) <= 1){
                    $typedestinyLB = $typedestinyobj['description'];
                } else{
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
                    'minimum'               => $minimumA,
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
                    'classminimun'          => $classminimun,
                    'classcurrency'         => $classcurrency,
                    'classvalidityto'       => $classvalidityTo,
                    'classvalidityfrom'     => $classvalidityfrom,
                    'operation'             => 1
                ];

                //dd($arreglo);
                $failglobalcoll->push($arreglo);

            }
            //dd($failglobalcoll);
            return DataTables::of($failglobalcoll)
                ->addColumn('surchargelb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classsurcharge'].'">'.$failglobalcoll['surchargelb'].'</span>';
                })
                ->addColumn('origin_portLb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classorigin'].'">'.$failglobalcoll['origin_portLb'].'</span>';
                })
                ->addColumn('destiny_portLb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classdestiny'].'">'.$failglobalcoll['destiny_portLb'].'</span>';
                })
                ->addColumn('typedestinylb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classtypedestiny'].'">'.$failglobalcoll['typedestinylb'].'</span>';
                })
                ->addColumn('carrierlb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classcarrier'].'">'.$failglobalcoll['carrierlb'].'</span>';
                })
                ->addColumn('ammount', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classammount'].'">'.$failglobalcoll['ammount'].'</span>';
                })
                ->addColumn('minimum', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classminimun'].'">'.$failglobalcoll['minimum'].'</span>';
                })
                ->addColumn('calculationtypelb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classcalculationtype'].'">'.$failglobalcoll['calculationtypelb'].'</span>';
                })
                ->addColumn('currencylb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classcurrency'].'">'.$failglobalcoll['currencylb'].'</span>';
                })
                ->addColumn('validitytolb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classvalidityto'].'">'.$failglobalcoll['validitytolb'].'</span>';
                })
                ->addColumn('validityfromlb', function ( $failglobalcoll){
                    return '<span style="'.$failglobalcoll['classvalidityfrom'].'">'.$failglobalcoll['validityfromlb'].'</span>';
                })
                ->addColumn('action', function ( $failglobalcoll) {
                    return '<a href="#" class="" onclick="showModalsavetoglobalcharge('.$failglobalcoll['id'].',1)"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Fail-global" data-id-failglobal="'.$failglobalcoll['id'].'" class=""><i class="la la-remove"></i></a>';
                })
                ->editColumn('id', 'ID: {{$id}}')->toJson();

        }else if($selector == 2){

            $globalcharges = DB::select('call select_for_acount_globalcharger_lcl('.$id.')');

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

    public function edit($id){

        $failglobal = FailedGlobalchargerLcl::find($id);
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        $surcharge      = Surcharge::where('company_user_id','=', $failglobal['company_user_id'])->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $harbor         = Harbor::all()->pluck('display_name','id');
        $currency       = Currency::all()->pluck('alphacode','id');
        $calculationT   = CalculationTypeLcl::all()->pluck('name','id');
        $countries      = Country::pluck('name','id');

        $classdorigin           =  'color:green';
        $classddestination      =  'color:green';
        $classtypedestiny       =  'color:green';
        $classcarrier           =  'color:green';
        $classsurcharger        =  'color:green';
        $classcalculationtype   =  'color:green';
        $classammount           =  'color:green';
        $classminimum           =  'color:green';
        $classcurrency          =  'color:green';
        $classvalidity          =  'color:green';

        $surchargeA         =  explode("_",$failglobal['surcharge']);
        $originA            =  explode("_",$failglobal['origin']);
        $destinationA       =  explode("_",$failglobal['destiny']);
        $calculationtypeA   =  explode("_",$failglobal['calculationtypelcl']);
        $ammountA           =  explode("_",$failglobal['ammount']);
        $minimumA           =  explode("_",$failglobal['minimum']);
        $currencyA          =  explode("_",$failglobal['currency']);
        $carrierA           =  explode("_",$failglobal['carrier']);
        $typedestinyA       =  explode("_",$failglobal['typedestiny']);
        $validitytoA        =  explode("_",$failglobal['expire']);
        $validityfromA      =  explode("_",$failglobal['validity']);

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
        $calculationtypeOb  = CalculationTypeLcl::where('name','=',$calculationtypeA[0])->first();
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

        // -------------- MINIMUN -----------------------------------------------------------
        $minimumC = count($minimumA);
        if($minimumC <= 1){
            $minimumA = floatval($failglobal['minimum']);
        }
        else{
            $minimumA       = $minimumA[0].' (error)';
            $classminimum   = 'color:red';
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
            'minimum'               => $minimumA,
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
            'classminimum'          => $classminimum,
            'classcurrency'         => $classcurrency,
            'classvalidity'         => $classvalidity,
            'globalcharcountry'     => [],
        ];

        //dd($arre);

        return view('importationGlobalChargerLcl.Body-Modal.saveFailToGood', compact('failglobal','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries','arre'));
    }

    //Agregar Global Charge de fallido a bueno
    public function saveFailToGood(Request $request,$idFail){

        $failglobal                 = FailedGlobalchargerLcl::find($idFail);
        $typerate                   = $request->input('typeroute');
        $validation                 = explode('/',$request->validation_expire);

        if($typerate == 'port'){ //si es puerto verificamos si exite uno creado con country
            $typeplace = 'globalcharportlcl';
        }elseif($typerate == 'country'){  //si es country verificamos si exite uno creado con puerto
            $typeplace = 'globalcharcountrylcl';
        }

        $amountV             = $request->input('ammount');
        $minimumV            = $request->input('minimum');
        $surchargerV         = $request->input('surcharge_id');
        $typedestinyV        = $request->input('changetype');
        $calculationtypeV    = $request->input('calculationtype_id');
        $currencyV           = $request->input('currency_id');
        $company_userV       = $failglobal['company_user_id'];
        $carrier             = $request->input('carrier_id');

        $global = GlobalChargeLcl::where('surcharge_id',$surchargerV)
            ->where('typedestiny_id',$typedestinyV)
            ->where('company_user_id',$company_userV)
            ->where('calculationtypelcl_id',$calculationtypeV)
            ->where('ammount',$amountV)
            ->where('minimum',$minimumV)
            ->where('validity',trim($validation[0]))
            ->where('expire',trim($validation[1]))
            ->where('currency_id',$currencyV)
            ->has($typeplace)
            ->first();

        if(count($global) == 0){
            $global                         = new GlobalChargeLcl();
            $global->validity               = trim($validation[0]);
            $global->expire                 = trim($validation[1]);
            $global->surcharge_id           = $surchargerV;
            $global->typedestiny_id         = $typedestinyV;
            $global->calculationtypelcl_id  = $calculationtypeV;
            $global->ammount                = $amountV;
            $global->minimum                = $minimumV;
            $global->currency_id            = $currencyV;
            $global->company_user_id        = $company_userV;
            $global->account_imp_gclcl_id   = $failglobal['account_imp_gclcl_id'];
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
                    $detailport = new GlobalCharPortLcl();
                    $detailport->port_orig          = $valueorig;
                    $detailport->port_dest          = $valuedest;
                    $detailport->globalchargelcl_id    = $id;
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
                    $detailcountry = new GlobalCharCountryLcl();
                    $detailcountry->country_orig = $valueC;
                    $detailcountry->country_dest =  $valuedestC;
                    $detailcountry->globalchargelcl_id = $id;
                    $detailcountry->save();
                }
            }
        }

        foreach($carrier as $key)
        {
            $detailcarrier = new GlobalCharCarrierLcl();
            $detailcarrier->carrier_id      = $key;
            $detailcarrier->globalchargelcl_id = $id;
            $detailcarrier->save();
        }

        if(empty($detailcountry->globalchargelcl_id) != true){
            $failglobal->delete();
        }

        $counfail = FailedGlobalchargerLcl::where('account_imp_gclcl_id','=',$global->account_imp_gclcl_id)->count();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The Global Charge was updated from fail to good');

        if($counfail == 0){
            return redirect()->route('showview.globalcharge.lcl',[$global->account_imp_gclcl_id,0]);
        }else {
            return redirect()->route('showview.globalcharge.lcl',[$global->account_imp_gclcl_id,1]);
        }

    }

    //Editar un global charge good -- precarga de body modal AJAX
    public function editGlobalChar($id){

        $countries = Country::pluck('name','id');
        $globalcharges      = GlobalChargeLcl::find($id);
        $calculationT       = CalculationTypeLcl::pluck('name','id');
        $typedestiny        = TypeDestiny::pluck('description','id');
        $surcharge          = Surcharge::where('company_user_id','=',$globalcharges['company_user_id'])->pluck('name','id');
        $harbor             = Harbor::pluck('display_name','id');
        $carrier            = Carrier::pluck('name','id');
        $currency           = Currency::pluck('alphacode','id');
        $validation_expire  = $globalcharges->validity ." / ". $globalcharges->expire;
        $globalcharges->setAttribute('validation_expire',$validation_expire);
        return view('importationGlobalChargerLcl.Body-Modal.edit', compact('globalcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }

    //Actualiza el globalcharge good
    public function updateGlobalChar(Request $request, $id){

        $harbor = Harbor::pluck('display_name','id');
        $carrier = Carrier::pluck('name','id');
        $currency = Currency::pluck('alphacode','id');
        $calculationT = CalculationTypeLcl::pluck('name','id');
        $typedestiny = TypeDestiny::pluck('description','id');

        $global = GlobalChargeLcl::find($id);
        $validation = explode('/',$request->validation_expire);
        $global->validity = $validation[0];
        $global->expire = $validation[1];
        $global->surcharge_id = $request->input('surcharge_id');
        $global->typedestiny_id = $request->input('changetype');
        $global->calculationtypelcl_id = $request->input('calculationtypelcl_id');
        $global->ammount = $request->input('ammount');
        $global->minimum = $request->input('minimum');
        $global->currency_id = $request->input('currency_id');

        $carrier = $request->input('carrier_id');
        $deleteCarrier = GlobalCharCarrierLcl::where("globalchargelcl_id",$id);
        $deleteCarrier->delete();
        $deletePort = GlobalCharPortLcl::where("globalchargelcl_id",$id);
        $deletePort->delete();
        $deleteCountry = GlobalCharCountryLcl::where("globalchargelcl_id",$id);
        $deleteCountry->delete();

        $typerate =  $request->input('typeroute');
        if($typerate == 'port'){
            $port_orig = $request->input('port_orig');
            $port_dest = $request->input('port_dest');
            foreach($port_orig as  $orig => $valueorig)
            {
                foreach($port_dest as $dest => $valuedest)
                {
                    $detailport = new GlobalCharPortLcl();
                    $detailport->port_orig = $valueorig;
                    $detailport->port_dest = $valuedest;
                    $detailport->globalchargelcl_id = $id;
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
                    $detailcountry = new GlobalCharCountryLcl();
                    $detailcountry->country_orig = $valueC;
                    $detailcountry->country_dest =  $valuedestC;
                    $detailcountry->globalchargelcl_id = $id;
                    $detailcountry->save();
                }
            }
        }

        foreach($carrier as $key)
        {
            $detailcarrier = new GlobalCharCarrierLcl();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->globalchargelcl_id = $id;
            $detailcarrier->save();
        }

        $global->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The Global Charge was updated');
        return redirect()->route('showview.globalcharge.lcl',[$global->account_imp_gclcl_id,0]);
    }

    // Elininar glog¿balcharger Good
    public function DestroyGlobalchargeG($id){
        try{
            $globalcharge = GlobalChargeLcl::find($id);
            $globalcharge->delete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    // Elininar glog¿balcharger Fail
    public function DestroyGlobalchargeF($id){
        try{
            $globalcharge = FailedGlobalchargerLcl::find($id);
            $globalcharge->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    public function update(Request $request, $id){
        //
    }

    public function destroy($id){
        //
    }

    //ACCOUNT GCLC    ---------------------------------------------------------------------------
    public function indexAccount(){
        $account = AccountImportationGlobalChargerLcl::with('companyuser')->get();
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
                return '<a href="'.route('showview.globalcharge.lcl',[$account->id,'1']).'" class="show"  title="Failed-Good" >
                            <samp class="la la-pencil-square-o" style="font-size:20px; color:#031B4E"></samp>
                        </a>
                        &nbsp;
                        &nbsp;
                        <a href="'.route('Download.Account.gclcl',$account->id).'" class="">
                            <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E" title="Download"></samp>
                        </a>
                        <!--&nbsp; &nbsp; 
                        <a href="#" class="eliminaracount" data-id-acount="'.$account->id.'"  title="Delete" >
                            <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                        </a>-->';
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function Download(Request $request,$id){
        $account    = AccountImportationGlobalChargerLcl::find($id);
        $time       = new \DateTime();
        $now        = $time->format('d-m-y');
        $company    = CompanyUser::find($account->company_user_id);
        $extObj     = new \SplFileInfo($account->namefile);
        $ext        = $extObj->getExtension();
        $name       = $account->id.'-'.$company->name.'_'.$now.'-GCLLC.'.$ext;
        if(empty($account->namefile) != true){
            if(Storage::disk('s3_upload')->exists('Account/Global-charges/LCL/'.$account->namefile)){
                return Storage::disk('s3_upload')->download('Account/Global-charges/LCL/'.$account->namefile,$name);
            } elseif(Storage::disk('GCAccountLcl')->exists($account->namefile,$name)){
                return Storage::disk('GCAccountLcl')->download($account->namefile,$name);
            }
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'The Global Charge Lcl File not exists');
            return redirect()->route('RequestsGlobalchargersFcl.index');
        }
    }

    public function deleteAccounts($id,$select){
        //try{
        $data = PrvValidation::AcountWithJob($id);
        dd($data);
        if($data['bool'] == false){
            $account = AccountImportationGlobalChargerLcl::find($id);
            if(count($account)>0){
                if(Storage::disk('GCRequestLcl')->exists($Ncontract->namefile)){
                    Storage::disk('GCAccountLcl')->delete($account->FileTmp->name_file);
                }
                $account->delete();
            }
        }
        if($select == 1){
            return redirect()->route('ImportationGlobalChargerLcl.index');
        } elseif($select == 2){
            return response()->json(['success' => '1','jobAssociate' => $data['bool']]);			
        }
        /* } catch(\Exception $e){
            return response()->json(['success' => '2','jobAssociate' => false]);			
        }*/

    }
}
