<?php

namespace App\Http\Controllers;
use Excel;
use PrvHarbor;
use App\Harbor;
use App\FileTmp;
use App\Carrier;
use App\CompanyUser;
use App\ContractLcl;
use Illuminate\Http\Request;

class ImportationLclController extends Controller
{

    public function index()
    {
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        return view('ImportationLcl.index',compact('harbor','carrier','companysUser'));
    }


    public function create(Request $request)
    {
        //dd($request->all());
        $requestobj = $request->all();
        $NameFile           = $requestobj['FileName'];
        $path = public_path(\Storage::disk('UpLoadFile')->url($NameFile));
        $companyUserIdVal       = $requestobj['CompanyUserId'];
        //dd($path);
        $errors = 0;
        Excel::selectSheetsByIndex(0)
            ->Load($path,function($reader) use($requestobj,$errors,$NameFile,$companyUserIdVal) {
                $reader->noHeading = true;

                $currency               = "Currency";
                $origin                 = "origin";
                $originExc              = "Origin";
                $destiny                = "destiny";
                $destinyExc             = "Destiny";
                $carrier                = "Carrier";
                $wm                     = "W/M";
                $contractId             = "Contract_id";
                $statustypecurren       = "statustypecurren";

                $ratescollection         = collect([]);
                $ratesFailcollection     = collect([]);
                $i = 0;
                foreach($reader->get() as $read){
                    $carrierVal          = '';
                    $originVal           = '';
                    $destinyVal          = '';
                    $currencyVal         = '';
                    $randons             = '';
                    $currencyVal         = '';
                    $contractIdVal       = $requestobj['Contract_id'];

                    $currencResul            = '';

                    $originBol               = false;
                    $origExiBol              = false;
                    $destinyBol              = false;
                    $destiExitBol            = false;
                    $carriExitBol            = false;
                    $carriBol                = false;
                    $variantecurrency        = false;
                    $curreExitBol            = false;

                    $values                  = true;

                    if($i != 0){
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
                            $carrierResul = str_replace($caracteres,'',$carrierVal);
                            $carrier = Carrier::where('name','=',$carrierResul)->first();
                            if(empty($carrier->id) != true){
                                $carriExitBol = true;
                                $carrierVal = $carrier->id;
                            }else{
                                $carrierVal = $carrierVal.'_E_E';
                            }
                        }

                        //---------------- CURRENCY VALUES ------------------------------------------------------

                   /*     $wmArr      = explode(' ',trim($read[$requestobj[$wm]]));

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
                                $curreExitBol = true;
                                $currencyVal =  $currenc->id;
                            }
                            else{
                                $currencyVal = $read[$requestobj[$currency]].'_E_E';
                            }

                        }*/
                        $data = [
                            'carriExitBol'      => $carriExitBol,
                            'carrierVal'        => $carrierVal,
                            'destiExitBol'      => $destiExitBol,
                            'destinyVal'        => $destinyVal,
                            'origExiBol'        => $origExiBol,
                            'originVal'         => $originVal,
                            'randons'           => $randons,
                            'contractIdVal'    => $contractIdVal,
                            //''  => ,
                        ];
                        dd($data);
                        /*  if(carriExitBol == true && destiExitBol == true &&
origExiBol == true){
                        if($originBol == true || $destinyBol == true){
                            foreach($randons as  $rando){
                                //insert por arreglo de puerto
                                if($originBol == true ){
                                    $originVal = $rando;
                                } else {
                                    $destinyVal = $rando;
                                }

                                if($requestobj[$statustypecurren] == 2){
                                    $currencyVal = $currencyValtwen;
                                }

                                $ratesArre = RateLcl::create([
                                'origin_port'    => $originVal,
                                'destiny_port'   => $destinyVal,
                                'carrier_id'     => $carrierVal,
                                'contract_id'    => $contractIdVal,
                                'twuenty'        => $twentyVal,
                                'forty'          => $fortyVal,
                                'fortyhc'        => $fortyhcVal,
                                'fortynor'       => $fortynorVal,
                                'fortyfive'      => $fortyfiveVal,
                                'currency_id'    => $currencyVal
                            ]);
                                //dd($ratesArre);
                            } 
                        }else {
                            // fila por puerto, sin expecificar origen ni destino manualmente
                            if($requestobj[$statustypecurren] == 2){
                                $currencyVal = $currencyValtwen;
                            }

                            /*$ratesArre =  RateLcl::create([
                            'origin_port'    => $originVal,
                            'destiny_port'   => $destinyVal,
                            'carrier_id'     => $carrierVal,
                            'contract_id'    => $contractIdVal,
                            'twuenty'        => $twentyVal,
                            'forty'          => $fortyVal,
                            'fortyhc'        => $fortyhcVal,
                            'fortynor'       => $fortynorVal,
                            'fortyfive'      => $fortyfiveVal,
                            'currency_id'    => $currencyVal
                        ]);

                            //dd($ratesArre);
                        }
                    } else {
                        // aqui van los fallidos
                    }*/
                    }
                    $i =$i + 1;
                }
            });
    }


    public function store(Request $request)
    {

    }

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request)
    {

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
            return redirect()->route('ImportationLCL.index');
        }
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        $validatefile = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

        if($validatefile){
            $contract     = new ContractLcl();
            $contract->name             = $request->name;
            $contract->number           = $request->number;
            $validity                   = explode('/',$request->validation_expire);
            $contract->validity         = $validity[0];
            $contract->expire           = $validity[1];
            $contract->status           = 'incomplete';
            $contract->comments         = $request->comments;
            $contract->company_user_id  = $CompanyUserId;
            $contract->save(); 
            $Contract_id = $contract->id;
            /* $fileTmp = new FileTmp();
            $fileTmp->contract_id = $Contract_id;
            $fileTmp->name_file   = $nombre;
            $fileTmp->save(); //*/
        }

        $statustypecurren = $request->valuesCurrency;
        $targetsArr =[ 0 => "W/M", 1 => "Minimun"];

        // si type es igual a  1, el proceso va por rates, si es 2 va por rate mas surchargers

        if($type == 2){
            array_push($targetsArr,"Calculation Type","Charge");
        }

        // DatOri - DatDes - DatCar, hacen referencia a si fue marcado el checkbox



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
            'fileName'        => $nombre,
            'validatiion'     => $request->validation_expire,
            'comments'        => $request->comments,
        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);

        return view('ImportationLcl.show',compact('harbor','carrier','coordenates','targetsArr','data','countTarges','type','statustypecurren','CompanyUserId'));
        /*}catch(\Exception $e){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error with the archive');
            return redirect()->route('importaion.fcl');
        }//*/

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
