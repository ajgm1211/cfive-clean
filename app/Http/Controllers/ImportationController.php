<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\Rate;
use PrvRates;
use PrvHarbor;
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
use App\ContractCarrier;
use App\CalculationType;
use App\LocalCharCountry;
use App\LocalCharCarrier;
use Illuminate\Http\Request;
use App\Jobs\ReprocessRatesJob;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use App\Jobs\ImportationRatesFclJob;
use App\Jobs\SynchronImgCarrierJob;
use App\Jobs\ReprocessSurchargersJob;
use Illuminate\Support\Facades\Storage;
use App\NewContractRequest as RequestFcl;
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
            ReprocessRatesJob::dispatch($id)->onQueue('importation');
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
            ReprocessSurchargersJob::dispatch($id)->onQueue('importation');
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

    // precarga la vista para importar rates o rates mas surchargers
    public function LoadViewImporContractFcl(){
        $harbor         = harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $direction      = [null=>'Please Select'];
        $direction2      = Direction::all();
        foreach($direction2 as $d){
            $direction[$d['id']]=$d->name;
        }
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('importation.ImporContractFcl',compact('harbor','direction','country','region','carrier','companysUser','typedestiny'));
    }

    // precarga la vista para importar rates o rates mas surchargers desde Request
    public function requestProccess($id,$selector,$request_id){
        $load_carrier = false;
        $carrier_exec = Carrier::where('name','ALL')->first();
        $carrier_exec = $carrier_exec->id;
        if($selector == 1){
            $requestfcl     = RequestFcl::find($id);
            $requestfcl->load('Requestcarriers');
            //dd($requestfcl);
            if(count($requestfcl->Requestcarriers) == 1){
                foreach($requestfcl->Requestcarriers as $carrier_uniq){
                    if($carrier_uniq->id != $carrier_exec){
                        $load_carrier = true;
                    }
                }
            }
        } elseif($selector == 2){
            $contract     = Contract::find($id);
            $contract->load('carriers');
            //dd($contract);
            if(count($contract->carriers) == 1){
                foreach($contract->carriers as $carrier_uniq){
                    if($carrier_uniq->id != $carrier_exec){
                        $load_carrier = true;
                    }
                }
            }
        }
        $harbor         = harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $direction      = Direction::pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        if($selector == 1){
            return view('importation.ImportContractFCLRequest',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','requestfcl','selector','load_carrier'));    
        } elseif($selector == 2){
            return view('importation.ImportContractFCLRequest',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','contract','selector','request_id','load_carrier'));
        }

    }

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request){
        //dd($request->all());
        $now                = new \DateTime();
        $now2               = $now;
        $now                = $now->format('dmY_His');
        $now2               = $now2->format('Y-m-d');
        $type               = $request->type;
        $request_id         = $request->request_id;
        $carrierVal         = $request->carrier;
        $typedestinyVal     = $request->typedestiny;
        $originArr          = $request->origin;
        $originCountArr     = $request->originCount;
        $originRegionArr    = $request->originRegion;
        $destinyArr         = $request->destiny;
        $destinyCountArr    = $request->destinyCount;
        $destinyRegionArr   = $request->destinyRegion;
        $CompanyUserId      = $request->CompanyUserId;
        $statustypecurren   = $request->valuesCurrency;
        $statusPortCountry  = $request->valuesportcountry;
        $direction_id       = $request->direction;
        $selector           = $request->selector;

        $carrierBol         = false;
        $destinyBol         = false;
        $originBol          = false;
        $typedestinyBol     = false;
        $fortynorBol        = false;
        $fortyfiveBol       = false;
        $filebool           = false;
        $scheduleinfoBoll   = false;

        $data               = collect([]);
        $direction          = Direction::pluck('name','id');
        $typedestiny        = TypeDestiny::all()->pluck('description','id');
        $harbor             = harbor::all()->pluck('display_name','id');
        $country            = Country::all()->pluck('name','id');
        $region             = Region::all()->pluck('name','id');
        $carrier            = carrier::all()->pluck('name','id');
        $Contract_id;

        $file       = $request->file('file');
        $ext        = strtolower($file->getClientOriginalExtension());

        $validator  = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );


        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('contracts.edit',$request->contract_id);
        }
        //obtenemos el nombre del archivo
        $nombre     = $file->getClientOriginalName();
        $nombre     = $now.'_'.$nombre;
        $filebool   = \Storage::disk('FclImport')->put($nombre,\File::get($file));

        if($filebool){

            \Storage::disk('FclAccount')->put($nombre,\File::get($file));
            $account = new AccountFcl();
            $account->name              = $request->name;
            $account->date              = $now2;
            $account->namefile          = $nombre;
            $account->company_user_id   = $CompanyUserId;
            $account->request_id        = $request_id;
            $account->save();

            ProcessContractFile::dispatch($account->id,$account->namefile,'fcl','account')->onQueue('importation');
            //Queue::connection("importation")->push(new ProcessContractFile($account->id,$account->namefile,'fcl','account'));

            if($selector == 2){
                $contract = Contract::find($request->contract_id);
                $contract->account_id  = $account->id;
                $contract->update();
            } else {
                $contract   = new Contract();
                $contract->name             = $request->name;
                $validity                   = explode('/',$request->validation_expire);
                $contract->validity         = $validity[0];
                $contract->expire           = $validity[1];
                $contract->direction_id     = $direction_id;
                $contract->status           = 'incomplete';
                $contract->company_user_id  = $CompanyUserId;
                $contract->account_id  = $account->id;
                $contract->save();

                foreach($request->carrierM as $carrierVal){
                    ContractCarrier::create([
                        'carrier_id'    => $carrierVal,
                        'contract_id'   => $contract->id
                    ]);
                }
            }
            $contract->load('carriers');
            $Contract_id = $contract->id;
            $fileTmp    = new FileTmp();
            $fileTmp->contract_id = $Contract_id;
            $fileTmp->name_file   = $nombre;
            $fileTmp->save(); //*/
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error storage:link!!');
            return redirect()->route('contracts.edit',$request->contract_id);
        }

        $targetsArr =[ 0 => "20'", 1 => "40'", 2 => "40'HC"];


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

        if($request->DatShe != true){
            $scheduleinfoBoll = true;
            array_push($targetsArr,"Schedule Type","Transit Time","Via");
        }
        // si type es igual a  1, el proceso va por rates, si es 2 va por rate mas surchargers

        if($type == 2){
            array_push($targetsArr,"Calculation Type","Charge");
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
        if($type == 2){
            if($request->DatTypeDes == false){
                array_push($targetsArr,'Type Destiny');
            } else {
                $typedestinyVal;
                $typedestinyBol = true;
            }
        }

        $coordenates = collect([]);

        ini_set('memory_limit', '1024M');

        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('FclImport')
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
            'originCount'       => $originCountArr,
            'originRegion'      => $originRegionArr,
            'existdestiny'      => $destinyBol,
            'destiny'           => $destinyArr,
            'destinyCount'      => $destinyCountArr,
            'destinyRegion'     => $destinyRegionArr,
            'existcarrier'      => $carrierBol,
            'carrier'           => $carrierVal,            
            'existtypedestiny'  => $typedestinyBol,
            'typedestiny'       => $typedestinyVal,
            'Contract_id'       => $Contract_id,
            'number'            => $request->number,
            'name'              => $request->name,
            'existfortynor'     => $fortynorBol,
            'fortynor'          => 0,
            'existfortyfive'    => $fortyfiveBol,
            'fortyfive'         => 0,
            'fileName'          => $nombre,
            'scheduleinfo'      => $scheduleinfoBoll,
            'validatiion'       => $request->validation_expire,
        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);

        return view('importation.ContractFclProcess',compact('harbor',
                                                             'country',
                                                             'data',
                                                             'contract',
                                                             'type',
                                                             'region',
                                                             'carrier',
                                                             'direction',
                                                             'targetsArr',
                                                             'coordenates',
                                                             'countTarges',
                                                             'CompanyUserId',
                                                             'statustypecurren',
                                                             'statusPortCountry',
                                                             'typedestiny'));
    }

    // * proccesa solo cuando son rates --------------------------------------------------
    public function ProcessContractFcl(Request $request){
        //dd($request->all());
        $requestobj = $request->all();
        /*Rate::where('contract_id',$request->Contract_id)->forceDelete();
        FailRate::where('contract_id',$request->Contract_id)->forceDelete();*/
        $errors = 0;
        ImportationRatesFclJob::dispatch($requestobj)->onQueue('importation');
        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$requestobj['Contract_id'],1]);
    }
    public function FailedRatesDeveloper($id,$tab){
        //$id se refiere al id del contracto
        $countrates     = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
        $countfailrates = FailRate::where('contract_id','=',$id)->count();
        $contract       = Contract::find($id);
        return view('importation.TestFailRates2',compact('countfailrates','countrates','contract','id','tab'));
    }

    // * proccesa solo cuando es Surchargers, Se envia a cola de trabajos 2do. plano
    public function ProcessContractFclRatSurch(Request $request){
        $companyUserId = $request->CompanyUserId;
        $UserId =\Auth::user()->id;

        $requestobj = $request;
        $companyUserIdVal = $companyUserId;
        $errors = 0;
        $NameFile = $requestobj['FileName'];
        $path = \Storage::disk('FclImport')->url($NameFile);
        /*
        FailSurCharge::where('contract_id',$request->Contract_id)->forceDelete();
        LocalCharge::where('contract_id',$request->Contract_id)->forceDelete();
        Rate::where('contract_id',$request->Contract_id)->forceDelete();
        FailRate::where('contract_id',$request->Contract_id)->forceDelete();*/

        ImportationRatesSurchargerJob::dispatch($request->all(),$companyUserId,$UserId)->onQueue('importation'); //NO BORRAR!!
        $id = $request['Contract_id'];
        return redirect()->route('redirect.Processed.Information',$id);
    }
    public function redirectProcessedInformation($id){
        $contract       = Contract::find($id);
        return view('importation.ProcessedInformation',compact('id','contract'));
    }

    // Rates ----------------------------------------------------------------------------

    public function UploadFileRateForContract(Request $request){
        $requestobj = $request;
        $nombre='';
        try {

            $now = new \DateTime();
            $now = $now->format('dmY_His');
            $file = $requestobj->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $validator = \Validator::make(
                array('ext' => $ext),
                array('ext' => 'in:xls,xlsx,csv')
            );
            if ($validator->fails()) {
                $requestobj->session()->flash('message.nivel', 'danger');
                $requestobj->session()->flash('message.content', 'just archive with extension xlsx xls csv');
                return redirect()->route('contracts.edit',$requestobj->contract_id);
            }
            //obtenemos el nombre del archivo
            $nombre = $file->getClientOriginalName();
            $nombre = $now.'_'.$nombre;
            $dd = \Storage::disk('FclImport')->put($nombre,\File::get($file));
            //dd(\Storage::disk('UpLoadFile')->url($nombre));
            $contract = $requestobj->contract_id;
            $errors=0;
            Excel::Load(\Storage::disk('FclImport')->url($nombre),function($reader) use($contract,$errors,$requestobj) {

                $originResul  = '';
                $destinResul  = '';
                $currencResul = '';

                if($reader->get()->isEmpty() != true){
                    Rate::where('contract_id','=',$contract)
                        ->delete();
                    FailRate::where('contract_id','=',$contract)
                        ->delete();
                } else{
                    $requestobj->session()->flash('message.nivel', 'danger');
                    $requestobj->session()->flash('message.content', 'The file is it empty');
                    return redirect()->route('contracts.edit',$contract);   
                }
                foreach ($reader->get() as $book) {
                    $originVdul = '';
                    $destinationVdul = '';

                    $carrier = Carrier::where('name','=',$book->carrier)->first();
                    $twuenty = "20";
                    $forty = "40";
                    $fortyhc = "40hc";
                    $origin = "origin";
                    $destination = "destiny";
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////

                    $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                    $resultadoPortOri = PrvHarbor::get_harbor($book->$origin);
                    if($resultadoPortOri['boolean']){
                        $origB = true;    
                    }
                    $originVdul  = $resultadoPortOri['puerto'];


                    $resultadoPortDes = PrvHarbor::get_harbor($book->$destination);
                    if($resultadoPortDes['boolean']){
                        $destiB = true;    
                    }
                    $destinationVdul  = $resultadoPortDes['puerto'];

                    /////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $duplicate =  Rate::where('origin_port','=',$originVdul)
                        ->where('destiny_port','=',$destinationVdul)
                        ->where('carrier_id','=',$carrier['id'])
                        ->where('contract_id','=',$contract)
                        ->count();
                    if($duplicate <= 0){
                        $originResul  = '';
                        $destinResul  = '';
                        $currencResul = '';
                        $carrierResul = '';
                        $origB=false;
                        $destiB=false;
                        $carriB=false;
                        $twuentyB=false;
                        $fortyB=false;
                        $fortyhcB=false;
                        $curreB=false;
                        $originV;
                        $destinationV;
                        $carrierV;
                        $twuentyV;
                        $fortyV;
                        $fortyhcV;
                        $currencyV;
                        $values = true;

                        $currencResul = str_replace($caracteres,'',$book->currency);
                        $currenc = Currency::where('alphacode','=',$currencResul)->first();

                        $carrierResul = str_replace($caracteres,'',$book->carrier);
                        $carrier = Carrier::where('name','=',$carrierResul)->first();

                        $originResul = str_replace($caracteres,'',strtolower($book->$origin));
                        $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
                            ->get();
                        if(count($originExits) == 1){
                            $origB=true;
                            foreach($originExits as $originRc){
                                $originV = $originRc['id'];
                            }
                        }else{
                            $originV = $book->$origin.'_E_E';
                        }

                        $destinResul = str_replace($caracteres,'',strtolower($book->$destination));
                        $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
                            ->get();
                        if(count($destinationExits) == 1){
                            $destiB=true;
                            foreach($destinationExits as $destinationRc){
                                $destinationV = $destinationRc['id'];
                                // dd($destinationV);
                            }
                        }else{
                            $destinationV = $book->$destination.'_E_E';
                        }
                        if(empty($carrier->id) != true){
                            $carriB=true;
                            $carrierV = $carrier->id;
                        }else{
                            $carrierV = $book->carrier.'_E_E';
                        }
                        //////
                        if(empty($book->$twuenty) != true || (int)$book->$twuenty == 0){
                            $twuentyB=true;
                            $twuentyV = (int)$book->$twuenty;
                        }
                        else{
                            $twuentyV = $book->$twuenty.'_E_E';
                        }
                        /////
                        if(empty($book->$forty) != true || (int)$book->$forty == 0){
                            $fortyB=true;
                            $fortyV = (int)$book->$forty;
                        }
                        else{
                            $fortyV = $book->$forty.'_E_E';
                        }
                        /////
                        if(empty($book->$fortyhc) != true || (int)$book->$fortyhc == 0){
                            $fortyhcB=true;
                            $fortyhcV = (int)$book->$fortyhc;
                        }
                        else{
                            $fortyhcV = $book->$fortyhc.'_E_E';
                        }

                        if((int)$book->$twuenty == 0
                           && (int)$book->$forty == 0
                           && (int)$book->$fortyhc == 0){
                            $values = false;
                        }

                        if(empty($currenc->id) != true){
                            $curreB=true;
                            $currencyV =  $currenc->id;
                        }
                        else{
                            $currencyV = $book->currency.'_E_E';
                        }
                        if( $origB == true && $destiB == true
                           && $carriB == true && $twuentyB == true
                           && $fortyB == true && $fortyhcB == true
                           && $curreB == true
                           && $values == true) {
                            Rate::create([
                                'origin_port'   => $originV,
                                'destiny_port'  => $destinationV,
                                'carrier_id'    => $carrierV,
                                'contract_id'   => $contract,
                                'twuenty'       => $twuentyV,
                                'forty'         => $fortyV,
                                'fortyhc'       => $fortyhcV,
                                'currency_id'   => $currencyV,
                            ]);
                        }
                        else{
                            if($origB == true){
                                $originV = $book->$origin;
                            }
                            if($destiB == true){
                                $destinationV = $book->$destination;
                            }
                            if($curreB == true){
                                $currencyV = $book->currency;
                            }
                            if($carriB == true){
                                $carrierV = $book->carrier;
                            }
                            if( empty($book->$origin) == true
                               && empty($book->$destination) == true
                               && empty($book->carrier) == true
                               && empty($book->currency) == true
                               && empty($book->$twuenty) == true
                               && empty($book->$forty) == true
                               && empty($book->$fortyhc) == true ) {
                            }else{
                                $duplicateFail =  FailRate::where('origin_port','=',$originV)
                                    ->where('destiny_port','=',$destinationV)
                                    ->where('carrier_id','=',$carrierV)
                                    ->where('contract_id','=',$contract)
                                    ->count();
                                if($duplicateFail <= 0){
                                    if((int)$book->$twuenty == 0
                                       && (int)$book->$forty == 0
                                       && (int)$book->$fortyhc == 0){

                                    }else {
                                        FailRate::create([
                                            'origin_port'   => $originV,
                                            'destiny_port'  => $destinationV,
                                            'carrier_id'    => $carrierV,
                                            'contract_id'   => $contract,
                                            'twuenty'       => $twuentyV,
                                            'forty'         => $fortyV,
                                            'fortyhc'       => $fortyhcV,
                                            'currency_id'   => $currencyV,
                                        ]);
                                        $errors++;
                                    }
                                }
                            }
                        }
                    }
                } //***
                if($errors > 0){
                    $requestobj->session()->flash('message.content', 'You successfully added the rate ');
                    $requestobj->session()->flash('message.nivel', 'danger');
                    $requestobj->session()->flash('message.title', 'Well done!');
                    if($errors == 1){
                        $requestobj->session()->flash('message.content', $errors.' fee is not charged correctly');
                    }else{
                        $requestobj->session()->flash('message.content', $errors.' Rates did not load correctly');
                    }
                }
                else{
                    $requestobj->session()->flash('message.nivel', 'success');
                    $requestobj->session()->flash('message.title', 'Well done!');
                }
            });
            Storage::delete($nombre);
            Rate::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            FailRate::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$contract,1]);
            //dd($res);*/
        } catch (\Illuminate\Database\QueryException $e) {
            Storage::delete($nombre);
            Rate::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            FailRate::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            $requestobj->session()->flash('message.nivel', 'danger');
            $requestobj->session()->flash('message.content', 'There was an error loading the file');
            return redirect()->route('contracts.edit',$requestobj->contract_id);
        }
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
            $failratesFor = FailRate::where('contract_id','=',$id)->get();
            foreach( $failratesFor as $failrate){
                $carrAIn;
                $pruebacurre = "";
                $originA        = explode("_",$failrate['origin_port']);
                $destinationA   = explode("_",$failrate['destiny_port']);
                $carrierA       = explode("_",$failrate['carrier_id']);
                $currencyA      = explode("_",$failrate['currency_id']);
                $twuentyA       = explode("_",$failrate['twuenty']);
                $fortyA         = explode("_",$failrate['forty']);
                $fortyhcA       = explode("_",$failrate['fortyhc']);
                $fortynorA      = explode("_",$failrate['fortynor']);
                $fortyfiveA     = explode("_",$failrate['fortyfive']);

                $schedule_typeA = explode("_",$failrate['schedule_type']);
                $transit_timeA  = explode("_",$failrate['transit_time']);
                $viaA           = explode("_",$failrate['via']);

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
                <a href="#" id="delete-FailRate" data-id-failrate="'.$failrate['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();



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
                <a href="#" id="delete-Rate" data-id-rate="'.$ratescol['id'].'" class=""><i class="la la-remove"></i></a>';
                })
                ->editColumn('id', 'ID: {{$id}}')->toJson();
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
            $failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();
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
        $account = AccountFcl::with('contract','companyuser')->get();
        return DataTables::of($account)
            ->addColumn('status', function ( $account) {
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
            })
            ->addColumn('action', function ( $account) {
                if(empty($account->contract->status)!=true){
                    return '
                <a href="/Importation/fcl/rate/'.$account->contract['id'].'/1" class=""><i class="la la-credit-card" title="Rates"></i></a>
                &nbsp;
                <a href="/Importation/fcl/surcharge/'.$account->contract['id'].'/1" class=""><i class="la la-rotate-right" title="Surchargers"></i></a>
                &nbsp;
                <a href="/Importation/DownloadAccountcfcl/'.$account['id'].'" class=""><i class="la la-cloud-download" title="Download"></i></a>
                &nbsp;
                <a href="#" id="delete-account-cfcl" data-id-account-cfcl="'.$account['id'].'" class=""><i class="la la-remove" title="Delete"></i></a>';
                }else{
                    return '
                <a href="/Importation/DownloadAccountcfcl/'.$account['id'].'" class=""><i class="la la-cloud-download" title="Download"></i></a>
                &nbsp;
                <a href="#" id="delete-account-cfcl" data-id-account-cfcl="'.$account['id'].'" class=""><i class="la la-remove" title="Delete"></i></a>';
                }
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function DestroyAccount($id){
        try{
            $account = AccountFcl::find($id);
            Storage::disk('FclAccount')->delete($account->namefile);
            $account->delete();
            return 1;
        } catch(Exception $e){
            return 2;
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

    // Solo Para Testear ----------------------------------------------------------------
    public function testExcelImportation(){

        /*$carriers = Carrier::all();
        foreach($carriers as $carrier){
            $type['type'] = [strtolower($carrier->name)];
            $json = json_encode($type);importation
            $carrier->varation  = $json;
            $carrier->save();
        }
        //dd(PrvHarbor::get_harbor('chile'));
        dd(PrvCarrier::get_carrier('cosco'));*/
        //SynchronImgCarrierJob::dispatch()->onConnection('importation');
        SynchronImgCarrierJob::dispatch()->onQueue('importation');
    }

}