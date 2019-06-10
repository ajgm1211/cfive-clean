<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ContractLcl;
use App\RateLcl;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\User;
use Illuminate\Support\Collection as Collection;
use App\Carrier;
use App\Harbor;
use App\Price;
use App\LocalChargeLcl;
use App\LocalCharCarrierLcl;
use App\LocalCharPortLcl;
use App\GlobalChargeLcl;
use App\GlobalCharCarrierLcl;
use App\GlobalCharPortLcl;

class QuoteAutomaticLclController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index(Request $request)
  {

    // Variables de configuracion
    $company_user_id=\Auth::user()->company_user_id;
    $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
    $typeCurrency =  $company->companyUser->currency->alphacode ;
    $idCurrency = $company->companyUser->currency_id;
    $weight = $request->input("chargeable_weight");
    $weight =  number_format($weight, 2, '.', '');
    // se separa el pais y el puerto 
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

    // Variables de formulario
    $delivery_type = $request->input('delivery_type');
    $price_id = $request->input('price_id');
    $date =  $request->input('date');
    $user_id =  \Auth::id();
    $company_user_id =  \Auth::user()->company_user_id;
    $company_id = $request->input('company_id_quote');


    //Markups

    $fclMarkup = Price::whereHas('company_price', function($q) use($price_id) {
      $q->where('price_id', '=',$price_id);
    })->with('freight_markup','local_markup','inland_markup')->get();
    $freighPercentage = 0;
    $freighAmmount = 0;
    $localPercentage = 0;
    $localAmmount = 0;
    $inlandPercentage = 0;
    $inlandAmmount = 0;
    $freighMarkup= 0;
    $localMarkup = 0;
    $inlandMarkup =0;
    $markupFreightCurre = $typeCurrency;
    $markupLocalCurre = $typeCurrency;
    $markupInlandCurre = $typeCurrency;
    foreach($fclMarkup as $freight){
      // Freight
      $fclFreight = $freight->freight_markup->where('price_type_id','=',2);
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

      // Local y global
      $fclLocal = $freight->local_markup->where('price_type_id','=',2);
      // markup currency


      if($request->modality == "1"){
        $markupLocalCurre =  $this->skipPluck($fclLocal->pluck('currency_export'));
        // valor de la conversion segun la moneda
        $localMarkup = $this->ratesCurrency($markupLocalCurre,$typeCurrency);
        // Objeto con las propiedades del currency por monto fijo
        $markupLocalCurre = Currency::find($markupLocalCurre);
        $markupLocalCurre = $markupLocalCurre->alphacode;
        // En caso de ser Porcentaje
        $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_export')));
        // Monto original
        $localAmmount =  intval($this->skipPluck($fclLocal->pluck('fixed_markup_export')));
        // monto aplicado al currency
        $localMarkup = $localAmmount / $localMarkup;
        $localMarkup = number_format($localMarkup, 2, '.', '');
      }else{
        $markupLocalCurre =  $this->skipPluck($fclLocal->pluck('currency_import'));
        // valor de la conversion segun la moneda
        $localMarkup = $this->ratesCurrency($markupLocalCurre,$typeCurrency);
        // Objeto con las propiedades del currency por monto fijo
        $markupLocalCurre = Currency::find($markupLocalCurre);
        $markupLocalCurre = $markupLocalCurre->alphacode;
        // en caso de ser porcentake
        $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_import')));
        // monto original
        $localAmmount =  intval($this->skipPluck($fclLocal->pluck('fixed_markup_import')));
        // monto aplicado al currency
        $localMarkup = $localAmmount / $localMarkup;
        $localMarkup = number_format($localMarkup, 2, '.', '');
      }
      // Inlands
      $fclInland = $freight->inland_markup->where('price_type_id','=',2);
      if($request->modality == "1"){
        $markupInlandCurre =  $this->skipPluck($fclInland->pluck('currency_export'));
        // valor de la conversion segun la moneda
        $inlandMarkup = $this->ratesCurrency($markupInlandCurre,$typeCurrency);
        // Objeto con las propiedades del currency por monto fijo
        $markupInlandCurre = Currency::find($markupInlandCurre);
        $markupInlandCurre = $markupInlandCurre->alphacode;
        // en caso de ser porcentake
        $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_export')));
        // Monto original
        $inlandAmmount =  intval($this->skipPluck($fclInland->pluck('fixed_markup_export')));
        // monto aplicado al currency
        $inlandMarkup = $inlandAmmount / $inlandMarkup;
        $inlandMarkup = number_format($inlandMarkup, 2, '.', '');


      }else{
        $markupInlandCurre =  $this->skipPluck($fclInland->pluck('currency_import'));

        // valor de la conversion segun la moneda
        $inlandMarkup = $this->ratesCurrency($markupInlandCurre,$typeCurrency);

        // Objeto con las propiedades del currency por monto fijo
        $markupInlandCurre = Currency::find($markupInlandCurre);

        $markupInlandCurre = $markupInlandCurre->alphacode;
        // en caso de ser porcentake
        $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_import')));
        // monto original
        $inlandAmmount =  intval($this->skipPluck($fclInland->pluck('fixed_markup_import')));
        // monto aplicado al currency
        $inlandMarkup = $inlandAmmount / $inlandMarkup;

        $inlandMarkup = number_format($inlandMarkup, 2, '.', '');

      }
    }

    //Colecciones

    $collectionRate = new Collection();





    // Rates LCL
    $arreglo = RateLcl::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id)
        {
          $q->whereHas('contract_user_restriction', function($a) use($user_id){
            $a->where('user_id', '=',$user_id);
          })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id)
                     {
                       $q->whereHas('contract_company_restriction', function($b) use($company_id){
                         $b->where('company_id', '=',$company_id);
                       })->orDoesntHave('contract_company_restriction');
                     })->whereHas('contract', function($q) use($date,$company_user_id){

      $q->where('validity', '<=',$date)->where('expire', '>=', $date)->where('company_user_id','=',$company_user_id);
    })->get();



    foreach($arreglo as $data){
      $totalFreight = 0;
      $FreightCharges = 0;
      $totalRates = 0;
      $totalOrigin = 0;
      $totalDestiny =0;
      $totalQuote= 0;
      $totalAmmount = 0;
      $collectionOrig = new Collection();
      $collectionDest = new Collection();
      $collectionFreight = new Collection();
      $collectionGloOrig = new Collection();
      $collectionGloDest = new Collection();
      $collectionGloFreight = new Collection();
      $collectionRate = new Collection();
      $rateC = $this->ratesCurrency($data->currency->id,$typeCurrency);
      $subtotal = 0;


      $inlandDestiny = new Collection();
      $inlandOrigin = new Collection();
      $totalChargeOrig = 0;
      $totalChargeDest =0;
      $totalInland = 0;

      if($request->input('total_weight') != null ) {

        $subtotalT = $weight *  $data->uom;
        $totalT = ( $weight *  $data->uom) / $rateC ;
        $priceRate =   $data->uom;

        if($subtotalT < $data->minimum){
          $subtotalT = $data->minimum;
          $totalT =    $subtotalT / $rateC ;
          $priceRate =  $data->minimum / $weight;
          $priceRate =  number_format($priceRate, 2, '.', '');
        }

        // MARKUPS
        if($freighPercentage != 0){
          $freighPercentage = intval($freighPercentage);
          $markup = ( $totalT *  $freighPercentage ) / 100 ;
          $markup = number_format($markup, 2, '.', '');
          $totalT += $markup ;
          $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{

          $markup =trim($freighAmmount);
          $markup = number_format($markup, 2, '.', '');
          $totalT += $freighMarkup;
          $arraymarkupT = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
        }

        $totalT =  number_format($totalT, 2, '.', '');
        $totalFreight += $totalT;
        $totalRates += $totalT;

        $array = array('type'=>'Ocean Freight', 'cantidad' => $weight,'detail'=>'W/M', 'price' => $priceRate, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalT , 'total' =>$totalT." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupT);
        $collectionRate->push($array);
        $data->setAttribute('montF',$array);
      }
      // POR PAQUETE
      if($request->input('total_weight_pkg') != null ) {
        $subtotalT = $weight *  $data->uom;
        $totalT = ( $weight *  $data->uom) / $rateC ;
        $priceRate =   $data->uom;


        if($subtotalT < $data->minimum){
          $subtotalT = $data->minimum;
          $totalT =    $subtotalT / $rateC ;
          $priceRate =  $data->minimum / $weight;
          $priceRate =  number_format($priceRate, 2, '.', '');
        }
        // MARKUPS
        if($freighPercentage != 0){
          $freighPercentage = intval($freighPercentage);
          $markup = ( $totalT *  $freighPercentage ) / 100 ;
          $markup = number_format($markup, 2, '.', '');
          $totalT += $markup ;
          $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
        }else{

          $markup =trim($freighAmmount);
          $markup = number_format($markup, 2, '.', '');
          $totalT += $freighMarkup;
          $arraymarkupT = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
        }


        $totalT =  number_format($totalT, 2, '.', '');
        $totalFreight += $totalT;
        $totalRates += $totalT;
        $array = array('type'=>'Ocean Freight', 'cantidad' =>$weight ,'detail'=>'W/M', 'price' => $priceRate, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalT , 'total' =>$totalT." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupT);
        $collectionRate->push($array);
        $data->setAttribute('montF',$array);
      }


      $data->setAttribute('rates',$collectionRate);


      $orig_port = array($data->origin_port);
      $dest_port = array($data->destiny_port);
      $carrier[] = $data->carrier_id;

      // id de los port  ALL
      array_push($orig_port,1485);
      array_push($dest_port,1485);
      // id de los carrier ALL 
      $carrier_all = 26;
      array_push($carrier,$carrier_all);
      // Id de los paises 
      array_push($origin_country,250);
      array_push($destiny_country,250);

      $arrayBlHblShip = array('1','2','3'); // id  calculation type 1 = HBL , 2=  Shipment , 3 = BL
      $arraytonM3 = array('4'); //  calculation type 4 = Per ton/m3
      $arraytonCompli = array('6','7'); //  calculation type 4 = Per ton/m3
      $arrayPerTon = array('5'); //  calculation type 5 = Per  TON 

      // Local charges 
      $localChar = LocalChargeLcl::where('contractlcl_id','=',$data->contractlcl_id)->whereHas('localcharcarrierslcl', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
        $query->whereHas('localcharportslcl', function($q) use($orig_port,$dest_port) {
          $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
        })->orwhereHas('localcharcountrieslcl', function($q) use($origin_country,$destiny_country) {
          $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
        });
      })->with('localcharportslcl.portOrig','localcharcarrierslcl.carrier','currency','surcharge.saleterm')->get();




      foreach($localChar as $local){

        $rateMount = $this->ratesCurrency($local->currency->id,$typeCurrency);
        //Totales peso y volumen
        if($request->input('total_weight') != null){
          $totalW = $request->input('total_weight') / 1000;
          $totalV = $request->input('total_volume');
        }else{            
          $totalW = $request->input('total_weight_pkg') / 1000; ;
          $totalV = $request->input('total_volume_pkg');
        }

        // Condicion para enviar los terminos de venta o compra
        if(isset($local->surcharge->saleterm->name)){
          $terminos = $local->surcharge->saleterm->name;
        }else{
          $terminos = $local->surcharge->name;
        }
        if(in_array($local->calculationtypelcl_id, $arrayBlHblShip)){
          $cantidadT = 1;
          foreach($local->localcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
              if($local->typedestiny_id == '1'){
                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                $arregloOrig = array_merge($arregloOrig,$arraymarkupPC);
                $origPer["origin"] =$arregloOrig;
                $collectionOrig->push($origPer);
              }
              if($local->typedestiny_id == '2'){
                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT  , 'idCurrency' => $local->currency->id  );
                $arregloDest = array_merge($arregloDest,$arraymarkupPC);
                $destPer["destiny"] = $arregloDest;
                $collectionDest->push($destPer);
              }
              if($local->typedestiny_id == '3'){
                $subtotal_local =  $local->ammount;
                $totalAmmount =  $local->ammount  / $rateMount;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                //$totalAmmount =  $local->ammout  / $rateMount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                $arregloPC = array_merge($arregloPC,$arraymarkupPC);
                $freightPer["freight"] = $arregloPC;
                $collectionFreight->push($freightPer);
              }
            }
          }
        }
        if(in_array($local->calculationtypelcl_id, $arraytonM3)){
          $cantidadT = $weight;
          foreach($local->localcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
              if($local->typedestiny_id == '1'){
                $subtotal_local =  $weight * $local->ammount;
                $totalAmmount =  ( $weight * $local->ammount)  / $rateMount;
                $mont = $local->ammount;
                if($subtotal_local < $local->minimum){
                  $subtotal_local = $local->minimum;
                  $totalAmmount =    $subtotal_local / $rateMount ;
                  $mont = $local->minimum / $weight;
                  $mont = number_format($mont, 2, '.', '');
                }

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $weight, 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                $arregloOrig = array_merge($arregloOrig,$arraymarkupTon);

                $origTon["origin"] =$arregloOrig;
                $collectionOrig->push($origTon);
              }
              if($local->typedestiny_id == '2'){
                $subtotal_local =  $weight * $local->ammount;
                $totalAmmount =  ( $weight * $local->ammount)  / $rateMount;
                $mont = $local->ammount;
                if($subtotal_local < $local->minimum){
                  $subtotal_local = $local->minimum;
                  $totalAmmount =    $subtotal_local / $rateMount ;
                  $mont = $local->minimum / $weight;
                  $mont = number_format($mont, 2, '.', '');
                }
                //$cantidadT = 1;
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $weight, 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT  , 'idCurrency' => $local->currency->id  );
                $arregloDest = array_merge($arregloDest,$arraymarkupTon);
                $destTon["destiny"] = $arregloDest;
                $collectionDest->push($destTon);
              }
              if($local->typedestiny_id == '3'){
                $subtotal_local =  $weight * $local->ammount;
                $totalAmmount =  ( $weight * $local->ammount)  / $rateMount;
                $mont = $local->ammount;
                if($subtotal_local < $local->minimum){
                  $subtotal_local = $local->minimum;
                  $totalAmmount =    $subtotal_local / $rateMount ;
                  $mont = $local->minimum / $weight;
                  $mont = number_format($mont, 2, '.', '');
                }

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                //$totalAmmount =  $local->ammout  / $rateMount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $weight , 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                $arregloPC = array_merge($arregloPC,$arraymarkupTon);
                $freightTon["freight"] = $arregloPC;
                $collectionFreight->push($freightTon);
              }
            }
          }
        }
        if(in_array($local->calculationtypelcl_id, $arrayPerTon)){

          foreach($local->localcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
              if($local->typedestiny_id == '1'){
                $subtotal_local =  $totalW * $local->ammount;
                $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                $mont = $local->ammount;
                $unidades = $totalW;
                if($subtotal_local < $local->minimum){
                  $subtotal_local = $local->minimum;
                  $totalAmmount =    $subtotal_local / $rateMount ;
                  $mont = $local->minimum / $totalW;
                  $mont = number_format($mont, 2, '.', '');

                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades , 'idCurrency' => $local->currency->id );
                $arregloOrig = array_merge($arregloOrig,$arraymarkupTon);

                $origTon["origin"] =$arregloOrig;
                $collectionOrig->push($origTon);
              }
              if($local->typedestiny_id == '2'){
                $subtotal_local =  $totalW * $local->ammount;
                $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                $mont = $local->ammount;
                $unidades = $totalW;
                if($subtotal_local < $local->minimum){
                  $subtotal_local = $local->minimum;
                  $totalAmmount =    $subtotal_local / $rateMount ;
                  $mont = $local->minimum / $totalW;
                  $mont = number_format($mont, 2, '.', '');

                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  , 'idCurrency' => $local->currency->id  );
                $arregloDest = array_merge($arregloDest,$arraymarkupTon);
                $destTon["destiny"] = $arregloDest;
                $collectionDest->push($destTon);
              }
              if($local->typedestiny_id == '3'){

                $subtotal_local =  $totalW * $local->ammount;
                $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                $mont = $local->ammount;
                $unidades = $totalW;
                if($subtotal_local < $local->minimum){
                  $subtotal_local = $local->minimum;
                  $totalAmmount =    $subtotal_local / $rateMount ;
                  $mont = $local->minimum / $totalW;
                  $mont = number_format($mont, 2, '.', '');

                }

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                //$totalAmmount =  $local->ammout  / $rateMount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades , 'idCurrency' => $local->currency->id  );
                $arregloPC = array_merge($arregloPC,$arraymarkupTon);
                $freightTon["freight"] = $arregloPC;
                $collectionFreight->push($freightTon);
              }
            }
          }
        }
        if(in_array($local->calculationtypelcl_id, $arraytonCompli)){


          foreach($local->localcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
              if($local->typedestiny_id == '1'){
                if($local->calculationtypelcl_id == '7'){
                  $subtotal_local =  $totalV * $local->ammount;
                  $totalAmmount =  ( $totalV * $local->ammount)  / $rateMount;
                  $mont = $local->ammount;
                  $unidades = $totalV;
                  if($subtotal_local < $local->minimum){
                    $subtotal_local = $local->minimum;
                    $totalAmmount =    $subtotal_local / $rateMount ;
                    $mont = $local->minimum / $totalV;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }else{
                  $subtotal_local =  $totalW * $local->ammount;
                  $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                  $mont = $local->ammount;
                  $unidades = $totalW;
                  if($subtotal_local < $local->minimum){
                    $subtotal_local = $local->minimum;
                    $totalAmmount =    $subtotal_local / $rateMount ;
                    $mont = $local->minimum / $totalW;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }

                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $mont , 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades , 'idCurrency' => $local->currency->id );
                $arregloOrig = array_merge($arregloOrig,$arraymarkupTon);
                $dataOrig[] = $arregloOrig;

              }
              if($local->typedestiny_id == '2'){
                if($local->calculationtypelcl_id == '7'){
                  $subtotal_local =  $totalV * $local->ammount;
                  $totalAmmount =  ( $totalV * $local->ammount)  / $rateMount;
                  $mont = $local->ammount;
                  $unidades = $totalV;
                  if($subtotal_local < $local->minimum){
                    $subtotal_local = $local->minimum;
                    $totalAmmount =    $subtotal_local / $rateMount ;
                    $mont = $local->minimum / $totalV;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }else{
                  $subtotal_local =  $totalW * $local->ammount;
                  $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                  $mont = $local->ammount;
                  $unidades = $totalW;
                  if($subtotal_local < $local->minimum){
                    $subtotal_local = $local->minimum;
                    $totalAmmount =    $subtotal_local / $rateMount ;
                    $mont = $local->minimum / $totalW;
                    $mont = number_format($mont, 2, '.', '');
                  }
                }

                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }

                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $mont , 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  , 'idCurrency' => $local->currency->id  );
                $arregloDest = array_merge($arregloDest,$arraymarkupTon);
                $dataDest[] = $arregloDest;

              }
              if($local->typedestiny_id == '3'){
                if($local->calculationtypelcl_id == '7'){
                  $subtotal_local =  $totalV * $local->ammount;
                  $totalAmmount =  ( $totalV * $local->ammount)  / $rateMount;
                  $mont = $local->ammount;
                  $unidades = $totalV;
                  if($subtotal_local < $local->minimum){
                    $subtotal_local = $local->minimum;
                    $totalAmmount =    $subtotal_local / $rateMount ;
                    $mont = $local->minimum / $totalV;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }else{
                  $subtotal_local =  $totalW * $local->ammount;
                  $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                  $mont = $local->ammount;
                  $unidades = $totalW;
                  if($subtotal_local < $local->minimum){
                    $subtotal_local = $local->minimum;
                    $totalAmmount =    $subtotal_local / $rateMount ;
                    if($totalW < 1){
                      $mont = $local->minimum * $totalW;
                    }else{
                      $mont = $local->minimum / $totalW;
                    }
                  }
                }

                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupTon = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                //$totalAmmount =  $local->ammout  / $rateMount;
                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $mont , 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades , 'idCurrency' => $local->currency->id  );
                $arregloPC = array_merge($arregloPC,$arraymarkupTon);
                $dataFreight[] = $arregloPC;

              }
            }
          }
        }
      }// Fin del calculo de los local charges 


      //############ Global Charges   ####################

      $globalChar = GlobalChargeLcl::where('validity', '<=',$date)->where('expire', '>=', $date)->whereHas('globalcharcarrierslcl', function($q) use($carrier) {
        $q->whereIn('carrier_id', $carrier);
      })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
        $query->whereHas('globalcharportlcl', function($q) use($orig_port,$dest_port) {
          $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
        })->orwhereHas('globalcharcountrylcl', function($q) use($origin_country,$destiny_country) {
          $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
        });
      })->where('company_user_id','=',$company_user_id)->with('globalcharportlcl.portOrig','globalcharportlcl.portDest','globalcharcarrierslcl.carrier','currency','surcharge.saleterm')->get();


      foreach($globalChar as $global){
        $rateMountG = $this->ratesCurrency($global->currency->id,$typeCurrency);
        if($request->input('total_weight') != null){
          $totalW = $request->input('total_weight') / 1000;
          $totalV = $request->input('total_volume');
        }else{            
          $totalW = $request->input('total_weight_pkg') / 1000; ;
          $totalV = $request->input('total_volume_pkg');
        }

        // Condicion para enviar los terminos de venta o compra
        if(isset($global->surcharge->saleterm->name)){
          $terminos = $global->surcharge->saleterm->name;
        }else{
          $terminos = $global->surcharge->name;
        }

        if(in_array($global->calculationtypelcl_id, $arrayBlHblShip)){
          $cantidadT = 1;
          foreach($global->globalcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){
              if($global->typedestiny_id == '1'){
                $subtotal_global =  $global->ammount;
                $totalAmmount =  $global->ammount  / $rateMountG;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => '-' , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\' Global '  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                $arregloOrig = array_merge($arregloOrig,$arraymarkupT);
                $origGlo["origin"] = $arregloOrig;
                $collectionGloOrig->push($origGlo);
              }
              if($global->typedestiny_id == '2'){

                $subtotal_global =  $global->ammount;
                $totalAmmount =  $global->ammount  / $rateMountG;


                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => '-' , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                $arregloDest = array_merge($arregloDest,$arraymarkupT);
                $destGlo["destiny"] = $arregloDest;
                $collectionGloDest->push($destGlo);
              }
              if($global->typedestiny_id == '3'){
                $subtotal_global =  $global->ammount;
                $totalAmmount =  $global->ammount  / $rateMountG;

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => '-' , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                $freighGlo["freight"] =$arregloFreight;
                $collectionGloFreight->push($freighGlo);

              }
            }
          }
        }
        if(in_array($global->calculationtypelcl_id, $arraytonM3)){
          $cantidadT = $weight;
          foreach($global->globalcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){
              if($global->typedestiny_id == '1'){
                $subtotal_global =  $weight * $global->ammount;
                $totalAmmount =  ( $weight * $global->ammount)  / $rateMountG;
                $mont = $global->ammount;
                if($subtotal_global < $global->minimum){
                  $subtotal_global = $global->minimum;
                  $totalAmmount =    $subtotal_global / $rateMountG ;
                  $mont = $global->minimum / $weight;
                  $mont = number_format($mont, 2, '.', '');
                }

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $cantidadT , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Ship/Hbl/Bl'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                $arregloOrig = array_merge($arregloOrig,$arraymarkupT);
                $origGlo["origin"] = $arregloOrig;
                $collectionGloOrig->push($origGlo);
              }
              if($global->typedestiny_id == '2'){

                $subtotal_global =  $weight * $global->ammount;
                $totalAmmount =  ( $weight * $global->ammount)  / $rateMountG;
                $mont = $global->ammount;
                if($subtotal_global < $global->minimum){
                  $subtotal_global = $global->minimum;
                  $totalAmmount =    $subtotal_global / $rateMountG ;
                  $mont = $global->minimum / $weight;
                  $mont = number_format($mont, 2, '.', '');
                }

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $cantidadT , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Ship/Hbl/Bl'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                $arregloDest = array_merge($arregloDest,$arraymarkupT);
                $destGlo["destiny"] = $arregloDest;
                $collectionGloDest->push($destGlo);
              }
              if($global->typedestiny_id == '3'){
                $subtotal_global =  $weight * $global->ammount;
                $totalAmmount =  ( $weight * $global->ammount)  / $rateMountG;
                $mont = $global->ammount;
                if($subtotal_global < $global->minimum){
                  $subtotal_global = $global->minimum;
                  $totalAmmount =    $subtotal_global / $rateMountG ;
                  $mont = $global->minimum / $weight;
                  $mont = number_format($mont, 2, '.', '');
                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $cantidadT , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Ship/Hbl/Bl'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                $freighGlo["freight"] =$arregloFreight;
                $collectionGloFreight->push($freighGlo);

              }
            }
          }
        }
        if(in_array($global->calculationtypelcl_id, $arrayPerTon)){

          foreach($global->globalcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){
              if($global->typedestiny_id == '1'){

                $subtotal_global =  $totalW * $global->ammount;
                $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                $mont = $global->ammount;
                $unidades = $totalW;
                if($subtotal_global < $global->minimum){
                  $subtotal_global = $global->minimum;
                  $totalAmmount =    $subtotal_global / $rateMountG ;
                  $mont = $global->minimum / $totalW;
                  $mont = number_format($mont, 2, '.', '');

                }

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalOrigin += $totalAmmount ;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Per Ton'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'idCurrency' => $global->currency->id);
                $arregloOrig = array_merge($arregloOrig,$arraymarkupT);
                $origGlo["origin"] = $arregloOrig;
                $collectionGloOrig->push($origGlo);
              }
              if($global->typedestiny_id == '2'){

                $subtotal_global =  $totalW * $global->ammount;
                $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                $mont = $global->ammount;
                $unidades = $totalW;
                if($subtotal_global < $global->minimum){
                  $subtotal_global = $global->minimum;
                  $totalAmmount =    $subtotal_global / $rateMountG ;
                  $mont = $global->minimum / $totalW;
                  $mont = number_format($mont, 2, '.', '');

                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalDestiny += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Per Ton'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'idCurrency' => $global->currency->id);
                $arregloDest = array_merge($arregloDest,$arraymarkupT);
                $destGlo["destiny"] = $arregloDest;
                $collectionGloDest->push($destGlo);
              }
              if($global->typedestiny_id == '3'){

                $subtotal_global =  $totalW * $global->ammount;
                $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                $mont = $global->ammount;
                $unidades = $totalW;
                if($subtotal_global < $global->minimum){
                  $subtotal_global = $global->minimum;
                  $totalAmmount =    $subtotal_global / $rateMountG ;
                  $mont = $global->minimum / $totalW;
                  $mont = number_format($mont, 2, '.', '');

                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }
                $totalFreight += $totalAmmount;
                $FreightCharges += $totalAmmount;
                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Per Ton', 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'idCurrency' => $global->currency->id);
                $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                $freighGlo["freight"] =$arregloFreight;
                $collectionGloFreight->push($freighGlo);

              }
            }
          }
        }
        if(in_array($global->calculationtypelcl_id, $arraytonCompli)){

          foreach($global->globalcharcarrierslcl as $carrierGlobal){
            if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){
              if($global->typedestiny_id == '1'){

                if($global->calculationtypelcl_id == '7'){
                  $subtotal_global =  $totalV * $global->ammount;
                  $totalAmmount =  ( $totalV * $global->ammount)  / $rateMountG;
                  $mont = $global->ammount;
                  $unidades = $totalV;
                  if($subtotal_global < $global->minimum){
                    $subtotal_global = $global->minimum;
                    $totalAmmount =    $subtotal_global / $rateMountG ;
                    $mont = $global->minimum / $totalV;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }else{
                  $subtotal_global =  $totalW * $global->ammount;
                  $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                  $mont = $global->ammount;
                  $unidades = $totalW;
                  if($subtotal_global < $global->minimum){
                    $subtotal_global = $global->minimum;
                    $totalAmmount =    $subtotal_global / $rateMountG ;
                    $mont = $global->minimum / $totalW;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }

                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }

                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Per Ton'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'idCurrency' => $global->currency->id);
                $arregloOrig = array_merge($arregloOrig,$arraymarkupT);
                $dataGOrig[] = $arregloOrig;
              }
              if($global->typedestiny_id == '2'){

                if($global->calculationtypelcl_id == '7'){
                  $subtotal_global =  $totalV * $global->ammount;
                  $totalAmmount =  ( $totalV * $global->ammount)  / $rateMountG;
                  $mont = $global->ammount;
                  $unidades = $totalV;
                  if($subtotal_global < $global->minimum){
                    $subtotal_global = $global->minimum;
                    $totalAmmount =    $subtotal_global / $rateMountG ;
                    $mont = $global->minimum / $totalV;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }else{
                  $subtotal_global =  $totalW * $global->ammount;
                  $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                  $mont = $global->ammount;
                  $unidades = $totalW;
                  if($subtotal_global < $global->minimum){
                    $subtotal_global = $global->minimum;
                    $totalAmmount =    $subtotal_global / $rateMountG ;
                    $mont = $global->minimum / $totalW;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }

                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Per Ton'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'idCurrency' => $global->currency->id);
                $arregloDest = array_merge($arregloDest,$arraymarkupT);
                $dataGDest[] = $arregloDest;
              }
              if($global->typedestiny_id == '3'){

                if($global->calculationtypelcl_id == '7'){
                  $subtotal_global =  $totalV * $global->ammount;
                  $totalAmmount =  ( $totalV * $global->ammount)  / $rateMountG;
                  $mont = $global->ammount;
                  $unidades = $totalV;
                  if($subtotal_global < $global->minimum){
                    $subtotal_global = $global->minimum;
                    $totalAmmount =    $subtotal_global / $rateMountG ;
                    $mont = $global->minimum / $totalV;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }else{
                  $subtotal_global =  $totalW * $global->ammount;
                  $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                  $mont = $global->ammount;
                  $unidades = $totalW;
                  if($subtotal_global < $global->minimum){
                    $subtotal_global = $global->minimum;
                    $totalAmmount =    $subtotal_global / $rateMountG ;
                    $mont = $global->minimum / $totalW;
                    $mont = number_format($mont, 2, '.', '');

                  }
                }
                // MARKUP
                if($localPercentage != 0){
                  $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $markup ;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                }else{
                  $markup =$localAmmount;
                  $markup = number_format($markup, 2, '.', '');
                  $totalAmmount += $localMarkup;
                  $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                }

                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'Per Ton', 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'idCurrency' => $global->currency->id);
                $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                $dataGFreight[] = $arregloFreight;

              }
            }
          }
        }

      }

      //############ Fin Global Charges ##################

      // Locales 
      if(!empty($dataOrig)){
        $collectOrig = Collection::make($dataOrig);

        $m3tonOrig= $collectOrig->groupBy('surcharge_name')->map(function($item) use($collectionOrig,&$totalOrigin,$data,$carrier_all){
          $carrArreglo = array($data->carrier_id,$carrier_all);
          $test = $item->where('totalAmmount', $item->max('totalAmmount'))->wherein('carrier_id',$carrArreglo)->first();
          if(!empty($test)){
            $totalA = explode(' ',$test['totalAmmount']);
            $totalOrigin += $totalA[0];  

            $arre['origin'] = $test;
            $collectionOrig->push($arre);
            return $test;
          }
        });
      }

      if(!empty($dataDest)){
        $collectDest = Collection::make($dataDest);
        $m3tonDest= $collectDest->groupBy('surcharge_name')->map(function($item) use($collectionDest,&$totalDestiny,$data,$carrier_all){
          $carrArreglo = array($data->carrier_id,$carrier_all);
          $test = $item->where('totalAmmount', $item->max('totalAmmount'))->wherein('carrier_id',$carrArreglo)->first();
          if(!empty($test)){
            $totalA = explode(' ',$test['totalAmmount']);
            $totalDestiny += $totalA[0];  
            $arre['destiny'] = $test;
            $collectionDest->push($arre);
            return $test;
          }
        });
      }

      if(!empty($dataFreight)){

        $collectFreight = Collection::make($dataFreight);
        $m3tonFreight= $collectFreight->groupBy('surcharge_name')->map(function($item) use($collectionFreight,&$totalFreight,$data,$carrier_all){
          $carrArreglo = array($data->carrier_id,$carrier_all);
          $test = $item->where('totalAmmount', $item->max('totalAmmount'))->wherein('carrier_id',$carrArreglo)->first();
          if(!empty($test)){
            $totalA = explode(' ',$test['totalAmmount']);
            $totalFreight += $totalA[0];  
            $arre['freight'] = $test;
            $collectionFreight->push($arre);
            return $test;
          }
        });
      }

      // Globales 
      if(!empty($dataGOrig)){
        $collectGOrig = Collection::make($dataGOrig);

        $m3tonGOrig= $collectGOrig->groupBy('surcharge_name')->map(function($item) use($collectionGloOrig,&$totalOrigin,$data,$carrier_all){
          $carrArreglo = array($data->carrier_id,$carrier_all);
          $test = $item->where('totalAmmount', $item->max('totalAmmount'))->wherein('carrier_id',$carrArreglo)->first();
          if(!empty($test)){
            $totalA = explode(' ',$test['totalAmmount']);
            $totalOrigin += $totalA[0];  

            $arre['origin'] = $test;
            $collectionGloOrig->push($arre);
            return $test;
          }
        });
      }

      if(!empty($dataGDest)){
        $collectGDest = Collection::make($dataGDest);
        $m3tonDestG= $collectGDest->groupBy('surcharge_name')->map(function($item) use($collectionGloDest,&$totalDestiny,$data,$carrier_all){
          $carrArreglo = array($data->carrier_id,$carrier_all);
          $test = $item->where('totalAmmount', $item->max('totalAmmount'))->wherein('carrier_id',$carrArreglo)->first();
          if(!empty($test)){
            $totalA = explode(' ',$test['totalAmmount']);
            $totalDestiny += $totalA[0];  
            $arre['destiny'] = $test;
            $collectionGloDest->push($arre);
            return $test;
          }
        });
      }

      if(!empty($dataGFreight)){

        $collectGFreight = Collection::make($dataGFreight);
        $m3tonFreightG= $collectGFreight->groupBy('surcharge_name')->map(function($item) use($collectionGloFreight,&$totalFreight,$data,$carrier_all){
          $carrArreglo = array($data->carrier_id,$carrier_all);
          $test = $item->where('totalAmmount', $item->max('totalAmmount'))->wherein('carrier_id',$carrArreglo)->first();
          if(!empty($test)){
            $totalA = explode(' ',$test['totalAmmount']);
            $totalFreight += $totalA[0];  
            $arre['freight'] = $test;
            $collectionGloFreight->push($arre);
            return $test;
          }
        });
      }

      //#######################################################################
      //Formato subtotales y operacion total quote
      $totalChargeOrig += $totalOrigin;
      $totalChargeDest += $totalDestiny;
      $totalFreight =  number_format($totalFreight, 2, '.', '');
      $FreightCharges =  number_format($FreightCharges, 2, '.', '');
      $totalOrigin  =  number_format($totalOrigin, 2, '.', '');
      $totalDestiny =  number_format($totalDestiny, 2, '.', '');
      $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
      $totalQuoteSin = number_format($totalQuote, 2, ',', '');


      $data->setAttribute('globalOrig',$collectionGloOrig);
      $data->setAttribute('globalDest',$collectionGloDest);
      $data->setAttribute('globalFreight',$collectionGloFreight);
      $data->setAttribute('localOrig',$collectionOrig);
      $data->setAttribute('localDest',$collectionDest);
      $data->setAttribute('localFreight',$collectionFreight);
      $data->setAttribute('totalFreight',$totalFreight);
      $data->setAttribute('freightCharges',$FreightCharges);
      $data->setAttribute('totalrates',$totalRates);
      $data->setAttribute('totalOrigin',$totalOrigin);
      $data->setAttribute('totalDestiny',$totalDestiny);
      $data->setAttribute('totalQuote',$totalQuote);
      // INLANDS
      $data->setAttribute('inlandDestiny',$inlandDestiny);
      $data->setAttribute('inlandOrigin',$inlandOrigin);
      $data->setAttribute('totalChargeOrig',$totalChargeOrig);
      $data->setAttribute('totalChargeDest',$totalChargeDest);
      $data->setAttribute('totalInland',$totalInland);
      //Total quote atributes
      $data->setAttribute('quoteCurrency',$typeCurrency);
      $data->setAttribute('totalQuoteSin',$totalQuoteSin);
      $data->setAttribute('idCurrency',$idCurrency);
      // SCHEDULES
      $data->setAttribute('schedulesFin',"");

    }
    $form  = $request->all();
    $objharbor = new Harbor();
    $harbor = $objharbor->all()->pluck('name','id');



    return view('quotation/lcl', compact('harbor','formulario','arreglo','form'));
    /*
    $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id)
        {
          $q->whereHas('contract_user_restriction', function($a) use($user_id){
            $a->where('user_id', '=',$user_id);
          })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id)
                     {
                       $q->whereHas('contract_company_restriction', function($b) use($company_id){
                         $b->where('company_id', '=',$company_id);
                       })->orDoesntHave('contract_company_restriction');
                     })->whereHas('contract', function($q) use($date,$company_user_id){
      $q->where('validity', '<=',$date)->where('expire', '>=', $date)->where('company_user_id','=',$company_user_id);
    });*/

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

  public function skipPluck($pluck)
  {
    $skips = ["[","]","\""];
    return str_replace($skips, '',$pluck);

  }



}
