<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Company;
use App\CompanyPrice;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\Price;
use App\User;
use Illuminate\Support\Collection as Collection;
use App\Contract;
use App\Rate;
use App\Harbor;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
use App\MergeTag;
use GoogleMaps;
use App\Inland;
use App\Carrier;
use App\TermAndCondition;
use App\TermsPort;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Schedule;
use App\Incoterm;
use App\SaleTerm;
use App\EmailTemplate;
use App\PackageLoad;
use App\Mail\SendQuotePdf;
use App\Quote;
use App\SearchRate;
use App\SearchPort;
use EventIntercom;
use App\Repositories\Schedules;

class QuoteAutoController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

  public function hideContainer($equipmentForm){

    $equipment = new Collection();
    $hidden20 = 'hidden';
    $hidden40 = 'hidden';
    $hidden40hc = 'hidden';
    $hidden40nor = 'hidden';
    $hidden45 = 'hidden';

    // Clases para reordenamiento de la tabla y ajuste 
    $originClass = 'col-md-2';
    $destinyClass = 'col-md-1';
    $dataOrigDest = 'col-md-3';
    $countEquipment = count($equipmentForm);
    $countEquipment = 5 - $countEquipment;

    if($countEquipment == 1 ){
      $originClass = 'col-md-3';
      $destinyClass = 'col-md-1';
      $dataOrigDest = 'col-md-4';

    }

    if($countEquipment == 2 ){

      $originClass = 'col-md-3';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-5';
    }
    if($countEquipment == 3){

      $originClass = 'col-md-4';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-6';
    }
    if($countEquipment == 4){

      $originClass = 'col-md-5';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-7';
    }


    foreach($equipmentForm as $val){

      if($val == '20'){
        $hidden20 = '';
      }
      if($val == '40'){
        $hidden40 = '';
      }
      if($val == '40hc'){
        $hidden40hc = '';
      }
      if($val == '40nor'){
        $hidden40nor = '';
      }
      if($val == '45'){
        $hidden45 = '';
      }
    }


    $equipment->put('originClass',$originClass);
    $equipment->put('destinyClass',$destinyClass);
    $equipment->put('dataOrigDest',$dataOrigDest);
    $equipment->put('20',$hidden20);
    $equipment->put('40',$hidden40);
    $equipment->put('40hc',$hidden40hc);
    $equipment->put('40nor',$hidden40nor);
    $equipment->put('45',$hidden45);

    return($equipment);


  }

  public function skipPluck($pluck)
  {
    $skips = ["[","]","\""];
    return str_replace($skips, '',$pluck);
  }
  public function ratesCurrency($id,$typeCurrency){
    $rates = Currency::where('id','=',$id)->get();
    foreach($rates as $rate){
      if($typeCurrency == "USD"){
        $rateC = $rate->rates;
      }else{
        $rateC = $rate->rates_eur;
      }
    }
    return $rateC;
  }
  public function search()
  {


    $company_user_id=\Auth::user()->company_user_id;
    $incoterm = Incoterm::pluck('name','id');
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }

    $harbors = Harbor::get()->pluck('display_name','id_complete');

    $countries = Country::all()->pluck('name','id');


    $prices = Price::all()->pluck('name','id');
    $company_user = User::where('id',\Auth::id())->first();
    if(count($company_user->companyUser)>0) {
      $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
    }else{
      $currency_name = '';
    }
    $currencies = Currency::all()->pluck('alphacode','id');
    return view('quotesv2/search',  compact('companies','countries','harbors','prices','company_user','currencies','currency_name','incoterm'));


  }

  public function processSearch(Request $request){


    //Variables del usuario conectado
    $company_user_id=\Auth::user()->company_user_id;
    $user_id =  \Auth::id();

    //Variables para cargar el  Formulario
    $form  = $request->all();
    $incoterm = Incoterm::pluck('name','id');
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }

    $harbors = Harbor::get()->pluck('display_name','id_complete');
    $countries = Country::all()->pluck('name','id');
    $prices = Price::all()->pluck('name','id');
    $company_user = User::where('id',\Auth::id())->first();

    if(count($company_user->companyUser)>0) {
      $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
    }else{
      $currency_name = '';
    }
    $currencies = Currency::all()->pluck('alphacode','id');


    //Settings de la compañia 
    $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
    $typeCurrency =  $company->companyUser->currency->alphacode ;
    $idCurrency = $company->companyUser->currency_id;

    // Request Formulario
    foreach($request->input('originport') as $origP){

      $infoOrig = explode("-", $origP);
      $origin_port[] = $infoOrig[0];
      $origin_country[] = $infoOrig[1];
    }
    foreach($request->input('destinyport') as $destP){

      $infoDest = explode("-", $destP);
      $destiny_port[] = $infoDest[0];
      $destiny_country[] = $infoDest[1];
    }
    $equipment = $request->input('equipment');
    $delivery_type = $request->input('delivery_type');
    $price_id = $request->input('price_id');
    $modality_inland = $request->modality;
    $company_id = $request->input('company_id_quote');
    // Fecha Contrato
    $dateRange =  $request->input('date');
    $dateRange = explode("/",$dateRange);
    $dateSince = $dateRange[0];
    $dateUntil = $dateRange[1];

    //Collection Equipment Dinamico
    $equipmentHides = $this->hideContainer($equipment);
    //Colecciones 

    //Markups Freight
    $freighPercentage = 0;
    $freighAmmount = 0;
    $freighMarkup= 0;
    // Markups
    $fclMarkup = Price::whereHas('company_price', function($q) use($price_id) {
      $q->where('price_id', '=',$price_id);
    })->with('freight_markup','local_markup','inland_markup')->get();

    foreach($fclMarkup as $freight){
      // Freight
      $fclFreight = $freight->freight_markup->where('price_type_id','=',1);
      // Valor de porcentaje
      $freighPercentage = $this->skipPluck($fclFreight->pluck('percent_markup'));
      // markup currency
      $markupFreightCurre =  $this->skipPluck($fclFreight->pluck('currency'));
      // markup con el monto segun la moneda
      $freighMarkup = $this->ratesCurrency($markupFreightCurre,$typeCurrency);
      // Objeto con las propiedades del currency
      $markupFreightCurre = Currency::find($markupFreightCurre);
      $markupFreightCurre = $markupFreightCurre->alphacode;
      // Monto original
      $freighAmmount =  $this->skipPluck($fclFreight->pluck('fixed_markup'));
      // monto aplicado al currency
      $freighMarkup = $freighAmmount / $freighMarkup;
      $freighMarkup = number_format($freighMarkup, 2, '.', '');

    }

    // Fin Markups

    // Consulta base de datos rates
    $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($dateSince,$dateUntil,$user_id,$company_user_id,$company_id)
        {
          $q->whereHas('contract_user_restriction', function($a) use($user_id){
            $a->where('user_id', '=',$user_id);
          })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function($q) use($dateSince,$dateUntil,$user_id,$company_user_id,$company_id)
                     {
                       $q->whereHas('contract_company_restriction', function($b) use($company_id){
                         $b->where('company_id', '=',$company_id);
                       })->orDoesntHave('contract_company_restriction');
                     })->whereHas('contract', function($q) use($dateSince,$dateUntil,$company_user_id){
      $q->where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->where('company_user_id','=',$company_user_id);
    });
    $arreglo = $arreglo->get();

    $formulario = $request;
    $array20 = array('2','4','5'); // id  calculation type 2 = per 20 , 4= per teu , 5 per container
    $array40 =  array('1','4','5'); // id  calculation type 2 = per 40 
    $array40Hc= array('3','4','5'); // id  calculation type 3 = per 40HC 
    $array40Nor = array('7','4','5');  // id  calculation type 7 = per 40NOR
    $array45 = array('8','4','5');  // id  calculation type 8 = per 45

    $arrarContainers =  array('1','2','3','4','7','8'); 




    foreach($arreglo as $data){
      $collectionRate = new Collection();
      $totalFreight = 0;
      $totalRates = 0;
      $totalT20 = 0;
      $totalT40 = 0;
      $totalT40hc = 0;
      $totalT40nor = 0;
      $totalT45 = 0;
      $totalT  = 0 ;
      $carrier[] = $data->carrier_id;
      $orig_port = array($data->origin_port);
      $dest_port = array($data->destiny_port);
      $rateDetail = new collection();
      $collectionFreight = new collection();
      $arregloRate =  array();
      $arregloFreight =  array();



      $rateC = $this->ratesCurrency($data->currency->id,$typeCurrency);

      // Rates 
      foreach($equipment as $containers){
        //Calculo para los diferentes tipos de contenedores
        if($containers == '20'){
          $markup20 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->twuenty,$typeCurrency,$containers);
          $array20Detail = array('price20' => $data->twuenty, 'currency20' => $data->currency->alphacode ,'idCurrency20' => $data->currency_id);
          $totalT20 += $markup20['monto20'] / $rateC;
          $array20T = array_merge($array20Detail,$markup20);
          $arregloRate = array_merge($array20T,$arregloRate);

        }
        if($containers == '40'){
          $markup40 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->forty,$typeCurrency,$containers);
          $array40Detail = array('price40' => $data->forty, 'currency40' => $data->currency->alphacode ,'idCurrency40' => $data->currency_id);
          $totalT40 += $markup40['monto40']  / $rateC;
          $array40T = array_merge($array40Detail,$markup40);
          $arregloRate = array_merge($array40T,$arregloRate); 

        }
        if($containers == '40hc'){
          $markup40hc = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortyhc,$typeCurrency,$containers);
          $array40hcDetail = array('price40hc' => $data->fortyhc, 'currency40hc' => $data->currency->alphacode ,'idCurrency40hc' => $data->currency_id);
          $totalT40hc += $markup40hc['monto40hc'] / $rateC;
          $array40hcT = array_merge($array40hcDetail,$markup40hc);
          $arregloRate = array_merge($array40hcT,$arregloRate); 

        }
        if($containers == '40nor'){
          $markup40nor = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortynor,$typeCurrency,$containers);
          $array40norDetail = array('price40nor' => $data->fortynor, 'currency40nor' => $data->currency->alphacode ,'idCurrency40nor' => $data->currency_id);
          $totalT40nor += $markup40nor['monto40nor'] / $rateC;
          $array40norT = array_merge($array40norDetail,$markup40nor);
          $arregloRate = array_merge($array40norT,$arregloRate); 

        }
        if($containers == '45'){
          $markup45 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortyfive,$typeCurrency,$containers);
          $array45Detail = array('price45' => $data->fortyfive, 'currency45' => $data->currency->alphacode ,'idCurrency45' => $data->currency_id);
          $totalT45 += $markup45['monto45'] / $rateC;
          $array45T = array_merge($array45Detail,$markup45);
          $arregloRate = array_merge($array45T,$arregloRate); 

        }
      }


      // id de los port  ALL
      array_push($orig_port,1485);
      array_push($dest_port,1485);
      // id de los carrier ALL 
      $carrier_all = 26;
      array_push($carrier,$carrier_all);
      // Id de los paises 
      array_push($origin_country,250);
      array_push($destiny_country,250);

      // ################### Calculos local  Charges #############################



      $localChar = LocalCharge::where('contract_id','=',$data->contract_id)->whereHas('localcharcarriers', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
        $query->whereHas('localcharports', function($q) use($orig_port,$dest_port) {
          $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
        })->orwhereHas('localcharcountries', function($q) use($origin_country,$destiny_country) {
          $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
        });
      })->with('localcharports.portOrig','localcharcarriers.carrier','currency','surcharge.saleterm')->orderBy('typedestiny_id','calculationtype_id','surchage_id')->get();




      foreach($localChar as $local){

        $rateMount = $this->ratesCurrency($local->currency->id,$typeCurrency);
        // Condicion para enviar los terminos de venta o compra
        if(isset($local->surcharge->saleterm->name)){
          $terminos = $local->surcharge->saleterm->name;
        }else{
          $terminos = $local->surcharge->name;
        }

        $cont = 0;
        foreach($local->localcharcarriers as $localCarrier){

          if($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id ==  $carrier_all ){

            if($local->typedestiny_id == '1'){

            }
            if($local->typedestiny_id == '2'){

            }
            if($local->typedestiny_id == '3'){

              if(in_array($local->calculationtype_id, $array20)){

                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $local->ammount, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' );

                $freighTwuenty["freight"] = $arregloFreight;
                $collectionFreight->push($arregloFreight);
              }
              if(in_array($local->calculationtype_id, $array40)){
                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $local->ammount, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' );
                $freighTwuenty["freight"] = $arregloFreight;
                $collectionFreight->push($arregloFreight);
              }

            }
          }
        }
      }

      $collectionFreight = $collectionFreight->groupBy([
        'type',
        function ($item)  {

          return $item['surcharge_name'];
        },
      ], $preserveKeys = true);



      $collect = new collection();
      $monto = 0;
      foreach($collectionFreight as $test){
        $total = count($test);
        foreach($test as $otro){

          if($total == 2 ){
            foreach($otro as $locura){
              $monto += $locura['monto']; 
            }
            $collect->push($monto);
            $monto = 0;
          }else{
            foreach($otro as $locura){
              $collect->push($locura['monto']); 
              $monto = 0;
            }
          }
        }
      }



      dd($collect);


      // ################## Fin local Charges        #############################

      $totalRates += $totalT;
      $array = array('type'=>'Ocean Freight','detail'=>'Per Container','subtotal'=>$totalRates, 'total' =>$totalRates." ". $typeCurrency , 'idCurrency' => $data->currency_id,'currency_rate' => $data->currency->alphacode );
      $array = array_merge($array,$arregloRate);
      $collectionRate->push($array);
      $data->setAttribute('rates',$collectionRate);
      // Valores totales por contenedor
      $data->setAttribute('total20', number_format($totalT20, 2, '.', ''));
      $data->setAttribute('total40', number_format($totalT40, 2, '.', ''));
      $data->setAttribute('total40hc', number_format($totalT40hc, 2, '.', ''));
      $data->setAttribute('total40nor', number_format($totalT40nor, 2, '.', ''));
      $data->setAttribute('total45', number_format($totalT45, 2, '.', ''));

    }
    //  dd($arreglo);

    return view('quotesv2/search',  compact('arreglo','form','companies','quotes','countries','harbors','prices','company_user','currencies','currency_name','incoterm','equipmentHides'));

  }

  public function freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$monto,$typeCurrency,$type){

    if($freighPercentage != 0){
      $freighPercentage = intval($freighPercentage);
      $markup = ( $monto *  $freighPercentage ) / 100 ;
      $markup = number_format($markup, 2, '.', '');
      $monto += $markup ;
      number_format($monto, 2, '.', '');
      $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $markup, "typemarkup".$type => "$typeCurrency ($freighPercentage%)", "monto".$type => $monto) ;
    }else{

      $markup =trim($freighAmmount);
      $monto += $freighMarkup;
      $monto = number_format($monto, 2, '.', '');
      $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $freighMarkup, "typemarkup".$type => $typeCurrency,"monto".$type => $monto) ;
    }

    return $arraymarkup;

  }
}
