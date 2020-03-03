<?php

namespace App\Jobs;

use Excel;
use App\User;
use PrvHarbor;
use App\Region;
use App\Harbor;
use PrvCarrier;
use App\Country;
use App\Carrier;
use App\Currency;
use App\Surcharge;
use Carbon\Carbon;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalChargeLcl;
use App\GlobalCharPortLcl;
use App\CalculationTypeLcl;
use App\GlobalCharCarrierLcl;
use App\GlobalCharCountryLcl;
use App\FailedGlobalchargerLcl;
use App\Notifications\N_general;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;
use App\AccountImportationGlobalChargerLcl;
use App\NewRequestGlobalChargerLcl as RequestGCLCL;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportationGlobalchargerLclJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $request,$companyUserId,$UserId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request,$companyUserId,$UserId)
    {
        $this->request          = $request;
        $this->companyUserId    = $companyUserId;
        $this->UserId           = $UserId;
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
        $errors     = 0;
        $NameFile   = $requestobj['FileName'];
        $path       = Storage::disk('GCImportLcl')->url($NameFile);
        
        Excel::selectSheetsByIndex(0)
            ->Load($path,function($reader) use($requestobj,$errors,$NameFile,$companyUserIdVal) {
                $reader->noHeading = true;

                $minimun                = "Minimun";
                $amount                 = "Amount";                
                $origin                 = "origin";
                $originExc              = "Origin";
                $destiny                = "destiny";
                $destinyExc             = "Destiny";
                $currency               = "Currency";
                $originCountry          = "originCount";//arreglo de multiples country
                $originRegion           = "originRegion";//arreglo de multiples Region
                $destinycountry         = "destinyCount";//arreglo de multiples country
                $destinyRegion          = "destinyRegion";//arreglo de multiples Region
                $carrier                = "Carrier";
                $CalculationType        = "Calculation_Type";
                $Charge                 = "Charge";
                $statustypecurren       = "statustypecurren";
                $typedestiny            = "Type_Destiny";
                $validityfrom           = "Validity_From";
                $validityto             = "Validity_To";
                $differentiator         = "Differentiator";

                $statusPortCountryTW        = $requestobj['statusPortCountry'];
                $account_id                 = $requestobj['account_id'];
                $statusexistdatevalidity    = $requestobj['existdatevalidity'];
                $statusPortCountry          = $requestobj['statusPortCountry'];

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':','1','2','3','4','5','6','7','8','9','0'];

                $globalChargerCollection    = collect([]);
                $globalChargerFailCollection   = collect([]);


                $i = 1;
                $falli =0;
                foreach($reader->get() as $read){

                    //--------------------------------------------------------
                    if($i != 1){
                        $differentiatorVal = '';
                        if($statusPortCountryTW == 2){
                            $differentiatorVal = $read[$requestobj[$differentiator]];
                        } else {
                            $differentiatorVal = 'port';
                        }
                        //--------------- CARGADOR DE ARREGLO ORIGEN DESTINO MULTIPLES ----------------------------
                        //--- ORIGIN ------------------------------------------------------
                        $oricount = 0;
                        if($requestobj['existorigin'] == true){
                            $originMultps = [0];
                        } else {
                            $originMultps = explode('|',$read[$requestobj[$originExc]]);
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
                        }
                        //--- DESTINY -----------------------------------------------------
                        $descount = 0;
                        if($requestobj['existdestiny'] == true){
                            $destinyMultps = [0];
                        } else {
                            $destinyMultps = explode('|',$read[$requestobj[$destinyExc]]);
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
                        }

                        //dd($originMultps);
                        //dd($destinyMultps);

                        foreach($originMultps as $originMult){
                            foreach($destinyMultps as $destinyMult){

                                $carrierVal                 = '';
                                $typedestinyVal             = '';
                                $originVal                  = '';
                                $destinyVal                 = '';
                                $origenFL                   = '';
                                $destinyFL                  = '';
                                $currencyVal                = '';
                                $currencyReadVal            = '';
                                $currencyReadVal            = '';
                                $minimunVal                 = null;
                                $amountVal                  = null;

                                $currencyValAmount          = '';
                                $currencyValMinimun         = '';

                                $calculationtypeVal         = '';
                                $surchargelist              = '';
                                $surchargeVal               = '';
                                $validityfromVal            = '';
                                $validitytoVal		        = '';
                                $differentiatorVal          = 1;
                                $account_idVal              = $account_id;

                                $calculationtypeValfail     = '';
                                $currencResultwen           = '';
                                $currencResulfor            = '';

                                $currencResul               = '';

                                $minimunArr                 = [];
                                $amountArr                  = [];


                                $originBol               = false;
                                $origExiBol              = false;
                                $destinyBol              = false;
                                $destiExitBol            = false;
                                $typedestinyExitBol      = false;
                                $typedestinyBol          = false;
                                $carriExitBol            = false;
                                $curreExiBol             = false;
                                $curreExitBol            = false;
                                $curreExiMinimunBol      = false;
                                $curreExiAmountBol       = false;

                                $minimunExiBol           = false;
                                $amountExiBol            = false;

                                $carriBol                = false;
                                $calculationtypeExiBol   = false;
                                $variantecurrency        = false;
                                $typeExiBol              = false;
                                $minimunArrBol           = false;
                                $amountArrBol            = false;
                                $validityfromExiBol		 = false;
                                $validitytoExiBol		 = false;
                                $differentiatorBol       = false;
                                $values                  = true;

                                if($requestobj[$statustypecurren] == 1){
                                    $currencyReadVal        = $read[$requestobj[$currency]];
                                } 

                                if($requestobj['existorigin'] != 1 && $requestobj['existdestiny'] != 1){
                                    $randons    = [];
                                }

                                $minimunReadVal             = $read[$requestobj[$minimun]];
                                $minimunVal                 = $minimunReadVal;
                                $amountReadVal              = $read[$requestobj[$amount]];
                                $amountVal                  = $amountReadVal;
                                $calculationvalvaration     = $read[$requestobj[$CalculationType]];
                                $chargerValRead             = $read[$requestobj[$Charge]];


                                if($statusexistdatevalidity == 1){
                                    $dateArr = explode('/',$requestobj['validitydate']);
                                    $validityfromVal    = trim($dateArr[0]);
                                    $validitytoVal      = trim($dateArr[1]);
                                } else{
                                    $validityfromVal = $read[$requestobj[$validityfrom]];
                                    $validitytoVal = $read[$requestobj[$validityto]];
                                }

                                //--------------- DIFRENCIADOR HARBOR COUNTRY -----------------------------------------

                                if($statusPortCountry == 2){
                                    $differentiatorVal = $read[$requestobj[$differentiator]];// hacer validacion de puerto o country
                                    $differentiatorValTw = $read[$requestobj[$differentiator]];// hacer validacion de puerto o country
                                    if(strnatcasecmp($differentiatorVal,'country') == 0 || strnatcasecmp($differentiatorVal,'region') == 0){
                                        $differentiatorBol = true;
                                        $differentiatorVal = 2;
                                    } else {
                                        $differentiatorVal = 1;
                                    }
                                }

                                //--------------- ORIGEN MULTIPLE O SIMPLE --------------------------------------------

                                if($requestobj['existorigin'] == 1){
                                    $originBol = true;
                                    $origExiBol = true; //segundo boolean para verificar campos errados
                                    if($differentiatorBol == false){
                                        $randons = $requestobj[$origin];
                                    } else if($differentiatorBol == true){
                                        if(strnatcasecmp($differentiatorValTw,'country') == 0){
                                            $randons = $requestobj[$originCountry];
                                        } else{
                                            $randons = [];
                                            foreach($requestobj[$originRegion] as $randosoriR){
                                                $regionsORIrans = Region::with('CountriesRegions.country')->find($randosoriR);
                                                foreach($regionsORIrans->CountriesRegions->pluck('country')->pluck('id')->toArray() as $regionsORIran){
                                                    array_push($randons,$regionsORIran);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //$originVal = $read[$requestobj[$originExc]];// hacer validacion de puerto en DB
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

                                }
                                //dd($originVal);
                                //---------------- DESTINO MULTIPLE O SIMPLE ------------------------------------------

                                if($requestobj['existdestiny'] == 1){
                                    $destinyBol = true;
                                    $destiExitBol = true; //segundo boolean para verificar campos errados
                                    if($differentiatorBol == false){
                                        $randons = $requestobj[$destiny];
                                    } else if($differentiatorBol == true){
                                        if(strnatcasecmp($differentiatorValTw,'country') == 0){
                                            $randons = $requestobj[$destinycountry];
                                        } else{
                                            $randons = $requestobj[$destinyRegion];

                                            $randons = [];
                                            foreach($requestobj[$destinyRegion] as $randosdesR){
                                                $regionsDEsrans = Region::with('CountriesRegions.country')->find($randosdesR);
                                                foreach($regionsDEsrans->CountriesRegions->pluck('country')->pluck('id')->toArray() as $regionsDESran){
                                                    array_push($randons,$regionsDESran);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //$destinyVal = $read[$requestobj[$destinyExc]];// hacer validacion de puerto en DB
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
                                }

                                //dd($destinyVal);
                                //dd($randons);
                                //------------------ VALIDITY FROM ----------------------------------------------------

                                try{
                                    $validityfromVal = Carbon::parse($validityfromVal)->format('Y-m-d');
                                    $validityfromExiBol = true;
                                } catch (\Exception $err){
                                    $validityfromVal = $validityfromVal.'_E_E';
                                }

                                //------------------ VALIDITY TO ------------------------------------------------------

                                try{
                                    $validitytoVal = Carbon::parse($validitytoVal)->format('Y-m-d');
                                    $validitytoExiBol = true;
                                } catch (\Exception $err){
                                    $validitytoVal = $validitytoVal.'_E_E';
                                }

                                //--------------- Type Destiny --------------------------------------------------------

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

                                //--------------- CARRIER -------------------------------------------------------------

                                if($requestobj['existcarrier'] == 1){
                                    $carriExitBol = true;
                                    $carriBol     = true;
                                    $carrierVal = $requestobj['carrier']; // cuando se indica que no posee carrier 
                                } else {
                                    $carrierVal      = $read[$requestobj['Carrier']]; // cuando el carrier existe en el excel
                                    $carrierArr      = PrvCarrier::get_carrier($carrierVal);
                                    $carriExitBol    = $carrierArr['boolean'];
                                    $carrierVal      = $carrierArr['carrier'];
                                }

                                //---------------- CURRENCY VALUES ----------------------------------------------------

                                if(empty($minimunVal) != true){ //Primero valido si el campo viene lleno, en caso contrario lo lleno manuelamene
                                    $minimunArrBol  = true;
                                    $minimunArr     = explode(' ',trim($minimunVal));
                                } else {
                                    $minimunArr     = ['0.0']; 
                                }

                                if(empty($amountVal) != true){
                                    $amountArrBol   = true;
                                    $amountArr      = explode(' ',trim($amountVal));
                                } else {
                                    $amountArr      = ['0.0'];
                                }

                                // ----------------------- Validacion de comapos vacios--------------------------------

                                if($requestobj[$statustypecurren] == 2){ // se verifica si el valor viene junto con el currency para no llenar el valor del currency arreglo[posicion 2] 
                                    // ------- AMOUNT'
                                    if($amountArrBol == false){ // Cargamos el arreglo[1] para que se pueda registrar
                                        if($minimunArrBol == true){
                                            array_push($amountArr,$minimunArr[1]);
                                        } else {
                                            array_push($amountArr,'');
                                        }
                                    }

                                    // ------- MINIUMUN -----------------------------------------------------------Min
                                    if($minimunArrBol == false){ // Cargamos el arreglo[1] para que el Rate se pueda registrar, y para que se validen los PER_DOC
                                        if($amountArrBol == true){
                                            array_push($minimunArr,$amountArr[1]);
                                        } else {
                                            array_push($minimunArr,'');
                                        }
                                    }
                                }

                                //---------------- AMOUNT -------------------------------------------------------------

                                if(empty($amountArr[0]) != true || floatval($amountArr[0]) == 0.00){
                                    $amountExiBol = true;
                                    $amountVal  = floatval($amountArr[0]);
                                }  else{
                                    $amountVal  = $amountArr[0].'_E_E';
                                }

                                //----------------- MINIMUN -----------------------------------------------------------

                                if(empty($minimunArr[0]) != true || floatval($minimunArr[0]) == 0.00){
                                    $minimunExiBol = true;
                                    $minimunVal   = floatval($minimunArr[0]);
                                }  else{
                                    $minimunVal = $minimunArr[0].'_E_E';
                                }                         

                                if($amountVal == 0.00
                                   && $minimunVal == 0.00){
                                    $values = false;
                                }

                                //---------------- CURRENCY -----------------------------------------------------------

                                if($requestobj[$statustypecurren] == 2){ // se verifica si el valor viene junto con el currency

                                    // cargar  columna con el  valor y currency  juntos, se descompone

                                    //---------------- CURRENCY AMUONT + VALUE ----------------------------------------


                                    if(count($amountArr) > 1){
                                        $currencyValAmount = str_replace($caracteres,'',$amountArr[1]);
                                    } else {
                                        $currencyValAmount = '_E_E';
                                    }

                                    $currencAmount = Currency::where('alphacode','=',$currencyValAmount)->first();

                                    if(empty($currencAmount->id) != true){
                                        $curreExiAmountBol = true;
                                        $currencyValAmount =  $currencAmount->id;
                                    } else{
                                        if(count($amountArr) > 1){
                                            $currencyValAmount = $amountArr[1].'_E_E';
                                        } else{
                                            $currencyValAmount = '_E_E';
                                        }
                                    }

                                    //---------------- CURRENCY MINUMUN + VALUE ---------------------------------------

                                    if(count($minimunArr) > 1){
                                        $currencResulMin = str_replace($caracteres,'',$minimunArr[1]);
                                    } else{
                                        $currencResulMin = '_E_E';
                                    }

                                    $currencMinimun = Currency::where('alphacode','=',$currencResulMin)->first();

                                    if(empty($currencMinimun->id) != true){
                                        $curreExiMinimunBol = true;
                                        $currencyValMinimun =  $currencMinimun->id;
                                    } else{
                                        if(count($minimunArr) > 1){
                                            $currencyValMinimun = $minimunArr[1].'_E_E';
                                        } else {
                                            $currencyValMinimun = '_E_E';
                                        }
                                    }

                                    if($curreExiAmountBol == false && $curreExiMinimunBol == true){
                                        $curreExiAmountBol = true;
                                        $currencyValAmount = $currencyValMinimun;
                                    } elseif($curreExiAmountBol == true && $curreExiMinimunBol == false){
                                        $curreExiMinimunBol = true;
                                        $currencyValMinimun = $currencyValAmount;
                                    }

                                    if($curreExiAmountBol){
                                        $currencyVal = $currencyValAmount;
                                    } else {
                                        $currencyVal = $currencyValMinimun;
                                    }

                                    if($curreExiAmountBol == true && $curreExiMinimunBol == true){
                                        $variantecurrency = true;
                                    }

                                } else {

                                    if(empty($currencyReadVal) != true){
                                        $currencResul= str_replace($caracteres,'',$currencyReadVal);
                                        $currenc = Currency::where('alphacode','=',$currencResul)->first();
                                        if(empty($currenc->id) != true){    
                                            $curreExitBol = true;
                                            $currencyVal =  $currenc->id;
                                        } else{
                                            $currencyVal = $currencyReadVal.'_E_E';                                    
                                        }
                                    }
                                    else{
                                        $currencyVal = $currencyReadVal.'_E_E';
                                    }

                                    if($curreExitBol == true ){
                                        $variantecurrency = true;
                                    }
                                }

                                //------------------ CALCULATION TYPE -------------------------------------------------

                                $calculationtype = CalculationTypeLcl::where('code','=',$calculationvalvaration)->orWhere('name','=',$calculationvalvaration)->first();
                                if(empty($calculationtype) != true){
                                    $calculationtypeExiBol  = true;
                                    $calculationtypeVal     = $calculationtype['id'];
                                } else{
                                    $calculationtypeVal     = $calculationvalvaration.' ROW Excel '.$i.' _E_E';
                                }

                                //------------------ TYPE -------------------------------------------------------------

                                if(empty($chargerValRead) != true){
                                    $typeExiBol = true;

                                    $surchargelist = Surcharge::where('name','=',$chargerValRead)
                                        ->where('company_user_id','=', $companyUserIdVal)
                                        ->first();
                                    if(empty($surchargelist) != true){
                                        $surchargeVal = $surchargelist['id'];
                                    } 	else{
                                        $surchargelist = Surcharge::create([
                                            'name'              => $chargerValRead,
                                            'description'       => $chargerValRead,
                                            'company_user_id'   => $companyUserIdVal
                                        ]);
                                        $surchargeVal = $surchargelist->id;
                                    }

                                } else {
                                    $surchargeVal = $chargerValRead.'_E_E';
                                }

                                //////////////////////////////////////////////////////////////////////////////////////////////////////

                                $prueba = collect([]);

                                $prueba = [
                                    '$differentiatorBol'         => $differentiatorBol,
                                    '$originBol'                 => $originBol,
                                    'origExiBol'                 => $origExiBol,
                                    '$destinyBol'                => $destinyBol,
                                    '$destiExitBol'              => $destiExitBol,
                                    '$typedestinyExitBol'        => $typedestinyExitBol,
                                    '$typedestinyBol'            => $typedestinyBol,
                                    '$carriExitBol'              => $carriExitBol,
                                    '$carriBol'                  => $carriBol,
                                    '$minimunArrBol'             => $minimunArrBol,
                                    '$minimunExiBol'             => $minimunExiBol,
                                    '$amountExiBol'              => $amountExiBol,
                                    '$amountArrBol'              => $amountArrBol,
                                    '$values'                    => $values,
                                    '$curreExiAmountBol'         => $curreExiAmountBol,
                                    '$curreExiMinimunBol'        => $curreExiMinimunBol,
                                    '$variantecurrency'          => $variantecurrency,
                                    '$calculationtypeExiBol'     => $calculationtypeExiBol,
                                    '$typeExiBol'                => $typeExiBol,
                                    '$validityfromVal'           => $validityfromVal,
                                    '$validitytoVal'             => $validitytoVal,
                                    '$differentiatorVal'         => $differentiatorVal,
                                    '$originVal'                 => $originVal,
                                    '$destinyVal'                => $destinyVal,                 
                                    '$typedestinyVal'            => $typedestinyVal,
                                    '$carrierVal'                => $carrierVal,
                                    '$minimunVal'                => $minimunVal,
                                    '$amountVal'                 => $amountVal,
                                    '$currencyValAmount'         => $currencyValAmount,
                                    '$currencyValMinimun'        => $currencyValMinimun,
                                    '$currencyVal'               => $currencyVal,
                                    '$calculationtypeVal'        => $calculationtypeVal,
                                    '$surchargeVal'              => $surchargeVal,
                                    '$companyUserIdVal'          => $companyUserIdVal,
                                    '$minimunArr'                => $minimunArr,
                                    '$amountArr'                 => $amountArr,
                                    '$randons'                   => $randons,
                                    '$statusexistdatevalidity'   => $statusexistdatevalidity,
                                    '$calculationvalvaration'    => $calculationvalvaration,
                                    '$calculationtype'           => $calculationtype,
                                    '$chargerValRead'            => $chargerValRead
                                ];

                                //dd($prueba);

                                if($carriExitBol            	== true
                                   && $origExiBol           	== true
                                   && $destiExitBol         	== true
                                   && $amountExiBol         	== true
                                   && $minimunExiBol          	== true
                                   && $calculationtypeExiBol 	== true
                                   && $variantecurrency     	== true
                                   && $typeExiBol           	== true
                                   && $typedestinyExitBol   	== true
                                   && $validityfromExiBol       == true
                                   && $validitytoExiBol         == true
                                   && $values 					== true ){

                                    if($differentiatorBol == false){ //si es puerto verificamos si exite uno creado con puerto
                                        $typeplace = 'globalcharportlcl';
                                    }else {  //si es country verificamos si exite uno creado con country
                                        $typeplace = 'globalcharcountrylcl';
                                    }

                                    // evaluamos si viene el valor con el currency juntos

                                    if($requestobj[$statustypecurren] == 2){
                                        if($curreExiAmountBol){
                                            $currencyVal = $currencyValAmount;
                                        }elseif($curreExiMinimunBol) {
                                            $currencyVal = $currencyValMinimun;
                                        }
                                    }

                                    if($amountVal != 0 || $amountVal != 0.0){
                                        $globalChargeArreG = null;
                                        $globalChargeArreG = GlobalChargeLcl::where('surcharge_id',$surchargeVal)
                                            ->where('typedestiny_id',$typedestinyVal)
                                            ->where('company_user_id',$companyUserIdVal)
                                            ->where('calculationtypelcl_id',$calculationtypeVal)
                                            ->where('ammount',$amountVal)
                                            ->where('minimum',$minimunVal)
                                            ->where('validity',$validityfromVal)
                                            ->where('expire',$validitytoVal)
                                            ->where('currency_id',$currencyVal)
                                            ->has($typeplace)
                                            ->first();

                                        if(count($globalChargeArreG) == 0){
                                            $globalChargeArreG = GlobalChargeLcl::create([ // tabla GlobalCharge
                                                'surcharge_id'       						=> $surchargeVal,
                                                'typedestiny_id'     						=> $typedestinyVal,
                                                'account_imp_gclcl_id'                      => $account_idVal,
                                                'company_user_id'    						=> $companyUserIdVal,
                                                'calculationtypelcl_id' 					=> $calculationtypeVal,
                                                'ammount'            						=> $amountVal,
                                                'minimum'            						=> $minimunVal,
                                                'validity' 									=> $validityfromVal,
                                                'expire'					 				=> $validitytoVal,
                                                'currency_id'        						=> $currencyVal
                                            ]);   
                                        }
                                        //---------------------------------- VALIDATE G.C. CARRIER -------------------------------------------

                                        $exitGCCPC = null;
                                        $exitGCCPC = GlobalCharCarrierLcl::where('carrier_id',$carrierVal)->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                        if(count($exitGCCPC) == 0){
                                            GlobalCharCarrierLcl::create([ // tabla GlobalCharCarrier
                                                'carrier_id'            => $carrierVal,
                                                'globalchargelcl_id'    => $globalChargeArreG->id
                                            ]);
                                        }
                                        //----------------------------------- ORIGIN DESTINATION ---------------------------------------------

                                        if($originBol == true || $destinyBol == true){
                                            foreach($randons as  $rando){
                                                //insert por arreglo de puerto
                                                if($originBol == true ){
                                                    $originVal = $rando;
                                                } else {
                                                    $destinyVal = $rando;
                                                }

                                                //---------------------------------- CAMBIAR POR ID -------------------------------

                                                if($differentiatorBol == false){
                                                    $exgcpt = null;
                                                    $exgcpt = GlobalCharPortLcl::where('port_orig',$originVal)->where('port_dest',$destinyVal)
                                                        ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                    if(count($exgcpt) == 0){
                                                        GlobalCharPortLcl::create([ // tabla GlobalCharPort
                                                            'port_orig'      	=> $originVal,
                                                            'port_dest'      	=> $destinyVal,
                                                            'globalchargelcl_id'   => $globalChargeArreG->id
                                                        ]);
                                                    }
                                                } else {
                                                    $exgcct = null;
                                                    $exgcct = GlobalCharCountryLcl::where('country_orig',$originVal)
                                                        ->where('country_dest',$destinyVal)
                                                        ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                    if(count($exgcct) == 0){
                                                        GlobalCharCountryLcl::create([ // tabla GlobalCharCountry harbor
                                                            'country_orig'          => $originVal,
                                                            'country_dest'          => $destinyVal,
                                                            'globalchargelcl_id'    => $globalChargeArreG->id
                                                        ]);
                                                    }
                                                }

                                                //---------------------------------------------------------------------------------

                                            } 
                                        }else {
                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                            if($differentiatorBol == false){
                                                $exgcpt = null;
                                                $exgcpt = GlobalCharPortLcl::where('port_orig',$originVal)->where('port_dest',$destinyVal)
                                                    ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                if(count($exgcpt) == 0){
                                                    GlobalCharPortLcl::create([ // tabla GlobalCharPort
                                                        'port_orig'      	=> $originVal,
                                                        'port_dest'      	=> $destinyVal,
                                                        'globalchargelcl_id'   => $globalChargeArreG->id
                                                    ]);
                                                }
                                            } else {
                                                $exgcct = null;
                                                $exgcct = GlobalCharCountryLcl::where('country_orig',$originVal)
                                                    ->where('country_dest',$destinyVal)
                                                    ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                if(count($exgcct) == 0){
                                                    GlobalCharCountryLcl::create([ // tabla GlobalCharCountry harbor
                                                        'country_orig'      => $originVal,
                                                        'country_dest'      => $destinyVal,
                                                        'globalchargelcl_id'   => $globalChargeArreG->id
                                                    ]);
                                                }
                                            }
                                        }
                                        //echo $i;
                                        //dd($globalChargeArreG);
                                    }

                                    //dd('primer registro');

                                } else {
                                    //dd($prueba);
                                    // van los fallidos
                                    //---------------------------- TYPE DESTINY  ----------------------------------------------------

                                    if($typedestinyExitBol == true){
                                        //if($typedestinyBol == true){
                                            $typedestinyobj = TypeDestiny::find($typedestinyVal);
                                            $typedestinyVal = $typedestinyobj->description;
                                        //}
                                    }

                                    //---------------------------- CARRIER  ---------------------------------------------------------

                                    if($carriExitBol == true){
                                        if($carriBol == true){
                                            $carrier = Carrier::find($carrierVal); 
                                            $carrierVal = $carrier['name'];  
                                        }else{
                                            /*$carriExitBol2   = false;
                                            $carrierArr      = PrvCarrier::get_carrier($read[$requestobj['Carrier']]);
                                            $carrierVal      = $carrierArr['carrier'];
                                            $carriExitBol2   = $carrierArr['boolean'];
                                            if($carriExitBol2 == true){
                                                $carrierVal = Carrier::find($carrierVal);
                                                $carrierVal = $carrierVal->name;
                                            }*/
                                            $carrierVal = Carrier::find($carrierVal);
                                            $carrierVal = $carrierVal->name;
                                        }
                                    }

                                    //---------------------------- VALUES CURRENCY ---------------------------------------------------

                                    if($curreExiBol == true){
                                        $currencyVal = $read[$requestobj[$currency]];
                                    }

                                    if( $amountExiBol == true){
                                        if(empty($amountVal) == true){
                                            $amountVal = '0';
                                        } 
                                    }

                                    if( $minimunExiBol == true){
                                        if(empty($minimunVal) == true){
                                            $minimunVal = '0';
                                        }
                                    }

                                    if( $variantecurrency == true){
                                        $currencyobj = Currency::find($currencyVal);
                                        $currencyVal = $currencyobj['alphacode'];
                                    } 

                                    //---------------------------- CALCULATION TYPE -------------------------------------------------

                                    if($calculationtypeExiBol == true){
                                        $calculationType = CalculationTypeLcl::find($calculationtypeVal);
                                        $calculationtypeVal = $calculationType['name'];
                                    } 

                                    //---------------------------- TYPE -------------------------------------------------------------

                                    if($typeExiBol == true){
                                        $Surchargeobj = Surcharge::find($surchargeVal);
                                        $surchargeVal = $Surchargeobj['name'];
                                    }

                                    //////////////////////////////////////////////////////////////////////////////////////////////
                                    // Globalchargers Fallidos
                                    // multiples puertos o por seleccion
                                    if($originBol == true || $destinyBol == true){
                                        foreach($randons as  $rando){
                                            //insert por arreglo de puerto
                                            if($originBol == true ){
                                                if($differentiatorBol){
                                                    $originerr = Country::find($rando);
                                                } else {
                                                    $originerr = Harbor::find($rando);
                                                }
                                                $originVal = $originerr['name'];
                                                if($destiExitBol == true){    
                                                    $destinyVal = $read[$requestobj[$destinyExc]];
                                                }
                                            } else {
                                                if($differentiatorBol){
                                                    $destinyerr = Country::find($rando);
                                                } else {
                                                    $destinyerr = Harbor::find($rando);
                                                }
                                                $destinyVal = $destinyerr['name'];
                                                if($origExiBol == true){
                                                    $originVal = $read[$requestobj[$originExc]];                                      
                                                }
                                            }

                                            if($amountVal != 0 || $amountVal != 0.00){
                                                $extgc = null;
                                                $extgc = FailedGlobalchargerLcl::where('surcharge',$surchargeVal)
                                                    ->where('origin',$originVal)
                                                    ->where('destiny',$destinyVal)
                                                    ->where('typedestiny',$typedestinyVal)
                                                    ->where('calculationtypelcl',$calculationtypeVal)
                                                    ->where('ammount',$amountVal)
                                                    ->where('minimum',$minimunVal)
                                                    ->where('currency',$currencyVal)
                                                    ->where('carrier',$carrierVal)
                                                    ->where('expire',$validitytoVal)
                                                    ->where('validity',$validityfromVal)
                                                    ->where('port',true)
                                                    ->where('country',false)
                                                    ->where('company_user_id',$companyUserIdVal)
                                                    ->where('differentiator',$differentiatorVal)
                                                    ->get();

                                                if(count($extgc) == 0){
                                                    FailedGlobalchargerLcl::create([
                                                        'surcharge'             => $surchargeVal,
                                                        'origin'          	    => $originVal,
                                                        'destiny'          	    => $destinyVal,
                                                        'typedestiny'     	    => $typedestinyVal,
                                                        'calculationtypelcl'    => $calculationtypeVal,  //////
                                                        'ammount'               => $amountVal, //////
                                                        'minimum'               => $minimunVal, //////
                                                        'currency'		        => $currencyVal, //////
                                                        'carrier'	            => $carrierVal,
                                                        'expire'	            => $validitytoVal,
                                                        'validity'              => $validityfromVal,
                                                        'port'        		    => true,// por defecto
                                                        'country'        	    => false,// por defecto
                                                        'company_user_id'       => $companyUserIdVal,
                                                        'account_imp_gclcl_id'  => $account_idVal,
                                                        'differentiator'        => $differentiatorVal
                                                    ]);
                                                    //$ratescollection->push($ree);                    
                                                }
                                            }

                                        }
                                    } else {
                                        // puertos leidos del excel
                                        if($origExiBol == true){
                                            if($differentiatorBol == true){
                                                $originExits = Country::find($originVal);
                                                $originVal = $originExits['name'];     
                                            } else {
                                                $originExits = Harbor::find($originVal);
                                                $originVal = $originExits->name;                                       
                                            }
                                        }
                                        if($destiExitBol == true){ 
                                            if($differentiatorBol == true){
                                                $destinyExits = Country::find($destinyVal);
                                                $destinyVal = $destinyExits['name'];
                                            } else {
                                                $destinyExits = Harbor::find($destinyVal);
                                                $destinyVal = $destinyExits->name;
                                            }
                                        }


                                        if($amountVal != 0 || $amountVal != 0.00){
                                            $extgc = null;
                                            $extgc = FailedGlobalchargerLcl::where('surcharge',$surchargeVal)
                                                ->where('origin',$originVal)
                                                ->where('destiny',$destinyVal)
                                                ->where('typedestiny',$typedestinyVal)
                                                ->where('calculationtypelcl',$calculationtypeVal)
                                                ->where('ammount',$amountVal)
                                                ->where('minimum',$minimunVal)
                                                ->where('currency',$currencyVal)
                                                ->where('carrier',$carrierVal)
                                                ->where('expire',$validitytoVal)
                                                ->where('validity',$validityfromVal)
                                                ->where('port',true)
                                                ->where('country',false)
                                                ->where('company_user_id',$companyUserIdVal)
                                                ->where('differentiator',$differentiatorVal)
                                                ->get();

                                            if(count($extgc) == 0){
                                                FailedGlobalchargerLcl::create([
                                                    'surcharge'             => $surchargeVal,
                                                    'origin'          	    => $originVal,
                                                    'destiny'          	    => $destinyVal,
                                                    'typedestiny'     	    => $typedestinyVal,
                                                    'calculationtypelcl'	=> $calculationtypeVal,  //////
                                                    'ammount'               => $amountVal, //////
                                                    'minimum'               => $minimunVal, //////
                                                    'currency'		        => $currencyVal, //////
                                                    'carrier'	            => $carrierVal,
                                                    'expire'	            => $validitytoVal,
                                                    'validity'              => $validityfromVal,
                                                    'port'        		    => true,// por defecto
                                                    'country'        	    => false,// por defecto
                                                    'company_user_id'       => $companyUserIdVal,
                                                    'account_imp_gclcl_id'  => $account_idVal,
                                                    'differentiator'        => $differentiatorVal
                                                ]);
                                                //  $ratescollection->push($ree);
                                            }
                                        }
                                    }

                                    $falli++;
                                    //echo $i;
                                    //dd($ratescollection);

                                }
                            }
                        }
                    }
                    //-------------------------- fin distinto del primer ciclo
                    $i++;
                }
            });
        
        $nopalicaHs = Harbor::where('name','No Aplica')->get();
        $nopalicaCs = Country::where('name','No Aplica')->get();
        foreach($nopalicaHs as $nopalicaH){
            $nopalicaH = $nopalicaH['id'];
        }
        foreach($nopalicaCs as $nopalicaC){
            $nopalicaC = $nopalicaC['id'];
        }

        FailedGlobalchargerLcl::where('account_imp_gclcl_id','=',$requestobj['account_id'])->where('origin','LIKE','%No Aplica%')->delete();
        FailedGlobalchargerLcl::where('account_imp_gclcl_id','=',$requestobj['account_id'])->where('destiny','LIKE','%No Aplica%')->delete();

        GlobalChargeLcl::where('account_imp_gclcl_id',$requestobj['account_id'])
            ->whereHas('globalcharportlcl',function($query) use($nopalicaH){
                $query->where('port_dest',$nopalicaH)->orWhere('port_orig',$nopalicaH);
            })
            ->orWhereHas('globalcharcountrylcl',function($query) use($nopalicaC){
                $query->where('country_dest',$nopalicaC)->orWhere('country_orig',$nopalicaC);
            })->Delete();
        
        $account = AccountImportationGlobalChargerLcl::find($requestobj['account_id']);
        $account->status = 'complete';
        $account->update();

        Storage::disk('GCImportLcl')->Delete($NameFile);

        $userNotifique = User::find($this->UserId);
        $message = 'The file GCLCL imported was processed :' . $requestobj['account_id'];
        $userNotifique->notify(new SlackNotification($message));
        $userNotifique->notify(new N_general($userNotifique,$message)); 
    }
}
