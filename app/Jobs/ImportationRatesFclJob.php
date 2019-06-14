<?php

namespace App\Jobs;

use Excel;
use App\User;
use App\Rate;
use PrvRates;
use PrvHarbor;
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
use App\Notifications\N_general;
use Illuminate\Support\Facades\Storage;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportationRatesFclJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request  = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requestobj = $this->request;
        $errors = 0;
        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('FclImport')
                   ->url($requestobj['FileName']),function($reader) use($requestobj,$errors) {
                       $reader->noHeading = true;
                       //$reader->ignoreEmpty();
                       $currency        = "Currency";
                       $twenty          = "20'";
                       $forty           = "40'";
                       $fortyhc         = "40'HC";
                       $fortynor        = "40'NOR";
                       $fortyfive       = "45'";
                       $origin          = "origin";
                       $originExc       = "Origin";
                       $destiny         = "destiny";
                       $destinyExc      = "Destiny";
                       $carrier         = "Carrier";
                       $scheduleTExc    = "Schedule_Type";
                       $transittimeExc  = "Transit_Time";
                       $viaExc          = "Via";
                       $scheduleinfo    = "scheduleinfo";

                       $statustypecurren        = "statustypecurren";
                       $statusexistfortynor     = "existfortynor";
                       $statusexistfortyfive    = "existfortyfive";

                       $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':','1','2','3','4','5','6','7','8','9','0'];

                       $i = 1;
                       foreach($reader->get() as $read){


                           //$originMultps      = [];
                           // $destinyMultps     = [];

                           if($i != 1){

                               //--------------- CARGADOR DE ARREGLO ORIGEN DESTINO MULTIPLES ----------------------------
                               //--- ORIGIN ------------------------------------------------------
                               if($requestobj['existorigin'] == true){
                                   $originMultps = [0];
                               } else {
                                   $originMultps = explode('|',$read[$requestobj[$originExc]]);
                               }
                               //--- DESTINY -----------------------------------------------------

                               if($requestobj['existdestiny'] == true){
                                   $destinyMultps = [0];
                               } else {
                                   $destinyMultps = explode('|',$read[$requestobj[$destinyExc]]);
                               }


                               foreach($originMultps as $originMult){
                                   foreach($destinyMultps as $destinyMult){

                                       $carrierVal          = '';
                                       $originVal           = '';
                                       $destinyVal          = '';
                                       $origenFL            = '';
                                       $destinyFL           = '';
                                       $currencyVal         = '';
                                       $twentyVal           = '';
                                       $fortyVal            = '';
                                       $fortyhcVal          = '';
                                       $fortynorVal         = '';
                                       $fortyfiveVal        = '';
                                       $originResul         = '';
                                       $destinResul         = '';
                                       $currencResul        = '';
                                       $carrierResul        = '';
                                       $scheduleTResul      = null;
                                       $transittimeResul    = 0;
                                       $viaResul            = null;

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
                                       $scheduleTBol    = false;
                                       $transittimeBol  = false;
                                       $viaBol          = false;
                                       $values          = true;

                                       $rqScheduleinfoBol = $requestobj[$scheduleinfo];

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
                                           //$originVal = $read[$requestobj[$originExc]];// hacer validacion de puerto en DB
                                           $originVal = trim($originMult);// hacer validacion de puerto en DB
                                           $resultadoPortOri = PrvHarbor::get_harbor($originVal);
                                           if($resultadoPortOri['boolean']){
                                               $origExiBol = true;    
                                           }
                                           $originVal  = $resultadoPortOri['puerto'];


                                       }
                                       //---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
                                       if($requestobj['existdestiny'] == true){
                                           $destinyBol = true;
                                           $destiExitBol = true; //segundo boolean para verificar campos errados
                                           $randons = $requestobj[$destiny];
                                       } else {
                                           //$destinyVal = $read[$requestobj[$destinyExc]];// hacer validacion de puerto en DB
                                           $destinyVal = trim($destinyMult);// hacer validacion de puerto en DB
                                           $resultadoPortDes = PrvHarbor::get_harbor($destinyVal);
                                           if($resultadoPortDes['boolean']){
                                               $destiExitBol = true;    
                                           }
                                           $destinyVal  = $resultadoPortDes['puerto'];
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
                                                       $curreExiBol = false;
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

                                       if( $origExiBol == true && $destiExitBol  == true
                                          && $carriExitBol  == true && $curreExiBol   == true 
                                          && $twentyExiBol  == true && $fortyExiBol   == true 
                                          && $twentyExiBol  == true && $fortynorExiBol == true
                                          && $fortyfiveExiBol == true && $scheduleTBol == true
                                          && $transittimeBol == true && $viaBol == true
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

                                                   $exRate = null;
                                                   $exRate = Rate::where('origin_port',$originVal)
                                                       ->where('destiny_port',$destinyVal)
                                                       ->where('carrier_id',$carrierVal)
                                                       ->where('contract_id',$requestobj['Contract_id'])
                                                       ->where('twuenty',$twentyVal)
                                                       ->where('forty',$fortyVal)
                                                       ->where('fortyhc',$fortyhcVal)
                                                       ->where('fortynor',$fortynorVal)
                                                       ->where('fortyfive',$fortyfiveVal)
                                                       ->where('currency_id',$currencyVal)
                                                       ->where('schedule_type_id',$scheduleTResul)
                                                       ->where('transit_time',$transittimeResul)
                                                       ->where('via',$viaResul)
                                                       ->get();
                                                   if(count($exRate) == 0){
                                                       Rate::create([
                                                           'origin_port'        => $originVal,
                                                           'destiny_port'       => $destinyVal,
                                                           'carrier_id'         => $carrierVal,
                                                           'contract_id'        => $requestobj['Contract_id'],
                                                           'twuenty'            => $twentyVal,
                                                           'forty'              => $fortyVal,
                                                           'fortyhc'            => $fortyhcVal,
                                                           'fortynor'           => $fortynorVal,
                                                           'fortyfive'          => $fortyfiveVal,
                                                           'currency_id'        => $currencyVal,
                                                           'schedule_type_id'   => $scheduleTResul,
                                                           'transit_time'       => $transittimeResul,
                                                           'via'                => $viaResul
                                                       ]);
                                                   }
                                               }
                                           }else {
                                               // fila por puerto, sin expecificar origen ni destino manualmente
                                               $exRate = null;
                                               $exRate = Rate::where('origin_port',$originVal)
                                                   ->where('destiny_port',$destinyVal)
                                                   ->where('carrier_id',$carrierVal)
                                                   ->where('contract_id',$requestobj['Contract_id'])
                                                   ->where('twuenty',$twentyVal)
                                                   ->where('forty',$fortyVal)
                                                   ->where('fortyhc',$fortyhcVal)
                                                   ->where('fortynor',$fortynorVal)
                                                   ->where('fortyfive',$fortyfiveVal)
                                                   ->where('currency_id',$currencyVal)
                                                   ->where('schedule_type_id',$scheduleTResul)
                                                   ->where('transit_time',$transittimeResul)
                                                   ->where('via',$viaResul)
                                                   ->get();
                                               if(count($exRate) == 0){
                                                   Rate::create([
                                                       'origin_port'        => $originVal,
                                                       'destiny_port'       => $destinyVal,
                                                       'carrier_id'         => $carrierVal,
                                                       'contract_id'        => $requestobj['Contract_id'],
                                                       'twuenty'            => $twentyVal,
                                                       'forty'              => $fortyVal,
                                                       'fortyhc'            => $fortyhcVal,
                                                       'fortynor'           => $fortynorVal,
                                                       'fortyfive'          => $fortyfiveVal,
                                                       'currency_id'        => $currencyVal,
                                                       'schedule_type_id'   => $scheduleTResul,
                                                       'transit_time'       => $transittimeResul,
                                                       'via'                => $viaResul
                                                   ]);
                                               }
                                           }
                                       } else {
                                           // fail rates

                                           if( $scheduleTBol == true && $rqScheduleinfoBol == true){
                                               $scheduleTResul = ScheduleType::find($scheduleTResul);
                                               $scheduleTResul = $scheduleTResul['name'];
                                           }
                                           //**********************
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

                                                       $exRate = null;
                                                       $exRate = FailRate::where('origin_port',$originVal)
                                                           ->where('destiny_port',$destinyVal)
                                                           ->where('carrier_id',$carrierVal)
                                                           ->where('contract_id',$requestobj['Contract_id'])
                                                           ->where('twuenty',$twentyVal)
                                                           ->where('forty',$fortyVal)
                                                           ->where('fortyhc',$fortyhcVal)
                                                           ->where('fortynor',$fortynorVal)
                                                           ->where('fortyfive',$fortyfiveVal)
                                                           ->where('currency_id',$currencyVal)
                                                           ->where('schedule_type',$scheduleTResul)
                                                           ->where('transit_time',$transittimeResul)
                                                           ->where('via',$viaResul)
                                                           ->get();
                                                       if(count($exRate) == 0){

                                                           FailRate::create([
                                                               'origin_port'        => $originVal,
                                                               'destiny_port'       => $destinyVal,
                                                               'carrier_id'         => $carrierVal,
                                                               'contract_id'        => $requestobj['Contract_id'],
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
                                                       $originExits = Harbor::find($originVal);
                                                       $originVal = $originExits->name;                                       
                                                   }
                                                   if($destiExitBol == true){  
                                                       $destinyExits = Harbor::find($destinyVal);
                                                       $destinyVal = $destinyExits->name;
                                                   }
                                                   $exRate = null;
                                                   $exRate = FailRate::where('origin_port',$originVal)
                                                       ->where('destiny_port',$destinyVal)
                                                       ->where('carrier_id',$carrierVal)
                                                       ->where('contract_id',$requestobj['Contract_id'])
                                                       ->where('twuenty',$twentyVal)
                                                       ->where('forty',$fortyVal)
                                                       ->where('fortyhc',$fortyhcVal)
                                                       ->where('fortynor',$fortynorVal)
                                                       ->where('fortyfive',$fortyfiveVal)
                                                       ->where('currency_id',$currencyVal)
                                                       ->where('schedule_type',$scheduleTResul)
                                                       ->where('transit_time',$transittimeResul)
                                                       ->where('via',$viaResul)
                                                       ->get();
                                                   if(count($exRate) == 0){
                                                       FailRate::create([
                                                           'origin_port'        => $originVal,
                                                           'destiny_port'       => $destinyVal,
                                                           'carrier_id'         => $carrierVal,
                                                           'contract_id'        => $requestobj['Contract_id'],
                                                           'twuenty'            => $twentyVal,
                                                           'forty'              => $fortyVal,
                                                           'fortyhc'            => $fortyhcVal,
                                                           'fortynor'           => $fortynorVal,
                                                           'fortyfive'          => $fortyfiveVal,
                                                           'currency_id'        => $currencyVal,
                                                           'schedule_type'      => $scheduleTResul,
                                                           'transit_time'       => $transittimeResul,
                                                           'via'                => $viaResul
                                                       ]); //*/
                                                       $errors++;
                                                   }
                                               }
                                           }
                                           //*/
                                           //dd('para');
                                       }
                                   }
                               }
                           }
                           $i++;
                       }

                       Storage::disk('FclImport')->delete($requestobj['FileName']);
                       FileTmp::where('contract_id','=',$requestobj['Contract_id'])->delete();                   
                   });
        $contract = new Contract();
        $contract = Contract::find($requestobj['Contract_id']);
        $contract->status = 'publish';
        $contract->update();
    }
}
