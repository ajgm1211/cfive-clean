<?php

namespace App\Http\Controllers;

use Excel;
use PrvHarbor;
use App\Harbor;
use App\Carrier;
use App\Country;
use App\Currency;
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
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\AccountImportationGlobalcharge;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportationGlobalchargeJob;

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
        $carrierVal         = $request->carrier;
        $typedestinyVal     = $request->typedestiny;
        $validitydateVal    = $request->validitydate;
        $destinyArr         = $request->destiny;
        $originArr          = $request->origin;
        $CompanyUserId      = $request->CompanyUserId;
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

    // Despachador a job de importacion
    public function create(Request $request)
    {
        $companyUserId = $request->CompanyUserId;
        $UserId =\Auth::user()->id;
        //dd($request->all());
        ImportationGlobalchargeJob::dispatch($request->all(),$companyUserId,$UserId); //NO BORRAR!!
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
        return view('ImportationGlobalchargersFcl.ProcessedInformation',compact('id'));
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
        $classvalidityto        =  'color:green';
        $classvalidityfrom      =  'color:green';

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
        $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
            ->first();
        $originC   = count($originA);
        if($originC <= 1){
            $originAIn = [$originOb['id']];
        } else{
            $originAIn = [];
            $classdorigin='color:red';
        }

        // -------------- DESTINATION --------------------------------------------------------
        $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
            ->first();
        $destinationC   = count($destinationA);
        if($destinationC <= 1){
            $destinationAIn = collect(['id'=>$destinationOb]);
            
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
            $ammountA = $failglobal['ammount'];
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

        if(count($classvalidityto) <= 1){
            $validitytoLB = $validitytoA[0];
        }
        else{
            $validitytoLB = '';
            $classvalidityto   = 'color:red';
        }

        // -------------- VALIDITYFROM -----------------------------------------------------

        if(count($validityfromA) <= 1){
            $validityfromLB = $validityfromA[0];
        }
        else{
            $validityfromLB      = '';
            $classvalidityfrom   = 'color:red';
        }

        $validation_expire = $validitytoLB.' / '.$validityfromLB;
        
        
        ////////////////////////////////////////////////////////////////////////////////////
        $arre = [
            'id'                    => $failglobal['id'],
            'surcharge_id'          => $surcharAin,
            'origin_port'           => $originAIn,
            'destiny_port'          => $destinationAIn->pluck('name','id'),
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
            'classvalidityto'       => $classvalidityto,
            'classvalidityfrom'     => $classvalidityfrom,
            'classvalidityfrom'     => $classvalidityfrom,
            'globalcharcountry'     => [],
        ];

        //dd($arre['destiny_port']);
        //dd($arre['destiny_port']->pluck('name','id'));

        return view('ImportationGlobalchargersFcl.Body-Modal.saveFailToGood', compact('failglobal','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }

    // Carga la vista de failed y goog globalchargers
    public function showviewfailedandgood($id,$tab)
    {

        $countfailglobal = FailedGlobalcharge::where('account_id','=',$id)->count();
        $countgoodglobal = GlobalCharge::where('account_importation_globalcharge_id','=',$id)->count();
        //dd('fallidos'.$countfailglobal);
        return view('ImportationGlobalchargersFcl.showview',compact('id','tab','countfailglobal','countgoodglobal'));
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
            $failglobalcharges = FailedGlobalcharge::where('account_id','=',$id)->get();
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

                $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                    ->first();
                $originAIn = $originOb['id'];
                $originC   = count($originA);
                if($originC <= 1){
                    $originA = $originOb['name'];
                } else{
                    $originA = $originA[0].' (error)';
                    $classdorigin='color:red';
                }

                // -------------- DESTINY ------------------------------------------------------------
                $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                    ->first();
                $destinationAIn = $destinationOb['id'];
                $destinationC   = count($destinationA);
                if($destinationC <= 1){
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
                    return $globalcharges->validity;
                })
                ->editColumn('validityfromlb', function ($globalcharges){ 
                    return $globalcharges->expire;
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
        $account = AccountImportationGlobalcharge::with('FileTmp')->find($id);
        if(count($account)>0){
            if(count($account->FileTmp)>0){
                Storage::disk('UpLoadFile')->delete($account->FileTmp->name_file);
            }
            $account->delete();
        }

        if($select == 1){
            return redirect()->route('ImportationGlobalchargeFcl.index');
        } elseif($select == 2){
            return redirect()->route('indextwo.globalcharge.fcl');			
        }

    }
}
