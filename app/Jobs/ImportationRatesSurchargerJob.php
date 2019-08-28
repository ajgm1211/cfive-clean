<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Excel;
use App\Rate;
use App\User;
use PrvHarbor;
use App\Region;
use App\Harbor;
use PrvCarrier;
use App\Company;
use App\Contact;
use App\Country;
use App\Carrier;
use App\FileTmp;
use App\Contract;
use App\FailRate;
use App\Currency;
use App\Surcharge;
use App\LocalCharge;
use App\TypeDestiny;
use App\ScheduleType;
use App\LocalCharPort;
use App\FailSurCharge;
use App\CalculationType;
use App\LocalCharCarrier;
use App\LocalCharCountry;
use Illuminate\Http\Request;
use App\ContractUserRestriction;
use App\Notifications\N_general;
use App\ContractCompanyRestriction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;
use App\Http\Requests\UploadFileRateRequest;

class ImportationRatesSurchargerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request,$companyUserId,$UserId;

    public function __construct($request,$companyUserId,$UserId)
    {
        $this->request          = $request;
        $this->companyUserId    = $companyUserId;
        $this->UserId           = $UserId;

    }

    public function handle()
    {
        $requestobj = $this->request;
        $companyUserIdVal = $this->companyUserId;
        $errors = 0;
        $NameFile = $requestobj['FileName'];
        $path = \Storage::disk('FclImport')->url($NameFile);
        //dd($path);
        //ini_set('memory_limit', '1024M');

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
                $origin                 = "origin";//arreglo de multiples puertos
                $originCountry          = "originCount";//arreglo de multiples country
                $originRegion           = "originRegion";//arreglo de multiples Region
                $originExc              = "Origin";// lectura de excel
                $destiny                = "destiny";//arreglo de multiples puertos
                $destinycountry         = "destinyCount";//arreglo de multiples country
                $destinyRegion          = "destinyRegion";//arreglo de multiples Region
                $destinyExc             = "Destiny";// lectura de excel
                $carrier                = "Carrier";
                $CalculationType        = "Calculation_Type";
                $Charge                 = "Charge";
                $statustypecurren       = "statustypecurren";
                $contractId             = "Contract_id";
                $typedestiny            = "Type_Destiny";
                $differentiator         = "Differentiator";
                $scheduleTExc           = "Schedule_Type";
                $transittimeExc         = "Transit_Time";
                $viaExc                 = "Via";
                $scheduleinfo           = "scheduleinfo";

                $statusPortCountryTW    = $requestobj['statusPortCountry'];
                $chargeVal              = $requestobj['chargeVal'];
                $contract_id            = $requestobj['Contract_id'];
                $statusexistfortynor    = $requestobj['existfortynor'];
                $statusexistfortyfive   = $requestobj['existfortyfive'];
                $statusPortCountry      = $requestobj['statusPortCountry'];

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':','1','2','3','4','5','6','7','8','9','0'];

                $ratescollection         = collect([]);
                $ratesFailcollection     = collect([]);
                $surcharcollection       = collect([]);
                $surcharFailcollection   = collect([]);


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
                                $differentiatorVal   = 1;
                                $contractIdVal       = $requestobj['Contract_id'];

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

                                $scheduleTResul      = null;
                                $transittimeResul    = 0;
                                $viaResul            = null;

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
                                $differentiatorBol       = false;
                                $scheduleTBol            = false;
                                $transittimeBol          = false;
                                $viaBol                  = false;
                                $ratesSchedulesValuesBol = true;
                                $values                  = true;



                                //--------------- Type Destiny ------------------------------------------------------------

                                if($requestobj['existtypedestiny'] == 1){
                                    $typedestinyExitBol = true;
                                    $typedestinyBol     = true;
                                    $typedestinyVal     = $requestobj['typedestiny']; // cuando se indica que no posee carrier 
                                } else {
                                    $typedestinyVal      = $read[$requestobj[$typedestiny]]; // cuando el carrier existe en el excel
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
                                    $carrierArr      = PrvCarrier::get_carrier($carrierVal);
                                    $carriExitBol    = $carrierArr['boolean'];
                                    $carrierVal      = $carrierArr['carrier'];
                                }

                                //--------------- DIFRENCIADOR HARBOR COUNTRY ---------------------------------------------
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

                                //--------------- ORIGEN MULTIPLE O SIMPLE ------------------------------------------------

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
                                //---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
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

                                //dd($randons);
                                //dd($destinyVal);
                                //dd($originVal);

                                //---------------- CURRENCY VALUES ------------------------------------------------------

                                if(empty($read[$requestobj[$twenty]]) != true){ //Primero valido si el campo viene lleno, en caso contrario lo lleno manuelamene
                                    $twentyArrBol = true;
                                    $twentyArr      = explode(' ',trim($read[$requestobj[$twenty]]));
                                } else {
                                    $twentyArr = ['0.0']; 
                                }

                                if(empty($read[$requestobj[$forty]]) != true){
                                    $fortyArrBol = true;
                                    $fortyArr       = explode(' ',trim($read[$requestobj[$forty]]));
                                } else {
                                    $fortyArr = ['0.0'];
                                }

                                if(empty($read[$requestobj[$fortyhc]]) != true){
                                    $fortyhcArrBol  = true;
                                    $fortyhcArr     = explode(' ',trim($read[$requestobj[$fortyhc]]));
                                } else {
                                    $fortyhcArr = ['0.0'];
                                }


                                if($statusexistfortynor == 1){ // si el selecionado en la vista que posee el campo 40'NOr o 45' hacemos lo mismo

                                    if(empty($read[$requestobj[$fortynor]]) != true){
                                        $fortynorArrBol  = true;
                                        $fortynorArr     = explode(' ',trim($read[$requestobj[$fortynor]]));
                                    } else {
                                        $fortynorArr = ['0.0'];
                                    }

                                }

                                if($statusexistfortyfive == 1){

                                    if(empty($read[$requestobj[$fortyfive]]) != true){
                                        $fortyfiveArrBol  = true;
                                        $fortyfiveArr     = explode(' ',trim($read[$requestobj[$fortyfive]]));
                                    } else {
                                        $fortyfiveArr = ['0.0'];
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
                                    $twentyVal   = floatval($twentyArr[0]);
                                }  else{
                                    $twentyVal = $twentyArr[0].'_E_E';
                                }

                                //----------------- 40' -----------------------------------------------------------------

                                if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                                    $fortyExiBol = true;
                                    $fortyVal   = floatval($fortyArr[0]);
                                }  else{
                                    $fortyVal = $fortyArr[0].'_E_E';
                                }

                                //----------------- 40'HC --------------------------------------------------------------

                                if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                                    $fortyhcExiBol = true;
                                    $fortyhcVal   = floatval($fortyhcArr[0]);
                                }   else{
                                    $fortyhcVal = $fortyhcArr[0].'_E_E';
                                }

                                //----------------- 40'NOR -------------------------------------------------------------
                                if($statusexistfortynor == 1){

                                    if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                                        $fortynorExiBol = true;
                                        $fortynorVal    = floatval($fortynorArr[0]);
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
                                        $fortyfiveVal    = floatval($fortyfiveArr[0]);
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
                                if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_SHIPMENT') == 0){
                                    $calculationvalvaration = 'Per Shipment';
                                } else if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_CONTAINER') == 0){
                                    $calculationvalvaration = 'Per Container';
                                } else if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_TON') == 0){
                                    $calculationvalvaration = 'Per TON';
                                } else if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_BL') == 0){
                                    $calculationvalvaration = 'Per BL';
                                } else if( strnatcasecmp($read[$requestobj[$CalculationType]],'PER_TEU') == 0){
                                    $calculationvalvaration = 'Per TEU';
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
                                    if($read[$requestobj[$Charge]] != $chargeVal){
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
                                                'description'       => $read[$requestobj[$Charge]],
                                                'company_user_id'   => $companyUserId
                                            ]);
                                            $surchargeVal = $surchargelist->id;
                                        }
                                    }
                                } else {
                                    $surchargeVal = $read[$requestobj[$Charge]].'_E_E';
                                }
                                //////////////////////////////////////////////////////////////////////////////////////////////////////

                                $rqScheduleinfoBol = $requestobj[$scheduleinfo];

                                //--------------- SCHEDULE TYPE --------------------------------------------

                                if($rqScheduleinfoBol == true && strnatcasecmp($read[$requestobj[$Charge]],$chargeVal) == 0){
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
                                if($rqScheduleinfoBol == true && strnatcasecmp($read[$requestobj[$Charge]],$chargeVal) == 0){
                                    $transittimeBol      = true;
                                    $transittimeResul   = (INT)$read[$requestobj[$transittimeExc]];
                                } else {
                                    $transittimeBol      = true;
                                }

                                //--------------- VIA --------------------------------------------
                                if($rqScheduleinfoBol == true && strnatcasecmp($read[$requestobj[$Charge]],$chargeVal) == 0){
                                    $viaBol     = true;
                                    $viaResul   = $read[$requestobj[$viaExc]];
                                } else {
                                    $viaBol     = true;
                                }

                                if($viaBol == false || $transittimeBol == false || $scheduleTBol == false){
                                    $ratesSchedulesValuesBol = false;
                                }

                                /* $pruebas = collect([]);
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
                            '$differentiatorBol'      => $differentiatorBol,
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
                            '$differentiatorVal'      => $differentiatorVal,
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
                            $prueba->push($cargaNor);
                        }

                        if($statusexistfortyfive == 1){
                            $cargaFive = ['$fortyfiveArr' => $fortyfiveArr];
                            $prueba->push($cargaFive);
                        }

                        $pruebas->push($prueba);

                        //dd($prueba);*/

                                if($carriExitBol            == true
                                   && $origExiBol           == true
                                   && $destiExitBol         == true
                                   && $twentyExiBol         == true
                                   && $fortyExiBol          == true
                                   && $fortyhcExiBol        == true
                                   && $fortynorExiBol       == true
                                   && $fortyfiveExiBol      == true
                                   && $calculationtypeExiBol == true
                                   && $variantecurrency     == true
                                   && $typeExiBol           == true
                                   && $typedestinyExitBol   == true
                                   && $ratesSchedulesValuesBol  == true
                                   && $values == true){


                                    if($differentiatorBol == false){ //si es puerto verificamos si exite uno creado con puerto
                                        $typeplace = 'localcharports';
                                    }else {  //si es country verificamos si exite uno creado con country 
                                        $typeplace = 'localcharcountries';
                                    }

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

                                                $exists = null;
                                                $exists = Rate::where('origin_port',$originVal)
                                                    ->where('destiny_port',$destinyVal)
                                                    ->where('carrier_id',$carrierVal)
                                                    ->where('contract_id',$contractIdVal)
                                                    ->where('twuenty',$twentyVal)
                                                    ->where('forty',$fortyVal)
                                                    ->where('fortyhc',$fortyhcVal)
                                                    ->where('fortynor',$fortynorVal)
                                                    ->where('fortyfive',$fortyfiveVal)
                                                    ->where('currency_id',$currencyVal)
                                                    ->where('schedule_type_id',$scheduleTResul)
                                                    ->where('transit_time',$transittimeResul)
                                                    ->where('via',$viaResul)
                                                    ->first();
                                                if(count($exists) == 0){
                                                    if($differentiatorBol == false){
                                                        $ratesArre = Rate::create([
                                                            'origin_port'       => $originVal,
                                                            'destiny_port'      => $destinyVal,
                                                            'carrier_id'        => $carrierVal,
                                                            'contract_id'       => $contractIdVal,
                                                            'twuenty'           => $twentyVal,
                                                            'forty'             => $fortyVal,
                                                            'fortyhc'           => $fortyhcVal,
                                                            'fortynor'          => $fortynorVal,
                                                            'fortyfive'         => $fortyfiveVal,
                                                            'currency_id'       => $currencyVal,
                                                            'schedule_type_id'  => $scheduleTResul,
                                                            'transit_time'      => $transittimeResul,
                                                            'via'               => $viaResul
                                                        ]);
                                                        //dd($ratesArre);
                                                    }
                                                }
                                            } 
                                        }else {
                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                            if($requestobj[$statustypecurren] == 2){
                                                $currencyVal = $currencyValtwen;
                                            }
                                            if($differentiatorBol == false){
                                                $exists = null;
                                                $exists = Rate::where('origin_port',$originVal)
                                                    ->where('destiny_port',$destinyVal)
                                                    ->where('carrier_id',$carrierVal)
                                                    ->where('contract_id',$contractIdVal)
                                                    ->where('twuenty',$twentyVal)
                                                    ->where('forty',$fortyVal)
                                                    ->where('fortyhc',$fortyhcVal)
                                                    ->where('fortynor',$fortynorVal)
                                                    ->where('fortyfive',$fortyfiveVal)
                                                    ->where('currency_id',$currencyVal)
                                                    ->where('schedule_type_id',$scheduleTResul)
                                                    ->where('transit_time',$transittimeResul)
                                                    ->where('via',$viaResul)
                                                    ->first();
                                                if(count($exists) == 0){
                                                    $ratesArre =  Rate::create([
                                                        'origin_port'       => $originVal,
                                                        'destiny_port'      => $destinyVal,
                                                        'carrier_id'        => $carrierVal,
                                                        'contract_id'       => $contractIdVal,
                                                        'twuenty'           => $twentyVal,
                                                        'forty'             => $fortyVal,
                                                        'fortyhc'           => $fortyhcVal,
                                                        'fortynor'          => $fortynorVal,
                                                        'fortyfive'         => $fortyfiveVal,
                                                        'currency_id'       => $currencyVal,
                                                        'schedule_type_id'  => $scheduleTResul,
                                                        'transit_time'      => $transittimeResul,
                                                        'via'               => $viaResul
                                                    ]);
                                                }
                                                //dd($ratesArre);
                                            }
                                        }


                                    } else{
                                        // se ejecuta la carga de los surcharges
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
                                                if($ammount != 0 || $ammount != 0.0){
                                                    $SurchargArreG = null;
                                                    $SurchargArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contractIdVal)
                                                        ->where('calculationtype_id',$calculationtypeVal)
                                                        ->where('ammount',$ammount)
                                                        ->where('currency_id',$currencyVal)
                                                        ->has($typeplace)
                                                        ->first();
                                                    if(count($SurchargArreG) == 0){
                                                        $SurchargArreG = LocalCharge::create([ // tabla localcharges
                                                            'surcharge_id'       => $surchargeVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeVal,
                                                            'ammount'            => $ammount,
                                                            'currency_id'        => $currencyVal
                                                        ]);
                                                    }
                                                    //---------------------------------- CAMBIAR POR ID -----------------------------------------------------------
                                                    $existsCar = null;
                                                    $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                        ->where('localcharge_id',$SurchargArreG->id)->first();
                                                    if(count($existsCar) == 0){
                                                        $SurchargCarrArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                            'carrier_id'      => $carrierVal,
                                                            'localcharge_id' => $SurchargArreG->id
                                                        ]);
                                                    }
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
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                            //---------------------------------------------------------------------------------

                                                        } 
                                                    }else {
                                                        // fila por puerto, sin expecificar origen ni destino manualmente
                                                        if($differentiatorBol){
                                                            $existCount = null;
                                                            $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                ->where('country_dest',$destinyVal)
                                                                ->where('localcharge_id',$SurchargArreG->id)
                                                                ->first();
                                                            if(count($existCount) == 0){
                                                                $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                    'country_orig'      => $originVal,
                                                                    'country_dest'      => $destinyVal,
                                                                    'localcharge_id'    => $SurchargArreG->id
                                                                ]);
                                                            }
                                                        } else {
                                                            $existPort = null;
                                                            $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('localcharge_id',$SurchargArreG->id)
                                                                ->first();
                                                            if(count($existPort) == 0){
                                                                $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                    'port_orig'      => $originVal,
                                                                    'port_dest'      => $destinyVal,
                                                                    'localcharge_id' => $SurchargArreG->id
                                                                ]);
                                                            }
                                                        }
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
                                                    if($twentyVal != 0 || $twentyVal != 0.0){
                                                        $SurchargTWArreG = null;
                                                        $SurchargTWArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',2)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency_id',$currencyValtwen)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargTWArreG) == 0){
                                                            $SurchargTWArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 2,
                                                                'ammount'            => $twentyVal,
                                                                'currency_id'        => $currencyValtwen
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargTWArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrTWArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'      => $carrierVal,
                                                                'localcharge_id' => $SurchargTWArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargTWArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargCountArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargTWArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargTWArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargTWArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargTWArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargTWArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargTWArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargTWArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    //---------------------- CARGA 40' ----------------------------------------------------

                                                    if($fortyVal != 0 || $fortyVal != 0.0){
                                                        $SurchargFORArreG = null;
                                                        $SurchargFORArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',1)
                                                            ->where('ammount',$fortyVal)
                                                            ->where('currency_id',$currencyValfor)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORArreG) == 0){
                                                            $SurchargFORArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 1,
                                                                'ammount'            => $fortyVal,
                                                                'currency_id'        => $currencyValfor
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'        => $carrierVal,
                                                                'localcharge_id'    => $SurchargFORArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                    }

                                                    // --------------------- CARGA 40'HC --------------------------------------------------

                                                    if($fortyhcVal != 0 || $fortyhcVal != 0.0){
                                                        $SurchargFORHCArreG = null;
                                                        $SurchargFORHCArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',3)
                                                            ->where('ammount',$fortyhcVal)
                                                            ->where('currency_id',$currencyValforHC)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORHCArreG) == 0){
                                                            $SurchargFORHCArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 3,
                                                                'ammount'            => $fortyhcVal,
                                                                'currency_id'        => $currencyValforHC
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORHCArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORHCArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'     => $carrierVal,
                                                                'localcharge_id' => $SurchargFORHCArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORHCArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORHCArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORHCArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORHCArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }

                                                        //echo $i;
                                                        //dd($SurchargArreG);
                                                    }

                                                    // --------------------- CARGA 40'NOR -------------------------------------------------

                                                    if($fortynorVal != 0 || $fortynorVal != 0.0){
                                                        $SurchargFORNORArreG = null;
                                                        $SurchargFORNORArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',7)
                                                            ->where('ammount',$fortynorVal)
                                                            ->where('currency_id',$currencyValfornor)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORNORArreG) == 0){
                                                            $SurchargFORNORArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 7,
                                                                'ammount'            => $fortynorVal,
                                                                'currency_id'        => $currencyValfornor
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORNORArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORNORArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'      => $carrierVal,
                                                                'localcharge_id' => $SurchargFORNORArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORNORArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORNORArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORNORArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORNORArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORNORArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORNORArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORNORArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORNORArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }

                                                        //echo $i;
                                                        //dd($SurchargArreG);
                                                    }

                                                    // --------------------- CARGA 45' ----------------------------------------------------

                                                    if($fortyfiveVal != 0 || $fortyfiveVal != 0.0){
                                                        $SurchargFORfiveArreG = null;
                                                        $SurchargFORfiveArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',8)
                                                            ->where('ammount',$fortyfiveVal)
                                                            ->where('currency_id',$currencyValforfive)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORfiveArreG) == 0){
                                                            $SurchargFORfiveArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 8,
                                                                'ammount'            => $fortyfiveVal,
                                                                'currency_id'        => $currencyValforfive
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORfiveArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORfiveArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'     => $carrierVal,
                                                                'localcharge_id' => $SurchargFORfiveArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORfiveArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORfiveArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORfiveArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORfiveArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }

                                                        //echo $i;
                                                        //dd($SurchargArreG);
                                                    }

                                                    //---------------------
                                                } else{

                                                    // cargar el currency ya descompuesto, ahora es un solo registro (currency ) de los cinco campos que existen

                                                    // ----------------------- CARGA 20' -------------------------------------------

                                                    if($twentyVal != 0 || $twentyVal != 0.0){
                                                        $SurchargTWArreG = null;
                                                        $SurchargTWArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',2)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargTWArreG) == 0){
                                                            $SurchargTWArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 2,
                                                                'ammount'            => $twentyVal,
                                                                'currency_id'        => $currencyVal
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargTWArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrTWArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'      => $carrierVal,
                                                                'localcharge_id' => $SurchargTWArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargTWArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargTWArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargTWArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargTWArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargTWArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargTWArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargTWArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargTWArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                    }

                                                    //---------------------- CARGA 40' -----------------------------------------------

                                                    if($fortyVal != 0 || $fortyVal != 0.0){
                                                        $SurchargFORArreG = null;
                                                        $SurchargFORArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',1)
                                                            ->where('ammount',$fortyVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORArreG) == 0){
                                                            $SurchargFORArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 1,
                                                                'ammount'            => $fortyVal,
                                                                'currency_id'        => $currencyVal
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'      => $carrierVal,
                                                                'localcharge_id' => $SurchargFORArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                    }

                                                    // --------------------- CARGA 40'HC ---------------------------------------------

                                                    if($fortyhcVal != 0 || $fortyhcVal != 0.0){
                                                        $SurchargFORHCArreG = null;
                                                        $SurchargFORHCArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',3)
                                                            ->where('ammount',$fortyhcVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORHCArreG) == 0){
                                                            $SurchargFORHCArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 3,
                                                                'ammount'            => $fortyhcVal,
                                                                'currency_id'        => $currencyVal
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORHCArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORHCArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'     => $carrierVal,
                                                                'localcharge_id' => $SurchargFORHCArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORHCArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORHCArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORHCArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORHCArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORHCArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                        //echo $i;
                                                        //dd($SurchargFORHCArreG);
                                                    }

                                                    // --------------------- CARGA 40'NOR --------------------------------------------

                                                    if($fortynorVal != 0 || $fortynorVal != 0.0){
                                                        $SurchargFORnorArreG = null;
                                                        $SurchargFORnorArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',7)
                                                            ->where('ammount',$fortynorVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORnorArreG) == 0){
                                                            $SurchargFORnorArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 7,
                                                                'ammount'            => $fortynorVal,
                                                                'currency_id'        => $currencyVal
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORnorArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORnorArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'     => $carrierVal,
                                                                'localcharge_id' => $SurchargFORnorArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORnorArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORnorArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORnorArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORnorArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORnorArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORnorArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORnorArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORnorArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                        //echo $i;
                                                        //dd($SurchargFORHCArreG);
                                                    }

                                                    // --------------------- CARGA 45' -----------------------------------------------

                                                    if($fortyfiveVal != 0 || $fortyfiveVal != 0.0){
                                                        $SurchargFORfiveArreG = null;
                                                        $SurchargFORfiveArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',8)
                                                            ->where('ammount',$fortyfiveVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->has($typeplace)
                                                            ->first();
                                                        if(count($SurchargFORfiveArreG) == 0){
                                                            $SurchargFORfiveArreG = LocalCharge::create([ // tabla localcharges
                                                                'surcharge_id'       => $surchargeVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => 8,
                                                                'ammount'            => $fortyfiveVal,
                                                                'currency_id'        => $currencyVal
                                                            ]);
                                                        }

                                                        $existsCar = null;
                                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                            ->where('localcharge_id',$SurchargFORfiveArreG->id)->first();
                                                        if(count($existsCar) == 0){
                                                            $SurchargCarrFORfiveArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                                'carrier_id'     => $carrierVal,
                                                                'localcharge_id' => $SurchargFORfiveArreG->id
                                                            ]);
                                                        }

                                                        if($originBol == true || $destinyBol == true){
                                                            foreach($randons as  $rando){
                                                                //insert por arreglo de puerto
                                                                if($originBol == true ){
                                                                    $originVal = $rando;
                                                                } else {
                                                                    $destinyVal = $rando;
                                                                }

                                                                if($differentiatorBol){
                                                                    $existCount = null;
                                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                        ->where('country_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                        ->first();
                                                                    if(count($existCount) == 0){
                                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                            'country_orig'      => $originVal,
                                                                            'country_dest'      => $destinyVal,
                                                                            'localcharge_id'    => $SurchargFORfiveArreG->id
                                                                        ]);
                                                                    }
                                                                } else {
                                                                    $existPort = null;
                                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                        ->where('port_dest',$destinyVal)
                                                                        ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                        ->first();
                                                                    if(count($existPort) == 0){
                                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                            'port_orig'      => $originVal,
                                                                            'port_dest'      => $destinyVal,
                                                                            'localcharge_id' => $SurchargFORfiveArreG->id
                                                                        ]);
                                                                    }
                                                                }
                                                            } 

                                                        } else {
                                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargFORfiveArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $existPort = null;
                                                                $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargFORfiveArreG->id)
                                                                    ->first();
                                                                if(count($existPort) == 0){
                                                                    $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                        'port_orig'      => $originVal,
                                                                        'port_dest'      => $destinyVal,
                                                                        'localcharge_id' => $SurchargFORfiveArreG->id
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                        //echo $i;
                                                        //dd($SurchargFORHCArreG);
                                                    }
                                                    //_____-----
                                                }

                                            }

                                        } else{
                                            if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_SHIPMENT') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'PER_TON') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'PER_BL')  == 0 ||
                                               strnatcasecmp($read[$requestobj[$CalculationType]],'PER_TEU') == 0){
                                                //per_shipment
                                                if($twentyVal != 0 || $twentyVal != 0.0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    } 
                                                    $ammount = $twentyVal;

                                                } else if ($fortyVal != 0 || $fortyVal != 0.0){
                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    } 
                                                    $ammount = $fortyVal;

                                                }else if ($fortyhcVal != 0 || $fortyhcVal != 0.0){

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    } 
                                                    $ammount = $fortyhcVal;

                                                }else if ($fortynorVal != 0 || $fortynorVal != 0.0){
                                                    if($statusexistfortynor == 1){
                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfornor;
                                                        } 
                                                    }
                                                    $ammount = $fortynorVal;

                                                }else if ($fortyfiveVal != 0 || $fortyfiveVal != 0.0){
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

                                                if($ammount != 0 || $ammount != 0.0){
                                                    $SurchargPERArreG = null;
                                                    $SurchargPERArreG = LocalCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contractIdVal)
                                                        ->where('calculationtype_id',$calculationtypeVal)
                                                        ->where('ammount',$ammount)
                                                        ->where('currency_id',$currencyVal)
                                                        ->has($typeplace)
                                                        ->first();
                                                    if(count($SurchargPERArreG) == 0){
                                                        $SurchargPERArreG = LocalCharge::create([ // tabla localcharges
                                                            'surcharge_id'       => $surchargeVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'calculationtype_id' => $calculationtypeVal,
                                                            'ammount'            => $ammount,
                                                            'currency_id'        => $currencyVal
                                                        ]);
                                                    }

                                                    $existsCar = null;
                                                    $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                        ->where('localcharge_id',$SurchargPERArreG->id)->first();
                                                    if(count($existsCar) == 0){
                                                        $SurchargCarrFORHCArreG = LocalCharCarrier::create([ // tabla localcharcarriers
                                                            'carrier_id'     => $carrierVal,
                                                            'localcharge_id' => $SurchargPERArreG->id
                                                        ]);
                                                    }

                                                    if($originBol == true || $destinyBol == true){
                                                        foreach($randons as  $rando){
                                                            //insert por arreglo de puerto
                                                            if($originBol == true ){
                                                                $originVal = $rando;
                                                            } else {
                                                                $destinyVal = $rando;
                                                            }

                                                            if($differentiatorBol){
                                                                $existCount = null;
                                                                $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                                    ->where('country_dest',$destinyVal)
                                                                    ->where('localcharge_id',$SurchargPERArreG->id)
                                                                    ->first();
                                                                if(count($existCount) == 0){
                                                                    $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                        'country_orig'      => $originVal,
                                                                        'country_dest'      => $destinyVal,
                                                                        'localcharge_id'    => $SurchargPERArreG->id
                                                                    ]);
                                                                }
                                                            } else {
                                                                $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                    'port_orig'      => $originVal,
                                                                    'port_dest'      => $destinyVal,
                                                                    'localcharge_id' => $SurchargPERArreG->id
                                                                ]);
                                                            }
                                                        } 

                                                    } else {
                                                        // fila por puerto, sin expecificar origen ni destino manualmente
                                                        if($differentiatorBol){
                                                            $existPort = null;
                                                            $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('localcharge_id',$SurchargPERArreG->id)
                                                                ->first();
                                                            if(count($existPort) == 0){
                                                                $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                    'country_orig'      => $originVal,
                                                                    'country_dest'      => $destinyVal,
                                                                    'localcharge_id'    => $SurchargPERArreG->id
                                                                ]);
                                                            }
                                                        } else {
                                                            $existPort = null;
                                                            $existPort = LocalCharPort::where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('localcharge_id',$SurchargPERArreG->id)
                                                                ->first();
                                                            if(count($existPort) == 0){
                                                                $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                    'port_orig'      => $originVal,
                                                                    'port_dest'      => $destinyVal,
                                                                    'localcharge_id' => $SurchargPERArreG->id
                                                                ]);
                                                            }
                                                        }
                                                    }
                                                }
                                                // echo $i;
                                                // dd($SurchargPERArreG);
                                            }
                                        }

                                    }
                                } else {
                                    // van los fallidos

                                    //---------------------------- SCHEDULE TYPE ----------------------------------------------------
                                    if( strnatcasecmp($read[$requestobj[$Charge]],$chargeVal) == 0 && $rqScheduleinfoBol == true){
                                        if( $scheduleTBol == true){
                                            $scheduleTResul = ScheduleType::find($scheduleTResul);
                                            $scheduleTResul = $scheduleTResul['name'];
                                        }
                                    }

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
                                            $carriExitBol2   = false;
                                            $carrierArr      = PrvCarrier::get_carrier($read[$requestobj['Carrier']]);
                                            $carrierVal      = $carrierArr['carrier'];
                                            $carriExitBol2   = $carrierArr['boolean'];
                                            if($carriExitBol2 == true){
                                                $carrierVal = Carrier::find($carrierVal);
                                                $carrierVal = $carrierVal->name;
                                            }

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

                                                    $exists = null;
                                                    $exists = FailRate::where('origin_port',$originVal)
                                                        ->where('destiny_port',$destinyVal)
                                                        ->where('carrier_id',$carrierVal)
                                                        ->where('contract_id',$contractIdVal)
                                                        ->where('twuenty',$twentyVal)
                                                        ->where('forty',$fortyVal)
                                                        ->where('fortyhc',$fortyhcVal)
                                                        ->where('fortynor',$fortynorVal)
                                                        ->where('fortyfive',$fortyfiveVal)
                                                        ->where('currency_id',$currencyVal)
                                                        ->where('schedule_type',$scheduleTResul)
                                                        ->where('transit_time',$transittimeResul)
                                                        ->where('via',$viaResul)
                                                        ->first();
                                                    if(count($exists) == 0){
                                                        FailRate::create([
                                                            'origin_port'        => $originVal,
                                                            'destiny_port'       => $destinyVal,
                                                            'carrier_id'         => $carrierVal,
                                                            'contract_id'        => $contractIdVal,
                                                            'twuenty'            => $twentyVal,
                                                            'forty'              => $fortyVal,
                                                            'fortyhc'            => $fortyhcVal,
                                                            'fortynor'           => $fortynorVal,
                                                            'fortyfive'          => $fortyfiveVal,
                                                            'currency_id'        => $currencyVal,
                                                            'schedule_type'      => $scheduleTResul,
                                                            'transit_time'       => $transittimeResul,
                                                            'via'                => $viaResul
                                                        ]);
                                                    }
                                                }
                                            } else {
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

                                                $exists = null;
                                                $exists = FailRate::where('origin_port',$originVal)
                                                    ->where('destiny_port',$destinyVal)
                                                    ->where('carrier_id',$carrierVal)
                                                    ->where('contract_id',$contractIdVal)
                                                    ->where('twuenty',$twentyVal)
                                                    ->where('forty',$fortyVal)
                                                    ->where('fortyhc',$fortyhcVal)
                                                    ->where('fortynor',$fortynorVal)
                                                    ->where('fortyfive',$fortyfiveVal)
                                                    ->where('currency_id',$currencyVal)
                                                    ->where('schedule_type',$scheduleTResul)
                                                    ->where('transit_time',$transittimeResul)
                                                    ->where('via',$viaResul)
                                                    ->first();
                                                if(count($exists) == 0){
                                                    FailRate::create([
                                                        'origin_port'        => $originVal,
                                                        'destiny_port'       => $destinyVal,
                                                        'carrier_id'         => $carrierVal,
                                                        'contract_id'        => $contractIdVal,
                                                        'twuenty'            => $twentyVal,
                                                        'forty'              => $fortyVal,
                                                        'fortyhc'            => $fortyhcVal,
                                                        'fortynor'           => $fortynorVal,
                                                        'fortyfive'          => $fortyfiveVal,
                                                        'currency_id'        => $currencyVal,
                                                        'schedule_type'      => $scheduleTResul,
                                                        'transit_time'       => $transittimeResul,
                                                        'via'                => $viaResul
                                                    ]);
                                                }
                                            } //*/
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
                                                            if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                                $exists = null;
                                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                    ->where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('typedestiny_id',$typedestinyVal)
                                                                    ->where('contract_id',$contractIdVal)
                                                                    ->where('calculationtype_id',$calculationtypeValfail)
                                                                    ->where('ammount',$twentyVal)
                                                                    ->where('currency_id',$currencyVal)
                                                                    ->where('carrier_id',$carrierVal)
                                                                    ->where('differentiator',$differentiatorVal)
                                                                    ->first();
                                                                if(count($exists) == 0){
                                                                    FailSurCharge::create([
                                                                        'surcharge_id'       => $surchargeVal,
                                                                        'port_orig'          => $originVal,
                                                                        'port_dest'          => $destinyVal,
                                                                        'typedestiny_id'     => $typedestinyVal,
                                                                        'contract_id'        => $contractIdVal,
                                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                                        'ammount'            => $twentyVal, //////
                                                                        'currency_id'        => $currencyVal, //////
                                                                        'carrier_id'         => $carrierVal,
                                                                        'differentiator'     => $differentiatorVal
                                                                    ]);
                                                                }
                                                            }
                                                            //$ratescollection->push($ree);

                                                        } else{


                                                            // -------- 20' ---------------------------------

                                                            $calculationtypeValfail = 'Per 20 "';

                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValtwen;
                                                            }
                                                            if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                                $exists = null;
                                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                    ->where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('typedestiny_id',$typedestinyVal)
                                                                    ->where('contract_id',$contractIdVal)
                                                                    ->where('calculationtype_id',$calculationtypeValfail)
                                                                    ->where('ammount',$twentyVal)
                                                                    ->where('currency_id',$currencyVal)
                                                                    ->where('carrier_id',$carrierVal)
                                                                    ->where('differentiator',$differentiatorVal)
                                                                    ->first();
                                                                if(count($exists) == 0){
                                                                    FailSurCharge::create([
                                                                        'surcharge_id'       => $surchargeVal,
                                                                        'port_orig'          => $originVal,
                                                                        'port_dest'          => $destinyVal,
                                                                        'typedestiny_id'     => $typedestinyVal,
                                                                        'contract_id'        => $contractIdVal,
                                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                                        'ammount'            => $twentyVal, //////
                                                                        'currency_id'        => $currencyVal, //////
                                                                        'carrier_id'         => $carrierVal,
                                                                        'differentiator'     => $differentiatorVal
                                                                    ]);
                                                                }
                                                            }
                                                            // $ratescollection->push($ree);

                                                            // -------- 40' ---------------------------------

                                                            $calculationtypeValfail = 'Per 40 "';

                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValfor;
                                                            }

                                                            if($fortyArr[0] != 0 || $fortyArr[0] != 0.0){
                                                                $exists = null;
                                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                    ->where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('typedestiny_id',$typedestinyVal)
                                                                    ->where('contract_id',$contractIdVal)
                                                                    ->where('calculationtype_id',$calculationtypeValfail)
                                                                    ->where('ammount',$fortyVal)
                                                                    ->where('currency_id',$currencyVal)
                                                                    ->where('carrier_id',$carrierVal)
                                                                    ->where('differentiator',$differentiatorVal)
                                                                    ->first();
                                                                if(count($exists) == 0){
                                                                    FailSurCharge::create([
                                                                        'surcharge_id'       => $surchargeVal,
                                                                        'port_orig'          => $originVal,
                                                                        'port_dest'          => $destinyVal,
                                                                        'typedestiny_id'     => $typedestinyVal,
                                                                        'contract_id'        => $contractIdVal,
                                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                                        'ammount'            => $fortyVal, //////
                                                                        'currency_id'        => $currencyVal, //////
                                                                        'carrier_id'         => $carrierVal,
                                                                        'differentiator'   => $differentiatorVal
                                                                    ]);
                                                                }
                                                            }
                                                            // $ratescollection->push($ree);

                                                            // -------- 40'HC -------------------------------

                                                            $calculationtypeValfail = 'Per 40 HC';

                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValforHC;
                                                            }

                                                            if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                                $exists = null;
                                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                    ->where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('typedestiny_id',$typedestinyVal)
                                                                    ->where('contract_id',$contractIdVal)
                                                                    ->where('calculationtype_id',$calculationtypeValfail)
                                                                    ->where('ammount',$fortyhcVal)
                                                                    ->where('currency_id',$currencyVal)
                                                                    ->where('carrier_id',$carrierVal)
                                                                    ->where('differentiator',$differentiatorVal)
                                                                    ->first();
                                                                if(count($exists) == 0){
                                                                    FailSurCharge::create([
                                                                        'surcharge_id'       => $surchargeVal,
                                                                        'port_orig'          => $originVal,
                                                                        'port_dest'          => $destinyVal,
                                                                        'typedestiny_id'     => $typedestinyVal,
                                                                        'contract_id'        => $contractIdVal,
                                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                                        'ammount'            => $fortyhcVal, //////
                                                                        'currency_id'        => $currencyVal, //////
                                                                        'carrier_id'         => $carrierVal,
                                                                        'differentiator'     => $differentiatorVal
                                                                    ]);
                                                                }
                                                            }
                                                            //$ratescollection->push($ree);

                                                            // -------- 40'NOR -------------------------------

                                                            $calculationtypeValfail = 'Per 40 NOR';

                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValfornor;
                                                            }

                                                            if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                                $exists = null;
                                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                    ->where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('typedestiny_id',$typedestinyVal)
                                                                    ->where('contract_id',$contractIdVal)
                                                                    ->where('calculationtype_id',$calculationtypeValfail)
                                                                    ->where('ammount',$fortynorVal)
                                                                    ->where('currency_id',$currencyVal)
                                                                    ->where('carrier_id',$carrierVal)
                                                                    ->where('differentiator',$differentiatorVal)
                                                                    ->first();
                                                                if(count($exists) == 0){
                                                                    FailSurCharge::create([
                                                                        'surcharge_id'       => $surchargeVal,
                                                                        'port_orig'          => $originVal,
                                                                        'port_dest'          => $destinyVal,
                                                                        'typedestiny_id'     => $typedestinyVal,
                                                                        'contract_id'        => $contractIdVal,
                                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                                        'ammount'            => $fortynorVal, //////
                                                                        'currency_id'        => $currencyVal, //////
                                                                        'carrier_id'         => $carrierVal,
                                                                        'differentiator'     => $differentiatorVal
                                                                    ]);
                                                                }
                                                            }
                                                            //$ratescollection->push($ree);

                                                            // -------- 45' ---------------------------------

                                                            $calculationtypeValfail = 'Per 45';

                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValforfive;
                                                            }

                                                            if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                                $exists = null;
                                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                    ->where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('typedestiny_id',$typedestinyVal)
                                                                    ->where('contract_id',$contractIdVal)
                                                                    ->where('calculationtype_id',$calculationtypeValfail)
                                                                    ->where('ammount',$fortyfiveVal)
                                                                    ->where('currency_id',$currencyVal)
                                                                    ->where('carrier_id',$carrierVal)
                                                                    ->where('differentiator',$differentiatorVal)
                                                                    ->first();
                                                                if(count($exists) == 0){
                                                                    FailSurCharge::create([
                                                                        'surcharge_id'       => $surchargeVal,
                                                                        'port_orig'          => $originVal,
                                                                        'port_dest'          => $destinyVal,
                                                                        'typedestiny_id'     => $typedestinyVal,
                                                                        'contract_id'        => $contractIdVal,
                                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                                        'ammount'            => $fortyfiveVal, //////
                                                                        'currency_id'        => $currencyVal, //////
                                                                        'carrier_id'         => $carrierVal,
                                                                        'differentiator'   => $differentiatorVal
                                                                    ]);
                                                                }
                                                            }
                                                            //$ratescollection->push($ree);

                                                        }
                                                    }
                                                } else {
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
                                                        if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$twentyVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $twentyVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //$ratescollection->push($ree);
                                                            }
                                                        }

                                                    } else{

                                                        // -------- 20' ---------------------------------

                                                        $calculationtypeValfail = 'Per 20 "';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValtwen;
                                                        }

                                                        if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$twentyVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $twentyVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //$ratescollection->push($ree);
                                                            }
                                                        }
                                                        // -------- 40' ---------------------------------

                                                        $calculationtypeValfail = 'Per 40 "';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfor;
                                                        }

                                                        if($fortyArr[0] != 0 || $fortyArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortyVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortyVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                // $ratescollection->push($ree);
                                                            }
                                                        }

                                                        // -------- 40'HC -------------------------------

                                                        $calculationtypeValfail = 'Per 40 HC';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforHC;
                                                        }

                                                        if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortyhcVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortyhcVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //  $ratescollection->push($ree);
                                                            }
                                                        }
                                                        // -------- 40'NOR ------------------------------

                                                        $calculationtypeValfail = 'Per 40 NOR';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfornor;
                                                        }

                                                        if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortynorVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortynorVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //  $ratescollection->push($ree);
                                                            }
                                                        }

                                                        // -------- 45' ---------------------------------

                                                        $calculationtypeValfail = 'Per 45';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforfive;
                                                        }

                                                        if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortyfiveVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortyfiveVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //  $ratescollection->push($ree);
                                                            }
                                                        }
                                                    }
                                                }

                                            } 
                                            else{
                                                if (strnatcasecmp($read[$requestobj[$CalculationType]],'PER_SHIPMENT') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'Per Shipment') == 0 || 
                                                    strnatcasecmp($read[$requestobj[$CalculationType]],'Per_BL') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TON') == 0 ||
                                                    strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TEU') == 0){
                                                    
                                                    if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_SHIPMENT') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'Per Shipment') == 0){
                                                        $calculationtypeValfail = 'Per Shipment';
                                                    } else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_BL') == 0 ){
                                                        $calculationtypeValfail = 'Per BL';
                                                    } else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TON') == 0){
                                                        $calculationtypeValfail = 'Per TON';
                                                    } else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TEU') == 0){
                                                        $calculationtypeValfail = 'Per TEU';
                                                    }
                                                    
                                                    
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

                                                            

                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValtwen;
                                                            }

                                                            if($twentyVal != 0 || $twentyVal != 0.0){
                                                                if($requestobj[$statustypecurren] == 2){
                                                                    $currencyVal = $currencyValtwen;
                                                                } 
                                                                $ammount = $twentyVal;

                                                            } else if ($fortyVal != 0 || $fortyVal != 0.0){
                                                                if($requestobj[$statustypecurren] == 2){
                                                                    $currencyVal = $currencyValfor;
                                                                } 
                                                                $ammount = $fortyVal;

                                                            }else if($fortyhcVal != 0 || $fortyhcVal != 0.0){

                                                                if($requestobj[$statustypecurren] == 2){
                                                                    $currencyVal = $currencyValforHC;
                                                                } 
                                                                $ammount = $fortyhcVal;

                                                            }else if($fortynorVal != 0 || $fortynorVal != 0.0){

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

                                                            if($ammount != 0 || $ammount != 0.0){
                                                                $exists = null;
                                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                    ->where('port_orig',$originVal)
                                                                    ->where('port_dest',$destinyVal)
                                                                    ->where('typedestiny_id',$typedestinyVal)
                                                                    ->where('contract_id',$contractIdVal)
                                                                    ->where('calculationtype_id',$calculationtypeValfail)
                                                                    ->where('ammount',$ammount)
                                                                    ->where('currency_id',$currencyVal)
                                                                    ->where('carrier_id',$carrierVal)
                                                                    ->where('differentiator',$differentiatorVal)
                                                                    ->first();
                                                                if(count($exists) == 0){
                                                                    FailSurCharge::create([
                                                                        'surcharge_id'       => $surchargeVal,
                                                                        'port_orig'          => $originVal,
                                                                        'port_dest'          => $destinyVal,
                                                                        'typedestiny_id'     => $typedestinyVal,
                                                                        'contract_id'        => $contractIdVal,
                                                                        'calculationtype_id' => $calculationtypeValfail,  //////
                                                                        'ammount'            => $ammount, //////
                                                                        'currency_id'        => $currencyVal, //////
                                                                        'carrier_id'         => $carrierVal,
                                                                        'differentiator'     => $differentiatorVal
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

                                                        

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValtwen;
                                                        }
                                                        if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$twentyVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $twentyVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //  $ratescollection->push($ree);
                                                            }
                                                        }
                                                    }

                                                }
                                            }
                                        } else{
                                            // se deconoce si es PER_CONTAINER O PER_DOC

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

                                                        if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$twentyVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $twentyVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                // $ratescollection->push($ree);
                                                            }
                                                        }
                                                    } else{

                                                        // -------- 20' ---------------------------------

                                                        $calculationtypeValfail = 'Per 20 "Error fila '.$i.'_E_E';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValtwen;
                                                        }
                                                        if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$twentyVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $twentyVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                // $ratescollection->push($ree);
                                                            }
                                                        }
                                                        // -------- 40' ---------------------------------

                                                        $calculationtypeValfail = 'Per 40 "Error fila '.$i.'_E_E';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfor;
                                                        }

                                                        if($fortyArr[0] != 0 || $fortyArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortyVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortyVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //$ratescollection->push($ree);
                                                            }
                                                        }

                                                        // -------- 40'HC -------------------------------

                                                        $calculationtypeValfail = '40HC Error fila '.$i.'_E_E';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforHC;
                                                        }

                                                        if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortyhcVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortyhcVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //$ratescollection->push($ree);
                                                            }
                                                        }

                                                        // -------- 40'NOR ------------------------------

                                                        $calculationtypeValfail = '40\'NOR Error fila '.$i.'_E_E';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfornor;
                                                        }

                                                        if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortynorVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortynorVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //$ratescollection->push($ree);
                                                            }
                                                        }

                                                        // -------- 45'  -------------------------------

                                                        $calculationtypeValfail = '45\' Error fila '.$i.'_E_E';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforfive;
                                                        }

                                                        if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contractIdVal)
                                                                ->where('calculationtype_id',$calculationtypeValfail)
                                                                ->where('ammount',$fortyfiveVal)
                                                                ->where('currency_id',$currencyVal)
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->first();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contractIdVal,
                                                                    'calculationtype_id' => $calculationtypeValfail,  //////
                                                                    'ammount'            => $fortyfiveVal, //////
                                                                    'currency_id'        => $currencyVal, //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                                //$ratescollection->push($ree);
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
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

                                                    if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                        $exists = null;
                                                        $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',$calculationtypeValfail)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->where('carrier_id',$carrierVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->first();
                                                        if(count($exists) == 0){
                                                            FailSurCharge::create([
                                                                'surcharge_id'       => $surchargeVal,
                                                                'port_orig'          => $originVal,
                                                                'port_dest'          => $destinyVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => $calculationtypeValfail,  //////
                                                                'ammount'            => $twentyVal, //////
                                                                'currency_id'        => $currencyVal, //////
                                                                'carrier_id'         => $carrierVal,
                                                                'differentiator'     => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }
                                                } else{

                                                    // -------- 20' ---------------------------------

                                                    $calculationtypeValfail = 'Per 20 "Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }

                                                    if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                        $exists = null;
                                                        $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',$calculationtypeValfail)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->where('carrier_id',$carrierVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->first();
                                                        if(count($exists) == 0){
                                                            FailSurCharge::create([
                                                                'surcharge_id'       => $surchargeVal,
                                                                'port_orig'          => $originVal,
                                                                'port_dest'          => $destinyVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => $calculationtypeValfail,  //////
                                                                'ammount'            => $twentyVal, //////
                                                                'currency_id'        => $currencyVal, //////
                                                                'carrier_id'         => $carrierVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 40' ---------------------------------

                                                    $calculationtypeValfail = 'Per 40 "Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    }

                                                    if($fortyArr[0] != 0 || $fortyArr[0] != 0.0){
                                                        $exists = null;
                                                        $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',$calculationtypeValfail)
                                                            ->where('ammount',$fortyVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->where('carrier_id',$carrierVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->first();
                                                        if(count($exists) == 0){
                                                            FailSurCharge::create([
                                                                'surcharge_id'       => $surchargeVal,
                                                                'port_orig'          => $originVal,
                                                                'port_dest'          => $destinyVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => $calculationtypeValfail,  //////
                                                                'ammount'            => $fortyVal, //////
                                                                'currency_id'        => $currencyVal, //////
                                                                'carrier_id'         => $carrierVal,
                                                                'differentiator'     => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 40'HC -------------------------------

                                                    $calculationtypeValfail = '40HC Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    }

                                                    if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                        $exists = null;
                                                        $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',$calculationtypeValfail)
                                                            ->where('ammount',$fortyhcVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->where('carrier_id',$carrierVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->first();
                                                        if(count($exists) == 0){
                                                            FailSurCharge::create([
                                                                'surcharge_id'       => $surchargeVal,
                                                                'port_orig'          => $originVal,
                                                                'port_dest'          => $destinyVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => $calculationtypeValfail,  //////
                                                                'ammount'            => $fortyhcVal, //////
                                                                'currency_id'        => $currencyVal, //////
                                                                'carrier_id'         => $carrierVal,
                                                                'differentiator'     => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 40'NOR -------------------------------

                                                    $calculationtypeValfail = '40\'NOR Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfornor;
                                                    }

                                                    if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                        $exists = null;
                                                        $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',$calculationtypeValfail)
                                                            ->where('ammount',$fortynorVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->where('carrier_id',$carrierVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->first();
                                                        if(count($exists) == 0){
                                                            FailSurCharge::create([
                                                                'surcharge_id'       => $surchargeVal,
                                                                'port_orig'          => $originVal,
                                                                'port_dest'          => $destinyVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => $calculationtypeValfail,  //////
                                                                'ammount'            => $fortynorVal, //////
                                                                'currency_id'        => $currencyVal, //////
                                                                'carrier_id'         => $carrierVal,
                                                                'differentiator'     => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 45' ---------------------------------

                                                    $calculationtypeValfail = '45\' Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforfive;
                                                    }

                                                    if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                        $exists = null;
                                                        $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                            ->where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('typedestiny_id',$typedestinyVal)
                                                            ->where('contract_id',$contractIdVal)
                                                            ->where('calculationtype_id',$calculationtypeValfail)
                                                            ->where('ammount',$fortyfiveVal)
                                                            ->where('currency_id',$currencyVal)
                                                            ->where('carrier_id',$carrierVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->first();
                                                        if(count($exists) == 0){
                                                            FailSurCharge::create([
                                                                'surcharge_id'       => $surchargeVal,
                                                                'port_orig'          => $originVal,
                                                                'port_dest'          => $destinyVal,
                                                                'typedestiny_id'     => $typedestinyVal,
                                                                'contract_id'        => $contractIdVal,
                                                                'calculationtype_id' => $calculationtypeValfail,  //////
                                                                'ammount'            => $fortyfiveVal, //////
                                                                'currency_id'        => $currencyVal, //////
                                                                'carrier_id'         => $carrierVal,
                                                                'differentiator'     => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
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
                        }
                    }
                    $i++;
                }

                //dd('Todo se cargo, surcharges o rates fallidos: '.$falli);
                //dd('Listo');
            });

        $nopalicaHs = Harbor::where('name','No Aplica')->get();
        $nopalicaCs = Country::where('name','No Aplica')->get();
        foreach($nopalicaHs as $nopalicaH){
            $nopalicaH = $nopalicaH['id'];
        }
        foreach($nopalicaCs as $nopalicaC){
            $nopalicaC = $nopalicaC['id'];
        }

        $failsurchargeS = FailSurCharge::where('contract_id','=',$requestobj['Contract_id'])->where('port_orig','LIKE','%No Aplica%')->delete();
        $failsurchargeS = FailSurCharge::where('contract_id','=',$requestobj['Contract_id'])->where('port_dest','LIKE','%No Aplica%')->delete();

        $surchargecollection = LocalCharge::where('contract_id',$requestobj['Contract_id'])
            ->whereHas('localcharcountries',function($query) use($nopalicaC){
                $query->where('country_dest',$nopalicaC)->orWhere('country_orig',$nopalicaC);
            })
            ->orWhereHas('localcharports',function($q) use($nopalicaH){
                $q->where('port_dest','=',$nopalicaH)->orWhere('port_orig',$nopalicaH);
            })->forceDelete();

        // dd($collection);
        //no borrar
        $contractData = new Contract();
        $contractData = Contract::find($requestobj['Contract_id']);
        $contractData->status = 'publish';
        $contractData->update();

        Storage::disk('FclImport')->Delete($NameFile);
        $FileTmp = new FileTmp();
        $FileTmp = FileTmp::where('name_file','=',$NameFile)->delete();

        $userNotifique = User::find($this->UserId);
        $message = 'The file imported was processed :' .$requestobj['Contract_id'] ;
        $userNotifique->notify(new SlackNotification($message));
        $userNotifique->notify(new N_general($userNotifique,$message)); 

    }
}