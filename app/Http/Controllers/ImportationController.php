<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\Rate;
use PrvRates;
use PrvHarbor;
use HelperAll;
use PrvCarrier;
use App\Harbor;
use App\Region;
use App\Carrier;
use App\Country;
use App\FileTmp;
use App\Company;
use App\Contact;
use App\FailRate;
use App\Currency;
use App\Contract;
use App\Container;
use PrvValidation;
use App\Direction;
use App\Surcharge;
use PrvSurchargers;
use App\Failcompany;
use App\LocalCharge;
use App\TypeDestiny;
use App\CompanyUser;
use App\ScheduleType;
use App\Failedcontact;
use App\LocalCharPort;
use App\FailSurCharge;
use App\GroupContainer;
use App\Jobs\GeneralJob;
use App\ContractFclFile;
use App\ContractCarrier;
use App\CalculationType;
use App\LocalCharCountry;
use App\LocalCharCarrier;
use App\NewContractRequest;
use Illuminate\Http\Request;
use App\ContainerCalculation;
use App\Jobs\ReprocessRatesJob;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\DB;
use App\Jobs\SynchronImgCarrierJob;
use Illuminate\Support\Facades\Log;
use App\MyClass\Excell\MyReadFilter;
use App\Jobs\ImportationRatesFclJob;
use Spatie\MediaLibrary\MediaStream;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\Models\Media;
use App\Jobs\ReprocessSurchargersJob;
use App\MyClass\Excell\ChunkReadFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\NewContractRequest as RequestFcl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Jobs\ImportationRatesSurchargerJob;
use App\AccountImportationContractFcl as AccountFcl;

class ImportationController extends Controller
{

    public function ReprocesarRates(Request $request, $id){
        $countfailrates = FailRate::where('contract_id','=',$id)->count();
        if($countfailrates <= 150){
            $failrates = FailRate::where('contract_id','=',$id)->get();
            foreach($failrates as $failrate){

                $carrierEX          = '';
                $twuentyEX          = '';
                $fortyEX            = '';
                $fortyhcEX          = '';
                $currencyEX         = '';
                $originResul        = '';
                $originExits        = '';
                $originV            = '';
                $destinResul        = '';
                $destinationExits   = '';
                $destinationV       = '';
                $originEX           = '';
                $destinyEX          = '';
                $twentyVal          = '';
                $fortyVal           = '';
                $fortyhcVal         = '';
                $carrierVal         = '';
                $carrierArr         = '';
                $twentyArr          = '';
                $fortyArr           = '';
                $fortyhcArr         = '';
                $currencyArr        = '';
                $currencyVal        = '';
                $currenct           = '';
                $fortynorVal        = '';
                $fortyfiveVal       = '';
                $scheduleTVal       = null;

                $curreExitBol       = false;
                $originB            = false;
                $destinyB           = false;
                $twentyExiBol       = false;
                $fortyExiBol        = false;
                $fortyhcExiBol      = false;
                $values             = true;
                $carriExitBol       = false;
                $fortynorExiBol     = false;
                $fortyfiveExiBol    = false;
                $scheduleTBol       = false;

                $originEX       = explode('_',$failrate->origin_port);
                $destinyEX      = explode('_',$failrate->destiny_port);
                $carrierArr     = explode('_',$failrate->carrier_id);
                $twentyArr      = explode('_',$failrate->twuenty);
                $fortyArr       = explode('_',$failrate->forty);
                $fortyhcArr     = explode('_',$failrate->fortyhc);
                $fortynorArr    = explode('_',$failrate->fortynor);
                $fortyfiveArr   = explode('_',$failrate->fortyfive);
                $currencyArr    = explode('_',$failrate->currency_id);
                $scheduleTArr   = explode('_',$failrate->schedule_type);

                $carrierEX     = count($carrierArr);
                $twuentyEX     = count($twentyArr);
                $fortyEX       = count($fortyArr);
                $fortyhcEX     = count($fortyhcArr);
                $currencyEX    = count($currencyArr);

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
                if( $twuentyEX   <= 1 &&
                   $fortyEX     <= 1 &&  $fortyhcEX   <= 1 &&
                   $currencyEX  <= 1 ){

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

                    //---------------- 20' ------------------------------------------------------------------

                    if(empty($twentyArr[0]) != true || (int)$twentyArr[0] == 0){
                        $twentyExiBol = true;
                        $twentyVal   = floatval($twentyArr[0]);
                    }

                    //----------------- 40' -----------------------------------------------------------------

                    if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                        $fortyExiBol = true;
                        $fortyVal   = floatval($fortyArr[0]);
                    }

                    //----------------- 40'HC --------------------------------------------------------------

                    if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                        $fortyhcExiBol = true;
                        $fortyhcVal   = floatval($fortyhcArr[0]);
                    }

                    //----------------- 40'NOR -------------------------------------------------------------

                    if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                        $fortynorExiBol = true;
                        $fortynorVal   = floatval($fortynorArr[0]);
                    }

                    //----------------- 45' ----------------------------------------------------------------

                    if(empty($fortyfiveArr[0]) != true || (int)$fortyfiveArr[0] == 0){
                        $fortyfiveExiBol = true;
                        $fortyfiveVal   = floatval($fortyfiveArr[0]);
                    }

                    if($twentyVal == 0
                       && $fortyVal == 0
                       && $fortyhcVal == 0
                       && $fortynorVal == 0
                       && $fortyfiveVal == 0){
                        $values = false;
                    }
                    //----------------- Currency -----------------------------------------------------------

                    $currenct = Currency::where('alphacode','=',$currencyArr[0])->orWhere('id','=',$currencyArr[0])->first();

                    if(empty($currenct->id) != true){
                        $curreExitBol = true;
                        $currencyVal =  $currenct->id;
                    }

                    $scheduleT = ScheduleType::where('name','=',$scheduleTArr[0])->first();

                    if(empty($scheduleT->id) != true || $scheduleTArr[0] == null){
                        $scheduleTBol = true;
                        if($scheduleTArr[0] != null){
                            $scheduleTVal =  $scheduleT->id;
                        } else {
                            $scheduleTVal = null;
                        }
                    }

                    $array = [
                        'ori' => $originB,
                        'des' => $destinyB,
                        '20' => $twentyExiBol,
                        '40' => $fortyExiBol,
                        '40h' => $fortyhcExiBol,
                        '40n' => $fortynorExiBol,
                        '45' => $fortyfiveExiBol,
                        'val' => $values,
                        'sch' => $scheduleTBol,
                        'car' => $carriExitBol,
                        'curr' => $curreExitBol
                    ];
                    //dd($array);


                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if($originB == true && $destinyB == true &&
                       $twentyExiBol   == true && $fortyExiBol    == true &&
                       $fortyhcExiBol  == true && $fortynorExiBol == true &&
                       $fortyfiveExiBol == true && $values        == true &&
                       $scheduleTBol == true && $curreExitBol   == true && $carriExitBol == true){
                        $collecciont = '';
                        $exists = null;
                        $exists = Rate::where('origin_port',$originV)
                            ->where('destiny_port',$destinationV)
                            ->where('carrier_id',$carrierVal)
                            ->where('contract_id',$id)
                            ->where('twuenty',$twentyVal)
                            ->where('forty',$fortyVal)
                            ->where('fortyhc',$fortyhcVal)
                            ->where('fortynor',$fortynorVal)
                            ->where('fortyfive',$fortyfiveVal)
                            ->where('currency_id',$currencyVal)
                            ->where('schedule_type_id',$scheduleTVal)
                            ->where('transit_time',(int)$failrate['transit_time'])
                            ->where('via',$failrate['via'])
                            ->first();
                        if(count($exists) == 0){
                            $collecciont = Rate::create([
                                'origin_port'       => $originV,
                                'destiny_port'      => $destinationV,
                                'carrier_id'        => $carrierVal,                            
                                'contract_id'       => $id,
                                'twuenty'           => $twentyVal,
                                'forty'             => $fortyVal,
                                'fortyhc'           => $fortyhcVal,
                                'fortynor'          => $fortynorVal,
                                'fortyfive'         => $fortyfiveVal,
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
            $contractData = Contract::find($id);
            $usersNotifiques = User::where('type','=','admin')->get();
            foreach($usersNotifiques as $userNotifique){
                $message = 'The Rates was Reprocessed. Contract: ' . $contractData->number ;
                $userNotifique->notify(new N_general($userNotifique,$message));
            }

        } else {
            if(env('APP_VIEW') == 'operaciones') {
                ReprocessRatesJob::dispatch($id)->onQueue('operaciones');
            }else {
                ReprocessRatesJob::dispatch($id);				
            }
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'The rates are reprocessing in the background');
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$id,'1']);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The rates are being reprocessed');
        $countfailratesNew = FailRate::where('contract_id','=',$id)->count();
        if($countfailratesNew > 0){
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$id,'1']);
        }else{
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$id,'0']);
        }
    }

    public function ReprocesarSurchargers(Request $request, $id){
        $countfailsurchargers = FailSurCharge::where('contract_id','=',$id)->count();
        if($countfailsurchargers <= 150){
            $failsurchargers = FailSurCharge::where('contract_id','=',$id)->get();
            foreach($failsurchargers as $FailSurchager){

                $surchargerEX       = '';
                $origenEX           = '';
                $destinyEX          = '';
                $typedestinyEX      = '';
                $calculationtypeEX  = '';
                $ammountEX          = '';
                $currencyEX         = '';
                $carrierEX          = '';
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

                $carrierB           = false;
                $calculationtypeB   = false;
                $typedestinyB       = false;
                $originB            = false;
                $destinyB           = false;
                $surcharB           = false;
                $currencyB          = false;


                $surchargerEX       = explode('_',$FailSurchager['surcharge_id']);
                $originEX           = explode('_',$FailSurchager['port_orig']);
                $destinyEX          = explode('_',$FailSurchager['port_dest']);
                $typedestinyEX      = explode('_',$FailSurchager['typedestiny_id']);
                $calculationtypeEX  = explode('_',$FailSurchager['calculationtype_id']);
                $ammountEX          = explode('_',$FailSurchager['ammount']);
                $currencyEX         = explode('_',$FailSurchager['currency_id']);
                $carrierEX          = explode('_',$FailSurchager['carrier_id']);

                if(count($surchargerEX) <= 1     && count($typedestinyEX) <= 1
                   && count($typedestinyEX) <= 1 && count($calculationtypeEX) <= 1
                   && count($ammountEX) <= 1     && count($currencyEX) <= 1){

                    // Origen Y Destino ------------------------------------------------------------------------

                    if($FailSurchager->differentiator  == 1){
                        $resultadoPortOri = PrvHarbor::get_harbor($originEX[0]);
                        $originV  = $resultadoPortOri['puerto'];
                    } else if($FailSurchager->differentiator  == 2){
                        $resultadoPortOri = PrvHarbor::get_country($originEX[0]);
                        $originV  = $resultadoPortOri['country'];
                    }
                    if($resultadoPortOri['boolean']){
                        $originB = true;    
                    }

                    if($FailSurchager->differentiator  == 1){
                        $resultadoPortDes = PrvHarbor::get_harbor($destinyEX[0]);
                        $destinationV  = $resultadoPortDes['puerto'];
                    } else if($FailSurchager->differentiator  == 2){
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
                        $calculationtypeV = $calculationtypeV['id'];
                    }

                    $calculationtypeB = true;
                    //  Amount ---------------------------------------------------------------------------------

                    $amountV = floatval($ammountEX[0]);

                    //  Currency -------------------------------------------------------------------------------

                    $currencyV = Currency::where('alphacode','=',$currencyEX[0])->first();
                    if(count($currencyV) == 1){
                        $currencyB = true;
                        $currencyV = $currencyV['id'];
                    }

                    //  Carrier -------------------------------------------------------------------------------
                    $carrierArr      = PrvCarrier::get_carrier($carrierEX[0]);
                    $carrierB        = $carrierArr['boolean'];
                    $carrierV        = $carrierArr['carrier'];

                    /*$colleccion = collect([]);
                    $colleccion = [
                        'origen'            =>  $originV,
                        'destiny'           =>  $destinationV,
                        'surcharge'         =>  $surchargerV,
                        'typedestuny'       =>  $typedestunyV,
                        'calculationtypeV'  =>  $calculationtypeV,
                        'amountV'           =>  $amountV,
                        'currencyV'         =>  $currencyV,
                        'carrierV'          =>  $carrierV,
                        'relation'          =>  $carrierArr['relation'],
                    ];

                    dd($colleccion);*/

                    if($originB == true     && $destinyB == true 
                       && $surcharB == true && $typedestinyB == true
                       && $calculationtypeB == true && $currencyB == true
                       && $carrierB == true){

                        $LocalchargeId = null;
                        $LocalchargeId = LocalCharge::where('surcharge_id',$surchargerV)
                            ->where('typedestiny_id',$typedestunyV)
                            ->where('contract_id',$id)
                            ->where('calculationtype_id',$calculationtypeV)
                            ->where('ammount',$amountV)
                            ->where('currency_id',$currencyV)
                            ->first();

                        if(count($LocalchargeId) == 0){
                            $LocalchargeId = LocalCharge::create([
                                'surcharge_id'          => $surchargerV,
                                'typedestiny_id'        => $typedestunyV,
                                'contract_id'           => $id,
                                'calculationtype_id'    => $calculationtypeV,
                                'ammount'               => $amountV,
                                'currency_id'           => $currencyV
                            ]);
                        }

                        $LocalchargeId = $LocalchargeId->id;

                        $existCa = null;
                        $existCa = LocalCharCarrier::where('carrier_id',$carrierV)
                            ->where('localcharge_id',$LocalchargeId)->first();
                        if(count($existCa) == 0){
                            LocalCharCarrier::create([
                                'carrier_id'     => $carrierV,
                                'localcharge_id' => $LocalchargeId
                            ]);
                        }

                        if($FailSurchager->differentiator  == 1){
                            $existsP = null;
                            $existsP = LocalCharPort::where('port_orig',$originV)
                                ->where('port_dest',$destinationV)
                                ->where('localcharge_id',$LocalchargeId)
                                ->first();
                            if(count($existsP) == 0){
                                LocalCharPort::create([
                                    'port_orig'         => $originV,
                                    'port_dest'         => $destinationV,
                                    'localcharge_id'    => $LocalchargeId                
                                ]);      
                            }
                        } else if($FailSurchager->differentiator  == 2){
                            $existsC = null;
                            $existsC = LocalCharCountry::where('country_orig',$originV)
                                ->where('country_dest',$destinationV)
                                ->where('localcharge_id',$LocalchargeId)
                                ->first();
                            if(count($existsC) == 0){
                                LocalCharCountry::create([
                                    'country_orig'      => $originV,
                                    'country_dest'      => $destinationV,
                                    'localcharge_id'    => $LocalchargeId                
                                ]);
                            }
                        }

                        $FailSurchager->forceDelete();
                    }
                }

            }

            $contractData = Contract::find($id);
            $usersNotifiques = User::where('type','=','admin')->get();
            foreach($usersNotifiques as $userNotifique){
                $message = 'The Surchargers was Reprocessed. Contract: ' . $contractData->number ;
                $userNotifique->notify(new N_general($userNotifique,$message));
            }

        } else {
            if(env('APP_VIEW') == 'operaciones') {
                ReprocessSurchargersJob::dispatch($id)->onQueue('operaciones');
            }else {
                ReprocessSurchargersJob::dispatch($id);				
            }
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'The Surchargers are reprocessing in the background');
            return redirect()->route('Failed.Surcharge.F.C.D',[$id,'1']);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The Surchargers are being reprocessed');
        $countfailSurChargersNew = FailSurCharge::where('contract_id','=',$id)->count();

        if($countfailSurChargersNew > 0){
            return redirect()->route('Failed.Surcharge.F.C.D',[$id,'1']);
        }else{
            return redirect()->route('Failed.Surcharge.F.C.D',[$id,'0']);
        }
    }

    ////BORRAR UNA VEZ HECHAS LAS PRUEBAS
    //    // precarga la vista para importar rates o rates mas surchargers
    //    public function LoadViewImporContractFcl(){
    //        $harbor         = harbor::all()->pluck('display_name','id');
    //        $country        = Country::all()->pluck('name','id');
    //        $region         = Region::all()->pluck('name','id');
    //        $carrier        = carrier::all()->pluck('name','id');
    //        $direction      = [null=>'Please Select'];
    //        $direction2      = Direction::all();
    //        foreach($direction2 as $d){
    //            $direction[$d['id']]=$d->name;
    //        }
    //        $companysUser   = CompanyUser::all()->pluck('name','id');
    //        $typedestiny    = TypeDestiny::all()->pluck('description','id');
    //        return view('importation.ImporContractFcl',compact('harbor','direction','country','region','carrier','companysUser','typedestiny'));
    //    }

    // precarga la vista para importar rates mas surchargers desde Request
    public function requestProccess($id,$selector,$request_id){
        $load_carrier   = false;
        $carrier_exec   = Carrier::where('name','ALL')->first();
        $carrier_exec   = $carrier_exec->id;
        $equiment       = ['id' => null,'name' => null,'color' => null];
        $json_rq        = null;
        if($selector == 1){
            $requestfcl     = RequestFcl::find($id);
            @$requestfcl->load('Requestcarriers');
            if(json_decode($requestfcl->request_data,true) != null){
                $json_rq = json_decode($requestfcl->request_data,true);
                if(!empty($json_rq['group_containers'])){
                    $equiment['id']     = $json_rq['group_containers']['id'];
                    $equiment['name']   = $json_rq['group_containers']['name'];
                    $equiment['color']  = $json_rq['group_containers']['color'];
                }
            } else {
                $groupContainer = GroupContainer::find(1);
                $json_rq        = json_decode($groupContainer->data,true);
                $equiment['id']     = $groupContainer->id;
                $equiment['name']   = $groupContainer->name;
                $equiment['color']  = $json_rq['color'];
            }
            //dd($requestfcl,$equiment);
            if(count($requestfcl->Requestcarriers) == 1){
                foreach($requestfcl->Requestcarriers as $carrier_uniq){
                    if($carrier_uniq->id != $carrier_exec){
                        $load_carrier = true;
                    }
                }
            }
        } elseif($selector == 2){
            $contract     = Contract::find($id);
            @$contract->load('carriers');
            if(!empty($contract->gp_container_id)){
                $groupContainer = GroupContainer::find($contract->gp_container_id);
                $json_rq        = json_decode($groupContainer->data,true);
                $equiment['id']     = $groupContainer->id;
                $equiment['name']   = $groupContainer->name;
                $equiment['color']  = $json_rq['color'];
            } else {
                $groupContainer = GroupContainer::find(1);
                $json_rq        = json_decode($groupContainer->data,true);
                $equiment['id']     = $groupContainer->id;
                $equiment['name']   = $groupContainer->name;
                $equiment['color']  = $json_rq['color'];
            }
            //dd($contract,$equiment);
            if(count($contract->carriers) == 1){
                foreach($contract->carriers as $carrier_uniq){
                    if($carrier_uniq->id != $carrier_exec){
                        $load_carrier = true;
                    }
                }
            }
        }
        $harbor         = harbor::pluck('display_name','id');
        $country        = Country::pluck('name','id');
        $region         = Region::pluck('name','id');
        $carrier        = carrier::pluck('name','id');
        $coins          = currency::pluck('alphacode','id');
        $currency       = currency::where('alphacode','USD')->pluck('id');
        $direction      = Direction::pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        if($selector == 1){
            return view('importationV2.Fcl.newImport',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','requestfcl','selector','load_carrier','coins','currency','equiment'));    

            //            return view('importation.ImportContractFCLRequest',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','requestfcl','selector','load_carrier'));    
        } elseif($selector == 2){
            return view('importationV2.Fcl.newImport',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','contract','selector','request_id','load_carrier','coins','currency','equiment'));

            //            return view('importation.ImportContractFCLRequest',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','contract','selector','request_id','load_carrier'));
        }

    }

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request){
        //dd($request->all());
        $now                = new \DateTime();
        $now2               = $now;
        $now                = $now->format('dmY_His');
        $now2               = $now2->format('Y-m-d');
        $datTypeDes         = false;
        $name               = $request->name;
        $CompanyUserId      = $request->CompanyUserId;
        $request_id         = $request->request_id;
        $contract_id        = $request->contract_id;
        $selector           = $request->selector;
        $dataCarrier        = $request->DatCar;
        $carrierVal         = $request->carrier;
        $datTypeDes         = $request->DatTypeDes;
        $typedestinyVal     = $request->typedestiny;
        $chargeVal          = $request->chargeVal;
        $gp_container_id    = $request->gp_container_id;
        $validity           = explode('/',$request->validation_expire);

        $statustypecurren   = $request->valuesCurrency;
        $currency           = $request->currency;
        $statusPortCountry  = $request->valuesportcountry;
        $direction_id       = $request->direction;
        $file 				= $request->input('document');

        $carrierBol             = false;
        $PortCountryRegionBol   = false;
        $typedestinyBol         = false;
        $filebool               = false;
        $data                   = collect([]);
        //$contract_id            = 45;

        if(!empty($file)){

            $account = new AccountFcl();
            $account->name              = $name;
            $account->date              = $now2;
            $account->company_user_id   = $CompanyUserId;
            $account->request_id        = $request_id;
            $account->save();

            $account->addMedia(storage_path('tmp/importation/fcl/'.$file))->toMediaCollection('document','FclAccount');

            if($selector == 2){
                $contract = Contract::find($contract_id);
                $contract->account_id  = $account->id;
                $contract->update();
            } else {
                $contract   = new Contract();
                $contract->name             = $request->name;

                $contract->validity         = $validity[0];
                $contract->expire           = $validity[1];
                $contract->direction_id     = $direction_id;
                $contract->status           = 'incomplete';
                $contract->company_user_id  = $CompanyUserId;
                $contract->account_id       = $account->id;
                $contract->$gp_container_id = $gp_container_id;
                $contract->save();

                foreach($request->carrierM as $carrierVal){
                    ContractCarrier::create([
                        'carrier_id'    => $carrierVal,
                        'contract_id'   => $contract->id
                    ]);
                }
            }
            $contract->load('carriers');
            $contract_id = $contract->id;

            if(!empty($request_id)){
                $requestFile    = NewContractRequest::find($request_id);
                if(!empty($requestFile->id)){
                    if(empty($requestFile->contract_id)){
                        $requestFile->contract_id = $contract_id;
                        $requestFile->update();
                    }
                }
            }
            //dd($account,$contract,$requestFile);
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error File!!');
            return back();
        }

        $requestCont    = NewContractRequest::find($request_id);
        $data           = json_decode($requestCont->data);
        $columnsSelected = collect(['ORIGIN','DESTINY','CHARGE','CALCULATION TYPE']);
        //dd($data);

        //$account    = AccountFcl::find(29);

        $valuesSelecteds = collect([
            'company_user_id'   => $CompanyUserId,
            'request_id'        => $request_id,
            'selector'          => $selector,
            'chargeVal'         => $chargeVal,
            'contract_id'       => $contract_id,
            'acount_id'         => $account->id
        ]);

        $request_columns = [];
        foreach($data->containers as $dataContainers){
            $columnsSelected->push($dataContainers->code);
            array_push($request_columns,$dataContainers->code);
        }

        $valuesSelecteds->put('group_container_id',$data->group_containers->id);
        $valuesSelecteds->put('request_columns',$request_columns);

        // ------- TYPE DESTINY -------------------

        if($datTypeDes){
            $typedestinyBol = true;
            $valuesSelecteds->put('typeDestinyVal',$typedestinyVal);
            $valuesSelecteds->put('select_typeDestiny',$typedestinyBol);
        } else {
            $columnsSelected->push('TYPE DESTINY');
            $valuesSelecteds->put('select_typeDestiny',$typedestinyBol);
        }

        // ------- CURRENCY -----------------------
        if($statustypecurren == 1){
            $columnsSelected->push('CURRENCY');
            $valuesSelecteds->put('select_currency',1);
        } elseif($statustypecurren == 2){
            $valuesSelecteds->put('select_currency',2);
        } elseif($statustypecurren == 3){
            $valuesSelecteds->put('select_currency',3);           
            $valuesSelecteds->put('currencyVal',$currency);
        }

        // ------- CARRIER ------------------------
        if($dataCarrier == false){
            $columnsSelected->push('CARRIER');
            $valuesSelecteds->put('select_carrier',$carrierBol);
        } else {
            $carrierBol = true;
            $valuesSelecteds->put('carrierVal',$carrierVal);
            $valuesSelecteds->put('select_carrier',$carrierBol);
        }

        // ------- PUERTO/COUNTRY/REGION ----------

        if($statusPortCountry == 2){
            $PortCountryRegionBol = true;
            $columnsSelected->push('DIFFERENTIATOR');
            $valuesSelecteds->put('select_portCountryRegion',$PortCountryRegionBol);
        } else {
            $valuesSelecteds->put('select_portCountryRegion',$PortCountryRegionBol);
        }

        $mediaItem  = $account->getFirstMedia('document');
        $excel      = Storage::disk('FclAccount')->get($mediaItem->id.'/'.$mediaItem->file_name);
        Storage::disk('FclImport')->put($mediaItem->file_name,$excel);
        $excelF     = Storage::disk('FclImport')->url($mediaItem->file_name);

        $extObj     = new \SplFileInfo($mediaItem->file_name);
        $ext        = $extObj->getExtension();
        if(strnatcasecmp($ext,'xlsx')==0){
            $inputFileType = 'Xlsx';
        } else if(strnatcasecmp($ext,'xls')==0){
            $inputFileType = 'Xls';
        } else {
            $inputFileType = 'Csv';
        }

        $firstRow   =  new MyReadFilter(1,1);
        $reader     = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $reader->setReadFilter($firstRow);
        $spreadsheet = $reader->load($excelF);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        //$sheetData = $spreadsheet->getActiveSheet()->toArray(null,true,true,true);
        //dd($sheetData); 
        $final_columns = collect([]);
        foreach($columnsSelected as $columnSelect){
            foreach($sheetData as $rowD){
                foreach($rowD as $key => $cells){
                    //dd($key,$cells);
                    if($columnSelect ==  $cells){
                        $final_columns->put($cells,$key);
                    }
                }
            }
        }

        // LOAD CALCULATIONS FOR COLUMN ------------------------
        $column_calculatioT_bol_rq = true;
        $contenedores_to_cal_rq = Container::where('gp_container_id',$valuesSelecteds['group_container_id'])->get();
        foreach($contenedores_to_cal_rq as $row_cont_calcult_rq){
            $contenedores_calcult_rq =  null;
            //$contenedores_calcult =  ContainerCalculation::where('container_id',10)
            $contenedores_calcult_rq =  ContainerCalculation::where('container_id',$row_cont_calcult_rq->id)
                ->whereHas('calculationtype', function ( $query) {
                    $query->where('gp_pcontainer',true);
                })->get();
            //dd($contenedores_to_cal,$row_cont_calcult->code,$contenedores_calcult);
            if(count($contenedores_calcult_rq) > 1 || count($contenedores_calcult_rq) == 0){
                $column_calculatioT_bol_rq = false;
            }
        }

        if($column_calculatioT_bol_rq){
            // despacha el job
            $json_account  = json_encode(['final_columns'=>$final_columns->toArray(),'valuesSelecteds'=>$valuesSelecteds->toArray()]);
            $account->data = $json_account;
            $account->update();
            // colocar contract_id al despachar para evitar el borrado mientras se importa el contracto
            if(env('APP_VIEW') == 'operaciones') {
                ImportationRatesSurchargerJob::dispatch($account->id,$contract_id,\Auth::user()->id)->onQueue('operaciones'); //NO BORRAR!!
            }else {
                ImportationRatesSurchargerJob::dispatch($account->id,$contract_id,\Auth::user()->id); //NO BORRAR!!
            }
            return redirect()->route('redirect.Processed.Information',$contract_id);
        } else {

            Log::error('Container calculation type relationship error. Check Relationship in the module "Containers Calculation Types"');
            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Error in the relation Container-CalculationType');
            return back();
        }

        //dd($final_columns,$valuesSelecteds,$columnsSelected,$sheetData);
        ///////////////////////////////// JOB IMPORTATION ///////////////////////////////////////////////////////////////////
        // Id de cuenta, pasar parametro Job.
        $account_id             = $account->id;

        ///

        //$account                = AccountFcl::find(29);
        $account                = AccountFcl::find($account_id);
        $json_account_dc        = json_decode($account->data,true);
        $valuesSelecteds        = $json_account_dc['valuesSelecteds'];
        $final_columns          = $json_account_dc['final_columns'];
        //dd($valuesSelecteds,$final_columns);

        $contract_id            = $valuesSelecteds['contract_id'];
        $groupContainer_id      = $valuesSelecteds['group_container_id'];
        $column_calculatioT_bol = true;
        $caracteres             = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':','1','2','3','4','5','6','7','8','9','0'];

        // LOAD CALCULATIONS FOR COLUMN ------------------------
        $contenedores_to_cal = Container::where('gp_container_id',$groupContainer_id)->get();
        $conatiner_calculation_id = [];
        foreach($contenedores_to_cal as $row_cont_calcult){
            $contenedores_calcult =  null;
            //$contenedores_calcult =  ContainerCalculation::where('container_id',10)
            $contenedores_calcult =  ContainerCalculation::where('container_id',$row_cont_calcult->id)
                ->whereHas('calculationtype', function ( $query) {
                    $query->where('gp_pcontainer',true);
                })->get();
            //dd($contenedores_to_cal,$row_cont_calcult->code,$contenedores_calcult);
            if(count($contenedores_calcult) == 1){
                foreach($contenedores_calcult as $contenedor_calcult){   
                    $conatiner_calculation_id[$row_cont_calcult->code] = $contenedor_calcult->calculationtype_id;
                }
            } else if(count($contenedores_calcult) > 1 || count($contenedores_calcult) == 0){
                $column_calculatioT_bol = false;
            }
        }
        //dd($conatiner_calculation_id);

        // --------------- AL FINALIZAR  CARGAR LA EXATRACCION DESDE S3 -----------------

        $mediaItem  = $account->getFirstMedia('document');
        $excel      = Storage::disk('FclAccount')->get($mediaItem->id.'/'.$mediaItem->file_name);
        Storage::disk('FclImport')->put($mediaItem->file_name,$excel);
        $excelF     = Storage::disk('FclImport')->url($mediaItem->file_name);

        $extObj     = new \SplFileInfo($mediaItem->file_name);
        $ext        = $extObj->getExtension();

        if(strnatcasecmp($ext,'xlsx')==0){
            $inputFileType = 'Xlsx';
        } else if(strnatcasecmp($ext,'xls')==0){
            $inputFileType = 'Xls';
        } else {
            $inputFileType = 'Csv';
        }
        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($excelF);
        $writer = IOFactory::createWriter($spreadsheet, "Csv");
        $writer->setSheetIndex(0);
        $excelF = str_replace($ext,'csv',$excelF);
        $inputFileType = 'Csv';
        $writer->save($excelF);
        //dd($excelF,$extObj,$ext);
        // --------------- AL FINALIZAR  CARGAR LA EXATRACCION DESDE S3 -----------------

        if($column_calculatioT_bol){
            $chunkRow   =  new ChunkReadFilter();

            $readerJob  = IOFactory::createReader($inputFileType);
            $readerJob->setReadDataOnly(true);
            //$readerJob->setReadFilter($chunkRow);

            $chunkSize = 2;

            $spreadsheetJob = $readerJob->load($excelF);
            $sheetData = $spreadsheetJob->getActiveSheet()->toArray();
            //dd($final_columns->toArray(),$valuesSelecteds->toArray(),$columnsSelected->toArray());

            $originExc              = $final_columns["ORIGIN"];// lectura de excel
            $destinyExc             = $final_columns["DESTINY"];// lectura de excel
            $chargeExc              = $final_columns["CHARGE"];// lectura de excel
            $calculationtypeExc     = $final_columns["CALCULATION TYPE"];// lectura de excel
            $chargeExc              = $final_columns["CHARGE"];// lectura de excel

            $company_user_id        = $valuesSelecteds['company_user_id'];
            $statusPortCountry      = $valuesSelecteds['select_portCountryRegion'];
            $statusTypeDestiny      = $valuesSelecteds['select_typeDestiny'];
            $statusCarrier          = $valuesSelecteds['select_carrier'];
            $chargeVal              = $valuesSelecteds['chargeVal'];
            $request_columns        = $valuesSelecteds['request_columns'];
            $statusCurrency         = $valuesSelecteds['select_currency'];

            $currencyVal            = '';

            // DIFERENCIADOR DE PUERTO CONTRY/REGION ---------------
            if($statusPortCountry){
                $differentiator = $final_columns["DIFFERENTIATOR"];            
            }

            // CURRENCY --------------------------------------------
            if($statusCurrency == 3){
                $currencyVal    = $valuesSelecteds['currencyVal'];            
            } else if($statusCurrency == 1){
                $currencyExc    = $final_columns["CURRENCY"];                        
            }

            // TYPE DESTINY ----------------------------------------
            if(!$statusTypeDestiny){
                $typedestinyExc     = $final_columns["TYPE DESTINY"];            
            }

            if(!$statusCarrier){
                $carrierExc     = $final_columns["CARRIER"];            
            }
            $columns_rt_ident = [];
            if($groupContainer_id == 1){
                $contenedores_rt = Container::where('gp_container_id',$groupContainer_id)->where('options->column',true)->get();
                foreach($contenedores_rt as $conten_rt){
                    $conten_rt->options = json_decode($conten_rt->options);
                    $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
                }
            }

            $countRow = 1;
            foreach($sheetData as $row){
                if($countRow > 1){
                    //dd($final_columns->toArray(),$valuesSelecteds->toArray(),$columnsSelected->toArray(),$row);

                    //------------------ COLUMNS SELECTEDS VALUES/CURRENCY/OPTIONS ----------------------------
                    $contenedores = Container::where('gp_container_id',$groupContainer_id)->get();
                    $columna_cont = [];
                    $currency_bol = [];
                    foreach($contenedores as $contenedor){
                        $options_cont = null;
                        $options_cont = json_decode($contenedor->options);
                        if(in_array($contenedor->code,$request_columns)){ // Asociamos en una matriz llaves Valores y moneda que exista en la seleccion
                            if($statusCurrency == 3){ //currency seleccionado en el panel(select) no hay columna en el excel
                                $value_ = null;
                                $value_ = floatval($row[$final_columns[$contenedor->code]]);
                                $columna_cont[$contenedor->code] = [$value_,$currencyVal,$options_cont->optional,false,$options_cont->column];
                                $currency_bol[$contenedor->code] = true;
                            } else if($statusCurrency == 2){ // valor y currency en la misma columna del excel
                                $value_arr = null;
                                $value_arr = explode(' ',$row[$final_columns[$contenedor->code]]);
                                if(count($value_arr) == 1){
                                    array_push($value_arr,'_E_E');
                                    array_push($value_arr,$options_cont->optional);
                                    array_push($value_arr,false);
                                    array_push($value_arr,$options_cont->column);
                                    $currency_bol[$contenedor->code] = false;
                                    $value_arr[0] = floatval($value_arr[0]);
                                    $columna_cont[$contenedor->code] = $value_arr;
                                } else if(count($value_arr) > 1){
                                    $curren_obj = Currency::where('alphacode','=',$value_arr[1])->first();
                                    if(!empty($curren_obj->id)){
                                        $value_arr[1] = $curren_obj->id;
                                        if(count($value_arr) == 2){
                                            $currency_bol[$contenedor->code] = true;
                                        } else {
                                            $value_arr[1] = $value_arr[1].'_E_E'; 
                                            $currency_bol[$contenedor->code] = false;
                                        }

                                        if(count($value_arr) == 2){
                                            array_push($value_arr,$options_cont->optional);
                                            array_push($value_arr,false);
                                            array_push($value_arr,$options_cont->column);
                                        } else if(count($value_arr) == 3){
                                            $value_arr[2] = $options_cont->optional;
                                            array_push($value_arr,false);
                                            array_push($value_arr,$options_cont->column);
                                        } else if(count($value_arr) == 4){
                                            $value_arr[2] = $options_cont->optional;
                                            $value_arr[3] = false;
                                            array_push($value_arr,$options_cont->column);
                                        }
                                        $value_arr[0] = floatval($value_arr[0]);
                                        $columna_cont[$contenedor->code] = $value_arr;
                                    } else {
                                        $value_arr[0] = floatval($value_arr[0]);
                                        $columna_cont[$contenedor->code] = [$value_arr[0],$value_arr[1].'_E_E',$options_cont->optional,false,$options_cont->column];
                                        $currency_bol[$contenedor->code] = false;
                                    }
                                }
                            } else if($statusCurrency == 1){// columna sola de currency en el excel
                                $value_cur  = null;
                                $value_cur  = trim($row[$currencyExc]);
                                $curren_obj = Currency::where('alphacode','=',$value_cur)->first();
                                //                                try{
                                //                                    $curren_obj->id;
                                //                                } catch(\Exception $e){
                                //                                    dd($statusCurrency,$currencyExc,$value_cur,$curren_obj,empty($curren_obj->id));
                                //                                }
                                if(!empty($curren_obj->id)){
                                    $value_cur = $curren_obj->id;
                                    $currency_bol[$contenedor->code] = true;
                                } else {
                                    $value_cur = $value_cur.'_E_E';                                    
                                    $currency_bol[$contenedor->code] = false;
                                }
                                $columna_cont[$contenedor->code] = [floatval($row[$final_columns[$contenedor->code]]),$value_cur,$options_cont->optional,false,$options_cont->column];
                            }
                            //array_push($columna_cont[$contenedor->code],false);
                        } else { // Agregamos en una matriz llaves Valores y moneda que no existen en la seleccion pero si en el equipo Dry,RF,FR,OP....
                            $currency_bol[$contenedor->code] = true;
                            $columna_cont[$contenedor->code] = [0.00,149,$options_cont->optional,true,$options_cont->column];
                        }
                    }

                    //  0 --->  valor.
                    //  1 --->  moneda.
                    //  2 --->  opcional en el comparador (nor y 45) (true si es opcional).
                    //  3 --->  la columna se agrego automaticamente(true) porque el usuario no la agrego, false no se agreo A.
                    //  5 --->  la columna pertenece a una columna(true) o a un json (false).


                    //dd($columna_cont,$currency_bol,$statusCurrency);
                    //--- PORT/CONTRY/REGION BOOL -------------------------------------
                    $differentiatorVal = '';
                    if($statusPortCountry){
                        $differentiatorVal = $row[$differentiator];
                    } else {
                        $differentiatorVal = 'port';
                    }

                    //--- ORIGIN ------------------------------------------------------
                    $oricount = 0;
                    $originMultps = explode('|',$row[$originExc]);
                    foreach($originMultps as $originMultCompact){
                        if(strnatcasecmp($differentiatorVal,'region') == 0){
                            $originMultCompact = trim($originMultCompact);
                            $regionsOR = Region::where('name','like','%'.$originMultCompact.'%')->with('CountriesRegions.country')->get();
                            if(count($regionsOR) == 1){
                                // region add
                                foreach($regionsOR as $regionor){   
                                    if($oricount == 0){
                                        $originMultps = $regionor->CountriesRegions->pluck('country')->pluck('name')->toArray();
                                    } else {
                                        foreach($regionor->CountriesRegions->pluck('country')->pluck('name')->toArray() as $oricountriesarray){
                                            array_push($originMultps,$oricountriesarray);
                                        }
                                    }
                                }
                            } elseif(count($regionsOR) == 0) {
                                // pais add
                                if($oricount == 0){
                                    $originMultps =[$originMultCompact];
                                } else {
                                    array_push($originMultps,$originMultCompact);
                                }
                            }
                        }
                        $oricount++;
                    }

                    //--- DESTINY -----------------------------------------------------
                    $descount = 0;
                    $destinyMultps = explode('|',$row[$destinyExc]);
                    foreach($destinyMultps as $destinyMultCompact){
                        if(strnatcasecmp($differentiatorVal,'region') == 0){
                            $destinyMultCompact = trim($destinyMultCompact);
                            $regionsDES = Region::where('name','like','%'.$destinyMultCompact.'%')->with('CountriesRegions.country')->get();
                            if(count($regionsDES) == 1){
                                // region add
                                foreach($regionsDES as $regiondes){                                            
                                    if($descount == 0){
                                        $destinyMultps = $regiondes->CountriesRegions->pluck('country')->pluck('name')->toArray();
                                    } else {
                                        foreach($regiondes->CountriesRegions->pluck('country')->pluck('name')->toArray() as $descountriesarray){
                                            array_push($destinyMultps,$descountriesarray);
                                        }
                                    }
                                }
                            } elseif(count($regionsDES) == 0) {
                                // pais add
                                if($descount == 0){
                                    $destinyMultps =[$destinyMultCompact];
                                } else {
                                    array_push($destinyMultps,$destinyMultCompact);
                                }

                            }
                        }
                        $descount++;
                    }

                    //--- INICION DE ERECORRIDO POR | ---------------------------------
                    foreach($originMultps as $originMult){
                        foreach($destinyMultps as $destinyMult){

                            $originVal              = '';
                            $destinyVal             = '';
                            $carrierVal             = '';
                            $typedestinyVal         = '';
                            $surchargeVal           = '';
                            $calculationtypeVal     = '';

                            $differentiatorBol       = false;
                            $origExiBol              = false;
                            $destiExitBol            = false;
                            $typeExiBol              = false;
                            $carriExitBol            = false;
                            $typeChargeExiBol        = false;
                            $calculationtypeExiBol   = false;
                            $typedestinyExitBol      = false;

                            $calculation_type_exc   = null;
                            $chargeExc_val          = null;
                            $calculation_type_exc   = $row[$calculationtypeExc];
                            $chargeExc_val          = $row[$chargeExc];


                            //--------------- DIFRENCIADOR HARBOR COUNTRY ---------------------------------------------
                            if($statusPortCountry){
                                if(strnatcasecmp($differentiatorVal,'country') == 0 || strnatcasecmp($differentiatorVal,'region') == 0){
                                    $differentiatorBol = true;
                                } 
                            }

                            //--------------- ORIGEN MULTIPLE O SIMPLE ------------------------------------------------
                            $originVal = trim($originMult);// hacer validacion de puerto en DB
                            if($differentiatorBol == false){
                                // El origen es  por puerto
                                $resultadoPortOri = PrvHarbor::get_harbor($originVal);
                                if($resultadoPortOri['boolean']){
                                    $origExiBol = true;    
                                }
                                $originVal  = $resultadoPortOri['puerto'];
                            } else if($differentiatorBol == true){
                                // El origen es  por country
                                $resultadocountrytOri = PrvHarbor::get_country($originVal);
                                if($resultadocountrytOri['boolean']){
                                    $origExiBol = true;    
                                }
                                $originVal  = $resultadocountrytOri['country'];
                            }

                            //---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
                            $destinyVal = trim($destinyMult);// hacer validacion de puerto en DB
                            if($differentiatorBol == false){
                                // El origen es  por Harbors
                                $resultadoPortDes = PrvHarbor::get_harbor($destinyVal);
                                if($resultadoPortDes['boolean']){
                                    $destiExitBol = true;    
                                }
                                $destinyVal  = $resultadoPortDes['puerto'];
                            } else if($differentiatorBol == true){
                                //El destino es por Country
                                $resultadocountryDes = PrvHarbor::get_country($destinyVal);
                                if($resultadocountryDes['boolean']){
                                    $destiExitBol = true;    
                                }
                                $destinyVal  = $resultadocountryDes['country'];
                            }

                            //--------------- Type Destiny ------------------------------------------------------------

                            if($statusTypeDestiny){
                                $typedestinyExitBol = true;
                                $typedestinyVal     = $valuesSelecteds['typeDestinyVal']; 
                            } else {
                                $typedestinyVal      = $row[$typedestinyExc]; // cuando el carrier existe en el excel
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
                            if($statusCarrier){
                                $carriExitBol   = true;
                                $carrierVal     = $valuesSelecteds['carrierVal']; // cuando se indica que no posee carrier 
                            } else {
                                $carrierVal     = $row[$carrierExc]; // cuando el carrier existe en el excel
                                $carrierArr     = PrvCarrier::get_carrier($carrierVal);
                                $carriExitBol   = $carrierArr['boolean'];
                                $carrierVal     = $carrierArr['carrier'];
                            }

                            //------------------ TYPE - CHARGE --------------------------------------------------------

                            if(!empty($chargeExc_val)){
                                $typeChargeExiBol = true;
                                if($chargeExc_val != $chargeVal){
                                    $surchargelist = Surcharge::where('name','=', $chargeExc_val)
                                        ->where('company_user_id','=', $company_user_id)
                                        ->first();
                                    if(empty($surchargelist) != true){
                                        $surchargeVal = $surchargelist['id'];
                                    }else{
                                        $surchargelist = Surcharge::create([
                                            'name'              => $chargeExc_val,
                                            'description'       => $chargeExc_val,
                                            'company_user_id'   => $company_user_id
                                        ]);
                                        $surchargeVal = $surchargelist->id;
                                    }
                                }
                            } else {
                                $surchargeVal = $chargeExc_val.'_E_E';
                            }

                            //------------------ CALCULATION TYPE -----------------------------------------------------
                            $calculationtype = null;
                            if(strnatcasecmp($calculation_type_exc,'PER_CONTAINER') == 0 ||
                               strnatcasecmp($calculation_type_exc,'PER_TEU') == 0){
                                $calculationtype = CalculationType::where('options->name','=',$calculation_type_exc)
                                    ->whereHas('containersCalculation.container', function ($query) use($groupContainer_id){
                                        $query->whereHas('groupContainer', function ($queryTw) use($groupContainer_id){
                                            $queryTw->where('gp_container_id',$groupContainer_id);
                                        });
                                    })->get();
                            } else {
                                $calculationtype = CalculationType::where('options->name','=',$calculation_type_exc)->get();
                            }

                            if(count($calculationtype) == 1){
                                $calculationtypeExiBol = true;
                                $calculationtypeVal = $calculationtype[0]['id'];
                            } else if(count($calculationtype) > 1){
                                $calculationtypeVal = $calculation_type_exc.'F.R + '.count($calculationtype).' fila '.$countRow.'_E_E';
                            } else{
                                $calculationtypeVal = $calculation_type_exc.' fila '.$countRow.'_E_E';
                            }
                            //------------------ VALIDACION DE CAMPOS VACIOS COLUMNAS 20 40 ...------------------------

                            // AYUDANTES -----------------------

                            //                            $currency_bol = [];
                            //              $statusCurrency           = 2;
                            //              $currency_bol['20DV']     = false;
                            //              $columna_cont['20DV'][1]  = 'USDD_E_E';
                            //              //                            $currency_bol['40DV'] = true;
                            //              //                            $currency_bol['40HC'] = true;
                            //              $calculation_type_exc     = 'PER_SHIPMENT';
                            //              $calculationtypeVal       = 6;
                            //              //$calculationtypeVal       = 'PER_SHIPMENTss F.R + 0 fila'.$countRow.'_E_E';
                            //              $calculationtypeExiBol    = true;
                            //
                            //              //$columna_cont = [];
                            //              $columna_cont['20DV'][0] = 1.0;
                            //              $columna_cont['40DV'][0] = 15.0;
                            //              $columna_cont['40HC'][0] = 7.00;

                            // FIN - AYUDANTES ------------------

                            $values = true; 
                            $values_uniq = [];
                            foreach($columna_cont as $columnaRow){
                                array_push($values_uniq,floatval($columnaRow[0]));
                            }
                            if(count(array_unique($values_uniq)) == 1 
                               && $values_uniq[0] == 0.00){
                                $values = false;
                            }

                            //dd($columna_cont,$values);

                            //------------------ VALIDACION DE CURRENCY FALSE Ó TRUE 20 40 ...------------------------

                            $variant_currency = true; 
                            $currency_uniq = [];
                            foreach($currency_bol as $columnCurrenRow){
                                if($columnCurrenRow == true){
                                    array_push($currency_uniq,1);
                                } else{
                                    array_push($currency_uniq,0);
                                }
                            }
                            if(count(array_unique($currency_uniq)) > 1){
                                $variant_currency = false;
                            } else if(count(array_unique($currency_uniq)) == 1 
                                      && $currency_uniq[0] == 0){
                                $variant_currency = false;
                            }
                            //dd($currency_bol,$currency_uniq,array_unique($currency_uniq),$variant_currency);

                            $datos_finales = [
                                'originVal'             => $originVal,
                                'destinyVal'            => $destinyVal,
                                'typedestinyVal'        => $typedestinyVal,  
                                'carrierVal'            => $carrierVal,  
                                'surchargeVal'          => $surchargeVal,  
                                'calculationtypeVal'    => $calculationtypeVal,
                                'contract_id'           => $contract_id,
                                'chargeVal'             => $chargeVal,          // indica la diferencia entre "rate" o surcharge
                                'columnas_por_request'  => $request_columns,    // valores por columna, incluye el currency por columna
                                'valores_por_columna'   => $columna_cont,       // valores por columna, incluye el currency por columna:
                                //  0 --->  valor.
                                //  1 --->  moneda.
                                //  2 --->  opcional en el comparador (nor y 45) (true si es opcional).
                                //  3 --->  la columna se agrego automaticamente(true) porque el usuario no la agrego, false no se agreo A.
                                //  5 --->  la columna pertenece a una columna(true) o a un json (false).
                                'columns_rt_ident'      => $columns_rt_ident,  // contiene los nombres de las columnas de rates, DRY options->column = true
                                'currencyBol_por_colum' => $currency_bol,       // Arreglo de  currency por columna
                                '$calculation_type_exc' => $calculation_type_exc,// Columna Calculation del excel
                                'origExiBol'            => $origExiBol,         // true si encontro el valor origen
                                'destiExitBol'          => $destiExitBol,       // true si encontro el valor destino
                                'typedestinyExitBol'    => $typedestinyExitBol, // true si encontro el valor type destiny
                                'carriExitBol'          => $carriExitBol,       // true si encontro el valor carrier
                                'calculationtypeExiBol' => $calculationtypeExiBol, // true si encontro el valor calculation type
                                'values'                => $values,            // true si si todos los valore son distintos de cero
                                'typeChargeExiBol'      => $typeChargeExiBol,  // true si el valor es distinto de vacio
                                'variant_currency'      => $variant_currency,  // true si el encontro todos los currency, false si alguno de sus contenedores no tiene currency
                                'differentiatorBol'     => $differentiatorBol, // falso para port, true  para country o region
                                'statusPortCountry'     => $statusPortCountry, // true status de activacion port contry region, false port
                                'statusTypeDestiny'     => $statusTypeDestiny, // true para Seleccion desde panel, false para mapeo de excel 
                                'statusCarrier'         => $statusCarrier,     // true para seleccion desde el panel, falso para mapear excel 
                                'statusCurrency'        => $statusCurrency,     // 3. val. por SELECT,1. columna de  currency, 2. currency mas valor juntos
                                'conatiner_calculation_id' => $conatiner_calculation_id, // asocia los calculations con las columnas. relacion columna => calculation_id
                                'column_calculatioT_bol'   => $column_calculatioT_bol // False si falla la asociacion, true si esta asociado correctamente

                            ];
                            if(strnatcasecmp($chargeExc_val,$chargeVal) == 0 && $typedestinyExitBol == false){
                                $typedestinyExitBol = true;
                            }
                            //dd($datos_finales);

                            /////////////////////////////////

                            // INICIO IF PARA FALLIDOS O BUENOS
                            if($origExiBol              == true 
                               && $destiExitBol         == true
                               && $typedestinyExitBol   == true 
                               && $carriExitBol         == true 
                               && $calculationtypeExiBol== true
                               && $values               == true 
                               && $variant_currency     == true 
                               && $typeChargeExiBol     == true){

                                ///////////////////////////////// GOOD

                                $container_json = null;

                                if(strnatcasecmp($chargeExc_val,$chargeVal) == 0){ // Rates 
                                    if($differentiatorBol == false){
                                        $twuenty_val    = 0;
                                        $forty_val      = 0;
                                        $fortyhc_val    = 0;
                                        $fortynor_val   = 0;
                                        $fortyfive_val  = 0;
                                        $currency_val   = null;

                                        if($groupContainer_id != 1){ //DISTINTO A DRY
                                            foreach($columna_cont as $key => $conta_row){
                                                if($conta_row[4] == false){
                                                    $container_json['C'.$key] = ''.$conta_row[0];
                                                }              
                                                $currency_val = $conta_row[1];
                                            }
                                            $container_json = json_encode($container_json);

                                        } else { // DRY
                                            foreach($columna_cont as $key => $conta_row){
                                                if($conta_row[4] == false){ // columna contenedores
                                                    $container_json['C'.$key] = ''.$conta_row[0];
                                                } else{ // por columna específica
                                                    if(strnatcasecmp($columns_rt_ident[$key],'twuenty') == 0){
                                                        $twuenty_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'forty') == 0){
                                                        $forty_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'fortyhc') == 0){
                                                        $fortyhc_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'fortynor') == 0){
                                                        $fortynor_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'fortyfive') == 0){
                                                        $fortyfive_val = $conta_row[0];                                        
                                                    }
                                                }  
                                                $currency_val = $conta_row[1];
                                            }
                                            $container_json = json_encode($container_json);

                                        }
                                        $exists = null;
                                        $exists = Rate::where('origin_port',$originVal)
                                            ->where('destiny_port',$destinyVal)
                                            ->where('carrier_id',$carrierVal)
                                            ->where('contract_id',$contract_id)
                                            ->where('twuenty',$twuenty_val)
                                            ->where('forty',$forty_val)
                                            ->where('fortyhc',$fortyhc_val)
                                            ->where('fortynor',$fortynor_val)
                                            ->where('fortyfive',$fortyfive_val)
                                            ->where('containers',$container_json)
                                            ->where('currency_id',$currency_val)
                                            ->get();
                                        //dd($twuenty_val,$forty_val,$fortyhc_val,$fortynor_val,$fortyfive_val,$container_json,$currency_val,$exists);
                                        if(count($exists) == 0){
                                            $ratesArre =  Rate::create([
                                                'origin_port'       => $originVal,
                                                'destiny_port'      => $destinyVal,
                                                'carrier_id'        => $carrierVal,
                                                'contract_id'       => $contract_id,
                                                'twuenty'           => $twuenty_val,
                                                'forty'             => $forty_val,
                                                'fortyhc'           => $fortyhc_val,
                                                'fortynor'          => $fortynor_val,
                                                'fortyfive'         => $fortyfive_val,
                                                'containers'        => $container_json,
                                                'currency_id'       => $currency_val
                                            ]);
                                        }
                                    }
                                } else { //Surcharges

                                    if($differentiatorBol == false){ //si es puerto verificamos si exite uno creado con puerto
                                        $typeplace = 'localcharports';
                                    }else {  //si es country verificamos si exite uno creado con country 
                                        $typeplace = 'localcharcountries';
                                    }

                                    if(strnatcasecmp($calculation_type_exc,'PER_CONTAINER') == 0){

                                        // ESTOS ARREGLOS SON DE EJEMPLO PARA IGUALDAD DE VALORES EN PER_CONTAINER / Solo condicional -------
                                        //$columna_cont['20DV'][0]    = 1200;
                                        //$columna_cont['20DV'][3]    = false;
                                        //$columna_cont['40DV'][0]    = 1200;
                                        //$columna_cont['40HC'][0]    = 1200;
                                        //$columna_cont['40NOR'][0]   = 1200;
                                        //$columna_cont['45HC'][0]    = 1200;
                                        //$columna_cont['40NOR'][3]   = true;
                                        //$columna_cont['45HC'][3]    = true;

                                        // Comparamos si todos los valores son iguales (PER_CONTAINER) o si son distintos, dependiendo de equipo DRY,RF...
                                        $equals_values = [];
                                        $key = null;
                                        foreach($columna_cont as $key => $conta_row){
                                            if($conta_row[3] == true && $conta_row[2] != true){
                                                array_push($equals_values,$conta_row[0]);
                                            } else if($conta_row[3] == false){
                                                array_push($equals_values,$conta_row[0]); 
                                            }
                                        }
                                        //dd($columna_cont,$equals_values,array_unique($equals_values),count(array_unique($equals_values)));

                                        if(count(array_unique($equals_values)) == 1){ //Calculation PER_CONTAINER 1 solo registro
                                            $currency_val   = null;
                                            $ammount        = null;
                                            $key            = null;
                                            foreach($columna_cont as $key => $conta_row){
                                                $ammount        = $conta_row[0];
                                                $currency_val   = $conta_row[1];
                                                break;
                                            }

                                            if($ammount != 0 || $ammount != 0.00){
                                                //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                                $surchargeObj = null;
                                                $surchargeObj = LocalCharge::where('surcharge_id',$surchargeVal)
                                                    ->where('typedestiny_id',$typedestinyVal)
                                                    ->where('contract_id',$contract_id)
                                                    ->where('calculationtype_id',$calculationtypeVal)
                                                    ->where('ammount',$ammount)
                                                    ->where('currency_id',$currency_val)
                                                    ->has($typeplace)
                                                    ->first();

                                                if(count($surchargeObj) == 0){
                                                    $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                        'surcharge_id'       => $surchargeVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contract_id,
                                                        'calculationtype_id' => $calculationtypeVal,
                                                        'ammount'            => $ammount,
                                                        'currency_id'        => $currency_val
                                                    ]);                                                    
                                                }

                                                //----------------------- CARRIERS -------------------------------------------
                                                $existsCar = null;
                                                $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                    ->where('localcharge_id',$surchargeObj->id)->first();
                                                if(count($existsCar) == 0){
                                                    LocalCharCarrier::create([ // tabla localcharcarriers
                                                        'carrier_id'        => $carrierVal,
                                                        'localcharge_id'    => $surchargeObj->id
                                                    ]);
                                                }

                                                //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------

                                                if($differentiatorBol){ // country
                                                    $existCount = null;
                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                        ->where('country_dest',$destinyVal)
                                                        ->where('localcharge_id',$surchargeObj->id)
                                                        ->first();
                                                    if(count($existCount) == 0){
                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                            'country_orig'      => $originVal,
                                                            'country_dest'      => $destinyVal,
                                                            'localcharge_id'    => $surchargeObj->id
                                                        ]);
                                                    }
                                                } else { // port
                                                    $existPort = null;
                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                        ->where('port_dest',$destinyVal)
                                                        ->where('localcharge_id',$surchargeObj->id)
                                                        ->first();
                                                    if(count($existPort) == 0){
                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                            'port_orig'      => $originVal,
                                                            'port_dest'      => $destinyVal,
                                                            'localcharge_id' => $surchargeObj->id
                                                        ]);
                                                    }
                                                }
                                            }

                                        } else if(count(array_unique($equals_values)) > 1){ //Calculation PER_ + "Contenedor o columna" registro por contenedor
                                            $key                = null;
                                            $rows_calculations   = [];
                                            foreach($columna_cont as $key => $conta_row){// Cargamos cada columna para despues insertarlas en la BD
                                                $rows_calculations[$key] = [
                                                    //'type'            => $key,
                                                    'calculationtype' => $conatiner_calculation_id[$key],
                                                    'ammount'         => $conta_row[0],
                                                    'currency'        => $conta_row[1]
                                                ];
                                            }
                                            //dd($rows_calculations);
                                            $key = null;
                                            foreach($rows_calculations as $key => $row_calculation){

                                                //dd($key,$row_calculation);
                                                if($row_calculation['ammount'] != 0 || $row_calculation['ammount'] != 0.00){
                                                    //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                                    $surchargeObj = null;
                                                    $surchargeObj = LocalCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contract_id)
                                                        ->where('calculationtype_id',$row_calculation['calculationtype'])
                                                        ->where('ammount',$row_calculation['ammount'])
                                                        ->where('currency_id',$row_calculation['currency'])
                                                        ->has($typeplace)
                                                        ->first();

                                                    if(count($surchargeObj) == 0){
                                                        $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                            'surcharge_id'       => $surchargeVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contract_id,
                                                            'calculationtype_id' => $row_calculation['calculationtype'],
                                                            'ammount'            => $row_calculation['ammount'],
                                                            'currency_id'        => $row_calculation['currency']
                                                        ]);
                                                    }

                                                    //----------------------- CARRIERS -------------------------------------------
                                                    $existsCar = null;
                                                    $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                        ->where('localcharge_id',$surchargeObj->id)->first();
                                                    if(count($existsCar) == 0){
                                                        LocalCharCarrier::create([ // tabla localcharcarriers
                                                            'carrier_id'        => $carrierVal,
                                                            'localcharge_id'    => $surchargeObj->id
                                                        ]);
                                                    }

                                                    //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------

                                                    if($differentiatorBol){ // country
                                                        $existCount = null;
                                                        $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                            ->where('country_dest',$destinyVal)
                                                            ->where('localcharge_id',$surchargeObj->id)
                                                            ->first();
                                                        if(count($existCount) == 0){
                                                            $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                'country_orig'      => $originVal,
                                                                'country_dest'      => $destinyVal,
                                                                'localcharge_id'    => $surchargeObj->id
                                                            ]);
                                                        }
                                                    } else { // port
                                                        $existPort = null;
                                                        $existPort = LocalCharPort::where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('localcharge_id',$surchargeObj->id)
                                                            ->first();
                                                        if(count($existPort) == 0){
                                                            $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                'port_orig'      => $originVal,
                                                                'port_dest'      => $destinyVal,
                                                                'localcharge_id' => $surchargeObj->id
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }                                          
                                        }

                                    } else {
                                        $currency_val   = null;
                                        $ammount        = null;
                                        $key            = null;
                                        foreach($columna_cont as $key => $conta_row){
                                            if($conta_row[3] != true){
                                                $ammount        = $conta_row[0];
                                            }
                                            $currency_val   = $conta_row[1];
                                            if($ammount != 0.00 || $ammount != null){
                                                break;
                                            }
                                        }

                                        //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                        $surchargeObj = null;
                                        $surchargeObj = LocalCharge::where('surcharge_id',$surchargeVal)
                                            ->where('typedestiny_id',$typedestinyVal)
                                            ->where('contract_id',$contract_id)
                                            ->where('calculationtype_id',$calculationtypeVal)
                                            ->where('ammount',$ammount)
                                            ->where('currency_id',$currency_val)
                                            ->has($typeplace)
                                            ->first();

                                        if(count($surchargeObj) == 0){
                                            $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => $typedestinyVal,
                                                'contract_id'        => $contract_id,
                                                'calculationtype_id' => $calculationtypeVal,
                                                'ammount'            => $ammount,
                                                'currency_id'        => $currency_val
                                            ]);
                                        }

                                        //----------------------- CARRIERS -------------------------------------------
                                        $existsCar = null;
                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                            ->where('localcharge_id',$surchargeObj->id)->first();
                                        if(count($existsCar) == 0){
                                            LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'        => $carrierVal,
                                                'localcharge_id'    => $surchargeObj->id
                                            ]);
                                        }

                                        //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------
                                        if($differentiatorBol){ // country
                                            $existCount = null;
                                            $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                ->where('country_dest',$destinyVal)
                                                ->where('localcharge_id',$surchargeObj->id)
                                                ->first();
                                            if(count($existCount) == 0){
                                                $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                    'country_orig'      => $originVal,
                                                    'country_dest'      => $destinyVal,
                                                    'localcharge_id'    => $surchargeObj->id
                                                ]);
                                            }
                                        } else { // port
                                            $existPort = null;
                                            $existPort = LocalCharPort::where('port_orig',$originVal)
                                                ->where('port_dest',$destinyVal)
                                                ->where('localcharge_id',$surchargeObj->id)
                                                ->first();
                                            if(count($existPort) == 0){
                                                $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $surchargeObj->id
                                                ]);
                                            }
                                        }

                                    }
                                }

                                ///////////////////////////////// END GOOD

                            } else {
                                //dd($datos_finales);
                                if($values != false){
                                    // ORIGIN -------------------------------------------------------------
                                    if($origExiBol){
                                        if($differentiatorBol == false){
                                            $originVal = Harbor::find($originVal);
                                            $originVal = $originVal->name;
                                        }else if($differentiatorBol == true){
                                            $originVal = Country::find($originVal);
                                            $originVal = $originVal['name']; 
                                        }
                                    } 
                                    // DESTINATION --------------------------------------------------------
                                    if($destiExitBol){
                                        if($differentiatorBol == false){
                                            $destinyVal = Harbor::find($destinyVal);
                                            $destinyVal = $destinyVal->name;
                                        }else if($differentiatorBol == true){
                                            $destinyVal = Country::find($destinyVal);
                                            $destinyVal = $destinyVal->name;
                                        }
                                    }
                                    //---------------------------- CALCULATION TYPE -----------------------
                                    if($calculationtypeExiBol){
                                        $calculationtypeVal = CalculationType::find($calculationtypeVal);
                                        $calculationtypeVal = $calculationtypeVal->name;
                                    }
                                    //---------------------------- TYPE - SURCHARGE -----------------------
                                    if(strnatcasecmp($chargeExc_val,$chargeVal) != 0){
                                        if($typeChargeExiBol){
                                            $surchargeVal = Surcharge::find($surchargeVal);
                                            $surchargeVal = $surchargeVal->name;
                                        }
                                    }
                                    //---------------------------- CARRIER --------------------------------
                                    if($carriExitBol){
                                        $carrierVal = Carrier::find($carrierVal);
                                        $carrierVal = $carrierVal->name;
                                    }
                                    //---------------------------- TYPE DESTINY ---------------------------
                                    if($typedestinyExitBol == true && strnatcasecmp($chargeExc_val,$chargeVal) != 0){
                                        try{
                                            $typedestinyVal = TypeDestiny::find($typedestinyVal);
                                            $typedestinyVal = $typedestinyVal->description;
                                        } catch(\Exception $e){
                                            dd($datos_finales);
                                        }
                                    }

                                    if(strnatcasecmp($chargeExc_val,$chargeVal) == 0){
                                        $twuenty_val    = 0;
                                        $forty_val      = 0;
                                        $fortyhc_val    = 0;
                                        $fortynor_val   = 0;
                                        $fortyfive_val  = 0;
                                        $currency_val   = null;
                                        $container_json = [];
                                        if($differentiatorBol == false){
                                            if($groupContainer_id != 1){ //DISTINTO A DRY
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[4] == false){                      
                                                        $rspVal = null;
                                                        $rspVal = HelperAll::currencyJoin($statusCurrency,
                                                                                          $currency_bol[$key],
                                                                                          $conta_row[0],
                                                                                          $conta_row[1]); 
                                                        $container_json['C'.$key] = ''.$rspVal;
                                                    }
                                                    if($conta_row[3] != true){
                                                        if($currency_bol[$key] == false){
                                                            $currency_val   = $conta_row[1];
                                                        } else{
                                                            $currencyObj  = Currency::find($conta_row[1]);
                                                            $currency_val = $currencyObj->alphacode;
                                                        }
                                                    }
                                                }
                                                $container_json = json_encode($container_json);


                                            } else { // DRY
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[4] == false){ // columna contenedores
                                                        $rspVal = null;
                                                        $rspVal = HelperAll::currencyJoin($statusCurrency,
                                                                                          $currency_bol[$key],
                                                                                          $conta_row[0],
                                                                                          $conta_row[1]); 
                                                        $container_json['C'.$key] = ''.$rspVal;
                                                    } else{ // por columna específica
                                                        if(strnatcasecmp($columns_rt_ident[$key],'twuenty') == 0){
                                                            $twuenty_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                   $currency_bol[$key],
                                                                                                   $conta_row[0],
                                                                                                   $conta_row[1]);    
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'forty') == 0){
                                                            $forty_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                 $currency_bol[$key],
                                                                                                 $conta_row[0],
                                                                                                 $conta_row[1]);
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'fortyhc') == 0){
                                                            $fortyhc_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                   $currency_bol[$key],
                                                                                                   $conta_row[0],
                                                                                                   $conta_row[1]);  
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'fortynor') == 0){
                                                            $fortynor_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                    $currency_bol[$key],
                                                                                                    $conta_row[0],
                                                                                                    $conta_row[1]);  
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'fortyfive') == 0){
                                                            $fortyfive_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                     $currency_bol[$key],
                                                                                                     $conta_row[0],
                                                                                                     $conta_row[1]);
                                                        }
                                                    }  
                                                    if($conta_row[3] != true){
                                                        $currency_val = $conta_row[1];                                                        
                                                    }
                                                }
                                                $container_json = json_encode($container_json);

                                            }


                                            $exists = null;
                                            $exists = FailRate::where('origin_port',$originVal)
                                                ->where('destiny_port',$destinyVal)
                                                ->where('carrier_id',$carrierVal)
                                                ->where('contract_id',$contract_id)
                                                ->where('twuenty',$twuenty_val)
                                                ->where('forty',$forty_val)
                                                ->where('fortyhc',$fortyhc_val)
                                                ->where('fortynor',$fortynor_val)
                                                ->where('fortyfive',$fortyfive_val)
                                                ->where('containers',$container_json)
                                                ->where('currency_id',$currency_val)
                                                ->get();

                                            if(count($exists) == 0){
                                                $respFR = FailRate::create([
                                                    'origin_port'       => $originVal,
                                                    'destiny_port'      => $destinyVal,
                                                    'carrier_id'        => $carrierVal,
                                                    'contract_id'       => $contract_id,
                                                    'twuenty'           => $twuenty_val,
                                                    'forty'             => $forty_val,
                                                    'fortyhc'           => $fortyhc_val,
                                                    'fortynor'          => $fortynor_val,
                                                    'fortyfive'         => $fortyfive_val,
                                                    'containers'        => $container_json,
                                                    'currency_id'       => $currency_val
                                                ]);
                                            }
                                        }
                                    } else {
                                        if($differentiatorBol){
                                            $differentiatorVal = 2;
                                        } else {
                                            $differentiatorVal = 1;
                                        }
                                        if($calculationtypeExiBol){
                                            if(strnatcasecmp($calculation_type_exc,'PER_CONTAINER') == 0){
                                                $equals_values = [];
                                                $key = null;
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[3] == true && $conta_row[2] != true){
                                                        array_push($equals_values,$conta_row[0]);
                                                    } else if($conta_row[3] == false){
                                                        array_push($equals_values,$conta_row[0]); 
                                                    }
                                                }

                                                if(count(array_unique($equals_values)) == 1){ // Valores iguales.
                                                    $currency_val   = null;
                                                    $ammount        = null;
                                                    $key            = null;
                                                    $currency_bol_f = true;
                                                    foreach($columna_cont as $key => $conta_row){
                                                        if($conta_row[3] != true ){
                                                            $ammount        = $conta_row[0];
                                                        }
                                                        if($variant_currency){                          
                                                            $currency_val   = $conta_row[1];
                                                            break;
                                                        } else {
                                                            if($currency_bol[$key] == false){
                                                                $currency_bol_f = false;
                                                                $currency_val   = $conta_row[1];
                                                            }
                                                        }
                                                    }

                                                    $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                       $currency_bol_f,
                                                                                       $ammount,
                                                                                       $currency_val);
                                                    if($currency_bol_f){
                                                        $currencyObj  = Currency::find($currency_val);
                                                        $currency_val = $currencyObj->alphacode;
                                                    }

                                                    //dd($ammount,$currency_val);
                                                    $exists = null;
                                                    $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('port_orig',$originVal)
                                                        ->where('port_dest',$destinyVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contract_id)
                                                        ->where('calculationtype_id',$calculationtypeVal)
                                                        ->where('ammount',$ammount)
                                                        ->where('currency_id',$currency_val)
                                                        ->where('carrier_id',$carrierVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();
                                                    if(count($exists) == 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contract_id,
                                                            'calculationtype_id' => $calculationtypeVal,  //////
                                                            'ammount'            => $ammount, //////
                                                            'currency_id'        => $currency_val, //////
                                                            'carrier_id'         => $carrierVal,
                                                            'differentiator'     => $differentiatorVal
                                                        ]);
                                                    }
                                                } else if(count(array_unique($equals_values)) > 1){ //Valores distintos
                                                    $key                 = null;
                                                    $rows_calculations   = [];
                                                    foreach($columna_cont as $key => $conta_row){// Cargamos cada columna para despues insertarlas en la BD
                                                        $calculationtypeVal = CalculationType::find($conatiner_calculation_id[$key]);
                                                        $calculationtypeVal = $calculationtypeVal->name;
                                                        if($currency_bol[$key]){
                                                            $currency_val = Currency::find($conta_row[1]);
                                                            $currency_val = $currency_val->alphacode;
                                                        } else {
                                                            $currency_val = $conta_row[1];
                                                        }
                                                        $ammount = null;
                                                        $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                           $currency_bol[$key],
                                                                                           $conta_row[0],
                                                                                           $conta_row[1]);
                                                        $ammoun_zero = false;
                                                        if($conta_row[0] == 0.0 || $conta_row[0] == 0){
                                                            $ammoun_zero = true;
                                                        }
                                                        $rows_calculations[$key] = [
                                                            'calculationtype' => $calculationtypeVal,
                                                            'ammount'         => $ammount,
                                                            'ammount_zero'    => $ammoun_zero,
                                                            'currency'        => $currency_val
                                                        ];
                                                    }
                                                    //dd($rows_calculations);
                                                    foreach($rows_calculations as $key => $row_calculation){
                                                        if($row_calculation['ammount_zero'] != true){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contract_id)
                                                                ->where('calculationtype_id',$row_calculation['calculationtype'])
                                                                ->where('ammount',$row_calculation['ammount'])
                                                                ->where('currency_id',$row_calculation['currency'])
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contract_id,
                                                                    'calculationtype_id' => $row_calculation['calculationtype'],  //////
                                                                    'ammount'            => $row_calculation['ammount'], //////
                                                                    'currency_id'        => $row_calculation['currency'], //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                            }

                                                        }
                                                    }
                                                }

                                            } else {

                                                $ammount        = null;
                                                $key            = null;
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[3] != true && $conta_row[0] != 0.00 && $conta_row[0] != null){
                                                        $ammount        = $conta_row[0];
                                                        break;
                                                    }
                                                }
                                                $key            = null;
                                                $currency_val   = null;
                                                $currency_bol_f = true;
                                                foreach($columna_cont as $key => $conta_rowT){
                                                    if($variant_currency){                          
                                                        $currency_val   = $conta_rowT[1];
                                                        break;
                                                    } else {
                                                        if($currency_bol[$key] == false){
                                                            $currency_bol_f = false;
                                                            $currency_val   = $conta_rowT[1];
                                                        }
                                                    }
                                                }


                                                $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                   $currency_bol_f,
                                                                                   $ammount,
                                                                                   $currency_val);
                                                if($currency_bol_f){
                                                    $currencyObj  = Currency::find($currency_val);
                                                    $currency_val = $currencyObj->alphacode;
                                                }
                                                //dd('registro pr ship',$variant_currency,$columna_cont,$currency_val,$ammount);
                                                $exists = null;
                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                    ->where('port_orig',$originVal)
                                                    ->where('port_dest',$destinyVal)
                                                    ->where('typedestiny_id',$typedestinyVal)
                                                    ->where('contract_id',$contract_id)
                                                    ->where('calculationtype_id',$calculationtypeVal)
                                                    ->where('ammount',$ammount)
                                                    ->where('currency_id',$currency_val)
                                                    ->where('carrier_id',$carrierVal)
                                                    ->where('differentiator',$differentiatorVal)
                                                    ->get();
                                                if(count($exists) == 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contract_id,
                                                        'calculationtype_id' => $calculationtypeVal,  //////
                                                        'ammount'            => $ammount, //////
                                                        'currency_id'        => $currency_val, //////
                                                        'carrier_id'         => $carrierVal,
                                                        'differentiator'     => $differentiatorVal
                                                    ]);
                                                }
                                            }
                                        } else {// Calculation Type desconocido

                                            $key                 = null;
                                            $rows_calculations   = [];
                                            foreach($columna_cont as $key => $conta_row){// Cargamos cada columna para despues insertarlas en la BD
                                                $calculationtypeValFail = null;
                                                $calculationtypeValFail = $key.' '.$calculationtypeVal;
                                                if($currency_bol[$key]){
                                                    $currency_val = Currency::find($conta_row[1]);
                                                    $currency_val = $currency_val->alphacode;
                                                } else {
                                                    $currency_val = $conta_row[1];
                                                }
                                                $ammount = null;
                                                $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                   $currency_bol[$key],
                                                                                   $conta_row[0],
                                                                                   $conta_row[1]);
                                                $ammoun_zero = false;
                                                if($conta_row[0] == 0.0 || $conta_row[0] == 0){
                                                    $ammoun_zero = true;
                                                }
                                                $rows_calculations[$key] = [
                                                    'calculationtype' => $calculationtypeValFail,
                                                    'ammount'         => $ammount,
                                                    'ammount_zero'    => $ammoun_zero,
                                                    'currency'        => $currency_val
                                                ];
                                            }
                                            //dd('llega aqui Cals',$rows_calculations);
                                            foreach($rows_calculations as $key => $row_calculation){
                                                if($row_calculation['ammount_zero'] != true){
                                                    $exists = null;
                                                    $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('port_orig',$originVal)
                                                        ->where('port_dest',$destinyVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contract_id)
                                                        ->where('calculationtype_id',$row_calculation['calculationtype'])
                                                        ->where('ammount',$row_calculation['ammount'])
                                                        ->where('currency_id',$row_calculation['currency'])
                                                        ->where('carrier_id',$carrierVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();
                                                    if(count($exists) == 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contract_id,
                                                            'calculationtype_id' => $row_calculation['calculationtype'],  //////
                                                            'ammount'            => $row_calculation['ammount'], //////
                                                            'currency_id'        => $row_calculation['currency'], //////
                                                            'carrier_id'         => $carrierVal,
                                                            'differentiator'     => $differentiatorVal
                                                        ]);
                                                    }

                                                }
                                            }  
                                            //dd('registro');
                                        }
                                    }
                                }

                            }
                            // ELSE O FIN DEL IF PARA FALLIDOS O BUENOS

                            /////////////////////////////////

                        }
                    }
                }
                $countRow++;
            }
        } else {
            //imprimir en el log error
            Log::error('Container calculation type relationship error');
        }

    }

    ////BORRAR UNA VEZ HECHAS LAS PRUEBAS
    //    // * proccesa solo cuando son rates --------------------------------------------------
    //    public function ProcessContractFcl(Request $request){
    //        //dd($request->all());
    //        $requestobj = $request->all();
    //        /*Rate::where('contract_id',$request->Contract_id)->forceDelete();
    //        FailRate::where('contract_id',$request->Contract_id)->forceDelete();*/
    //        $errors = 0;
    //        if(env('APP_VIEW') == 'operaciones') {
    //            ImportationRatesFclJob::dispatch($requestobj)->onQueue('operaciones');
    //        } else {
    //            ImportationRatesFclJob::dispatch($requestobj);
    //        }
    //        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$requestobj['Contract_id'],1]);
    //    }

    public function FailedRatesDeveloper($id,$tab){
        //$id se refiere al id del contracto
        $countrates     = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
        $countfailrates = FailRate::where('contract_id','=',$id)->count();
        $contract       = Contract::find($id);
        return view('importation.TestFailRates2',compact('countfailrates','countrates','contract','id','tab'));
    }

    ////BORRAR UNA VEZ HECHAS LAS PRUEBAS
    //    // * proccesa solo cuando es Surchargers, Se envia a cola de trabajos 2do. plano
    //    public function ProcessContractFclRatSurch(Request $request){
    //        $companyUserId = $request->CompanyUserId;
    //        $UserId =\Auth::user()->id;
    //
    //        $requestobj = $request;
    //        $companyUserIdVal = $companyUserId;
    //        $errors = 0;
    //        $NameFile = $requestobj['FileName'];
    //        $path = \Storage::disk('FclImport')->url($NameFile);
    //        /*
    //        FailSurCharge::where('contract_id',$request->Contract_id)->forceDelete();
    //        LocalCharge::where('contract_id',$request->Contract_id)->forceDelete();
    //        Rate::where('contract_id',$request->Contract_id)->forceDelete();
    //        FailRate::where('contract_id',$request->Contract_id)->forceDelete();*/
    //
    //        if(env('APP_VIEW') == 'operaciones') {
    //            ImportationRatesSurchargerJob::dispatch($request->all(),$companyUserId,$UserId)->onQueue('operaciones'); //NO BORRAR!!
    //        }else {
    //            ImportationRatesSurchargerJob::dispatch($request->all(),$companyUserId,$UserId); //NO BORRAR!!
    //        }
    //        $id = $request['Contract_id'];
    //        return redirect()->route('redirect.Processed.Information',$id);
    //    }

    public function redirectProcessedInformation($id){
        $contract       = Contract::find($id);
        return view('importation.ProcessedInformation',compact('id','contract'));
    }

    // Rates ----------------------------------------------------------------------------
    ////BORRAR UNA VEZ HECHAS LAS PRUEBAS
    //    public function UploadFileRateForContract(Request $request){
    //        $requestobj = $request;
    //        $nombre='';
    //        try {
    //
    //            $now = new \DateTime();
    //            $now = $now->format('dmY_His');
    //            $file = $requestobj->file('file');
    //            $ext = strtolower($file->getClientOriginalExtension());
    //            $validator = \Validator::make(
    //                array('ext' => $ext),
    //                array('ext' => 'in:xls,xlsx,csv')
    //            );
    //            if ($validator->fails()) {
    //                $requestobj->session()->flash('message.nivel', 'danger');
    //                $requestobj->session()->flash('message.content', 'just archive with extension xlsx xls csv');
    //                return redirect()->route('contracts.edit',$requestobj->contract_id);
    //            }
    //            //obtenemos el nombre del archivo
    //            $nombre = $file->getClientOriginalName();
    //            $nombre = $now.'_'.$nombre;
    //            $dd = \Storage::disk('FclImport')->put($nombre,\File::get($file));
    //            //dd(\Storage::disk('UpLoadFile')->url($nombre));
    //            $contract = $requestobj->contract_id;
    //            $errors=0;
    //            Excel::Load(\Storage::disk('FclImport')->url($nombre),function($reader) use($contract,$errors,$requestobj) {
    //
    //                $originResul  = '';
    //                $destinResul  = '';
    //                $currencResul = '';
    //
    //                if($reader->get()->isEmpty() != true){
    //                    Rate::where('contract_id','=',$contract)
    //                        ->delete();
    //                    FailRate::where('contract_id','=',$contract)
    //                        ->delete();
    //                } else{
    //                    $requestobj->session()->flash('message.nivel', 'danger');
    //                    $requestobj->session()->flash('message.content', 'The file is it empty');
    //                    return redirect()->route('contracts.edit',$contract);   
    //                }
    //                foreach ($reader->get() as $book) {
    //                    $originVdul = '';
    //                    $destinationVdul = '';
    //
    //                    $carrier = Carrier::where('name','=',$book->carrier)->first();
    //                    $twuenty = "20";
    //                    $forty = "40";
    //                    $fortyhc = "40hc";
    //                    $origin = "origin";
    //                    $destination = "destiny";
    //                    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //                    $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
    //
    //                    $resultadoPortOri = PrvHarbor::get_harbor($book->$origin);
    //                    if($resultadoPortOri['boolean']){
    //                        $origB = true;    
    //                    }
    //                    $originVdul  = $resultadoPortOri['puerto'];
    //
    //
    //                    $resultadoPortDes = PrvHarbor::get_harbor($book->$destination);
    //                    if($resultadoPortDes['boolean']){
    //                        $destiB = true;    
    //                    }
    //                    $destinationVdul  = $resultadoPortDes['puerto'];
    //
    //                    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                    $duplicate =  Rate::where('origin_port','=',$originVdul)
    //                        ->where('destiny_port','=',$destinationVdul)
    //                        ->where('carrier_id','=',$carrier['id'])
    //                        ->where('contract_id','=',$contract)
    //                        ->count();
    //                    if($duplicate <= 0){
    //                        $originResul  = '';
    //                        $destinResul  = '';
    //                        $currencResul = '';
    //                        $carrierResul = '';
    //                        $origB=false;
    //                        $destiB=false;
    //                        $carriB=false;
    //                        $twuentyB=false;
    //                        $fortyB=false;
    //                        $fortyhcB=false;
    //                        $curreB=false;
    //                        $originV;
    //                        $destinationV;
    //                        $carrierV;
    //                        $twuentyV;
    //                        $fortyV;
    //                        $fortyhcV;
    //                        $currencyV;
    //                        $values = true;
    //
    //                        $currencResul = str_replace($caracteres,'',$book->currency);
    //                        $currenc = Currency::where('alphacode','=',$currencResul)->first();
    //
    //                        $carrierResul = str_replace($caracteres,'',$book->carrier);
    //                        $carrier = Carrier::where('name','=',$carrierResul)->first();
    //
    //                        $originResul = str_replace($caracteres,'',strtolower($book->$origin));
    //                        $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
    //                            ->get();
    //                        if(count($originExits) == 1){
    //                            $origB=true;
    //                            foreach($originExits as $originRc){
    //                                $originV = $originRc['id'];
    //                            }
    //                        }else{
    //                            $originV = $book->$origin.'_E_E';
    //                        }
    //
    //                        $destinResul = str_replace($caracteres,'',strtolower($book->$destination));
    //                        $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
    //                            ->get();
    //                        if(count($destinationExits) == 1){
    //                            $destiB=true;
    //                            foreach($destinationExits as $destinationRc){
    //                                $destinationV = $destinationRc['id'];
    //                                // dd($destinationV);
    //                            }
    //                        }else{
    //                            $destinationV = $book->$destination.'_E_E';
    //                        }
    //                        if(empty($carrier->id) != true){
    //                            $carriB=true;
    //                            $carrierV = $carrier->id;
    //                        }else{
    //                            $carrierV = $book->carrier.'_E_E';
    //                        }
    //                        //////
    //                        if(empty($book->$twuenty) != true || (int)$book->$twuenty == 0){
    //                            $twuentyB=true;
    //                            $twuentyV = (int)$book->$twuenty;
    //                        }
    //                        else{
    //                            $twuentyV = $book->$twuenty.'_E_E';
    //                        }
    //                        /////
    //                        if(empty($book->$forty) != true || (int)$book->$forty == 0){
    //                            $fortyB=true;
    //                            $fortyV = (int)$book->$forty;
    //                        }
    //                        else{
    //                            $fortyV = $book->$forty.'_E_E';
    //                        }
    //                        /////
    //                        if(empty($book->$fortyhc) != true || (int)$book->$fortyhc == 0){
    //                            $fortyhcB=true;
    //                            $fortyhcV = (int)$book->$fortyhc;
    //                        }
    //                        else{
    //                            $fortyhcV = $book->$fortyhc.'_E_E';
    //                        }
    //
    //                        if((int)$book->$twuenty == 0
    //                           && (int)$book->$forty == 0
    //                           && (int)$book->$fortyhc == 0){
    //                            $values = false;
    //                        }
    //
    //                        if(empty($currenc->id) != true){
    //                            $curreB=true;
    //                            $currencyV =  $currenc->id;
    //                        }
    //                        else{
    //                            $currencyV = $book->currency.'_E_E';
    //                        }
    //                        if( $origB == true && $destiB == true
    //                           && $carriB == true && $twuentyB == true
    //                           && $fortyB == true && $fortyhcB == true
    //                           && $curreB == true
    //                           && $values == true) {
    //                            Rate::create([
    //                                'origin_port'   => $originV,
    //                                'destiny_port'  => $destinationV,
    //                                'carrier_id'    => $carrierV,
    //                                'contract_id'   => $contract,
    //                                'twuenty'       => $twuentyV,
    //                                'forty'         => $fortyV,
    //                                'fortyhc'       => $fortyhcV,
    //                                'currency_id'   => $currencyV,
    //                            ]);
    //                        }
    //                        else{
    //                            if($origB == true){
    //                                $originV = $book->$origin;
    //                            }
    //                            if($destiB == true){
    //                                $destinationV = $book->$destination;
    //                            }
    //                            if($curreB == true){
    //                                $currencyV = $book->currency;
    //                            }
    //                            if($carriB == true){
    //                                $carrierV = $book->carrier;
    //                            }
    //                            if( empty($book->$origin) == true
    //                               && empty($book->$destination) == true
    //                               && empty($book->carrier) == true
    //                               && empty($book->currency) == true
    //                               && empty($book->$twuenty) == true
    //                               && empty($book->$forty) == true
    //                               && empty($book->$fortyhc) == true ) {
    //                            }else{
    //                                $duplicateFail =  FailRate::where('origin_port','=',$originV)
    //                                    ->where('destiny_port','=',$destinationV)
    //                                    ->where('carrier_id','=',$carrierV)
    //                                    ->where('contract_id','=',$contract)
    //                                    ->count();
    //                                if($duplicateFail <= 0){
    //                                    if((int)$book->$twuenty == 0
    //                                       && (int)$book->$forty == 0
    //                                       && (int)$book->$fortyhc == 0){
    //
    //                                    }else {
    //                                        FailRate::create([
    //                                            'origin_port'   => $originV,
    //                                            'destiny_port'  => $destinationV,
    //                                            'carrier_id'    => $carrierV,
    //                                            'contract_id'   => $contract,
    //                                            'twuenty'       => $twuentyV,
    //                                            'forty'         => $fortyV,
    //                                            'fortyhc'       => $fortyhcV,
    //                                            'currency_id'   => $currencyV,
    //                                        ]);
    //                                        $errors++;
    //                                    }
    //                                }
    //                            }
    //                        }
    //                    }
    //                } //***
    //                if($errors > 0){
    //                    $requestobj->session()->flash('message.content', 'You successfully added the rate ');
    //                    $requestobj->session()->flash('message.nivel', 'danger');
    //                    $requestobj->session()->flash('message.title', 'Well done!');
    //                    if($errors == 1){
    //                        $requestobj->session()->flash('message.content', $errors.' fee is not charged correctly');
    //                    }else{
    //                        $requestobj->session()->flash('message.content', $errors.' Rates did not load correctly');
    //                    }
    //                }
    //                else{
    //                    $requestobj->session()->flash('message.nivel', 'success');
    //                    $requestobj->session()->flash('message.title', 'Well done!');
    //                }
    //            });
    //            Storage::delete($nombre);
    //            Rate::onlyTrashed()->where('contract_id','=',$contract)
    //                ->forceDelete();
    //            FailRate::onlyTrashed()->where('contract_id','=',$contract)
    //                ->forceDelete();
    //            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$contract,1]);
    //            //dd($res);*/
    //        } catch (\Illuminate\Database\QueryException $e) {
    //            Storage::delete($nombre);
    //            Rate::onlyTrashed()->where('contract_id','=',$contract)
    //                ->restore();
    //            FailRate::onlyTrashed()->where('contract_id','=',$contract)
    //                ->restore();
    //            $requestobj->session()->flash('message.nivel', 'danger');
    //            $requestobj->session()->flash('message.content', 'There was an error loading the file');
    //            return redirect()->route('contracts.edit',$requestobj->contract_id);
    //        }
    //    }

    //Edita solo el origen y destino para rates fallidos, solo se coloca una vez
    public function EdicionRatesMultiples(Request $request){
        $harbor         = Harbor::pluck('display_name','id');
        $arreglo        = $request->idAr;
        $contract_id    = $request->contract_id;
        //dd($harbor,$arreglo);
        return view('importation.Body-Modals.storeFailRatesMultiples',compact('harbor','arreglo','contract_id'));
    }

    //Carga la edicion multiple de rates fallidos, para todos los datos del Rate

    public function loadArrayEditMult(Request $request){
        $array = $request->idAr;
        $array_count = count($array);
        $contract_id = $request->contract_id;
        return view('importation.Body-Modals.FailEditByDetalls',compact('array','array_count','contract_id'));
    }

    public function showRatesMultiplesPorDetalles(Request $request){
        //dd($request->all());
        $fail_rates_total = collect([]);
        $contract_id      = $request->contract_id;

        $harbor 		= Harbor::pluck('display_name','id');
        $carrier 		= Carrier::pluck('name','id');
        $currency 		= Currency::pluck('alphacode','id');
        $schedulesT		= [null=>'Please Select'];
        $scheduleTo		= ScheduleType::all();

        foreach($scheduleTo as $d){
            $schedulesT[$d['id']]=$d->name;
        }
        foreach($request->idAr as $rate_fail_id){

            $failrate = FailRate::find($rate_fail_id);

            $originV			= null;
            $destinationV		= null;
            $carrierV			= null;
            $currencyV			= null;
            $originA			= null;
            $destinationA		= null;
            $carrierA			= null;
            $currencyA			= null;
            $twuentyA			= null;
            $fortyA				= null;
            $fortyhcA			= null;
            $fortynorA			= null;
            $fortyfiveA			= null;
            $failrates			= [];

            $carrAIn			= null;
            $pruebacurre    	= null;
            $classdorigin   	= 'color:green';
            $classddestination  = 'color:green';
            $classcarrier   	= 'color:green';
            $classcurrency  	= 'color:green';
            $classtwuenty   	= 'color:green';
            $classforty     	= 'color:green';
            $classfortyhc   	= 'color:green';
            $classfortynor  	= 'color:green';
            $classfortyfive 	= 'color:green';

            $classscheduleT     = 'color:green';
            $classtransittime   = 'color:green';
            $classvia           = 'color:green';

            $originA 			= explode("_",$failrate['origin_port']);
            $destinationA   	= explode("_",$failrate['destiny_port']);
            $carrierA       	= explode("_",$failrate['carrier_id']);
            $currencyA      	= explode("_",$failrate['currency_id']);
            $twuentyA       	= explode("_",$failrate['twuenty']);
            $fortyA         	= explode("_",$failrate['forty']);
            $fortyhcA       	= explode("_",$failrate['fortyhc']);
            $fortynorA      	= explode("_",$failrate['fortynor']);
            $fortyfiveA     	= explode("_",$failrate['fortyfive']);
            $schedueleTA    	= explode("_",$failrate['schedule_type']);

            if(count($schedueleTA) <= 1){
                $schedueleTA = ScheduleType::where('name',$schedueleTA[0])->first();
                $schedueleTA = $schedueleTA['id'];
            } else{
                $classscheduleT = 'color:red';
            }

            $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                ->first();
            if(count($originA) <= 1){
                $originV = $originOb['id'];
            } else{
                $classdorigin = 'color:red';
            }

            $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                ->first();
            if(count($destinationA) <= 1 ){
                $destinationV = $destinationOb['id'];
            } else{
                $classddestination = 'color:red';
            }

            if(count($twuentyA) <= 1){
                $twuentyA = $twuentyA[0];
            } else{
                $twuentyA = $twuentyA[0].' (error)';
                $classtwuenty='color:red';
            }

            if(count($fortyA) <= 1){
                $fortyA = $fortyA[0];
            } else{
                $fortyA = $fortyA[0].' (error)';
                $classforty='color:red';
            }

            if(count($fortyhcA) <= 1){
                $fortyhcA = $fortyhcA[0];
            } else{
                $fortyhcA = $fortyhcA[0].' (error)';
                $classfortyhc='color:red';
            }

            if(count($fortynorA) <= 1){
                $fortynorA = $fortynorA[0];
            } else{
                $fortynorA = $fortynorA[0].' (error)';
                $classfortynor ='color:red';
            }

            if(count($fortyfiveA) <= 1){
                $fortyfiveA = $fortyfiveA[0];
            } else{
                $fortyfiveA = $fortyfiveA[0].' (error)';
                $classfortyfive = 'color:red';
            }

            if(count($carrierA) <= 1){
                $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
                $carrierV  = $carrierOb['id'];
            }else{
                $classcarrier = 'color:red';
            }

            if(count($currencyA) <= 1){
                $currenc = Currency::where('alphacode','=',$currencyA[0])->orWhere('id','=',$currencyA[0])->first();
                $currencyV = $currenc['id'];
            } else{
                $classcurrency='color:red';
            }

            $failrates = ['rate_id'         =>  $failrate->id,
                          'contract_id'     =>  $failrate->contract_id,
                          'origin_port'     =>  $originV,   
                          'destiny_port'    =>  $destinationV,     
                          'carrierAIn'      =>  $carrierV,
                          'twuenty'         =>  $twuentyA,      
                          'forty'           =>  $fortyA,      
                          'fortyhc'         =>  $fortyhcA,  
                          'fortynor'        =>  $fortynorA,  
                          'fortyfive'       =>  $fortyfiveA,  
                          'currencyAIn'     =>  $currencyV,
                          'transit_time'    =>  $failrate->transit_time,
                          'via'             =>  $failrate->via,
                          'schedueleT'      =>  $schedueleTA,
                          'classtransittime'=>  $classtransittime,
                          'classvia'        =>  $classvia,
                          'classscheduleT'  =>  $classscheduleT,
                          'classorigin'     =>  $classdorigin,
                          'classdestiny'    =>  $classddestination,
                          'classcarrier'    =>  $classcarrier,
                          'classtwuenty'    =>  $classtwuenty,
                          'classforty'      =>  $classforty,
                          'classfortyhc'    =>  $classfortyhc,
                          'classfortynor'   =>  $classfortyhc,
                          'classfortyfive'  =>  $classfortyhc,
                          'classcurrency'   =>  $classcurrency
                         ];

            $fail_rates_total->push($failrates);
        }

        //dd($fail_rates_total);
        return view('importation.EditByDetallFailRates',compact('fail_rates_total','contract_id','schedulesT','harbor','carrier','currency'));

    }

    public function StoreFailRatesMultiplesByDetalls(Request $request){
        //dd($request->all());
        $contract_id        = $request->contract_id;
        $data_rates         = $request->rate_fail_id;
        $data_origins       = $request->origin_id;
        $data_destinations  = $request->destiny_id;
        $data_carrier       = $request->carrier_id;
        $data_twuenty       = $request->twuenty;
        $data_forty         = $request->forty;
        $data_fortyhc       = $request->fortyhc;
        $data_fortyhc       = $request->fortyhc;
        $data_fortynor      = $request->fortynor;
        $data_fortyfive     = $request->fortyfive;
        $data_currency      = $request->currency_id;

        foreach($data_rates as $key => $data_rate){
            //dd($request->all(),$data_rate,$key);
            foreach($data_origins[$key] as $origin){
                foreach($data_destinations[$key] as $destiny){
                    // dd($request->all(),$key,$origin,$destiny);
                    if($origin != $destiny){
                        $exists_rate = Rate::where('origin_port',$origin)
                            ->where('destiny_port',$destiny)
                            ->where('carrier_id',$data_carrier[$key])
                            ->where('contract_id',$contract_id)
                            ->where('twuenty',floatval($data_twuenty[$key]))
                            ->where('forty',floatval($data_forty[$key]))
                            ->where('fortyhc',floatval($data_fortyhc[$key]))
                            ->where('fortynor',floatval($data_fortynor[$key]))
                            ->where('fortyfive',floatval($data_fortyfive[$key]))
                            ->where('currency_id',$data_currency[$key])
                            ->first();
                        if(count($exists_rate) == 0){
                            $return = Rate::create([
                                "origin_port"       => $origin,
                                "destiny_port"      => $destiny,
                                "carrier_id"        => $data_carrier[$key],
                                "contract_id"       => $contract_id,
                                "twuenty"           => floatval($data_twuenty[$key]),
                                "forty"             => floatval($data_forty[$key]),
                                "fortyhc"           => floatval($data_fortyhc[$key]),
                                "fortynor"          => floatval($data_fortynor[$key]),
                                "fortyfive"         => floatval($data_fortyfive[$key]),
                                "currency_id"       => $data_currency[$key],
                                "schedule_type_id"  => null,
                                "transit_time"      => 0,
                                "via"               => null
                            ]);
                        }                        
                    }
                }
            }
            $failrate = FailRate::find($data_rate);
            $failrate->forceDelete();
            //eliminar fail aqui
        }

        $request->session()->flash('message.content', 'Updated Rates' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$contract_id,1]);


    }

    public function StoreFailRatesMultiples(Request $request){
        //dd($request->all());
        $id = $request->contract_id;
        $dataArr = ['id' => $id,'data' => $request->toArray()];
        //dd($dataArr);
        if(env('APP_VIEW') == 'operaciones') {
            GeneralJob::dispatch('edit_mult_rates_fcl',$dataArr)->onQueue('operaciones');
        }else {
            GeneralJob::dispatch('edit_mult_rates_fcl',$dataArr);
        }

        $request->session()->flash('message.content', 'Updating Rate' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$id,1]);
    }

    public function EditRatesGood($id){
        $harbor         = Harbor::pluck('display_name','id');
        $carrier        = Carrier::pluck('name','id');
        $currency       = Currency::pluck('alphacode','id');
        $schedulesT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $schedulesT[$d['id']]=$d->name;
        }
        $rates          = Rate::find($id);
        //dd($rates);
        return view('importation.Body-Modals.GoodEditRates', compact('rates','harbor','carrier','schedulesT','currency'));
    }
    public function EditRatesFail($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $schedulesT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $schedulesT[$d['id']]=$d->name;
        }
        $failrate = FailRate::find($id);
        //dd($failrate);
        $originV;
        $destinationV;
        $carrierV;
        $currencyV;
        $originA;
        $destinationA;
        $carrierA;
        $currencyA;
        $twuentyA;
        $fortyA;
        $fortyhcA;
        $fortynorA;
        $fortyfiveA;

        $carrAIn;
        $pruebacurre    = "";
        $classdorigin   ='color:green';
        $classddestination  ='color:green';
        $classcarrier   ='color:green';
        $classcurrency  ='color:green';
        $classtwuenty   ='color:green';
        $classforty     ='color:green';
        $classfortyhc   ='color:green';
        $classfortynor  ='color:green';
        $classfortyfive ='color:green';

        $classscheduleT     ='color:green';
        $classtransittime   ='color:green';
        $classvia           ='color:green';

        $originA =  explode("_",$failrate['origin_port']);
        //dd($originA);
        $destinationA   = explode("_",$failrate['destiny_port']);
        $carrierA       = explode("_",$failrate['carrier_id']);
        $currencyA      = explode("_",$failrate['currency_id']);
        $twuentyA       = explode("_",$failrate['twuenty']);
        $fortyA         = explode("_",$failrate['forty']);
        $fortyhcA       = explode("_",$failrate['fortyhc']);
        $fortynorA      = explode("_",$failrate['fortynor']);
        $fortyfiveA     = explode("_",$failrate['fortyfive']);
        $schedueleTA    = explode("_",$failrate['schedule_type']);

        if(count($schedueleTA) <= 1){
            $schedueleTA = ScheduleType::where('name',$schedueleTA[0])->first();
            $schedueleTA = $schedueleTA['id'];
        } else{
            $schedueleTA = '';
            $classscheduleT='color:red';
        }

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
        $twuentyC   = count($twuentyA);
        if($twuentyC <= 1){
            $twuentyA = $twuentyA[0];
        } else{
            $twuentyA = $twuentyA[0].' (error)';
            $classtwuenty='color:red';
        }
        $fortyC   = count($fortyA);
        if($fortyC <= 1){
            $fortyA = $fortyA[0];
        } else{
            $fortyA = $fortyA[0].' (error)';
            $classforty='color:red';
        }
        $fortyhcC   = count($fortyhcA);
        if($fortyhcC <= 1){
            $fortyhcA = $fortyhcA[0];
        } else{
            $fortyhcA = $fortyhcA[0].' (error)';
            $classfortyhc='color:red';
        }

        $fortynorC   = count($fortynorA);
        if($fortynorC <= 1){
            $fortynorA = $fortynorA[0];
        } else{
            $fortynorA = $fortynorA[0].' (error)';
            $classfortynor ='color:red';
        }

        $fortyfiveC   = count($fortyfiveA);
        if($fortyfiveC <= 1){
            $fortyfiveA = $fortyfiveA[0];
        } else{
            $fortyfiveA = $fortyfiveA[0].' (error)';
            $classfortyfive = 'color:red';
        }

        $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
        $carrAIn = $carrierOb['id'];
        $carrierC = count($carrierA);
        if($carrierC <= 1){
            //dd($carrierAIn);
            $carrierA = $carrierA[0];
        }
        else{
            $carrierA = $carrierA[0].' (error)';
            $classcarrier='color:red';
        }
        $currencyC = count($currencyA);
        if($currencyC <= 1){
            $currenc = Currency::where('alphacode','=',$currencyA[0])->orWhere('id','=',$currencyA[0])->first();
            $pruebacurre = $currenc['id'];
            $currencyA = $currencyA[0];
        }
        else{
            $currencyA = $currencyA[0].' (error)';
            $classcurrency='color:red';
        }        
        $failrates = ['rate_id'         =>  $failrate->id,
                      'contract_id'     =>  $failrate->contract_id,
                      'origin_port'     =>  $originAIn,   
                      'destiny_port'    =>  $destinationAIn,     
                      'carrierAIn'      =>  $carrAIn,
                      'twuenty'         =>  $twuentyA,      
                      'forty'           =>  $fortyA,      
                      'fortyhc'         =>  $fortyhcA,  
                      'fortynor'        =>  $fortynorA,  
                      'fortyfive'       =>  $fortyfiveA,  
                      'currencyAIn'     =>  $pruebacurre,
                      'transit_time'    =>  $failrate->transit_time,
                      'via'             =>  $failrate->via,
                      'schedueleT'      =>  $schedueleTA,
                      'classtransittime'=>  $classtransittime,
                      'classvia'        =>  $classvia,
                      'classscheduleT'  =>  $classscheduleT,
                      'classorigin'     =>  $classdorigin,
                      'classdestiny'    =>  $classddestination,
                      'classcarrier'    =>  $classcarrier,
                      'classtwuenty'    =>  $classtwuenty,
                      'classforty'      =>  $classforty,
                      'classfortyhc'    =>  $classfortyhc,
                      'classfortynor'   =>  $classfortyhc,
                      'classfortyfive'  =>  $classfortyhc,
                      'classcurrency'   =>  $classcurrency
                     ];
        $pruebacurre = "";
        $carrAIn = "";
        // dd($failrates);
        return view('importation.Body-Modals.FailEditRates',compact('failrates','schedulesT','harbor','carrier','currency'));
    }
    public function CreateRates(Request $request, $id){
        //dd($request->all());
        $origins = $request->origin_port;
        $destinis = $request->destiny_port;
        foreach($origins as $origin){
            foreach($destinis as $destiny){
                if($origin != $destiny){
                    $exists_rate = Rate::where('origin_port',$origin)
                        ->where('destiny_port',$destiny)
                        ->where('carrier_id',$request->carrier_id)
                        ->where('contract_id',$request->contract_id)
                        ->where('twuenty',floatval($request->twuenty))
                        ->where('forty',floatval($request->forty))
                        ->where('fortyhc',floatval($request->fortyhc))
                        ->where('fortynor',floatval($request->fortynor))
                        ->where('fortyfive',floatval($request->fortyfive))
                        ->where('currency_id',$request->currency_id)
                        ->where('schedule_type_id',$request->scheduleT)
                        ->where('transit_time',$request->transit_time)
                        ->where('via',$request->via)
                        ->first();
                    if(count($exists_rate) == 0){
                        $return = Rate::create([
                            "origin_port"       => $origin,
                            "destiny_port"      => $destiny,
                            "carrier_id"        => $request->carrier_id,
                            "contract_id"       => $request->contract_id,
                            "twuenty"           => floatval($request->twuenty),
                            "forty"             => floatval($request->forty),
                            "fortyhc"           => floatval($request->fortyhc),
                            "fortynor"          => floatval($request->fortynor),
                            "fortyfive"         => floatval($request->fortyfive),
                            "currency_id"       => $request->currency_id,
                            "schedule_type_id"  => $request->scheduleT,
                            "transit_time"      => $request->transit_time,
                            "via"               => $request->via
                        ]);
                    }
                }
            }
        }

        $failrate = FailRate::find($id);
        $failrate->forceDelete();
        $request->session()->flash('message.content', 'Updated Rate' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$request->contract_id,1]);
    }
    public function UpdateRatesD(Request $request, $id){
        //dd($request->all());

        $rate = Rate::find($id);
        $rate->origin_port      =  $request->origin_id;
        $rate->destiny_port     =  $request->destiny_id;
        $rate->carrier_id       =  $request->carrier_id;
        $rate->contract_id      =  $request->contract_id;
        $rate->currency_id      =  $request->currency_id;
        $rate->twuenty          =  floatval($request->twuenty);
        $rate->forty            =  floatval($request->forty);
        $rate->fortyhc          =  floatval($request->fortyhc);
        $rate->fortynor         =  floatval($request->fortynor);
        $rate->fortyfive        =  floatval($request->fortyfive);
        $rate->schedule_type_id =  $request->scheduleT;
        $rate->transit_time     =  (int)$request->transit_time;
        $rate->via              =  $request->via;
        $rate->update();

        $request->session()->flash('message.content', 'Updated Rate' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $tab = 0;
        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$request->contract_id,$tab]);
    }
    public function DestroyRatesF($id){
        try{
            $failRate = FailRate::find($id);
            $failRate->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }
    public function DestroyRatesG($id){
        try{
            $Rate = Rate::find($id);
            $Rate->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    // Surcharge ------------------------------------------------------------------------
    // Revisar  para eliminacion de este method
    public function UploadFileSubchargeForContract(Request $request){
        //dd($request->all());
        $contractId       = $request->contract_id;
        $carrierVal       = $request->carrier;
        $statustypecurren = $request->valuesCurrency;
        $carrier          = carrier::all()->pluck('name','id');
        $harbor           = harbor::all()->pluck('display_name','id');
        $destinyArr       = $request->destiny;
        $originArr        = $request->origin;
        $destinyBol       = false;
        $originBol        = false;
        $carrierBol       = false;
        $fortynorBol      = false;
        $fortyfiveBol     = false;

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );

        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('contracts.edit',$request->contract_id);
        }


        $now = new \DateTime();
        $now = $now->format('dmY_His');  
        $nombre = $file->getClientOriginalName();
        $fileName = $now.'_'.$nombre;
        $fileputtmp = \Storage::disk('FclImport')->put($fileName,\File::get($file));

        $targetsArr =[ 
            0 => "20'",
            1 => "40'",
            2 => "40'HC"
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

        if($request->DatCar == false){
            array_push($targetsArr,'Carrier');
        } else {
            $carrierVal;
            $carrierBol = true;
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


        if($statustypecurren == 1){
            array_push($targetsArr,"Currency");
        }

        array_push($targetsArr,"Calculation Type");
        array_push($targetsArr,"Surcharge");

        $coordenates = collect([]);
        //ini_set('max_execution_time', 300);

        $path =Storage::disk('FclImport')->url($fileName);
        Excel::selectSheetsByIndex(0)
            ->Load($path,function($reader) use($request,$coordenates) {
                $reader->noHeading = true;
                $reader->ignoreEmpty();
                $reader->takeRows(2);
                $read = $reader->first();
                $columna= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V');
                for($i=0;$i<count($reader->first());$i++){
                    $coordenates->push($columna[$i].' '.$read[$i]);
                }

            });

        $contract      = Contract::find($contractId);
        $countTarges = count($targetsArr);

        $value = [
            'existorigin'     => $originBol,
            'origin'          => $originArr,
            'existdestiny'    => $destinyBol,
            'destiny'         => $destinyArr,
            'existfortynor'   => $fortynorBol,
            'existfortyfive'  => $fortyfiveBol,
            'fileName'        => $fileName,
            'existcarrier'    => $carrierBol,
            'countTarges'     => $countTarges,
            'carrier'         => $carrierVal,
        ];

        return view('importation.surchargeforcontract',compact('contract',
                                                               'value',
                                                               'harbor',
                                                               'carrier',
                                                               'coordenates',
                                                               'statustypecurren',
                                                               'targetsArr'));
    }   
    // Revisar  para eliminacion de este method
    public function ProcessSurchargeForContract(Request $request){

        // dd($request->all());
        $requestobj = $request;
        $fileName   = $requestobj['fileName'];
        $contract_id = $requestobj['contractId'];

        $path = \Storage::disk('FclImport')->url($fileName);
        Excel::selectSheetsByIndex(0)
            ->Load($path,function($reader) use($requestobj,$contract_id) { 
                $reader->noHeading = true;


                //validamos que el excel este lleno
                if($reader->get()->isEmpty() != true){

                    // dd('if($reader->get()->isEmpty() != true){');
                    LocalCharge::where('contract_id','=',$contract_id)
                        ->delete();
                    FailSurCharge::where('contract_id','=',$contract_id)
                        ->delete();
                } else{
                    $requestobj->session()->flash('message.nivel', 'danger');
                    $requestobj->session()->flash('message.content', 'The file is it empty');
                    return redirect()->route('contracts.edit',$contract);   
                }


                $statusexistfortynor    = $requestobj['existfortynor'];
                $statusexistfortyfive   = $requestobj['existfortyfive'];
                // $chargeVal              = $requestobj['chargeVal'];

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
                $Charge                 = "Surcharge";
                $statustypecurren       = "statustypecurren";
                $contractId             = "Contract_id";


                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                $surcharcollection       = collect([]);
                $surcharFailcollection   = collect([]);

                $i = 1;
                $errors =0;
                foreach($reader->get() as $read){
                    $carrierVal                = '';
                    $originVal                 = '';
                    $destinyVal                = '';
                    $origenFL                  = '';
                    $destinyFL                 = '';
                    $currencyVal               = '';
                    $currencyValtwen           = '';
                    $currencyValfor            = '';
                    $currencyValforHC          = '';
                    $currencyValfornor         = '';
                    $currencyValforfive        = '';
                    $calculationtypeVal        = '';
                    $surchargelist             = '';
                    $surchargeVal              = '';
                    $contractIdVal             = $contract_id;
                    $companyUserIdVal          = \Auth::user()->company_user_id;

                    $calculationtypeValfail    = '';
                    $currencResultwen          = '';
                    $currencResulfor           = '';
                    $currencResulforhc         = '';
                    $currencResulfornor        = '';
                    $currencResulforfive       = '';
                    $currencResul              = '';

                    $twentyArr;
                    $fortyArr;
                    $fortyhcArr;
                    $fortynorArr;
                    $fortyfiveArr;
                    $twentyVal                 = '';
                    $fortyVal                  = '';
                    $fortyhcVal                = '';
                    $fortynorVal               = '';
                    $fortyfiveVal              = '';

                    $originBol               = false;
                    $origExiBol              = false;
                    $destinyBol              = false;
                    $destiExitBol            = false;
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
                    $values                  = true;


                    //--------------- Incio de Insercion -----------------------------------------------------------------
                    if($i != 1){


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

                        // ----------------------- Validacion de comapos acios--------------------------------------
                        if($requestobj[$statustypecurren] == 2){ 
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
                        }
                        else{
                            $twentyVal = $twentyArr[0].'_E_E';
                        }

                        //----------------- 40' -----------------------------------------------------------------

                        if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                            $fortyExiBol = true;
                            $fortyVal   = (int)$fortyArr[0];
                        }
                        else{
                            $fortyVal = $fortyArr[0].'_E_E';
                        }

                        //----------------- 40'HC --------------------------------------------------------------

                        if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                            $fortyhcExiBol = true;
                            $fortyhcVal   = (int)$fortyhcArr[0];
                        }
                        else{
                            $fortyhcVal = $fortyhcArr[0].'_E_E';
                        }

                        //----------------- 40'NOR -------------------------------------------------------------
                        if($statusexistfortynor == 1){

                            if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                                $fortynorExiBol = true;
                                $fortynorVal    = (int)$fortynorArr[0];
                            }
                            else{
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
                            }
                            else{
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
                            }
                            else{
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
                            }
                            else{
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
                                $curreExitBol = true;
                                $currencyVal =  $currenc->id;
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
                        if( $read[$requestobj[$CalculationType]] == 'PER_DOC'){
                            $calculationvalvaration = 'Per Shipment';
                        } else if( $read[$requestobj[$CalculationType]] == 'PER_CONTAINER'){
                            $calculationvalvaration = 'Per Container';
                        } else{
                            $calculationvalvaration = $read[$requestobj[$CalculationType]];
                        }

                        $calculationtype = CalculationType::where('name','=',$calculationvalvaration)->first();
                        if(empty($calculationtype) != true){
                            $calculationtypeExiBol = true;
                            $calculationtypeVal = $calculationtype['id'];
                        }
                        else{
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
                            }
                            else{
                                $companyUserId = $companyUserIdVal;
                                $surchargelist = Surcharge::create([
                                    'name'              => $read[$requestobj[$Charge]],
                                    'description'       => 'created in the import of the file',
                                    'company_user_id'   => $companyUserId
                                ]);
                                $surchargeVal = $surchargelist->id;
                            }
                        } else {
                            $surchargeVal = $read[$requestobj[$Charge]].'_E_E';
                        }
                        //////////////////////////////////////////////////////////////////////////////////////////////////////
                        //Para realizar pruebas solamente
                        /*$prueba = collect([]);

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
                     '$fortyfiveVal'           => $fortyfiveVal
                  ];


                  if($statusexistfortynor == 1){
                     $cargaNor = ['$fortynorArr' => $fortynorArr];
                     array_push($prueba,$cargaNor);
                  }

                  if($statusexistfortyfive == 1){
                     $cargaFive = ['$fortyfiveArr' => $fortyfiveArr];
                     array_push($prueba,$cargaFive);
                  }

                  dd($prueba);*/

                        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////


                        if($carriExitBol        == true
                           && $origExiBol       == true
                           && $destiExitBol     == true
                           && $twentyExiBol     == true
                           && $fortyExiBol      == true
                           && $fortyhcExiBol    == true
                           && $fortynorExiBol   == true
                           && $fortyfiveExiBol  == true
                           && $calculationtypeExiBol == true
                           && $variantecurrency == true
                           && $typeExiBol       == true
                           && $values == true){


                            // se ejecuta la carga de los surcharges
                            if($read[$requestobj[$CalculationType]] == 'PER_CONTAINER'){
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
                                        $SurchargArreG = LocalCharge::create([ // tabla localcharges
                                            'surcharge_id'       => $surchargeVal,
                                            'typedestiny_id'     => 3,
                                            'contract_id'        => $contractIdVal,
                                            'calculationtype_id' => $calculationtypeVal,
                                            'ammount'            => $ammount,
                                            'currency_id'        => $currencyVal
                                        ]);

                                        //---------------------------------- CAMBIAR POR ID -----------------------------------------------------------
                                        $SurchargCarrArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                            'carrier_id'      => $carrierVal,
                                            'localcharge_id' => $SurchargArreG->id
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

                                                $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargArreG->id
                                                ]);
                                                //---------------------------------------------------------------------------------

                                            } 
                                        }else {
                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                            $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports
                                                'port_orig'      => $originVal,
                                                'port_dest'      => $destinyVal,
                                                'localcharge_id' => $SurchargArreG->id
                                            ]);
                                        }
                                        //echo $i;
                                        //dd($SurchargArreG);
                                    }
                                } else {
                                    // dd('llega No iguales');
                                    // se crea un registro por cada carga o valor
                                    // se valida si el currency viene junto con el valor

                                    if($requestobj[$statustypecurren] == 2){
                                        // cargar valor y currency  juntos, se trae la descomposicion
                                        // ----------------------- CARGA 20' -------------------------------------------
                                        if($twentyVal != 0){
                                            $SurchargTWArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 2,
                                                'ammount'            => $twentyVal,
                                                'currency_id'        => $currencyValtwen
                                            ]);

                                            $SurchargCarrTWArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'      => $carrierVal,
                                                'localcharge_id' => $SurchargTWArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortTWArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargTWArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortTWArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargTWArreG->id
                                                ]);
                                            }
                                        }
                                        //---------------------- CARGA 40' ----------------------------------------------------

                                        if($fortyVal != 0){
                                            $SurchargFORArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 1,
                                                'ammount'            => $fortyVal,
                                                'currency_id'        => $currencyValfor
                                            ]);

                                            $SurchargCarrFORArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'      => $carrierVal,
                                                'localcharge_id' => $SurchargFORArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORArreG->id
                                                ]);
                                            }
                                        }

                                        // --------------------- CARGA 40'HC --------------------------------------------------

                                        if($fortyhcVal != 0){
                                            $SurchargFORHCArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 3,
                                                'ammount'            => $fortyhcVal,
                                                'currency_id'        => $currencyValforHC
                                            ]);

                                            $SurchargCarrFORHCArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'      => $carrierVal,
                                                'localcharge_id' => $SurchargFORHCArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORHCArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORHCArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORHCArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORHCArreG->id
                                                ]);
                                            }

                                            //echo $i;
                                            //dd($SurchargArreG);
                                        }

                                        // --------------------- CARGA 40'NOR -------------------------------------------------

                                        if($fortynorVal != 0){
                                            $SurchargFORNORArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 7,
                                                'ammount'            => $fortynorVal,
                                                'currency_id'        => $currencyValfornor
                                            ]);

                                            $SurchargCarrFORNORArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'      => $carrierVal,
                                                'localcharge_id' => $SurchargFORNORArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORNORArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORNORArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORNORArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORNORArreG->id
                                                ]);
                                            }

                                            //echo $i;
                                            //dd($SurchargArreG);
                                        }

                                        // --------------------- CARGA 45' ----------------------------------------------------

                                        if($fortyfiveVal != 0){
                                            $SurchargFORfiveArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 8,
                                                'ammount'            => $fortyfiveVal,
                                                'currency_id'        => $currencyValforfive
                                            ]);

                                            $SurchargCarrFORfiveArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'     => $carrierVal,
                                                'localcharge_id' => $SurchargFORfiveArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORfiveArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORfiveArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORfiveArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORfiveArreG->id
                                                ]);
                                            }

                                            //echo $i;
                                            //dd($SurchargArreG);
                                        }

                                        //---------------------
                                    } else{

                                        // cargar el currency ya descompuesto, ahora es un solo registro (currency ) de los tres campos que existen

                                        // ----------------------- CARGA 20' -------------------------------------------

                                        if($twentyVal != 0){
                                            $SurchargTWArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 2,
                                                'ammount'            => $twentyVal,
                                                'currency_id'        => $currencyVal
                                            ]);

                                            $SurchargCarrTWArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'      => $carrierVal,
                                                'localcharge_id' => $SurchargTWArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortTWArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargTWArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortTWArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargTWArreG->id
                                                ]);
                                            }
                                        }

                                        //---------------------- CARGA 40' -----------------------------------------------

                                        if($fortyVal != 0){
                                            $SurchargFORArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 1,
                                                'ammount'            => $fortyVal,
                                                'currency_id'        => $currencyVal
                                            ]);

                                            $SurchargCarrFORArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'      => $carrierVal,
                                                'localcharge_id' => $SurchargFORArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORArreG->id
                                                ]);
                                            }
                                        }

                                        // --------------------- CARGA 40'HC ---------------------------------------------

                                        if($fortyhcVal != 0){
                                            $SurchargFORHCArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 3,
                                                'ammount'            => $fortyhcVal,
                                                'currency_id'        => $currencyVal
                                            ]);

                                            $SurchargCarrFORHCArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'     => $carrierVal,
                                                'localcharge_id' => $SurchargFORHCArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORHCArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORHCArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORHCArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORHCArreG->id
                                                ]);
                                            }
                                            //echo $i;
                                            //dd($SurchargFORHCArreG);
                                        }

                                        // --------------------- CARGA 40'NOR --------------------------------------------

                                        if($fortynorVal != 0){
                                            $SurchargFORnorArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 7,
                                                'ammount'            => $fortynorVal,
                                                'currency_id'        => $currencyVal
                                            ]);

                                            $SurchargCarrFORnorArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'     => $carrierVal,
                                                'localcharge_id' => $SurchargFORnorArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORnorArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORnorArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORHCArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORnorArreG->id
                                                ]);
                                            }
                                            //echo $i;
                                            //dd($SurchargFORHCArreG);
                                        }

                                        // --------------------- CARGA 45' -----------------------------------------------

                                        if($fortyfiveVal != 0){
                                            $SurchargFORfiveArreG = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => 3,
                                                'contract_id'        => $contractIdVal,
                                                'calculationtype_id' => 8,
                                                'ammount'            => $fortyfiveVal,
                                                'currency_id'        => $currencyVal
                                            ]);

                                            $SurchargCarrFORfiveArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'     => $carrierVal,
                                                'localcharge_id' => $SurchargFORfiveArreG->id
                                            ]);

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        $originVal = $rando;
                                                    } else {
                                                        $destinyVal = $rando;
                                                    }

                                                    $SurchargPortFORfiveArreG = LocalCharPort::create([ // tabla localcharports
                                                        'port_orig'      => $originVal,
                                                        'port_dest'      => $destinyVal,
                                                        'localcharge_id' => $SurchargFORfiveArreG->id
                                                    ]);
                                                } 

                                            } else {
                                                // fila por puerto, sin expecificar origen ni destino manualmente
                                                $SurchargPortFORfiveArreG = LocalCharPort::create([ // tabla localcharports
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $SurchargFORfiveArreG->id
                                                ]);
                                            }
                                            //echo $i;
                                            //dd($SurchargFORHCArreG);
                                        }
                                        //_____-----
                                    }

                                }

                            } else if($read[$requestobj[$CalculationType]] == 'PER_DOC'){
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
                                /*
                                           if($requestobj->$statustypecurren == 2){
                                               $currencyVal = $currencyValforHC;
                                           } */

                                if($ammount != 0){
                                    $SurchargPERArreG = LocalCharge::create([ // tabla localcharges
                                        'surcharge_id'       => $surchargeVal,
                                        'typedestiny_id'     => 3,
                                        'contract_id'        => $contractIdVal,
                                        'calculationtype_id' => $calculationtypeVal,
                                        'ammount'            => $ammount,
                                        'currency_id'        => $currencyVal
                                    ]);

                                    $SurchargCarrFORHCArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                        'carrier_id'     => $carrierVal,
                                        'localcharge_id' => $SurchargPERArreG->id
                                    ]);

                                    if($originBol == true || $destinyBol == true){
                                        foreach($randons as  $rando){
                                            //insert por arreglo de puerto
                                            if($originBol == true ){
                                                $originVal = $rando;
                                            } else {
                                                $destinyVal = $rando;
                                            }

                                            $SurchargPortFORHCArreG = LocalCharPort::create([ // tabla localcharports
                                                'port_orig'      => $originVal,
                                                'port_dest'      => $destinyVal,
                                                'localcharge_id' => $SurchargPERArreG->id
                                            ]);
                                        } 

                                    } else {
                                        // fila por puerto, sin expecificar origen ni destino manualmente
                                        $SurchargPortFORHCArreG = LocalCharPort::create([ // tabla localcharports
                                            'port_orig'      => $originVal,
                                            'port_dest'      => $destinyVal,
                                            'localcharge_id' => $SurchargPERArreG->id
                                        ]);
                                    }
                                }
                                // echo $i;
                                // dd($SurchargPERArreG);
                            }


                        } else {
                            // van los fallidos

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

                            // Surcharges Fallidos
                            if($calculationtypeExiBol == true){
                                //
                                if($read[$requestobj[$CalculationType]] == 'PER_CONTAINER'){
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
                                                        'typedestiny_id'     => 'freight',
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
                                                        'typedestiny_id'     => 'freight',
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
                                                        'typedestiny_id'     => 'freight',
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
                                                        'typedestiny_id'     => 'freight',
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
                                                        'typedestiny_id'     => 'freight',
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
                                                        'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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

                                } else if ($read[$requestobj[$CalculationType]] == 'PER_DOC' 
                                           || $read[$requestobj[$CalculationType]] == 'Per Shipment'){
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
                                                    'typedestiny_id'     => 'freight',
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
                                                'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                    'typedestiny_id'     => 'freight',
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
                                                'typedestiny_id'     => 'freight',
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
                                                'typedestiny_id'     => 'freight',
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
                                                'typedestiny_id'     => 'freight',
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
                                                'typedestiny_id'     => 'freight',
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
                                                'typedestiny_id'     => 'freight',
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
                                                'typedestiny_id'     => 'freight',
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


                            $errors++;
                            //echo $i;
                            //dd($ratescollection);

                        }
                    }
                    $i++;
                    if($errors > 0){
                        $requestobj->session()->flash('message.content', 'You successfully added the surcharge ');
                        $requestobj->session()->flash('message.nivel', 'danger');
                        $requestobj->session()->flash('message.title', 'Well done!');
                        if($errors == 1){
                            $requestobj->session()->flash('message.content', $errors.' fee is not charged correctly');
                        }else{
                            $requestobj->session()->flash('message.content', $errors.' Surcharge did not load correctly');
                        }
                    }
                    else{
                        $requestobj->session()->flash('message.nivel', 'success');
                        $requestobj->session()->flash('message.title', 'Well done!');
                    }
                }
                LocalCharge::onlyTrashed()->where('contract_id','=',$contract_id)
                    ->forceDelete();
                FailSurCharge::onlyTrashed()->where('contract_id','=',$contract_id)
                    ->forceDelete();
            });
        Storage::disk('FclImport')->delete($fileName);
        return redirect()->route('contracts.edit', setearRouteKey($contract_id));
    }

    public function FailedSurchargeDeveloper($id,$tab){
        //$id se refiere al id del contracto
        $countfailsurcharge = FailSurCharge::where('contract_id','=',$id)->count();
        $countgoodsurcharge = LocalCharge::where('contract_id','=',$id)->count();
        $contract       = Contract::find($id);
        return view('importation.SurchargersFailOF',compact('countfailsurcharge','contract','countgoodsurcharge','id','tab'));

    }

    public function EditSurchargersGood($id){
        $objharbor          = new Harbor();
        $objcurrency        = new Currency();
        $objcarrier         = new Carrier();
        $objsurcharge       = new Surcharge();
        $objtypedestiny     = new TypeDestiny();
        $objCalculationType = new CalculationType();
        $countries          = Country::pluck('name','id');

        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $carrierSelect         = $objcarrier->all()->pluck('name','id');
        $harbor                = $objharbor->all()->pluck('display_name','id');
        $currency              = $objcurrency->all()->pluck('alphacode','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

        $goodsurcharges  = LocalCharge::with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest','localcharcountries.countryOrig','localcharcountries.countryDest')->find($id);
        $surchargeSelect       = $objsurcharge->where('company_user_id','=', $goodsurcharges->contract->company_user_id)->pluck('name','id');
        //dd($goodsurcharges);
        return view('importation.Body-Modals.GoodEditSurcharge', compact('harbor',
                                                                         'currency',
                                                                         'countries',
                                                                         'typedestiny',
                                                                         'carrierSelect',
                                                                         'goodsurcharges',
                                                                         'surchargeSelect',
                                                                         'calculationtypeselect'));
    }
    public function EditSurchargersFail($id){
        $objharbor          = new Harbor();
        $objcurrency        = new Currency();
        $objcarrier         = new Carrier();
        $objsurcharge       = new Surcharge();
        $objtypedestiny     = new TypeDestiny();
        $objCalculationType = new CalculationType();

        $countries              = Country::pluck('name','id');
        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $carrierSelect         = $objcarrier->all()->pluck('name','id');
        $harbor                = $objharbor->all()->pluck('display_name','id');
        $currency              = $objcurrency->all()->pluck('alphacode','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

        $failsurcharge  = FailSurCharge::find($id);
        $failsurcharge->load('contract');
        $surchargeSelect       = $objsurcharge->where('company_user_id','=', $failsurcharge->contract->company_user_id)->pluck('name','id');
        //dd($failsurcharge->contract->company_user_id);
        $differentiator = $failsurcharge->differentiator;

        $classdorigin           =  'color:green';
        $classddestination      =  'color:green';
        $classtypedestiny       =  'color:green';
        $classcarrier           =  'color:green';
        $classsurcharger        =  'color:green';
        $classcalculationtype   =  'color:green';
        $classammount           =  'color:green';
        $classcurrency          =  'color:green';
        $surchargeA         =  explode("_",$failsurcharge['surcharge_id']);
        $originA            =  explode("_",$failsurcharge['port_orig']);
        $destinationA       =  explode("_",$failsurcharge['port_dest']);
        $calculationtypeA   =  explode("_",$failsurcharge['calculationtype_id']);
        $ammountA           =  explode("_",$failsurcharge['ammount']);
        $currencyA          =  explode("_",$failsurcharge['currency_id']);
        $carrierA           =  explode("_",$failsurcharge['carrier_id']);
        $typedestinyA       =  explode("_",$failsurcharge['typedestiny_id']);

        // -------------- ORIGIN -------------------------------------------------------------

        if($failsurcharge->differentiator == 1){
            $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                ->first();
        } else if($failsurcharge->differentiator == 2){
            $originOb  = Country::where('variation->type','like','%'.strtolower($originA[0]).'%')
                ->first();
        }

        $originAIn = $originOb['id'];
        $originC   = count($originA);
        if($originC <= 1){
            //$originA = $originOb['name'];
        } else{
            //$originA = $originA[0].' (error)';
            $classdorigin='color:red';
        }

        // -------------- DESTINATION --------------------------------------------------------

        if($failsurcharge->differentiator == 1){
            $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                ->first();
        } else if($failsurcharge->differentiator == 2){
            $destinationOb  = Country::where('variation->type','like','%'.strtolower($destinationA[0]).'%')
                ->first();
        }

        $destinationAIn = $destinationOb['id'];
        $destinationC   = count($destinationA);
        if($destinationC <= 1){
            //$destinationA = $destinationOb['name'];
        } else{
            //$destinationA = $destinationA[0].' (error)';
            $classddestination='color:red';
        }

        // -------------- SURCHARGE ....-----------------------------------------------------
        $surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',$failsurcharge->contract->company_user_id)->first();
        $surcharAin  = $surchargeOb['id'];
        $surchargeC = count($surchargeA);
        if($surchargeC <= 1){
            //$surchargeA = $surchargeA[0];
        }
        else{
            //$surchargeA         = $surchargeA[0].' (error)';
            $classsurcharger    = 'color:red';
        }

        // -------------- CARRIER -----------------------------------------------------------
        $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
        $carrAIn = $carrierOb['id'];
        $carrierC = count($carrierA);
        if($carrierC <= 1){
            //$carrierA = $carrierA[0];
        }
        else{
            //$carrierA       = $carrierA[0].' (error)';
            $classcarrier   ='color:red';
        }

        // -------------- CALCULATION TYPE --------------------------------------------------
        $calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
        $calculationtypeAIn = $calculationtypeOb['id'];
        $calculationtypeC   = count($calculationtypeA);
        if($calculationtypeC <= 1){
            //$calculationtypeA = $calculationtypeA[0];
        }
        else{
            //$calculationtypeA       = $calculationtypeA[0].' (error)';
            $classcalculationtype   = 'color:red';
        }

        // -------------- AMMOUNT -----------------------------------------------------------
        $ammountC = count($ammountA);
        if($ammountC <= 1){
            $ammountA = $failsurcharge['ammount'];
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
            // $currencyA = $currencyA[0];
        }
        else{
            $currencyA      = $currencyA[0].' (error)';
            $classcurrency  = 'color:red';
        }

        // -------------- TYPE DESTINY -----------------------------------------------------
        //dd($failsurcharge['typedestiny_id']);
        $typedestinyobj    = TypeDestiny::where('description',$typedestinyA[0])->first();
        if(count($typedestinyA) <= 1){
            $typedestinyLB = $typedestinyobj['id'];
        }
        else{
            $typedestinyLB      = $typedestinyA[0].' (error)';
            $classtypedestiny   = 'color:red';
        }


        ////////////////////////////////////////////////////////////////////////////////////
        $failsurchargeArre = [
            'id'                    => $failsurcharge['id'],
            'surcharge'             => $surcharAin,
            'origin_port'           => $originAIn,
            'destiny_port'          => $destinationAIn,
            'carrier'               => $carrAIn,
            'contract_id'           => $failsurcharge['contract_id'],
            'typedestiny'           => $typedestinyLB,
            'ammount'               => $ammountA,
            'calculationtype'       => $calculationtypeAIn,
            'currency'              => $currencyAIn,
            'classsurcharge'        => $classsurcharger,
            'classorigin'           => $classdorigin,
            'classdestiny'          => $classddestination,
            'classtypedestiny'      => $classtypedestiny,
            'classcarrier'          => $classcarrier,
            'classcalculationtype'  => $classcalculationtype,
            'classammount'          => $classammount,
            'classcurrency'         => $classcurrency,
        ];
        //dd($arreglo);


        //dd($failsurchargeArre);
        return view('importation.Body-Modals.FailEditSurcharge', compact('failsurchargeArre',
                                                                         'harbor',
                                                                         'carrierSelect',
                                                                         'currency',
                                                                         'countries',
                                                                         'surchargeSelect',
                                                                         'typedestiny',
                                                                         'differentiator',
                                                                         'calculationtypeselect'));
    }
    public function CreateSurchargers(Request $request, $id){
        //dd($request->all());

        $surchargeVar       = $request->surcharge_id;
        $typedestinyVar     = $request->changetype;
        $carrierVarArr      = $request->carrier_id;
        $calculationtypeVar = $request->calculationtype_id;
        $ammountVar         = floatval($request->ammount);
        $currencyVar        = $request->currency_id;
        $contractVar        = $request->contract_id;
        $typerate           =  $request->typeroute;

        $failSurcharge = new FailSurCharge();
        $failSurcharge = FailSurCharge::find($id);
        $SurchargeId = null;
        $SurchargeId = LocalCharge::where('surcharge_id',$surchargeVar)
            ->where('typedestiny_id',$typedestinyVar)
            ->where('contract_id',$contractVar)
            ->where('calculationtype_id',$calculationtypeVar)
            ->where('ammount',$ammountVar)
            ->where('currency_id',$currencyVar)
            ->first();
        if(count($SurchargeId) == 0){
            $SurchargeId = LocalCharge::create([
                'surcharge_id'          => $surchargeVar,
                'typedestiny_id'        => $typedestinyVar,
                'contract_id'           => $contractVar,
                'calculationtype_id'    => $calculationtypeVar,
                'ammount'               => $ammountVar,
                'currency_id'           => $currencyVar
            ]);
        }

        if($typerate == 'port'){
            $originVarArr          =  $request->port_origlocal;
            $destinationVarArr     =  $request->port_destlocal;
            foreach($originVarArr as $originVar){
                foreach($destinationVarArr as $destinationVar){
                    $existsLP = null;
                    $existsLP = LocalCharPort::where('port_orig',$originVar)
                        ->where('port_dest',$destinationVar)
                        ->where('localcharge_id',$SurchargeId->id)
                        ->first();
                    if(count($existsLP) == 0){
                        LocalCharPort::create([
                            'port_orig'         => $originVar,
                            'port_dest'         => $destinationVar,
                            'localcharge_id'    => $SurchargeId->id
                        ]); //
                    }
                }
            }
        }elseif($typerate == 'country'){
            $originVarCounArr      =  $request->country_orig;
            $destinationCounVarArr =  $request->country_dest;

            foreach($originVarCounArr as $originCounVar){
                foreach($destinationCounVarArr as $destinationCounVar){
                    $existsLC = null;
                    $existsLC = LocalCharCountry::where('country_orig',$originCounVar)
                        ->where('country_dest',$destinationCounVar)
                        ->where('localcharge_id',$SurchargeId->id)
                        ->first();
                    if(count($existsLC) == 0){
                        LocalCharCountry::create([
                            'country_orig'      => $originCounVar,
                            'country_dest'      => $destinationCounVar,
                            'localcharge_id'    => $SurchargeId->id
                        ]); //
                    }
                }
            }
        }

        foreach($carrierVarArr as $carrierVar){
            LocalCharCarrier::create([
                'carrier_id'        => $carrierVar,
                'localcharge_id'    => $SurchargeId->id  
            ]);
        }
        $failSurcharge->forceDelete();
        $request->session()->flash('message.content', 'Surcharge Updated' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Surcharge.F.C.D',[$request->contract_id,1]);

    }

    public function UpdateSurchargersD(Request $request, $id){
        //dd($request->all());

        $surchargeVar          =  $request->surcharge_id; // id de la columna surchage_id
        $contractVar           =  $request->contract_id;
        $typedestinyVar        =  $request->changetype;
        $calculationtypeVar    =  $request->calculationtype_id;
        $ammountVar            =  floatval($request->ammount);
        $currencyVar           =  $request->currency_id;
        $carrierVarArr         =  $request->carrier_id;
        $typerate              =  $request->typeroute;

        $SurchargeId = new LocalCharge();
        $SurchargeId  = LocalCharge::find($id);
        $SurchargeId->surcharge_id          = $surchargeVar;
        $SurchargeId->typedestiny_id        = $typedestinyVar;
        $SurchargeId->contract_id           = $contractVar;
        $SurchargeId->calculationtype_id    = $calculationtypeVar;
        $SurchargeId->ammount               = $ammountVar;
        $SurchargeId->currency_id           = $currencyVar;
        $SurchargeId->update();

        LocalCharPort::where('localcharge_id','=',$SurchargeId->id)->forceDelete();
        LocalCharCountry::where('localcharge_id','=',$SurchargeId->id)->forceDelete();

        LocalCharCarrier::where('localcharge_id','=',$SurchargeId->id)->forceDelete();
        foreach($carrierVarArr as $carrierVar){
            LocalCharCarrier::create([
                'carrier_id'        => $carrierVar,
                'localcharge_id'    => $SurchargeId->id  
            ]); //
        }

        if($typerate == 'port'){
            $originVarArr          =  $request->port_origlocal;
            $destinationVarArr     =  $request->port_destlocal;
            foreach($originVarArr as $originVar){
                foreach($destinationVarArr as $destinationVar){
                    LocalCharPort::create([
                        'port_orig'         => $originVar,
                        'port_dest'         => $destinationVar,
                        'localcharge_id'    => $SurchargeId->id
                    ]); //
                }
            }
        }elseif($typerate == 'country'){
            $originVarCounArr      =  $request->country_orig;
            $destinationCounVarArr =  $request->country_dest;

            foreach($originVarCounArr as $originCounVar){
                foreach($destinationCounVarArr as $destinationCounVar){
                    LocalCharCountry::create([
                        'country_orig'      => $originCounVar,
                        'country_dest'      => $destinationCounVar,
                        'localcharge_id'    => $SurchargeId->id
                    ]); //
                }
            }
        }

        $request->session()->flash('message.content', 'Surcharge Updated' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Surcharge.F.C.D',[$request->contract_id,0]);
    }
    public function DestroySurchargersF($id){
        try{
            $failsurCharge = FailSurCharge::find($id);
            $failsurCharge->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }
    public function DestroySurchargersG($id){
        try{
            $surchargers = LocalCharge::find($id);
            $surchargers->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    //Datatable Rates Y Surchargers -----------------------------------------------------
    public function FailedRatesDeveloperLoad($id,$selector){
        //$id se refiere al id del contracto
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcarrier = new Carrier();

        $originV;
        $destinationV;
        $carrierV;
        $currencyV;
        $originA;
        $destinationA;
        $carrierA;
        $currencyA;
        $twuentyA;
        $fortyA;
        $fortyhcA;
        $fortynorA;
        $fortyfiveA;
        $failrates = collect([]);

        if($selector == 1){
            $failratesFor   = DB::select('call  proc_fail_rates_fcl('.$id.')');

            //$failratesFor = FailRate::where('contract_id','=',$id)->get();
            foreach( $failratesFor as $failrate){
                $carrAIn;
                $pruebacurre = "";
                $originA        = explode("_",$failrate->origin_port);
                $destinationA   = explode("_",$failrate->destiny_port);
                $carrierA       = explode("_",$failrate->carrier_id);
                $currencyA      = explode("_",$failrate->currency_id);
                $twuentyA       = explode("_",$failrate->twuenty);
                $fortyA         = explode("_",$failrate->forty);
                $fortyhcA       = explode("_",$failrate->fortyhc);
                $fortynorA      = explode("_",$failrate->fortynor);
                $fortyfiveA     = explode("_",$failrate->fortyfive);

                $schedule_typeA = explode("_",$failrate->schedule_type);
                $transit_timeA  = explode("_",$failrate->transit_time);
                $viaA           = explode("_",$failrate->via);

                $originOb       = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                    ->first();
                //$originAIn = $originOb['id'];
                $originC   = count($originA);
                if($originC <= 1){
                    $originA = $originOb['name'];
                } else{
                    $originA = $originA[0].' (error)';
                    $classdorigin='color:red';
                }

                $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                    ->first();
                //$destinationAIn = $destinationOb['id'];
                $destinationC   = count($destinationA);
                if($destinationC <= 1){
                    $destinationA = $destinationOb['name'];
                } else{
                    $destinationA = $destinationA[0].' (error)';
                }

                $twuentyC   = count($twuentyA);
                if($twuentyC <= 1){
                    $twuentyA = $twuentyA[0];
                } else{
                    $twuentyA = $twuentyA[0].' (error)';
                }

                $fortyC   = count($fortyA);
                if($fortyC <= 1){
                    $fortyA = $fortyA[0];
                } else{
                    $fortyA = $fortyA[0].' (error)';
                }

                $fortyhcC   = count($fortyhcA);
                if($fortyhcC <= 1){
                    $fortyhcA = $fortyhcA[0];
                } else{
                    $fortyhcA = $fortyhcA[0].' (error)';
                }

                $fortynorC   = count($fortynorA);
                if($fortynorC <= 1){
                    $fortynorA = $fortynorA[0];
                } else{
                    $fortynorA = $fortynorA[0].' (error)';
                }

                $fortyfiveC   = count($fortyfiveA);
                if($fortyfiveC <= 1){
                    $fortyfiveA = $fortyfiveA[0];
                } else{
                    $fortyfiveA = $fortyfiveA[0].' (error)';
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

                //-------------------------------------------
                $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
                //$carrAIn = $carrierOb['id'];
                $carrierC = count($carrierA);
                if($carrierC <= 1){
                    //dd($carrierAIn);
                    $carrierA = $carrierA[0];
                }
                else{
                    $carrierA = $carrierA[0].' (error)';
                }
                $currencyC = count($currencyA);
                if($currencyC <= 1){
                    $currenc = Currency::where('alphacode','=',$currencyA[0])->orWhere('id','=',$currencyA[0])->first();
                    //$pruebacurre = $currenc['id'];
                    $currencyA = $currenc['alphacode'];
                }
                else{
                    $currencyA = $currencyA[0].' (error)';
                }        
                $colec = ['id'              =>  $failrate->id,
                          'contract_id'     =>  $id,
                          'origin_portLb'   =>  $originA,       //
                          'destiny_portLb'  =>  $destinationA,  // 
                          'carrierLb'       =>  $carrierA,      //
                          'twuenty'         =>  $twuentyA,      //    
                          'forty'           =>  $fortyA,        //  
                          'fortyhc'         =>  $fortyhcA,      //
                          'fortynor'        =>  $fortynorA,     //
                          'fortyfive'       =>  $fortyfiveA,    //
                          'currency_id'     =>  $currencyA,     //
                          'operation'       =>  '1',
                          'schedule_type'   =>  $schedule_typeA,
                          'transit_time'    =>  $transit_timeA,
                          'via'             =>  $viaA
                         ];

                $pruebacurre = "";
                $carrAIn = "";
                $failrates->push($colec);

            }
            return DataTables::of($failrates)->addColumn('action', function ( $failrate) {
                return '<a href="#" class="" onclick="showModalsavetorate('.$failrate['id'].','.$failrate['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-FailRate" data-id-failrate="'.$failrate['id'].'" class=""><i class="la la-trash"></i></a>';
            })
                ->editColumn('id', '{{$id}}')->toJson();



        } else if($selector == 2){

            $ratescol = PrvRates::get_rates($id);

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
                <a href="#" id="delete-Rate" data-id-rate="'.$ratescol['id'].'" class=""><i class="la la-trash"></i></a>';
                })
                ->editColumn('id', '{{$id}}')->toJson();
        }
    }

    public function FailSurchargeLoad($id,$selector){

        if($selector == 1){
            $objharbor          = new Harbor();
            $objcurrency        = new Currency();
            $objcarrier         = new Carrier();
            $objsurcharge       = new Surcharge();
            $objtypedestiny     = new TypeDestiny();
            $objCalculationType = new CalculationType();
            $typedestiny           = $objtypedestiny->all()->pluck('description','id');
            $surchargeSelect       = $objsurcharge->where('company_user_id','=', \Auth::user()->company_user_id)->pluck('name','id');
            $carrierSelect         = $objcarrier->all()->pluck('name','id');
            $harbor                = $objharbor->all()->pluck('display_name','id');
            $currency              = $objcurrency->all()->pluck('alphacode','id');
            $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

            $failsurchargeS   = DB::select('call  proc_fails_surchargers_fcl('.$id.')');
            //$failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();
            $failsurchargecoll = collect([]);
            foreach($failsurchargeS as $failsurcharge){
                $classdorigin           =  'color:green';
                $classddestination      =  'color:green';
                $classtypedestiny       =  'color:green';
                $classcarrier           =  'color:green';
                $classsurcharger        =  'color:green';
                $classcalculationtype   =  'color:green';
                $classammount           =  'color:green';
                $classcurrency          =  'color:green';
                $surchargeA         =  explode("_",$failsurcharge->surcharge_id);
                $originA            =  explode("_",$failsurcharge->port_orig);
                $destinationA       =  explode("_",$failsurcharge->port_dest);
                $calculationtypeA   =  explode("_",$failsurcharge->calculationtype_id);
                $ammountA           =  explode("_",$failsurcharge->ammount);
                $currencyA          =  explode("_",$failsurcharge->currency_id);
                $carrierA           =  explode("_",$failsurcharge->carrier_id);
                $typedestinyA       =  explode("_",$failsurcharge->typedestiny_id);

                // -------------- ORIGIN -------------------------------------------------------------
                if($failsurcharge->differentiator == 1){
                    $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                        ->first();
                } else if($failsurcharge->differentiator == 2){
                    $originOb  = Country::where('variation->type','like','%'.strtolower($originA[0]).'%')
                        ->first();
                }
                $originAIn = $originOb['id'];
                $originC   = count($originA);
                if($originC <= 1){
                    $originA = $originOb['name'];
                } else{
                    $originA = $originA[0].' (error)';
                    $classdorigin='color:red';
                }

                // -------------- DESTINY ------------------------------------------------------------
                if($failsurcharge->differentiator == 1){
                    $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                        ->first();
                } else if($failsurcharge->differentiator == 2){
                    $destinationOb  = Country::where('variation->type','like','%'.strtolower($destinationA[0]).'%')
                        ->first();
                }
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
                }else{
                    $surchargeA         = $surchargeA[0].' (error)';
                    $classsurcharger    = 'color:red';
                }

                // -------------- CARRIER -------------------------------------------------------------
                $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
                $carrAIn = $carrierOb['id'];
                $carrierC = count($carrierA);
                if($carrierC <= 1){
                    $carrierA = $carrierA[0];
                }else{
                    $carrierA       = $carrierA[0].' (error)';
                    $classcarrier   ='color:red';
                }

                // -------------- CALCULATION TYPE ----------------------------------------------------
                $calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
                $calculationtypeAIn = $calculationtypeOb['id'];
                $calculationtypeC   = count($calculationtypeA);
                if($calculationtypeC <= 1){
                    $calculationtypeA = $calculationtypeA[0];
                }else{
                    $calculationtypeA       = $calculationtypeA[0].' (error)';
                    $classcalculationtype   = 'color:red';
                }

                // -------------- AMMOUNT ------------------------------------------------------------
                $ammountC = count($ammountA);
                if($ammountC <= 1){
                    $ammountA = $failsurcharge->ammount;
                }else{
                    $ammountA       = $ammountA[0].' (error)';
                    $classammount   = 'color:red';
                }

                // -------------- CURRENCY ----------------------------------------------------------
                $currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
                $currencyAIn  = $currencyOb['id'];
                $currencyC    = count($currencyA);
                if($currencyC <= 1){
                    $currencyA = $currencyA[0];
                }else{
                    $currencyA      = $currencyA[0].' (error)';
                    $classcurrency  = 'color:red';
                }
                // -------------- TYPE DESTINY -----------------------------------------------------
                //dd($failsurcharge['typedestiny_id']);
                $typedestinyobj    = TypeDestiny::where('description',$typedestinyA[0])->first();
                if(count($typedestinyA) <= 1){
                    $typedestinyLB = $typedestinyobj['description'];
                }else{
                    $typedestinyLB      = $typedestinyA[0].' (error)';
                    $classcurrency  = 'color:red';
                }


                ////////////////////////////////////////////////////////////////////////////////////
                $arreglo = [
                    'id'                    => $failsurcharge->id,
                    'surchargelb'           => $surchargeA,
                    'origin_portLb'         => $originA,
                    'destiny_portLb'        => $destinationA,
                    'carrierlb'             => $carrierA,
                    'typedestinylb'         => $typedestinyLB,
                    'ammount'               => $ammountA,
                    'calculationtypelb'     => $calculationtypeA,
                    'currencylb'            => $currencyA,
                    'classsurcharge'        => $classsurcharger,
                    'classorigin'           => $classdorigin,
                    'classdestiny'          => $classddestination,
                    'classtypedestiny'      => $classtypedestiny,
                    'classcarrier'          => $classcarrier,
                    'classcalculationtype'  => $classcalculationtype,
                    'classammount'          => $classammount,
                    'classcurrency'         => $classcurrency,
                    'operation'             => 1
                ];
                //dd($arreglo);
                $failsurchargecoll->push($arreglo);

            }
            //dd($failsurchargecoll);
            return DataTables::of($failsurchargecoll)->addColumn('action', function ( $failsurchargecoll) {
                return '<a href="#" class="" onclick="showModalsavetosurcharge('.$failsurchargecoll['id'].','.$failsurchargecoll['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Fail-Surcharge" data-id-failSurcharge="'.$failsurchargecoll['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();

        }else if($selector == 2){
            $surchargecollection = '';
            $surchargecollection = PrvSurchargers::get_surchargers($id);
            return DataTables::of($surchargecollection)->addColumn('action', function ( $surchargecollection) {
                return '<a href="#" class="" onclick="showModalsavetosurcharge('.$surchargecollection['id'].','.$surchargecollection['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Surcharge" data-id-Surcharge="'.$surchargecollection['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();
        }
    }

    // Descargar Archivos de referencia para la importacion -----------------------------

    public function DowLoadFiles($id){
        if($id == 1){
            return Storage::disk('DownLoadFile')->download('COMPANIES.xlsx');

        }else if($id == 2){
            return Storage::disk('DownLoadFile')->download('CONTACTS.xlsx');

        }
    }

    // Companies ------------------------------------------------------------------------
    public function UploadCompanies(Request $request){
        //dd($request->all());
        //dd($request->file('file'));
        $file = $request->file('file');
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $ext = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );

        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('companies.index');
        }

        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
        $errors = 0;
        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('UpLoadFile')
                   ->url($nombre),function($reader) use($errors,$request) {
                       $businessnameread    = 'business_name';
                       $phoneRead           = 'phone';
                       $emailRead           = 'email';
                       $taxnumberead        = 'tax_number';
                       $addressRead         = 'address';
                       $pricelevelRead      = 'price_level';

                       foreach($reader->get() as $read){

                           $businessnameVal    = '';
                           $phoneVal           = '';
                           $emailVal           = '';
                           $taxnumbeVal        = '';
                           $addressVal         = '';
                           $pricelevelVal      = '';
                           $ownerVal           = \Auth::user()->id;
                           $company_user_id    = \Auth::user()->company_user_id ;

                           $businessnameBol = false;
                           $phoneBol        = false;
                           $emailBol        = false;

                           $businessnameVal = $read[$businessnameread];
                           $phoneVal        = $read[$phoneRead];
                           $emailVal        = $read[$emailRead];
                           $taxnumbeVal     = $read[$taxnumberead];
                           $addressVal      = $read[$addressRead];
                           $pricelevelVal   = $read[$pricelevelRead];

                           if(empty($businessnameVal) != true){
                               $businessnameBol = true;
                           } else {
                               $businessnameVal = $businessnameVal.'_E_E';
                           }

                           if(empty($phoneVal) != true){
                               $phoneBol = true;
                           } else {
                               $phoneVal = $phoneVal.'_E_E';
                           }

                           if(empty($emailVal) != true){
                               $emailBol = true;
                           } else {
                               $emailVal = $emailVal.'_E_E';
                           }

                           if($businessnameBol == true &&
                              $phoneBol        == true &&
                              $emailBol        == true){

                               $existe = Company::where('business_name','=',$businessnameVal)
                                   ->where('phone','=',$phoneVal)
                                   ->where('address','=',$addressVal)
                                   ->where('email','=',$emailVal)
                                   ->where('tax_number','=',$taxnumbeVal)
                                   ->where('company_user_id','=',$company_user_id)
                                   ->where('owner','=',$ownerVal)
                                   ->get();
                               if(count($existe) == 0){
                                   Company::create([
                                       'business_name'          => $businessnameVal,
                                       'phone'                  => $phoneVal,
                                       'address'                => $addressVal,
                                       'email'                  => $emailVal,
                                       'tax_number'             => $taxnumbeVal,
                                       'logo'                   => null,
                                       'associated_quotes'      => null,
                                       'company_user_id'        => $company_user_id,
                                       'owner'                  => $ownerVal
                                   ]);
                               }
                           } else {
                               Failcompany::create([
                                   'business_name'          => $businessnameVal,
                                   'phone'                  => $phoneVal,
                                   'address'                => $addressVal,
                                   'email'                  => $emailVal,
                                   'tax_number'             => $taxnumbeVal,
                                   'associated_quotes'      => null,
                                   'company_user_id'        => $company_user_id,
                                   'owner'                  => $ownerVal
                               ]);
                               $errors = $errors + 1;
                           }
                       }

                       if($errors > 0){
                           $request->session()->flash('message.content', 'You successfully added the companies ');
                           $request->session()->flash('message.nivel', 'danger');
                           $request->session()->flash('message.title', 'Well done!');
                           if($errors == 1){
                               $request->session()->flash('message.content', $errors.' fee is not charged correctly');
                           }else{
                               $request->session()->flash('message.content', $errors.' Companies did not load correctly');
                           }
                       }
                       else{
                           $request->session()->flash('message.nivel', 'success');
                           $request->session()->flash('message.title', 'Well done!');
                       }
                   });
        Storage::Delete($nombre);
        return redirect()->route('companies.index');
    }

    public function FailedCompnaiesView(){
        $companyuser =\Auth::user()->company_user_id;
        $countfailcompanies = Failcompany::where('company_user_id',$companyuser)->count();
        return view('importation.failcompanies',compact('companyuser','countfailcompanies'));
        dd($countfailcompanies);
    }

    public function FailedCompnaieslist($id){
        $failcompanies = Failcompany::where('company_user_id',$id)->get();
        //dd($failcompanies);
        $collections = collect([]);
        foreach($failcompanies as $failcompany){

            $businessnameVal  = '';
            $phoneVal         = '';
            $emailVal         = '';
            $taxnumberVal     = '';

            $businessnameArr  = explode('_',$failcompany->business_name);
            $phoneArr         = explode('_',$failcompany->phone);
            $emailArr         = explode('_',$failcompany->email);
            $taxnumberArr     = explode('_',$failcompany->tax_number);

            if(count($businessnameArr) == 1){
                $businessnameVal = $businessnameArr[0];
            } else {
                $businessnameVal = $businessnameArr[0].'(Error)';
            }

            if(count($phoneArr) == 1){
                $phoneVal = $phoneArr[0];
            } else {
                $phoneVal = $phoneArr[0].'(Error)';
            }

            if(count($emailArr) == 1){
                $emailVal = $emailArr[0];
            } else {
                $emailVal = $emailArr[0].'(Error)';        
            }

            if(count($taxnumberArr) == 1){
                $taxnumberVal = $taxnumberArr[0];
            } else {
                $taxnumberVal = $taxnumberArr[0].'(Error)';         
            }

            $compnyuser = CompanyUser::find($id);
            $user = User::find($failcompany->owner);
            $idFC = $failcompany->id;
            $detalle = [
                'id'           => $idFC,
                'businessname' => $businessnameVal,
                'phone'        => $phoneVal,
                'address'      => $failcompany->address,
                'email'        => $emailVal,
                'taxnumber'    => $taxnumberVal,
                'compnyuser'   => $compnyuser->name,
                'owner'        => $user->name.' '.$user->lastname,
            ];
            //dd($detalle);
            $collections->push($detalle);
        }
        return DataTables::of($collections)->addColumn('action', function ($collection) {
            return '
                <a href="#" onclick="showModalcompany('.$collection['id'].')" class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-failcompany" data-id-failcompany="'.$collection['id'].'" class=""><i class="la la-remove"></i></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')->toJson();
    }

    public function ShowFailCompany($id){
        $failcompany = Failcompany::find($id);
        $businessnameVal  = '';
        $phoneVal         = '';
        $emailVal         = '';
        $taxnumberVal     = '';

        $classbusiness    =  'color:green';
        $classphone       =  'color:green';
        $classemail       =  'color:green';
        $classtaxnumber   =  'color:green';

        $businessnameArr  = explode('_',$failcompany->business_name);
        $phoneArr         = explode('_',$failcompany->phone);
        $emailArr         = explode('_',$failcompany->email);
        $taxnumberArr     = explode('_',$failcompany->tax_number);

        if(count($businessnameArr) == 1){
            $businessnameVal = $businessnameArr[0];
        } else {
            $businessnameVal = $businessnameArr[0].'(Error)';
            $classbusiness   = 'color:red';
        }

        if(count($phoneArr) == 1){
            $phoneVal = $phoneArr[0];
        } else {
            $phoneVal   = $phoneArr[0].'(Error)';
            $classphone = 'color:red';
        }

        if(count($emailArr) == 1){
            $emailVal = $emailArr[0];
        } else {
            $emailVal   = $emailArr[0].'(Error)';  
            $classemail = 'color:red';
        }

        if(count($taxnumberArr) == 1){
            $taxnumberVal = $taxnumberArr[0];
        } else {
            $taxnumberVal   = $taxnumberArr[0].'(Error)';
            $classtaxnumber =  'color:red';
        }

        $compnyuser = CompanyUser::find($failcompany->company_user_id);
        $user = User::find($failcompany->owner);
        $idFC = $failcompany->id;
        $detalle = [
            'id'              => $idFC,
            'businessname'    => $businessnameVal,
            'phone'           => $phoneVal,
            'address'         => $failcompany->address,
            'email'           => $emailVal,
            'taxnumber'       => $taxnumberVal,
            'compnyuser'      => $compnyuser->name,
            'compnyuserid'    => $compnyuser->id,
            'owner'           => $user->name.' '.$user->lastname,
            'ownerid'         => $user->id,
            'classbusiness'   => $classbusiness,
            'classphone'      => $classphone,
            'classemail'      => $classemail,
            'classtaxnumber'  => $classtaxnumber,
        ];
        return view('importation.Body-Modals.failedCompany',compact('detalle'));
        dd($detalle);

    }

    public function UpdateFailedCompany(Request $request,$id){
        //dd($request->all());
        $company = new Company();
        $company->business_name     = $request->businessname;
        $company->phone             = $request->phone;
        $company->address           = $request->address;
        $company->email             = $request->email;
        $company->tax_number        = $request->taxnumber;
        $company->company_user_id   = $request->compnyuserid;
        $company->owner             = $request->ownerid;
        $company->save();

        if(empty($company->id) != true){
            $failcompany = Failcompany::find($id);
            $failcompany->delete();
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The company was updated');

        $failcompany = Failcompany::where('company_user_id',$request->compnyuserid)->count();
        if($failcompany >= 1){
            return redirect()->route('view.fail.company');
        } else {
            return redirect()->route('companies.index');      
        }
    }

    public function DeleteFailedCompany($id){
        try{
            $fcompany = Failcompany::find($id);
            $fcompany->delete();
            return 1;
        } catch(Exception $e){
            return 2;
        }
    }

    // Contacts -------------------------------------------------------------------------

    public function UploadContacts(Request $request){
        //dd($request->all());
        $file = $request->file('file');
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;

        $ext = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );

        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('contacts.index');
        }


        Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
        $errors = 0;
        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('UpLoadFile')
                   ->url($nombre),function($reader) use($errors,$request) {

                       $firstname   = 'first_name';
                       $lastname    = 'last_name';
                       $email       = 'email';
                       $phone       = 'phone';
                       $position    = 'position';
                       $company     = 'company';

                       foreach($reader->get() as $read){

                           $firstnameVal    = $read[$firstname];
                           $lastnameVal     = $read[$lastname];
                           $emailVal        = $read[$email];
                           $phoneVal        = $read[$phone];
                           $positionVal     = $read[$position];
                           $companyVal      = $read[$company];

                           $companyBol      = false;
                           $firstnameBol    = false;
                           $lastnameBol     = false;
                           $phoneBol        = false;
                           $emailBol        = false;
                           $positionBol     = false;

                           $companies     = Company::where('business_name',$companyVal)->get();

                           if(count($companies) == 1){
                               $companyBol = true;
                               foreach($companies as $companyobj){
                                   $companyVal = $companyobj->id;
                               }
                           } else {
                               $companyVal = $companyVal.'_E_E';
                           }

                           if(empty($firstnameVal) != true){
                               $firstnameBol = true;
                           } else {
                               $firstnameVal = $firstnameVal.'_E_E';
                           }

                           if(empty($lastnameVal) != true){
                               $lastnameBol = true;
                           } else {
                               $lastnameVal = $lastnameVal.'_E_E';
                           }

                           if(empty($phoneVal) != true){
                               $phoneBol = true;
                           } else {
                               $phoneVal = $phoneVal.'_E_E';
                           }

                           if(empty($emailVal) != true){
                               $emailBol = true;
                           } else {
                               $emailVal = $emailVal.'_E_E';
                           }

                           if(empty($positionVal) != true){
                               $positionBol = true;
                           } else {
                               $positionVal = $positionVal.'_E_E';
                           }

                           if($companyBol  == true && $firstnameBol == true &&
                              $lastnameBol == true && $emailBol     == true && 
                              $positionBol == true && $phoneBol     == true){

                               $contactexits = Contact::where('first_name',$firstnameVal)
                                   ->where('last_name',$lastnameVal)
                                   ->where('phone',$phoneVal)
                                   ->where('email',$emailVal)
                                   ->where('position',$positionVal)
                                   ->where('company_id',$companyVal)
                                   ->get();

                               if(count($contactexits) == 0){
                                   Contact::create([
                                       'first_name' => $firstnameVal,
                                       'last_name'  => $lastnameVal,
                                       'phone'      => $phoneVal,
                                       'email'      => $emailVal,
                                       'position'   => $positionVal,
                                       'company_id' => $companyVal
                                   ]);
                               }
                           } else {

                               $failcontactexits = Failedcontact::where('first_name',$firstnameVal)
                                   ->where('last_name',$lastnameVal)
                                   ->where('phone',$phoneVal)
                                   ->where('email',$emailVal)
                                   ->where('position',$positionVal)
                                   ->where('company_id',$companyVal)
                                   ->where('company_user_id',\Auth::user()->company_user_id)
                                   ->get();

                               if(count($failcontactexits) == 0){
                                   Failedcontact::create([
                                       'first_name'      => $firstnameVal,
                                       'last_name'       => $lastnameVal,
                                       'phone'           => $phoneVal,
                                       'email'           => $emailVal,
                                       'position'        => $positionVal,
                                       'company_id'      => $companyVal,
                                       'company_user_id' => \Auth::user()->company_user_id
                                   ]);
                                   $errors = $errors + 1;
                               }
                           }
                       }

                       if($errors > 0){
                           $request->session()->flash('message.content', 'You successfully added the companies ');
                           $request->session()->flash('message.nivel', 'danger');
                           $request->session()->flash('message.title', 'Well done!');
                           if($errors == 1){
                               $request->session()->flash('message.content', $errors.' fee is not charged correctly');
                           }else{
                               $request->session()->flash('message.content', $errors.' Companies did not load correctly');
                           }
                       }
                       else{
                           $request->session()->flash('message.nivel', 'success');
                           $request->session()->flash('message.title', 'Well done!');
                       }
                   });

        Storage::Delete($nombre);
        return redirect()->route('contacts.index');

    }

    public function FailedContactView(){
        $companyuser = \Auth::user()->company_user_id;
        $countfailcontacts = Failedcontact::where('company_user_id',$companyuser)->count();
        return view('importation.failedcontacts',compact('countfailcontacts','companyuser'));
    }

    public function FailedContactlist($id){

        $failedconatcs =  Failedcontact::where('company_user_id',$id)->get();

        $collections = collect([]);
        foreach($failedconatcs as $failedconatc){

            $companylb     = '';
            $firstnameVal  = explode('_',$failedconatc['first_name']);
            $lastnameVal   = explode('_',$failedconatc['last_name']);
            $phoneVal      = explode('_',$failedconatc['phone']);
            $emailVal      = explode('_',$failedconatc['email']);
            $positionVal   = explode('_',$failedconatc['position']);
            $company_idVal = explode('_',$failedconatc['company_id']);

            if(count($firstnameVal) ==1){
                $firstnameVal = $firstnameVal[0];
            } else {
                $firstnameVal = $firstnameVal[0].'(Error)';
            }

            if(count($lastnameVal) ==1){
                $lastnameVal = $lastnameVal[0];
            } else {
                $lastnameVal = $lastnameVal[0].'(Error)';
            }

            if(count($phoneVal) ==1){
                $phoneVal = $phoneVal[0];
            } else {
                $phoneVal = $phoneVal.'(Error)';
            }

            if(count($emailVal) ==1){
                $emailVal = $emailVal[0];
            } else {
                $emailVal = $emailVal[0].'(Error)';
            }

            if(count($positionVal) ==1){
                $positionVal = $positionVal[0];
            } else {
                $positionVal = $positionVal[0].'(Error)';
            }
            $company = Company::where('id',$company_idVal[0])->first();
            if(count($company) == 1){
                $company_idVal = $company['id'];
                $companylb = $company['business_name'];
            } else {
                $companylb     = $company_idVal[0].'(Error)';
                $company_idVal = '';
            }
            $data = [
                'id'          => $failedconatc->id,
                'firstname'   => $firstnameVal,
                'lastname'    => $lastnameVal,
                'phone'       => $phoneVal,
                'email'       => $emailVal,
                'position'    => $positionVal,
                'company'     => $company_idVal,
                'companylb'   => $companylb,
            ];

            $collections->push($data);
        }
        //dd($collections);

        return DataTables::of($collections)->addColumn('action', function ($collection) {
            return '
                <a href="#" onclick="showModalcontact('.$collection['id'].')" class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-failcontact" data-id-failcontact="'.$collection['id'].'" class=""><i class="la la-remove"></i></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')->toJson();
    }

    public function DeleteFailedContact($id){
        try{
            $fcontact = Failedcontact::find($id);
            $fcontact->delete();
            return 1;
        } catch(Exception $e){
            return 2;
        }
    }

    public function ShowFailContact($id){
        $failedcontact = Failedcontact::find($id);

        $firnameVal     = '';
        $lastnameVal    = '';
        $phoneVal       = '';
        $emailVal       = '';
        $positionVal    = '';
        $companyVal     = '';

        $firnameclass   = 'color:green';
        $lastnameclass  = 'color:green';
        $phoneclass     = 'color:green';
        $emailclass     = 'color:green';
        $positionclass  = 'color:green';
        $companyclass   = 'color:green';

        $firnameArr   = explode('_',$failedcontact->first_name);
        $lastnameArr  = explode('_',$failedcontact->last_name);
        $phoneArr     = explode('_',$failedcontact->phone);
        $emailArr     = explode('_',$failedcontact->email);
        $positionArr  = explode('_',$failedcontact->position);
        $companyArr   = explode('_',$failedcontact->company_id);


        if(count($firnameArr) <= 1){
            $firnameVal = $firnameArr[0];
        } else{
            $firnameVal   = $firnameArr[0].'(Error)';
            $firnameclass = 'color:red';
        }

        if(count($lastnameArr) <= 1){
            $lastnameVal = $lastnameArr[0];
        } else{
            $lastnameVal   = $lastnameArr[0].'(Error)';
            $lastnameclass = 'color:red';
        }

        if(count($phoneArr) <= 1){
            $phoneVal = $phoneArr[0];
        } else{
            $phoneVal   = $phoneArr[0].'(Error)';
            $phoneclass = 'color:red';
        }

        if(count($emailArr) <= 1){
            $emailVal = $emailArr[0];
        } else{
            $emailVal   = $emailArr[0].'(Error)';
            $emailclass = 'color:red';
        }

        if(count($positionArr) <= 1){
            $positionVal = $positionArr[0];
        } else{
            $positionVal   = $positionArr[0].'(Error)';
            $positionclass = 'color:red';
        }

        if(count($companyArr) <= 1){
            $companyVal = $companyArr[0];
        } else{
            $companyVal   = '';
            $companyclass = 'color:red';
        }

        $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
        $detalle = [
            'id'             => $id,
            'firstname'      => $firnameVal,
            'lastname'       => $lastnameVal,
            'phone'          => $phoneVal,
            'email'          => $emailVal,
            'position'       => $positionVal,
            'company'        => $companyVal,
            'firstnameclass' => $firnameclass,
            'lastnameclass'  => $lastnameclass,
            'phoneclass'     => $phoneclass,
            'emailclass'     => $emailclass,
            'positionclass'  => $positionclass,
            'companyclass'   => $companyclass
        ];

        //dd($detalle);

        return view('importation.Body-Modals.FailEditContact',compact('detalle','companies'));
    }

    public function UpdateFailedContact(Request $request,$id){

        $contact = new Contact();
        $contact->first_name    = $request->firstname;
        $contact->last_name     = $request->lastname;
        $contact->phone         = $request->phone;
        $contact->email         = $request->email;
        $contact->position      = $request->position;
        $contact->company_id    = $request->company;
        $contact->save();

        if(empty($contact->id) != true){
            $contact = Failedcontact::find($id);
            $contact->delete();
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The conatct was updated');

        $countfail = Failedcontact::where('company_user_id',$id)->count();
        if(count($countfail) > 0){
            return redirect()->route('contacts.index');
        } else {
            return redirect()->route('view.fail.contact');
        }

    }

    public function ValidateCompany($id){
        $company = CompanyUser::find($id);
        return response()->Json($company);
    }

    // Account Importation --------------------------------------------------------------

    public function indexAccount(){


        $account = \DB::select('call  proc_account_fcl');

        return DataTables::of($account)
            /*  ->addColumn('status', function ( $account) {
                if(empty($account->contract->status)!=true){
                    return  $account->contract->status;
                }else{
                    return  'Contract erased';
                }

            })
            ->addColumn('company_user_id', function ( $account) {
                return  $account->companyuser->name;
            })
            ->addColumn('request_id', function ( $account) {
                if(empty($account->request_id) != true){
                    return  $account->request_id;
                } else {
                    return 'Manual';
                }
            })*/
            ->addColumn('action', function ( $account) {
                if(strnatcasecmp($account->namefile,'N/A') == 0){
                    if(empty($account->request_dp_id)){
                        $descarga = '&nbsp;<span style="color:#0072FC;font-size:15px" title="Duplicate Contract">Dp</span>';
                    } else {
                        $descarga = '&nbsp;<a href="#" onclick="AbrirModal(\'showRequestDp\','.$account->request_dp_id.',0)"><span  style="color:#0072FC;font-size:15px" title="Duplicate Contract">Dp</span></a>';
                    }

                } else {
                    $descarga = '&nbsp;
                    <a href="/Importation/DownloadAccountcfcl/'.$account->id.'" class=""><i class="la la-cloud-download" title="Download"></i></a>';
                }
                if($account->status != 'Contract erased'){
                    return '
                <a href="/Importation/fcl/rate/'.$account->contract_id.'/1" class=""><i class="la la-credit-card" title="Rates"></i></a>
                &nbsp;
                <a href="/Importation/fcl/surcharge/'.$account->contract_id.'/1" class=""><i class="la la-rotate-right" title="Surchargers"></i></a>
                &nbsp;
                '.$descarga.'
                &nbsp;
                <a href="#" id="delete-account-cfcl" data-id-account-cfcl="'.$account->id.'" class=""><i class="la la-remove" title="Delete"></i></a>';
                }else{
                    return $descarga.'&nbsp;
                <a href="#" id="delete-account-cfcl" data-id-account-cfcl="'.$account->id.'" class=""><i class="la la-remove" title="Delete"></i></a>';
                }
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function DestroyAccount($id){
        try{
            $contract = Contract::where('account_id',$id)->first();
            if(count($contract) == 1){
                $data = PrvValidation::ContractWithJob($contract->id);
                if($data['bool'] == false){
                    $account = AccountFcl::find($id);
                    Storage::disk('FclAccount')->delete($account->namefile);
                    $account->delete();
                }
                return response()->json(['success' => 1,'jobAssociate' => $data['bool']]);
            } else {
                $account = AccountFcl::find($id);
                Storage::disk('FclAccount')->delete($account->namefile);
                $account->delete();
                return response()->json(['success' => 1,'jobAssociate' => false]);
            }
        } catch(Exception $e){
            return response()->json(['success' => 2,'jobAssociate' => false]);
        }
    }

    public function Download($id){
        $account  = AccountFcl::find($id);
        $time       = new \DateTime();
        $now        = $time->format('d-m-y');
        $company    = CompanyUser::find($account->company_user_id);
        $extObj     = new \SplFileInfo($account->namefile);
        $ext        = $extObj->getExtension();
        $name       = $account->id.'-'.$company->name.'_'.$now.'-FLC.'.$ext;
        try{
            return Storage::disk('s3_upload')->download('Account/FCL/'.$account->namefile,$name);
        } catch(\Exception $e){
            return Storage::disk('FclAccount')->download($account->namefile,$name);
        }
    }

    // Account Request duplicated SHOW --------------------------------------------------

    public function ShowRequestDp($id){
        $request = NewContractRequest::find($id);
        $request->load('user','direction','Requestcarriers.carrier','companyuser');
        //dd($request->Requestcarriers->pluck('carrier')->implode('name',', '));
        return view('RequestV2.Fcl.Body-Modals.ShowRequest',compact('request'));
    }

    // Dropzone Importation Fcl----------------------------------------------------------
    public function storeMedia(Request $request){
        $path = storage_path('tmp/importation/fcl');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    // Solo Para Testear ----------------------------------------------------------------
    public function testExcelImportation(){

        //account 29
        //contracto 45
        //request 13
        $account = AccountFcl::find(29);

        //                $mediaItem  = $account->getFirstMedia('document');
        //                $excel      = Storage::disk('FclAccount')->get($mediaItem->id.'/'.$mediaItem->file_name);
        //                Storage::disk('FclImport')->put($mediaItem->file_name,$excel);
        //                $excelF     = Storage::disk('FclImport')->url($mediaItem->file_name);

        //$extObj     = new \SplFileInfo($mediaItem->file_name);
        //$ext        = $extObj->getExtension();
        //        $ext        = 'xlsx';
        //        if(strnatcasecmp($ext,'xlsx')==0){
        //            $inputFileType = 'Xlsx';
        //        } else if(strnatcasecmp($ext,'xls')==0){
        //            $inputFileType = 'Xls';
        //        } else {
        //            $inputFileType = 'Csv';
        //        }
        //
        //        $myacl =  new MyReadFilter(1,5);
        //        $reader = IOFactory::createReader($inputFileType);
        //$reader->setReadDataOnly(true);
        //$reader->setReadFilter($myacl);
        //$spreadsheet = $reader->load($excelF);
        //        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        //        //$sheetData = $spreadsheet->getActiveSheet()->toArray(null,true,true,true);
        //        dd($sheetData);
        //        dd($sheetData[1]['Receipt']);

        $resp = NewContractRequest::find(9);
        //$name = json_decode($resp->data,true);
        //dd($name['group_containers']['name']);
        $groupContainers = GroupContainer::all();
        $data = $groupContainers->firstWhere('id', 1);
        dd(json_decode(null,true),$data);

    }

}
