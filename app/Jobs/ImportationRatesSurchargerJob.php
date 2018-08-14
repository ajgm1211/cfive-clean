<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Company;
use App\ContractUserRestriction;
use App\ContractCompanyRestriction;
use Illuminate\Http\Request;
use App\Contract;
use App\Contact;
use App\Country;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\FailRate;
use App\Currency;
use App\CalculationType;
use App\LocalCharge;
use App\Surcharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\User;
use App\TypeDestiny;
use App\FailSurCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Excel;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UploadFileRateRequest;
use App\FileTmp;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class ImportationRatesSurchargerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request,$companyUserId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request,$companyUserId)
    {
        $this->request = $request;
        $this->companyUserId = $companyUserId;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requestobj = $this->request;
        $companyUserIdVal = $this->companyUserId;
        $errors = 0;
        $NameFile = $requestobj['FileName'];
        $path = public_path(\Storage::disk('UpLoadFile')->url($NameFile));
        //dd($path);
        $path = public_path(\Storage::disk('UpLoadFile')->url($NameFile));
        //dd($path);
        Excel::selectSheetsByIndex(0)
            ->Load($path,function($reader) use($requestobj,$errors,$NameFile,$companyUserIdVal) {
                $reader->noHeading = true;
                //$reader->ignoreEmpty();

                $currency            = "Currency";
                $twenty              = "20'";
                $forty               = "40'";
                $fortyhc             = "40'HC";
                $origin              = "origin";
                $originExc           = "Origin";
                $destiny             = "destiny";
                $destinyExc          = "Destiny";
                $carrier             = "Carrier";
                $CalculationType     = "Calculation_Type";
                $Charge              = "Charge";
                $statustypecurren    = "statustypecurren";
                $contractId          = "Contract_id";
                $chargeVal           = $requestobj['chargeVal'];
                $contract_id         = $requestobj['Contract_id'];

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                $ratescollection         = collect([]);
                $ratesFailcollection     = collect([]);
                $surcharcollection       = collect([]);
                $surcharFailcollection   = collect([]);


                $i = 1;
                $falli =0;
                foreach($reader->get() as $read){
                    $carrierVal          = '';
                    $originVal           = '';
                    $destinyVal          = '';
                    $origenFL            = '';
                    $destinyFL           = '';
                    $currencyVal         = '';
                    $currencyValtwen     = '';
                    $currencyValfor      = '';
                    $currencyValforHC    = '';
                    $calculationtypeVal  = '';
                    $surchargelist       = '';
                    $surchargeVal        = '';
                    $contractIdVal       = $requestobj['Contract_id'];

                    $calculationtypeValfail  = '';
                    $currencResultwen        = '';
                    $currencResulfor         = '';
                    $currencResulforhc       = '';
                    $currencResul            = '';

                    $twentyArr;
                    $fortyArr;
                    $fortyhcArr;
                    $twentyVal           = '';
                    $fortyVal            = '';
                    $fortyhcVal          = '';

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
                    $twentyExiBol            = false;
                    $fortyExiBol             = false;
                    $fortyhcExiBol           = false;
                    $carriBol                = false;
                    $calculationtypeExiBol   = false;
                    $variantecurrency        = false;
                    $typeExiBol              = false;
                    $values                  = true;

                    //--------------------------------------------------------
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
                        if($requestobj['existdestiny'] == 1){
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

                        //---------------- CURRENCY VALUES ------------------------------------------------------

                        $twentyArr  = explode(' ',$read[$requestobj[$twenty]]);
                        $fortyArr   = explode(' ',$read[$requestobj[$forty]]);
                        $fortyhcArr = explode(' ',$read[$requestobj[$fortyhc]]);

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

                        if($twentyVal == 0
                           && $fortyVal == 0
                           && $fortyhcVal == 0){
                            $values = false;
                        }

                        //---------------- CURRENCY ------------------------------------------------------------


                        if($requestobj[$statustypecurren] == 2){

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

                            if($curreExitwenBol == true && $curreExiforBol == true && $curreExiforHCBol == true){
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
                            if($read[$requestobj[$Charge]] != $chargeVal){
                                dd($companyUserIdVal);
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
                            }
                        } else {
                            $surchargeVal = $read[$requestobj[$Charge]].'_E_E';
                        }
                        //////////////////////////////////////////////////////////////////////////////////////////////////////

                        if($carriExitBol     == true
                           && $origExiBol    == true
                           && $destiExitBol  == true
                           && $twentyExiBol  == true
                           && $fortyExiBol   == true
                           && $fortyhcExiBol == true
                           && $calculationtypeExiBol == true
                           && $variantecurrency == true
                           && $typeExiBol       == true
                           && $values == true){

                            if($read[$requestobj[$Charge]] == $chargeVal){

                                // Se carga un Rate nuevo
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

                                        $ratesArre = Rate::create([
                                            'origin_port'    => $originVal,
                                            'destiny_port'   => $destinyVal,
                                            'carrier_id'     => $carrierVal,
                                            'contract_id'    => $contractIdVal,
                                            'twuenty'        => $twentyVal,
                                            'forty'          => $fortyVal,
                                            'fortyhc'        => $fortyhcVal,
                                            'currency_id'    => $currencyVal
                                        ]);
                                        //dd($ratesArre);
                                    } 
                                }else {
                                    // fila por puerto, sin expecificar origen ni destino manualmente
                                    if($requestobj[$statustypecurren] == 2){
                                        $currencyVal = $currencyValtwen;
                                    }

                                    $ratesArre =  Rate::create([
                                        'origin_port'    => $originVal,
                                        'destiny_port'   => $destinyVal,
                                        'carrier_id'     => $carrierVal,
                                        'contract_id'    => $contractIdVal,
                                        'twuenty'        => $twentyVal,
                                        'forty'          => $fortyVal,
                                        'fortyhc'        => $fortyhcVal,
                                        'currency_id'    => $currencyVal
                                    ]);

                                    //dd($ratesArre);
                                }


                            } else{
                                // se ejecuta la carga de los surcharges
                                if($read[$requestobj[$CalculationType]] == 'PER_CONTAINER'){
                                    //dd($read[$request->$twenty]);
                                    // se verifica si los valores son iguales 
                                    if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                       $read[$requestobj[$forty]] == $read[$requestobj[$fortyhc]]){

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
                                        } else{

                                            // cargar el currency ya descompuesto, un solo registro de los tres

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

                                    }else {

                                        if($requestobj[$statustypecurren] == 2){
                                            $currencyVal = $currencyValforHC;
                                        } 
                                        $ammount = $fortyhcVal;
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
                                $twentyVal = $read[$requestobj[$twenty]];
                            }
                            if( $fortyExiBol == true){
                                $fortyVal = $read[$requestobj[$forty]];
                            }
                            if( $fortyhcExiBol == true){
                                $fortyhcVal = $read[$requestobj[$fortyhc]];
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

                                } else {
                                    $currencyobj = Currency::find($currencyVal);
                                    $currencyVal = $currencyobj['alphacode'];
                                }
                            } 

                            //---------------------------- CALCULATION TYPE -------------------------------------------------

                            if($calculationtypeExiBol == true){
                                $calculationType =CalculationType::find($calculationtypeVal);
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
                                if($twentyVal == 0 && $fortyVal == 0 && $fortyhcVal == 0){

                                } else {

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
                                            'currency_id'   => $currencyVal,
                                        ]);

                                    } //*/
                                }
                                //////-------------------////////////////////////////////////-----------------------------
                            } else {
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

                                                if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                                   $read[$requestobj[$forty]] == $read[$requestobj[$fortyhc]]){

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

                                                    $calculationtypeValfail = '40HC';

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

                                            if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                               $read[$requestobj[$forty]] == $read[$requestobj[$fortyhc]]){

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

                                                $calculationtypeValfail = '40HC';

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

                                                }else {

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    } 
                                                    $ammount = $fortyhcVal;
                                                }

                                                if($twentyArr[0] != 0){
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

                                            if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                               $read[$requestobj[$forty]] == $read[$requestobj[$fortyhc]]){

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

                                        if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                           $read[$requestobj[$forty]] == $read[$requestobj[$fortyhc]]){

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
                                        }
                                    }
                                }

                            }
                            $falli++;
                            //echo $i;
                            //dd($ratescollection);

                        }
                    }
                    //-------------------------- fin distinto del primer ciclo
                    $i++;
                }
                $contractData = new Contract();
                $contractData = Contract::find($contract_id);
                $contractData->status = 'publish';
                $contractData->update();

                //dd('Todo se cargo, surcharges o rates fallidos: '.$falli);
            });
        // dd($collection);
        $contractData = new Contract();
        $contractData = Contract::find($requestobj['Contract_id']);
        $contractData->status = 'publish';
        $contractData->update();
        Storage::Delete($NameFile);
        $FileTmp = new FileTmp();
        $FileTmp = FileTmp::where('name_file','=',$NameFile)->delete();


    }
}