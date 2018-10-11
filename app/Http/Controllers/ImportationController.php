<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\Rate;
use App\Harbor;
use App\Carrier;
use App\FileTmp;
use App\Company;
use App\FailRate;
use App\Currency;
use App\Contract;
use App\Surcharge;
use App\LocalCharge;
use App\TypeDestiny;
use App\CompanyUser;
use App\LocalCharPort;
use App\FailSurCharge;
use App\CalculationType;
use App\LocalCharCarrier;
use Illuminate\Http\Request;
use App\Jobs\ReprocessRatesJob;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ReprocessSurchargersJob;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportationRatesSurchargerJob;

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

                $originEX       = explode('_',$failrate->origin_port);
                $destinyEX      = explode('_',$failrate->destiny_port);
                $carrierArr     = explode('_',$failrate->carrier_id);
                $twentyArr      = explode('_',$failrate->twuenty);
                $fortyArr       = explode('_',$failrate->forty);
                $fortyhcArr     = explode('_',$failrate->fortyhc);
                $fortynorArr    = explode('_',$failrate->fortynor);
                $fortyfiveArr   = explode('_',$failrate->fortyfive);
                $currencyArr    = explode('_',$failrate->currency_id);


                $carrierEX     = count($carrierArr);
                $twuentyEX     = count($twentyArr);
                $fortyEX       = count($fortyArr);
                $fortyhcEX     = count($fortyhcArr);
                $currencyEX    = count($currencyArr);

                if($carrierEX   <= 1 &&  $twuentyEX   <= 1 &&
                   $fortyEX     <= 1 &&  $fortyhcEX   <= 1 &&
                   $currencyEX  <= 1 ){
                    $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
                    // Origen Y Destino ------------------------------------------------------------------------

                    $originResul = str_replace($caracteres,'',strtolower($originEX[0]));
                    $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
                        ->get();
                    if(count($originExits) == 1){
                        $originB = true;
                        foreach($originExits as $originRc){
                            $originV = $originRc['id'];
                        }
                    }

                    $destinResul = str_replace($caracteres,'',strtolower($destinyEX[0]));
                    $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
                        ->get();
                    if(count($destinationExits) == 1){
                        $destinyB = true;
                        foreach($destinationExits as $destinationRc){
                            $destinationV = $destinationRc['id'];
                            // dd($destinationV);
                        }
                    }

                    //---------------- Carrier ------------------------------------------------------------------

                    $carrierResul = str_replace($caracteres,'',$carrierArr[0]);
                    $carrier = Carrier::where('name','=',$carrierResul)->first();
                    if(empty($carrier->id) != true){
                        $carriExitBol = true;
                        $carrierVal = $carrier->id;
                    }

                    //---------------- 20' ------------------------------------------------------------------

                    if(empty($twentyArr[0]) != true || (int)$twentyArr[0] == 0){
                        $twentyExiBol = true;
                        $twentyVal   = (int)$twentyArr[0];
                    }

                    //----------------- 40' -----------------------------------------------------------------

                    if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                        $fortyExiBol = true;
                        $fortyVal   = (int)$fortyArr[0];
                    }

                    //----------------- 40'HC --------------------------------------------------------------

                    if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                        $fortyhcExiBol = true;
                        $fortyhcVal   = (int)$fortyhcArr[0];
                    }

                    //----------------- 40'NOR -------------------------------------------------------------

                    if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                        $fortynorExiBol = true;
                        $fortynorVal   = (int)$fortynorArr[0];
                    }

                    //----------------- 45' ----------------------------------------------------------------

                    if(empty($fortyfiveArr[0]) != true || (int)$fortyfiveArr[0] == 0){
                        $fortyfiveExiBol = true;
                        $fortyfiveVal   = (int)$fortyfiveArr[0];
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

                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if($originB == true && $destinyB == true &&
                       $twentyExiBol   == true && $fortyExiBol    == true &&
                       $fortyhcExiBol  == true && $fortynorExiBol == true &&
                       $fortyfiveExiBol == true && $values        == true &&
                       $carriExitBol   == true && $curreExitBol   == true){
                        $collecciont = '';

                        $collecciont = Rate::create([
                            'origin_port'   => $originV,
                            'destiny_port'  => $destinationV,
                            'carrier_id'    => $carrierVal,                            
                            'contract_id'   => $id,
                            'twuenty'       => $twentyVal,
                            'forty'         => $fortyVal,
                            'fortyhc'       => $fortyhcVal,
                            'fortynor'      => $fortynorVal,
                            'fortyfive'     => $fortyfiveVal,
                            'currency_id'   => $currencyVal
                        ]);
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
            ReprocessRatesJob::dispatch($id);
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

                if(count($surchargerEX) == 1     && count($typedestinyEX) == 1
                   && count($typedestinyEX) == 1 && count($calculationtypeEX) == 1
                   && count($ammountEX) == 1     && count($currencyEX) == 1
                   && count($carrierEX) == 1){

                    $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
                    // Origen Y Destino ------------------------------------------------------------------------

                    $originResul = str_replace($caracteres,'',strtolower($originEX[0]));
                    $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
                        ->get();    
                    if(count($originExits) == 1){
                        $originB = true;
                        foreach($originExits as $originRc){
                            $originV = $originRc['id'];
                        }
                    }

                    $destinResul = str_replace($caracteres,'',strtolower($destinyEX[0]));
                    $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
                        ->get();
                    if(count($destinationExits) == 1){
                        $destinyB = true;
                        foreach($destinationExits as $destinationRc){
                            $destinationV = $destinationRc['id'];
                            // dd($destinationV);
                        }
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

                    $amountV = (int)$ammountEX[0];

                    //  Currency -------------------------------------------------------------------------------

                    $currencyV = Currency::where('alphacode','=',$currencyEX[0])->first();
                    if(count($currencyV) == 1){
                        $currencyB = true;
                        $currencyV = $currencyV['id'];
                    }

                    //  Carrier -------------------------------------------------------------------------------

                    $carrierV = Carrier::where('name','=',$carrierEX[0])->first();
                    if(count($carrierV) == 1){
                        $carrierB = true;
                        $carrierV = $carrierV['id'];
                    }

                    /*$colleccion = collect([]);
                    $colleccion = [
                        'origen'            =>  $originV,
                        'destiny'           =>  $destinationV,
                        'surcharge'         =>  $surchargerV,
                        'typedestuny'       =>  $typedestunyV,
                        'calculationtypeV'  =>  $calculationtypeV,
                        'amountV'           =>  $amountV,
                        'currencyV'         =>  $currencyV,
                        'carrierV'          =>  $carrierV
                    ];

                    dd($colleccion);*/

                    if($originB == true     && $destinyB == true 
                       && $surcharB == true && $typedestinyB == true
                       && $calculationtypeB == true && $currencyB == true
                       && $carrierB == true){

                        $Localchargeobj = LocalCharge::create([
                            'surcharge_id'          => $surchargerV,
                            'typedestiny_id'        => $typedestunyV,
                            'contract_id'           => $id,
                            'calculationtype_id'    => $calculationtypeV,
                            'ammount'               => $amountV,
                            'currency_id'           => $currencyV
                        ]);

                        $LocalchargeId = $Localchargeobj->id;

                        LocalCharCarrier::create([
                            'carrier_id'     => $carrierV,
                            'localcharge_id' => $LocalchargeId
                        ]);

                        LocalCharPort::create([
                            'port_orig'         => $originV,
                            'port_dest'         => $destinationV,
                            'localcharge_id'    => $LocalchargeId                
                        ]);
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
            ReprocessSurchargersJob::dispatch($id);
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
        $carrier        = carrier::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        return view('importation.ImporContractFcl',compact('harbor','carrier','companysUser'));
    }

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request){
        //dd($request->all());
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $type       = $request->type;
        $carrierVal     = $request->carrier;
        $destinyArr     = $request->destiny;
        $originArr      = $request->origin;
        $CompanyUserId  = $request->CompanyUserId;
        $carrierBol     = false;
        $destinyBol     = false;
        $originBol      = false;
        $fortynorBol    = false;
        $fortyfiveBol   = false;
        $data= collect([]);
        $harbor  = harbor::all()->pluck('display_name','id');
        $carrier = carrier::all()->pluck('name','id');
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
            return redirect()->route('contracts.edit',$request->contract_id);
        }
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
        $contract     = new Contract();
        $contract->name             = $request->name;
        $contract->number           = $request->number;
        $validity                   = explode('/',$request->validation_expire);
        $contract->validity         = $validity[0];
        $contract->expire           = $validity[1];
        $contract->status           = 'incomplete';
        $contract->company_user_id  = $CompanyUserId;
        $contract->save(); 
        $Contract_id = $contract->id;
        $fileTmp = new FileTmp();
        $fileTmp->contract_id = $Contract_id;
        $fileTmp->name_file   = $nombre;
        $fileTmp->save(); //*/

        $statustypecurren = $request->valuesCurrency;
        $targetsArr =[ 0 => "20'", 1 => "40'", 2 => "40'HC"];

        // si type es igual a  1, el proceso va por rates, si es 2 va por rate mas surchargers

        if($type == 2){
            array_push($targetsArr,"Calculation Type","Charge");
        }

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
            ->Load(\Storage::disk('UpLoadFile')
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
            'Contract_id'     => $Contract_id,
            'number'          => $request->number,
            'name'            => $request->name,
            'existfortynor'   => $fortynorBol,
            'fortynor'        => 0,
            'existfortyfive'  => $fortyfiveBol,
            'fortyfive'       => 0,
            'fileName'        => $nombre,
            'validatiion'     => $request->validation_expire,
        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);

        return view('importation.ContractFclProcess',compact('harbor','carrier','coordenates','targetsArr','data','countTarges','type','statustypecurren','CompanyUserId'));
        /*}catch(\Exception $e){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error with the archive');
            return redirect()->route('importaion.fcl');
        }//*/
    }

    // * proccesa solo cuando son rates --------------------------------------------------
    public function ProcessContractFcl(Request $request){
        //dd($request->all());
        $requestobj = $request->all();
        try{
            $errors = 0;
            Excel::selectSheetsByIndex(0)
                ->Load(\Storage::disk('UpLoadFile')
                       ->url($requestobj['FileName']),function($reader) use($requestobj,$request,$errors) {
                           $reader->noHeading = true;
                           //$reader->ignoreEmpty();
                           $currency   = "Currency";
                           $twenty     = "20'";
                           $forty      = "40'";
                           $fortyhc    = "40'HC";
                           $fortynor   = "40'NOR";
                           $fortyfive  = "45'";
                           $origin     = "origin";
                           $originExc  = "Origin";
                           $destiny    = "destiny";
                           $destinyExc = "Destiny";
                           $carrier    = "Carrier";
                           $statustypecurren        = "statustypecurren";
                           $statusexistfortynor     = "existfortynor";
                           $statusexistfortyfive    = "existfortyfive";

                           $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                           $i = 1;
                           foreach($reader->get() as $read){
                               $carrierVal      = '';
                               $originVal       = '';
                               $destinyVal      = '';
                               $origenFL        = '';
                               $destinyFL       = '';
                               $currencyVal     = '';
                               $twentyVal       = '';
                               $fortyVal        = '';
                               $fortyhcVal      = '';
                               $fortynorVal     = '';
                               $fortyfiveVal    = '';
                               $originResul     = '';
                               $destinResul     = '';
                               $currencResul    = '';
                               $carrierResul    = '';

                               $originBol       = false;
                               $origExiBol      = false;
                               $destinyBol      = false;
                               $destiExitBol    = false;
                               $carriExitBol    = false;
                               $curreExiBol     = false;
                               $twentyExiBol    = false;
                               $fortyExiBol     = false;
                               $fortyhcExiBol   = false;
                               $fortynorExiBol  = false;
                               $fortyfiveExiBol = false;
                               $carriBol        = false;
                               $values          = true;
                               if($i != 1){
                                   // 0 => 'Currency', 1 => "20'", 2 => "40'", 3 => "40'HC"
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
                                   if($requestobj['existorigin'] == true){
                                       $originBol = true;
                                       $origExiBol = true; //segundo boolean para verificar campos errados
                                       $randons = $requestobj[$origin];
                                   } else {
                                       // dd($read[$requestobj->$originExc]);
                                       $originVal = $read[$requestobj[$originExc]];// hacer validacion de puerto en DB
                                       $originResul = str_replace($caracteres,'',strtolower($originVal));
                                       $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
                                           ->get();
                                       if(count($originExits) == 1){
                                           $origExiBol = true;
                                           foreach($originExits as $originRc){
                                               $originVal = $originRc['id'];
                                           }
                                       } else{
                                           $originVal = $originVal.'_E_E';
                                       }
                                   }
                                   //---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
                                   if($requestobj['existdestiny'] == true){
                                       $destinyBol = true;
                                       $destiExitBol = true; //segundo boolean para verificar campos errados
                                       $randons = $requestobj[$destiny];
                                   } else {
                                       $destinyVal = $read[$requestobj[$destinyExc]];// hacer validacion de puerto en DB
                                       $destinResul = str_replace($caracteres,'',strtolower($destinyVal));
                                       $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
                                           ->get();
                                       if(count($destinationExits) == 1){
                                           $destiExitBol = true;
                                           foreach($destinationExits as $destinationRc){
                                               $destinyVal = $destinationRc['id'];
                                           }
                                       }else{
                                           $destinyVal = $destinyVal.'_E_E';
                                       }
                                   }

                                   $twentyArr    = explode(' ',$read[$requestobj[$twenty]]);
                                   $fortyArr     = explode(' ',$read[$requestobj[$forty]]);
                                   $fortyhcArr   = explode(' ',$read[$requestobj[$fortyhc]]);
                                   if($requestobj[$statusexistfortynor] == 1){
                                       $fortynorArr  = explode(' ',$read[$requestobj[$fortynor]]);
                                   }
                                   if($requestobj[$statusexistfortyfive] == 1){
                                       $fortyfiveArr = explode(' ',$read[$requestobj[$fortyfive]]);
                                   }

                                   //---------------- CURRENCY ---------------------------------------------------------------
                                   $arraycarga = [];
                                   if($requestobj[$statustypecurren] == 2){
                                       if(count($twentyArr) > 1 ){
                                           array_push($arraycarga,$twentyArr[1]);
                                       } 

                                       if(count($fortyArr) > 1 ){
                                           array_push($arraycarga,$fortyArr[1]);
                                       }

                                       if(count($fortyhcArr) > 1 ){
                                           array_push($arraycarga,$fortyhcArr[1]);
                                       }

                                       if($requestobj[$statusexistfortynor] == 1){
                                           if(count($fortynorArr) > 1 ){
                                               array_push($arraycarga,$fortynorArr[1]);
                                           }
                                       }

                                       if($requestobj[$statusexistfortyfive] == 1){
                                           if(count($fortyfiveArr) > 1 ){
                                               array_push($arraycarga,$fortyfiveArr[1]);
                                           }
                                       }

                                       if(count($arraycarga) > 0){
                                           foreach($arraycarga as $true){
                                               $currencyVal = str_replace($caracteres,'',$true);
                                               $currenctwen = Currency::where('alphacode','=',$currencyVal)->first();
                                               if(empty($currenctwen->id) != true){
                                                   $curreExiBol = true;
                                                   $currencyVal = $currenctwen->id;
                                                   break;
                                               }else {
                                                   $currencyVal = $currencyVal.'_E_E';
                                               }
                                           }
                                       }  else{
                                           if(count($twentyArr) > 1){
                                               $currencyVal = $twentyArr[1].'_E_E';
                                           } else{
                                               $currencyVal = '_E_E';
                                           }
                                       }

                                   } else { 
                                       $currencResul = str_replace($caracteres,'',$read[$requestobj[$currency]]);
                                       $currenc = Currency::where('alphacode','=',$currencResul)->first();
                                       if(empty($currenc->id) != true){
                                           $curreExiBol = true;
                                           $currencyVal =  $currenc->id;
                                       }
                                       else{
                                           $currencyVal = $read[$requestobj[$currency]].'_E_E';
                                       }
                                   }
                                   //dd($currencyVal);



                                   //---------------- 20' ---------------------------------------------------------------
                                   if(empty($twentyArr[0]) != true || (int)$twentyArr[0] == '0'){
                                       $twentyExiBol = true;
                                       $twentyVal = (int)$twentyArr[0];
                                   }
                                   else{
                                       $twentyVal = $read[$requestobj[$twenty]].'_E_E';
                                   }
                                   //---------------- 40' ---------------------------------------------------------------
                                   if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                                       $fortyExiBol = true;
                                       $fortyVal = (int)$fortyArr[0];
                                   }
                                   else{
                                       $fortyVal = $read[$requestobj[$forty]].'_E_E';
                                   }
                                   //---------------- 40'HC -------------------------------------------------------------
                                   if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                                       $fortyhcExiBol = true;
                                       $fortyhcVal = (int)$fortyhcArr[0];
                                   }
                                   else{
                                       $fortyhcVal = $read[$requestobj[$fortyhc]].'_E_E';
                                   }
                                   if($requestobj[$statusexistfortynor] == 1){
                                       //---------------- 40'NOR -------------------------------------------------------------
                                       if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                                           $fortynorExiBol = true;
                                           $fortynorVal = (int)$fortynorArr[0];
                                       }
                                       else{
                                           $fortynorVal = $read[$requestobj[$fortyhc]].'_E_E';
                                       }

                                   } else {
                                       $fortynorVal = 0;
                                       $fortynorExiBol = true;
                                   }

                                   if($requestobj[$statusexistfortyfive] == 1){
                                       //---------------- 45' ----------------------------------------------------------------
                                       if(empty($fortyfiveArr[0]) != true || (int)$fortyfiveArr[0] == '0'){
                                           $fortyfiveExiBol = true;
                                           $fortyfiveVal = (int)$fortyfiveArr[0];
                                       }
                                       else{
                                           $fortyfiveVal = $read[$requestobj[$fortyfive]].'_E_E';
                                       }
                                   } else {
                                       $fortyfiveVal = 0;
                                       $fortyfiveExiBol = true;
                                   }

                                   if($twentyVal == 0
                                      && $fortyVal == 0
                                      && $fortyhcVal == 0
                                      && $fortynorVal == 0
                                      && $fortyfiveVal == 0){
                                       $values = false;
                                   }

                                   if( $origExiBol == true 
                                      && $destiExitBol  == true
                                      && $carriExitBol  == true 
                                      && $twentyExiBol  == true 
                                      && $fortyExiBol   == true 
                                      && $twentyExiBol  == true 
                                      && $fortynorExiBol == true
                                      && $fortyfiveExiBol == true
                                      && $values == true ){
                                       //good rates

                                       if($originBol == true || $destinyBol == true){

                                           foreach($randons as  $rando){
                                               //insert por arreglo de puerto
                                               if($originBol == true ){
                                                   $originVal = $rando;
                                               } else {
                                                   $destinyVal = $rando;
                                               }

                                               Rate::create([
                                                   'origin_port'   => $originVal,
                                                   'destiny_port'  => $destinyVal,
                                                   'carrier_id'    => $carrierVal,
                                                   'contract_id'   => $requestobj['Contract_id'],
                                                   'twuenty'       => $twentyVal,
                                                   'forty'         => $fortyVal,
                                                   'fortyhc'       => $fortyhcVal,
                                                   'fortynor'      => $fortynorVal,
                                                   'fortyfive'     => $fortyfiveVa,
                                                   'currency_id'   => $currencyVal
                                               ]);
                                           }
                                       }else {
                                           // fila por puerto, sin expecificar origen ni destino manualmente
                                           Rate::create([
                                               'origin_port'   => $originVal,
                                               'destiny_port'  => $destinyVal,
                                               'carrier_id'    => $carrierVal,
                                               'contract_id'   => $requestobj['Contract_id'],
                                               'twuenty'       => $twentyVal,
                                               'forty'         => $fortyVal,
                                               'fortyhc'       => $fortyhcVal,
                                               'fortynor'      => $fortynorVal,
                                               'fortyfive'     => $fortyfiveVal,
                                               'currency_id'   => $currencyVal
                                           ]);
                                       }
                                   } else {
                                       // fail rates
                                       if($carriExitBol == true){
                                           if($carriBol == true){
                                               $carrier = Carrier::find($requestobj['carrier']); 
                                               $carrierVal = $carrier['name'];  
                                           }else{
                                               $carrier = Carrier::where('name','=',$read[$requestobj['Carrier']])->first(); 
                                               $carrierVal = $carrier['name']; 
                                           }
                                       }
                                       /* if($curreExiBol == true){
                                           $currencyVal = $read[$requestobj[$currency]];
                                       }*/

                                       if( $twentyExiBol == true){
                                           if(empty($read[$requestobj[$twenty]]) == true){
                                               $twentyVal = 0;
                                           } else{
                                               //  $twentyVal = $read[$requestobj[$twenty]];
                                           }
                                       }

                                       //---------------------------------------------------
                                       if( $fortyExiBol == true){
                                           if(empty($read[$requestobj[$forty]]) == true){
                                               $fortyVal = 0;
                                           } 
                                       }
                                       //---------------------------------------------------
                                       if( $fortyhcExiBol == true){
                                           if(empty($read[$requestobj[$fortyhc]]) == true){
                                               $fortyhcVal = 0;
                                           } else{
                                               //$fortyhcVal = $read[$requestobj[$fortyhc]];
                                           }
                                       }
                                       //---------------------------------------------------
                                       if( $fortynorExiBol == true){
                                           if(empty($fortynorVal) == true){
                                               $fortynorVal = 0;
                                           } 
                                       }
                                       //---------------------------------------------------
                                       if( $fortyfiveExiBol == true){
                                           if(empty($fortyfiveVal) == true){
                                               $fortyfiveVal = 0;
                                           } 
                                       }
                                       /*        $prue = collect([]);
                                       $prue = [
                                           '$origExiBol'  =>  $origExiBol, 
                                           '$destiExitBol'=>  $destiExitBol,
                                           '$carriExitBol'=>  $carriExitBol, 
                                           '$twentyExiBol'=>  $twentyExiBol, 
                                           '$fortyExiBol'=> $fortyExiBol , 
                                           '$twentyExiBol'=>  $twentyExiBol, 
                                           '$fortynorExiBo'=>  $fortynorExiBol,
                                           '$fortyfiveExiBo'=>  $fortyfiveExiBol,
                                           '$originVal'=>  $originVal,
                                           '$destinyVal'=>  $destinyVal,
                                           '$values'=>  $values,
                                           '$twentyVal' => $twentyVal,
                                           '$fortyVal' => $fortyVal,
                                           '$fortyhcVal' => $fortyhcVal,
                                           '$fortyhcVal' => $fortyhcVal,
                                           '$fortyfiveVal' => $fortyfiveVal,
                                           '$fortynor' => $fortynorVal,
                                           '$currencyVal' => $currencyVal,
                                           '$carrierVal' => $carrierVal,
                                           '$fortynorExiBol' => $fortynorExiBol,
                                           '$fortyfiveExiBol' => $fortyfiveExiBol,
                                       ];
                                       dd($prue); */
                                       if($values == true){
                                           // dd('$prue');

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
                                                       'contract_id'   => $requestobj['Contract_id'],
                                                       'twuenty'       => $twentyVal,
                                                       'forty'         => $fortyVal,
                                                       'fortyhc'       => $fortyhcVal,
                                                       'fortynor'      => $fortynorVal,
                                                       'fortyfive'     => $fortyfiveVal,
                                                       'currency_id'   => $currencyVal
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
                                                   'contract_id'   => $requestobj['Contract_id'],
                                                   'twuenty'       => $twentyVal,
                                                   'forty'         => $fortyVal,
                                                   'fortyhc'       => $fortyhcVal,
                                                   'fortynor'      => $fortynorVal,
                                                   'fortyfive'     => $fortyfiveVal,
                                                   'currency_id'   => $currencyVal
                                               ]); //*/
                                               $errors++;
                                           }
                                       }
                                       //*/
                                       //dd('para');
                                   }
                               }
                               $i++;
                           }

                           Storage::delete($requestobj['FileName']);
                           FileTmp::where('contract_id','=',$requestobj['Contract_id'])->delete();
                           if($errors > 0){
                               $request->session()->flash('message.content', 'You successfully added the rate ');
                               $request->session()->flash('message.nivel', 'danger');
                               $request->session()->flash('message.title', 'Well done!');
                               if($errors == 1){
                                   $request->session()->flash('message.content', $errors.' fee is not charged correctly');
                               }else{
                                   $request->session()->flash('message.content', $errors.' Rates did not load correctly');
                               }
                           }
                           else{
                               $request->session()->flash('message.nivel', 'success');
                               $request->session()->flash('message.title', 'Well done!');
                           }
                       });
            $contract = new Contract();
            $contract = Contract::find($request['Contract_id']);
            $contract->status = 'publish';
            $contract->update();
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$requestobj['Contract_id'],1]);

        } catch(\Illuminate\Database\QueryException $e){

            Storage::delete($request->FileName);
            FileTmp::where('contract_id','=',$requestobj['Contract_id'])->delete();
            $contractobj = new Contract();
            $contractobj = Contract::find($requestobj['Contract_id']);
            $contractobj->delete();

            $requestobj->session()->flash('message.nivel', 'danger');
            $requestobj->session()->flash('message.content', 'There was an error loading the file');
            return redirect()->route('importaion.fcl');
        }
    }
    public function FailedRatesDeveloper($id,$tab){
        //$id se refiere al id del contracto
        $countrates = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
        $countfailrates = FailRate::where('contract_id','=',$id)->count();
        return view('importation.TestFailRates2',compact('countfailrates','countrates','id','tab'));
    }

    // * proccesa solo cuando es Surchargers, Se envia a cola de trabajos 2do. plano
    public function ProcessContractFclRatSurch(Request $request){
        $companyUserId = $request->CompanyUserId;
        $UserId =\Auth::user()->id;
        ImportationRatesSurchargerJob::dispatch($request->all(),$companyUserId,$UserId); //NO BORRAR!!
        return redirect()->route('redirect.Processed.Information');
    }
    public function redirectProcessedInformation(){
        return view('importation.ProcessedInformation');
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
            $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
            //dd(\Storage::disk('UpLoadFile')->url($nombre));
            $contract = $requestobj->contract_id;
            $errors=0;
            Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors,$requestobj) {

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

                    $originResul = str_replace($caracteres,'',strtolower($book->$origin));

                    $originExitsDul = Harbor::where('varation->type','like','%'.$originResul.'%')
                        ->get();
                    if(count($originExitsDul) == 1){
                        $origB=true;
                        foreach($originExitsDul as $originRc){
                            $originVdul = $originRc['id'];
                        }
                    }

                    $destinResul = str_replace($caracteres,'',strtolower($book->$destination));

                    $destinationExitsDul = Harbor::where('varation->type','like','%'.$destinResul.'%')
                        ->get();
                    if(count($destinationExitsDul) == 1){
                        $destiB=true;
                        foreach($destinationExitsDul as $destinationRc){
                            $destinationVdul = $destinationRc['id'];
                        }
                    }


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
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $rates = Rate::find($id);
        return view('importation.Body-Modals.GoodEditRates', compact('rates','harbor','carrier','currency'));
    }
    public function EditRatesFail($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $failrate = FailRate::find($id);

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
        //dd($rates);
        return view('importation.Body-Modals.FailEditRates', compact('failrates','harbor','carrier','currency'));
    }
    public function CreateRates(Request $request, $id){



        $return = Rate::create([
            "origin_port"  => $request->origin_port,
            "destiny_port" => $request->destiny_port,
            "carrier_id"   => $request->carrier_id,
            "contract_id"  => $request->contract_id,
            "twuenty"      => $request->twuenty,
            "forty"        => $request->forty,
            "fortyhc"      => $request->fortyhc,
            "fortynor"     => $request->fortynor,
            "fortyfive"    => $request->fortyfive,
            "currency_id"  => $request->currency_id
        ]);
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
        $rate->origin_port  =  $request->origin_id;
        $rate->destiny_port =  $request->destiny_id;
        $rate->carrier_id   =  $request->carrier_id;
        $rate->contract_id  =  $request->contract_id;
        $rate->twuenty      =  $request->twuenty;
        $rate->forty        =  $request->forty;
        $rate->fortyhc      =  $request->fortyhc;
        $rate->fortynor     =  $request->fortynor;
        $rate->fortyfive    =  $request->fortyfive;
        $rate->currency_id  =  $request->currency_id;
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
            $Rate->delete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    // Surcharge ------------------------------------------------------------------------

    public function UploadFileSubchargeForContract(Request $request){
        //dd($request);
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
            $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
            $contract = $request->contract_id;
            $errors=0;
            Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors,$requestobj) {
                if($reader->get()->isEmpty() != true){
                    LocalCharge::where('contract_id','=',$contract)
                        ->delete();
                    FailSurCharge::where('contract_id','=',$contract)
                        ->delete();
                } else{
                    $requestobj->session()->flash('message.nivel', 'danger');
                    $requestobj->session()->flash('message.content', 'The file is it empty');
                    return redirect()->route('contracts.edit',$contract);   
                }
                $i=1;
                $SurcharExist;
                $SurcharcarrierExist;
                $SurcharPortExist;
                foreach ($reader->get() as $book) {
                    $surchargeBook        = "surcharge";
                    $originBook           = "origin";
                    $destinationBook      = "destination";
                    $carrierBook          = "carrier";
                    $calculationtypeBook  = "calculation_type";
                    $amountBook           = "amount";
                    $currencyBook         = "currency";
                    $surchargeBol       = false;
                    $carrierBol         = false;
                    $calculationtypeBol = false;
                    $currencyBol        = false;
                    $ammountBol         = false;
                    $originBol          = false;
                    $destinationBol     = false;
                    $originVar      = $book->$originBook;
                    $destinationVar = $book->$destinationBook;
                    $ammountVar     = (int)$book->$amountBook;
                    $destinytypeVar = 3;
                    $surchargeVar       ="";
                    $carrierVar         ="";
                    $calculationtypeVar ="";
                    $currencyVar        ="";
                    $SurcharExist       = "";
                    $SurcharcarrierExist = "";
                    $SurcharPortExist = "";
                    $SurcharBootPortExist = "";
                    $surcharge = Surcharge::where('name','=',$book->$surchargeBook)->where('company_user_id','=',\Auth::user()->company_user_id)->first();
                    $calculationtype = CalculationType::where('name','like','%'.$book->$calculationtypeBook.'%')->first();
                    $originExits = Harbor::where('varation->type','like','%'.strtolower($originVar).'%')
                        ->get();
                    if(count($originExits) == 1){
                        $originBol=true;
                        foreach($originExits as $originRc){
                            $originVar = $originRc['id'];
                        }
                    }else{
                        $originVar = $originVar.'_E_E';
                    }
                    $destinationExits = Harbor::where('varation->type','like','%'.strtolower($destinationVar).'%')
                        ->get();
                    if(count($destinationExits) == 1){
                        $destinationBol=true;
                        foreach($destinationExits as $destinationRc){
                            $destinationVar = $destinationRc['id'];
                        }
                    }else{
                        $destinationVar = $destinationVar.'_E_E';
                    }
                    if(empty($surcharge) != true){
                        $surchargeBol = true;
                        $surchargeVar = $surcharge['id'];
                    }
                    else{
                        $surchargeVar = $book->$surchargeBook.'_E_E';
                    }

                    $carrier = Carrier::where('name','=',$book->$carrierBook)->first();
                    if(empty($carrier) != true){
                        $carrierBol = true;
                        $carrierVar = $carrier['id'];
                    }
                    else{
                        $carrierVar = $book->$carrierBook.'_E_E';
                    }

                    if(empty($calculationtype) != true){
                        $calculationtypeBol = true;
                        $calculationtypeVar = $calculationtype['id'];
                    }
                    else{
                        $calculationtypeVar = $book->$calculationtypeBook.'_E_E';
                    }

                    $currency = Currency::where('alphacode','=',$book->$currencyBook)->first();
                    if(empty($currency) != true){
                        $currencyBol = true;
                        $currencyVar = $currency['id'];
                    }
                    else{
                        $currencyVar = $book->$currencyBook.'_E_E';
                    }
                    if(empty($ammountVar) != true){
                        $ammountBol = true;
                    }
                    else{
                        $ammountVar = $ammountVar.'_E_E';
                    }
                    if($surchargeBol == true 
                       && $carrierBol == true 
                       && $calculationtypeBol == true 
                       && $currencyBol == true 
                       && $originBol == true
                       && $destinationBol == true
                       && $ammountBol == true){ 
                        $SurcharFull = LocalCharge::where('surcharge_id','=',$surchargeVar)
                            ->where('typedestiny_id','=',$destinytypeVar)
                            ->where('contract_id','=',$contract)
                            ->where('calculationtype_id','=',$calculationtypeVar)
                            ->where('ammount','=',$ammountVar)
                            ->where('currency_id','=',$currencyVar)
                            ->whereHas('localcharcarriers',function($q) use($carrierVar){
                                $q->where('carrier_id','=',$carrierVar);
                            })
                            ->whereHas('localcharports', function($k) use($originVar,$destinationVar){
                                $k->where('port_orig','=',$originVar);
                            })->get();
                        if($SurcharFull->isEmpty() != true){
                            //  echo 'existe sur y port orig <br>';
                            foreach($SurcharFull as $Surchar){
                                $existportdest = LocalCharge::where('surcharge_id','=',$surchargeVar)
                                    ->where('typedestiny_id','=',$destinytypeVar)
                                    ->where('contract_id','=',$contract)
                                    ->where('calculationtype_id','=',$calculationtypeVar)
                                    ->where('ammount','=',$ammountVar)
                                    ->where('currency_id','=',$currencyVar)
                                    ->whereHas('localcharcarriers',function($q) use($carrierVar){
                                        $q->where('carrier_id','=',$carrierVar);
                                    })
                                    ->whereHas('localcharports', function($k) use($originVar,$destinationVar){
                                        $k->where('port_orig','=',$originVar)->where('port_dest','=',$destinationVar);
                                    })->get();
                                if($existportdest->isEmpty()){
                                    //echo 'No existe puertto destno <br>';
                                    foreach($SurcharFull as $Surchar){
                                        LocalCharPort::create([
                                            'port_orig'        => $originVar,
                                            'port_dest'        => $destinationVar,
                                            'localcharge_id'   => $Surchar['id']
                                        ]);  
                                    }
                                } else{
                                    //echo 'existe valor<br>';
                                }
                            }
                        } else {
                            // echo 'vacio<br>';
                            $idlocalchar = LocalCharge::create([
                                'surcharge_id'        => $surchargeVar,
                                'typedestiny_id'      => $destinytypeVar,
                                'contract_id'         => $contract,
                                'calculationtype_id'  => $calculationtypeVar,
                                'ammount'             => $ammountVar,
                                'currency_id'         => $currencyVar,
                            ]);
                            LocalCharPort::create([
                                'port_orig'        => $originVar,
                                'port_dest'        => $destinationVar,
                                'localcharge_id'   => $idlocalchar->id
                            ]);
                            LocalCharCarrier::create([
                                'carrier_id'      => $carrierVar,
                                'localcharge_id'  => $idlocalchar->id
                            ]);
                        }
                    } else {
                        if($surchargeBol == true){
                            $surchargeVar = $book->$surchargeBook;
                        }
                        if($carrierBol == true){
                            $carrierVar = $book->$carrierBook;
                        }
                        if($calculationtypeBol == true){
                            $calculationtypeVar = $book->$calculationtypeBook;
                        }
                        if($currencyBol == true){
                            $currencyVar = $book->$currencyBook;
                        }
                        if($originBol == true){
                            $originVar = $book->$originBook;
                        }
                        if($destinationBol == true){
                            $destinationVar = $book->$destinationBook;
                        }
                        if($ammountBol == true){
                            $ammountVar = $book->$amountBook;
                        }

                        FailSurCharge::create([
                            'surcharge_id'       => $surchargeVar,
                            'port_orig'          => $originVar,
                            'port_dest'          => $destinationVar,
                            'typedestiny_id'     => $destinytypeVar,
                            'contract_id'        => $contract,
                            'calculationtype_id' => $calculationtypeVar,
                            'ammount'            => $ammountVar,
                            'currency_id'        => $currencyVar,
                            'carrier_id'         => $carrierVar
                        ]); //*/
                        $errors++;
                    }
                    if($errors > 0){
                        $requestobj->session()->flash('message.content', 'You successfully added the rate ');
                        $requestobj->session()->flash('message.nivel', 'danger');
                        $requestobj->session()->flash('message.title', 'Well done!');
                        if($errors == 1){
                            $requestobj->session()->flash('message.content', $errors.' Subcharge is not charged correctly');
                        }else{
                            $requestobj->session()->flash('message.content', $errors.' Subcharge did not load correctly');
                        }
                    }
                    else{
                        $requestobj->session()->flash('message.nivel', 'success');
                        $requestobj->session()->flash('message.title', 'Well done!');
                    }
                    $i++;
                }
            });
            Storage::delete($nombre);
            LocalCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            FailSurCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            return redirect()->route('Failed.Surcharge.F.C.D',[$contract,1]);
        } catch (\Illuminate\Database\QueryException $e) {
            Storage::delete($nombre);
            LocalCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            FailSurCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            $requestobj->session()->flash('message.nivel', 'danger');
            $requestobj->session()->flash('message.content', 'There was an error loading the file');
            return redirect()->route('contracts.edit',$requestobj->contract_id);
        }
    }    
    public function FailedSurchargeDeveloper($id,$tab){
        //$id se refiere al id del contracto
        $countfailsurcharge = FailSurCharge::where('contract_id','=',$id)->count();
        $countgoodsurcharge = LocalCharge::where('contract_id','=',$id)->count();
        return view('importation.SurchargersFailOF',compact('countfailsurcharge','countgoodsurcharge','id','tab'));

    }

    public function EditSurchargersGood($id){
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

        $goodsurcharges  = LocalCharge::with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->find($id);
        //dd($goodsurcharges);
        return view('importation.Body-Modals.GoodEditSurcharge', compact('goodsurcharges',
                                                                         'harbor',
                                                                         'carrierSelect',
                                                                         'currency',
                                                                         'surchargeSelect',
                                                                         'typedestiny',
                                                                         'calculationtypeselect'));
    }
    public function EditSurchargersFail($id){
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

        $failsurcharge = FailSurCharge::find($id);

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

        $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
            ->first();
        $originAIn = $originOb['id'];
        $originC   = count($originA);
        if($originC <= 1){
            //$originA = $originOb['name'];
        } else{
            //$originA = $originA[0].' (error)';
            $classdorigin='color:red';
        }
        $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
            ->first();
        $destinationAIn = $destinationOb['id'];
        $destinationC   = count($destinationA);
        if($destinationC <= 1){
            //$destinationA = $destinationOb['name'];
        } else{
            //$destinationA = $destinationA[0].' (error)';
            $classddestination='color:red';
        }

        $surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',\Auth::user()->company_user_id)->first();
        $surcharAin  = $surchargeOb['id'];
        $surchargeC = count($surchargeA);
        if($surchargeC <= 1){
            //$surchargeA = $surchargeA[0];
        }
        else{
            //$surchargeA         = $surchargeA[0].' (error)';
            $classsurcharger    = 'color:red';
        }
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
        $ammountC = count($ammountA);
        if($ammountC <= 1){
            $ammountA = $failsurcharge['ammount'];
        }
        else{
            $ammountA       = $ammountA[0].' (error)';
            $classammount   = 'color:red';
        }
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
        $typedestinyLB    = TypeDestiny::find($failsurcharge['typedestiny_id']);


        ////////////////////////////////////////////////////////////////////////////////////
        $failsurchargeArre = [
            'id'                    => $failsurcharge['id'],
            'surcharge'             => $surcharAin,
            'origin_port'           => $originAIn,
            'destiny_port'          => $destinationAIn,
            'carrier'               => $carrAIn,
            'contract_id'           => $failsurcharge['contract_id'],
            'typedestiny'           => $typedestinyLB['id'],
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
                                                                         'surchargeSelect',
                                                                         'typedestiny',
                                                                         'calculationtypeselect'));
    }
    public function CreateSurchargers(Request $request, $id){
        //dd($request->all());

        $surchargeVar       = $request->surcharge_id;
        $originVarArr       = $request->port_origlocal;
        $destinationVarArr  = $request->port_destlocal;
        $typedestinyVar     = $request->changetype;
        $carrierVarArr      = $request->carrier_id;
        $calculationtypeVar = $request->calculationtype_id;
        $ammountVar         = $request->ammount;
        $currencyVar        = $request->currency_id;
        $contractVar        = $request->contract_id;

        $failSurcharge = new FailSurCharge();
        $failSurcharge = FailSurCharge::find($id);
        $SurchargeId = LocalCharge::create([
            'surcharge_id'          => $surchargeVar,
            'typedestiny_id'        => $typedestinyVar,
            'contract_id'           => $contractVar,
            'calculationtype_id'    => $calculationtypeVar,
            'ammount'               => $ammountVar,
            'currency_id'           => $currencyVar
        ]);
        foreach($originVarArr as $originVar){
            foreach($destinationVarArr as $destinationVar){
                LocalCharPort::create([
                    'port_orig'         => $originVar,
                    'port_dest'         => $destinationVar,
                    'localcharge_id'    => $SurchargeId->id
                ]);
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
        $originVarArr          =  $request->port_origlocal;
        $destinationVarArr     =  $request->port_destlocal;
        $typedestinyVar        =  $request->changetype;
        $calculationtypeVar    =  $request->calculationtype_id;
        $ammountVar            =  $request->ammount;
        $currencyVar           =  $request->currency_id;
        $carrierVarArr         =  $request->carrier_id;

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
        foreach($originVarArr as $originVar){
            foreach($destinationVarArr as $destinationVar){
                LocalCharPort::create([
                    'port_orig'         => $originVar,
                    'port_dest'         => $destinationVar,
                    'localcharge_id'    => $SurchargeId->id
                ]); //
            }
        }
        LocalCharCarrier::where('localcharge_id','=',$SurchargeId->id)->forceDelete();
        foreach($carrierVarArr as $carrierVar){
            LocalCharCarrier::create([
                'carrier_id'        => $carrierVar,
                'localcharge_id'    => $SurchargeId->id  
            ]); //
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
            $surchargers->delete();
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


        $rates = Rate::with('carrier','contract','port_origin','port_destiny','currency')->where('contract_id','=',$id)->get();
        //dd($rates);


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
        $ratescol = collect([]);
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
                          'operation'       =>  '1'
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


            foreach($rates as $rate){
                $originRate     = '';
                $detinyRate     = '';
                $carrierRate    = '';
                $currencyRate   = '';

                $originRate     = $rate['port_origin']['name'];
                $detinyRate     = $rate['port_destiny']['name'];
                $carrierRate    = $rate['carrier']['name'];
                $currencyRate   = $rate->Currency->alphacode;

                $colec = ['id'              =>  $rate->id,
                          'contract_id'     =>  $id,            //
                          'origin_portLb'   =>  $originRate,    //
                          'destiny_portLb'  =>  $detinyRate,    //
                          'carrierLb'       =>  $carrierRate,   //
                          'twuenty'         =>  $rate->twuenty, //    
                          'forty'           =>  $rate->forty,   //  
                          'fortyhc'         =>  $rate->fortyhc, //
                          'fortynor'        =>  $rate->fortynor, //
                          'fortyfive'       =>  $rate->fortyfive, //
                          'currency_id'     =>  $currencyRate,  //
                          'operation'       =>  '2'
                         ];
                $ratescol->push($colec);
            }
            return DataTables::of($ratescol)->addColumn('action', function ($ratescol) {
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
                $ammountC = count($ammountA);
                if($ammountC <= 1){
                    $ammountA = $failsurcharge->ammount;
                }
                else{
                    $ammountA       = $ammountA[0].' (error)';
                    $classammount   = 'color:red';
                }
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
                $typedestinyLB    = TypeDestiny::where('description',$failsurcharge['typedestiny_id'])->first();

                ////////////////////////////////////////////////////////////////////////////////////
                $arreglo = [
                    'id'                    => $failsurcharge->id,
                    'surchargelb'           => $surchargeA,
                    'origin_portLb'         => $originA,
                    'destiny_portLb'        => $destinationA,
                    'carrierlb'             => $carrierA,
                    'typedestinylb'         => $typedestinyLB['description'],
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
            $goodsurcharges     = LocalCharge::where('contract_id','=',$id)->with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->get();
            $surchargecollection = collect([]);
            foreach($goodsurcharges as $surcharge){
                $origin             = '';
                $destiny            = '';
                $surchargeName      = '';
                $typedestiny        = '';
                $calculationtype    = '';
                $ammount            = '';
                $carrier            = '';
                $currency           = '';
                //dd($surcharge->localcharports);
                $PuertosOrigins = $surcharge->localcharports->pluck('port_orig')->unique();
                foreach($PuertosOrigins as $PortOrigin){
                    $OriginObj = Harbor::find($PortOrigin);
                    $origin = $origin.$OriginObj->name.'.';
                }
                //dd($surcharge->localcharports);
                $PuertosDestins = $surcharge->localcharports->pluck('port_dest')->unique();
                foreach($PuertosDestins as $PortDestiny){
                    $DestinyObj = Harbor::find($PortDestiny);
                    $destiny = $destiny.$DestinyObj->name.'. ';
                }

                $carrierArre = $surcharge->localcharcarriers->pluck('carrier')->pluck('name');
                foreach($carrierArre as $carrierName){
                    $carrier = $carrier.$carrierName.'. ';
                }

                $surchargeName   = $surcharge->surcharge['name'];
                $typedestiny     = $surcharge->typedestiny->description;
                $calculationtype = $surcharge->calculationtype->name;
                $ammount         = $surcharge->ammount;
                $currency        = $surcharge->currency->alphacode;
                $arreglo = [
                    'id'                => $surcharge->id,
                    'surchargelb'       => $surchargeName,
                    'origin_portLb'     => $origin,
                    'destiny_portLb'    => $destiny,
                    'carrierlb'         => $carrier,
                    'typedestinylb'     => $typedestiny,
                    'ammount'           => $ammount,
                    'calculationtypelb' => $calculationtype,
                    'currencylb'        => $currency,
                    'operation'         => 2
                ];

                $surchargecollection->push($arreglo);
            }
            //dd($surchargecollection);
            return DataTables::of($surchargecollection)->addColumn('action', function ( $surchargecollection) {
                return '<a href="#" class="" onclick="showModalsavetosurcharge('.$surchargecollection['id'].','.$surchargecollection['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Surcharge" data-id-Surcharge="'.$surchargecollection['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();
        }
    }

    // Solo Para Testear ----------------------------------------------------------------
    public function testExcelImportation(){
        ini_set('max_execution_time', 300); 
        Excel::load(\Storage::disk('UpLoadFile')
                    ->url('02102018_153843_MAERSK2.xlsx'), function($sheet) {
                        $sheet->noHeading = true;
                        $sheet->ignoreEmpty();
                        $sheet->takeRows(4);
                        dd($sheet->first());
                    });

    }

}
