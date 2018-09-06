<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Company;
use App\CompanyPrice;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\DestinationAmmount;
use App\DestinationAmount;
use App\FreightAmmount;
use App\OriginAmmount;
use App\OriginAmount;
use App\Price;
use App\Quote;
use App\User;
use Illuminate\Http\Request;
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
use App\Airport;
use GoogleMaps;
use App\Inland;
use App\Carrier;
use App\TermAndCondition;
use App\TermsPort;
use App\StatusQuote;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Schedule;
use App\Incoterm;
use App\SaleTerm;
use App\EmailTemplate;
use App\PackageLoad;
use App\Airline;
use App\Mail\SendQuotePdf;
class QuoteController extends Controller
{

    public function index(){
        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = Quote::where('owner',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->get();
        }else{
            $quotes = Quote::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->get();
        }

<<<<<<< HEAD
    $companies = Company::all()->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('business_name','id');
    $countries = Country::all()->pluck('name','id');
    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
    }else{
      $company_user='';
      $currency_cfg = '';
    }
    return view('quotes/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'currency_cfg'=>$currency_cfg]);
  }

  public function automatic(){
    $quotes = Quote::all();
    $company_user_id=\Auth::user()->company_user_id;
    $incoterm = Incoterm::pluck('name','id');
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
=======
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('business_name','id');
        $countries = Country::all()->pluck('name','id');
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }else{
            $company_user='';
            $currency_cfg = '';
        }
        return view('quotes/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'currency_cfg'=>$currency_cfg]);
>>>>>>> remotes/origin/julio
    }

    public function automatic(){
        $quotes = Quote::all();
        $company_user_id=\Auth::user()->company_user_id;
        $incoterm = Incoterm::pluck('name','id');
        if(\Auth::user()->hasRole('subuser')){
            $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
                $q->where('user_id',\Auth::user()->id);
            })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
        }else{
            $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
        }

        $harbors = Harbor::all()->pluck('display_name','id');
        $countries = Country::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        $company_user = User::where('id',\Auth::id())->first();
        if(count($company_user->companyUser)>0) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
        }else{
            $currency_name = '';
        }
        $currencies = Currency::all()->pluck('alphacode','id');
        return view('quotation/new2', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$company_user,'currencies'=>$currencies,'currency_name'=>$currency_name,'incoterm' => $incoterm]);
    }

    public function test(Request $request){
        $info =$request->input('info');
        $info = json_decode($info);
        $form =$request->input('form');
        $schedules = $request->input('schedules');
        $form = json_decode($form);
        $companiesInfo = Company::where('id','=',$form->company_id_quote)->first();  
        $contactInfo = Contact::where('id','=',$form->contact_id)->first();  
        $company_user_id=\Auth::user()->company_user_id;
        $quotes = Quote::all();
        $company_user=CompanyUser::find($company_user_id);
        $companies=Company::where('company_user_id',$company_user->id)->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('display_name','id');
        $countries = Country::all()->pluck('name','id');
        $currency = Currency::all()->pluck('alphacode','id');
        $prices = Price::all()->pluck('name','id');
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $currencies = Currency::all();
        $currency_cfg = Currency::find($company_user->currency_id);
        if($company_user_id){
            $company_user=CompanyUser::find($company_user_id);
            $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            $companies=Company::where('company_user_id',$company_user->id)->pluck('business_name','id');
            $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->pluck('name','id');
        }
        if($company_user){
            $currencies = Currency::pluck('alphacode','id');
            $currency_cfg = Currency::find($company_user->currency_id);
        }
        if(\Auth::user()->company_user_id && $currency_cfg != ''){
            if($currency_cfg->alphacode=='USD'){
                $exchange = Currency::where('api_code_eur','EURUSD')->first();
            }else{
                $exchange = Currency::where('api_code','USDEUR')->first();
            }
        }
        if(\Auth::user()->company_user_id){
            $terms_origin = TermsPort::where('port_id',$info->origin_port)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_destination = TermsPort::where('port_id',$info->destiny_port)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
        }
        return view('quotation/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$user,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'info'=> $info,'form' => $form ,'currency' => $currency , 'schedules' => $schedules ,'exchange'=>$exchange ,'email_templates'=>$email_templates,'user'=>$user,'companyInfo' => $companiesInfo , 'contactInfo' => $contactInfo ,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination]);
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

    // COTIZACION AUTOMATICA

    public function listRate(Request $request)
    {
        $company_user_id=\Auth::user()->company_user_id;
        $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
        $typeCurrency =  $company->companyUser->currency->alphacode ;
        $idCurrency = $company->companyUser->currency_id;
        //dd($company);
        $origin_port = $request->input('originport');
        $destiny_port = $request->input('destinyport');
        $delivery_type = $request->input('delivery_type');
        //$typeCurrency = 'USD';
        // valores de los markup en Freight 
        $price_id = $request->input('price_id');
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
        // Calculo de los markups
        foreach($fclMarkup as $freight){
            // Freight 
            $fclFreight = $freight->freight_markup->where('price_type_id','=',1);
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
            $fclLocal = $freight->local_markup->where('price_type_id','=',1);
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
                // en caso de ser porcentake
                $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_import')));
                // monto original
                $localAmmount =  intval($this->skipPluck($fclLocal->pluck('fixed_markup_import')));
                // monto aplicado al currency
                $localMarkup = $localAmmount / $localMarkup;
                $localMarkup = number_format($localMarkup, 2, '.', '');
            }
            // Inlands 
            $fclInland = $freight->inland_markup->where('price_type_id','=',1);
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
        //--------------------------------------
        // Calculo de los inlands
        if($delivery_type == "2" || $delivery_type == "4" ){
            $inlands = Inland::whereHas('inlandports', function($q) use($destiny_port) {
                $q->whereIn('port', $destiny_port);
            })->where('company_user_id','=',$company_user_id)->with('inlandports.ports','inlanddetails.currency')->get();
            foreach($inlands as $inlandsValue){
                foreach($inlandsValue->inlandports as $ports){
                    $monto = 0;
                    $temporal = 0;
                    if (in_array($ports->ports->id, $destiny_port )) {
                        $origin =  $ports->ports->coordinates;
                        $destination = $request->input('destination_address');
                        $response = GoogleMaps::load('directions')
                            ->setParam([
                                'origin'          => $origin,
                                'destination'     => $destination,
                                'mode' => 'driving' ,
                                'language' => 'es',
                            ])->get();
                        $var = json_decode($response);
                        foreach($var->routes as $resp) {
                            foreach($resp->legs as $dist) {
                                $km = explode(" ",$dist->distance->text);
                                foreach($inlandsValue->inlanddetails as $details){
                                    $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);
                                    if($details->type == 'twuenty' && $request->input('twuenty') != "0"){
                                        $distancia = intval($km[0]);
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += ($request->input('twuenty') * $details->ammount) / $rateI;
                                            //  echo $monto;
                                            //echo '<br>';

                                        }
                                    }
                                    if($details->type == 'forty' && $request->input('forty') != "0"){
                                        $distancia = intval($km[0]);
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += ($request->input('forty') * $details->ammount) / $rateI;
                                        }
                                    }
                                    if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){
                                        $distancia = intval($km[0]);
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += ($request->input('fortyhc') * $details->ammount) / $rateI;
                                        }
                                    }
                                }
                                // MARKUPS 
                                if($inlandPercentage != 0){
                                    $markup = ( $monto *  $inlandPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $monto += $markup ;
                                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                                }else{
                                    $markup =$inlandAmmount;
                                    $markup = number_format($markup, 2, '.', '');

                                    $monto += $inlandMarkup;
                                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                                }
                                $monto = number_format($monto, 2, '.', '');
                                if($monto > 0){
                                    $arregloInland =  array("prov_id" => $inlandsValue->id ,"provider" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $km[0] , "monto" => $monto ,'type' => 'Destiny Port To Door','type_currency' => $typeCurrency ,'idCurrency' => $inlandsValue->currency_id );
                                    $arregloInland = array_merge($arraymarkupT,$arregloInland);   
                                    $data[] =$arregloInland;
                                }
                            }
                        }
                    } // if ports
                }// foreach ports
            }//foreach inlands
            if(!empty($data)){
                $collection = Collection::make($data);
                // dd($collection); //  completo 
                $inlandDestiny = $collection->groupBy('port_id')->map(function($item){
                    $test = $item->where('monto', $item->min('monto'))->first();
                    return $test;
                });
                // dd($inlandDestiny); // filtraor por el minimo 
            }
        }
        if($delivery_type == "3" || $delivery_type == "4" ){
            $inlands = Inland::whereHas('inlandports', function($q) use($origin_port) {
                $q->whereIn('port', $origin_port);
            })->where('company_user_id','=',$company_user_id)->with('inlandports.ports','inlanddetails.currency')->get();
            foreach($inlands as $inlandsValue){
                foreach($inlandsValue->inlandports as $ports){
                    $monto = 0;
                    $temporal = 0;
                    if (in_array($ports->ports->id, $origin_port )) {
                        $origin = $request->input('origin_address');
                        $destination =  $ports->ports->coordinates;
                        $response = GoogleMaps::load('directions')
                            ->setParam([
                                'origin'          => $origin,
                                'destination'     => $destination,
                                'mode' => 'driving' ,
                                'language' => 'es',
                            ])->get();
                        $var = json_decode($response);
                        foreach($var->routes as $resp) {
                            foreach($resp->legs as $dist) {
                                $km = explode(" ",$dist->distance->text);
                                foreach($inlandsValue->inlanddetails as $details){
                                    $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);
                                    if($details->type == 'twuenty' && $request->input('twuenty') != "0"){
                                        $distancia = intval($km[0]);
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += ($request->input('twuenty') * $details->ammount) / $rateI ;
                                        }
                                    }
                                    if($details->type == 'forty' && $request->input('forty') != "0"){
                                        $distancia = intval($km[0]);
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += ($request->input('forty') * $details->ammount)  / $rateI;
                                        }
                                    }
                                    if($details->type == 'fortyhc' && $request->input('fortyhc') != "0"){
                                        $distancia = intval($km[0]);
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $monto += ($request->input('fortyhc') * $details->ammount) / $rateI;
                                        }
                                    }
                                }
                                // MARKUPS 
                                if($inlandPercentage != 0){
                                    $markup = ( $monto *  $inlandPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $monto += $markup ;
                                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => "$typeCurrency ($inlandPercentage%)") ;
                                }else{
                                    $markup =$inlandAmmount;
                                    $markup = number_format($markup, 2, '.', '');
                                    $monto += $inlandMarkup;
                                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre) ;
                                }
                                $monto = number_format($monto, 2, '.', '');
                                if($monto > 0){
                                    $arregloInland = array("prov_id" => $inlandsValue->id ,"provider" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,"km" => $km[0] , "monto" => $monto ,'type' => 'Origin Port To Door','type_currency' => $typeCurrency ,'idCurrency' => $inlandsValue->currency_id  );
                                    $arregloInland = array_merge($arregloInland,$arraymarkupT);
                                    $dataOrig[] = $arregloInland;
                                }
                            }
                        }
                    } // if ports
                }// foreach ports
            }//foreach inlands
            if(!empty($dataOrig)){
                $collectionOrig = Collection::make($dataOrig);
                // dd($collection); //  completo 
                $inlandOrigin= $collectionOrig->groupBy('port_id')->map(function($item){
                    $test = $item->where('monto', $item->min('monto'))->first();
                    return $test;
                });
                // dd($inlandOrigin); // filtraor por el minimo 
            }
        }
        // Fin del calculo de los inlands 
        $date =  $request->input('date');
        $user_id =  \Auth::id();
        $company_user_id =  \Auth::user()->company_user_id;
        $company_id = $request->input('company_id_quote');

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
        });



        /*
    $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($date,$user_id,$company_user_id,$company_id) 
    {
      $q->where('validity', '<=',$date)->where('expire', '>=', $date)->where('company_user_id','=',$company_user_id);
    });*/



        // Se agregan las condiciones para evitar traer rates con ceros dependiendo de lo seleccionado por el usuario
        if($request->input('twuenty') != "0" ){
            $arreglo->where('twuenty' , '!=' , "0");

        }
        if($request->input('forty') != "0"){
            $arreglo->where('forty' , '!=' , "0");
        }

        if($request->input('fortyhc') != "0"){
            $arreglo->where('fortyhc' , '!=' , "0");
        }

        $arreglo = $arreglo->get();

        // Fin condiciones del cero



        $formulario = $request;
        $array20 = array('2','4','5');
        $array40 =  array('1','4','5');
        $array40Hc= array('3','4','5');
        $collectionLocal = new Collection();
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
            $orig_port = array($data->origin_port);
            $dest_port = array($data->destiny_port);
            $carrier[] = $data->carrier_id;
            // Calculo de los rates
            if($request->input('twuenty') != "0") {
                $subtotalT = $formulario->twuenty *  $data->twuenty;
                $totalT = ($formulario->twuenty *  $data->twuenty) / $rateC ;
                // MARKUPS 
                if($freighPercentage != 0){
                    $freighPercentage = intval($freighPercentage);
                    $markup = ( $totalT *  $freighPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $markup ;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
                }else{

                    $markup =trim($freighAmmount);
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $freighMarkup;
                    $arraymarkupT = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
                }
                $totalT =  number_format($totalT, 2, '.', '');
                $totalFreight += $totalT;
                $totalRates += $totalT;
                $array = array('type'=>'Ocean Freight 20 ', 'cantidad' => $formulario->twuenty,'detail'=>'Container 20', 'price' => $data->twuenty, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalT , 'total' =>$totalT." ". $typeCurrency , 'idCurrency' => $data->currency_id );
                $array = array_merge($array,$arraymarkupT);
                $collectionRate->push($array);
                $data->setAttribute('montT',$array);
            }
            if($request->input('forty') != "0") {
                $subtotalF = $formulario->forty *  $data->forty;
                $totalF = ($formulario->forty *  $data->forty)  / $rateC ;
                // MARKUPS 
                if($freighPercentage != 0){
                    $freighPercentage = intval($freighPercentage);
                    $markup = ( $totalF *  $freighPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalF += $markup ;
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $freighMarkup,  "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
                }else{
                    $markup =trim($freighAmmount);
                    $markup = number_format($markup, 2, '.', '');
                    $totalF += $freighMarkup;
                    $arraymarkupF = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
                }
                $totalF =  number_format($totalF, 2, '.', '');
                $totalFreight += $totalF;
                $totalRates += $totalF;
                $array = array('type'=>'Ocean Freight 40', 'cantidad' => $formulario->forty,'detail'=>'Container 40', 'price' => $data->forty, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalF , 'total' =>$totalF." ". $typeCurrency , 'idCurrency' => $data->currency_id);
                $array = array_merge($array,$arraymarkupF);
                $collectionRate->push($array);
                $data->setAttribute('montF',$array);
            }
            if($request->input('fortyhc') != "0") {
                $subtotalFHC = $formulario->fortyhc *  $data->fortyhc;
                $totalFHC = ($formulario->fortyhc *  $data->fortyhc)  / $rateC ;
                // MARKUPS 
                if($freighPercentage != 0){
                    $freighPercentage = intval($freighPercentage);
                    $markup = ( $totalFHC *  $freighPercentage ) / 100 ;
                    $markup = number_format($markup, 2, '.', '');
                    $totalFHC += $markup ;
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => "$typeCurrency ($freighPercentage%)") ;
                }else{
                    $markup =trim($freighAmmount);
                    $markup = number_format($markup, 2, '.', '');
                    $totalFHC += $freighMarkup;
                    $arraymarkupFH = array("markup" => $markup , "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre) ;
                }
                $totalFHC =  number_format($totalFHC, 2, '.', '');
                $totalFreight += $totalFHC;
                $totalRates += $totalFHC;
                $array = array('type'=>'Ocean Freight 40HC ', 'cantidad' => $formulario->fortyhc,'detail'=>'Container 40HC', 'price' => $data->fortyhc, 'currency' => $data->currency->alphacode ,'subtotal' => $subtotalFHC , 'total' =>$totalFHC." ". $typeCurrency , 'idCurrency' => $data->currency_id);
                $array = array_merge($array,$arraymarkupFH);
                $data->setAttribute('montFHC',$array);
                $collectionRate->push($array);
            }
            $data->setAttribute('rates',$collectionRate);
            // id de los ALL 
            array_push($orig_port,742);
            array_push($dest_port,742);
            //  calculo de los local charges en freight , origin y destiny 
            $localChar = LocalCharge::where('contract_id','=',$data->contract_id)->whereHas('localcharcarriers', function($q) use($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->whereHas('localcharports', function($q) use($orig_port,$dest_port) {
                $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
            })->with('localcharports.portOrig','localcharcarriers.carrier','currency','surcharge.saleterm')->get();
            foreach($localChar as $local){
                $rateMount = $this->ratesCurrency($local->currency->id,$typeCurrency);
                // Condicion para enviar los terminos de venta o compra 
                if(isset($local->surcharge->saleterm->name)){
                    $terminos = $local->surcharge->saleterm->name;
                }else{
                    $terminos = $local->surcharge->name;
                }
                if(in_array($local->calculationtype_id, $array20)){
                    if($request->input('twuenty') != "0") {
                        foreach($local->localcharcarriers as $carrierGlobal){
                            if($carrierGlobal->carrier_id == $data->carrier_id ){
                                if($local->typedestiny_id == '1'){  
                                    $subtotal_local = $formulario->twuenty *  $local->ammount;
                                    $totalAmmount = ($formulario->twuenty *  $local->ammount) / $rateMount ;
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\'  Local ' , 'subtotal_local' => $subtotal_local , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id);
                                    $arregloOrig = array_merge($arregloOrig,$arraymarkupT);
                                    $origTwuenty["origin"] = $arregloOrig;
                                    $collectionOrig->push($origTwuenty);
                                }
                                if($local->typedestiny_id == '2'){
                                    $subtotal_local = $formulario->twuenty *  $local->ammount;
                                    $totalAmmount = ($formulario->twuenty *  $local->ammount) / $rateMount ;
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id );
                                    $arregloDest = array_merge($arregloDest,$arraymarkupT);
                                    $destTwuenty["destiny"] =$arregloDest;
                                    $collectionDest->push($destTwuenty);
                                }
                                if($local->typedestiny_id == '3'){
                                    $subtotal_local = $formulario->twuenty *  $local->ammount;
                                    $totalAmmount = ($formulario->twuenty *  $local->ammount) / $rateMount;
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\'  Local ' , 'subtotal_local' => $subtotal_local , 'cantidadT' => $formulario->twuenty , 'idCurrency' => $local->currency->id);
                                    $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                                    $freighTwuenty["freight"] = $arregloFreight;
                                    $collectionFreight->push($freighTwuenty);
                                }
                            }
                        }
                    }
                }
                if(in_array($local->calculationtype_id, $array40)){
                    if($request->input('forty') != "0") {
                        foreach($local->localcharcarriers as $carrierGlobal){
                            if($carrierGlobal->carrier_id == $data->carrier_id ){
                                if($local->typedestiny_id == '1'){
                                    if($local->calculationtype_id == "4"  ){
                                        $subtotal_local = ($formulario->forty *  $local->ammount) * 2 ;
                                        $totalAmmount = (($formulario->forty *  $local->ammount) * 2 ) / $rateMount ;
                                        $cantidadT = $formulario->forty + $formulario->forty;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalOrigin += $totalAmmount ;
                                    }else{
                                        $subtotal_local = $formulario->forty *  $local->ammount;
                                        $totalAmmount = ($formulario->forty *  $local->ammount) *  $rateMount ;
                                        $cantidadT = $formulario->forty;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalOrigin += $totalAmmount ;
                                    }
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id);
                                    $arregloOrig = array_merge($arregloOrig,$arraymarkupF);
                                    $origForty["origin"] =$arregloOrig;
                                    $collectionOrig->push($origForty);
                                }
                                if($local->typedestiny_id == '2'){
                                    if($local->calculationtype_id == "4"  ){
                                        $subtotal_local = ($formulario->forty *  $local->ammount) * 2 ;
                                        $totalAmmount = (($formulario->forty *  $local->ammount) * 2 ) / $rateMount ;
                                        $cantidadT = $formulario->forty + $formulario->forty;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalDestiny += $totalAmmount;
                                    }else{
                                        $subtotal_local = $formulario->forty *  $local->ammount;
                                        $totalAmmount = ($formulario->forty *  $local->ammount) / $rateMount ;
                                        $cantidadT = $formulario->forty ;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalDestiny += $totalAmmount;
                                    }
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                                    $arregloDest = array_merge($arregloDest,$arraymarkupF);
                                    $destForty["destiny"] =$arregloDest;
                                    $collectionDest->push($destForty);
                                }
                                if($local->typedestiny_id == '3'){
                                    if($local->calculationtype_id == "4"  ){
                                        $subtotal_local = ($formulario->forty *  $local->ammount) * 2 ;
                                        $totalAmmount = (($formulario->forty *  $local->ammount) * 2 ) / $rateMount ;
                                        $cantidadT = $formulario->forty + $formulario->forty;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalFreight += $totalAmmount;
                                        $FreightCharges += $totalAmmount;
                                    }else{
                                        $subtotal_local = $formulario->forty *  $local->ammount;
                                        $totalAmmount = ($formulario->forty *  $local->ammount)/ $rateMount ;
                                        $cantidadT = $formulario->forty;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalFreight += $totalAmmount;
                                        $FreightCharges += $totalAmmount;
                                    }
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\'  Local ' , 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                                    $arregloFreight = array_merge($arregloFreight,$arraymarkupF);
                                    $freighForty["freight"] = $arregloFreight;
                                    $collectionFreight->push($freighForty);
                                }
                            }
                        }
                    }
                }
                if(in_array($local->calculationtype_id, $array40Hc)){
                    if($request->input('fortyhc') != "0") {
                        foreach($local->localcharcarriers as $carrierGlobal){
                            if($carrierGlobal->carrier_id == $data->carrier_id ){
                                if($local->typedestiny_id == '1'){
                                    if($local->calculationtype_id == "4"  ){
                                        $subtotal_local = ($formulario->fortyhc *  $local->ammount) * 2 ;
                                        $totalAmmount = (($formulario->fortyhc *  $local->ammount) * 2 ) / $rateMount ;
                                        $cantidadT = $formulario->fortyhc + $formulario->fortyhc;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalOrigin += $totalAmmount ;
                                    }else{
                                        $subtotal_local = $formulario->fortyhc *  $local->ammount;
                                        $totalAmmount = ($formulario->fortyhc *  $local->ammount)  / $rateMount;
                                        $cantidadT = $formulario->fortyhc ;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalOrigin += $totalAmmount ;
                                    }
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                                    $arregloOrig = array_merge($arregloOrig,$arraymarkupFH);
                                    $origFortyHc["origin"] =$arregloOrig;
                                    $collectionOrig->push($origFortyHc);
                                }
                                if($local->typedestiny_id == '2'){
                                    if($local->calculationtype_id == "4"  ){
                                        $subtotal_local = ($formulario->fortyhc *  $local->ammount) * 2 ;
                                        $totalAmmount = (($formulario->fortyhc *  $local->ammount) * 2) / $rateMount ;
                                        $cantidadT = $formulario->fortyhc + $formulario->fortyhc;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalDestiny += $totalAmmount;
                                    }else{
                                        $subtotal_local = $formulario->fortyhc *  $local->ammount;
                                        $totalAmmount = ($formulario->fortyhc *  $local->ammount) / $rateMount;
                                        $cantidadT = $formulario->fortyhc;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalDestiny += $totalAmmount;
                                    }
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                                    $arregloDest  = array_merge($arregloDest,$arraymarkupFH);
                                    $destFortyHc["destiny"] = $arregloDest;
                                    $collectionDest->push($destFortyHc);
                                }
                                if($local->typedestiny_id == '3'){
                                    if($local->calculationtype_id == "4"  ){
                                        $subtotal_local = ($formulario->fortyhc *  $local->ammount) * 2 ;
                                        $totalAmmount = (($formulario->fortyhc *  $local->ammount) * 2) / $rateMount ;
                                        $cantidadT = $formulario->fortyhc + $formulario->fortyhc;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalFreight += $totalAmmount;
                                        $FreightCharges += $totalAmmount;
                                    }else{
                                        $subtotal_local = $formulario->fortyhc *  $local->ammount;
                                        $totalAmmount = ($formulario->fortyhc *  $local->ammount) / $rateMount;
                                        $cantidadT = $formulario->fortyhc;
                                        // MARKUP
                                        if($localPercentage != 0){
                                            $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $markup ;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                        }else{
                                            $markup =$localAmmount;
                                            $markup = number_format($markup, 2, '.', '');
                                            $totalAmmount += $localMarkup;
                                            $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                        }
                                        $totalFreight += $totalAmmount;
                                        $FreightCharges += $totalAmmount;
                                    }
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                                    $arregloFreight = array_merge($arregloFreight,$arraymarkupFH);
                                    $freighFortyHc["freight"] = $arregloFreight;
                                    $collectionFreight->push($freighFortyHc);
                                }
                            }
                        }
                    }
                }
                if($local->calculationtype_id == "6"){
                    foreach($local->localcharcarriers as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id ){
                            if($local->typedestiny_id == '1'){
                                $subtotal_local =  $local->ammount;
                                $totalAmmount =  $local->ammount  / $rateMount;
                                $cantidadT = 1;
                                // MARKUP
                                if($localPercentage != 0){
                                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $markup ;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                }else{
                                    $markup =$localAmmount;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $localMarkup;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                }
                                $totalOrigin += $totalAmmount ;
                                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id );
                                $arregloOrig = array_merge($arregloOrig,$arraymarkupPC);
                                $origPer["origin"] =$arregloOrig;
                                $collectionOrig->push($origPer);
                            }
                            if($local->typedestiny_id == '2'){
                                $subtotal_local =  $local->ammount;
                                $totalAmmount =  $local->ammount  / $rateMount;
                                $cantidadT = 1;
                                // MARKUP
                                if($localPercentage != 0){
                                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $markup ;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                }else{
                                    $markup =$localAmmount;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $localMarkup;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                }
                                $totalDestiny += $totalAmmount;
                                $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT  , 'idCurrency' => $local->currency->id  );
                                $arregloDest = array_merge($arregloDest,$arraymarkupPC);
                                $destPer["destiny"] = $arregloDest;
                                $collectionDest->push($destPer);
                            }
                            if($local->typedestiny_id == '3'){
                                $subtotal_local =  $local->ammount;
                                $totalAmmount =  $local->ammount  / $rateMount;
                                $cantidadT = 1;
                                // MARKUP
                                if($localPercentage != 0){
                                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $markup ;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
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
                                $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $local->ammount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>' Shipment Local ', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id  );
                                $arregloPC = array_merge($arregloPC,$arraymarkupPC);
                                $freightPer["freight"] = $arregloPC;
                                $collectionFreight->push($freightPer);
                            }
                        }
                    }
                }
            }
            // fin calculo local charges 
            //#######################################################################
            //  calculo de los global charges en freight , origin y destiny 
            $globalChar = GlobalCharge::whereHas('globalcharcarrier', function($q) use($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->whereHas('globalcharport', function($q) use($orig_port,$dest_port) {
                $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
            })->where('company_user_id','=',$company_user_id)->with('globalcharport.portOrig','globalcharport.portDest','globalcharcarrier.carrier','currency','surcharge.saleterm')->get();
            foreach($globalChar as $global){
                $rateMountG = $this->ratesCurrency($global->currency->id,$typeCurrency);
                // Condicion para enviar los terminos de venta o compra 
                if(isset($global->surcharge->saleterm->name)){
                    $terminos = $global->surcharge->saleterm->name;
                }else{
                    $terminos = $global->surcharge->name;
                }
                if(in_array($global->calculationtype_id, $array20)){
                    if($request->input('twuenty') != "0") {
                        foreach($global->globalcharcarrier as $carrierGlobal){
                            if($carrierGlobal->carrier_id == $data->carrier_id ){
                                if($global->typedestiny_id == '1'){
                                    $subtotal_global = $formulario->twuenty *  $global->ammount;
                                    $totalAmmount = ($formulario->twuenty *  $global->ammount) / $rateMountG ;
                                    $cantidadT = $formulario->twuenty;
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'20\' Global '  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                                    $arregloOrig = array_merge($arregloOrig,$arraymarkupT);
                                    $origTwuentyGlo["origin"] = $arregloOrig;
                                    $collectionGloOrig->push($origTwuentyGlo);
                                }
                                if($global->typedestiny_id == '2'){
                                    $subtotal_global = $formulario->twuenty *  $global->ammount;
                                    $totalAmmount = ($formulario->twuenty *  $global->ammount) / $rateMountG;
                                    $cantidadT = $formulario->twuenty;
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                                    $arregloDest = array_merge($arregloDest,$arraymarkupT);
                                    $destTwuentyGlo["destiny"] = $arregloDest;
                                    $collectionGloDest->push($destTwuentyGlo);
                                }
                                if($global->typedestiny_id == '3'){
                                    $subtotal_global = $formulario->twuenty *  $global->ammount;
                                    $totalAmmount = ($formulario->twuenty *  $global->ammount) / $rateMountG;
                                    $cantidadT = $formulario->twuenty;
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupT = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
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
                                    $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->twuenty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'20\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                    $arregloFreight = array_merge($arregloFreight,$arraymarkupT);
                                    $freighTwuentyGlo["freight"] =$arregloFreight;
                                    $collectionGloFreight->push($freighTwuentyGlo);
                                }
                            }
                        }
                    }
                }
                if(in_array($global->calculationtype_id, $array40)){
                    if($request->input('forty') != "0") {
                        foreach($global->globalcharcarrier as $carrierGlobal){
                            if($carrierGlobal->carrier_id == $data->carrier_id ){
                                if($global->typedestiny_id == '1'){
                                    if($global->calculationtype_id == "4"  ){
                                        $subtotal_global = ($formulario->forty *  $global->ammount) * 2 ;
                                        $totalAmmount = (($formulario->forty *  $global->ammount) * 2 ) / $rateMountG ;
                                        $cantidadT = $formulario->forty +  $formulario->forty  ;
                                        $totalOrigin += $totalAmmount ;
                                    }else{
                                        $subtotal_global = $formulario->forty *  $global->ammount;
                                        $totalAmmount = ($formulario->forty *  $global->ammount) / $rateMountG;
                                        $totalOrigin += $totalAmmount ;
                                        $cantidadT = $formulario->forty;
                                    }
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT , 'idCurrency' => $global->currency->id);
                                    $arregloOrig = array_merge($arregloOrig,$arraymarkupF);
                                    $origFortyGlo["origin"] =$arregloOrig;
                                    $collectionGloOrig->push($origFortyGlo);
                                }
                                if($global->typedestiny_id == '2'){
                                    if($global->calculationtype_id == "4"  ){
                                        $subtotal_global = ($formulario->forty *  $global->ammount) * 2 ;
                                        $totalAmmount = (($formulario->forty *  $global->ammount) * 2 ) / $rateMountG ;
                                        $totalDestiny += $totalAmmount;
                                        $cantidadT = $formulario->forty +  $formulario->forty  ;
                                    }else{
                                        $subtotal_global = $formulario->forty *  $global->ammount;
                                        $totalAmmount = ($formulario->forty *  $global->ammount) / $rateMountG;
                                        $totalDestiny += $totalAmmount;
                                        $cantidadT = $formulario->forty;
                                    }
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                    $arregloDest = array_merge($arregloDest,$arraymarkupF);
                                    $destFortyGlo["destiny"] =$arregloDest;
                                    $collectionGloDest->push($destFortyGlo);
                                }
                                if($global->typedestiny_id == '3'){
                                    if($global->calculationtype_id == "4"  ){
                                        $subtotal_global = ($formulario->forty *  $global->ammount) * 2 ;
                                        $totalAmmount = (($formulario->forty *  $global->ammount) * 2 ) / $rateMountG ;
                                        $cantidadT = $formulario->forty +  $formulario->forty  ;
                                    }else{
                                        $subtotal_global = $formulario->forty *  $global->ammount;
                                        $totalAmmount = ($formulario->forty *  $global->ammount) / $rateMountG;
                                        $cantidadT = $formulario->forty;
                                    }
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupF = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->forty , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' Global ' , 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                    $arregloFreight = array_merge($arregloFreight,$arraymarkupF);
                                    $freighFortyGlo["freight"] = $arregloFreight;
                                    $collectionGloFreight->push($freighFortyGlo);
                                }
                            }
                        }
                    }
                }
                if(in_array($global->calculationtype_id, $array40Hc)){
                    if($request->input('fortyhc') != "0") {
                        foreach($global->globalcharcarrier as $carrierGlobal){
                            if($carrierGlobal->carrier_id == $data->carrier_id ){
                                if($global->typedestiny_id == '1'){
                                    if($global->calculationtype_id == "4"  ){
                                        $subtotal_global = ($formulario->fortyhc *  $global->ammount) * 2 ;
                                        $totalAmmount = (($formulario->fortyhc *  $global->ammount) * 2)  / $rateMountG ;
                                        $totalOrigin += $totalAmmount ;
                                        $cantidadT = $formulario->fortyhc +  $formulario->fortyhc  ;
                                    }else{
                                        $subtotal_global =  $formulario->fortyhc *  $global->ammount;
                                        $totalAmmount = ($formulario->fortyhc *  $global->ammount) / $rateMountG;
                                        $totalOrigin += $totalAmmount ;
                                        $cantidadT = $formulario->fortyhc;
                                    }
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                    $arregloOrig = array_merge($arregloOrig,$arraymarkupFH);
                                    $origFortyHcGlo["origin"] =$arregloOrig;
                                    $collectionGloOrig->push($origFortyHcGlo);
                                }
                                if($global->typedestiny_id == '2'){
                                    if($global->calculationtype_id == "4"  ){
                                        $subtotal_global = ($formulario->fortyhc *  $global->ammount) * 2 ;
                                        $totalAmmount = (($formulario->fortyhc *  $global->ammount) * 2)  / $rateMountG ;
                                        $totalDestiny += $totalAmmount;
                                        $cantidadT = $formulario->fortyhc +  $formulario->fortyhc  ;
                                    }else{
                                        $subtotal_global =  $formulario->fortyhc *  $global->ammount;
                                        $totalAmmount = ($formulario->fortyhc *  $global->ammount) / $rateMountG;
                                        $totalDestiny += $totalAmmount;
                                        $cantidadT = $formulario->fortyhc;
                                    }
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                    $arregloDest = array_merge($arregloDest,$arraymarkupFH);
                                    $destFortyHcGlo["destiny"] = $arregloDest;
                                    $collectionGloDest->push($destFortyHcGlo);
                                }
                                if($global->typedestiny_id == '3'){
                                    if($global->calculationtype_id == "4"  ){
                                        $subtotal_global = ($formulario->fortyhc *  $global->ammount) * 2 ;
                                        $totalAmmount = (($formulario->fortyhc *  $global->ammount) * 2)  / $rateMountG ;
                                        $cantidadT = $formulario->fortyhc +  $formulario->fortyhc;
                                    }else{
                                        $subtotal_global =  $formulario->fortyhc *  $global->ammount;
                                        $totalAmmount = ($formulario->fortyhc *  $global->ammount) / $rateMountG;
                                        $cantidadT = $formulario->fortyhc;
                                    }
                                    // MARKUP
                                    if($localPercentage != 0){
                                        $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $markup ;
                                        $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                    }else{
                                        $markup =$localAmmount;
                                        $markup = number_format($markup, 2, '.', '');
                                        $totalAmmount += $localMarkup;
                                        $arraymarkupFH = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                    }
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $formulario->fortyhc , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'40\' HC Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                    $arregloFreight = array_merge($arregloFreight,$arraymarkupFH);
                                    $freighFortyHcGlo["freight"] =$arregloFreight;
                                    $collectionGloFreight->push($freighFortyHcGlo);
                                }
                            }
                        }
                    }
                }
                if($global->calculationtype_id == "6"){
                    foreach($global->globalcharcarrier as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id ){
                            if($global->typedestiny_id == '1'){
                                $subtotal_global = $global->ammount;
                                $totalAmmount =  $global->ammount / $rateMountG;
                                $cantidadT = 1;
                                // MARKUP
                                if($localPercentage != 0){
                                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $markup ;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                }else{
                                    $markup =$localAmmount;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $localMarkup;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                }
                                $totalOrigin += $totalAmmount ;
                                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                $arregloOrig = array_merge($arregloOrig,$arraymarkupPC);
                                $origPerGlo["origin"] =$arregloOrig;
                                $collectionGloOrig->push($origPerGlo);
                            }
                            if($global->typedestiny_id == '2'){
                                $subtotal_global = $global->ammount;
                                $totalAmmount =  $global->ammount / $rateMountG;
                                $cantidadT = 1;
                                // MARKUP
                                if($localPercentage != 0){
                                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $markup ;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                }else{
                                    $markup =$localAmmount;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $localMarkup;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                }
                                $totalDestiny += $totalAmmount;
                                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                $arregloDest = array_merge($arregloDest,$arraymarkupPC);
                                $destPerGlo["destiny"] = $arregloDest;
                                $collectionGloDest->push($destPerGlo);
                            }
                            if($global->typedestiny_id == '3'){
                                $subtotal_global = $global->ammount;
                                $totalAmmount =  $global->ammount / $rateMountG;
                                $cantidadT = 1;
                                // MARKUP
                                if($localPercentage != 0){
                                    $markup = ( $totalAmmount *  $localPercentage ) / 100 ;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $markup ;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => "$typeCurrency ($localPercentage%)") ;
                                }else{
                                    $markup =$localAmmount;
                                    $markup = number_format($markup, 2, '.', '');
                                    $totalAmmount += $localMarkup;
                                    $arraymarkupPC = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre) ;
                                }
                                $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                $totalFreight += $totalAmmount;
                                $FreightCharges += $totalAmmount;
                                $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => "-" , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtype->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=> 'Shipment Global ', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'idCurrency' => $global->currency->id);
                                $arregloFreight = array_merge($arregloFreight,$arraymarkupPC);
                                $freightPerGlo["freight"] = $arregloFreight;
                                $collectionGloFreight->push($freightPerGlo);
                            }
                        }
                    }
                }
            }
            // fin calculo Global charges 
            //#######################################################################
            // Armar los schedules
            try{
                $url = "http://schedules.cargofive.com/schedule/".strtolower($data->carrier->name)."/".$data->port_origin->code."/".$data->port_destiny->code;
                $client = new Client();
                $res = $client->request('GET', $url, [
                ]);
                $schedules = Collection::make(json_decode($res->getBody()));
                //  $schedules= $schedules->where($schedules->schedules->Etd,'2018-07-16');
                $schedulesArr = new Collection();
                $schedulesFin = new Collection();
                if(!$schedules->isEmpty()){
                    foreach($schedules['schedules'] as $schedules){
                        $collectS = Collection::make($schedules);
                        $days =  $this->dias_transcurridos($schedules->Eta,$schedules->Etd);
                        $collectS->put('days',$days);
                        if($schedules->Transfer > 1){
                            $collectS->put('type','Scale');
                        }else{
                            $collectS->put('type','Direct');
                        }
                        $schedulesArr->push($collectS);
                    }
                    //'2018-07-24'
                    $dateSchedule = strtotime($date);
                    $dateSchedule =  date('Y-m-d',$dateSchedule);
                    if(!$schedulesArr->isEmpty()){ 
                        $schedulesArr =  $schedulesArr->where('Etd','>=', $dateSchedule)->first();
                        $schedulesFin->push($schedulesArr);
                    }
                }
            }catch (\Guzzle\Http\Exception\ConnectException $e) {
            }
            //#######################################################################
            //Formato subtotales y operacion total quote 
            $totalFreight =  number_format($totalFreight, 2, '.', '');
            $FreightCharges =  number_format($FreightCharges, 2, '.', '');
            $totalOrigin  =  number_format($totalOrigin, 2, '.', '');
            $totalDestiny =  number_format($totalDestiny, 2, '.', '');
            $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
            // Variables suma de todo los origin o destiny 
            $totalChargeDest = 0;
            $totalChargeOrig = 0;
            $totalInland = 0;
            if(!empty($inlandOrigin)){
                foreach($inlandOrigin as $inlandOrig){
                    if($inlandOrig['port_id'] == $data->port_origin->id ){
                        $totalQuote += $inlandOrig['monto'];
                        $totalChargeOrig += $inlandOrig['monto'];
                        $totalInland += $inlandOrig['monto'];
                    }
                }
            }else{
                $inlandOrigin = array();
            }
            if(!empty($inlandDestiny)){
                foreach($inlandDestiny as $inlandDest){
                    if($inlandDest['port_id'] == $data->port_destiny->id ){
                        $totalQuote += $inlandDest['monto'];
                        $totalChargeDest += $inlandDest['monto'];
                        $totalInland +=  $inlandDest['monto'];
                    }
                }
            }else{
                $inlandDestiny =  array();
            }
            $totalChargeOrig += $totalOrigin;
            $totalChargeDest += $totalDestiny;
            $totalFreight = $totalFreight." ".$typeCurrency;
            $totalOrigin = $totalOrigin." ".$typeCurrency;
            $totalDestiny = $totalDestiny." ".$typeCurrency;
            $totalQuoteSin = number_format($totalQuote, 2, ',', '');
            $totalQuote = $totalQuote." ".$typeCurrency;
            $quoteCurrency = $typeCurrency;
            $totalInland = number_format($totalInland, 2, ',', '');
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
            $data->setAttribute('totalQuoteSin',$totalQuoteSin);
            $data->setAttribute('quoteCurrency',$quoteCurrency);
            $data->setAttribute('idCurrency',$idCurrency);
            // SCHEDULES
            $data->setAttribute('schedulesFin',$schedulesFin);
        }
        $form  = $request->all();
        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');
        return view('quotation/index', compact('harbor','formulario','arreglo','inlandDestiny','inlandOrigin','form'));
    }

    // FIN COTIZACION AUTOMATICA

    //Crear cotización manual
    public function create()
    {
        $company_user='';
        $companies='';
        $saleterms = '';
        $currencies = '';
        $currency_cfg = '';
        $exchange = '';
        $email_templates = '';
        $company_user_id=\Auth::user()->company_user_id;
        $quotes = Quote::all();
        $harbors = Harbor::all()->pluck('name','id');
        $countries = Country::all()->pluck('name','id');
        $airports = Airport::all()->pluck('name','id');
        $carriers = Carrier::all()->pluck('name','id');
        $airlines = Airline::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        $user = User::where('id',\Auth::id())->first();
        $incoterm = Incoterm::pluck('name','id');
        if($company_user_id){
            $company_user=CompanyUser::find($company_user_id);
            $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->pluck('name','id');
            if(\Auth::user()->hasRole('subuser')){
                $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
                    $q->where('user_id',\Auth::user()->id);
                })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
            }else{
                $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
            }
        }
        if($company_user){
            $currencies = Currency::pluck('alphacode','id');
            $currency_cfg = Currency::find($company_user->currency_id);
        }
        if(\Auth::user()->company_user_id && $currency_cfg != ''){
            if($currency_cfg->alphacode=='USD'){
                $exchange = Currency::where('api_code_eur','EURUSD')->first();
            }else{
                $exchange = Currency::where('api_code','USDEUR')->first();
            }
        }
        return view('quotes/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$user,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates,'carriers'=>$carriers,'airports'=>$airports,'airlines'=>$airlines]);

    }

    public function edit($id){
        $email_templates='';
        $quote = Quote::findOrFail($id);
        $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->pluck('name','id');
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $currencies = Currency::pluck('alphacode','id');
        $carriers = Carrier::pluck('name','id');
        $airlines = Airline::pluck('name','id');
        $airports = Airport::pluck('name','id');
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
            $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            if($currency_cfg->alphacode=='USD'){
                $exchange = Currency::where('api_code_eur','EURUSD')->first();
            }else{
                $exchange = Currency::where('api_code','USDEUR')->first();
            }
        }
        $incoterm = Incoterm::pluck('name','id');
        return view('quotes/edit', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                    'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates,'carriers'=>$carriers,'airports'=>$airports,'airlines'=>$airlines,'user'=>$user]);

    }

    public function store(Request $request)
    {
        $rules = array(
            'pick_up_date' => 'required',
            'validity' => 'required',
            'company_id' => 'required',
            'contact_id' => 'required',
            'type' => 'required',
            'freight_ammount_charge' => 'required',
            'freight_ammount_detail' => 'required',
            'freight_ammount_units' => 'required',
            'freight_price_per_unit' => 'required',
            'freight_total_ammount' => 'required',
            'freight_total_ammount_2' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {

            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Error!');
            $request->session()->flash('message.content', 'There is empty fields');
            //return redirect()->route('quotes.index');
            return redirect('/quotes/create');

        }else{
            $input = Input::all();

            $total_markup_origin=array_values( array_filter($input['origin_ammount_markup']) );
            $total_markup_freight=array_values( array_filter($input['freight_ammount_markup']) );
            $total_markup_destination=array_values( array_filter($input['destination_ammount_markup']) );
            $sum_markup_origin=array_sum($total_markup_origin);
            $sum_markup_freight=array_sum($total_markup_freight);
            $sum_markup_destination=array_sum($total_markup_destination);
            $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
            $request->request->add(['owner' => \Auth::id(),'currency_id'=>$currency->currency_id,'total_markup_origin'=>$sum_markup_origin,'total_markup_freight'=>$sum_markup_freight,'total_markup_destination'=>$sum_markup_destination]);
            $quote=Quote::create($request->all());
            if($input['origin_ammount_charge']!=[null]) {
                $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
                $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
                $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
                $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
                $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
                $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
                $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
                $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
                foreach ($origin_ammount_charge as $key => $item) {
                    $origin_ammount = new OriginAmmount();
                    $origin_ammount->quote_id = $quote->id;
                    if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
                        $origin_ammount->charge = $origin_ammount_charge[$key];
                    }
                    if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
                        $origin_ammount->detail = $origin_ammount_detail[$key];
                    }
                    if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
                        $origin_ammount->units = $origin_total_units[$key];
                    }
                    if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
                        $origin_ammount->markup = $origin_total_markup[$key];
                    }
                    if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
                        $origin_ammount->price_per_unit = $origin_ammount_price_per_unit[$key];
                        $origin_ammount->currency_id = $origin_ammount_currency[$key];
                    }
                    if ((isset($origin_total_ammount[$key])) && ($origin_total_ammount[$key] != '')) {
                        $origin_ammount->total_ammount = $origin_total_ammount[$key];
                    }
                    if ((isset($origin_total_ammount_2[$key])) && ($origin_total_ammount_2[$key] != '')) {
                        $origin_ammount->total_ammount_2 = $origin_total_ammount_2[$key];
                    }
                    $origin_ammount->save();
                }
            }
            if($input['freight_ammount_charge']!=[null]) {
                $freight_ammount_charge = array_values( array_filter($input['freight_ammount_charge']) );
                $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
                $freight_ammount_price_per_unit = array_values( array_filter($input['freight_price_per_unit']) );
                $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
                $freight_total_units = array_values( array_filter($input['freight_ammount_units']) );
                $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
                $freight_total_ammount_2 = array_values( array_filter($input['freight_total_ammount_2']) );
                $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
                foreach ($freight_ammount_charge as $key => $item) {
                    $freight_ammount = new FreightAmmount();
                    $freight_ammount->quote_id = $quote->id;
                    if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
                        $freight_ammount->charge = $freight_ammount_charge[$key];
                    }
                    if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                        $freight_ammount->detail = $freight_ammount_detail[$key];
                    }
                    if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
                        $freight_ammount->units = $freight_total_units[$key];
                    }
                    if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
                        $freight_ammount->markup = $freight_total_markup[$key];
                    }
                    if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
                        $freight_ammount->price_per_unit = $freight_ammount_price_per_unit[$key];
                        $freight_ammount->currency_id = $freight_ammount_currency[$key];
                    }
                    if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
                        $freight_ammount->total_ammount = $freight_total_ammount[$key];
                    }
                    if ((isset($freight_total_ammount_2[$key])) && ($freight_total_ammount_2[$key] != '')) {
                        $freight_ammount->total_ammount_2 = $freight_total_ammount_2[$key];
                    }
                    $freight_ammount->save();
                }
            }
            if($input['destination_ammount_charge']!=[null]) {
                $destination_ammount_charge = array_values( array_filter($input['destination_ammount_charge']) );
                $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
                $destination_ammount_price_per_unit = array_values( array_filter($input['destination_price_per_unit']) );
                $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
                $destination_ammount_units = array_values( array_filter($input['destination_ammount_units']) );
                $destination_ammount_markup = array_values( array_filter($input['destination_ammount_markup']) );
                $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
                $destination_total_ammount_2 = array_values( array_filter($input['destination_total_ammount_2']) );
                foreach ($destination_ammount_charge as $key => $item) {
                    $destination_ammount = new DestinationAmmount();
                    $destination_ammount->quote_id = $quote->id;
                    if ((isset($destination_ammount_charge[$key])) && (!empty($destination_ammount_charge[$key]))) {
                        $destination_ammount->charge = $destination_ammount_charge[$key];
                    }
                    if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
                        $destination_ammount->detail = $destination_ammount_detail[$key];
                    }
                    if ((isset($destination_ammount_units[$key])) && (!empty($destination_ammount_units[$key]))) {
                        $destination_ammount->units = $destination_ammount_units[$key];
                    }
                    if ((isset($destination_ammount_markup[$key])) && (!empty($destination_ammount_markup[$key]))) {
                        $destination_ammount->markup = $destination_ammount_markup[$key];
                    }
                    if ((isset($destination_ammount_price_per_unit[$key])) && (!empty($destination_ammount_price_per_unit[$key]))) {
                        $destination_ammount->price_per_unit = $destination_ammount_price_per_unit[$key];
                        $destination_ammount->currency_id = $destination_ammount_currency[$key];
                    }
                    if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
                        $destination_ammount->total_ammount = $destination_total_ammount[$key];
                    }
                    if ((isset($destination_total_ammount_2[$key])) && (!empty($destination_total_ammount_2[$key]))) {
                        $destination_ammount->total_ammount_2 = $destination_total_ammount_2[$key];
                    }
                    $destination_ammount->save();
                }
            }
            if(isset($input['schedule'])){
                if($input['schedule'] != 'null'){
                    $schedules = json_decode($input['schedule']);
                    foreach( $schedules as $schedule){ 
                        $sche = json_decode($schedule);
                        $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
                        $saveSchedule  = new Schedule();
                        $saveSchedule->vessel = $sche->VesselName;
                        $saveSchedule->etd = $sche->Etd;
                        $saveSchedule->transit_time =  $dias;
                        $saveSchedule->eta = $sche->Eta;
                        $saveSchedule->type = 'direct';
                        $saveSchedule->quotes()->associate($quote);
                        $saveSchedule->save(); 
                    }
                }
            }
            // Schedule manual 
            if(isset($input['schedule_manual'])){
                if($input['schedule_manual'] != 'null'){
                    $sche = json_decode($input['schedule_manual']);
                    // dd($sche);
                    $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
                    $saveSchedule  = new Schedule();
                    $saveSchedule->vessel = $sche->VesselName;
                    $saveSchedule->etd = $sche->Etd;
                    $saveSchedule->transit_time =  $dias;
                    $saveSchedule->eta = $sche->Eta;
                    $saveSchedule->type = 'direct';
                    $saveSchedule->quotes()->associate($quote);
                    $saveSchedule->save(); 
                }
            }
<<<<<<< HEAD
            $schedulesArr->push($collectS);
          }
          //'2018-07-24'
          $dateSchedule = strtotime($date);
          $dateSchedule =  date('Y-m-d',$dateSchedule);
          if(!$schedulesArr->isEmpty()){ 
            $schedulesArr =  $schedulesArr->where('Etd','>=', $dateSchedule)->first();
            $schedulesFin->push($schedulesArr);
          }
        }
      }catch (\Guzzle\Http\Exception\ConnectException $e) {
      }
      //#######################################################################
      //Formato subtotales y operacion total quote 
      $totalFreight =  number_format($totalFreight, 2, '.', '');
      $FreightCharges =  number_format($FreightCharges, 2, '.', '');
      $totalOrigin  =  number_format($totalOrigin, 2, '.', '');
      $totalDestiny =  number_format($totalDestiny, 2, '.', '');
      $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
      // Variables suma de todo los origin o destiny 
      $totalChargeDest = 0;
      $totalChargeOrig = 0;
      $totalInland = 0;
      if(!empty($inlandOrigin)){
        foreach($inlandOrigin as $inlandOrig){
          if($inlandOrig['port_id'] == $data->port_origin->id ){
            $totalQuote += $inlandOrig['monto'];
            $totalChargeOrig += $inlandOrig['monto'];
            $totalInland += $inlandOrig['monto'];
          }
        }
      }else{
        $inlandOrigin = array();
      }
      if(!empty($inlandDestiny)){
        foreach($inlandDestiny as $inlandDest){
          if($inlandDest['port_id'] == $data->port_destiny->id ){
            $totalQuote += $inlandDest['monto'];
            $totalChargeDest += $inlandDest['monto'];
            $totalInland +=  $inlandDest['monto'];
          }
        }
      }else{
        $inlandDestiny =  array();
      }
      $totalChargeOrig += $totalOrigin;
      $totalChargeDest += $totalDestiny;
      $totalFreight = $totalFreight." ".$typeCurrency;
      $totalOrigin = $totalOrigin." ".$typeCurrency;
      $totalDestiny = $totalDestiny." ".$typeCurrency;
      $totalQuoteSin = number_format($totalQuote, 2, ',', '');
      $totalQuote = $totalQuote." ".$typeCurrency;
      $quoteCurrency = $typeCurrency;
      $totalInland = number_format($totalInland, 2, ',', '');
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
      $data->setAttribute('totalQuoteSin',$totalQuoteSin);
      $data->setAttribute('quoteCurrency',$quoteCurrency);
      $data->setAttribute('idCurrency',$idCurrency);
      // SCHEDULES
      $data->setAttribute('schedulesFin',$schedulesFin);
    }
    $form  = $request->all();
    $objharbor = new Harbor();
    $harbor = $objharbor->all()->pluck('name','id');
    return view('quotation/index', compact('harbor','formulario','arreglo','inlandDestiny','inlandOrigin','form'));
  }

  // FIN COTIZACION AUTOMATICA

  //Crear cotización manual
  public function create()
  {
    $company_user='';
    $companies='';
    $saleterms = '';
    $currencies = '';
    $currency_cfg = '';
    $exchange = '';
    $email_templates = '';
    $company_user_id=\Auth::user()->company_user_id;
    $quotes = Quote::all();
    $harbors = Harbor::all()->pluck('name','id');
    $countries = Country::all()->pluck('name','id');
    $airports = Airport::all()->pluck('name','id');
    $carriers = Carrier::all()->pluck('name','id');
    $airlines = Airline::all()->pluck('name','id');
    $prices = Price::all()->pluck('name','id');
    $user = User::where('id',\Auth::id())->first();
    $incoterm = Incoterm::pluck('name','id');
    if($company_user_id){
      $company_user=CompanyUser::find($company_user_id);
      $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->pluck('name','id');
      if(\Auth::user()->hasRole('subuser')){
        $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
          $q->where('user_id',\Auth::user()->id);
        })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
      }else{
        $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
      }
    }
    if($company_user){
      $currencies = Currency::pluck('alphacode','id');
      $currency_cfg = Currency::find($company_user->currency_id);
    }
    if(\Auth::user()->company_user_id && $currency_cfg != ''){
      if($currency_cfg->alphacode=='USD'){
        $exchange = Currency::where('api_code_eur','EURUSD')->first();
      }else{
        $exchange = Currency::where('api_code','USDEUR')->first();
      }
    }
    return view('quotes/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$user,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates,'carriers'=>$carriers,'airports'=>$airports,'airlines'=>$airlines]);

  }

  public function edit($id){
    $email_templates='';
    $quote = Quote::findOrFail($id);
    $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
    $harbors = Harbor::all()->pluck('name','id');
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
    $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
    $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
    $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
    $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->pluck('name','id');
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    $currencies = Currency::pluck('alphacode','id');
    $carriers = Carrier::pluck('name','id');
    $airlines = Airline::pluck('name','id');
    $airports = Airport::pluck('name','id');
    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
      $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      if($currency_cfg->alphacode=='USD'){
        $exchange = Currency::where('api_code_eur','EURUSD')->first();
      }else{
        $exchange = Currency::where('api_code','USDEUR')->first();
      }
    }
    $incoterm = Incoterm::pluck('name','id');

    return view('quotes/edit', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates,'carriers'=>$carriers,'airports'=>$airports,'airlines'=>$airlines,'user'=>$user]);

  }

  public function store(Request $request)
  {
    $input = Input::all();
    $company_quote = $this->idPersonalizado();    //ID PERSONALIZADO


    $total_markup_origin=array_values( array_filter($input['origin_ammount_markup']) );
    $total_markup_freight=array_values( array_filter($input['freight_ammount_markup']) );
    $total_markup_destination=array_values( array_filter($input['destination_ammount_markup']) );
    $sum_markup_origin=array_sum($total_markup_origin);
    $sum_markup_freight=array_sum($total_markup_freight);
    $sum_markup_destination=array_sum($total_markup_destination);
    $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
    $request->request->add(['owner' => \Auth::id(),'currency_id'=>$currency->currency_id,'total_markup_origin'=>$sum_markup_origin,'total_markup_freight'=>$sum_markup_freight,'total_markup_destination'=>$sum_markup_destination,'company_quote' => $company_quote]);
    
    $quote=Quote::create($request->all());
    if($input['origin_ammount_charge']!=[null]) {
      $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
      $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
      $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
      $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
      $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
      $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
      $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
      $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
      foreach ($origin_ammount_charge as $key => $item) {
        $origin_ammount = new OriginAmmount();
        $origin_ammount->quote_id = $quote->id;
        if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
          $origin_ammount->charge = $origin_ammount_charge[$key];
        }
        if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
          $origin_ammount->detail = $origin_ammount_detail[$key];
        }
        if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
          $origin_ammount->units = $origin_total_units[$key];
        }
        if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
          $origin_ammount->markup = $origin_total_markup[$key];
        }
        if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
          $origin_ammount->price_per_unit = $origin_ammount_price_per_unit[$key];
          $origin_ammount->currency_id = $origin_ammount_currency[$key];
        }
        if ((isset($origin_total_ammount[$key])) && ($origin_total_ammount[$key] != '')) {
          $origin_ammount->total_ammount = $origin_total_ammount[$key];
        }
        if ((isset($origin_total_ammount_2[$key])) && ($origin_total_ammount_2[$key] != '')) {
          $origin_ammount->total_ammount_2 = $origin_total_ammount_2[$key];
        }
        $origin_ammount->save();
      }
    }
    if($input['freight_ammount_charge']!=[null]) {
      $freight_ammount_charge = array_values( array_filter($input['freight_ammount_charge']) );
      $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
      $freight_ammount_price_per_unit = array_values( array_filter($input['freight_price_per_unit']) );
      $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
      $freight_total_units = array_values( array_filter($input['freight_ammount_units']) );
      $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
      $freight_total_ammount_2 = array_values( array_filter($input['freight_total_ammount_2']) );
      $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
      foreach ($freight_ammount_charge as $key => $item) {
        $freight_ammount = new FreightAmmount();
        $freight_ammount->quote_id = $quote->id;
        if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
          $freight_ammount->charge = $freight_ammount_charge[$key];
        }
        if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
          $freight_ammount->detail = $freight_ammount_detail[$key];
        }
        if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
          $freight_ammount->units = $freight_total_units[$key];
        }
        if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
          $freight_ammount->markup = $freight_total_markup[$key];
        }
        if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
          $freight_ammount->price_per_unit = $freight_ammount_price_per_unit[$key];
          $freight_ammount->currency_id = $freight_ammount_currency[$key];
        }
        if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
          $freight_ammount->total_ammount = $freight_total_ammount[$key];
        }
        if ((isset($freight_total_ammount_2[$key])) && ($freight_total_ammount_2[$key] != '')) {
          $freight_ammount->total_ammount_2 = $freight_total_ammount_2[$key];
        }
        $freight_ammount->save();
      }
    }
    if($input['destination_ammount_charge']!=[null]) {
      $destination_ammount_charge = array_values( array_filter($input['destination_ammount_charge']) );
      $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
      $destination_ammount_price_per_unit = array_values( array_filter($input['destination_price_per_unit']) );
      $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
      $destination_ammount_units = array_values( array_filter($input['destination_ammount_units']) );
      $destination_ammount_markup = array_values( array_filter($input['destination_ammount_markup']) );
      $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
      $destination_total_ammount_2 = array_values( array_filter($input['destination_total_ammount_2']) );
      foreach ($destination_ammount_charge as $key => $item) {
        $destination_ammount = new DestinationAmmount();
        $destination_ammount->quote_id = $quote->id;
        if ((isset($destination_ammount_charge[$key])) && (!empty($destination_ammount_charge[$key]))) {
          $destination_ammount->charge = $destination_ammount_charge[$key];
        }
        if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
          $destination_ammount->detail = $destination_ammount_detail[$key];
        }
        if ((isset($destination_ammount_units[$key])) && (!empty($destination_ammount_units[$key]))) {
          $destination_ammount->units = $destination_ammount_units[$key];
        }
        if ((isset($destination_ammount_markup[$key])) && (!empty($destination_ammount_markup[$key]))) {
          $destination_ammount->markup = $destination_ammount_markup[$key];
        }
        if ((isset($destination_ammount_price_per_unit[$key])) && (!empty($destination_ammount_price_per_unit[$key]))) {
          $destination_ammount->price_per_unit = $destination_ammount_price_per_unit[$key];
          $destination_ammount->currency_id = $destination_ammount_currency[$key];
        }
        if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
          $destination_ammount->total_ammount = $destination_total_ammount[$key];
        }
        if ((isset($destination_total_ammount_2[$key])) && (!empty($destination_total_ammount_2[$key]))) {
          $destination_ammount->total_ammount_2 = $destination_total_ammount_2[$key];
        }
        $destination_ammount->save();
      }
    }
    if(isset($input['schedule'])){
      if($input['schedule'] != 'null'){
        $schedules = json_decode($input['schedule']);
        foreach( $schedules as $schedule){ 
          $sche = json_decode($schedule);
          $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
          $saveSchedule  = new Schedule();
          $saveSchedule->vessel = $sche->VesselName;
          $saveSchedule->etd = $sche->Etd;
          $saveSchedule->transit_time =  $dias;
          $saveSchedule->eta = $sche->Eta;
          $saveSchedule->type = 'direct';
          $saveSchedule->quotes()->associate($quote);
          $saveSchedule->save(); 
        }
      }
    }
    // Schedule manual 
    if(isset($input['schedule_manual'])){
      if($input['schedule_manual'] != 'null'){
        $sche = json_decode($input['schedule_manual']);
        // dd($sche);
        $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
        $saveSchedule  = new Schedule();
        $saveSchedule->vessel = $sche->VesselName;
        $saveSchedule->etd = $sche->Etd;
        $saveSchedule->transit_time =  $dias;
        $saveSchedule->eta = $sche->Eta;
        $saveSchedule->type = 'direct';
        $saveSchedule->quotes()->associate($quote);
        $saveSchedule->save(); 
      }
    }

    $quantity = array_values( array_filter($input['quantity']) );
    $type_cargo = array_values( array_filter($input['type_load_cargo']) );
    $height = array_values( array_filter($input['height']) );
    $width = array_values( array_filter($input['width']) );
    $large = array_values( array_filter($input['large']) );
    $weight = array_values( array_filter($input['weight']) );
    $volume = array_values( array_filter($input['volume']) );

    if(count($quantity)>0){
      foreach($type_cargo as $key=>$item){
        $package_load = new PackageLoad();
        $package_load->quote_id = $quote->id;
        $package_load->type_cargo = $type_cargo[$key];
        $package_load->quantity = $quantity[$key];
        $package_load->height = $height[$key];
        $package_load->width = $width[$key];
        $package_load->large = $large[$key];
        $package_load->weight = $weight[$key];
        $package_load->total_weight = $weight[$key]*$quantity[$key];
        $package_load->volume = $volume[$key];
        $package_load->save();
      }
    }
    if(isset($input['btnsubmit']) && $input['btnsubmit'] == 'submit-pdf'){
      return redirect()->route('quotes.show', ['quote_id' => $quote->id])->with('pdf','true');
    }
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'Register completed successfully!');
    //return redirect()->route('quotes.index');
    return redirect()->action('QuoteController@show',$quote->id);
  }

  public function storeWithEmail(Request $request)
  {
    $input = Input::all();
    $company_quote = $this->idPersonalizado();    //ID PERSONALIZADO
    $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
    $request->request->add(['owner' => \Auth::id(),'currency_id'=>$currency->currency_id,'status_quote_id'=>2,'company_quote' => $company_quote]);
    $quote=Quote::create($request->all());
    if($input['origin_ammount_charge']!=[null]) {
      $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
      $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
      $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
      $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
      $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
      $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
      $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
      $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
      foreach ($origin_ammount_charge as $key => $item) {
        $origin_ammount = new OriginAmmount();
        $origin_ammount->quote_id = $quote->id;
        if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
          $origin_ammount->charge = $origin_ammount_charge[$key];
        }
        if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
          $origin_ammount->detail = $origin_ammount_detail[$key];
        }
        if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
          $origin_ammount->units = $origin_total_units[$key];
        }
        if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
          $origin_ammount->markup = $origin_total_markup[$key];
        }
        if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
          $origin_ammount->price_per_unit = $origin_ammount_price_per_unit[$key];
          $origin_ammount->currency_id = $origin_ammount_currency[$key];
        }
        if ((isset($origin_total_ammount[$key])) && ($origin_total_ammount[$key] != '')) {
          $origin_ammount->total_ammount = $origin_total_ammount[$key];
        }
        if ((isset($origin_total_ammount_2[$key])) && ($origin_total_ammount_2[$key] != '')) {
          $origin_ammount->total_ammount_2 = $origin_total_ammount_2[$key];
        }
        $origin_ammount->save();
      }
    }
    if($input['freight_ammount_charge']!=[null]) {
      $freight_ammount_charge = array_values( array_filter($input['freight_ammount_charge']) );
      $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
      $freight_ammount_price_per_unit = array_values( array_filter($input['freight_price_per_unit']) );
      $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
      $freight_total_units = array_values( array_filter($input['freight_ammount_units']) );
      $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
      $freight_total_ammount_2 = array_values( array_filter($input['freight_total_ammount_2']) );
      $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
      foreach ($freight_ammount_charge as $key => $item) {
        $freight_ammount = new FreightAmmount();
        $freight_ammount->quote_id = $quote->id;
        if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
          $freight_ammount->charge = $freight_ammount_charge[$key];
        }
        if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
          $freight_ammount->detail = $freight_ammount_detail[$key];
        }
        if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
          $freight_ammount->units = $freight_total_units[$key];
        }
        if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
          $freight_ammount->markup = $freight_total_markup[$key];
        }
        if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
          $freight_ammount->price_per_unit = $freight_ammount_price_per_unit[$key];
          $freight_ammount->currency_id = $freight_ammount_currency[$key];
        }
        if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
          $freight_ammount->total_ammount = $freight_total_ammount[$key];
        }
        if ((isset($freight_total_ammount_2[$key])) && ($freight_total_ammount_2[$key] != '')) {
          $freight_ammount->total_ammount_2 = $freight_total_ammount_2[$key];
        }
        $freight_ammount->save();
      }
    }
    if($input['destination_ammount_charge']!=[null]) {
      $destination_ammount_charge = array_values( array_filter($input['destination_ammount_charge']) );
      $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
      $destination_ammount_price_per_unit = array_values( array_filter($input['destination_price_per_unit']) );
      $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
      $destination_ammount_units = array_values( array_filter($input['destination_ammount_units']) );
      $destination_ammount_markup = array_values( array_filter($input['destination_ammount_markup']) );
      $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
      $destination_total_ammount_2 = array_values( array_filter($input['destination_total_ammount_2']) );
      foreach ($destination_ammount_charge as $key => $item) {
        $destination_ammount = new DestinationAmmount();
        $destination_ammount->quote_id = $quote->id;
        if ((isset($destination_ammount_charge[$key])) && (!empty($destination_ammount_charge[$key]))) {
          $destination_ammount->charge = $destination_ammount_charge[$key];
        }
        if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
          $destination_ammount->detail = $destination_ammount_detail[$key];
        }
        if ((isset($destination_ammount_units[$key])) && (!empty($destination_ammount_units[$key]))) {
          $destination_ammount->units = $destination_ammount_units[$key];
        }
        if ((isset($destination_ammount_markup[$key])) && (!empty($destination_ammount_markup[$key]))) {
          $destination_ammount->markup = $destination_ammount_markup[$key];
        }
        if ((isset($destination_ammount_price_per_unit[$key])) && (!empty($destination_ammount_price_per_unit[$key]))) {
          $destination_ammount->price_per_unit = $destination_ammount_price_per_unit[$key];
          $destination_ammount->currency_id = $destination_ammount_currency[$key];
        }
        if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
          $destination_ammount->total_ammount = $destination_total_ammount[$key];
        }
        if ((isset($destination_total_ammount_2[$key])) && (!empty($destination_total_ammount_2[$key]))) {
          $destination_ammount->total_ammount_2 = $destination_total_ammount_2[$key];
        }
        $destination_ammount->save();
      }
    }
    if(isset($input['schedule'])){
      if($input['schedule'] != 'null'){
        $schedules = json_decode($input['schedule']);
        foreach( $schedules as $schedule){ 
          $sche = json_decode($schedule);
          $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
          $saveSchedule  = new Schedule();
          $saveSchedule->vessel = $sche->VesselName;
          $saveSchedule->etd = $sche->Etd;
          $saveSchedule->transit_time =  $dias;
          $saveSchedule->eta = $sche->Eta;
          $saveSchedule->type = 'direct';
          $saveSchedule->quotes()->associate($quote);
          $saveSchedule->save(); 
        }
      }
    }
    // Schedule manual 
    if(isset($input['schedule_manual'])){
      if($input['schedule_manual'] != 'null'){
        $sche = json_decode($input['schedule_manual']);
        // dd($sche);
        $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
        $saveSchedule  = new Schedule();
        $saveSchedule->vessel = $sche->VesselName;
        $saveSchedule->etd = $sche->Etd;
        $saveSchedule->transit_time =  $dias;
        $saveSchedule->eta = $sche->Eta;
        $saveSchedule->type = 'direct';
        $saveSchedule->quotes()->associate($quote);
        $saveSchedule->save(); 
      }
    }
    $quantity = array_values( array_filter($input['quantity']) );
    $type_cargo = array_values( array_filter($input['type_load_cargo']) );
    $height = array_values( array_filter($input['height']) );
    $width = array_values( array_filter($input['width']) );
    $large = array_values( array_filter($input['large']) );
    $weight = array_values( array_filter($input['weight']) );
    $volume = array_values( array_filter($input['volume']) );

    if(count($quantity)>0){
      foreach($type_cargo as $key=>$item){
        $package_load = new PackageLoad();
        $package_load->quote_id = $quote->id;
        $package_load->type_cargo = $type_cargo[$key];
        $package_load->quantity = $quantity[$key];
        $package_load->height = $height[$key];
        $package_load->width = $width[$key];
        $package_load->large = $large[$key];
        $package_load->weight = $weight[$key];
        $package_load->total_weight = $weight[$key]*$quantity[$key];
        $package_load->volume = $volume[$key];
        $package_load->save();
      }
    }
    //Sending email
    if(isset($input['subject']) && isset($input['body'])){
      $subject = $input['subject'];
      $body = $input['body'];
      $contact_email = Contact::find($quote->contact_id);
      $companies = Company::all()->pluck('business_name','id');
      $harbors = Harbor::all()->pluck('name','id');
      $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
      $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
      $prices = Price::all()->pluck('name','id');
      $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
      $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
      $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
      $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
      $user = User::where('id',\Auth::id())->with('companyUser')->first();
      if(\Auth::user()->company_user_id){
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);
      }        
      $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
                                               'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg]);
      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');
      \Mail::to($contact_email->email)->send(new SendQuotePdf($subject,$body,$quote));
    }
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'Register completed successfully!');
    return redirect()->action('QuoteController@show',$quote->id);
  }

  function dias_transcurridos($fecha_i,$fecha_f)
  {
    $dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
    $dias 	= abs($dias); $dias = floor($dias);		
    return intval($dias);
  }

  public function showWithPdf($id){
    $currency_cfg='';
    $company_user='';
    $email_templates='';
    $exchange='';
    $companies='';
    $prices='';
    $pdf='yes';
    $terms_origin='';
    $terms_destination='';
    $quote = Quote::findOrFail($id);
    $harbors = Harbor::all()->pluck('name','id');
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
    $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
    $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
    $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    $status_quotes=StatusQuote::all()->pluck('name','id');
    $currencies = Currency::pluck('alphacode','id');
    $package_loads = PackageLoad::where('quote_id',$id)->get();
    if(\Auth::user()->company_user_id){
      $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $email_templates=EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
      $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
      if($currency_cfg->alphacode=='USD'){
        $exchange = Currency::where('api_code_eur','EURUSD')->first();
      }else{
        $exchange = Currency::where('api_code','USDEUR')->first();
      }
    }
    return view('quotes/show', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
                                'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'user'=>$user,'status_quotes'=>$status_quotes,'exchange'=>$exchange,'email_templates'=>$email_templates,'package_loads'=>$package_loads,'pdf'=>$pdf]);
  }

  public function show($id)
  {

    $currency_cfg='';
    $company_user='';
    $email_templates='';
    $exchange='';
    $companies='';
    $prices='';
    $terms_origin='';
    $terms_destination='';
    $quote = Quote::findOrFail($id);
    $harbors = Harbor::all()->pluck('name','id');
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
    $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
    $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
    $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    $status_quotes=StatusQuote::all()->pluck('name','id');
    $currencies = Currency::pluck('alphacode','id');
    $package_loads = PackageLoad::where('quote_id',$id)->get();
    if(\Auth::user()->company_user_id){
      $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
      })->get();
      $email_templates=EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
      $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
      $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
      if($currency_cfg->alphacode=='USD'){
        $exchange = Currency::where('api_code_eur','EURUSD')->first();
      }else{
        $exchange = Currency::where('api_code','USDEUR')->first();
      }
=======

            $quantity = array_values( array_filter($input['quantity']) );
            $type_cargo = array_values( array_filter($input['type_load_cargo']) );
            $height = array_values( array_filter($input['height']) );
            $width = array_values( array_filter($input['width']) );
            $large = array_values( array_filter($input['large']) );
            $weight = array_values( array_filter($input['weight']) );
            $volume = array_values( array_filter($input['volume']) );

            if(count($quantity)>0){
                foreach($type_cargo as $key=>$item){
                    $package_load = new PackageLoad();
                    $package_load->quote_id = $quote->id;
                    $package_load->type_cargo = $type_cargo[$key];
                    $package_load->quantity = $quantity[$key];
                    $package_load->height = $height[$key];
                    $package_load->width = $width[$key];
                    $package_load->large = $large[$key];
                    $package_load->weight = $weight[$key];
                    $package_load->total_weight = $weight[$key]*$quantity[$key];
                    $package_load->volume = $volume[$key];
                    $package_load->save();
                }
            }
            if(isset($input['btnsubmit']) && $input['btnsubmit'] == 'submit-pdf'){
                return redirect()->route('quotes.show', ['quote_id' => $quote->id])->with('pdf','true');
            }
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Register completed successfully!');
            //return redirect()->route('quotes.index');
            return redirect()->action('QuoteController@show',$quote->id);
        }
>>>>>>> remotes/origin/julio
    }

    public function storeWithEmail(Request $request)
    {
        $input = Input::all();
        $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
        $request->request->add(['owner' => \Auth::id(),'currency_id'=>$currency->currency_id,'status_quote_id'=>2]);
        $quote=Quote::create($request->all());
        if($input['origin_ammount_charge']!=[null]) {
            $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
            $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
            $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
            $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
            $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
            $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
            $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
            $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
            foreach ($origin_ammount_charge as $key => $item) {
                $origin_ammount = new OriginAmmount();
                $origin_ammount->quote_id = $quote->id;
                if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
                    $origin_ammount->charge = $origin_ammount_charge[$key];
                }
                if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
                    $origin_ammount->detail = $origin_ammount_detail[$key];
                }
                if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
                    $origin_ammount->units = $origin_total_units[$key];
                }
                if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
                    $origin_ammount->markup = $origin_total_markup[$key];
                }
                if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
                    $origin_ammount->price_per_unit = $origin_ammount_price_per_unit[$key];
                    $origin_ammount->currency_id = $origin_ammount_currency[$key];
                }
                if ((isset($origin_total_ammount[$key])) && ($origin_total_ammount[$key] != '')) {
                    $origin_ammount->total_ammount = $origin_total_ammount[$key];
                }
                if ((isset($origin_total_ammount_2[$key])) && ($origin_total_ammount_2[$key] != '')) {
                    $origin_ammount->total_ammount_2 = $origin_total_ammount_2[$key];
                }
                $origin_ammount->save();
            }
        }
        if($input['freight_ammount_charge']!=[null]) {
            $freight_ammount_charge = array_values( array_filter($input['freight_ammount_charge']) );
            $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
            $freight_ammount_price_per_unit = array_values( array_filter($input['freight_price_per_unit']) );
            $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
            $freight_total_units = array_values( array_filter($input['freight_ammount_units']) );
            $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
            $freight_total_ammount_2 = array_values( array_filter($input['freight_total_ammount_2']) );
            $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
            foreach ($freight_ammount_charge as $key => $item) {
                $freight_ammount = new FreightAmmount();
                $freight_ammount->quote_id = $quote->id;
                if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
                    $freight_ammount->charge = $freight_ammount_charge[$key];
                }
                if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                    $freight_ammount->detail = $freight_ammount_detail[$key];
                }
                if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
                    $freight_ammount->units = $freight_total_units[$key];
                }
                if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
                    $freight_ammount->markup = $freight_total_markup[$key];
                }
                if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
                    $freight_ammount->price_per_unit = $freight_ammount_price_per_unit[$key];
                    $freight_ammount->currency_id = $freight_ammount_currency[$key];
                }
                if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
                    $freight_ammount->total_ammount = $freight_total_ammount[$key];
                }
                if ((isset($freight_total_ammount_2[$key])) && ($freight_total_ammount_2[$key] != '')) {
                    $freight_ammount->total_ammount_2 = $freight_total_ammount_2[$key];
                }
                $freight_ammount->save();
            }
        }
        if($input['destination_ammount_charge']!=[null]) {
            $destination_ammount_charge = array_values( array_filter($input['destination_ammount_charge']) );
            $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
            $destination_ammount_price_per_unit = array_values( array_filter($input['destination_price_per_unit']) );
            $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
            $destination_ammount_units = array_values( array_filter($input['destination_ammount_units']) );
            $destination_ammount_markup = array_values( array_filter($input['destination_ammount_markup']) );
            $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
            $destination_total_ammount_2 = array_values( array_filter($input['destination_total_ammount_2']) );
            foreach ($destination_ammount_charge as $key => $item) {
                $destination_ammount = new DestinationAmmount();
                $destination_ammount->quote_id = $quote->id;
                if ((isset($destination_ammount_charge[$key])) && (!empty($destination_ammount_charge[$key]))) {
                    $destination_ammount->charge = $destination_ammount_charge[$key];
                }
                if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
                    $destination_ammount->detail = $destination_ammount_detail[$key];
                }
                if ((isset($destination_ammount_units[$key])) && (!empty($destination_ammount_units[$key]))) {
                    $destination_ammount->units = $destination_ammount_units[$key];
                }
                if ((isset($destination_ammount_markup[$key])) && (!empty($destination_ammount_markup[$key]))) {
                    $destination_ammount->markup = $destination_ammount_markup[$key];
                }
                if ((isset($destination_ammount_price_per_unit[$key])) && (!empty($destination_ammount_price_per_unit[$key]))) {
                    $destination_ammount->price_per_unit = $destination_ammount_price_per_unit[$key];
                    $destination_ammount->currency_id = $destination_ammount_currency[$key];
                }
                if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
                    $destination_ammount->total_ammount = $destination_total_ammount[$key];
                }
                if ((isset($destination_total_ammount_2[$key])) && (!empty($destination_total_ammount_2[$key]))) {
                    $destination_ammount->total_ammount_2 = $destination_total_ammount_2[$key];
                }
                $destination_ammount->save();
            }
        }
        if(isset($input['schedule'])){
            if($input['schedule'] != 'null'){
                $schedules = json_decode($input['schedule']);
                foreach( $schedules as $schedule){ 
                    $sche = json_decode($schedule);
                    $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
                    $saveSchedule  = new Schedule();
                    $saveSchedule->vessel = $sche->VesselName;
                    $saveSchedule->etd = $sche->Etd;
                    $saveSchedule->transit_time =  $dias;
                    $saveSchedule->eta = $sche->Eta;
                    $saveSchedule->type = 'direct';
                    $saveSchedule->quotes()->associate($quote);
                    $saveSchedule->save(); 
                }
            }
        }
        // Schedule manual 
        if(isset($input['schedule_manual'])){
            if($input['schedule_manual'] != 'null'){
                $sche = json_decode($input['schedule_manual']);
                // dd($sche);
                $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
                $saveSchedule  = new Schedule();
                $saveSchedule->vessel = $sche->VesselName;
                $saveSchedule->etd = $sche->Etd;
                $saveSchedule->transit_time =  $dias;
                $saveSchedule->eta = $sche->Eta;
                $saveSchedule->type = 'direct';
                $saveSchedule->quotes()->associate($quote);
                $saveSchedule->save(); 
            }
        }
        $quantity = array_values( array_filter($input['quantity']) );
        $type_cargo = array_values( array_filter($input['type_load_cargo']) );
        $height = array_values( array_filter($input['height']) );
        $width = array_values( array_filter($input['width']) );
        $large = array_values( array_filter($input['large']) );
        $weight = array_values( array_filter($input['weight']) );
        $volume = array_values( array_filter($input['volume']) );

        if(count($quantity)>0){
            foreach($type_cargo as $key=>$item){
                $package_load = new PackageLoad();
                $package_load->quote_id = $quote->id;
                $package_load->type_cargo = $type_cargo[$key];
                $package_load->quantity = $quantity[$key];
                $package_load->height = $height[$key];
                $package_load->width = $width[$key];
                $package_load->large = $large[$key];
                $package_load->weight = $weight[$key];
                $package_load->total_weight = $weight[$key]*$quantity[$key];
                $package_load->volume = $volume[$key];
                $package_load->save();
            }
        }
        //Sending email
        if(isset($input['subject']) && isset($input['body'])){
            $subject = $input['subject'];
            $body = $input['body'];
            $contact_email = Contact::find($quote->contact_id);
            $companies = Company::all()->pluck('business_name','id');
            $harbors = Harbor::all()->pluck('name','id');
            $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
            $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
            $prices = Price::all()->pluck('name','id');
            $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
            $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
            $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
            $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
            $user = User::where('id',\Auth::id())->with('companyUser')->first();
            if(\Auth::user()->company_user_id){
                $company_user=CompanyUser::find(\Auth::user()->company_user_id);
                $currency_cfg = Currency::find($company_user->currency_id);
            }        
            $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
                                                     'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg]);
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');
            \Mail::to($contact_email->email)->send(new SendQuotePdf($subject,$body,$quote));
        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');
        return redirect()->action('QuoteController@show',$quote->id);
    }

    function dias_transcurridos($fecha_i,$fecha_f)
    {
        $dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
        $dias 	= abs($dias); $dias = floor($dias);		
        return intval($dias);
    }

    public function showWithPdf($id){
        $currency_cfg='';
        $company_user='';
        $email_templates='';
        $exchange='';
        $companies='';
        $prices='';
        $pdf='yes';
        $terms_origin='';
        $terms_destination='';
        $quote = Quote::findOrFail($id);
        $harbors = Harbor::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $status_quotes=StatusQuote::all()->pluck('name','id');
        $currencies = Currency::pluck('alphacode','id');
        $package_loads = PackageLoad::where('quote_id',$id)->get();
        if(\Auth::user()->company_user_id){
            $port_all = harbor::where('name','ALL')->first();
            $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->orWhere('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->orWhere('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $email_templates=EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
            $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
            if($currency_cfg->alphacode=='USD'){
                $exchange = Currency::where('api_code_eur','EURUSD')->first();
            }else{
                $exchange = Currency::where('api_code','USDEUR')->first();
            }
        }
        return view('quotes/show', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                    'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
                                    'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'user'=>$user,'status_quotes'=>$status_quotes,'exchange'=>$exchange,'email_templates'=>$email_templates,'package_loads'=>$package_loads,'pdf'=>$pdf]);
    }

    public function show($id)
    {

        $currency_cfg='';
        $company_user='';
        $email_templates='';
        $exchange='';
        $companies='';
        $prices='';
        $terms_origin='';
        $terms_destination='';
        $quote = Quote::findOrFail($id);
        $harbors = Harbor::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $status_quotes=StatusQuote::all()->pluck('name','id');
        $currencies = Currency::pluck('alphacode','id');
        $package_loads = PackageLoad::where('quote_id',$id)->get();
        if(\Auth::user()->company_user_id){
            $port_all = harbor::where('name','ALL')->first();
            $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->orWhere('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->orWhere('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $email_templates=EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
            $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
            if($currency_cfg->alphacode=='USD'){
                $exchange = Currency::where('api_code_eur','EURUSD')->first();
            }else{
                $exchange = Currency::where('api_code','USDEUR')->first();
            }
        }

        return view('quotes/show', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                    'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
                                    'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'user'=>$user,'status_quotes'=>$status_quotes,'exchange'=>$exchange,'email_templates'=>$email_templates,'package_loads'=>$package_loads]);
    }

    public function update(Request $request, $id)
    {
        $input = Input::all();
        $quote = Quote::find($id);
        $quote->update($request->all());
        OriginAmmount::where('quote_id',$quote->id)->delete();
        FreightAmmount::where('quote_id',$quote->id)->delete();
        DestinationAmmount::where('quote_id',$quote->id)->delete();
        PackageLoad::where('quote_id',$quote->id)->delete();
        if($input['origin_ammount_charge']!=[null]) {
            $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
            $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
            $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
            $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
            $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
            $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
            $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
            $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
            foreach ($origin_ammount_charge as $key => $item) {
                $origin_ammount = new OriginAmmount();
                $origin_ammount->quote_id = $quote->id;
                if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
                    $origin_ammount->charge = $origin_ammount_charge[$key];
                }
                if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
                    $origin_ammount->detail = $origin_ammount_detail[$key];
                }
                if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
                    $origin_ammount->units = $origin_total_units[$key];
                }
                if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
                    $origin_ammount->markup = $origin_total_markup[$key];
                }
                if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
                    $origin_ammount->price_per_unit = $origin_ammount_price_per_unit[$key];
                    $origin_ammount->currency_id = $origin_ammount_currency[$key];
                }
                if ((isset($origin_total_ammount[$key])) && ($origin_total_ammount[$key] != '')) {
                    $origin_ammount->total_ammount = $origin_total_ammount[$key];
                }
                if ((isset($origin_total_ammount_2[$key])) && ($origin_total_ammount_2[$key] != '')) {
                    $origin_ammount->total_ammount_2 = $origin_total_ammount_2[$key];
                }
                $origin_ammount->save();
            }
        }
        if($input['freight_ammount_charge']!=[null]) {
            $freight_ammount_charge = array_values( array_filter($input['freight_ammount_charge']) );
            $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
            $freight_ammount_price_per_unit = array_values( array_filter($input['freight_price_per_unit']) );
            $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
            $freight_total_units = array_values( array_filter($input['freight_ammount_units']) );
            $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
            $freight_total_ammount_2 = array_values( array_filter($input['freight_total_ammount_2']) );
            $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
            foreach ($freight_ammount_charge as $key => $item) {
                $freight_ammount = new FreightAmmount();
                $freight_ammount->quote_id = $quote->id;
                if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
                    $freight_ammount->charge = $freight_ammount_charge[$key];
                }
                if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                    $freight_ammount->detail = $freight_ammount_detail[$key];
                }
                if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
                    $freight_ammount->units = $freight_total_units[$key];
                }
                if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
                    $freight_ammount->markup = $freight_total_markup[$key];
                }
                if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
                    $freight_ammount->price_per_unit = $freight_ammount_price_per_unit[$key];
                    $freight_ammount->currency_id = $freight_ammount_currency[$key];
                }
                if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
                    $freight_ammount->total_ammount = $freight_total_ammount[$key];
                }
                if ((isset($freight_total_ammount_2[$key])) && ($freight_total_ammount_2[$key] != '')) {
                    $freight_ammount->total_ammount_2 = $freight_total_ammount_2[$key];
                }
                $freight_ammount->save();
            }
        }
        if($input['destination_ammount_charge']!=[null]) {
            $destination_ammount_charge = array_values( array_filter($input['destination_ammount_charge']) );
            $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
            $destination_ammount_price_per_unit = array_values( array_filter($input['destination_price_per_unit']) );
            $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
            $destination_ammount_units = array_values( array_filter($input['destination_ammount_units']) );
            $destination_ammount_markup = array_values( array_filter($input['destination_ammount_markup']) );
            $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
            $destination_total_ammount_2 = array_values( array_filter($input['destination_total_ammount_2']) );
            foreach ($destination_ammount_charge as $key => $item) {
                $destination_ammount = new DestinationAmmount();
                $destination_ammount->quote_id = $quote->id;
                if ((isset($destination_ammount_charge[$key])) && (!empty($destination_ammount_charge[$key]))) {
                    $destination_ammount->charge = $destination_ammount_charge[$key];
                }
                if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
                    $destination_ammount->detail = $destination_ammount_detail[$key];
                }
                if ((isset($destination_ammount_units[$key])) && (!empty($destination_ammount_units[$key]))) {
                    $destination_ammount->units = $destination_ammount_units[$key];
                }
                if ((isset($destination_ammount_markup[$key])) && (!empty($destination_ammount_markup[$key]))) {
                    $destination_ammount->markup = $destination_ammount_markup[$key];
                }
                if ((isset($destination_ammount_price_per_unit[$key])) && (!empty($destination_ammount_price_per_unit[$key]))) {
                    $destination_ammount->price_per_unit = $destination_ammount_price_per_unit[$key];
                    $destination_ammount->currency_id = $destination_ammount_currency[$key];
                }
                if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
                    $destination_ammount->total_ammount = $destination_total_ammount[$key];
                }
                if ((isset($destination_total_ammount_2[$key])) && (!empty($destination_total_ammount_2[$key]))) {
                    $destination_ammount->total_ammount_2 = $destination_total_ammount_2[$key];
                }
                $destination_ammount->save();
            }
        }

        $quantity = array_values( array_filter($input['quantity']) );
        $type_cargo = array_values( array_filter($input['type_load_cargo']) );
        $height = array_values( array_filter($input['height']) );
        $width = array_values( array_filter($input['width']) );
        $large = array_values( array_filter($input['large']) );
        $weight = array_values( array_filter($input['weight']) );
        $volume = array_values( array_filter($input['volume']) );

        //dd($quantity);
        if(count($quantity)>0){
            foreach($type_cargo as $key=>$item){
                $package_load = new PackageLoad();
                $package_load->quote_id = $quote->id;
                $package_load->type_cargo = $type_cargo[$key];
                $package_load->quantity = $quantity[$key];
                $package_load->height = $height[$key];
                $package_load->width = $width[$key];
                $package_load->large = $large[$key];
                $package_load->weight = $weight[$key];
                $package_load->total_weight = $weight[$key]*$quantity[$key];
                $package_load->volume = $volume[$key];
                $package_load->save();
            }
        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->route('quotes.index');
    }

    public function destroy($id)
    {
        $quote = Quote::find($id);
        $quote->delete();
        return $quote;
    }
    public function getHarborName($id)
    {
        $harbor = Harbor::findOrFail($id);
        return $harbor;
    }
    public function getAirportName($id)
    {
        $airport = Airport::findOrFail($id);
        return $airport;
    }
    public function getQuoteTerms($id)
    {
        $terms = TermAndCondition::where('harbor_id',$id)->first();
        return $terms;
    }
    public function duplicate(Request $request,$id)
    {
        $quotes = Quote::all();
        $quote = Quote::findOrFail($id);
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $countries = Country::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $prices = Price::all()->pluck('name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $packaging_loads = PackageLoad::where('quote_id',$quote->id)->get();

        $quote_duplicate = new Quote();
        $quote_duplicate->owner=\Auth::id();
        $quote_duplicate->incoterm=$quote->incoterm;
        $quote_duplicate->modality=$quote->modality;
        $quote_duplicate->currency_id=$quote->currency_id;
        $quote_duplicate->pick_up_date=$quote->pick_up_date;
        if($quote->validity){
            $quote_duplicate->validity=$quote->validity;
        }
        if($quote->origin_address){
            $quote_duplicate->origin_address=$quote->origin_address;
        }
        if($quote->destination_address){
            $quote_duplicate->destination_address=$quote->destination_address;
        }
        if($quote->company_id){
            $quote_duplicate->company_id=$quote->company_id;
        }
        if($quote->origin_harbor_id){
            $quote_duplicate->origin_harbor_id=$quote->origin_harbor_id;
        }
        if($quote->destination_harbor_id){
            $quote_duplicate->destination_harbor_id=$quote->destination_harbor_id;
        }
        if($quote->origin_airport_id){
            $quote_duplicate->origin_airport_id=$quote->origin_airport_id;
        }
        if($quote->destination_airport_id){
            $quote_duplicate->destination_airport_id=$quote->destination_airport_id;
        }
        if($quote->price_id){
            $quote_duplicate->price_id=$quote->price_id;
        }
        if($quote->contact_id){
            $quote_duplicate->contact_id=$quote->contact_id;
        }
        if($quote->qty_20){
            $quote_duplicate->qty_20=$quote->qty_20;
        }
        if($quote->qty_40){
            $quote_duplicate->qty_40=$quote->qty_40;
        }
        if($quote->qty_40_hc){
            $quote_duplicate->qty_40_hc=$quote->qty_40_hc;
        }
        if($quote->delivery_type){
            $quote_duplicate->delivery_type=$quote->delivery_type;
        }
        if($quote->sub_total_origin){
            $quote_duplicate->sub_total_origin=$quote->sub_total_origin;
        }
        if($quote->sub_total_freight){
            $quote_duplicate->sub_total_freight=$quote->sub_total_freight;
        }
        if($quote->sub_total_destination){
            $quote_duplicate->sub_total_destination=$quote->sub_total_destination;
        }
        if($quote->total_markut_origin){
            $quote_duplicate->total_markut_origin=$quote->total_markut_origin;
        }
        if($quote->total_markut_freight){
            $quote_duplicate->total_markut_freight=$quote->total_markut_freight;
        }
        if($quote->total_markut_destination){
            $quote_duplicate->total_markut_destination=$quote->total_markut_destination;
        }
        if($quote->carrier_id){
            $quote_duplicate->carrier_id=$quote->carrier_id;
        }
        if($quote->airline_id){
            $quote_duplicate->airline_id=$quote->airline_id;
        }        
        $quote_duplicate->status_quote_id=$quote->status_quote_id;
        $quote_duplicate->type_cargo=$quote->type_cargo;
        $quote_duplicate->type=$quote->type;
        $quote_duplicate->save();
        foreach ($origin_ammounts as $origin){
            $origin_ammount_duplicate = new OriginAmmount();
            $origin_ammount_duplicate->charge=$origin->charge;
            $origin_ammount_duplicate->detail=$origin->detail;
            $origin_ammount_duplicate->units=$origin->units;
            $origin_ammount_duplicate->price_per_unit=$origin->price_per_unit;
            $origin_ammount_duplicate->markup=$origin->markup;
            $origin_ammount_duplicate->currency_id=$origin->currency_id;
            $origin_ammount_duplicate->total_ammount=$origin->total_ammount;
            if($origin->total_ammount_2){
                $origin_ammount_duplicate->total_ammount_2=$origin->total_ammount_2;
            }
            $origin_ammount_duplicate->quote_id=$quote_duplicate->id;
            $origin_ammount_duplicate->save();
        }
        foreach ($freight_ammounts as $freight){
            $freight_ammount_duplicate = new FreightAmmount();
            $freight_ammount_duplicate->charge=$freight->charge;
            $freight_ammount_duplicate->detail=$freight->detail;
            $freight_ammount_duplicate->units=$freight->units;
            $freight_ammount_duplicate->price_per_unit=$freight->price_per_unit;
            $freight_ammount_duplicate->markup=$freight->markup;
            $freight_ammount_duplicate->currency_id=$freight->currency_id;
            $freight_ammount_duplicate->total_ammount=$freight->total_ammount;
            if($freight->total_ammount_2){
                $freight_ammount_duplicate->total_ammount_2=$freight->total_ammount_2;
            }
            $freight_ammount_duplicate->quote_id=$quote_duplicate->id;
            $freight_ammount_duplicate->save();
        }
        foreach ($destination_ammounts as $destination){
            $destination_ammount_duplicate = new DestinationAmmount();
            $destination_ammount_duplicate->charge=$destination->charge;
            $destination_ammount_duplicate->detail=$destination->detail;
            $destination_ammount_duplicate->units=$destination->units;
            $destination_ammount_duplicate->price_per_unit=$destination->price_per_unit;
            $destination_ammount_duplicate->markup=$destination->markup;
            $destination_ammount_duplicate->currency_id=$destination->currency_id;
            $destination_ammount_duplicate->total_ammount=$destination->total_ammount;
            if($destination->total_ammount_2){
                $destination_ammount_duplicate->total_ammount_2=$destination->total_ammount_2;
            }
            $destination_ammount_duplicate->quote_id=$quote_duplicate->id;
            $destination_ammount_duplicate->save();
        }

        foreach ($packaging_loads as $packaging_load){
            $packaging_load_duplicate = new PackageLoad();
            $packaging_load_duplicate->type_cargo=$packaging_load->type_cargo;
            $packaging_load_duplicate->quantity=$packaging_load->quantity;
            $packaging_load_duplicate->height=$packaging_load->height;
            $packaging_load_duplicate->width=$packaging_load->width;
            $packaging_load_duplicate->large=$packaging_load->large;
            $packaging_load_duplicate->weight=$packaging_load->weight;
            $packaging_load_duplicate->total_weight=$packaging_load->total_weight;
            $packaging_load_duplicate->volume=$packaging_load->volume;
            $packaging_load_duplicate->quote_id=$quote_duplicate->id;
            $packaging_load_duplicate->save();
        }

        if($request->ajax()){
            return response()->json(['message' => 'Ok']);
        }else{
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Quote duplicated successfully!');
            return redirect()->action('QuoteController@show',$quote_duplicate->id);
        }
    }
    public function updateStatus(Request $request,$id)
    {
        $quote=Quote::findOrFail($id);
        $quote->status_quote_id=$request->status_quote_id;
        $quote->update();
        $quotes = Quote::all();
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $countries = Country::all()->pluck('name','id');
        if($request->ajax()){
            return response()->json(['message' => 'Ok']);
        }else{
            return redirect()->route('quotes.index', compact(['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors]));
        }
    }
    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $quote=Quote::findOrFail($id);
        $status_quotes=StatusQuote::pluck('name','id');
        return view('quotes.changeStatus',compact('quote','status_quotes'));
    }
    public function scheduleManual($orig_port,$dest_port,$date_pick)
    {
        $code_orig = $this->getHarborName($orig_port);
        $code_dest = $this->getHarborName($dest_port);
        $date  = $date_pick;
        $carrier = 'maersk';
        // Armar los schedules
        try{
            $url = "http://schedules.cargofive.com/schedule/".$carrier."/".$code_orig->code."/".$code_dest->code;
            $client = new Client();
            $res = $client->request('GET', $url, [
            ]);
            $schedules = Collection::make(json_decode($res->getBody()));
            //  $schedules= $schedules->where($schedules->schedules->Etd,'2018-07-16');
            $schedulesArr = new Collection();
            $schedulesFin = new Collection();
            if(!$schedules->isEmpty()){
                foreach($schedules['schedules'] as $schedules){
                    $collectS = Collection::make($schedules);
                    $days =  $this->dias_transcurridos($schedules->Eta,$schedules->Etd);
                    $collectS->put('days',$days);
                    if($schedules->Transfer > 1){
                        $collectS->put('type','Scale');
                    }else{
                        $collectS->put('type','Direct');
                    }
                    $schedulesArr->push($collectS);
                }
                //'2018-07-24'
                $dateSchedule = strtotime($date);
                $dateSchedule =  date('Y-m-d',$dateSchedule);
                if(!$schedulesArr->isEmpty()){ 
                    $schedulesArr =  $schedulesArr->where('Etd','>=', $dateSchedule)->first();
                    $schedulesFin->push($schedulesArr);
                }
            }
        }catch (\Guzzle\Http\Exception\ConnectException $e) {
        }
        return view('quotes.scheduleInfo',compact('code_orig','code_dest','schedulesFin'));
    }

    public function StoreWithPdf(Request $request)
    {
        $input = Input::all();
        $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
        $request->request->add(['owner' => \Auth::id(),'currency_id'=>$currency->currency_id,'status_quote_id'=>2]);
        $quote=Quote::create($request->all());
        if($input['origin_ammount_charge']!=[null]) {
            $origin_ammount_charge = array_values( array_filter($input['origin_ammount_charge']) );
            $origin_ammount_detail = array_values( array_filter($input['origin_ammount_detail']) );
            $origin_ammount_price_per_unit = array_values( array_filter($input['origin_price_per_unit']) );
            $origin_ammount_currency = array_values( array_filter($input['origin_ammount_currency']) );
            $origin_total_units = array_values( array_filter($input['origin_ammount_units']) );
            $origin_total_ammount = array_values( array_filter($input['origin_total_ammount']) );
            $origin_total_ammount_2 = array_values( array_filter($input['origin_total_ammount_2']) );
            $origin_total_markup = array_values( array_filter($input['origin_ammount_markup']) );
            foreach ($origin_ammount_charge as $key => $item) {
                $origin_ammount = new OriginAmmount();
                $origin_ammount->quote_id = $quote->id;
                if ((isset($origin_ammount_charge[$key])) && (!empty($origin_ammount_charge[$key]))) {
                    $origin_ammount->charge = $origin_ammount_charge[$key];
                }
                if ((isset($origin_ammount_detail[$key])) && (!empty($origin_ammount_detail[$key]))) {
                    $origin_ammount->detail = $origin_ammount_detail[$key];
                }
                if ((isset($origin_total_units[$key])) && (!empty($origin_total_units[$key]))) {
                    $origin_ammount->units = $origin_total_units[$key];
                }
                if ((isset($origin_total_markup[$key])) && (!empty($origin_total_markup[$key]))) {
                    $origin_ammount->markup = $origin_total_markup[$key];
                }
                if ((isset($origin_ammount_price_per_unit[$key])) && ($origin_ammount_price_per_unit[$key]) != '') {
                    $origin_ammount->price_per_unit = $origin_ammount_price_per_unit[$key];
                    $origin_ammount->currency_id = $origin_ammount_currency[$key];
                }
                if ((isset($origin_total_ammount[$key])) && ($origin_total_ammount[$key] != '')) {
                    $origin_ammount->total_ammount = $origin_total_ammount[$key];
                }
                if ((isset($origin_total_ammount_2[$key])) && ($origin_total_ammount_2[$key] != '')) {
                    $origin_ammount->total_ammount_2 = $origin_total_ammount_2[$key];
                }
                $origin_ammount->save();
            }
        }
        if($input['freight_ammount_charge']!=[null]) {
            $freight_ammount_charge = array_values( array_filter($input['freight_ammount_charge']) );
            $freight_ammount_detail = array_values( array_filter($input['freight_ammount_detail']) );
            $freight_ammount_price_per_unit = array_values( array_filter($input['freight_price_per_unit']) );
            $freight_ammount_currency = array_values( array_filter($input['freight_ammount_currency']) );
            $freight_total_units = array_values( array_filter($input['freight_ammount_units']) );
            $freight_total_ammount = array_values( array_filter($input['freight_total_ammount']) );
            $freight_total_ammount_2 = array_values( array_filter($input['freight_total_ammount_2']) );
            $freight_total_markup = array_values( array_filter($input['freight_ammount_markup']) );
            foreach ($freight_ammount_charge as $key => $item) {
                $freight_ammount = new FreightAmmount();
                $freight_ammount->quote_id = $quote->id;
                if ((isset($freight_ammount_charge[$key])) && (!empty($freight_ammount_charge[$key]))) {
                    $freight_ammount->charge = $freight_ammount_charge[$key];
                }
                if ((isset($freight_ammount_detail[$key])) && (!empty($freight_ammount_detail[$key]))) {
                    $freight_ammount->detail = $freight_ammount_detail[$key];
                }
                if ((isset($freight_total_units[$key])) && (!empty($freight_total_units[$key]))) {
                    $freight_ammount->units = $freight_total_units[$key];
                }
                if ((isset($freight_total_markup[$key])) && (!empty($freight_total_markup[$key]))) {
                    $freight_ammount->markup = $freight_total_markup[$key];
                }
                if ((isset($freight_ammount_price_per_unit[$key])) && ($freight_ammount_price_per_unit[$key]) != '') {
                    $freight_ammount->price_per_unit = $freight_ammount_price_per_unit[$key];
                    $freight_ammount->currency_id = $freight_ammount_currency[$key];
                }
                if ((isset($freight_total_ammount[$key])) && ($freight_total_ammount[$key] != '')) {
                    $freight_ammount->total_ammount = $freight_total_ammount[$key];
                }
                if ((isset($freight_total_ammount_2[$key])) && ($freight_total_ammount_2[$key] != '')) {
                    $freight_ammount->total_ammount_2 = $freight_total_ammount_2[$key];
                }
                $freight_ammount->save();
            }
        }
        if($input['destination_ammount_charge']!=[null]) {
            $destination_ammount_charge = array_values( array_filter($input['destination_ammount_charge']) );
            $destination_ammount_detail = array_values( array_filter($input['destination_ammount_detail']) );
            $destination_ammount_price_per_unit = array_values( array_filter($input['destination_price_per_unit']) );
            $destination_ammount_currency = array_values( array_filter($input['destination_ammount_currency']) );
            $destination_ammount_units = array_values( array_filter($input['destination_ammount_units']) );
            $destination_ammount_markup = array_values( array_filter($input['destination_ammount_markup']) );
            $destination_total_ammount = array_values( array_filter($input['destination_total_ammount']) );
            $destination_total_ammount_2 = array_values( array_filter($input['destination_total_ammount_2']) );
            foreach ($destination_ammount_charge as $key => $item) {
                $destination_ammount = new DestinationAmmount();
                $destination_ammount->quote_id = $quote->id;
                if ((isset($destination_ammount_charge[$key])) && (!empty($destination_ammount_charge[$key]))) {
                    $destination_ammount->charge = $destination_ammount_charge[$key];
                }
                if ((isset($destination_ammount_detail[$key])) && (!empty($destination_ammount_detail[$key]))) {
                    $destination_ammount->detail = $destination_ammount_detail[$key];
                }
                if ((isset($destination_ammount_units[$key])) && (!empty($destination_ammount_units[$key]))) {
                    $destination_ammount->units = $destination_ammount_units[$key];
                }
                if ((isset($destination_ammount_markup[$key])) && (!empty($destination_ammount_markup[$key]))) {
                    $destination_ammount->markup = $destination_ammount_markup[$key];
                }
                if ((isset($destination_ammount_price_per_unit[$key])) && (!empty($destination_ammount_price_per_unit[$key]))) {
                    $destination_ammount->price_per_unit = $destination_ammount_price_per_unit[$key];
                    $destination_ammount->currency_id = $destination_ammount_currency[$key];
                }
                if ((isset($destination_total_ammount[$key])) && (!empty($destination_total_ammount[$key]))) {
                    $destination_ammount->total_ammount = $destination_total_ammount[$key];
                }
                if ((isset($destination_total_ammount_2[$key])) && (!empty($destination_total_ammount_2[$key]))) {
                    $destination_ammount->total_ammount_2 = $destination_total_ammount_2[$key];
                }
                $destination_ammount->save();
            }
        }
        if(isset($input['schedule'])){
            if($input['schedule'] != 'null'){
                $schedules = json_decode($input['schedule']);
                foreach( $schedules as $schedule){ 
                    $sche = json_decode($schedule);
                    $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
                    $saveSchedule  = new Schedule();
                    $saveSchedule->vessel = $sche->VesselName;
                    $saveSchedule->etd = $sche->Etd;
                    $saveSchedule->transit_time =  $dias;
                    $saveSchedule->eta = $sche->Eta;
                    $saveSchedule->type = 'direct';
                    $saveSchedule->quotes()->associate($quote);
                    $saveSchedule->save(); 
                }
            }
        }
        // Schedule manual 
        if(isset($input['schedule_manual'])){
            if($input['schedule_manual'] != 'null'){
                $sche = json_decode($input['schedule_manual']);
                // dd($sche);
                $dias = $this->dias_transcurridos($sche->Eta,$sche->Etd);
                $saveSchedule  = new Schedule();
                $saveSchedule->vessel = $sche->VesselName;
                $saveSchedule->etd = $sche->Etd;
                $saveSchedule->transit_time =  $dias;
                $saveSchedule->eta = $sche->Eta;
                $saveSchedule->type = 'direct';
                $saveSchedule->quotes()->associate($quote);
                $saveSchedule->save(); 
            }
        }

<<<<<<< HEAD
    $contact_email = Contact::find($quote->contact_id);
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
    $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
    $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
    }        
    $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg]);
    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($view);

    //$pdf->download('quote');

    return redirect()->action('QuoteController@showWithPdf',$quote->id);
  }

  public function idPersonalizado(){
    $user_company = CompanyUser::where('id',\Auth::user()->company_user_id)->first(); 
    $iniciales =  strtoupper(substr($user_company->name,0, 2)); 
    $quote = Quote::where('company_id',$user_company->id)->first();

    if($quote == null){
      $iniciales = $iniciales."-1";
    }else{
      $numeroFinal = explode('-',$quote->company_quote);

      $numeroFinal = $numeroFinal[1] +1;
      $iniciales = $iniciales."-".$numeroFinal;
    }
    return $iniciales;
  }
=======
        $quantity = array_values( array_filter($input['quantity']) );
        $type_cargo = array_values( array_filter($input['type_load_cargo']) );
        $height = array_values( array_filter($input['height']) );
        $width = array_values( array_filter($input['width']) );
        $large = array_values( array_filter($input['large']) );
        $weight = array_values( array_filter($input['weight']) );
        $volume = array_values( array_filter($input['volume']) );

        if(count($quantity)>0){
            foreach($type_cargo as $key=>$item){
                $package_load = new PackageLoad();
                $package_load->quote_id = $quote->id;
                $package_load->type_cargo = $type_cargo[$key];
                $package_load->quantity = $quantity[$key];
                $package_load->height = $height[$key];
                $package_load->width = $width[$key];
                $package_load->large = $large[$key];
                $package_load->weight = $weight[$key];
                $package_load->total_weight = $weight[$key]*$quantity[$key];
                $package_load->volume = $volume[$key];
                $package_load->save();
            }
        }

        $contact_email = Contact::find($quote->contact_id);
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }        
        $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg]);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        //$pdf->download('quote');

        return redirect()->action('QuoteController@showWithPdf',$quote->id);
    }
>>>>>>> remotes/origin/julio
}