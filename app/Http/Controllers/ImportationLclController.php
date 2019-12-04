<?php

namespace App\Http\Controllers;
use Excel;
use App\User;
use PrvHarbor;
use PrvCarrier;
use App\Harbor;
use PrvRatesLcl;
use App\RateLcl;
use App\FileTmp;
use App\Carrier;
use App\Currency;
use App\Direction;
use App\FailRateLcl;
use App\CompanyUser;
use App\ContractLcl;
use App\ScheduleType;
use App\ContractLclFile;
use App\ContractCarrierLcl;
use Illuminate\Http\Request;
use App\NewContractRequestLcl;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\DB;
use App\Jobs\ReprocesarRatesLclJob;
use Illuminate\Support\Facades\Storage;
use App\NewContractRequestLcl as RequestLCL;
use App\AccountImportationContractLcl as AccountLcl;

class ImportationLclController extends Controller
{

    // Reprocess ---------------------------------------------------------
    public function reprocessRatesLcl(Request $request, $id){
        $countfailrates = FailRateLcl::where('contractlcl_id','=',$id)->count();
        if($countfailrates <= 150){
            $failrates = FailRateLcl::where('contractlcl_id','=',$id)->get();
            // dd($failrates);
            foreach($failrates as $failrate){

                $carrierEX          = '';
                $wmEX               = '';
                $minimunEX          = '';
                $currencyEX         = '';
                $originResul        = '';
                $originExits        = '';
                $originV            = '';
                $destinResul        = '';
                $destinationExits   = '';
                $destinationV       = '';
                $originEX           = '';
                $destinyEX          = '';
                $wmVal              = '';
                $minimunVal         = '';
                $carrierVal         = '';
                $carrierArr         = '';
                $wmArr              = '';
                $minimunArr         = '';
                $scheduleTVal       = null;


                $curreExitBol       = false;
                $originB            = false;
                $destinyB           = false;
                $wmExiBol           = false;
                $minimunExiBol      = false;
                $values             = true;
                $carriExitBol       = false;
                $scheduleTBol       = false;

                $originEX       = explode('_',$failrate->origin_port);
                $destinyEX      = explode('_',$failrate->destiny_port);
                $carrierArr     = explode('_',$failrate->carrier_id);
                $wmArr          = explode('_',$failrate->uom);
                $minimunArr     = explode('_',$failrate->minimum);
                $currencyArr    = explode('_',$failrate->currency_id);
                $scheduleTArr   = explode('_',$failrate->schedule_type);

                $carrierEX     = count($carrierArr);
                $wmEX          = count($wmArr);
                $minimunEX     = count($minimunArr);
                $currencyEX    = count($currencyArr);

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                if($wmEX        <= 1 &&
                   $minimunEX   <= 1 &&  $currencyEX  <= 1 ){

                    $resultadoPortOri = PrvHarbor::get_harbor($originEX[0]);
                    if($resultadoPortOri['boolean']){
                        $originB = true;    
                    }
                    $originV  = $resultadoPortOri['puerto'];

                    $resultadoPortDes = PrvHarbor::get_harbor($destinyEX[0]);
                    if($resultadoPortDes['boolean']){
                        $destinyB = true;    
                    }
                    $destinationV  = $resultadoPortDes['puerto'];


                    //---------------- Carrier ------------------------------------------------------------------

                    $carrierArr      = PrvCarrier::get_carrier($carrierArr[0]);
                    $carriExitBol    = $carrierArr['boolean'];
                    $carrierVal      = $carrierArr['carrier'];

                    //---------------- W/M ------------------------------------------------------------------

                    if(empty($wmArr[0]) != true || (int)$wmArr[0] == 0){
                        $wmExiBol = true;
                        $wmVal    = floatval($wmArr[0]);
                    }

                    //----------------- 40' -----------------------------------------------------------------

                    if(empty($minimunArr[0]) != true || (int)$minimunArr[0] == 0){
                        $minimunExiBol = true;
                        $minimunVal    = floatval($minimunArr[0]);
                    }

                    if($wmVal == 0 && $minimunVal == 0){
                        $values = false;
                    }
                    //----------------- Currency -----------------------------------------------------------

                    $currenct = Currency::where('alphacode','=',$currencyArr[0])->orWhere('id','=',$currencyArr[0])->first();

                    if(empty($currenct->id) != true){
                        $curreExitBol = true;
                        $currencyVal =  $currenct->id;
                    }

                    //----------------- Currency -----------------------------------------------------------

                    if(empty($scheduleT->id) != true || $scheduleTArr[0] == null){
                        $scheduleTBol = true;
                        if($scheduleTArr[0] != null){
                            $scheduleTVal =  $scheduleT->id;
                        } else {
                            $scheduleTVal = null;
                        }
                    }

                    //dd($array);


                    // Validacion de los datos en buen estado ----------------------------------------------
                    if($originB == true && $destinyB == true &&
                       $wmExiBol   == true && $minimunExiBol    == true &&
                       $scheduleTBol == true && $carriExitBol   == true &&
                       $curreExitBol   == true){
                        $collecciont = '';
                        if($values){
                            $collecciont = RateLcl::create([
                                'origin_port'       => $originV,
                                'destiny_port'      => $destinationV,
                                'carrier_id'        => $carrierVal,                            
                                'contractlcl_id'    => $id,
                                'uom'               => $wmVal,
                                'minimum'           => $minimunVal,
                                'currency_id'       => $currencyVal,
                                'schedule_type_id'  => $scheduleTVal,
                                'transit_time'      => (int)$failrate['transit_time'],
                                'via'               => $failrate['via']
                            ]);
                        }
                        $failrate->forceDelete();

                    } 
                }
            }

            $contractData = ContractLcl::find($id);
            $usersNotifiques = User::where('type','=','admin')->get();
            foreach($usersNotifiques as $userNotifique){
                $message = 'The Rates was Reprocessed. Contract: ' . $contractData->number ;
                $userNotifique->notify(new N_general($userNotifique,$message));
            }

        } else {
            ReprocesarRatesLclJob::dispatch($id);
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'The rates are reprocessing in the background');
            return redirect()->route('Failed.Rates.lcl.view',[$id,'1']);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The rates are being reprocessed');
        $countfailratesNew = FailRateLcl::where('contractlcl_id','=',$id)->count();
        if($countfailratesNew > 0){
            return redirect()->route('Failed.Rates.lcl.view',[$id,'1']);
        }else{
            return redirect()->route('Failed.Rates.lcl.view',[$id,'0']);
        }
    }

    // -------------------------------------------------------------------
    public function index()
    {
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $direction      = [null=>'Please Select'];
        $direction2     = Direction::all();
        foreach($direction2 as $d){
            $direction[$d['id']]=$d->name;
        }
        return view('ImportationLcl.index',compact('harbor','carrier','direction','companysUser'));
    }

    public function indexRequest($id,$selector,$request_id)
    {
        $load_carrier = false;
        $carrier_exec = Carrier::where('name','ALL')->first();
        $carrier_exec = $carrier_exec->id;
        if($selector == 1){
            $requestlcl     = RequestLCL::find($id);
            $requestlcl->load('Requestcarriers');
            if(count($requestlcl->Requestcarriers) == 1){
                foreach($requestlcl->Requestcarriers as $carrier_uniq){
                    if($carrier_uniq->id != $carrier_exec){
                        $load_carrier = true;
                    }
                }
            }
        } elseif($selector == 2){
            $contract = ContractLcl::find($id);
            $contract->load('carriers');
            if(count($contract->carriers) == 1){
                foreach($contract->carriers as $carrier_uniq){
                    if($carrier_uniq->id != $carrier_exec){
                        $load_carrier = true;
                    }
                }
            }
        }
        //dd($requestlcl);
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $direction      = Direction::pluck('name','id');
        if($selector == 1){
            return view('ImportationLcl.indexRequest',compact('harbor','carrier','direction','companysUser','requestlcl','selector','load_carrier'));
        } elseif($selector == 2){
            return view('ImportationLcl.indexRequest',compact('harbor','carrier','direction','companysUser','contract','selector','request_id','load_carrier'));
        }
    }

    // --------------- Rates ---------------------------------------------

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request)
    {

        //dd($request->all());
        $now = new \DateTime();
        $now2           = $now;
        $now2           = $now2->format('Y-m-d');
        $now            = $now->format('dmY_His');
        $type           = $request->type;
        $request_id     = $request->request_id;
        $carrierVal     = $request->carrier;
        $destinyArr     = $request->destiny;
        $originArr      = $request->origin;
        $CompanyUserId  = $request->CompanyUserId;
        $direction_id   = $request->direction;
        $selector       = $request->selector;

        $carrierBol     = false;
        $destinyBol     = false;
        $originBol      = false;
        $scheduleinfoBoll   = false;

        $data= collect([]);
        $harbor     = harbor::all()->pluck('display_name','id');
        $carrier    = carrier::all()->pluck('name','id');
        $direction  = Direction::pluck('name','id');
        // try {
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );
        $Contract_id;
        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('ImportationLCL.index');
        }
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        $validatefile = \Storage::disk('LclImport')->put($nombre,\File::get($file));

        if($validatefile){
            \Storage::disk('LclAccount')->put($nombre,\File::get($file));

            $account = new AccountLcl();
            $account->name              = $request->name;
            $account->date              = $now2;
            $account->namefile          = $nombre;
            $account->company_user_id   = $CompanyUserId;
            $account->requestlcl_id     = $request_id;
            $account->save();

            ProcessContractFile::dispatch($account->id,$account->namefile,'lcl','account');
            if($selector == 2){
                $contract               = ContractLcl::find($request->contract_id);
                $contract->account_id   = $account->id;
                $contract->update();
            } else {
                $contract     = new ContractLcl();
                $contract->name             = $request->name;
                $validity                   = explode('/',$request->validation_expire);
                $contract->validity         = $validity[0];
                $contract->expire           = $validity[1];
                $contract->status           = 'incomplete';
                $contract->comments         = $request->comments;
                $contract->company_user_id  = $CompanyUserId;
                $contract->direction_id     = $direction_id;
                $contract->account_id       = $account->id;
                $contract->save(); 

                /* $fileTmp = new FileTmp();
            $fileTmp->contract_id = $Contract_id;
            $fileTmp->name_file   = $nombre;
            $fileTmp->save(); //*/
                foreach($request->carrierM as $carrierVal){
                    ContractCarrierLcl::create([
                        'carrier_id'    => $carrierVal,
                        'contract_id'   => $contract->id
                    ]);
                }
            }
            $contract->load('carriers');
            $Contract_id = $contract->id;

            if(!empty($request_id)){
                $requestFile    = NewContractRequestLcl::find($request_id);
                if(!empty($requestFile->id)){
                    $contractFile   =  new ContractLclFile();
                    $contractFile->contractlcl_id   = $Contract_id;
                    $contractFile->namefile         = $requestFile->namefile;
                    $contractFile->save();
                }
            }

        }

        $statustypecurren = $request->valuesCurrency;
        $targetsArr =[ 0 => "W/M", 1 => "Minimun"];

        // si type es igual a  1, el proceso va por rates, si es 2 va por rate mas surchargers

        if($type == 2){
            array_push($targetsArr,"Calculation Type","Charge");
        }

        // DatOri - DatDes - DatCar, hacen referencia a si fue marcado el checkbox

        if($request->DatShe != true){
            $scheduleinfoBoll = true;
            array_push($targetsArr,"Schedule Type","Transit Time","Via");
        }

        /* si $statustypecurren es igual a 2, los currencys estan contenidos en la misma columna 
        con los valores, si es uno el currency viene en una colmna aparte        
        */

        if($statustypecurren == 1){
            array_push($targetsArr,"Currency");
        }

        if($request->DatOri == false){
            array_push($targetsArr,'Origin');
        }
        else{
            $originBol = true;
            $originArr;
        }
        if($request->DatDes == false){
            array_push($targetsArr,'Destiny');
        } else {
            $destinyArr;
            $destinyBol = true;
        }
        if($request->DatCar == false){
            array_push($targetsArr,'Carrier');
        } else {
            $carrierVal;
            $carrierBol = true;
        }
        //dd($targetsArr);
        //  dd($data);
        $coordenates = collect([]);
        //ini_set('max_execution_time', 300);
        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('LclImport')
                   ->url($nombre),function($reader) use($request,$coordenates) {
                       $reader->noHeading = true;
                       $reader->ignoreEmpty();
                       $reader->takeRows(2);
                       // foreach($reader->first() as $read){
                       $read = $reader->first();
                       $columna= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V');
                       for($i=0;$i<count($reader->first());$i++){
                           $coordenates->push($columna[$i].' '.$read[$i]);
                       }
                       /*break;
                       }*/

                   });
        $boxdinamy = [
            'existorigin'     => $originBol,
            'origin'          => $originArr,
            'existdestiny'    => $destinyBol,
            'destiny'         => $destinyArr,
            'existcarrier'    => $carrierBol,
            'carrier'         => $carrierVal,
            'comments'        => $contract->comments,
            'Contract_id'     => $Contract_id,
            'number'          => $request->number,
            'name'            => $request->name,
            'fileName'        => $nombre,
            'validatiion'     => $request->validation_expire,
            'comments'        => $request->comments,
            'scheduleinfo'      => $scheduleinfoBoll
        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);

        return view('ImportationLcl.show',compact('harbor','carrier','direction','coordenates','targetsArr','data','countTarges','type','statustypecurren','contract','CompanyUserId'));
        /*}catch(\Exception $e){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error with the archive');
            return redirect()->route('importaion.fcl');
        }//*/

    }

    // Importador de Rates LCL 
    public function create(Request $request)
    {
        //dd($request->all());
        $requestobj = $request->all();
        $NameFile           = $requestobj['FileName'];
        $path = \Storage::disk('LclImport')->url($NameFile);
        $companyUserIdVal       = $requestobj['CompanyUserId'];
        //dd($path);
        $errors = 0;
        Excel::selectSheetsByIndex(0)
            ->Load($path,function($reader) use($requestobj,$errors,$NameFile,$companyUserIdVal,$request) {
                $reader->noHeading = true;

                $currency               = "Currency";
                $origin                 = "origin";
                $originExc              = "Origin";
                $destiny                = "destiny";
                $destinyExc             = "Destiny";
                $carrier                = "Carrier";
                $wm                     = "W/M";
                $minimun                = "Minimun";
                $contractId             = "Contract_id";
                $statustypecurren       = "statustypecurren";
                $scheduleTExc           = "Schedule_Type";
                $transittimeExc         = "Transit_Time";
                $viaExc                 = "Via";
                $scheduleinfo           = "scheduleinfo";
                $contractIdVal          = $requestobj['Contract_id'];

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                $ratescollection         = collect([]);
                $ratesFailcollection     = collect([]);
                $i = 0;
                foreach($reader->get() as $read){

                    $carrierVal         = '';
                    $originVal          = '';
                    $destinyVal         = '';
                    $currencyVal        = '';
                    $randons            = '';
                    $currencyVal        = '';
                    $wmVal              = '';
                    $currencResul       = '';
                    $minimunVal         = '';
                    $scheduleTResul     = null;
                    $transittimeResul   = 0;
                    $viaResul           = null;

                    $carriBol           = false;
                    $wmExiBol           = false;
                    $originBol          = false;
                    $origExiBol         = false;
                    $destinyBol         = false;
                    $curreExitBol       = false;
                    $destiExitBol       = false;
                    $carriExitBol       = false;
                    $minimunExiBol      = false;
                    $scheduleTBol       = false;
                    $transittimeBol     = false;
                    $viaBol             = false;

                    $rqScheduleinfoBol = $requestobj[$scheduleinfo];

                    if($i != 0){

                        //--------------- SCHEDULE TYPE --------------------------------------------

                        if($rqScheduleinfoBol == true){
                            $scheduleTResul = ScheduleType::where('name',$read[$requestobj[$scheduleTExc]])->first();
                            if(count($scheduleTResul) >= 1){
                                $scheduleTBol = true;
                                $scheduleTResul = $scheduleTResul['id'];
                            } else {
                                $scheduleTResul = $read[$requestobj[$scheduleTExc]].'_E_E'; 
                            }
                        } else {
                            $scheduleTBol = true;
                        }
                        //--------------- TRANSIT TIME ---------------------------------------------
                        if($rqScheduleinfoBol == true){
                            $transittimeBol      = true;
                            $transittimeResul   = (INT)$read[$requestobj[$transittimeExc]];
                        } else {
                            $transittimeBol      = true;
                        }

                        //--------------- VIA --------------------------------------------
                        if($rqScheduleinfoBol == true){
                            $viaBol     = true;
                            $viaResul   = $read[$requestobj[$viaExc]];
                        } else {
                            $viaBol     = true;
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

                        //--------------- CARRIER -----------------------------------------------------------------
                        if($requestobj['existcarrier'] == 1){
                            $carriExitBol = true;
                            $carriBol     = true;
                            $carrierVal = $requestobj['carrier']; // cuando se indica que no posee carrier 
                        } else {
                            $carrierVal = $read[$requestobj['Carrier']]; // cuando el carrier existe en el excel
                            $carrierArr      = PrvCarrier::get_carrier($carrierVal);
                            $carriExitBol    = $carrierArr['boolean'];
                            $carrierVal      = $carrierArr['carrier'];
                        }

                        //---------------- W/M ------------------------------------------------------------------

                        $wmArr      = explode(' ',trim($read[$requestobj[$wm]]));

                        if(empty($wmArr[0]) != true || (int)$wmArr[0] == 0){
                            $wmExiBol = true;
                            $wmVal   = floatval($wmArr[0]);
                        }else{
                            $wmVal = $wmArr[0].'_E_E';
                        }

                        //---------------- MINIMUN --------------------------------------------------------------

                        $minimunArr      = explode(' ',trim($read[$requestobj[$minimun]]));

                        if(empty($minimunArr[0]) != true || (int)$minimunArr[0] == 0){
                            $minimunExiBol = true;
                            $minimunVal   = floatval($minimunArr[0]);
                        }else{
                            $minimunVal = $minimunArr[0].'_E_E';
                        }

                        //---------------- CURRENCY VALUES ------------------------------------------------------

                        if($requestobj[$statustypecurren] == 2){ // se verifica si el valor viene junto con el currency

                            // cargar  columna con el  valor y currency  juntos, se descompone

                            //---------------- CURRENCY W/M + value ---------------------------------------------

                            if(count($wmArr) > 1){
                                $currencResultwm = str_replace($caracteres,'',$wmArr[1]);
                            } else {
                                $currencResultwm = '';
                            }

                            $currencwm = Currency::where('alphacode','=',$currencResultwm)->first();

                            if(empty($currencwm->id) != true){
                                $curreExitBol = true;
                                $currencyValtwm =  $currencwm->id;
                            }
                            else{
                                if(count($wmArr) > 1){
                                    $currencyValtwm = $wmArr[1].'_E_E';
                                } else{
                                    $currencyValtwm = '_E_E';
                                }
                            }

                            $currencyVal = $currencyValtwm;

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

                        }

                        /*  $data = [
                            'carriExitBol'      => $carriExitBol,
                            'carrierVal'        => $carrierVal,
                            'destinyBol'        => $destinyBol,
                            'destiExitBol'      => $destiExitBol,
                            'destinyVal'        => $destinyVal,
                            'originBol'         => $originBol,
                            'origExiBol'        => $origExiBol,
                            'originVal'         => $originVal,
                            'randons'           => $randons,
                            'contractIdVal'     => $contractIdVal,
                            'curreExitBol'      => $curreExitBol,
                            'currencyVal'       => $currencyVal,
                            'wmExiBol'          => $wmExiBol,
                            'wmVal'             => $wmVal,
                            'minimunExiBol'     => $minimunExiBol,
                            'minimunVal'        => $minimunVal,
                            //''  => ,
                        ];

                        dd($data);*/
                        if($carriExitBol == true && $destiExitBol     == true &&
                           $origExiBol   == true && $curreExitBol     == true &&
                           $wmExiBol     == true && $scheduleTBol     == true && 
                           $transittimeBol == true && $viaBol == true
                           && $minimunExiBol    == true ){

                            if($originBol == true || $destinyBol == true){
                                foreach($randons as  $rando){
                                    //insert por arreglo de puerto
                                    if($originBol == true ){
                                        $originVal = $rando;
                                    } else {
                                        $destinyVal = $rando;
                                    }

                                    $ratesArre = RateLcl::create([
                                        'origin_port'       => $originVal,
                                        'destiny_port'      => $destinyVal,
                                        'carrier_id'        => $carrierVal,
                                        'contractlcl_id'    => $contractIdVal,
                                        'uom'               => $wmVal,
                                        'minimum'           => $minimunVal,
                                        'currency_id'       => $currencyVal,
                                        'schedule_type_id'  => $scheduleTResul,
                                        'transit_time'      => $transittimeResul,
                                        'via'               => $viaResul
                                    ]);
                                } 
                                //dd($ratesArre);
                            }else {
                                // fila por puerto, sin expecificar origen ni destino manualmente

                                $ratesArre = RateLcl::create([
                                    'origin_port'       => $originVal,
                                    'destiny_port'      => $destinyVal,
                                    'carrier_id'        => $carrierVal,
                                    'contractlcl_id'    => $contractIdVal,
                                    'uom'               => $wmVal,
                                    'minimum'           => $minimunVal,
                                    'currency_id'       => $currencyVal,
                                    'schedule_type_id'  => $scheduleTResul,
                                    'transit_time'      => $transittimeResul,
                                    'via'               => $viaResul
                                ]);

                                //dd($ratesArre);
                            }
                        } else {
                            // aqui van los fallidos
                            //---------------------------- SHEDULES ---------------------------------------------------------

                            if( $scheduleTBol == true && $rqScheduleinfoBol == true){
                                $scheduleTResul = ScheduleType::find($scheduleTResul);
                                $scheduleTResul = $scheduleTResul['name'];
                            }

                            //---------------------------- CARRIER  ---------------------------------------------------------

                            if($carriExitBol == true){
                                if($carriBol == true){
                                    $carrier = Carrier::find($requestobj['carrier']); 
                                    $carrierVal = $carrier['name'];  
                                }else{
                                    $carrier = Carrier::find($carrierVal); 
                                    //$carrier = Carrier::where('name','=',$read[$requestobj['Carrier']])->first(); 
                                    $carrierVal = $carrier['name']; 
                                }
                            }

                            //---------------------------- CURRENCY  ---------------------------------------------------------

                            if($curreExitBol == true){
                                $currencyVal = Currency::find($currencyVal);
                                $currencyVal = $currencyVal->id;
                            }  

                            //---------------------------- w/m  --------------------------------------------------------------                                    
                            /*  $dataErr = [
                                'carriExitBol'      => $carriExitBol,
                                'carrierVal'        => $carrierVal,
                                'destinyBol'        => $destinyBol,
                                'destiExitBol'      => $destiExitBol,
                                'destinyVal'        => $destinyVal,
                                'originBol'         => $originBol,
                                'origExiBol'        => $origExiBol,
                                'originVal'         => $originVal,
                                'randons'           => $randons,
                                'contractIdVal'     => $contractIdVal,
                                'curreExitBol'      => $curreExitBol,
                                'currencyVal'       => $currencyVal,
                                'wmExiBol'          => $wmExiBol,
                                'wmVal'             => $wmVal,
                                'minimunExiBol'     => $minimunExiBol,
                                'minimunVal'        => $minimunVal,
                                //''  => ,
                            ];

                            dd($dataErr); */

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
                                    $ratesArre = FailRateLcl::create([
                                        'origin_port'    => $originVal,
                                        'destiny_port'   => $destinyVal,
                                        'carrier_id'     => $carrierVal,
                                        'contractlcl_id' => $contractIdVal,
                                        'uom'            => $wmVal,
                                        'minimum'        => $minimunVal,
                                        'currency_id'    => $currencyVal,
                                        'schedule_type'  => $scheduleTResul,
                                        'transit_time'   => $transittimeResul,
                                        'via'            => $viaResul
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

                                $ratesArre = FailRateLcl::create([
                                    'origin_port'    => $originVal,
                                    'destiny_port'   => $destinyVal,
                                    'carrier_id'     => $carrierVal,
                                    'contractlcl_id' => $contractIdVal,
                                    'uom'            => $wmVal,
                                    'minimum'        => $minimunVal,
                                    'currency_id'    => $currencyVal,
                                    'schedule_type'  => $scheduleTResul,
                                    'transit_time'   => $transittimeResul,
                                    'via'            => $viaResul
                                ]);
                            }
                            $errors = $errors + 1;
                        }
                    }
                    $i =$i + 1;
                }
                //dd('fin import');
                \Storage::disk('LclImport')->delete($requestobj['FileName']);
            });

        $contract = ContractLcl::find($request['Contract_id']);
        $contract->status = 'publish';
        $contract->update();

        $countfailrates = FailRateLcl::where('contractlcl_id','=',$request['Contract_id'])->count();


        if($countfailrates > 0){

            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Well done!');
            if($countfailrates == 1){
                $request->session()->flash('message.content', ' '.$countfailrates.' fee is not charged correctly');
            }else{
                $request->session()->flash('message.content', ' '.$countfailrates.' Rates did not load correctly');
            }
            return redirect()->route('Failed.Rates.lcl.view',[$request['Contract_id'],1]);

        } else{
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'You successfully added the rate ');
            return redirect()->route('Failed.Rates.lcl.view',[$request['Contract_id'],0]);
        }

    }

    // Rates view
    public function FailedRatesView($id,$tab){
        //$id se refiere al id del contracto
        $countrates = RateLcl::with('carrier','contract')->where('contractlcl_id','=',$id)->count();
        $countfailrates = FailRateLcl::where('contractlcl_id','=',$id)->count();
        $contract = ContractLcl::find($id);
        return view('ImportationLcl.showrates',compact('countfailrates','countrates','contract','id','tab'));
    }

    // Datatable de Rates
    public function FailedRatesDT($id,$selector){
        //$id se refiere al id del contracto
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcarrier = new Carrier();

        $failrates = collect([]);

        if($selector == 1){
            $failratesFor   = DB::select('call  proc_fails_rates_lcl('.$id.')');
            //$failratesFor   = FailRateLcl::where('contractlcl_id','=',$id)->get();
            foreach( $failratesFor as $failrate){

                $carrAIn;
                $pruebacurre = "";
                $originA        = explode("_",$failrate->origin_port);
                $destinationA   = explode("_",$failrate->destiny_port);
                $carrierA       = explode("_",$failrate->carrier_id);
                $uomA           = explode("_",$failrate->uom);
                $minimumA       = explode("_",$failrate->minimum);
                $currencyA      = explode("_",$failrate->currency_id);
                $schedule_typeA = explode("_",$failrate->schedule_type);
                $transit_timeA  = explode("_",$failrate->transit_time);
                $viaA           = explode("_",$failrate->via);

                //------------ ORIGIN ------------------------------------------------------------------------

                $originOb       = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                    ->first();

                $originC   = count($originA);
                if($originC <= 1){
                    $originA = $originOb['name'];
                } else{
                    $originA = $originA[0].' (error)';
                }

                //------------ DESTINATION -------------------------------------------------------------------

                $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                    ->first();

                $destinationC   = count($destinationA);
                if($destinationC <= 1){
                    $destinationA = $destinationOb['name'];
                } else{
                    $destinationA = $destinationA[0].' (error)';
                }


                //------------ W/M ---------------------------------------------------------------------------

                $uomC   = count($uomA);
                if($uomC <= 1){
                    $uomA = $uomA[0];
                } else{
                    $uomA = $uomA[0].' (error)';
                }

                //------------ MINIMUN ---------------------------------------------------------------------------

                $minimumC   = count($minimumA);
                if($minimumC <= 1){
                    $minimumA = $minimumA[0];
                } else{
                    $minimumA = $minimumA[0].' (error)';
                }

                //------------ CARRIER ---------------------------------------------------------------------------

                $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
                //$carrAIn = $carrierOb['id'];
                $carrierC = count($carrierA);
                if($carrierC <= 1){
                    $carrierA = $carrierA[0];
                } else{
                    $carrierA = $carrierA[0].' (error)';
                }

                //-------------------------------------------

                if(count($schedule_typeA) <= 1){
                    if(empty($schedule_typeA[0]) != true){
                        $schedule_typeA = $schedule_typeA[0];
                    } else {
                        $schedule_typeA = '---------';
                    }
                } else{
                    $schedule_typeA = $schedule_typeA[0].' (error)';
                }

                if(count($transit_timeA) <= 1){
                    if(empty($transit_timeA[0]) != true){
                        $transit_timeA = $transit_timeA[0];
                    } else {
                        $transit_timeA = '0';
                    }
                } else{
                    $transit_timeA = $transit_timeA[0].' (error)';
                }

                if(count($viaA) <= 1){
                    if(empty($viaA[0]) != true){
                        $viaA = $viaA[0];
                    } else {
                        $viaA = '---------';
                    }
                } else{
                    $viaA = $viaA[0].' (error)';
                }

                //------------ CURRENCY --------------------------------------------------------------------------

                $currencyC = count($currencyA);
                if($currencyC <= 1){
                    $currenc = Currency::where('alphacode','=',$currencyA[0])->orWhere('id','=',$currencyA[0])->first();
                    $currencyA = $currenc['alphacode'];
                } else{
                    $currencyA = $currencyA[0].' (error)';
                }        

                $colec = ['id'              =>  $failrate->id,
                          'contract_id'     =>  $id,
                          'origin_portLb'   =>  $originA,      
                          'destiny_portLb'  =>  $destinationA,  
                          'carrierLb'       =>  $carrierA,     
                          'w/m'             =>  $uomA,         
                          'minimum'         =>  $minimumA,         
                          'currency_id'     =>  $currencyA,    
                          'operation'       =>  '1',
                          'schedule_type'   =>  $schedule_typeA,
                          'transit_time'    =>  $transit_timeA,
                          'via'             =>  $viaA
                         ];

                $failrates->push($colec);

            }
            return DataTables::of($failrates)
                ->addColumn('action', function ( $failrate) {
                    return '<a href="#" class="" onclick="showModalsavetorate('.$failrate['id'].','.$failrate['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-FailRate" data-id-failrate="'.$failrate['id'].'" class=""><i class="la la-remove"></i></a>';
                })
                ->editColumn('id', 'ID: {{$id}}')->toJson();



        } else if($selector == 2){

            $ratescol = PrvRatesLcl::get_rates($id);

            return DataTables::of($ratescol)
                ->addColumn('via', function ($ratescol) { 
                    if(empty($ratescol['via']) != true){
                        return $ratescol['via'];
                    } else {
                        return '--------';
                    }
                })
                ->addColumn('action', function ($ratescol) {
                    return '
                <a href="#" onclick="showModalsavetorate('.$ratescol['id'].','.$ratescol['operation'].')" class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Rate" data-id-rate="'.$ratescol['id'].'" class=""><i class="la la-remove"></i></a>';
                })
                ->editColumn('id', 'ID: {{$id}}')->toJson();
        }

    }

    // Rates a editar para pasar a good rates (show modal)
    public function EditRatesFail($id){
        $harbor         = Harbor::pluck('display_name','id');
        $carrier        = Carrier::pluck('name','id');
        $currency       = Currency::pluck('alphacode','id');
        $schedulesT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $schedulesT[$d['id']]=$d->name;
        }


        $failrate       = FailRateLcl::find($id);

        $pruebacurre        = '';
        $classdorigin       = 'color:green';
        $classddestination  = 'color:green';
        $classcarrier       = 'color:green';
        $classcurrency      = 'color:green';
        $classuom           = 'color:green';
        $classminimum       = 'color:green';

        $classscheduleT     ='color:green';
        $classtransittime   ='color:green';
        $classvia           ='color:green';

        $originA        = explode("_",$failrate['origin_port']);
        $destinationA   = explode("_",$failrate['destiny_port']);
        $carrierA       = explode("_",$failrate['carrier_id']);
        $currencyA      = explode("_",$failrate['currency_id']);
        $uomA           = explode("_",$failrate['uom']);
        $minimumA       = explode("_",$failrate['minimum']);
        $schedueleTA    = explode("_",$failrate['schedule_type']);

        // --------------------------  SCHEDULES  ---------------------------------------------------------
        if(count($schedueleTA) <= 1){
            $schedueleTA = ScheduleType::where('name',$schedueleTA[0])->first();
            $schedueleTA = $schedueleTA['id'];
        } else{
            $schedueleTA = '';
            $classscheduleT='color:red';
        }

        // --------------------------  ORIGIN  ------------------------------------------------------------

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

        // --------------------------  DESTINATIO  --------------------------------------------------------

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

        // --------------------------  W/M  ---------------------------------------------------------------

        $uomC   = count($uomA);
        if($uomC <= 1){
            $uomA = $uomA[0];
        } else{
            $uomA       = $uomA[0].' (error)';
            $classuom   = 'color:red';
        }

        // --------------------------  MINIMUM  -----------------------------------------------------------

        $minimumC   = count($minimumA);
        if($minimumC <= 1){
            $minimumA = $minimumA[0];
        } else{
            $minimumA       = $minimumA[0].' (error)';
            $classminimum   = 'color:red';
        }

        // --------------------------  CARRIER  -----------------------------------------------------------

        $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
        $carrAIn = $carrierOb['id'];
        $carrierC = count($carrierA);
        if($carrierC <= 1){
            //dd($carrierAIn);
            $carrierA = $carrierA[0];
        }
        else{
            $carrierA       = $carrierA[0].' (error)';
            $classcarrier   = 'color:red';
        }

        // --------------------------  CURRENCY  ----------------------------------------------------------

        $currencyC = count($currencyA);
        if($currencyC <= 1){
            $currenc        = Currency::where('alphacode','=',$currencyA[0])->orWhere('id','=',$currencyA[0])->first();
            $pruebacurre    = $currenc['id'];
            $currencyA      = $currencyA[0];
        }
        else{
            $currencyA      = $currencyA[0].' (error)';
            $classcurrency  = 'color:red';
        }        

        $failrates = ['rate_id'         =>  $failrate->id,
                      'contract_id'     =>  $failrate->contractlcl_id,
                      'origin_port'     =>  $originAIn,   
                      'destiny_port'    =>  $destinationAIn,     
                      'carrierAIn'      =>  $carrAIn,
                      'uom'             =>  $uomA,      
                      'minimum'         =>  $minimumA,      
                      'currencyAIn'     =>  $pruebacurre,
                      'classorigin'     =>  $classdorigin,
                      'classdestiny'    =>  $classddestination,
                      'classcarrier'    =>  $classcarrier,
                      'classuom'        =>  $classuom,
                      'classminimum'    =>  $classminimum,
                      'classcurrency'   =>  $classcurrency,
                      'transit_time'    =>  $failrate->transit_time,
                      'via'             =>  $failrate->via,
                      'schedueleT'      =>  $schedueleTA,
                      'classtransittime'=>  $classtransittime,
                      'classvia'        =>  $classvia,
                      'classscheduleT'  =>  $classscheduleT
                     ];
        $pruebacurre = "";
        $carrAIn = "";
        // dd($failrates);
        return view('ImportationLcl.Body-Modals.FailEditRates', compact('failrates','harbor','carrier','currency','schedulesT'));
    }

    // Rates crea desde la edicion fail rates y los elimina de fail Rates
    public function CreateRates(Request $request, $id){
        //dd($request->all());
        $return = RateLcl::create([
            "origin_port"       => $request->origin_port,
            "destiny_port"      => $request->destiny_port,
            "carrier_id"        => $request->carrier_id,
            "contractlcl_id"    => $request->contract_id,
            "uom"               => floatval($request->uom),
            "minimum"           => floatval($request->minimum),
            "currency_id"       => $request->currency_id,
            "schedule_type_id"  => $request->scheduleT,
            "transit_time"      => $request->transit_time,
            "via"               => $request->via
        ]);

        $failrate = FailRateLcl::find($id);
        $failrate->forceDelete();
        $request->session()->flash('message.content', 'Updated Rate' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');

        $countfailrates = FailRateLcl::where('contractlcl_id','=',$request->contract_id)->count();

        if($countfailrates > 0){
            return redirect()->route('Failed.Rates.lcl.view',[$request->contract_id,1]);
        } else{
            return redirect()->route('Failed.Rates.lcl.view',[$request->contract_id,0]);
        }
    }

    // Rates Eliminar Fail Rates
    public function DestroyRatesF($id){
        try{
            $failRate = FailRateLcl::find($id);
            $failRate->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    // Rates a editar (show modal)
    public function EditRatesGood($id){
        $harbor         = Harbor::pluck('display_name','id');
        $carrier        = Carrier::pluck('name','id');
        $currency       = Currency::pluck('alphacode','id');
        $schedulesT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $schedulesT[$d['id']]=$d->name;
        }
        $rates = RateLcl::find($id);
        return view('ImportationLcl.Body-Modals.GoodEditRates', compact('rates','harbor','carrier','currency','schedulesT'));
    }

    // Actualiza Los rates
    public function UpdateRatesD(Request $request, $id){
        //dd($request->all());
        $rate = RateLcl::find($id);
        $rate->origin_port      =  $request->origin_id;
        $rate->destiny_port     =  $request->destiny_id;
        $rate->carrier_id       =  $request->carrier_id;
        $rate->contractlcl_id   =  $request->contract_id;
        $rate->uom              =  floatval($request->uom);
        $rate->minimum          =  floatval($request->minimum);
        $rate->currency_id      =  $request->currency_id;
        $rate->schedule_type_id =  $request->scheduleT;
        $rate->transit_time     =  (int)$request->transit_time;
        $rate->via              =  $request->via;
        $rate->update();

        $request->session()->flash('message.content', 'Updated Rate' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Rates.lcl.view',[$request->contract_id,0]);

    }

    // Elimina los Rates Good
    public function DestroyRatesG($id){
        try{
            $Rate = RateLcl::find($id);
            $Rate->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    // Account Importation --------------------------------------------------------------

    public function indexAccount(){


        $account = \DB::select('call  proc_account_lcl');
        //dd($account);
        return DataTables::of($account)
            /* ->addColumn('status', function ( $account) {
                if(empty($account->contractlcl->status)!=true){
                    return  $account->contractlcl->status;
                }else{
                    return  'Contract erased';
                }
            })
            ->addColumn('company_user_id', function ( $account) {
                return  $account->companyuser->name;
            })
            ->addColumn('requestlcl_id', function ( $account) {
                if(empty($account->requestlcl_id) != true){
                    return  $account->requestlcl_id;
                } else {
                    return 'Manual';
                }
            })*/
            ->addColumn('action', function ( $account) {
                if($account->status != 'Contract erased'){
                    return '
                <a href="/ImportationLCL/lcl/rates/'.$account->contract_id.'/1" class=""><i class="la la-credit-card" title="Rates"></i></a>
                <!--&nbsp;
                <a href="" class=""><i class="la la-rotate-right" title="Surchargers"></i></a>-->
                &nbsp;
                <a href="/ImportationLCL/DownloadAccountclcl/'.$account->id.'" class=""><i class="la la-cloud-download" title="Download"></i></a>
                &nbsp;
                <a href="#" id="delete-account-clcl" data-id-account-clcl="'.$account->id.'" class=""><i class="la la-remove" title="Delete"></i></a>';
                }else{
                    return '
                <a href="/ImportationLCL/DownloadAccountclcl/'.$account->id.'" class=""><i class="la la-cloud-download" title="Download"></i></a>
                &nbsp;
                <a href="#" id="delete-account-clcl" data-id-account-clcl="'.$account->id.'" class=""><i class="la la-remove" title="Delete"></i></a>';
                }
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function DestroyAccount($id){
        try{
            $account = AccountLcl::find($id);
            Storage::disk('LclAccount')->delete($account->namefile);
            $account->delete();
            return 1;
        } catch(Exception $e){
            return 2;
        }
    }

    public function Download($id){
        $account    = AccountLcl::find($id);
        $time       = new \DateTime();
        $now        = $time->format('d-m-y');
        $company    = CompanyUser::find($account->company_user_id);
        $extObj     = new \SplFileInfo($account->namefile);
        $ext        = $extObj->getExtension();
        $name       = $account->id.'-'.$company->name.'_'.$now.'-FLC.'.$ext;
        try{
            return Storage::disk('s3_upload')->download('Account/LCL/'.$account->namefile,$name);
        } catch(\Exception $e){
            return Storage::disk('LclAccount')->download($account->namefile,$name);
        }
    }


    //********************************************************************
    public function store(Request $request)
    {

    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
