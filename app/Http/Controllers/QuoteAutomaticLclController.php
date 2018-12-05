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
    $formulario = $request;

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
    $arreglo = RateLcl::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($date,$company_user_id){
      $q->where('validity', '<=',$date)->where('expire', '>=', $date)->where('company_user_id','=',$company_user_id);
    })->get();



    foreach($arreglo as $data){
      $totalFreight = 0;
      $totalRates = 0;
      $rateC = $this->ratesCurrency($data->currency->id,$typeCurrency);
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

        $array = array('type'=>'Shipment', 'cantidad' => $weight,'detail'=>'Shipment', 'price' => $priceRate, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalT , 'total' =>$totalT." ". $typeCurrency , 'idCurrency' => $data->currency_id);
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

        $totalT = $subtotalT;
        $totalT =  number_format($totalT, 2, '.', '');
        $totalFreight += $totalT;
        $totalRates += $totalT;
        $array = array('type'=>'Package', 'cantidad' =>$weight ,'detail'=>'Package', 'price' => $priceRate, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalT , 'total' =>$totalT." ". $typeCurrency , 'idCurrency' => $data->currency_id);
        $array = array_merge($array,$arraymarkupT);
        $collectionRate->push($array);
        $data->setAttribute('montF',$array);
      }

      $totalQuote = $totalFreight;// $totalFreight + $totalOrigin + $totalDestiny;
      $totalQuoteSin = number_format($totalQuote, 2, ',', '');

      $data->setAttribute('rates',$collectionRate);

      $collectionGloOrig = new Collection();
      $collectionGloDest = new Collection();
      $collectionGloFreight = new Collection();
      $collectionOrig = new Collection();
      $collectionDest = new Collection();
      $collectionFreight = new Collection();
      $FreightCharges = new Collection();
      $totalOrigin ="";
      $totalDestiny ="";
      $inlandDestiny = new Collection();
      $inlandOrigin = new Collection();
      $totalChargeOrig = "";
      $totalChargeDest ="";
      $totalInland = "";

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
    //dd($form);
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
