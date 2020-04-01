<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Exports\QuotesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\AutomaticRate;
use App\AutomaticInland;
use App\AutomaticInlandLclAir;
use App\CalculationType;
use App\CalculationTypeLcl;
use App\Charge;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\EmailTemplate;
use App\Harbor;
use App\Incoterm;
use App\Price;
use App\Inland;
use App\Quote;
use App\Carrier;
use App\QuoteV2;
use App\Surcharge;
use App\User;
use App\PdfOption;
use App\Jobs\SendQuotes;
use App\SendQuote;
use App\Contract;
use App\Rate;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
use App\RateApi;
use App\LocalChargeApi;
use App\LocalChargeCarrierApi;
use App\LocalChargePortApi;
use App\PackageLoad;
use App\ChargeLclAir;
use App\Schedule;
use GoogleMaps;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Collection as Collection;
use App\Repositories\Schedules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\PackageLoadV2;
use App\Airline;
use App\TermsPort;
use App\TermsAndCondition;
use App\TermAndConditionV2;
use App\ScheduleType;
use App\RemarkCondition;
use App\RemarkHarbor;
use App\RemarkCarrier;
use App\EmailSetting;
use App\SaleTermV2;
use App\Airport;
//LCL
use App\ContractLcl;
use App\RateLcl;
use App\LocalChargeLcl;
use App\LocalCharCarrierLcl;
use App\LocalCharPortLcl;
use App\GlobalChargeLcl;
use App\GlobalCharCarrierLcl;
use App\GlobalCharPortLcl;
use App\NewContractRequest;
use App\NewContractRequestLcl;
use Illuminate\Support\Facades\Storage;
use App\SearchRate;
use App\SearchPort;
use App\ViewQuoteV2;
use App\ContractFclFile;
use App\ContractLclFile;
use App\Http\Traits\QuoteV2Trait;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\MediaStream;
use App\Jobs\ProcessPdfApi;
use App\Jobs\UpdatePdf;

class QuoteV2Controller extends Controller
{

    use QuoteV2Trait;

    /**
   * Show quotes list
   * @param Request $request 
   * @return Illuminate\View\View
   */
    public function index(Request $request){
        $company_user = null;
        $currency_cfg = null;
        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = QuoteV2::where('user_id',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->with(['rates_v2'=>function($query){
                $query->with('origin_port','destination_port','origin_airport','destination_airport','currency','charge','charge_lcl_air');
            }])->get();
        }else{
            $quotes = QuoteV2::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->with(['rates_v2'=>function($query){
                $query->with('origin_port','destination_port','origin_airport','destination_airport','currency','charge','charge_lcl_air');
            }])->get();
        }
        $companies = Company::pluck('business_name','id');
        $harbors = Harbor::pluck('display_name','id');
        $countries = Country::pluck('name','id');
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        if($request->ajax()){
            $quotes->load('user','company','contact','incoterm');
            $collection = Collection::make($quotes);
            $collection->transform(function ($quote, $key) {
                unset($quote['origin_port_id']);
                unset($quote['destination_port_id']);
                unset($quote['origin_address']);
                unset($quote['destination_address']);
                unset($quote['currency_id']);
                return $quote;
            });
            return $collection;
        }

        return view('quotesv2/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'currency_cfg'=>$currency_cfg]);
    }

    public function LoadDatatableIndex(){

        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = ViewQuoteV2::where('user_id',\Auth::user()->id)->orderBy('created_at', 'desc')->get();
        }else{
            $quotes = ViewQuoteV2::where('company_user_id',$company_user_id)->orderBy('created_at', 'desc')->get();
        }

        $colletions = collect([]);
        foreach($quotes as $quote){
            $custom_id      = '---';
            $company  = '---';
            $origin = '';
            $destination = '';
            $origin_li = '';
            $destination_li = '';

            if(isset($quote->company)){
                $company  = $quote->company->business_name;
            }
            if($quote->custom_quote_id!=''){
                $id  = $quote->custom_quote_id;
            }else{
                $id = $quote->quote_id;
            }

            if($quote->type=='AIR'){
                $origin=$quote->origin_airport;
                $destination=$quote->destination_airport;
                $img='<img src="/images/plane-blue.svg" class="img img-responsive" width="25">';
            }else{
                $origin=$quote->origin_port;
                $destination=$quote->destination_port;
                $img='<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
            }

            $explode_orig = explode("| ",$origin);
            $explode_dest = explode("| ",$destination);

            foreach($explode_orig as $item){
                $origin_li.='<li>'.$item.'</li>';
            }

            foreach($explode_dest as $item){
                $destination_li.='<li>'.$item.'</li>';
            }

            if($quote->business_name!=''){
                $company = $quote->business_name;
            }else{
                $company = '---';
            }

            if($quote->contact!=''){
                $contact = $quote->contact;
            }else{
                $contact = '---';
            }

            $data = [
                'id'            => $id,
                'idSet'         => setearRouteKey($quote->id),
                'client'        => $company,
                'contact'       => $contact,
                'user'          => $quote->owner,                
                'created'       => date_format($quote->created_at, 'M d, Y H:i'),
                'origin'        => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  See origins
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                  <small>'.$origin_li.'</small>
                                  </div>',
                'destination'   => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  See destinations
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                  <small>'.$destination_li.'</small>
                                  </div>',
                'type'          => $quote->type,
            ];
            $colletions->push($data);
        }
        return DataTables::of($colletions)

            ->addColumn('action',function($colletion){
                return
                    '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Options
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
          <a class="dropdown-item" href="/v2/quotes/show/'.$colletion['idSet'].'">
          <span>
          <i class="la la-edit"></i>
          &nbsp;
          Edit
          </span>
          </a>
          <a href="/v2/quotes/duplicate/'.$colletion['idSet'].'" class="dropdown-item" >
          <span>
          <i class="la la-plus"></i>
          &nbsp;
          Duplicate
          </span>
          </a>
          <a href="#" class="dropdown-item" id="delete-quote-v2" data-quote-id="'.$colletion['idSet'].'" >
          <span>
          <i class="la la-eraser"></i>
          &nbsp;
          Delete
          </span>
          </a>
          </div>';
            })->editColumn('id', '{{$id}}')->make(true);
    }

    /**
   * Mostrar detalles de una cotización
   * @param integer $id 
   * @return Illuminate\View\View
   */

    public function show(Request $request, $id)
    {
        //Setting id
        $id = obtenerRouteKey($id);
        $origin_charges = new Collection();
        $freight_charges = new Collection();
        $destination_charges = new Collection();
        $equipmentHides = null;
        $originAddressHides = 'hide';
        $destinationAddressHides = 'hide';
        $currency_name = null;

        //Retrieving all data
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        if($company_user->companyUser) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
        }

        $company_user_id = \Auth::user()->company_user_id;
        $quote = QuoteV2::with(['rates_v2'=>function($query){
            $query->with('origin_port','destination_port','origin_airport','destination_airport','currency','charge','charge_lcl_air');
        }])->findOrFail($id);
        $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
        $inlands = AutomaticInland::where('quote_id',$quote->id)->get();
        $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge','automaticInlandLclAir','charge_lcl_air')->get();
        $harbors = Harbor::get()->pluck('display_name','id');
        $countries = Country::pluck('name','id');
        $currency_cfg = Currency::find($company_user->currency_id);
        $sale_terms = SaleTermV2::where('quote_id',$quote->id)->get();
        $sale_terms_origin = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
        $sale_terms_destination = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();

        if($quote->delivery_type==2 || $quote->delivery_type==4){
            $destinationAddressHides = null;
        }

        if($quote->delivery_type==3 || $quote->delivery_type==4){
            $originAddressHides = null;
        }

        foreach($sale_terms_origin as $value){
            foreach($value->charge as $item){
                if($item->currency_id!=''){
                    if($quote->pdf_option->grouped_origin_charges==1){
                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                    $item->sum20 += $item->c20/$currency_rate;
                    $item->sum40 += $item->c40/$currency_rate;
                    $item->sum40hc += $item->c40hc/$currency_rate;
                    $item->sum40nor += $item->c40nor/$currency_rate;
                    $item->sum45 += $item->c45/$currency_rate;
                }
            }
        }

        foreach($sale_terms_destination as $value){
            foreach($value->charge as $item){
                if($item->currency_id!=''){
                    if($quote->pdf_option->grouped_destination_charges==1){
                        $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                    $item->sum20 += $item->c20/$currency_rate;
                    $item->sum40 += $item->c40/$currency_rate;
                    $item->sum40hc += $item->c40hc/$currency_rate;
                    $item->sum40nor += $item->c40nor/$currency_rate;
                    $item->sum45 += $item->c45/$currency_rate;
                }
            }
        }

        $origin_sales = $sale_terms_origin->map(function ($origin) {
            return collect($origin->toArray())
                ->only(['port_id'])
                ->all();
        });

        $destination_sales = $sale_terms_destination->map(function ($origin) {
            return collect($origin->toArray())
                ->only(['port_id'])
                ->all();
        });

        //Ports when saleterms
        $port_origin_ids = $rates->implode('origin_port_id', ', ');
        $port_origin_ids = explode(",",$port_origin_ids);
        $port_destination_ids = $rates->implode('destination_port_id', ', ');
        $port_destination_ids = explode(",",$port_destination_ids);
        $rate_origin_ports = Harbor::whereIn('id',$port_origin_ids)->whereNotIn('id',$origin_sales)->pluck('display_name','id');
        $rate_destination_ports = Harbor::whereIn('id',$port_destination_ids)->whereNotIn('id',$destination_sales)->pluck('display_name','id');

        //Airports when saleterms
        $airport_origin_ids = $rates->implode('origin_airport_id', ', ');
        $airport_origin_ids = explode(",",$airport_origin_ids);
        $airport_destination_ids = $rates->implode('destination_airport_id', ', ');
        $airport_destination_ids = explode(",",$airport_destination_ids);
        $rate_origin_airports = Airport::whereIn('id',$airport_origin_ids)->whereNotIn('id',$origin_sales)->pluck('display_name','id');
        $rate_destination_airports = Airport::whereIn('id',$airport_destination_ids)->whereNotIn('id',$destination_sales)->pluck('display_name','id');

        $carrierMan = Carrier::pluck('name','id');
        $airlines = Airline::pluck('name','id');
        $companies = Company::where('company_user_id',$company_user_id)->pluck('business_name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $incoterms = Incoterm::pluck('name','id');
        $users = User::where('company_user_id',$company_user_id)->pluck('name','id');
        $prices = Price::where('company_user_id',$company_user_id)->pluck('name','id');
        $currencies = Currency::pluck('alphacode','id');
        if($quote->equipment!=''){
            $equipmentHides = $this->hideContainer($quote->equipment,'BD');
        }
        $calculation_types = CalculationType::pluck('name','id');
        $calculation_types_lcl_air = CalculationTypeLcl::pluck('name','id');
        $surcharges = Surcharge::where('company_user_id',\Auth::user()->company_user_id)->orwhere('company_user_id',NULL)->orderBy('name','Asc')->pluck('name','id');
        $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
        $hideO = 'hide';
        $hideD = 'hide';

        foreach ($rates as $item) {
            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;

            $total_markup20=0;
            $total_markup40=0;
            $total_markup40hc=0;
            $total_markup40nor=0;
            $total_markup45=0;

            $total_rate20=0;
            $total_rate40=0;
            $total_rate40hc=0;
            $total_rate40nor=0;
            $total_rate45=0;

            $total_rate_markup20=0;
            $total_rate_markup40=0;
            $total_rate_markup40hc=0;
            $total_rate_markup40nor=0;
            $total_rate_markup45=0;

            $total_lcl_air_freight=0;
            $total_lcl_air_origin=0;
            $total_lcl_air_destination=0;

            $currency = Currency::find($item->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;

            $typeCurrency =  $currency_cfg->alphacode;

            $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);

            //Charges
            foreach ($item->charge as $value) {

                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                $array_amounts = json_decode($value->amount,true);
                $array_markups = json_decode($value->markups,true);

                if(isset($array_amounts['c20'])){
                    $amount20=$array_amounts['c20'];
                    $total20=$amount20/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_markups['m20'])){
                    $markup20=$array_markups['m20'];
                    $total_markup20=$markup20/$currency_rate;
                }

                if(isset($array_amounts['c40'])){
                    $amount40=$array_amounts['c40'];
                    $total40=$amount40/$currency_rate;          
                    $sum40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_markups['m40'])){
                    $markup40=$array_markups['m40'];
                    $total_markup40=$markup40/$currency_rate;
                }

                if(isset($array_amounts['c40hc'])){
                    $amount40hc=$array_amounts['c40hc'];
                    $total40hc=$amount40hc/$currency_rate;          
                    $sum40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_markups['m40hc'])){
                    $markup40hc=$array_markups['m40hc'];
                    $total_markup40hc=$markup40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor'])){
                    $amount40nor=$array_amounts['c40nor'];
                    $total40nor=$amount40nor/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_markups['m40nor'])){
                    $markup40nor=$array_markups['m40nor'];
                    $total_markup40nor=$markup40nor/$currency_rate;
                }

                if(isset($array_amounts['c45'])){
                    $amount45=$array_amounts['c45'];
                    $total45=$amount45/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                }

                if(isset($array_markups['m45'])){
                    $markup45=$array_markups['m45'];
                    $total_markup45=$markup45/$currency_rate;
                }

                $value->total_20=number_format($sum20, 2, '.', '');
                $value->total_40=number_format($sum40, 2, '.', '');
                $value->total_40hc=number_format($sum40hc, 2, '.', '');
                $value->total_40nor=number_format($sum40nor, 2, '.', '');
                $value->total_45=number_format($sum45, 2, '.', '');

                $value->total_markup20=number_format($total_markup20, 2, '.', '');
                $value->total_markup40=number_format($total_markup40, 2, '.', '');
                $value->total_markup40hc=number_format($total_markup40hc, 2, '.', '');
                $value->total_markup40nor=number_format($total_markup40nor, 2, '.', '');
                $value->total_markup45=number_format($total_markup45, 2, '.', '');       

                $currency_charge = Currency::find($value->currency_id);
                $value->currency_usd = $currency_charge->rates;
                $value->currency_eur = $currency_charge->rates_eur;
            }

            //Charges
            foreach ($item->charge_lcl_air as $value) {

                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                if($value->type_id==3){
                    $value->price_per_unit=number_format(($value->price_per_unit), 2, '.', '');
                    $value->markup=number_format(($value->markup), 2, '.', '');
                    $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');          
                    //$value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                }elseif($value->type_id==1){
                    $value->price_per_unit=number_format(($value->price_per_unit), 2, '.', '');
                    $value->markup=number_format(($value->markup), 2, '.', '');
                    $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    //$value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                }else{
                    $value->price_per_unit=number_format(($value->price_per_unit), 2, '.', '');
                    $value->markup=number_format(($value->markup), 2, '.', '');
                    $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    //$value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                }
            }

            //Inland
            foreach ($item->inland as $inland) {
                $typeCurrency =  $currency_cfg->alphacode;
                $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                $array_amounts = json_decode($inland->rate,true);
                $array_markups = json_decode($inland->markup,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                    $amount20=$array_amounts['c20'];
                    $markup20=$array_markups['m20'];
                    $total20=($amount20+$markup20)/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                    $amount20=$array_amounts['c20'];
                    $total20=$amount20/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                    $markup20=$array_markups['m20'];
                    $total20=$markup20/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                    $amount40=$array_amounts['c40'];
                    $markup40=$array_markups['m40'];
                    $total40=($amount40+$markup40)/$currency_rate;
                    $sum40 = number_format($total40, 2, '.', '');
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                    $amount40=$array_amounts['c40'];
                    $total40=$amount40/$currency_rate;
                    $sum40 = number_format($total40, 2, '.', '');
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                    $markup40=$array_markups['m40'];
                    $total40=$markup40/$currency_rate;
                    $sum40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                    $amount40hc=$array_amounts['c40hc'];
                    $markup40hc=$array_markups['m40hc'];
                    $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                    $sum40hc = number_format($total40hc, 2, '.', '');
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                    $amount40hc=$array_amounts['c40hc'];
                    $total40hc=$amount40hc/$currency_rate;
                    $sum40hc = number_format($total40hc, 2, '.', '');
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                    $markup40hc=$array_markups['m40hc'];
                    $total40hc=$markup40hc/$currency_rate;
                    $sum40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                    $amount40nor=$array_amounts['c40nor'];
                    $markup40nor=$array_markups['m40nor'];
                    $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                    $amount40nor=$array_amounts['c40nor'];
                    $total40nor=$amount40nor/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                    $markup40nor=$array_markups['m40nor'];
                    $total40nor=$markup40nor/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                    $amount45=$array_amounts['c45'];
                    $markup45=$array_markups['m45'];
                    $total45=($amount45+$markup45)/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                    $amount45=$array_amounts['c45'];
                    $total45=$amount45/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                    $markup45=$array_markups['m45'];
                    $total45=$markup45/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                }

                $inland->total_20=number_format($sum20, 2, '.', '');
                $inland->total_40=number_format($sum40, 2, '.', '');
                $inland->total_40hc=number_format($sum40hc, 2, '.', '');
                $inland->total_40nor=number_format($sum40nor, 2, '.', '');
                $inland->total_45=number_format($sum45, 2, '.', '');

                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }

            if(!$rates->isEmpty()){
                foreach ($rates as $item) {
                    $rates->map(function ($item) {
                        if($item->origin_port_id!='' ){
                            $item['origin_country_code'] = strtolower(substr(@$item->origin_port->code, 0, 2));
                        }else{
                            $item['origin_country_code'] = strtolower(@$item->origin_airport->code);
                        }
                        if($item->destination_port_id!='' ){
                            $item['destination_country_code'] = strtolower(substr(@$item->destination_port->code, 0, 2));
                        }else{
                            $item['destination_country_code'] = strtolower(@$item->destination_airport->code); 
                        }

                        return $item;
                    }); 
                }
            }

            if(!$sale_terms->isEmpty()){
                foreach ($sale_terms as $v) {
                    $sale_terms->map(function ($v) {
                        if($v->port_id!='' ){
                            $v['country_code'] = strtolower(substr(@$v->port->code, 0, 2));
                        }else{
                            $v['country_code'] = strtolower(@$v->airport->code);
                        }
                        return $v;
                    }); 
                }
            }

            $emaildimanicdata = json_encode([
                'quote_bool'   => 'true',
                'company_id'   => '',
                'contact_id'   => '',
                'quote_id'     => $id
            ]);

            if($request->ajax()){
                $quote->load('user','company','contact','incoterm');
                $collection = Collection::make($quote);
                return $collection;
            }

            return view('quotesv2/show', compact('quote','companies','incoterms','users','prices','contacts','currencies','currency_cfg','equipmentHides','freight_charges','origin_charges','destination_charges','calculation_types','calculation_types_lcl_air','rates','surcharges','email_templates','inlands','emaildimanicdata','package_loads','countries','harbors','prices','airlines','carrierMan','currency_name','hideO','hideD','sale_terms','rate_origin_ports','rate_destination_ports','rate_origin_airports','rate_destination_airports','destinationAddressHides','originAddressHides'));
        }
    }
    /**
   * Update charges by rate
   * @param Request $request 
   * @return array json
   */
    public function updateRateCharges(Request $request)
    {
        $charge=AutomaticRate::find($request->pk);
        $name = explode("->", $request->name);
        if (strpos($request->name, '->') == true) {
            if ($name[0] == 'rates') {
                $array = json_decode($charge->rates, true);
            }else{
                $array = json_decode($charge->markups, true);
            }
            $field = (string) $name[0];
            $array[$name[1]]=$request->value;
            $array = json_encode($array);
            $charge->$field=$array;
        }else{
            $name = $request->name;
            $charge->$name=$request->value;
        }

        $charge->update();
        $this->updatePdfApi($charge->quote_id);
        $this->updateIntegrationQuoteStatus($charge->quote_id);
        return response()->json(['success'=>'Ok']);
    }

    /**
   * Update charges
   * @param Request $request 
   * @return array json
   */
    public function updateQuoteCharges(Request $request)
    {
        $charge=Charge::find($request->pk);
        $name = explode("->", $request->name);
        $value = str_replace(",",".",$request->value);
        
        if (strpos($request->name, '->') == true) {
            if ($name[0] == 'amount') {
                $array = json_decode($charge->amount, true);
            }else{
                $array = json_decode($charge->markups, true);
            }
            $field = (string) $name[0];
            $array[$name[1]]=$value;
            $array = json_encode($array);
            $charge->$field=$array;
        }else{
            $name = $request->name;
            $charge->$name=$value;
        }
        $charge->update();
        $quote_id= $charge->automatic_rate->quote_id;
        $this->updatePdfApi($quote_id);
        $this->updateIntegrationQuoteStatus($quote_id);
        return response()->json(['success'=>'Ok']);
    }

    /**
   * Update LCL Quotes info
   * @param Request $request 
   * @return array json
   */
    public function updateQuoteInfo(Request $request)
    {
        if($request->value){
            $quote=QuoteV2::find($request->pk);
            $name = $request->name;
            $quote->$name=$request->value;
            $quote->update();  
            $this->updatePdfApi($quote->id); 
        }
        return response()->json(['success'=>'Ok']);
    }

    /**
   * Update inland's charges
   * @param Request $request 
   * @return array json
   */
    public function updateInlandCharges(Request $request)
    {
        $charge=AutomaticInland::find($request->pk);
        $name = explode("->", $request->name);
        if (strpos($request->name, '->') == true) {
            if ($name[0] == 'rate') {
                $array = json_decode($charge->rate, true);
            }else{
                $array = json_decode($charge->markup, true);
            }
            $field = (string) $name[0];
            $array[$name[1]]=$request->value;
            $array = json_encode($array);
            $charge->$field=$array;
        }else{
            $name = $request->name;
            $charge->$name=$request->value;
        }
        $charge->update();
        return response()->json(['success'=>'Ok']);
    }

    /**
   * Update inland's charges LCL/AIR
   * @param Request $request 
   * @return array json
   */
    public function updateInlandChargeLcl(Request $request)
    {
        $charge=AutomaticInlandLclAir::find($request->pk);
        $name = $request->name;
        $charge->$name=$request->value;

        $charge->update();
        return response()->json(['success'=>'Ok']);
    }

    //Actualiza Cargos por rate en LCL y Aereo
    public function updateQuoteChargesLcl(Request $request)
    {
        $charge=ChargeLclAir::find($request->pk);
        $name = $request->name;
        $charge->$name=$request->value;
        $charge->update();
        return response()->json(['success'=>'Ok']);
    }

    /**
   * Update Quote's data
   * @param Request $request 
   * @param integer $id 
   * @return array json
   */
    public function update(Request $request,$id)
    {

        $validation = explode('/',$request->validity);
        $validity_start = $validation[0];
        $validity_end = $validation[1];
        $contact_name='';
        $price_name='';
        $gdp='No';

        $quote=QuoteV2::find($id);
        if($quote->quote_id!=$request->quote_id){
            $quote->custom_quote_id=$request->quote_id;
        }else{
            $quote->custom_quote_id='';
        }
        $quote->type=$request->type;
        $quote->company_id=$request->company_id;
        $quote->contact_id=$request->contact_id;
        $quote->delivery_type=$request->delivery_type;
        $quote->date_issued=$request->date_issued;
        $quote->incoterm_id=$request->incoterm_id;
        if($request->equipment!=''){
            $quote->equipment=json_encode($request->equipment);
        }
        $quote->validity_start=$validity_start;
        $quote->validity_end=$validity_end;
        $quote->price_id=$request->price_id;
        $quote->user_id=$request->user_id;
        $quote->kind_of_cargo=$request->kind_of_cargo;
        $quote->commodity=$request->commodity;
        $quote->status=$request->status;
        $quote->gdp=$request->gdp;
        $quote->risk_level=$request->risk_level;
        $quote->origin_address=$request->origin_address;
        $quote->destination_address=$request->destination_address;
        $quote->update();
        $this->updatePdfApi($quote->id);

        if($request->contact_id!=''){
            $contact_name=$quote->contact->first_name.' '.$quote->contact->last_name;
        }

        if($quote->gdp==1){
            $gdp='Yes';
        }

        if($request->price_id!=''){
            $price_name=$quote->price->name;
        }

        $owner=$quote->user->name.' '.$quote->user->lastname;
        if($quote->company_id!=''){
            $company_name=$quote->company->business_name;   
        }else{
            $company_name = '';
        }

        return response()->json(['message'=>'Ok','quote'=>$quote,'contact_name'=>$contact_name,'owner'=>$owner,'price_name'=>$price_name,'gdp'=>$gdp,'company_name'=>$company_name]);
    }

    //Actualiza condiciones de pago
    public function updatePaymentConditions(Request $request,$id)
    {
        $quote=QuoteV2::find($id);

        $quote->payment_conditions=$request->payments;
        $quote->update();
        $this->updatePdfApi($quote->id);
        return response()->json(['message'=>'Ok','quote'=>$quote]);
    }

    /**
   * Actualizar términos y condiciones de una cotización
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function updateTerms(Request $request,$id)
    {
        $quote=QuoteV2::find($id);
        $name = $request->name;
        $quote->$name=$request->terms;
        $quote->update();
        $this->updatePdfApi($quote->id);
        $this->updateIntegrationQuoteStatus($quote->id);
        return response()->json(['message'=>'Ok','quote'=>$quote]);
    }

    /**
   * Actualizar remarsk de un rate
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function updateRemarks(Request $request,$id)
    {
        $rate=AutomaticRate::find($id);

        if($request->language == 'all'){
            $rate->remarks=$request->remarks;
        }
        if($request->language == 'english'){
            $rate->remarks_english=$request->remarks;
        }
        if($request->language == 'spanish'){
            $rate->remarks_spanish=$request->remarks;
        }
        if($request->language == 'portuguese'){
            $rate->remarks_portuguese=$request->remarks;
        }

        $rate->update();

        $this->updatePdfApi($rate->quote_id);
        $this->updateIntegrationQuoteStatus($rate->quote_id);

        return response()->json(['message'=>'Ok','rate'=>$rate]);
    }

    /**
   * Duplicar una cotización existente
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function duplicate(Request $request, $id){

        $id = obtenerRouteKey($id);
        $quote=QuoteV2::find($id);
        $quote_duplicate = new QuoteV2();
        $quote_duplicate->user_id=\Auth::id();
        $quote_duplicate->company_user_id=\Auth::user()->company_user_id;
        $quote_duplicate->quote_id=$this->idPersonalizado();
        $quote_duplicate->incoterm_id=$quote->incoterm_id;
        $quote_duplicate->type=$quote->type;
        $quote_duplicate->cargo_type=$quote->cargo_type;
        $quote_duplicate->total_quantity=$quote->total_quantity;
        $quote_duplicate->total_weight=$quote->total_weight;
        $quote_duplicate->total_volume=$quote->total_volume;
        $quote_duplicate->chargeable_weight=$quote->chargeable_weight;
        $quote_duplicate->delivery_type=$quote->delivery_type;
        $quote_duplicate->currency_id=$quote->currency_id;
        $quote_duplicate->contact_id=$quote->contact_id;
        $quote_duplicate->company_id=$quote->company_id;
        $quote_duplicate->validity_start=$quote->validity_start;
        $quote_duplicate->validity_end=$quote->validity_end;
        $quote_duplicate->equipment=$quote->equipment;
        $quote_duplicate->status=$quote->status;
        $quote_duplicate->date_issued=$quote->date_issued;
        $quote_duplicate->terms_and_conditions=$quote->terms_and_conditions;
        $quote_duplicate->terms_english=$quote->terms_english;
        $quote_duplicate->terms_portuguese=$quote->terms_portuguese;
        $quote_duplicate->payment_conditions=$quote->payment_conditions;
        if($quote->origin_address){
            $quote_duplicate->origin_address=$quote->origin_address;
        }
        if($quote->destination_address){
            $quote_duplicate->destination_address=$quote->destination_address;
        }
        if($quote->origin_port_id){
            $quote_duplicate->origin_port_id=$quote->origin_port_id;
        }
        if($quote->destination_port_id){
            $quote_duplicate->destination_port_id=$quote->destination_port_id;
        }
        if($quote->price_id){
            $quote_duplicate->price_id=$quote->price_id;
        }
        if($quote->custom_quote_id){
            $quote_duplicate->custom_quote_id=$quote->custom_quote_id;
        }
        if($quote->kind_of_cargo){
            $quote_duplicate->kind_of_cargo=$quote->kind_of_cargo;
        }
        if($quote->commodity){
            $quote_duplicate->commodity=$quote->commodity;
        }    
        $quote_duplicate->save();

        $this->savePdfOptionsDuplicate($quote, $quote_duplicate);

        $this->saveScheduleQuoteDuplicate($quote, $quote_duplicate);

        $this->saveAutomaticRateDuplicate($quote, $quote_duplicate);

        if($request->ajax()){
            return response()->json(['message' => 'Ok']);
        }else{
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Quote duplicated successfully!');
            return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote_duplicate->id));
        }
    }

    public function savePdfOptionsDuplicate($quote, $quote_duplicate){
        $pdf = PdfOption::where('quote_id',$quote->id)->first();
        $pdf_duplicate = new PdfOption();
        $pdf_duplicate->quote_id=$quote_duplicate->id;
        $pdf_duplicate->show_type=$pdf->show_type;
        $pdf_duplicate->grouped_total_currency=$pdf->grouped_total_currency;
        $pdf_duplicate->total_in_currency=$pdf->total_in_currency;
        $pdf_duplicate->grouped_freight_charges=$pdf->grouped_freight_charges;
        $pdf_duplicate->freight_charges_currency=$pdf->freight_charges_currency;
        $pdf_duplicate->grouped_origin_charges=$pdf->grouped_origin_charges;
        $pdf_duplicate->origin_charges_currency=$pdf->origin_charges_currency;
        $pdf_duplicate->grouped_destination_charges=$pdf->grouped_destination_charges;
        $pdf_duplicate->destination_charges_currency=$pdf->destination_charges_currency;
        $pdf_duplicate->show_total_freight_in_currency=$pdf->show_total_freight_in_currency;
        $pdf_duplicate->language=$pdf->language;
        $pdf_duplicate->show_carrier=$pdf->show_carrier;
        $pdf_duplicate->show_logo=$pdf->show_logo;
        $pdf_duplicate->show_gdp_logo=$pdf->show_gdp_logo;
        $pdf_duplicate->save();
    }

    public function saveScheduleQuoteDuplicate($quote, $quote_duplicate){
        $schedule_quote = Schedule::where('quote_id',$quote->id)->first();

        if($schedule_quote){
            $schedule = new Schedule();
            $schedule->vessel = $schedule_quote->vessel;
            $schedule->etd = $schedule_quote->etd;
            $schedule->transit_time = $schedule_quote->transit_time;
            $schedule->type = $schedule_quote->type;
            $schedule->eta = $schedule_quote->eta;
            $schedule->quote_id = $quote_duplicate->id;
            $schedule->save();
        }
    }

    public function saveAutomaticRateDuplicate($quote, $quote_duplicate){
        $rates = AutomaticRate::where('quote_id',$quote->id)->get();

        foreach ($rates as $rate){

            $rate_duplicate = new AutomaticRate();
            $rate_duplicate->quote_id=$quote_duplicate->id;
            $rate_duplicate->contract=$rate->contract;
            $rate_duplicate->validity_start=$rate->validity_start;
            $rate_duplicate->validity_end=$rate->validity_end;
            $rate_duplicate->origin_port_id=$rate->origin_port_id;
            $rate_duplicate->destination_port_id=$rate->destination_port_id;
            $rate_duplicate->origin_airport_id=$rate->origin_airport_id;
            $rate_duplicate->destination_airport_id=$rate->destination_airport_id;
            $rate_duplicate->carrier_id=$rate->carrier_id;
            $rate_duplicate->rates=$rate->rates;
            $rate_duplicate->markups=$rate->markups;
            $rate_duplicate->total=$rate->total;
            $rate_duplicate->currency_id=$rate->currency_id;
            $rate_duplicate->schedule_type=$rate->schedule_type;
            $rate_duplicate->transit_time=$rate->transit_time;
            $rate_duplicate->via=$rate->via;
            $rate_duplicate->remarks=$rate->remarks;
            $rate_duplicate->remarks_spanish=$rate->remarks_spanish;
            $rate_duplicate->remarks_english=$rate->remarks_english;
            $rate_duplicate->remarks_portuguese=$rate->remarks_portuguese;
            $rate_duplicate->save();

            $charges=Charge::where('automatic_rate_id',$rate->id)->get();
            if($charges->count()>0){
                foreach ($charges as $charge){
                    $charge_duplicate = new Charge();
                    $charge_duplicate->automatic_rate_id=$rate_duplicate->id;
                    $charge_duplicate->type_id=$charge->type_id;
                    $charge_duplicate->surcharge_id=$charge->surcharge_id;
                    $charge_duplicate->calculation_type_id=$charge->calculation_type_id;
                    $charge_duplicate->amount=$charge->amount;
                    $charge_duplicate->markups=$charge->markups;
                    $charge_duplicate->total=$charge->total;
                    $charge_duplicate->currency_id=$charge->currency_id;
                    $charge_duplicate->save();
                }
            }

            $chargesLcl=ChargeLclAir::where('automatic_rate_id',$rate->id)->get();
            if($chargesLcl->count()>0){
                foreach ($chargesLcl as $charge){
                    $charge_duplicate = new ChargeLclAir();
                    $charge_duplicate->automatic_rate_id=$rate_duplicate->id;
                    $charge_duplicate->type_id=$charge->type_id;
                    $charge_duplicate->surcharge_id=$charge->surcharge_id;
                    $charge_duplicate->calculation_type_id=$charge->calculation_type_id;
                    $charge_duplicate->units=$charge->units;
                    $charge_duplicate->price_per_unit=$charge->price_per_unit;
                    $charge_duplicate->markup=$charge->markup;
                    $charge_duplicate->total=$charge->total;
                    $charge_duplicate->currency_id=$charge->currency_id;
                    $charge_duplicate->save();
                }
            }
        }
    }

    /**
   * Crea Custom ID a partir de datos del usuario
   * @return type
   */
    public function idPersonalizado(){
        $user_company = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
        $iniciales =  strtoupper(substr($user_company->name,0, 2));
        $quote = QuoteV2::where('company_user_id',$user_company->id)->orderBy('created_at', 'desc')->first();

        if($quote == null){
            $iniciales = $iniciales."-1";
        }else{

            $numeroFinal = explode('-',$quote->quote_id);

            //dd($quote->quote_id);
            $numeroFinal = $numeroFinal[1] +1;

            $iniciales = $iniciales."-".$numeroFinal;
        }
        return $iniciales;
    }

    /**
   * Mostrar/Ocultar contenedores en la vista
   * @param array $equipmentForm 
   * @param integer $tipo 
   * @return type
   */
    public function hideContainer($equipmentForm,$tipo){
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

        if($tipo == 'BD'){
            $equipmentForm = json_decode($equipmentForm);
        }

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
            if($val == '40HC'){
                $hidden40hc = '';
            }
            if($val == '40NOR'){
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

    /**
   * Generate PDF FCL
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function pdf(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $equipmentHides = '';
        $quote = QuoteV2::findOrFail($id);
        $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();
        $origin_charges = AutomaticRate::where('quote_id',$quote->id)
            ->where(function ($query) {
                $query->whereHas('charge', function ($query) {
                    $query->where('type_id', 1);
                })->orWhereHas('inland', function($query) {
                    $query->where('type', 'Origin');
                });
            })->get();
        $destination_charges = AutomaticRate::where('quote_id',$quote->id)
            ->where(function ($query) {
                $query->whereHas('charge', function ($query) {
                    $query->where('type_id', 2);
                })->orWhereHas('inland', function($query) {
                    $query->where('type', 'Destination');
                });
            })->get();
        $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
            $query->where('type_id', 3);
        })->where('quote_id',$quote->id)->get();
        $contact_email = Contact::find($quote->contact_id);
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        if($quote->equipment!=''){
            $equipmentHides = $this->hideContainer($quote->equipment,'BD');
        }

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        /** Rates **/

        $rates = $this->processGlobalRates($rates, $quote, $currency_cfg);

        /** Origin Charges **/

        $origin_charges_grouped=$this->processOriginGrouped($origin_charges, $quote, $currency_cfg);

        $origin_charges_detailed=$this->processOriginDetailed($origin_charges, $quote, $currency_cfg);

        /** Destination Charges **/

        $destination_charges_grouped=$this->processDestinationGrouped($destination_charges, $quote, $currency_cfg);

        $destination_charges=$this->processDestinationDetailed($destination_charges, $quote, $currency_cfg);

        /** Freight Charges **/

        $freight_charges_grouped = $this->processFreightCharges($freight_charges, $quote, $currency_cfg);

        $view = \View::make('quotesv2.pdf.index', ['quote'=>$quote,'rates'=>$rates,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg, 'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'origin_charges_detailed'=>$origin_charges_detailed,'destination_charges_grouped'=>$destination_charges_grouped]);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        return $pdf->stream('quote-'.$quote->quote_id.'-'.date('Ymd').'.pdf');
    }

    /**
   * Generate PDF to LCL/AIR
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function pdfLclAir(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $equipmentHides = '';
        $quote = QuoteV2::findOrFail($id);
        $rates_lcl_air = AutomaticRate::where('quote_id',$quote->id)->with('charge_lcl_air')->get();
        $freight_charges = AutomaticRate::whereHas('charge_lcl_air', function ($query) {
            $query->where('type_id', 3);
        })->where('quote_id',$quote->id)->get();
        $origin_charges = AutomaticRate::where('quote_id',$quote->id)
            ->where(function ($query) {
                $query->whereHas('charge_lcl_air', function ($query) {
                    $query->where('type_id', 1);
                })->orWhereHas('automaticInlandLclAir', function($query) {
                    $query->where('type', 'Origin');
                });
            })->get();
        $destination_charges = AutomaticRate::where('quote_id',$quote->id)
            ->where(function ($query) {
                $query->whereHas('charge_lcl_air', function ($query) {
                    $query->where('type_id', 2);
                })->orWhereHas('automaticInlandLclAir', function($query) {
                    $query->where('type', 'Destination');
                });
            })->get();
        $contact_email = Contact::find($quote->contact_id);
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
        if($quote->equipment!=''){
            $equipmentHides = $this->hideContainer($quote->equipment,'BD');
        }

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $type=$company_user->type_pdf;
            $ammounts_type=$company_user->pdf_ammounts;
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        foreach ($rates_lcl_air as $item) {

            if($quote->pdf_option->grouped_total_currency==1){
                $typeCurrency = $quote->pdf_option->total_in_currency;
            }else{
                $typeCurrency =  $currency_cfg->alphacode;
            }

            $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);

            foreach ($item->charge_lcl_air as $value) {

                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                if($value->type_id==3){
                    if($value->units>0){
                        $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    }
                }elseif($value->type_id==1){
                    if($value->units>0){
                        $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    }
                }else{
                    if($value->units>0){
                        $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    }
                }
            }
            if(!$item->automaticInlandLclAir->isEmpty()){
                foreach($item->automaticInlandLclAir as $inland){
                    if($quote->pdf_option->grouped_origin_charges==1){
                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                    if($inland->units>0){
                        $inland->total_inland=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');
                    }
                }
            }
            foreach ($item->inland as $inland) {
                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }
        }

        $origin_charges_grouped = collect($origin_charges);

        $origin_charges_grouped = $origin_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },      
            function ($item) {
                return $item['destination_port']['name'];
            },
        ], $preserveKeys = true);

        foreach($origin_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $value){

                            if($value->type_id==1){
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }

                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                if($value->units>0){
                                    $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                }else{
                                    $value->rate=0;
                                }
                                $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                            }
                        }

                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $inland){
                                if($inland->type=='Origin'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                    if($inland->units>0){
                                        $inland->rate_amount=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$inland->units, 2, '.', '');
                                    }else{
                                        $inland->rate_amount=0;
                                    }
                                    $inland->total_inland_origin=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');

                                }
                            }
                        }
                    }
                }
            }
        }

        /*** DESTINATION CHARGES ***/

        $destination_charges_grouped = collect($destination_charges);

        $destination_charges_grouped = $destination_charges_grouped->groupBy([

            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },
            function ($item) {
                return $item['origin_port']['name'];
            },

        ], $preserveKeys = true);
        foreach($destination_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $value){

                            if($value->type_id==2){

                                if($quote->pdf_option->grouped_destination_charges==1){
                                    $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                if($value->units>0){
                                    $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                }else{
                                    $value->rate=0;
                                }
                                $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                            }
                        }
                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $value){
                                if($value->type=='Destination'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                    if($value->units>0){
                                        $value->rate_amount=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                    }else{
                                        $value->rate_amount=0;
                                    }
                                    $value->total_inland_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                                }
                            }
                        }
                    }
                }
            }
        }

        /** FREIGHT CHARGES **/

        $freight_charges_detailed = collect($freight_charges);

        $freight_charges_detailed = $freight_charges_detailed->groupBy([   
            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },      
        ], $preserveKeys = true);

        foreach($freight_charges_detailed as $origin=>$item){
            foreach($item as $destination=>$items){
                foreach($items as $carrier=>$itemsDetail){
                    foreach ($itemsDetail as $value) {     
                        foreach ($value->charge as $amounts) {
                            if($amounts->type_id==3){
                                $sum_freight_20=0;
                                $sum_freight_40=0;
                                $sum_freight_40hc=0;
                                $sum_freight_40nor=0;
                                $sum_freight_45=0;
                                $total_freight_40=0;
                                $total_freight_20=0;
                                $total_freight_40hc=0;
                                $total_freight_40nor=0;
                                $total_freight_45=0;
                                //dd($quote->pdf_option->destination_charges_currency);
                                if($quote->pdf_option->grouped_freight_charges==1){
                                    $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                $array_amounts = json_decode($amounts->amount,true);
                                $array_markups = json_decode($amounts->markups,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum_freight_20=$array_amounts['c20']+$array_markups['m20'];
                                    $total_freight_20=$sum_freight_20/$currency_rate;
                                }
                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum_freight_40=$array_amounts['c40']+$array_markups['m40'];
                                    $total_freight_40=$sum_freight_40/$currency_rate;
                                }
                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum_freight_40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                    $total_freight_40hc=$sum_freight_40hc/$currency_rate;
                                }
                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum_freight_40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                    $total_freight_40nor=$sum_freight_40nor/$currency_rate;
                                }
                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum_freight_45=$array_amounts['c45']+$array_markups['m45'];
                                    $total_freight_45=$sum_freight_45/$currency_rate;
                                }            

                                $amounts->total_20 = number_format($total_freight_20, 2, '.', '');
                                $amounts->total_40 = number_format($total_freight_40, 2, '.', '');
                                $amounts->total_40hc = number_format($total_freight_40hc, 2, '.', '');
                                $amounts->total_40nor = number_format($total_freight_40nor, 2, '.', '');
                                $amounts->total_45 = number_format($total_freight_45, 2, '.', '');
                            }
                        }
                    }
                } 
            }
        }

        $freight_charges_grouped = collect($freight_charges);

        $freight_charges_grouped = $freight_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },

        ], $preserveKeys = true);

        foreach($freight_charges_grouped as $freight){
            foreach($freight as $detail){
                foreach($detail as $item){
                    foreach($item as $rate){
                        foreach ($rate->charge_lcl_air as $value) {
                            if($value->type_id==3){
                                if($freight_charges_grouped->count()>1){
                                    $typeCurrency = $currency_cfg->alphacode;
                                }else{
                                    if($quote->pdf_option->grouped_freight_charges==1){
                                        $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                    }else{
                                        $typeCurrency = $currency_cfg->alphacode;
                                    }
                                }
                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                                //$value->price_per_unit=number_format(($value->price_per_unit/$currency_rate), 2, '.', '');
                                //$value->markup=number_format(($value->markup/$currency_rate), 2, '.', '');
                                if($value->units>0){
                                    $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                }else{
                                    $value->rate=0;
                                }
                                $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                            }
                        }
                    }
                }
            }
        }

        $view = \View::make('quotesv2.pdf.index_lcl_air', ['quote'=>$quote,'rates'=>$rates_lcl_air,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'destination_charges_grouped'=>$destination_charges_grouped,'freight_charges_detailed'=>$freight_charges_detailed,'package_loads'=>$package_loads]);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        return $pdf->stream('quote-'.$quote->quote_id.'-'.date('Ymd').'.pdf');
    }

    /**
   * Generate PDF to LCL/AIR
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function pdfAir(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $equipmentHides = '';
        $quote = QuoteV2::findOrFail($id);
        $rates_lcl_air = AutomaticRate::where('quote_id',$quote->id)->with('charge_lcl_air')->get();
        $origin_charges = AutomaticRate::whereHas('charge_lcl_air', function ($query) {
            $query->where('type_id', 1);
        })->where('quote_id',$quote->id)->get();
        $freight_charges = AutomaticRate::whereHas('charge_lcl_air', function ($query) {
            $query->where('type_id', 3);
        })->where('quote_id',$quote->id)->get();
        $destination_charges = AutomaticRate::whereHas('charge_lcl_air', function ($query) {
            $query->where('type_id', 2);
        })->where('quote_id',$quote->id)->get();
        $contact_email = Contact::find($quote->contact_id);
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
        if($quote->equipment!=''){
            $equipmentHides = $this->hideContainer($quote->equipment,'BD');
        }

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $type=$company_user->type_pdf;
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        foreach ($rates_lcl_air as $item) {

            if($quote->pdf_option->grouped_total_currency==1){
                $typeCurrency = $quote->pdf_option->total_in_currency;
            }else{
                $typeCurrency =  $currency_cfg->alphacode;
            }

            $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);

            foreach ($item->charge_lcl_air as $value) {

                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                if($value->type_id==3){
                    $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');          
                }elseif($value->type_id==1){
                    $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                }else{
                    $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                }
            }
            if(!$item->automaticInlandLclAir->isEmpty()){
                foreach($item->automaticInlandLclAir as $inland){
                    if($quote->pdf_option->grouped_origin_charges==1){
                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                    if($inland->units>0){
                        $inland->total_inland=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');
                    }
                }
            }            
            foreach ($item->inland as $inland) {
                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }
        }

        $origin_charges_grouped = collect($origin_charges);

        $origin_charges_grouped = $origin_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_airport']['name'].', '.$item['origin_airport']['code'];
            },
            function ($item) {
                return $item['airline']['name'];
            },      
            function ($item) {
                return $item['destination_airport']['name'];
            },
        ], $preserveKeys = true);
        foreach($origin_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $value){

                            if($value->type_id==1){
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }

                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                if($value->units>0){
                                    $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                }else{
                                    $value->rate=0;
                                }
                                $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                            }
                        }
                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $inland){
                                if($inland->type=='Origin'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                    if($inland->units>0){
                                        $inland->rate_amount=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$inland->units, 2, '.', '');
                                    }else{
                                        $inland->rate_amount=0;
                                    }
                                    $inland->total_inland_origin=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');

                                }
                            }
                        }
                    }
                }
            }
        }

        /*** DESTINATION CHARGES ***/

        $destination_charges_grouped = collect($destination_charges);

        $destination_charges_grouped = $destination_charges_grouped->groupBy([

            function ($item) {
                return $item['destination_airport']['name'].', '.$item['destination_airport']['code'];
            },
            function ($item) {
                return $item['airline']['name'];
            },
            function ($item) {
                return $item['origin_port']['name'];
            },

        ], $preserveKeys = true);
        foreach($destination_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $value){

                            if($value->type_id==2){

                                if($quote->pdf_option->grouped_destination_charges==1){
                                    $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                if($value->units>0){
                                    $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                }else{
                                    $value->rates=0;
                                }
                                $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                            }
                        }
                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $inland){
                                if($inland->type=='Destination'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                    if($inland->units>0){
                                        $inland->rate_amount=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$inland->units, 2, '.', '');
                                    }else{
                                        $inland->rate_amount=0;
                                    }
                                    $inland->total_inland_origin=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');

                                }
                            }
                        }
                    }
                }
            }
        }    


        /** FREIGHT CHARGES **/

        $freight_charges_detailed = collect($freight_charges);

        $freight_charges_detailed = $freight_charges_detailed->groupBy([   
            function ($item) {
                return $item['origin_airport']['name'].', '.$item['origin_airport']['code'];
            },
            function ($item) {
                return $item['destination_airport']['name'].', '.$item['destination_airport']['code'];
            },
            function ($item) {
                return $item['airline']['name'];
            },      
        ], $preserveKeys = true);

        foreach($freight_charges_detailed as $origin=>$item){
            foreach($item as $destination=>$items){
                foreach($items as $carrier=>$itemsDetail){
                    foreach ($itemsDetail as $value) {     
                        foreach ($value->charge as $amounts) {
                            if($amounts->type_id==3){
                                $sum_freight_20=0;
                                $sum_freight_40=0;
                                $sum_freight_40hc=0;
                                $sum_freight_40nor=0;
                                $sum_freight_45=0;
                                $total_freight_40=0;
                                $total_freight_20=0;
                                $total_freight_40hc=0;
                                $total_freight_40nor=0;
                                $total_freight_45=0;
                                //dd($quote->pdf_option->destination_charges_currency);
                                if($quote->pdf_option->grouped_freight_charges==1){
                                    $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                $array_amounts = json_decode($amounts->amount,true);
                                $array_markups = json_decode($amounts->markups,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum_freight_20=$array_amounts['c20']+$array_markups['m20'];
                                    $total_freight_20=$sum_freight_20/$currency_rate;
                                }
                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum_freight_40=$array_amounts['c40']+$array_markups['m40'];
                                    $total_freight_40=$sum_freight_40/$currency_rate;
                                }
                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum_freight_40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                    $total_freight_40hc=$sum_freight_40hc/$currency_rate;
                                }
                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum_freight_40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                    $total_freight_40nor=$sum_freight_40nor/$currency_rate;
                                }
                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum_freight_45=$array_amounts['c45']+$array_markups['m45'];
                                    $total_freight_45=$sum_freight_45/$currency_rate;
                                }            

                                $amounts->total_20 = number_format($total_freight_20, 2, '.', '');
                                $amounts->total_40 = number_format($total_freight_40, 2, '.', '');
                                $amounts->total_40hc = number_format($total_freight_40hc, 2, '.', '');
                                $amounts->total_40nor = number_format($total_freight_40nor, 2, '.', '');
                                $amounts->total_45 = number_format($total_freight_45, 2, '.', '');
                            }
                        }
                    }
                } 
            }
        }

        $freight_charges_grouped = collect($freight_charges);

        $freight_charges_grouped = $freight_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_airport']['name'].', '.$item['origin_airport']['code'];
            },
            function ($item) {
                return $item['destination_airport']['name'].', '.$item['destination_airport']['code'];
            },
            function ($item) {
                return $item['airline']['name'];
            },

        ], $preserveKeys = true);

        foreach($freight_charges_grouped as $freight){
            foreach($freight as $detail){
                foreach($detail as $item){
                    foreach($item as $rate){
                        foreach ($rate->charge_lcl_air as $value) {
                            if($value->type_id==3){
                                if($freight_charges_grouped->count()>1){
                                    $typeCurrency = $currency_cfg->alphacode;
                                }else{
                                    if($quote->pdf_option->grouped_freight_charges==1){
                                        $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                    }else{
                                        $typeCurrency = $currency_cfg->alphacode;
                                    }
                                }
                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                if($value->units>0){
                                    $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                }else{
                                    $value->rate=0;
                                }
                                $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                            }
                        }
                    }
                }
            }
        }

        $view = \View::make('quotesv2.pdf.index_lcl_air', ['quote'=>$quote,'rates'=>$rates_lcl_air,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'destination_charges_grouped'=>$destination_charges_grouped,'freight_charges_detailed'=>$freight_charges_detailed,'package_loads'=>$package_loads]);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        return $pdf->stream('quote-'.$quote->quote_id.'-'.date('Ymd').'.pdf');
    }


    public function html(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $equipmentHides = '';
        $quote = QuoteV2::findOrFail($id);
        $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();
        $origin_charges = AutomaticRate::whereHas('charge', function ($query) {
            $query->where('type_id', 1);
        })->where('quote_id',$quote->id)->get();
        $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
            $query->where('type_id', 3);
        })->where('quote_id',$quote->id)->get();
        $destination_charges = AutomaticRate::whereHas('charge', function ($query) {
            $query->where('type_id', 2);
        })->where('quote_id',$quote->id)->get();
        $contact_email = Contact::find($quote->contact_id);
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
        if($quote->equipment!=''){
            $equipmentHides = $this->hideContainer($quote->equipment,'BD');
        }

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $type=$company_user->type_pdf;
            $ammounts_type=$company_user->pdf_ammounts;
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        foreach ($rates as $item) {
            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;

            $total_markup20=0;
            $total_markup40=0;
            $total_markup40hc=0;
            $total_markup40nor=0;
            $total_markup45=0;

            $total_lcl_air_freight=0;
            $total_lcl_air_origin=0;
            $total_lcl_air_destination=0;

            $currency = Currency::find($item->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;

            //Charges
            foreach ($item->charge as $value) {

                if($quote->pdf_option->grouped_total_currency==1){
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                }else{
                    $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                $array_amounts = json_decode($value->amount,true);
                $array_markups = json_decode($value->markups,true);

                if(isset($array_amounts['c20'])){
                    $amount20=$array_amounts['c20'];
                    $total20=$amount20/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_markups['m20'])){
                    $markup20=$array_markups['m20'];
                    $total_markup20=$markup20/$currency_rate;
                }

                if(isset($array_amounts['c40'])){
                    $amount40=$array_amounts['c40'];
                    $total40=$amount40/$currency_rate;          
                    $sum40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_markups['m40'])){
                    $markup40=$array_markups['m40'];
                    $total_markup40=$markup40/$currency_rate;
                }

                if(isset($array_amounts['c40hc'])){
                    $amount40hc=$array_amounts['c40hc'];
                    $total40hc=$amount40hc/$currency_rate;          
                    $sum40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_markups['m40hc'])){
                    $markup40hc=$array_markups['m40hc'];
                    $total_markup40hc=$markup40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor'])){
                    $amount40nor=$array_amounts['c40nor'];
                    $total40nor=$amount40nor/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_markups['m40nor'])){
                    $markup40nor=$array_markups['m40nor'];
                    $total_markup40nor=$markup40nor/$currency_rate;
                }

                if(isset($array_amounts['c45'])){
                    $amount45=$array_amounts['c45'];
                    $total45=$amount45/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                }

                if(isset($array_markups['m45'])){
                    $markup45=$array_markups['m45'];
                    $total_markup45=$markup45/$currency_rate;
                }

                $value->total_20=number_format($sum20+$total_markup20, 2, '.', '');
                $value->total_40=number_format($sum40+$total_markup40, 2, '.', '');
                $value->total_40hc=number_format($sum40hc+$total_markup40hc, 2, '.', '');
                $value->total_40nor=number_format($sum40nor+$total_markup40nor, 2, '.', '');
                $value->total_45=number_format($sum45+$total_markup45, 2, '.', '');

                $value->total_markup20=number_format($total_markup20, 2, '.', '');
                $value->total_markup40=number_format($total_markup40, 2, '.', '');
                $value->total_markup40hc=number_format($total_markup40hc, 2, '.', '');
                $value->total_markup40nor=number_format($total_markup40nor, 2, '.', '');
                $value->total_markup45=number_format($total_markup45, 2, '.', '');     

                $currency_charge = Currency::find($value->currency_id);
                $value->currency_usd = $currency_charge->rates;
                $value->currency_eur = $currency_charge->rates_eur;
            }

            //Inland
            foreach ($item->inland as $inland) {
                if($quote->pdf_option->grouped_total_currency==1){
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                }else{
                    $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                $array_amounts = json_decode($inland->rate,true);
                $array_markups = json_decode($inland->markup,true);
                if(isset($array_amounts['c20'])){
                    $amount20=$array_amounts['c20'];
                    $total20=$amount20/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_markups['m20'])){
                    $markup20=$array_markups['m20'];
                    $total_markup20=$markup20/$currency_rate;
                }

                if(isset($array_amounts['c40'])){
                    $amount40=$array_amounts['c40'];
                    $total40=$amount40/$currency_rate;          
                    $sum40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_markups['m40'])){
                    $markup40=$array_markups['m40'];
                    $total_markup40=$markup40/$currency_rate;
                }

                if(isset($array_amounts['c40hc'])){
                    $amount40hc=$array_amounts['c40hc'];
                    $total40hc=$amount40hc/$currency_rate;          
                    $sum40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_markups['m40hc'])){
                    $markup40hc=$array_markups['m40hc'];
                    $total_markup40hc=$markup40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor'])){
                    $amount40nor=$array_amounts['c40nor'];
                    $total40nor=$amount40nor/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_markups['m40nor'])){
                    $markup40nor=$array_markups['m40nor'];
                    $total_markup40nor=$markup40nor/$currency_rate;
                }

                if(isset($array_amounts['c45'])){
                    $amount45=$array_amounts['c45'];
                    $total45=$amount45/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                }

                if(isset($array_markups['m45'])){
                    $markup45=$array_markups['m45'];
                    $total_markup45=$markup45/$currency_rate;
                }


                $inland->total_20=number_format($sum20, 2, '.', '');
                $inland->total_40=number_format($sum40, 2, '.', '');
                $inland->total_40hc=number_format($sum40hc, 2, '.', '');
                $inland->total_40nor=number_format($sum40nor, 2, '.', '');
                $inland->total_45=number_format($sum45, 2, '.', '');

                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }
        }

        /** ORIGIN GHARGES **/

        $origin_charges_grouped=$this->processOriginGrouped($origin_charges, $quote, $currency_cfg);

        $origin_charges_detailed=$this->processOriginDetailed($origin_charges, $quote, $currency_cfg);

        /** DESTINATION CHARGES **/

        $destination_charges_grouped=$this->processDestinationGrouped($destination_charges, $quote, $currency_cfg);

        $destination_charges=$this->processDestinationDetailed($destination_charges, $quote, $currency_cfg);

        /** FREIGHT CHARGES **/

        $freight_charges_grouped = $this->processFreightCharges($freight_charges, $quote, $currency_cfg);

        return $view = \View::make('quotesv2.html', ['quote'=>$quote,'rates'=>$rates,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'origin_charges_detailed'=>$origin_charges_detailed,'destination_charges_grouped'=>$destination_charges_grouped,'package_loads'=>$package_loads]);
    }

    /**
   * Delete quotes v2 (Soft Delete)
   * @param integer $id 
   * @return type
   */
    public function destroy($id){
        $quote_id = obtenerRouteKey($id);
        QuoteV2::where('id',$quote_id)->delete();
        return response()->json(['message' => 'Ok']);
    }

    /**
   * Destroy automatic rates
   * @param  integer $id
   * @return array json
   */
    public function delete($id){
        AutomaticRate::where('id',$id)->delete();
        return response()->json(['message' => 'Ok']);
    }

    /**
   * Delete Charges FCL
   * @param Request $request 
   * @param integer $id 
   * @return array json
   */

    public function deleteCharge(Request $request, $id){
        if($request->type==1){
            Charge::where('id',$id)->delete();
        }else{
            $charge = Charge::findOrFail($id);
            $charge->amount=null;
            $charge->markups=null;
            $charge->update();
        }

        return response()->json(['message' => 'Ok','type'=>$request->type]);
    }

    /**
   * Delete charges FCL/AIR
   * @param Request $request 
   * @param integer $id 
   * @return Array Json
   */

    public function deleteChargeLclAir(Request $request, $id){
        if($request->type==1){
            ChargeLclAir::where('id',$id)->delete();
        }else{
            $charge = ChargeLclAir::findOrFail($id);
            $charge->units=0;
            $charge->price_per_unit=0;
            $charge->markup=0;
            $charge->total=0;
            $charge->update();
        }
        return response()->json(['message' => 'Ok','type'=>$request->type]);
    }

    /**
   * Delete inlands
   * @param Request $request 
   * @param integer $id 
   * @return Array Json
   */

    public function deleteInland(Request $request, $id){

        AutomaticInland::where('id',$id)->delete();

        return response()->json(['message' => 'Ok']);
    }

    /**
   * Store quotes
   * @param Request $request 
   * @return type
   */

    public function storeCharge(Request $request){

        $array_amount_20 = array();
        $array_markup_20 = array();
        $array_amount_40 = array();
        $array_markup_40 = array();
        $array_amount_40hc = array();
        $array_markup_40hc = array();
        $array_amount_40nor = array();
        $array_markup_40nor = array();
        $array_amount_45 = array();
        $array_markup_45 = array();
        $merge_amounts = array();
        $merge_markups = array();
        if($request->amount_c20){
            $array_amount_20 = array('c20' => $request->amount_c20);
        }
        if($request->markup_m20){
            $array_markup_20 = array('m20' => $request->markup_m20);
        }
        if($request->amount_c40){
            $array_amount_40 = array('c40' => $request->amount_c40);
        }
        if($request->markup_m40){
            $array_markup_40 = array('m40' => $request->markup_m40);
        }
        if($request->amount_c40hc){
            $array_amount_40hc = array('c40hc' => $request->amount_c40hc);
        }
        if($request->markup_m40hc){
            $array_markup_40hc = array('m40hc' => $request->markup_m40hc);
        }
        if($request->amount_c40nor){
            $array_amount_40nor = array('c40nor' => $request->amount_c40nor);
        }
        if($request->markup_m40nor){
            $array_markup_40nor = array('m40nor' => $request->markup_m40nor);
        }
        if($request->amount_c45){
            $array_amount_45 = array('c45' => $request->amount_c45);
        }
        if($request->markup_m45){
            $array_markup_45 = array('m45' => $request->markup_m45);
        }
        $merge_amounts = array_merge($array_amount_20,$array_amount_40,$array_amount_40hc,$array_amount_40nor,$array_amount_45);
        $merge_markups = array_merge($array_markup_20,$array_markup_40,$array_markup_40hc,$array_markup_40nor,$array_markup_45);

        $charge = new Charge();
        $charge->automatic_rate_id=$request->automatic_rate_id;
        $charge->type_id=$request->type_id;
        $charge->surcharge_id=$request->surcharge_id;
        $charge->calculation_type_id=$request->calculation_type_id;
        $charge->amount=json_encode($merge_amounts);
        $charge->markups=json_encode($merge_markups);
        $charge->currency_id=$request->currency_id;
        $charge->save();

        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);

        $surcharge = Surcharge::find($request->surcharge_id);
        $calculation_type = CalculationType::find($request->calculation_type_id);
        $currency_charge = Currency::find($request->currency_id);

        $rates = AutomaticRate::whereHas('charge', function ($query) use($request){
            $query->where('type_id', $request->type_id);
        })->where('id',$request->automatic_rate_id)->get();

        $charges = Charge::where('automatic_rate_id',$request->automatic_rate_id)->where('type_id', $request->type_id)->get();

        //foreach ($rates as $item) {

        $sum_total_20=0;
        $sum_total_40=0;
        $sum_total_40hc=0;
        $sum_total_40nor=0;
        $sum_total_45=0;

        //Charges
        foreach ($charges as $value) {

            $typeCurrency =  $currency_cfg->alphacode;

            $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;

            $amount20=0;
            $amount40=0;
            $amount40hc=0;
            $amount40nor=0;
            $amount45=0;

            $markup20=0;
            $markup40=0;
            $markup40hc=0;
            $markup40nor=0;
            $markup45=0;

            $total_20=0;
            $total_40=0;
            $total_40hc=0;
            $total_40nor=0;
            $total_45=0;

            $total_markup20=0;
            $total_markup40=0;
            $total_markup40hc=0;
            $total_markup40nor=0;
            $total_markup45=0;

            $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

            $array_amounts = json_decode($value->amount,true);
            $array_markups = json_decode($value->markups,true);

            if(isset($array_amounts['c20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $sum20 = number_format($total20, 2, '.', '');
            }

            if(isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total_markup20=number_format($markup20/$currency_rate, 2, '.', '');
            }

            if(isset($array_amounts['c40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;          
                $sum40 = number_format($total40, 2, '.', '');
            }

            if(isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total_markup40=number_format($markup40/$currency_rate, 2, '.', '');
            }

            if(isset($array_amounts['c40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;          
                $sum40hc = number_format($total40hc, 2, '.', '');
            }

            if(isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total_markup40hc=number_format($markup40hc/$currency_rate, 2, '.', '');
            }

            if(isset($array_amounts['c40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $sum40nor = number_format($total40nor, 2, '.', '');
            }

            if(isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total_markup40nor=number_format($markup40nor/$currency_rate, 2, '.', '');
            }

            if(isset($array_amounts['c45'])){
                $amount45=$array_amounts['c45'];
                $total45=$amount45/$currency_rate;
                $sum45 = number_format($total45, 2, '.', '');
            }

            if(isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total_markup45=number_format($markup45/$currency_rate, 2, '.', '');
            }

            $total_20=$sum20+$total_markup20;
            $total_40=$sum40+$total_markup40;
            $total_40hc=$sum40hc+$total_markup40hc;
            $total_40nor=$sum40nor+$total_markup40nor;
            $total_45=$sum45+$total_markup45;

            $sum_total_20+=number_format($total_20, 2, '.', '');
            $sum_total_40+=number_format($total_40, 2, '.', '');
            $sum_total_40hc+=number_format($total_40hc, 2, '.', '');
            $sum_total_40nor+=number_format($total_40nor, 2, '.', '');
            $sum_total_45+=number_format($total_45, 2, '.', '');
        }

        return response()->json(['message' => 'Ok','amount20'=>$amount20,'markup20'=>$markup20,'total_20'=>$total_20,'amount40'=>$amount40,'markup40'=>$markup40,'total_40'=>$total_40,'amount40hc'=>$amount40hc,'markup40hc'=>$markup40hc,'total_40hc'=>$total_40hc,'amount40nor'=>$amount40nor,'markup40nor'=>$markup40nor,'total_40nor'=>$total_40nor,'amount45'=>$amount45,'markup45'=>$markup45,'total_45'=>$total_45,'surcharge'=>$surcharge->name,'calculation_type'=>$calculation_type->name,'currency'=>$currency_charge->alphacode,'sum_total_20'=>$sum_total_20,'sum_total_40'=>$sum_total_40,'sum_total_40hc'=>$sum_total_40hc,'sum_total_40nor'=>$sum_total_40nor,'sum_total_45'=>$sum_total_45,'id'=>$charge->id]);

    }

    public function storeChargeLclAir(Request $request){

        $charge = new ChargeLclAir();
        $charge->automatic_rate_id=$request->automatic_rate_id;
        $charge->type_id=$request->type_id;
        $charge->surcharge_id=$request->surcharge_id;
        $charge->calculation_type_id=$request->calculation_type_id;
        $charge->units=$request->units;
        $charge->price_per_unit=$request->price_per_unit;
        $charge->total=$request->total;
        $charge->markup=$request->markup;
        $charge->currency_id=$request->currency_id;
        $charge->save();

        $charge = ChargeLclAir::find($charge->id);
        $total=($charge->units*$charge->price_per_unit)+$charge->markup;

        return response()->json(['message' => 'Ok','surcharge'=>$charge->surcharge->name,'calculation_type'=>$charge->calculation_type->name,'units'=>$charge->units,'rate'=>$charge->price_per_unit,'markup'=>$charge->markup,'total'=>$total,'currency'=>$charge->currency->alphacode,'type'=>$charge->type_id,'id'=>$charge->id]);

    }

    public function getCompanyPayments($id)
    {
        $payments = Company::find($id);
        return $payments->payment_conditions;
    }

    public function termsandconditions($origin_port,$destiny_port,$carrier,$mode){

        // TERMS AND CONDITIONS 
        $carrier_all = 26;
        $port_all = harbor::where('name','ALL')->first();
        $term_port_orig = array($origin_port->id);
        $term_port_dest = array($destiny_port->id);
        $term_carrier_id[] = $carrier->id;
        array_push($term_carrier_id,$carrier_all);

        /* $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q) use($term_carrier_id)  {
      $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
        $b->wherein('carrier_id',$term_carrier_id);
      });
    })->get();*/
        $terms_origin = TermsPort::wherein('port_id',$term_port_orig)->with('term')->whereHas('term', function($q) use($term_carrier_id)  {
            $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
                $b->wherein('carrier_id',$term_carrier_id);
            });
        })->get();

        $terms_destination = TermsPort::wherein('port_id',$term_port_dest)->with('term')->whereHas('term', function($q)  use($term_carrier_id) {
            $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
                $b->wherein('carrier_id',$term_carrier_id);
            });
        })->get();

        $termsO='';
        $termsD='';
        $terms ='';

        foreach($terms_origin as $termOrig){
            $terms .="<br>";
            $termsO = $origin_port->name." / ".$carrier->name;
            $termsO .=  "<br>".$termOrig->term->export."<br>";
        }
        foreach($terms_destination as $termDest){
            $terms .="<br>";
            $termsD = $destiny_port->name." / ".$carrier->name;
            $termsD .=  "<br>".$termDest->term->export."<br>";
        }
        $terms = $termsO." ".$termsD ; 
        return $terms;
    }

    public function remarksCondition($origin_port,$destiny_port,$carrier,$mode){

        // TERMS AND CONDITIONS 
        $carrier_all = 26;
        $port_all = harbor::where('name','ALL')->first();
        $rem_port_orig = array($origin_port->id);
        $rem_port_dest = array($destiny_port->id);
        $rem_carrier_id[] = $carrier->id;
        array_push($rem_carrier_id,$carrier_all);


        /* $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q) use($term_carrier_id)  {
      $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
        $b->wherein('carrier_id',$term_carrier_id);
      });
    })->get();*/

        $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();

        $language_id = $company->companyUser->pdf_language;



        $remarks_all = RemarkHarbor::where('port_id',$port_all->id)->with('remark')->whereHas('remark', function($q) use($rem_carrier_id,$language_id)  {
            $q->where('remark_conditions.company_user_id',\Auth::user()->company_user_id)->where('language_id',$language_id)->whereHas('remarksCarriers', function($b) use($rem_carrier_id)  {
                $b->wherein('carrier_id',$rem_carrier_id);
            });
        })->get();


        $remarks_origin = RemarkHarbor::wherein('port_id',$rem_port_orig)->with('remark')->whereHas('remark', function($q) use($rem_carrier_id,$language_id)  {
            $q->where('remark_conditions.company_user_id',\Auth::user()->company_user_id)->where('language_id',$language_id)->whereHas('remarksCarriers', function($b) use($rem_carrier_id)  {
                $b->wherein('carrier_id',$rem_carrier_id);
            });
        })->get();

        $remarks_destination = RemarkHarbor::wherein('port_id',$rem_port_dest)->with('remark')->whereHas('remark', function($q)  use($rem_carrier_id,$language_id) {
            $q->where('remark_conditions.company_user_id',\Auth::user()->company_user_id)->where('language_id',$language_id)->whereHas('remarksCarriers', function($b) use($rem_carrier_id)  {
                $b->wherein('carrier_id',$rem_carrier_id);
            });
        })->get();

        $remarkA='';
        $remarkO='';
        $remarkD='';
        $rems ='';

        foreach($remarks_all as $remAll){
            $rems .="<br>";
            $remarkA = $origin_port->name." / ".$carrier->name;
            if($mode == 1)
                $remarkA .=  "<br>".$remAll->remark->export;
            else
                $remarkA .=  "<br>".$remAll->remark->import;
        }

        foreach($remarks_origin as $remOrig){

            $rems .="<br>";
            $remarkO = $origin_port->name." / ".$carrier->name;
            if($mode == 1)
                $remarkO .=  "<br>".$remOrig->remark->export;
            else
                $remarkO .=  "<br>".$remOrig->remark->import;

        }
        foreach($remarks_destination as $remDest){
            $rems .="<br>";
            $remarkD = $destiny_port->name." / ".$carrier->name;
            if($mode == 1)
                $remarkD .=  "<br>".$remDest->remark->export;
            else
                $remarkD .=  "<br>".$remDest->remark->import;
        }
        $rems = $remarkO." ".$remarkD." ".$remarkA ; 
        return $rems;

    }

    function saveRemarks($rateId,$orig,$dest,$carrier,$modo){

        $carrier_all = 26;
        $port_all = harbor::where('name','ALL')->first();
        $nameOrig = $orig->name;
        $rem_port_orig[] =$orig->id;
        $nameDest = $dest->name;
        $rem_port_dest[] = $dest->id;
        $rem_carrier_id[] = $carrier;
        array_push($rem_carrier_id,$carrier_all);

        $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
        $language_id = $company->companyUser->pdf_language;

        $remarks_all = RemarkHarbor::where('port_id',$port_all->id)->with('remark')->whereHas('remark', function($q) use($rem_carrier_id,$language_id)  {
            $q->where('remark_conditions.company_user_id',\Auth::user()->company_user_id)->whereHas('remarksCarriers', function($b) use($rem_carrier_id)  {
                $b->wherein('carrier_id',$rem_carrier_id);
            });
        })->get();


        $remarks_origin = RemarkHarbor::wherein('port_id',$rem_port_orig)->with('remark')->whereHas('remark', function($q) use($rem_carrier_id,$language_id)  {
            $q->where('remark_conditions.company_user_id',\Auth::user()->company_user_id)->whereHas('remarksCarriers', function($b) use($rem_carrier_id)  {
                $b->wherein('carrier_id',$rem_carrier_id);
            });
        })->get();

        $remarks_destination = RemarkHarbor::wherein('port_id',$rem_port_dest)->with('remark')->whereHas('remark', function($q)  use($rem_carrier_id,$language_id) {
            $q->where('remark_conditions.company_user_id',\Auth::user()->company_user_id)->whereHas('remarksCarriers', function($b) use($rem_carrier_id)  {
                $b->wherein('carrier_id',$rem_carrier_id);
            });
        })->get();




        $remarks_english="";
        $remarks_spanish="";
        $remarks_portuguese="";

        foreach($remarks_all as $remAll){
            $remarks_english .="<br>";
            $remarks_spanish .="<br>";
            $remarks_portuguese .="<br>";
            if($modo == '1'){
                if($remAll->remark->language_id == '1')
                    $remarks_english .=$remAll->remark->export."<br>";
                if($remAll->remark->language_id == '2')
                    $remarks_spanish .=$remAll->remark->export."<br>";
                if($remAll->remark->language_id == '3')
                    $remarks_portuguese .=$remAll->remark->export."<br>";
            }else{ // import

                if($remAll->remark->language_id == '1')
                    $remarks_english .=$remAll->remark->import."<br>";
                if($remAll->remark->language_id == '2')
                    $remarks_spanish .=$remAll->remark->import."<br>";
                if($remAll->remark->language_id == '3')
                    $remarks_portuguese .=$remAll->remark->import."<br>";
            }

        }

        foreach($remarks_origin as $remOrig){

            $remarks_english .="<br>";
            $remarks_spanish .="<br>";
            $remarks_portuguese .="<br>";

            if($modo == '1'){
                if($remOrig->remark->language_id == '1')
                    $remarks_english .=$remOrig->remark->export."<br>";
                if($remOrig->remark->language_id == '2')
                    $remarks_spanish .=$remOrig->remark->export."<br>";
                if($remOrig->remark->language_id == '3')
                    $remarks_portuguese .=$remOrig->remark->export."<br>";
            }else{ // import

                if($remOrig->remark->language_id == '1')
                    $remarks_english .=$remOrig->remark->import."<br>";
                if($remOrig->remark->language_id == '2')
                    $remarks_spanish .=$remOrig->remark->import."<br>";
                if($remOrig->remark->language_id == '3')
                    $remarks_portuguese .=$remOrig->remark->import."<br>";
            }

        }

        foreach($remarks_destination as $remDest){

            $remarks_english .="<br>";
            $remarks_spanish .="<br>";
            $remarks_portuguese .="<br>";

            if($modo == '1'){
                if($remDest->remark->language_id == '1')
                    $remarks_english .=$remDest->remark->export."<br>";
                if($remDest->remark->language_id == '2')
                    $remarks_spanish .=$remDest->remark->export."<br>";
                if($remDest->remark->language_id == '3')
                    $remarks_portuguese .=$remDest->remark->export."<br>";
            }else{ // import

                if($remDest->remark->language_id == '1')
                    $remarks_english .=$remDest->remark->import."<br>";
                if($remDest->remark->language_id == '2')
                    $remarks_spanish .=$remDest->remark->import."<br>";
                if($remDest->remark->language_id == '3')
                    $remarks_portuguese .=$remDest->remark->import."<br>";
            }
        }

        //   $remarkGenerales = array('english' => $remarks_english , 'spanish' => $remarks_spanish , 'portuguese' => $remarks_portuguese ,'origen' => $nameOrig , 'destino' => $nameDest  );

        //return $remarkGenerales ; 


        $quoteEdit = AutomaticRate::find($rateId);
        $quoteEdit->remarks_english= $remarks_english;
        $quoteEdit->remarks_spanish = $remarks_spanish;
        $quoteEdit->remarks_portuguese = $remarks_portuguese;
        $quoteEdit->update();



    }

    /*function saveRemarks($quoteId,$remarkGenerales){

    $remarks_english="";
    $remarks_spanish="";
    $remarks_portuguese="";

    foreach($remarkGenerales as  $remark){
      $titulo = $remark['origen']." / ".$remark['destino']."<br>";

      $remarks_english.= $titulo."<br>". $remark['english']."<br>";
      $remarks_spanish.=$titulo."<br>". $remark['english']."<br>";
      $remarks_portuguese.=$titulo."<br>". $remark['english']."<br>";

    }
    $quoteEdit = QuoteV2::find($quoteId);
    $quoteEdit->remarks_english= $remarks_english;
    $quoteEdit->remarks_spanish = $remarks_spanish;
    $quoteEdit->remarks_portuguese = $remarks_portuguese;
    $quoteEdit->update();


  }*/

    function saveTerms($quoteId,$type,$modo){

        $companyUser = CompanyUser::All();
        $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
        $terms = TermAndConditionV2::where('company_user_id', Auth::user()->company_user_id)->where('type',$type)->with('language')->get();


        $terminos_english="";
        $terminos_spanish="";
        $terminos_portuguese="";

        //Export
        foreach($terms as $term){
            if($modo == '1'){
                if($term->language_id == '1')
                    $terminos_english .=$term->export."<br>";
                if($term->language_id == '2')
                    $terminos_spanish .=$term->export."<br>";
                if($term->language_id == '3')
                    $terminos_portuguese .=$term->export."<br>";
            }else{ // import

                if($term->language_id == '1')
                    $terminos_english .=$term->import."<br>";
                if($term->language_id == '2')
                    $terminos_spanish .=$term->import."<br>";
                if($term->language_id == '3')
                    $terminos_portuguese .=$term->import."<br>";
            }
        }

        $quoteEdit = QuoteV2::find($quoteId);
        $quoteEdit->terms_english= $terminos_english;
        $quoteEdit->terms_and_conditions = $terminos_spanish;
        $quoteEdit->terms_portuguese = $terminos_portuguese;

        $quoteEdit->update();


    }

    function updatePdfApi($id){
        //$this->dispatch((new UpdatePdf($id, Auth::user()->company_user_id, Auth::user()->id))->onQueue('default'));
        UpdatePdf::dispatch($id,Auth::user()->company_user_id, Auth::user()->id)->onQueue('default');
    }

    public function store(Request $request){
        if(!empty($request->input('form'))){
            $form =  json_decode($request->input('form'));
            $info = $request->input('info');
            $equipment =  stripslashes(json_encode($form->equipment ));
            $dateQ = explode('/',$form->date);
            $since = $dateQ[0];
            $until = $dateQ[1];
            $priceId = null;
            $mode =   $form->mode;
            if(isset($form->price_id )){
                $priceId = $form->price_id;
                if($priceId=="0"){
                    $priceId = null;
                }
            }       
            $fcompany_id = null;
            $fcontact_id  = null;
            $payments = null;

            if(isset($form->company_id_quote)){

                if($form->company_id_quote != "0" && $form->company_id_quote != null ){
                    $payments = $this->getCompanyPayments($form->company_id_quote);
                    $fcompany_id = $form->company_id_quote;

                }
            }

            if(isset($form->contact_id)){
                if($form->contact_id != "0" && $form->contact_id != null ){
                    $fcontact_id  = $form->contact_id;
                }
            }

            $request->request->add(['company_user_id' => \Auth::user()->company_user_id ,'quote_id'=>$this->idPersonalizado(),'type'=>'FCL','delivery_type'=>$form->delivery_type,'company_id'=>$fcompany_id,'contact_id' =>$fcontact_id,'validity_start'=>$since,'validity_end'=>$until,'user_id'=>\Auth::id(), 'equipment'=>$equipment  , 'status'=>'Draft' ,'date_issued'=>$since ,'price_id' => $priceId ,'payment_conditions' => $payments,'origin_address'=> $form->origin_address,'destination_address'  => $form->destination_address ]);

            $quote= QuoteV2::create($request->all());



            $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
            $currency_id = $company->companyUser->currency_id;
            $currency = Currency::find($currency_id);

            $pdf_option = new PdfOption();
            $pdf_option->quote_id=$quote->id;
            $pdf_option->show_type='detailed';
            $pdf_option->grouped_total_currency=0;
            $pdf_option->total_in_currency=$currency->alphacode;
            $pdf_option->freight_charges_currency=$currency->alphacode;
            $pdf_option->origin_charges_currency=$currency->alphacode;
            $pdf_option->destination_charges_currency=$currency->alphacode;
            $pdf_option->show_total_freight_in_currency=$currency->alphacode;
            $pdf_option->show_schedules=1;
            $pdf_option->show_gdp_logo=1;
            $pdf_option->language='English';
            $pdf_option->save();

        }else{// COTIZACION MANUAL

            $dateQ = explode('/',$request->input('date'));
            $since = $dateQ[0];
            $until = $dateQ[1];
            $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();

            $idCurrency = $company->companyUser->currency_id;
            $currency = Currency::find($idCurrency);

            $arregloNull = array();
            $arregloNull = json_encode($arregloNull);


            if($request->input('type') == '1'){
                $typeText = "FCL";
                $equipment =  stripslashes(json_encode($request->input('equipment')));
                $delivery_type = $request->input('delivery_type') ;


            }
            if($request->input('type') == '2'){
                $typeText = "LCL";
                $equipment =  $arregloNull;
                $delivery_type = $request->input('delivery_type') ;
            }
            if($request->input('type') == '3'){
                $typeText = "AIR";
                $equipment =  $arregloNull;
                $delivery_type = $request->input('delivery_type_air') ;

            }
            $fcompany_id = null;
            $fcontact_id  = null;
            $payments = null;
            //  if(isset($request->input('company_id_quote'))){
            if($request->input('company_id_quote')!= "0" && $request->input('company_id_quote') != null ){
                $payments = $this->getCompanyPayments($request->input('company_id_quote'));
                $fcompany_id = $request->input('company_id_quote');
                $fcontact_id  = $request->input('contact_id');
            }
            //  }



            $priceId = null;
            if(isset($request->price_id )){
                $priceId = $request->price_id;
                if($priceId=="0"){
                    $priceId = null;
                }
            }
            $request->request->add(['company_user_id' => \Auth::user()->company_user_id ,'quote_id'=>$this->idPersonalizado(),'type'=> $typeText,'delivery_type'=>$delivery_type,'company_id'=>$fcompany_id,'contact_id' =>$fcontact_id ,'validity_start'=>$since,'validity_end'=>$until,'user_id'=>\Auth::id(), 'equipment'=>$equipment  , 'status'=>'Draft' , 'date_issued'=>$since ,'payment_conditions' => $payments ,'price_id' => $priceId ]);
            $quote= QuoteV2::create($request->all());
            $modo  =  $request->input('mode');
            // FCL
            if($typeText == 'FCL' ){
                foreach($request->input('originport') as $origP){
                    $infoOrig = explode("-", $origP);
                    $origin_port[] = $infoOrig[0];
                }
                foreach($request->input('destinyport') as $destP){
                    $infoDest = explode("-", $destP);
                    $destiny_port[] = $infoDest[0];
                }
                foreach($origin_port as $orig){
                    foreach($destiny_port as $dest){
                        $request->request->add(['contract' => '' ,'origin_port_id'=> $orig,'destination_port_id'=>$dest ,'currency_id'=>  $idCurrency ,'quote_id'=>$quote->id]);
                        $rate = AutomaticRate::create($request->all());

                        $oceanFreight = new Charge();
                        $oceanFreight->automatic_rate_id= $rate->id;
                        $oceanFreight->type_id = '3' ;
                        $oceanFreight->surcharge_id = null ;
                        $oceanFreight->calculation_type_id = '5' ;
                        $oceanFreight->amount = $arregloNull;
                        $oceanFreight->markups = $arregloNull;
                        $oceanFreight->currency_id = $idCurrency;
                        $oceanFreight->total =  $arregloNull;
                        $oceanFreight->save();

                    }
                }

                $this->saveTerms($quote->id,'FCL',$modo);
            }
            if($typeText == 'LCL'){
                foreach($request->input('originport') as $origP){
                    $infoOrig = explode("-", $origP);
                    $origin_port[] = $infoOrig[0];
                }
                foreach($request->input('destinyport') as $destP){
                    $infoDest = explode("-", $destP);
                    $destiny_port[] = $infoDest[0];
                }
                foreach($origin_port as $orig){
                    foreach($destiny_port as $dest){
                        $request->request->add(['contract' => '' ,'origin_port_id'=> $orig,'destination_port_id'=>$dest ,'currency_id'=>  $idCurrency ,'quote_id'=>$quote->id]);
                        $rate = AutomaticRate::create($request->all());


                        $oceanFreight = new ChargeLclAir();
                        $oceanFreight->automatic_rate_id= $rate->id;
                        $oceanFreight->type_id = '3' ;
                        $oceanFreight->surcharge_id = null ;
                        $oceanFreight->calculation_type_id = '4' ;
                        $oceanFreight->units = "0";
                        $oceanFreight->price_per_unit =  "0";
                        $oceanFreight->total = "0";
                        $oceanFreight->markup =  "0";
                        $oceanFreight->currency_id = $idCurrency; 
                        $oceanFreight->save();


                    }
                }

                $this->saveTerms($quote->id,'LCL',$modo);
            }
            if($typeText == 'AIR' ){

                $request->request->add(['contract' => '' ,'origin_airport_id'=> $request->input('origin_airport_id'),'destination_airport_id'=> $request->input('destination_airport_id'),'currency_id'=>  $idCurrency ,'quote_id'=>$quote->id]);
                $rate = AutomaticRate::create($request->all());


                $oceanFreight = new ChargeLclAir();
                $oceanFreight->automatic_rate_id= $rate->id;
                $oceanFreight->type_id = '3' ;
                $oceanFreight->surcharge_id = null ;
                $oceanFreight->calculation_type_id = '4' ;
                $oceanFreight->units = "0";
                $oceanFreight->price_per_unit =  "0";
                $oceanFreight->total = "0";
                $oceanFreight->markup =  "0";
                $oceanFreight->currency_id = $idCurrency; 
                $oceanFreight->save();




            }
            //LCL        $input = Input::all();

            if($typeText == 'LCL' || $typeText == 'AIR' ){
                $input = Input::all();
                $quantity = array_values( array_filter($input['quantity']) );
                //dd($input);
                $type_cargo = array_values( array_filter($input['type_load_cargo']) );
                $height = array_values( array_filter($input['height']) );
                $width = array_values( array_filter($input['width']) );
                $large = array_values( array_filter($input['large']) );
                $weight = array_values( array_filter($input['weight']) );
                $volume = array_values( array_filter($input['volume']) );
                if(count($quantity)>0){
                    foreach($type_cargo as $key=>$item){
                        $package_load = new PackageLoadV2();
                        $package_load->quote_id = $quote->id;
                        $package_load->type_cargo = $type_cargo[$key];
                        $package_load->quantity = $quantity[$key];
                        $package_load->height = $height[$key];
                        $package_load->width = $width[$key];
                        $package_load->large = $large[$key];
                        $package_load->weight = $weight[$key];
                        $package_load->total_weight = $weight[$key]*$quantity[$key];
                        // if(!empty($volume)){
                        if(!empty($volume[$key]) && $volume[$key] != null){
                            $package_load->volume = $volume[$key];
                        }else{
                            $package_load->volume = 0.01;
                        }

                        $package_load->save();
                    }
                }
            }


            $pdf_option = new PdfOption();
            $pdf_option->quote_id=$quote->id;
            $pdf_option->show_type='detailed';
            $pdf_option->grouped_total_currency=0;
            $pdf_option->total_in_currency=$currency->alphacode;
            $pdf_option->freight_charges_currency=$currency->alphacode;
            $pdf_option->origin_charges_currency=$currency->alphacode;
            $pdf_option->destination_charges_currency=$currency->alphacode;
            $pdf_option->show_total_freight_in_currency=$currency->alphacode;
            $pdf_option->show_schedules=1;
            $pdf_option->show_gdp_logo=1;
            $pdf_option->language='English';
            $pdf_option->save();



            // MANUAL RATE
        }

        //CONDICION PARA GUARDAR AUTOMATIC QUOTE
        if(!empty($info)){
            $terms = '';

            foreach($info as $infoA){
                $info_D = json_decode($infoA);

                // Rates

                foreach($info_D->rates as $rateO){

                    $rates =   json_encode($rateO->rate);

                    $markups =   json_encode($rateO->markups);
                    $arregloNull = array();

                    $remarks = $info_D->remarks."<br>";          
                    // $remarks .= $this->remarksCondition($info_D->port_origin,$info_D->port_destiny,$info_D->carrier,$mode);

                    $request->request->add(['contract' => $info_D->contract->name." / ".$info_D->contract->number ,'origin_port_id'=> $info_D->port_origin->id,'destination_port_id'=>$info_D->port_destiny->id ,'carrier_id'=>$info_D->carrier->id ,'currency_id'=>  $info_D->currency->id ,'quote_id'=>$quote->id,'remarks'=>$remarks , 'schedule_type' =>$info_D->sheduleType , 'transit_time'=> $info_D->transit_time  , 'via' => $info_D->via ]);

                    $rate = AutomaticRate::create($request->all());

                    $oceanFreight = new Charge();
                    $oceanFreight->automatic_rate_id= $rate->id;
                    $oceanFreight->type_id = '3' ;
                    $oceanFreight->surcharge_id = null ;
                    $oceanFreight->calculation_type_id = '5' ;
                    $oceanFreight->amount = $rates;
                    $oceanFreight->markups = $markups;
                    $oceanFreight->currency_id = $info_D->currency->id;
                    $oceanFreight->total =  $rates;
                    $oceanFreight->save();

                    $inlandD =  $request->input('inlandD'.$rateO->rate_id);
                    $inlandO =  $request->input('inlandO'.$rateO->rate_id);
                    //INLAND DESTINO
                    if(!empty($inlandD)){

                        foreach( $inlandD as $inlandDestiny){

                            $inlandDestiny = json_decode($inlandDestiny);

                            $arregloMontoInDest = array();
                            $arregloMarkupsInDest = array();
                            $montoInDest = array();
                            $markupInDest = array();
                            foreach($inlandDestiny->inlandDetails as $key => $inlandDet){

                                if(@$inlandDet->sub_in != 0){
                                    $arregloMontoInDest = array($key => $inlandDet->sub_in);
                                    $montoInDest = array_merge($arregloMontoInDest,$montoInDest);  
                                }
                                if(@$inlandDet->markup != 0){
                                    $arregloMarkupsInDest = array($key => $inlandDet->markup);
                                    $markupInDest = array_merge($arregloMarkupsInDest,$markupInDest);
                                }

                            }



                            $arregloMontoInDest =  json_encode($montoInDest);
                            $arregloMarkupsInDest =  json_encode($markupInDest);
                            $inlandDest = new AutomaticInland();
                            $inlandDest->quote_id= $quote->id;
                            $inlandDest->automatic_rate_id = $rate->id;
                            $inlandDest->provider =  "Inland ".$form->destination_address;
                            $inlandDest->distance =  $inlandDestiny->km;
                            $inlandDest->contract = $info_D->contract->id;
                            $inlandDest->port_id = $inlandDestiny->port_id;
                            $inlandDest->type = $inlandDestiny->type;
                            $inlandDest->rate = $arregloMontoInDest;
                            $inlandDest->markup = $arregloMarkupsInDest;
                            $inlandDest->validity_start =$inlandDestiny->validity_start ;
                            $inlandDest->validity_end=$inlandDestiny->validity_end ;
                            $inlandDest->currency_id =  $info_D->idCurrency;
                            $inlandDest->save();

                        }  
                    }
                    //INLAND ORIGEN 

                    if(!empty($inlandO)){

                        foreach( $inlandO as $inlandOrigin){

                            $inlandOrigin = json_decode($inlandOrigin);

                            $arregloMontoInOrig = array();
                            $arregloMarkupsInOrig = array();
                            $montoInOrig = array();
                            $markupInOrig = array();
                            foreach($inlandOrigin->inlandDetails as $key => $inlandDetails){

                                if(@$inlandDetails->sub_in != 0){
                                    $arregloMontoInOrig = array($key => $inlandDetails->sub_in);
                                    $montoInOrig = array_merge($arregloMontoInOrig,$montoInOrig);  
                                }
                                if(@$inlandDetails->markup != 0){
                                    $arregloMarkupsInOrig = array($key => $inlandDetails->markup);
                                    $markupInOrig = array_merge($arregloMarkupsInOrig,$markupInOrig);
                                }

                            }

                            $arregloMontoInOrig =  json_encode($montoInOrig);
                            $arregloMarkupsInOrig =  json_encode($markupInOrig);
                            $inlandOrig = new AutomaticInland();
                            $inlandOrig->quote_id= $quote->id;
                            $inlandOrig->automatic_rate_id = $rate->id;
                            $inlandOrig->provider = "Inland ". $form->origin_address;
                            $inlandOrig->distance =  $inlandOrigin->km;
                            $inlandOrig->contract = $info_D->contract->id;
                            $inlandOrig->port_id = $inlandOrigin->port_id;
                            $inlandOrig->type = $inlandOrigin->type;
                            $inlandOrig->rate = $arregloMontoInOrig;
                            $inlandOrig->markup = $arregloMarkupsInOrig;
                            $inlandOrig->validity_start =$inlandOrigin->validity_start ;
                            $inlandOrig->validity_end=$inlandOrigin->validity_end ;
                            $inlandOrig->currency_id =  $info_D->idCurrency;
                            $inlandOrig->save();

                        }  
                    }

                    $this->saveRemarks($rate->id,$info_D->port_origin,$info_D->port_destiny,$info_D->carrier->id,$form->mode);

                }
                //CHARGES ORIGIN
                foreach($info_D->localorigin as $localorigin){
                    $arregloMontoO = array();
                    $arregloMarkupsO = array();
                    $montoO = array();
                    $markupO = array();
                    foreach($localorigin as $localO){
                        foreach($localO as $local){
                            if($local->type != '99'){
                                $arregloMontoO = array('c'.$local->type => $local->monto);
                                $montoO = array_merge($arregloMontoO,$montoO);
                                $arregloMarkupsO = array('m'.$local->type => $local->markup);
                                $markupO = array_merge($arregloMarkupsO,$markupO);
                            }
                            if($local->type == '99'){
                                $arregloO = array('type_id' => '1' , 'surcharge_id' => $local->surcharge_id , 'calculation_type_id' => $local->calculation_id, 'currency_id' => $local->currency_id);
                            }
                        }
                    }

                    $arregloMontoO =  json_encode($montoO);
                    $arregloMarkupsO =  json_encode($markupO);

                    $chargeOrigin = new Charge();
                    $chargeOrigin->automatic_rate_id= $rate->id;
                    $chargeOrigin->type_id = $arregloO['type_id'] ;
                    $chargeOrigin->surcharge_id = $arregloO['surcharge_id']  ;
                    $chargeOrigin->calculation_type_id = $arregloO['calculation_type_id']  ;
                    $chargeOrigin->amount =  $arregloMontoO  ;
                    $chargeOrigin->markups = $arregloMarkupsO  ;
                    $chargeOrigin->currency_id = $arregloO['currency_id']  ;
                    $chargeOrigin->total =  $arregloMarkupsO ;
                    $chargeOrigin->save();
                }

                // CHARGES DESTINY 
                foreach($info_D->localdestiny as $localdestiny){
                    $arregloMontoD = array();
                    $arregloMarkupsD = array();
                    $montoD = array();
                    $markupD = array();
                    foreach($localdestiny as $localD){
                        foreach($localD as $local){
                            if($local->type != '99'){

                                $arregloMontoD = array('c'.$local->type => $local->monto);
                                $montoD = array_merge($arregloMontoD,$montoD);
                                $arregloMarkupsD = array('m'.$local->type => $local->markup);
                                $markupD = array_merge($arregloMarkupsD,$markupD);
                            }
                            if($local->type == '99'){
                                $arregloD = array('type_id' => '2' , 'surcharge_id' => $local->surcharge_id , 'calculation_type_id' => $local->calculation_id, 'currency_id' => $local->currency_id );
                            }
                        }
                    }

                    $arregloMontoD =  json_encode($montoD);
                    $arregloMarkupsD =  json_encode($markupD);

                    $chargeDestiny = new Charge();
                    $chargeDestiny->automatic_rate_id= $rate->id;
                    $chargeDestiny->type_id = $arregloD['type_id'] ;
                    $chargeDestiny->surcharge_id = $arregloD['surcharge_id']  ;
                    $chargeDestiny->calculation_type_id = $arregloD['calculation_type_id']  ;
                    $chargeDestiny->amount =  $arregloMontoD;
                    $chargeDestiny->markups = $arregloMarkupsD;
                    $chargeDestiny->currency_id = $arregloD['currency_id']  ;
                    $chargeDestiny->total =  $arregloMarkupsD;
                    $chargeDestiny->save();
                }

                // CHARGES FREIGHT 
                foreach($info_D->localfreight as $localfreight){
                    $arregloMontoF = array();
                    $arregloMarkupsF = array();
                    $montoF = array();
                    $markupF = array();
                    foreach($localfreight as $localF){
                        foreach($localF as $local){
                            if($local->type != '99'){
                                $arregloMontoF = array('c'.$local->type => $local->monto);
                                $montoF = array_merge($arregloMontoF,$montoF);
                                $arregloMarkupsF = array('m'.$local->type => $local->markup);
                                $markupF = array_merge($arregloMarkupsF,$markupF);
                            }
                            if($local->type == '99'){
                                $arregloF = array('type_id' => '3' , 'surcharge_id' => $local->surcharge_id , 'calculation_type_id' => $local->calculation_id , 'currency_id' => $local->currency_id );
                            }
                        }
                    }
                    $arregloMontoF =  json_encode($montoF);
                    $arregloMarkupsF =  json_encode($markupF);

                    $chargeFreight = new Charge();
                    $chargeFreight->automatic_rate_id= $rate->id;
                    $chargeFreight->type_id = $arregloF['type_id'] ;
                    $chargeFreight->surcharge_id = $arregloF['surcharge_id']  ;
                    $chargeFreight->calculation_type_id = $arregloF['calculation_type_id']  ;
                    $chargeFreight->amount =  $arregloMontoF;
                    $chargeFreight->markups = $arregloMarkupsF;
                    $chargeFreight->currency_id = $arregloF['currency_id']  ;
                    $chargeFreight->total =  $arregloMarkupsF;
                    $chargeFreight->save();
                }


            }  


            // Terminos Automatica
            $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
            $language_id = $company->companyUser->pdf_language;
            $this->saveTerms($quote->id,'FCL',$form->mode);
            //$this->saveRemarks($quote->id,$remarksGenerales);

            // SAVE PDF FOR API 
            if(\Auth::user()->company_user_id){
                $company_user=CompanyUser::find(\Auth::user()->company_user_id);
                $currency_cfg = Currency::find($company_user->currency_id);
            }else{
                $company_user="";
                $currency_cfg ="";
            }

            //$pdfarray= $this->generatepdf($quote->id,$company_user,$currency_cfg,\Auth::user()->id);
            /*$pdf = $pdfarray['pdf'];
      $view = $pdfarray['view'];
      $idQuote= $pdfarray['idQuote'];
      $idQ = $pdfarray['idQ'];

      $pdf->loadHTML($view)->save('pdf/quote-'.$idQuote.'.pdf');
      $quote->addMedia('pdf/quote-'.$idQuote.'.pdf')->toMediaCollection('document','pdfApiS3');*/

        }
        return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
    }

    /**
   * Store new rates
   * @param Request $request 
   * @return STRING Json
   */
    public function storeRates(Request $request){

        $arregloNull = array();
        $arregloNull = json_encode($arregloNull);
        $quote = QuoteV2::find($request->input('quote_id'));
        $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
        $idCurrency = $company->companyUser->currency_id;
        $dateQ = explode('/',$request->input('date'));
        $since = $dateQ[0];
        $until = $dateQ[1];

        // FCL & LCL
        if($quote->type == 'FCL' || $quote->type == 'LCL'){
            foreach($request->input('originport') as $origP){
                $infoOrig = explode("-", $origP);
                $origin_port[] = $infoOrig[0];
            }
            foreach($request->input('destinyport') as $destP){
                $infoDest = explode("-", $destP);
                $destiny_port[] = $infoDest[0];
            }
            foreach($origin_port as $orig){
                foreach($destiny_port as $dest){
                    $request->request->add(['contract' => '' ,'origin_port_id'=> $orig,'destination_port_id'=>$dest,'carrier_id'=>$request->input('carrieManual')  ,'rates'=> $arregloNull ,'validity_start'=>$since,'validity_end'=>$until,'markups'=> $arregloNull ,'currency_id'=>  $idCurrency ,'total' => $arregloNull,'quote_id'=>$quote->id]);
                    $rate = AutomaticRate::create($request->all());
                }
            }
        }else if($quote->type == 'AIR' ){
            $request->request->add(['contract' => '' ,'origin_airport_id'=> $request->input('origin_airport_id'),'destination_airport_id'=> $request->input('destination_airport_id'),'airline_id'=>$request->input('airline_id')  ,'rates'=> $arregloNull ,'markups'=> $arregloNull ,'validity_start'=>$since,'validity_end'=>$until,'currency_id'=>  $idCurrency ,'total' => $arregloNull,'quote_id'=>$quote->id]);
            $rate = AutomaticRate::create($request->all());
        }

        if($quote->type == 'FCL'){
            $charge = new Charge();
            $charge->automatic_rate_id=$rate->id;
            $charge->type_id=3;
            $charge->calculation_type_id=5;
            $charge->amount=$arregloNull;
            $charge->markups=$arregloNull;
            $charge->currency_id=$idCurrency;
            $charge->save();
        }else{
            $charge = new ChargeLclAir();
            $charge->automatic_rate_id=$rate->id;
            $charge->type_id=3;
            $charge->calculation_type_id=5;
            $charge->units=0;
            $charge->price_per_unit=0;
            $charge->markup=0;
            $charge->total=0;
            $charge->currency_id=$idCurrency;
            $charge->save();
        }

        return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
    }

    /**
 * Show modal with form to edit rates
 * @param integer $id 
 * @return Illuminate\View\View
 */
    public function editRates($id){
        $rate=AutomaticRate::find($id);
        $quote=QuoteV2::find($rate->quote_id);
        $harbors=Harbor::pluck('display_name','id');
        $carriers=Carrier::pluck('name','id');
        $airlines=Airline::pluck('name','id');

        return view('quotesv2.partials.editRate', compact('rate','quote','harbors','carriers','airlines'));
    }

    /**
 * Update rates
 * @param integer $id 
 * @return Illuminate\View\View
 */
    public function updateRates(Request $request,$id){

        $rate=AutomaticRate::find($id);
        if($request->origin_port_id){
            $rate->origin_port_id=$request->origin_port_id;
        }
        if($request->destination_port_id){
            $rate->destination_port_id=$request->destination_port_id;
        }
        if($request->origin_airport_id){
            $rate->origin_airport_id=$request->origin_airport_id;
        }
        if($request->destination_airport_id){
            $rate->destination_airport_id=$request->destination_airport_id;
        }
        if($request->origin_address){
            $rate->origin_address=$request->origin_address;
        }
        if($request->destination_address){
            $rate->destination_address=$request->destination_address;
        }
        if($request->carrier_id){
            $rate->carrier_id=$request->carrier_id;
        }
        if($request->airline_id){
            $rate->airline_id=$request->airline_id;
        }    
        $rate->transit_time=$request->transit_time;
        $rate->schedule_type=$request->schedule_type;
        $rate->via=$request->via;
        $rate->update();

        return redirect()->action('QuoteV2Controller@show', setearRouteKey($rate->quote_id));
    }

    /**
   * Store new inlands
   * @param Request $request 
   * @return Illuminate\View\View
   */
    public function storeInlands(Request $request){

        $quote = QuoteV2::find($request->input('quote_id'));
        $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
        $idCurrency = $company->companyUser->currency_id;
        $dateQ = explode('/',$request->input('date'));
        $since = $dateQ[0];
        $until = $dateQ[1];

        if($request->quote_type=='FCL'){
            $arregloNull = array();
            $arregloNull = json_encode($arregloNull);
            $request->request->add(['contract' => '','rate'=> $arregloNull ,'validity_start'=>$since,'validity_end'=>$until,'markup'=> $arregloNull]);
            AutomaticInland::create($request->all());
        }else{
            $request->request->add(['contract' => '','validity_start'=>$since,'validity_end'=>$until]);
            AutomaticInlandLclAir::create($request->all());
        }

        return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
    }

    /**
 * Show modal with form to edit inlands
 * @param integer $id 
 * @return Illuminate\View\View
 */
    public function editInlands($id){
        $inland=AutomaticInland::find($id);
        $quote=QuoteV2::find($inland->quote_id);
        $harbors=Harbor::pluck('display_name','id');
        $carriers=Carrier::pluck('name','id');
        $airlines=Airline::pluck('name','id');
        $currencies=Currency::pluck('alphacode','id');

        return view('quotesv2.partials.editInland', compact('inland','quote','harbors','carriers','airlines','currencies'));
    }

    /**
 * Show modal with form to edit inlands lcl air
 * @param integer $id 
 * @return Illuminate\View\View
 */
    public function editInlandsLcl($id){
        $inland=AutomaticInlandLclAir::find($id);
        $quote=QuoteV2::find($inland->quote_id);
        $harbors=Harbor::pluck('display_name','id');
        $carriers=Carrier::pluck('name','id');
        $airlines=Airline::pluck('name','id');
        $currencies=Currency::pluck('alphacode','id');

        return view('quotesv2.partials.editInland', compact('inland','quote','harbors','carriers','airlines','currencies'));
    }

    /**
 * Update inlands
 * @param integer $id 
 * @return Illuminate\View\View
 */
    public function updateInlands(Request $request,$id){

        if($request->quote_type=='FCL'){
            $inland=AutomaticInland::find($id);
        }else{
            $inland=AutomaticInlandLclAir::find($id);
        }
        if($request->port_id){
            $inland->port_id=$request->port_id;
        }  
        $inland->type=$request->type;
        $inland->provider=$request->provider;
        $inland->currency_id=$request->currency_id;
        $inland->update();

        return redirect()->action('QuoteV2Controller@show', setearRouteKey($inland->quote_id));
    }

    /**
   * Description
   * @param type $pluck 
   * @return type
   */

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
        $incoterm->prepend('Select at option','');
        if(\Auth::user()->hasRole('subuser')){
            $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
                $q->where('user_id',\Auth::user()->id);
            })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
        }else{
            $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
        }
        $companies->prepend('Select at option','0');
        $harbors = Harbor::get()->pluck('display_name','id_complete');
        $countries = Country::all()->pluck('name','id');

        $prices = Price::all()->pluck('name','id');
        $carrierMan = Carrier::all()->pluck('name','id');
        $airlines = Airline::all()->pluck('name','id');

        $company_user = User::where('id',\Auth::id())->first();
        if($company_user->companyUser) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
        }else{
            $currency_name = '';
        }
        $currencies = Currency::all()->pluck('alphacode','id');
        $hideO = 'hide';
        $hideD = 'hide';
        $chargeOrigin = 'true';
        $chargeDestination= 'true';
        $chargeFreight= 'true';
        $chargeAPI= 'true';
        $chargeAPI_M = 'false';
        $chargeAPI_SF = 'false';
        $form['equipment'] = array('20','40','40HC');
        $form['company_id_quote'] ='';

        return view('quotesv2/search',  compact('companies','carrierMan','hideO','hideD','countries','harbors','prices','company_user','currencies','currency_name','incoterm','airlines','chargeOrigin','chargeDestination','chargeFreight','chargeAPI','form','chargeAPI_M', 'chargeAPI_SF'));


    }




    /**
   * Return rates after process search
   * @param Request $request 
   * @return Illuminate\View\View
   */


    public function processSearch(Request $request){




        //Variables del usuario conectado



        $company_user_id=\Auth::user()->company_user_id;
        $user_id =  \Auth::id();

        //Variables para cargar el  Formulario
        $chargesOrigin = $request->input('chargeOrigin');
        $chargesDestination = $request->input('chargeDestination');
        $chargesFreight = $request->input('chargeFreight');
        $chargesAPI = $request->input('chargeAPI');
        $chargesAPI_M = $request->input('chargeAPI_M');
        $chargesAPI_SF = $request->input('chargeAPI_SF');


        $form  = $request->all();
        $incoterm = Incoterm::pluck('name','id');
        if(\Auth::user()->hasRole('subuser')){
            $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
                $q->where('user_id',\Auth::user()->id);
            })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
        }else{
            $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
        }
        $companies->prepend('Please an option','0');
        $airlines = Airline::all()->pluck('name','id');
        $harbors = Harbor::get()->pluck('display_name','id_complete');
        $countries = Country::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        $company_user = User::where('id',\Auth::id())->first();
        $carrierMan = Carrier::all()->pluck('name','id');

        if($company_user->companyUser) {
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
        $mode = $request->mode;
        // $incoterm_id = $request->input('incoterm_id');
        $address =$request->input('origin_address')." ".$request->input('destination_address'); 


        $this->storeSearchV2($origin_port,$destiny_port,$request->input('date'),$equipment,$delivery_type,$mode,$company_user_id,'FCL');

        // Fecha Contrato
        $dateRange =  $request->input('date');
        $dateRange = explode("/",$dateRange);
        $dateSince = $dateRange[0];
        $dateUntil = $dateRange[1];

        //Collection Equipment Dinamico
        $equipmentHides = $this->hideContainer($equipment,'');
        //Colecciones 
        $inlandDestiny = new collection();
        $inlandOrigin = new collection();

        //Markups Freight
        $freighPercentage = 0;
        $freighAmmount = 0;
        $freighMarkup= 0;
        // Markups Local
        $localPercentage = 0;
        $localAmmount = 0;
        $localMarkup = 0;
        $markupLocalCurre = 0;
        // Markups Local
        $inlandPercentage = 0;
        $inlandAmmount = 0;
        $inlandMarkup = 0;
        $markupInlandCurre = 0;
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

            // Local y global
            $fclLocal = $freight->local_markup->where('price_type_id','=',1);
            // markup currency

            if($request->mode == "1"){
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
        // Fin Markups

        // Calculo de los inlands
        $modality_inland = '1';// FALTA AGREGAR EXPORT
        $company_inland = $request->input('company_id_quote');
        $texto20 = 'Inland 20 x' .$request->input('twuenty'); 
        $texto40 = 'Inland 40 x' .$request->input('forty');
        $texto40hc = 'Inland 40HC x'. $request->input('fortyhc');
        // Destination Address
        $hideO = 'hide';
        $hideD = 'hide';
        if($delivery_type == "2" || $delivery_type == "4" ){ 

            $hideD = '';
            $inlands = Inland::whereHas('inland_company_restriction', function($a) use($company_inland){
                $a->where('company_id', '=',$company_inland);
            })->orDoesntHave('inland_company_restriction')->whereHas('inlandports', function($q) use($destiny_port) {
                $q->whereIn('port', $destiny_port);
            })->where('company_user_id','=',$company_user_id)->with('inlandadditionalkms','inlandports.ports','inlanddetails.currency');

            $inlands->where(function ($query) use($modality_inland)  {
                $query->where('type',$modality_inland)->orwhere('type','3');
            });


            $inlands = $inlands->get();
            $dataDest = array();
            // se agregan los aditional km
            foreach($inlands as $inlandsValue){
                $km20 = true;
                $km40 = true;
                $km40hc = true;
                $inlandDetails = array();


                foreach($inlandsValue->inlandports as $ports){
                    $monto = 0;

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
                                $distancia = str_replace ( ".", "", $km[0]);
                                $distancia = floatval($distancia);
                                if($distancia < 1){
                                    $distancia = 1;
                                }
                                foreach($inlandsValue->inlanddetails as $details){
                                    $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);
                                    if($details->type == 'twuenty' &&  in_array( '20',$equipment) ){
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $sub_20 = number_format( $details->ammount / $rateI, 2, '.', ''); 
                                            $monto += number_format($sub_20, 2, '.', ''); 
                                            $amount_inland = number_format($details->ammount, 2, '.', ''); 
                                            $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                            $km20 = false;
                                            // CALCULO MARKUPS 
                                            $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                                            // FIN CALCULO MARKUPS 
                                            $arrayInland20 = array("cant_cont" =>  '1' , "sub_in" => $sub_20 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'c20') ; 
                                            $arrayInland20 = array_merge($markupI20,$arrayInland20);
                                            $inlandDetails[] = $arrayInland20;
                                        }
                                    }
                                    if($details->type == 'forty' &&  in_array( '40',$equipment) ){

                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $sub_40 = number_format( $details->ammount / $rateI, 2, '.', ''); 
                                            $monto +=  number_format($sub_40, 2, '.', ''); 
                                            $amount_inland = $details->ammount;
                                            $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                            $km40 = false;
                                            // CALCULO MARKUPS 
                                            $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                                            // FIN CALCULO MARKUPS 
                                            $arrayInland40 = array("cant_cont" =>  '1' , "sub_in" => $sub_40 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'c40') ; 
                                            $arrayInland40 = array_merge($markupI40,$arrayInland40);
                                            $inlandDetails[] = $arrayInland40;
                                        }
                                    }
                                    if($details->type == 'fortyhc' &&   in_array( '40HC',$equipment) ){
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $sub_40hc =  number_format( $details->ammount / $rateI, 2, '.', ''); 
                                            $monto +=  number_format($sub_40hc, 2, '.', ''); 
                                            $price_per_unit = number_format($details->ammount / $distancia, 2, '.', '');
                                            $amount_inland =  number_format($details->ammount , 2, '.', ''); 
                                            $km40hc = false;

                                            // CALCULO MARKUPS 
                                            $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$markupInlandCurre);
                                            // FIN CALCULO MARKUPS 
                                            $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'c40hc' ) ;
                                            $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                                            $inlandDetails[] = $arrayInland40hc;
                                        }
                                    }

                                }
                                // KILOMETROS ADICIONALES 

                                if(isset($inlandsValue->inlandadditionalkms)){


                                    $rateGeneral = $this->ratesCurrency($inlandsValue->inlandadditionalkms->currency_id,$typeCurrency);
                                    if($km20 &&  in_array( '20',$equipment) ){
                                        $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_20) / $rateGeneral;
                                        $sub_20 =  number_format($montoKm, 2, '.', '');
                                        $monto += $sub_20;
                                        $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_20;
                                        $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                        $amount_inland = number_format($amount_inland, 2, '.', '');
                                        // CALCULO MARKUPS 
                                        $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                                        // FIN CALCULO MARKUPS 
                                        $sub_20 = number_format($sub_20, 2, '.', '');
                                        $arrayInland20 = array("cant_cont" =>'1' , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' =>$inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit , 'typeContent' => 'c20' ) ;

                                        $arrayInland20 = array_merge($markupI20,$arrayInland20);
                                        $inlandDetails[] = $arrayInland20;
                                    }
                                    if($km40 &&  in_array( '40',$equipment) ){
                                        $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40) / $rateGeneral;

                                        $sub_40 = number_format($montoKm, 2, '.', '');
                                        $monto += $sub_40;
                                        $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40 ;
                                        $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                        $amount_inland = number_format($amount_inland, 2, '.', '');
                                        // CALCULO MARKUPS 
                                        $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                                        // FIN CALCULO MARKUPS
                                        $sub_40 = number_format($sub_40, 2, '.', '');
                                        $arrayInland40 = array("cant_cont" => '1', "sub_in" => $sub_40, "des_in" =>  $texto40,'amount' => $amount_inland ,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode , 'price_unit' => $price_per_unit, 'typeContent' => 'c40' ) ;
                                        $arrayInland40 = array_merge($markupI40,$arrayInland40);
                                        $inlandDetails[] = $arrayInland40;

                                    }
                                    if($km40hc &&  in_array( '40HC',$equipment)){
                                        $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) / $rateGeneral;
                                        $sub_40hc = number_format($montoKm, 2, '.', '');
                                        $monto += $sub_40hc;

                                        $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40hc;
                                        $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                        $amount_inland = number_format($amount_inland, 2, '.', '');
                                        // CALCULO MARKUPS 
                                        $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$markupInlandCurre);
                                        // FIN CALCULO MARKUPS
                                        $sub_40hc = number_format($sub_40hc, 2, '.', '');
                                        $arrayInland40hc = array("cant_cont" =>'1' , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland ,'currency' => $typeCurrency , 'price_unit' => $price_per_unit , 'typeContent' => 'c40hc') ;
                                        $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                                        $inlandDetails[] = $arrayInland40hc;
                                    }

                                }

                                $monto = number_format($monto, 2, '.', '');
                                if($monto > 0){
                                    $inlandDetails = Collection::make($inlandDetails);
                                    $arregloInland =  array("prov_id" => $inlandsValue->id ,"provider" => "Inland Haulage","providerName" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name,'port_id'=> $ports->ports->id ,'validity_start'=>$inlandsValue->validity,'validity_end'=>$inlandsValue->expire ,"km" => $distancia, "monto" => $monto ,'type' => 'Destination','type_currency' => $inlandsValue->inlandadditionalkms->currency->alphacode ,'idCurrency' => $inlandsValue->currency_id );
                                    $arregloInland['inlandDetails'] = $inlandDetails->groupBy('typeContent')->map(function($item){
                                        $minimoD = $item->where('sub_in', '>' ,0);
                                        $minimoDetails = $minimoD->where('sub_in', $minimoD->min('sub_in'))->first();
                                        return $minimoDetails;
                                    });

                                    $dataDest[] =$arregloInland;
                                }
                            }
                        }
                    } // if ports
                }// foreach ports
            }//foreach inlands
            if(!empty($dataDest)){
                $inlandDestiny = Collection::make($dataDest);
                //dd($collection); //  completo
                /* $inlandDestiny = $collection->groupBy('port_id')->map(function($item){
          $test = $item->where('monto', $item->min('monto'))->first();
          return $test;
        });*/
                // filtraor por el minimo
            }

        }
        // Origin Addrees

        if($delivery_type == "3" || $delivery_type == "4" ){
            $hideO = '';
            $inlands = Inland::whereHas('inland_company_restriction', function($a) use($company_inland){
                $a->where('company_id', '=',$company_inland);
            })->orDoesntHave('inland_company_restriction')->whereHas('inlandports', function($q) use($origin_port) {
                $q->whereIn('port', $origin_port);
            })->where('company_user_id','=',$company_user_id)->with('inlandadditionalkms','inlandports.ports','inlanddetails.currency');

            $inlands->where(function ($query) use($modality_inland) {
                $query->where('type',$modality_inland)->orwhere('type','3');
            });

            $inlands = $inlands->get();

            $dataOrig = array();
            foreach($inlands as $inlandsValue){
                $km20 = true;
                $km40 = true;
                $km40hc = true;
                $inlandDetailsOrig = array();
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
                                $distancia = str_replace ( ".", "", $km[0]);
                                $distancia = floatval($distancia);

                                if($distancia < 1){
                                    $distancia = 1;
                                }

                                foreach($inlandsValue->inlanddetails as $details){
                                    $rateI = $this->ratesCurrency($details->currency->id,$typeCurrency);
                                    if($details->type == 'twuenty' &&  in_array( '20',$equipment) ){
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $sub_20 =  number_format( $details->ammount / $rateI, 2, '.', ''); 
                                            $monto += $sub_20;
                                            $amount_inland = $details->ammount;
                                            $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                            $km20 = false;
                                            // CALCULO MARKUPS 
                                            $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                                            // FIN CALCULO MARKUPS 
                                            $arrayInland20 = array("cant_cont" =>  '1' , "sub_in" => $sub_20 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'c20') ; 
                                            $arrayInland20 = array_merge($markupI20,$arrayInland20);
                                            $inlandDetailsOrig[] = $arrayInland20;
                                        }
                                    }
                                    if($details->type == 'forty' &&  in_array( '40',$equipment) ){

                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $sub_40 =  number_format( $details->ammount / $rateI, 2, '.', ''); 
                                            $monto += $sub_40;
                                            $amount_inland = $details->ammount;
                                            $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                            $km40 = false;
                                            // CALCULO MARKUPS 
                                            $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                                            // FIN CALCULO MARKUPS 
                                            $arrayInland40 = array("cant_cont" =>  '1' , "sub_in" => $sub_40 ,'amount' => $amount_inland ,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'c40') ; 
                                            $arrayInland40 = array_merge($markupI40,$arrayInland40);
                                            $inlandDetailsOrig[] = $arrayInland40;
                                        }
                                    }
                                    if($details->type == 'fortyhc' &&   in_array( '40HC',$equipment) ){
                                        if( $distancia >= $details->lower && $distancia  <= $details->upper){
                                            $sub_40hc =   number_format( $details->ammount / $rateI, 2, '.', ''); 
                                            $monto += $sub_40hc;
                                            $price_per_unit = number_format($details->ammount / $distancia, 2, '.', '');
                                            $amount_inland =  $details->ammount;
                                            $km40hc = false;
                                            // CALCULO MARKUPS 
                                            $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$markupInlandCurre);
                                            // FIN CALCULO MARKUPS 
                                            $arrayInland40hc = array("cant_cont" => $request->input('fortyhc') , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland,'currency' => $details->currency->alphacode , 'price_unit' => $price_per_unit , 'typeContent' => 'c40hc' ) ;
                                            $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                                            $inlandDetailsOrig[] = $arrayInland40hc;
                                        }
                                    }

                                }
                                // KILOMETROS ADICIONALES 

                                if(isset($inlandsValue->inlandadditionalkms)){

                                    $rateGeneral = $this->ratesCurrency($inlandsValue->inlandadditionalkms->currency_id,$typeCurrency);
                                    if($km20 &&  in_array( '20',$equipment) ){
                                        $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_20) / $rateGeneral;

                                        $sub_20 = $montoKm;
                                        $monto += $sub_20;
                                        $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_20;
                                        $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                        $amount_inland = number_format($amount_inland, 2, '.', '');
                                        // CALCULO MARKUPS 
                                        $markupI20=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_20,$typeCurrency,$markupInlandCurre);
                                        // FIN CALCULO MARKUPS 

                                        $sub_20 = number_format($sub_20, 2, '.', '');
                                        $arrayInland20 = array("cant_cont" =>'1' , "sub_in" => $sub_20, "des_in" => $texto20 ,'amount' => $amount_inland ,'currency' =>$inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit , 'typeContent' => 'c20' ) ;

                                        $arrayInland20 = array_merge($markupI20,$arrayInland20);
                                        $inlandDetailsOrig[] = $arrayInland20;
                                    }
                                    if($km40 &&  in_array( '40',$equipment) ){
                                        $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40) / $rateGeneral;
                                        $sub_40 = $montoKm;
                                        $monto += $sub_40;
                                        $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40 ;
                                        $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                        $amount_inland = number_format($amount_inland, 2, '.', '');
                                        // CALCULO MARKUPS 
                                        $markupI40=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40,$typeCurrency,$markupInlandCurre);
                                        // FIN CALCULO MARKUPS
                                        $sub_40 = number_format($sub_40, 2, '.', '');
                                        $arrayInland40 = array("cant_cont" => '1', "sub_in" => $sub_40, "des_in" =>  $texto40,'amount' => $amount_inland ,'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode , 'price_unit' => $price_per_unit, 'typeContent' => 'c40' ) ;
                                        $arrayInland40 = array_merge($markupI40,$arrayInland40);
                                        $inlandDetailsOrig[] = $arrayInland40;
                                    }
                                    if($km40hc &&  in_array( '40HC',$equipment)){
                                        $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->km_40hc) / $rateGeneral;
                                        $sub_40hc = $montoKm;
                                        $monto += $sub_40hc;

                                        $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->km_40hc;
                                        $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                        $amount_inland = number_format($amount_inland, 2, '.', '');
                                        // CALCULO MARKUPS 
                                        $markupI40hc=$this->inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$sub_40hc,$typeCurrency,$markupInlandCurre);
                                        // FIN CALCULO MARKUPS
                                        $sub_40hc = number_format($sub_40hc, 2, '.', '');
                                        $arrayInland40hc = array("cant_cont" =>'1' , "sub_in" => $sub_40hc, "des_in" => $texto40hc,'amount' => $amount_inland ,'currency' => $typeCurrency , 'price_unit' => $price_per_unit , 'typeContent' => 'c40hc') ;
                                        $arrayInland40hc = array_merge($markupI40hc,$arrayInland40hc);
                                        $inlandDetailsOrig[] = $arrayInland40hc;
                                    }

                                }

                                $monto = number_format($monto, 2, '.', '');
                                if($monto > 0){
                                    $inlandDetailsOrig = Collection::make($inlandDetailsOrig);


                                    $arregloInlandOrig = array("prov_id" => $inlandsValue->id ,"provider" => "Inland Haulage","providerName" => $inlandsValue->provider ,"port_id" => $ports->ports->id,"port_name" =>  $ports->ports->name ,'validity_start'=>$inlandsValue->validity,'validity_end'=>$inlandsValue->expire ,"km" => $distancia , "monto" => $monto ,'type' => 'Origin','type_currency' => $typeCurrency ,'idCurrency' => $inlandsValue->currency_id  );


                                    $arregloInlandOrig['inlandDetails'] = $inlandDetailsOrig->groupBy('typeContent')->map(function($item){
                                        $minimoD = $item->where('sub_in', '>' ,0);

                                        $minimoDetails = $minimoD->where('sub_in', $minimoD->min('sub_in'))->first();

                                        return $minimoDetails;
                                    });
                                    $dataOrig[] = $arregloInlandOrig;
                                }
                            }//antes de esto 
                        }
                    } // if ports
                }// foreach ports
            }//foreach inlands

            if(!empty($dataOrig)){
                $inlandOrigin = Collection::make($dataOrig);

                //dd($collectionOrig); //  completo
                /*$inlandOrigin= $collectionOrig->groupBy('port_id')->map(function($item){
          $test = $item->where('monto', $item->min('monto'))->first();
          return $test;
        });*/
                //dd($inlandOrigin); // filtraor por el minimo
            }
        }// Fin del calculo de los inlands




        // Consulta base de datos rates

        if($company_id != null || $company_id != 0 ){
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
        }else{
            $arreglo = Rate::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q){
                $q->doesnthave('contract_user_restriction');
            })->whereHas('contract', function($q){
                $q->doesnthave('contract_company_restriction');
            })->whereHas('contract', function($q) use($dateSince,$dateUntil,$company_user_id){
                $q->where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->where('company_user_id','=',$company_user_id);
            });



        }

        // ************************* CONSULTA RATE API ****************************** 



        if($chargesAPI != null){

            $client = new Client();

            foreach($origin_port as $orig){
                foreach($destiny_port as $dest){

                    $url = env('CMA_API_URL', 'http://cfive-api.eu-central-1.elasticbeanstalk.com/rates/api/{code}/{orig}/{dest}/{date}');
                    $url = str_replace(['{code}', '{orig}', '{dest}', '{date}'], ['cmacgm', $orig, $dest, trim($dateUntil)], $url);
                    $response = $client->request('GET', $url);

                    //$response = $client->request('GET','http://cfive-api.eu-central-1.elasticbeanstalk.com/rates/HARIndex/'.$orig.'/'.$dest.'/'.trim($dateUntil));
                    //  $response = $client->request('GET','http://cmacgm/rates/HARIndex/'.$orig.'/'.$dest.'/'.trim($dateUntil));
                }
            }
            $arreglo2 = RateApi::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($dateSince,$dateUntil,$company_user_id){
                $q->where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->where('number','CMA CGM');
            });
        }

        if($chargesAPI_M != null){

            $client = new Client();


            foreach($origin_port as $orig){
                foreach($destiny_port as $dest){

                    $url = env('MAERSK_API_URL', 'http://maersk-info.eu-central-1.elasticbeanstalk.com/rates/api/{code}/{orig}/{dest}/{date}');
                    $url = str_replace(['{code}', '{orig}', '{dest}', '{date}'], ['maersk', $orig, $dest, trim($dateUntil)], $url);

                    try {
                        $response = $client->request('GET', $url);
                    } catch (\Exception $e) {

                    }  

                }
            }

            $arreglo3 = RateApi::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($dateSince,$dateUntil,$company_user_id){
                $q->where('validity', '>=',$dateSince)->where('number','MAERSK');
            });
        }

        if($chargesAPI_SF != null){

            $client = new Client();
            foreach($origin_port as $orig){
                foreach($destiny_port as $dest){

                    $url = env('SAFMARINE_API_URL', 'http://maersk-info.eu-central-1.elasticbeanstalk.com/rates/api/{code}/{orig}/{dest}/{date}');
                    $url = str_replace(['{code}', '{orig}', '{dest}', '{date}'], ['safmarine', $orig, $dest, trim($dateUntil)], $url);

                    try {
                        $response = $client->request('GET', $url);
                    } catch (\Exception $e) {

                    }  

                }
            }

            $arreglo4 = RateApi::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($dateSince,$dateUntil,$company_user_id){
                $q->where('validity', '>=',$dateSince)->where('number','SAFMARINE');
            });
        }


        //ACA


        // Se agregan las condiciones para evitar traer rates con ceros dependiendo de lo seleccionado por el usuario

        /*if(in_array('20',$equipment)){
      $arreglo->where('twuenty' , '!=' , "0");
    }
    if(in_array('40',$equipment)){
      $arreglo->where('forty' , '!=' , "0");
    }
    if(in_array('40HC',$equipment)){
      $arreglo->where('fortyhc' , '!=' , "0");
    }
    if(in_array('40NOR',$equipment)){
      $arreglo->where('fortynor' , '!=' , "0");
    }
    if(in_array('45',$equipment)){
      $arreglo->where('fortyfive' , '!=' , "0"); 
    }*/


        $arreglo = $arreglo->get();

        if($chargesAPI != null){
            $arreglo2 = $arreglo2->get();
            $arreglo = $arreglo->merge($arreglo2);
        }

        if($chargesAPI_M != null){
            $arreglo3 = $arreglo3->get();

            $arreglo = $arreglo->merge($arreglo3);
        }

        if($chargesAPI_SF != null){
            $arreglo4 = $arreglo4->get();

            $arreglo = $arreglo->merge($arreglo4);
        }




        //  dd($arreglo);



        $formulario = $request;
        $array20 = array('2','4','5','6','9','10','11'); // id  calculation type 2 = per 20 , 4= per teu , 5 per container
        $array40 =  array('1','4','5','6','9','10','11'); // id  calculation type 2 = per 40 
        $array40Hc= array('3','4','5','6','9','10','11'); // id  calculation type 3 = per 40HC 
        $array40Nor = array('7','4','5','6','9','10','11');  // id  calculation type 7 = per 40NOR
        $array45 = array('8','4','5','6','9','10','11');  // id  calculation type 8 = per 45

        $arrayContainers =  array('1','2','3','4','7','8'); 


        foreach($arreglo as $data){
            $contractStatus = $data->contract->status;
            $collectionRate = new Collection();
            $totalFreight = 0;
            $totalRates = 0;
            $totalT20 = 0;
            $totalT40 = 0;
            $totalT40hc = 0;
            $totalT40nor = 0;
            $totalT45 = 0;
            $totalT  = 0 ;
            //Variables Totalizadoras 
            $totales = array();

            $tot_20_F = 0;
            $tot_40_F = 0;
            $tot_40hc_F = 0;
            $tot_40nor_F = 0;
            $tot_45_F = 0;

            $tot_20_O = 0;
            $tot_40_O = 0;
            $tot_40hc_O = 0;
            $tot_40nor_O = 0;
            $tot_45_O = 0;

            $tot_20_D = 0;
            $tot_40_D = 0;
            $tot_40hc_D = 0;
            $tot_40nor_D = 0;
            $tot_45_D = 0;

            $carrier[] = $data->carrier_id;
            $orig_port = array($data->origin_port);
            $dest_port = array($data->destiny_port);
            $rateDetail = new collection();
            $collectionOrigin = new collection();
            $collectionDestiny = new collection();
            $collectionFreight = new collection();


            $arregloRate =  array();
            //Arreglos para guardar el rate

            $arregloRateSave['rate'] = array();
            $arregloRateSave['markups'] = array();

            //Arreglo para guardar charges
            $arregloCharges['origin'] =  array();

            $arregloOrigin =  array();
            $arregloFreight =  array();
            $arregloDestiny =  array();
            // globales
            $arregloOriginG =  array();
            $arregloFreightG =  array();
            $arregloDestinyG =  array();

            $rateC = $this->ratesCurrency($data->currency->id,$typeCurrency);

            // Rates 
            foreach($equipment as $containers){
                //Calculo para los diferentes tipos de contenedores
                if($containers == '20'){
                    $markup20 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->twuenty,$typeCurrency,$containers);

                    // dd($markup20);

                    $array20Detail = array('price20' => $data->twuenty, 'currency20' => $data->currency->alphacode ,'idCurrency20' => $data->currency_id);
                    $tot_20_F += $markup20['monto20'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_20_save = array('c20' => $data->twuenty);
                    $arregloRateSave['rate']  = array_merge($array_20_save,$arregloRateSave['rate']);
                    // Markups
                    $array_20_markup =  array('m20' => $markup20['markup20']);
                    $arregloRateSave['markups']  = array_merge($array_20_markup,$arregloRateSave['markups']);

                    $array20T = array_merge($array20Detail,$markup20);
                    $arregloRate = array_merge($array20T,$arregloRate);

                    //Total 
                    $totales['20F'] =  $tot_20_F;

                }
                if($containers == '40'){
                    $markup40 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->forty,$typeCurrency,$containers);
                    $array40Detail = array('price40' => $data->forty, 'currency40' => $data->currency->alphacode ,'idCurrency40' => $data->currency_id);
                    $tot_40_F += $markup40['monto40']  / $rateC;
                    // Arreglos para guardar los rates
                    $array_40_save = array('c40' => $data->forty);
                    $arregloRateSave['rate']  = array_merge($array_40_save,$arregloRateSave['rate']);
                    // Markups
                    $array_40_markup =  array('m40' => $markup40['markup40']);
                    $arregloRateSave['markups']  = array_merge($array_40_markup,$arregloRateSave['markups']);

                    $array40T = array_merge($array40Detail,$markup40);
                    $arregloRate = array_merge($array40T,$arregloRate); 
                    $totales['40F'] = $tot_40_F;

                }
                if($containers == '40HC'){
                    $markup40hc = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortyhc,$typeCurrency,$containers);
                    $array40hcDetail = array('price40hc' => $data->fortyhc, 'currency40hc' => $data->currency->alphacode ,'idCurrency40hc' => $data->currency_id);
                    $tot_40hc_F += $markup40hc['monto40HC'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_40hc_save = array('c40hc' => $data->fortyhc);
                    $arregloRateSave['rate']  = array_merge($array_40hc_save,$arregloRateSave['rate']);
                    // Markups
                    $array_40hc_markup =  array('m40hc' => $markup40hc['markup40HC']);
                    $arregloRateSave['markups']  = array_merge($array_40hc_markup,$arregloRateSave['markups']);

                    $array40hcT = array_merge($array40hcDetail,$markup40hc);
                    $arregloRate = array_merge($array40hcT,$arregloRate); 
                    $totales['40hcF'] = $tot_40hc_F;

                }
                if($containers == '40NOR'){
                    $markup40nor = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortynor,$typeCurrency,$containers);
                    $array40norDetail = array('price40nor' => $data->fortynor, 'currency40nor' => $data->currency->alphacode ,'idCurrency40nor' => $data->currency_id);
                    $tot_40nor_F += $markup40nor['monto40NOR'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_40nor_save = array('c40nor' => $data->fortynor);
                    $arregloRateSave['rate']  = array_merge($array_40nor_save,$arregloRateSave['rate']);
                    // Markups
                    $array_40nor_markup =  array('m40nor' => $markup40nor['markup40NOR']);
                    $arregloRateSave['markups']  =array_merge($array_40nor_markup,$arregloRateSave['markups']);

                    $array40norT = array_merge($array40norDetail,$markup40nor);
                    $arregloRate = array_merge($array40norT,$arregloRate); 
                    $totales['40norF'] = $tot_40nor_F;

                }
                if($containers == '45'){
                    $markup45 = $this->freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$data->fortyfive,$typeCurrency,$containers);
                    $array45Detail = array('price45' => $data->fortyfive, 'currency45' => $data->currency->alphacode ,'idCurrency45' => $data->currency_id);
                    $tot_45_F += $markup45['monto45'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_45_save = array('c45' => $data->fortyfive);
                    $arregloRateSave['rate'] = array_merge($array_45_save,$arregloRateSave['rate']);
                    // Markups
                    $array_45_markup =  array('m45' => $markup45['markup45']);
                    $arregloRateSave['markups']  = array_merge($array_45_markup,$arregloRateSave['markups']);

                    $array45T = array_merge($array45Detail,$markup45);
                    $arregloRate = array_merge($array45T,$arregloRate); 
                    $totales['45F'] = $tot_45_F;

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
            if($contractStatus != 'api'){ 
                $localChar = LocalCharge::where('contract_id','=',$data->contract_id)->whereHas('localcharcarriers', function($q) use($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
                    $query->whereHas('localcharports', function($q) use($orig_port,$dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
                    })->orwhereHas('localcharcountries', function($q) use($origin_country,$destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    });
                })->with('localcharports.portOrig','localcharcarriers.carrier','currency','surcharge.saleterm')->orderBy('typedestiny_id','calculationtype_id','surchage_id')->get();
            }else{

                $localChar = LocalChargeApi::where('contract_id','=',$data->contract_id)->whereHas('localcharcarriers', function($q) use($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
                    $query->whereHas('localcharports', function($q) use($orig_port,$dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest',$dest_port);
                    })->orwhereHas('localcharcountries', function($q) use($origin_country,$destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    });
                })->with('localcharports.portOrig','localcharcarriers.carrier','currency','surcharge.saleterm')->orderBy('typedestiny_id','calculationtype_id','surchage_id')->get();

            }

            foreach($localChar as $local){

                $rateMount = $this->ratesCurrency($local->currency->id,$typeCurrency);

                // Condicion para enviar los terminos de venta o compra
                if(isset($local->surcharge->saleterm->name)){
                    $terminos = $local->surcharge->saleterm->name;
                }else{
                    $terminos = $local->surcharge->name;
                }

                foreach($local->localcharcarriers as $localCarrier){
                    if($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id ==  $carrier_all ){
                        //Origin
                        if($chargesOrigin != null){
                            if($local->typedestiny_id == '1'){

                                if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment) ){

                                    $montoOrig = $local->ammount;
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup20 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id ,'montoOrig' => $montoOrig,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );

                                    $arregloOrigin = array_merge($arregloOrigin,$markup20);
                                    $collectionOrigin->push($arregloOrigin);      
                                    $tot_20_O  +=  $markup20['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40) && in_array( '40',$equipment) ){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);

                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40 =$this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40','rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                                    $arregloOrigin = array_merge($arregloOrigin,$markup40);
                                    $collectionOrigin->push($arregloOrigin);

                                    $tot_40_O  +=  $markup40['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);

                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40hc =$this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                                    $arregloOrigin = array_merge($arregloOrigin,$markup40hc);
                                    $collectionOrigin->push($arregloOrigin);
                                    $tot_40hc_O  +=   $markup40hc['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40nor = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                                    $arregloOrigin = array_merge($arregloOrigin,$markup40nor);
                                    $collectionOrigin->push($arregloOrigin);
                                    $tot_40nor_O  +=  $markup40nor['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment)){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup45 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency );
                                    $arregloOrigin = array_merge($arregloOrigin,$markup45);
                                    $collectionOrigin->push($arregloOrigin);
                                    $tot_45_O  +=  $markup45['montoMarkup'];

                                }

                                if(in_array($local->calculationtype_id,$arrayContainers)){
                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'montoMarkupO' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99','rate_id' => $data->id  ,'calculation_id'=> '5', 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,'markupConvert' => 0.00 );

                                }else{
                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'montoMarkupO' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency ,'markupConvert' => 0.00);

                                }
                                $collectionOrigin->push($arregloOrigin);

                            }  
                        }

                        //Destiny
                        if($chargesDestination != null){
                            if($local->typedestiny_id == '2'){

                                if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment)  ){

                                    $montoOrig = $local->ammount;
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup20 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                                    $arregloDestiny = array_merge($arregloDestiny,$markup20);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_20_D +=  $markup20['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40)&& in_array( '40',$equipment)){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency  );
                                    $arregloDestiny = array_merge($arregloDestiny,$markup40);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_40_D  +=  $markup40['montoMarkup'];
                                }
                                if(in_array($local->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40hc = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloDestiny = array_merge($arregloDestiny,$markup40hc);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_40hc_D  +=   $markup40hc['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40nor = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloDestiny = array_merge($arregloDestiny,$markup40nor);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_40nor_D  +=  $markup40nor['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment)){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup45 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloDestiny = array_merge($arregloDestiny,$markup45);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_45_D  +=  $markup45['montoMarkup'];
                                    $montoOrig = $local->ammount;
                                }

                                if(in_array($local->calculationtype_id,$arrayContainers)){
                                    $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> '5', 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00
                                                            ,'markupConvert' => 0.00 );
                                }else{
                                    $arregloDestiny = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99','rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00);
                                }
                                $collectionDestiny->push($arregloDestiny);
                            }
                        }
                        //Freight
                        if($chargesFreight != null){
                            if($local->typedestiny_id == '3'){

                                if(in_array($local->calculationtype_id, $array20) && in_array( '20',$equipment) ){

                                    $montoOrig = $local->ammount;

                                    $monto =   $local->ammount  / $rateMount ;

                                    $monto = number_format($monto, 2, '.', '');
                                    $markup20 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'20','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloFreight = array_merge($arregloFreight,$markup20);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['20F'] += $markup20['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40) && in_array( '40',$equipment) ){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);

                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloFreight = array_merge($arregloFreight,$markup40);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['40F'] +=  $markup40['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40Hc) && in_array( '40HC',$equipment) ){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40hc = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40hc' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloFreight = array_merge($arregloFreight,$markup40hc);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['40hcF'] +=   $markup40hc['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array40Nor)  && in_array( '40NOR',$equipment) ){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto = $local->ammount / $rateMount;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40nor = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id ,'montoOrig' => $montoOrig ,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloFreight = array_merge($arregloFreight,$markup40nor);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['40norF'] += $markup40nor['montoMarkup'];

                                }
                                if(in_array($local->calculationtype_id, $array45) && in_array( '45',$equipment) ){

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig,$local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount ;
                                    $monto = $this->perTeu($monto,$local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup45 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'45','rate_id' => $data->id ,'montoOrig' => $montoOrig,'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency );
                                    $arregloFreight = array_merge($arregloFreight,$markup45);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['45F'] +=  $markup45['montoMarkup'];

                                }

                                if(in_array($local->calculationtype_id,$arrayContainers)){
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> '5' , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00 );
                                }else{

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $localCarrier->carrier_id,'type'=>'99'  ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtype->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $local->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00 ) ;
                                }

                                $collectionFreight->push($arregloFreight);

                            }
                        }

                    }
                }

            }
            // ################## Fin local Charges        #############################

            //################## Calculos Global Charges #################################

            if($contractStatus != 'api'){ 



                $globalChar = GlobalCharge::where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrier', function($q) use($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->where(function ($query) use($orig_port,$dest_port,$origin_country,$destiny_country){
                    $query->orwhereHas('globalcharport', function($q) use($orig_port,$dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                    })->orwhereHas('globalcharcountry', function($q) use($origin_country,$destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    })->orwhereHas('globalcharportcountry', function($q) use($orig_port,$destiny_country) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('country_dest', $destiny_country);
                    })->orwhereHas('globalcharcountryport', function($q) use($origin_country,$dest_port) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('port_dest', $dest_port);
                    });
                })->where('company_user_id','=',$company_user_id)->with('globalcharcarrier.carrier','currency','surcharge.saleterm')->get();





                foreach($globalChar as $global){

                    $rateMount = $this->ratesCurrency($global->currency->id,$typeCurrency);

                    // Condicion para enviar los terminos de venta o compra
                    if(isset($global->surcharge->saleterm->name)){
                        $terminos = $global->surcharge->saleterm->name;
                    }else{
                        $terminos = $global->surcharge->name;
                    }

                    foreach($global->globalcharcarrier as $globalCarrier){
                        if($globalCarrier->carrier_id == $data->carrier_id || $globalCarrier->carrier_id ==  $carrier_all ){
                            //Origin
                            if($chargesOrigin != null){
                                if($global->typedestiny_id == '1'){

                                    if(in_array($global->calculationtype_id, $array20) && in_array('20',$equipment) ){

                                        $montoOrig = $global->ammount ;
                                        $monto =   $global->ammount  / $rateMount ;

                                        $monto = number_format($monto, 2, '.', '');
                                        $markup20 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency  );
                                        $arregloOriginG = array_merge($arregloOriginG,$markup20);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_20_O  +=  $markup20['montoMarkup'];

                                    }
                                    if(in_array($global->calculationtype_id, $array40)&& in_array( '40',$equipment)){

                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40' ,'rate_id' => $data->id ,'montoOrig' => $montoOrig ,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloOriginG = array_merge($arregloOriginG,$markup40);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_40_O  +=   $markup40['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment)){

                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40hc = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40hc','rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloOriginG = array_merge($arregloOriginG,$markup40hc);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_40hc_O  +=   $markup40hc['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment)){

                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40nor = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id  ,'montoOrig' => $montoOrig ,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloOriginG = array_merge($arregloOriginG,$markup40nor);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_40nor_O  +=  $markup40nor['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array45)&& in_array( '45',$equipment)){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup45 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'45','rate_id' => $data->id   ,'montoOrig' => $montoOrig ,  'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloOriginG = array_merge($arregloOriginG,$markup45);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_45_O  +=  $markup45['montoMarkup'];
                                    }

                                    if(in_array($global->calculationtype_id,$arrayContainers)){
                                        $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'montoMarkupO' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> '5' ,'montoOrig' => 0.00,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00);

                                    }else{
                                        $arregloOriginG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'montoMarkupO' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtype->id ,'montoOrig' => 0.00,  'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00);


                                    }
                                    $collectionOrigin->push($arregloOriginG);
                                }
                            }
                            //Destiny
                            if($chargesDestination != null){
                                if($global->typedestiny_id == '2'){

                                    if(in_array($global->calculationtype_id, $array20) &&  in_array('20',$equipment)){
                                        $montoOrig = $global->ammount ;
                                        $monto =   $global->ammount  / $rateMount ;

                                        $monto = number_format($monto, 2, '.', '');
                                        $markup20 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloDestinyG = array_merge($arregloDestinyG,$markup20);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_20_D +=  $markup20['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array40)&& in_array( '40',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloDestinyG = array_merge($arregloDestinyG,$markup40);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_40_D  +=  $markup40['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array40Hc)&& in_array( '40HC',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40hc = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40hc' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloDestinyG = array_merge($arregloDestinyG,$markup40hc);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_40hc_D  +=   $markup40hc['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array40Nor)&& in_array( '40NOR',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40nor = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40nor' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloDestinyG = array_merge($arregloDestinyG,$markup40nor);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_40nor_D  +=  $markup40nor['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array45)&& in_array( '45',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup45 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'45' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloDestinyG = array_merge($arregloDestinyG,$markup45);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_45_D  +=  $markup45['montoMarkup'];
                                    }


                                    if(in_array($global->calculationtype_id,$arrayContainers)){
                                        $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99','rate_id' => $data->id ,'calculation_id'=> '5', 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00);
                                    }else{
                                        $arregloDestinyG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtype->id, 'montoOrig' => 0.00,  'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00);
                                    }

                                    $collectionDestiny->push($arregloDestinyG);
                                }
                            }
                            //Freight
                            if($chargesFreight != null){
                                if($global->typedestiny_id == '3'){

                                    if(in_array($global->calculationtype_id, $array20) && in_array('20',$equipment)){
                                        $montoOrig = $global->ammount ;

                                        $monto =   $global->ammount  / $rateMount ;

                                        $monto = number_format($monto, 2, '.', '');
                                        $markup20 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'20' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloFreightG = array_merge($arregloFreightG,$markup20);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['20F'] += $markup20['montoMarkup'];

                                    }
                                    if(in_array($global->calculationtype_id, $array40) && in_array( '40',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;

                                        $monto = $this->perTeu($monto,$global->calculationtype_id);

                                        $monto = number_format($monto, 2, '.', '');

                                        $markup40 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);

                                        $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40','rate_id' => $data->id   , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloFreightG = array_merge($arregloFreightG,$markup40);

                                        $collectionFreight->push($arregloFreightG);
                                        $totales['40F'] +=  $markup40['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array40Hc) && in_array( '40HC',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40hc = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40hc','rate_id' => $data->id   , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloFreightG = array_merge($arregloFreightG,$markup40hc);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['40hcF'] +=   $markup40hc['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array40Nor) && in_array( '40NOR',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40nor = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'40nor','rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency  ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloFreightG = array_merge($arregloFreightG,$markup40nor);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['40norF'] += $markup40nor['montoMarkup'];
                                    }
                                    if(in_array($global->calculationtype_id, $array45) && in_array( '45',$equipment) ){
                                        $montoOrig = $global->ammount ;
                                        $montoOrig = $this->perTeu($montoOrig,$global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount ;
                                        $monto = $this->perTeu($monto,$global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup45 = $this->localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'45' ,'rate_id' => $data->id  , 'montoOrig' => $montoOrig , 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency );
                                        $arregloFreightG = array_merge($arregloFreightG,$markup45);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['45F'] +=  $markup45['montoMarkup'];
                                    }

                                    if(in_array($global->calculationtype_id,$arrayContainers)){

                                        $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container','contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99','rate_id' => $data->id ,'calculation_id'=> '5' , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00);
                                    }else{

                                        $arregloFreightG = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' =>  $global->calculationtype->name,'contract_id' => $data->contract_id,'carrier_id' => $globalCarrier->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtype->id ,'montoOrig' => 0.00,  'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id  ,'currency_orig_id' => $idCurrency ,'montoMarkupO' => 0.00,'markupConvert' => 0.00 );

                                    }

                                    $collectionFreight->push($arregloFreightG);

                                }
                            }

                        }
                    }
                }

            }// fin if contract Api
            // ############################ Fin global charges ######################


            // Ordenar las colecciones
            if(!empty($collectionFreight))
                $collectionFreight = $this->OrdenarCollection($collectionFreight);
            if(!empty($collectionDestiny))
                $collectionDestiny = $this->OrdenarCollection($collectionDestiny);
            if(!empty($collectionOrigin))
                $collectionOrigin = $this->OrdenarCollection($collectionOrigin);



            // Totales Freight 
            if(!isset($totales['20F']))
                $totales['20F'] = 0;
            if(!isset($totales['40F']))
                $totales['40F'] = 0;
            if(!isset($totales['40hcF']))
                $totales['40hcF'] = 0;
            if(!isset($totales['40norF']))
                $totales['40norF'] = 0;
            if(!isset($totales['45F']))
                $totales['45F'] = 0;



            $totalT20 = $tot_20_D +  $totales['20F'] + $tot_20_O ;
            $totalT40  = $tot_40_D + $totales['40F'] + $tot_40_O ;
            $totalT40hc  = $tot_40hc_D + $totales['40hcF'] + $tot_40hc_O ;
            $totalT40nor  = $tot_40nor_D +  $totales['40norF'] + $tot_40nor_O ;
            $totalT45  = $tot_45_D + $totales['45F'] + $tot_45_O ;


            $totalRates += $totalT;
            $array = array('type'=>'Ocean Freight','detail'=>'Per Container','subtotal'=>$totalRates, 'total' =>$totalRates." ". $typeCurrency , 'idCurrency' => $data->currency_id,'currency_rate' => $data->currency->alphacode,'rate_id' => $data->id );
            $array = array_merge($array,$arregloRate);
            $array =  array_merge($array,$arregloRateSave);
            $collectionRate->push($array);

            // SCHEDULE TYPE 
            if($data->schedule_type_id != null){
                $sheduleType = ScheduleType::find($data->schedule_type_id);
                $data->setAttribute('sheduleType',$sheduleType->name);
            }else{
                $data->setAttribute('sheduleType',null);
            }
            //remarks
            $typeMode = $request->input('mode');


            $remarks="";
            if($data->contract->remarks != "")
                $remarks = $data->contract->remarks."<br>";



            $remarksGeneral = "";
            $remarksGeneral .= $this->remarksCondition($data->port_origin,$data->port_destiny,$data->carrier,$typeMode);


            $data->setAttribute('remarks',$remarks);
            $data->setAttribute('remarksG',$remarksGeneral);

            // EXCEL REQUEST 

            $excelRequestFCL = ContractFclFile::where('contract_id',$data->contract->id)->first();
            if(!empty($excelRequestFCL)){
                $excelRequestIdFCL = $excelRequestFCL->id;
            }else{
                $excelRequestIdFCL= '0';

            }



            $excelRequest = NewContractRequest::where('contract_id',$data->contract->id)->first();
            if(!empty($excelRequest)){
                $excelRequestId = $excelRequest->id;
            }else{
                $excelRequestId = "0";
            }

            $idContract = 0;
            $totalItems= 0;
            if($data->contract->status !='api'){
                $mediaItems = $data->contract->getMedia('document');
                $totalItems = count($mediaItems);

                if($totalItems > 0)
                    $idContract = $data->contract->id;
            }


            // Franja APIS
            $color = '';
            if($data->contract->status == 'api'){
                if($data->contract->number == 'MAERSK'){
                    $color = 'bg-maersk';
                } else if($data->contract->number == 'SAFMARINE') {
                    $color = 'bg-safmarine';
                } else {
                    $color = 'bg-danger';
                }

            }

            // Valores
            $data->setAttribute('excelRequest',$excelRequestId);
            $data->setAttribute('excelRequestFCL',$excelRequestIdFCL);
            $data->setAttribute('rates',$collectionRate);
            $data->setAttribute('localfreight',$collectionFreight);
            $data->setAttribute('localdestiny',$collectionDestiny);
            $data->setAttribute('localorigin',$collectionOrigin);
            // Valores totales por contenedor
            $data->setAttribute('total20', number_format($totalT20, 2, '.', ''));
            $data->setAttribute('total40', number_format($totalT40, 2, '.', ''));
            $data->setAttribute('total40hc', number_format($totalT40hc, 2, '.', ''));
            $data->setAttribute('total40nor', number_format($totalT40nor, 2, '.', ''));
            $data->setAttribute('total45', number_format($totalT45, 2, '.', ''));

            // Freight
            $data->setAttribute('tot20F', number_format($totales['20F'], 2, '.', ''));
            $data->setAttribute('tot40F', number_format($totales['40F'], 2, '.', ''));
            $data->setAttribute('tot40hcF', number_format($totales['40hcF'], 2, '.', ''));
            $data->setAttribute('tot40norF', number_format($totales['40norF'], 2, '.', ''));
            $data->setAttribute('tot45F', number_format($totales['45F'], 2, '.', ''));

            // Origin
            $data->setAttribute('tot20O', number_format($tot_20_O, 2, '.', ''));
            $data->setAttribute('tot40O', number_format($tot_40_O, 2, '.', ''));
            $data->setAttribute('tot40hcO', number_format($tot_40hc_O, 2, '.', ''));
            $data->setAttribute('tot40norO', number_format($tot_40nor_O, 2, '.', ''));
            $data->setAttribute('tot45O', number_format($tot_45_O, 2, '.', ''));
            //Destiny
            $data->setAttribute('tot20D', number_format($tot_20_D, 2, '.', ''));
            $data->setAttribute('tot40D', number_format($tot_40_D, 2, '.', ''));
            $data->setAttribute('tot40hcD', number_format($tot_40hc_D, 2, '.', ''));
            $data->setAttribute('tot40norD', number_format($tot_40nor_D, 2, '.', ''));
            $data->setAttribute('tot45D', number_format($tot_45_D, 2, '.', ''));
            // INLANDS
            $data->setAttribute('inlandDestiny',$inlandDestiny);
            $data->setAttribute('inlandOrigin',$inlandOrigin);
            $data->setAttribute('typeCurrency',$typeCurrency);

            $data->setAttribute('idCurrency',$idCurrency);
            //Excel
            $data->setAttribute('totalItems',$totalItems);
            $data->setAttribute('idContract',$idContract);
            //COlor
            $data->setAttribute('color',$color);


        }

        $chargeOrigin = ($chargesOrigin != null ) ? true : false;
        $chargeDestination = ($chargesDestination != null ) ? true : false;
        $chargeFreight = ($chargesFreight != null ) ? true : false;
        $chargeAPI = ($chargesAPI != null ) ? true : false;
        $chargeAPI_M = ($chargesAPI_M != null ) ? true : false;
        $chargeAPI_SF = ($chargesAPI_SF != null ) ? true : false;


        // Ordenar por prioridad 
        if(in_array('20',$equipment))
            $arreglo  =  $arreglo->sortBy('total20');
        else if(in_array('40',$equipment))
            $arreglo  =  $arreglo->sortBy('total40');
        else if(in_array('40HC',$equipment))
            $arreglo  =  $arreglo->sortBy('total40hc');
        else if(in_array('40NOR',$equipment))
            $arreglo  =  $arreglo->sortBy('total40nor');
        else if(in_array('45',$equipment))
            $arreglo  =  $arreglo->sortBy('total45');


        return view('quotesv2/search',  compact('arreglo','form','companies','quotes','countries','harbors','prices','company_user','currencies','currency_name','incoterm','equipmentHides','carrierMan','hideD','hideO','airlines','chargeOrigin','chargeDestination','chargeFreight','chargeAPI','chargeAPI_M', 'chargeAPI_SF'));

    }


    public function perTeu($monto,$calculation_type){
        if($calculation_type == 4){
            $monto = $monto * 2;
            return $monto;
        }else{
            return $monto;
        }
    }

    public function inlandMarkup($inlandPercentage,$inlandAmmount,$inlandMarkup,$monto,$typeCurrency,$markupInlandCurre){

        if($inlandPercentage != 0){
            $markup = ( $monto *  $inlandPercentage ) / 100 ;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup ;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkupI = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)",'montoInlandT' => $monto,'montoMarkupO' => $markup ) ;
        }else{

            $markup =$inlandAmmount;
            $markup = number_format($markup, 2, '.', '');
            $monto += number_format($inlandMarkup, 2, '.', '');
            $arraymarkupI = array("markup" => $markup , "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre,'montoInlandT' => $monto ,'montoMarkupO' => $markup) ;

        }
        return $arraymarkupI;

    }

    public function freightMarkups($freighPercentage,$freighAmmount,$freighMarkup,$monto,$typeCurrency,$type){

        if($freighPercentage != 0){
            $freighPercentage = intval($freighPercentage);
            $markup = ( $monto *  $freighPercentage ) / 100 ;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup ;
            number_format($monto, 2, '.', '');
            $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $markup, "typemarkup".$type => "$typeCurrency ($freighPercentage%)", "monto".$type => $monto,'montoMarkupO' => $markup) ;
        }else{

            $markup =trim($freighAmmount);
            $monto += $freighMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $freighMarkup, "typemarkup".$type => $typeCurrency,"monto".$type => $monto,'montoMarkupO' => $markup) ;
        }

        return $arraymarkup;

    }

    public function freightMarkupsFCL($freighPercentage,$freighAmmount,$freighMarkup,$monto,$typeCurrency,$type,$chargeCurrency){

        if($freighPercentage != 0){
            $freighPercentage = intval($freighPercentage);
            $markup = ( $monto *  $freighPercentage ) / 100 ;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup ;
            number_format($monto, 2, '.', '');
            $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $markup, "typemarkup".$type => "$typeCurrency ($freighPercentage%)", "monto".$type => $monto,'montoMarkupO'.$type  => $monto) ;
        }else{

            $valor = $this->ratesCurrency($chargeCurrency,$typeCurrency);

            $markupOrig =$freighMarkup * $valor;
            $montoOrig = $monto;

            $markup =trim($freighAmmount);
            $monto += $freighMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup".$type => $markup , "markupConvert".$type => $markupOrig, "typemarkup".$type => $typeCurrency,"monto".$type => $monto,'montoMarkupO'.$type  =>   number_format($montoOrig + $markupOrig, 2, '.', '') ) ;
        }

        return $arraymarkup;

    }

    public function localMarkups($localPercentage,$localAmmount,$localMarkup,$monto,$typeCurrency,$markupLocalCurre){

        if($localPercentage != 0){
            $markup = ( $monto *  $localPercentage ) / 100 ;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $arraymarkup = array("markup" => $markup , "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)",'montoMarkup' => $monto) ;

        }else{
            $markup =$localAmmount;
            $markup = number_format($markup, 2, '.', '');
            $monto += $localMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" => $markup , "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre,'montoMarkup' => $monto) ;

        }


        return $arraymarkup;

    }

    public function localMarkupsFCL($localPercentage,$localAmmount,$localMarkup,$monto,$montoOrig,$typeCurrency,$markupLocalCurre,$chargeCurrency){

        if($localPercentage != 0){

            // Monto original 
            $markupO = ( $montoOrig *  $localPercentage ) / 100 ;
            $montoOrig += $markupO;
            $montoOrig = number_format($montoOrig, 2, '.', '');

            $markup = ( $monto *  $localPercentage ) / 100 ;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $arraymarkup = array("markup" => $markup , "markupConvert" => $markupO, "typemarkup" => "$typeCurrency ($localPercentage%)",'montoMarkup' => $monto , 'montoMarkupO' => $montoOrig) ;

        }else{// oki
            $valor = $this->ratesCurrency($chargeCurrency,$typeCurrency);

            if($valor == '1')
                $markupOrig =$localMarkup * $valor;
            else
                $markupOrig =$localMarkup * $valor;

            $markup =trim($localMarkup);
            $markup = number_format($markup, 2, '.', '');
            $monto += $localMarkup;
            $monto = number_format($monto, 2, '.', '');

            $arraymarkup = array("markup" => $markup , "markupConvert" => $markupOrig, "typemarkup" => $markupLocalCurre,'montoMarkup' => $monto,'montoMarkupO' => $montoOrig + $markupOrig ) ;

        }


        return $arraymarkup;

    }

    public function OrdenarCollection($collection){

        $collection = $collection->groupBy([
            'surcharge_name',
            function ($item)  {
                return $item['type'];
            },
        ], $preserveKeys = true);

        // Se Ordena y unen la collection
        $collect = new collection();
        $monto = 0;
        $montoMarkup = 0;
        $totalMarkup = 0;




        foreach($collection as $item){
            $total = count($item['99']);
            $fin = array();

            foreach($item['99'] as $test){  
                $fin[] = $test['currency_id'];
            }
            $resultado = array_unique($fin); 
            foreach($item as $items){
                $totalPadres = count($item['99']);
                // $totalhijos = count($items);

                if($totalPadres >= 2 && count($resultado) > 1  ){
                    foreach($items as $itemsDetail){

                        $monto += $itemsDetail['monto']; 
                        $montoMarkup += $itemsDetail['montoMarkup']; 
                        $totalMarkup += $itemsDetail['markupConvert']; 
                    }
                    $itemsDetail['monto'] = number_format($monto, 2, '.', '');
                    $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', ''); 
                    $itemsDetail['markup'] =  number_format($totalMarkup, 2, '.', '');
                    $itemsDetail['currency'] = $itemsDetail['typecurrency'];
                    $itemsDetail['currency_id'] = $itemsDetail['currency_orig_id'];
                    $collect->push($itemsDetail);
                    $monto = 0;
                    $montoMarkup = 0;
                    $totalMarkup = 0;

                }/*else if($totalhijos > 1 ){
          foreach($items as $itemsDetail){

            $monto += $itemsDetail['monto']; 
            $montoMarkup += $itemsDetail['montoMarkup']; 
            $totalMarkup += $itemsDetail['markupConvert']; 
          }
          $itemsDetail['monto'] = number_format($monto, 2, '.', '');
          $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', ''); 
          $itemsDetail['markup'] =  number_format($totalMarkup, 2, '.', '');
          $itemsDetail['currency'] = $itemsDetail['typecurrency'];
          $itemsDetail['currency_id'] = $itemsDetail['currency_orig_id'];
          $collect->push($itemsDetail);
          $monto = 0;
          $montoMarkup = 0;
          $totalMarkup = 0;

        }*/
                else{
                    $monto = 0;
                    $montoMarkup = 0;
                    $markup = 0;

                    foreach($items as $itemsDetail){//aca

                        $monto += $itemsDetail['montoOrig']; 
                        $montoMarkup += $itemsDetail['montoMarkupO'];
                        $markup += $itemsDetail['markupConvert'];
                    }  
                    $itemsDetail['monto'] = number_format($monto, 2, '.', ''); 
                    $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', '');
                    $itemsDetail['markup'] = number_format($markup, 2, '.', '');

                    $collect->push($itemsDetail); 

                }
            }
        }



        $collect = $collect->groupBy([
            'surcharge_name',
            function ($item) use($collect) {
                $collect->put('x','surcharge_name');
                return $item['type'];
            },
        ], $preserveKeys = true);




        return $collect;
    }


    public function excelDownload($id,$idFcl,$idContract){

        if($idContract == 0){
            $Ncontract = NewContractRequest::find($id);
            if(!empty($Ncontract)){

                $time       = new \DateTime();
                $now        = $time->format('d-m-y');
                $company    = CompanyUser::find($Ncontract->company_user_id);
                $extObj     = new \SplFileInfo($Ncontract->namefile);
                $ext        = $extObj->getExtension();
                $name       = $Ncontract->id.'-'.$company->name.'_'.$now.'-FLC.'.$ext;

            }else{
                $Ncontract = ContractFclFile::find($idFcl);
                $time       = new \DateTime();
                $now        = $time->format('d-m-y');
                $extObj     = new \SplFileInfo($Ncontract->namefile);
                $ext        = $extObj->getExtension();
                $name       = $Ncontract->id.'-'.$now.'-FLC.'.$ext;

            }

            $success 	= false;
            $descarga 	= null;

            if(Storage::disk('s3_upload')->exists('Request/FCL/'.$Ncontract->namefile,$name)){
                $success 	= true;
                $descarga	= Storage::disk('s3_upload')->url('Request/FCL/'.$Ncontract->namefile,$name);
            } elseif(Storage::disk('s3_upload')->exists('contracts/'.$Ncontract->namefile,$name)){
                $success 	= true;
                $descarga	= Storage::disk('s3_upload')->url('contracts/'.$Ncontract->namefile,$name);
            } elseif(Storage::disk('FclRequest')->exists($Ncontract->namefile,$name)){
                $success 	= true;
                $descarga	= Storage::disk('FclRequest')->url($Ncontract->namefile,$name);
            } elseif(Storage::disk('UpLoadFile')->exists($Ncontract->namefile,$name)){
                $success 	= true;
                $descarga	= Storage::disk('UpLoadFile')->url($Ncontract->namefile,$name);
            }

            return response()->json(['success' => $success,'url'=>$descarga]);

            /*try{
                return Storage::disk('s3_upload')->download('Request/FCL/'.$Ncontract->namefile,$name);
            } catch(\Exception $e){
                try{
                    return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
                } catch(\Exception $e){
                    try{
                        return Storage::disk('FclRequest')->download($Ncontract->namefile,$name);
                    } catch(\Exception $e){
                        return Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
                    }
                }
            }*/
        }else{
            $contract = Contract::find($idContract);
            $downloads = $contract->getMedia('document');
            $total = count($downloads);
            if($total > 1){
                return MediaStream::create('my-contract.zip')->addMedia($downloads);

            }else{
                $media = $downloads->first();
                $mediaItem = Media::find($media->id);
                return $mediaItem;
            }



        }


    }

    public function excelDownloadLCL($id,$idlcl)
    {


        $Ncontract = NewContractRequestLcl::find($id);
        if(!empty($Ncontract)){

            $time       = new \DateTime();
            $now        = $time->format('d-m-y');
            $company    = CompanyUser::find($Ncontract->company_user_id);
            $extObj     = new \SplFileInfo($Ncontract->namefile);
            $ext        = $extObj->getExtension();
            $name       = $Ncontract->id.'-'.$company->name.'_'.$now.'-LCL.'.$ext;

        }else{
            $Ncontract = ContractLclFile::find($idlcl);
            $time       = new \DateTime();
            $now        = $time->format('d-m-y');
            $extObj     = new \SplFileInfo($Ncontract->namefile);
            $ext        = $extObj->getExtension();
            $name       = $Ncontract->id.'-'.$now.'-LCL.'.$ext;

        }

        $success 	= false;
        $descarga 	= null;

        if(Storage::disk('s3_upload')->exists('Request/LCL/'.$Ncontract->namefile,$name)){
            $success 	= true;
            //return 1;
            $descarga	= Storage::disk('s3_upload')->url('Request/LCL/'.$Ncontract->namefile,$name);
        } elseif(Storage::disk('s3_upload')->exists('contracts/'.$Ncontract->namefile,$name)){
            //return 2;
            $success 	= true;
            $descarga	= Storage::disk('s3_upload')->url('contracts/'.$Ncontract->namefile,$name);
        } elseif(Storage::disk('LclRequest')->exists($Ncontract->namefile,$name)){
            //return 3;
            $success 	= true;
            $descarga	= Storage::disk('LclRequest')->url($Ncontract->namefile,$name);
        } elseif(Storage::disk('UpLoadFile')->exists($Ncontract->namefile,$name)){
            //return 4;
            $success 	= true;
            $descarga	= Storage::disk('UpLoadFile')->url($Ncontract->namefile,$name);
        }

        return response()->json(['success' => $success,'url'=>$descarga]);


        /*try{
            return Storage::disk('s3_upload')->download('Request/LCL/'.$Ncontract->namefile,$name);
        } catch(\Exception $e){
            try{
                return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
            } catch(\Exception $e){
                try{
                    return Storage::disk('LclRequest')->download($Ncontract->namefile,$name);
                } catch(\Exception $e){
                    return Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
                }
            }
        }*/
    }


    // Funcion para Traer cantidad de Paquete y pallets 

    public function totalPalletPackage($total_quantity,$cargo_type,$type_load_cargo,$quantity){

        $cantidad_pack_pallet = array();

        if($total_quantity != null ){
            if($cargo_type ==  '1'){//Pallet
                $cantidad_pack_pallet = array('pallet' => ['cantidad' =>$total_quantity ],'package' => ['cantidad' =>0 ] );
            }else{
                $cantidad_pack_pallet = array('pallet' => ['cantidad' =>0 ],'package' => ['cantidad' =>$total_quantity ] ); 
            }
        }else{
            $cantidadPallet = 0;
            $cantidadPackage = 0;
            $type_load_cargo = array_values( array_filter($type_load_cargo));
            $quantity = array_values( array_filter($quantity));
            $count = count($type_load_cargo);
            for($i = 0; $i < $count ; $i++){     
                if($type_load_cargo[$i] ==  '1'){//Pallet
                    $cantidadPallet += $quantity[$i];
                }else{
                    $cantidadPackage += $quantity[$i];
                }   
            }
            $cantidad_pack_pallet = array('pallet' => ['cantidad' =>$cantidadPallet ],'package' => ['cantidad' =>$cantidadPackage ] );
        }

        return $cantidad_pack_pallet;

    }

    /*  **************************  LCL  ******************************************** */
    public function processSearchLCL(Request $request)
    {

        //Variables del usuario conectado
        $company_user_id=\Auth::user()->company_user_id;
        $user_id =  \Auth::id();

        //Variables para cargar el  Formulario
        $chargesOrigin = $request->input('chargeOrigin');
        $chargesDestination = $request->input('chargeDestination');
        $chargesFreight = $request->input('chargeFreight');
        $chargesAPI = $request->input('chargeAPI');
        $chargesAPI_M = $request->input('chargeAPI_M');
        $chargesAPI_SF = $request->input('chargeAPI_SF');

        $form  = $request->all();



        // Traer cantidad total de paquetes y pallet segun sea el caso 
        $package_pallet = $this->totalPalletPackage($request->input('total_quantity'),$request->input('cargo_type'),$request->input('type_load_cargo'),$request->input('quantity'));


        //dd($package_pallet);

        $incoterm = Incoterm::pluck('name','id');
        if(\Auth::user()->hasRole('subuser')){
            $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
                $q->where('user_id',\Auth::user()->id);
            })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
        }else{
            $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
        }
        $companies->prepend('Select at option','0');
        $airlines = Airline::all()->pluck('name','id');
        $harbors = Harbor::get()->pluck('display_name','id_complete');
        $countries = Country::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        $company_user = User::where('id',\Auth::id())->first();
        $carrierMan = Carrier::all()->pluck('name','id');

        if($company_user->companyUser) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
        }else{
            $currency_name = '';
        }


        if($request->input('total_weight') != null ) {

            $simple = 'show active';
            $paquete = '';
        }
        if($request->input('total_weight_pkg') != null ) {

            $simple = '';
            $paquete = 'show active';
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


        $delivery_type = $request->input('delivery_type');
        $price_id = $request->input('price_id');
        $modality_inland = $request->modality;
        $company_id = $request->input('company_id_quote');
        $mode = $request->mode;
        $incoterm_id = $request->input('incoterm_id');
        $arregloNull = array();
        $arregloNull = json_encode($arregloNull);
        //istory
        $this->storeSearchV2($origin_port,$destiny_port,$request->input('date'),$arregloNull,$delivery_type,$mode,$company_user_id,'LCL');

        $weight = $request->input("chargeable_weight");
        $weight =  number_format($weight, 2, '.', '');
        // Fecha Contrato
        $dateRange =  $request->input('date');
        $dateRange = explode("/",$dateRange);
        $dateSince = $dateRange[0];
        $dateUntil = $dateRange[1];

        //Collection Equipment Dinamico


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

        $arreglo = RateLcl::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($user_id,$company_user_id,$company_id,$dateSince,$dateUntil)
        {
            $q->whereHas('contract_user_restriction', function($a) use($user_id){
                $a->where('user_id', '=',$user_id);
            })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function($q) use($user_id,$company_user_id,$company_id,$dateSince,$dateUntil)
                     {
                         $q->whereHas('contract_company_restriction', function($b) use($company_id){
                             $b->where('company_id', '=',$company_id);
                         })->orDoesntHave('contract_company_restriction');
                     })->whereHas('contract', function($q) use($company_user_id,$dateSince,$dateUntil){
            $q->where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->where('company_user_id','=',$company_user_id);
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

                $simple = 'show active';
                $paquete = '';
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

                $simple = '';
                $paquete = 'show active';
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

            //Calculation type 
            $arrayBlHblShip = array('1','2','3','16'); // id  calculation type 1 = HBL , 2=  Shipment , 3 = BL , 16 per set
            $arraytonM3 = array('4','11','17'); //  calculation type 4 = Per ton/m3
            $arraytonCompli = array('6','7','12','13'); //  calculation type 4 = Per ton/m3
            $arrayPerTon = array('5','10'); //  calculation type 5 = Per  TON 
            $arrayPerKG = array('9'); //  calculation type 5 = Per  TON 
            $arrayPerPack = array('14'); //  per package
            $arrayPerPallet = array('15'); //  per pallet

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

                            if($chargesOrigin != null){
                                if($local->typedestiny_id == '1'){
                                    $subtotal_local =  $local->ammount;
                                    $totalAmmount =  $local->ammount  / $rateMount;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'origin', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount );
                                    $arregloOrig = array_merge($arregloOrig,$markupBL);

                                    $collectionOrig->push($arregloOrig);


                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $cantidadT );


                                    $collectionOrig->push($arregloOrigin);


                                }
                            }
                            if($chargesDestination != null){
                                if($local->typedestiny_id == '2'){
                                    $subtotal_local =  $local->ammount;
                                    $totalAmmount =  $local->ammount  / $rateMount;
                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloDest = array_merge($arregloDest,$markupBL);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99 


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $cantidadT );


                                    $collectionDest->push($arregloDest);


                                }
                            }
                            if($chargesFreight != null){
                                if($local->typedestiny_id == '3'){
                                    $subtotal_local =  $local->ammount;
                                    $totalAmmount =  $local->ammount  / $rateMount;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => "-" , 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount );
                                    $arregloPC = array_merge($arregloPC,$markupBL);

                                    $collectionFreight->push($arregloPC);

                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $cantidadT);


                                    $collectionFreight->push($arregloFreight);

                                }
                            }
                        }
                    }
                }

                if(in_array($local->calculationtypelcl_id, $arraytonM3)){

                    //ROUNDED

                    if($local->calculationtypelcl_id == '11'){
                        $ton_weight =   ceil($weight);
                    }else{
                        $ton_weight =   $weight;
                    }
                    $cantidadT = $ton_weight;

                    foreach($local->localcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){

                            if($chargesOrigin != null){
                                if($local->typedestiny_id == '1'){

                                    $subtotal_local =  $ton_weight * $local->ammount;
                                    $totalAmmount =  ( $ton_weight * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =    $subtotal_local / $rateMount ;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $markupTonM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTonM3 =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'origin', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT , 'idCurrency' => $local->currency->id ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount );
                                    $arregloOrigTonM3 = array_merge($arregloOrigTonM3,$markupTonM3);

                                    $collectionOrig->push($arregloOrigTonM3);


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $cantidadT );


                                    $collectionOrig->push($arregloOrigin);

                                }
                            }
                            if($chargesDestination != null){
                                if($local->typedestiny_id == '2'){
                                    $subtotal_local =  $ton_weight * $local->ammount;
                                    $totalAmmount =  ( $ton_weight * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =    $subtotal_local / $rateMount ;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');


                                    $markupTonM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT   , 'idCurrency' => $local->currency->id ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloDest = array_merge($arregloDest,$markupTonM3);

                                    $collectionDest->push($arregloDest);

                                    // Arreglo 99


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $cantidadT);


                                    $collectionDest->push($arregloDest);

                                }
                            }
                            if($chargesFreight != null){
                                if($local->typedestiny_id == '3'){
                                    $subtotal_local =  $ton_weight * $local->ammount;
                                    $totalAmmount =  ( $ton_weight * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =    $subtotal_local / $rateMount ;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);
                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $cantidadT , 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $cantidadT  , 'idCurrency' => $local->currency->id ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount );
                                    $arregloPC = array_merge($arregloPC,$markupTonM3);

                                    $collectionFreight->push($arregloPC);

                                    // Arreglo 99


                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $cantidadT);


                                    $collectionFreight->push($arregloFreight);

                                }
                            }
                        }
                    }
                }

                if(in_array($local->calculationtypelcl_id, $arrayPerTon)){

                    foreach($local->localcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){

                            //ROUNDED
                            if($local->calculationtypelcl_id == '10'){
                                $totalW =   ceil($totalW);
                            }

                            if($chargesOrigin != null){
                                if($local->typedestiny_id == '1'){
                                    $subtotal_local =  $totalW * $local->ammount;
                                    $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =    $subtotal_local / $rateMount ;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');

                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTon =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'origin', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloOrigTon = array_merge($arregloOrigTon,$markupTON);
                                    $collectionOrig->push($arregloOrigTon);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionOrig->push($arregloOrigin);


                                }
                            }

                            if($chargesDestination != null){
                                if($local->typedestiny_id == '2'){
                                    $subtotal_local =  $totalW * $local->ammount;
                                    $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =    $subtotal_local / $rateMount ;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');

                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloDest = array_merge($arregloDest,$markupTON);

                                    $collectionDest->push($arregloDest);


                                    // ARREGLO GENERAL 99 

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null){
                                if($local->typedestiny_id == '3'){

                                    $subtotal_local =  $totalW * $local->ammount;
                                    $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =    $subtotal_local / $rateMount ;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');

                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloPC = array_merge($arregloPC,$markupTON);

                                    $collectionFreight->push($arregloPC);
                                    // ARREGLO GENERAL 99 


                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,  'cantidad' => $unidades);


                                    $collectionFreight->push($arregloFreight);
                                }
                            }

                        }
                    }
                }

                if(in_array($local->calculationtypelcl_id, $arraytonCompli)){


                    foreach($local->localcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){


                            if($chargesOrigin != null){
                                if($local->typedestiny_id == '1'){
                                    if($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13' ){

                                        if($local->calculationtypelcl_id == '13'){
                                            $totalV = ceil($totalV);
                                        }

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
                                        if($local->calculationtypelcl_id == '12'){
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local =  $totalW * $local->ammount;
                                        $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                        $mont = $local->ammount;
                                        if($totalW > 1)
                                            $unidades = $totalW;
                                        else
                                            $unidades = '1';

                                        if($subtotal_local < $local->minimum){
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount =    $subtotal_local / $rateMount ;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');

                                        }
                                    }
                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $markupTONM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);


                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    //$totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig =  array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount , 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'calculation_id'=> $local->calculationtypelcl->id,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'origin', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'currency_id' => $local->currency->id,'montoOrig' =>$totalAmmount );
                                    $arregloOrig = array_merge($arregloOrig,$markupTONM3);
                                    $dataOrig[] = $arregloOrig;

                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id,'currency_orig_id' => $idCurrency,'cantidad' => $unidades );

                                    $dataOrig[] = $arregloOrigin;
                                    //$collectionOrig->push($arregloOrigin);

                                }
                            }

                            if($chargesDestination != null){
                                if($local->typedestiny_id == '2'){
                                    if($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13'){
                                        if($local->calculationtypelcl_id == '13'){
                                            $totalV = ceil($totalV);
                                        }
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
                                        if($local->calculationtypelcl_id == '12'){
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local =  $totalW * $local->ammount;
                                        $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                        $mont = $local->ammount;
                                        if($totalW > 1)
                                            $unidades = $totalW;
                                        else
                                            $unidades = '1';
                                        if($subtotal_local < $local->minimum){
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount =    $subtotal_local / $rateMount ;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $markupTONM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount , 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'calculation_id'=> $local->calculationtypelcl->id,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'currency_id' => $local->currency->id,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloDest = array_merge($arregloDest,$markupTONM3);
                                    $dataDest[] = $arregloDest;

                                    // ARREGLO 99


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );

                                    $dataDest[] = $arregloDest;
                                    //$collectionDest->push($arregloDest);



                                }
                            }

                            if($chargesFreight != null){
                                if($local->typedestiny_id == '3'){
                                    if($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13'){
                                        if($local->calculationtypelcl_id == '13'){
                                            $totalV = ceil($totalV);
                                        }
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
                                        if($local->calculationtypelcl_id == '12'){
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local =  $totalW * $local->ammount;
                                        $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                        $mont = $local->ammount;
                                        if($totalW > 1)
                                            $unidades = $totalW;
                                        else
                                            $unidades = '1';
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
                                    // Markup
                                    $markupTONM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloPC = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount , 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'calculation_id'=> $local->calculationtypelcl->id,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades ,'typecurrency' => $typeCurrency  ,'currency_id' => $local->currency->id,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloPC = array_merge($arregloPC,$markupTONM3);
                                    $dataFreight[] = $arregloPC;

                                    // ARREGLO 99


                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' =>$unidades );

                                    $dataFreight[] = $arregloFreight;
                                    //$collectionFreight->push($arregloFreight);


                                }
                            }

                        }
                    }
                }

                if(in_array($local->calculationtypelcl_id, $arrayPerKG)){

                    foreach($local->localcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){


                            if($chargesOrigin != null){
                                if($local->typedestiny_id == '1'){
                                    $subtotal_local =  $totalW * $local->ammount;
                                    $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $totalW * $subtotal_local)  / $rateMount;
                                        $unidades = $subtotal_local / $totalW;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'origin', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloOrigKg = array_merge($arregloOrigKg,$markupKG);
                                    $collectionOrig->push($arregloOrigKg);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionOrig->push($arregloOrigin);


                                }
                            }

                            if($chargesDestination != null){
                                if($local->typedestiny_id == '2'){
                                    $subtotal_local =  $totalW * $local->ammount;
                                    $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $totalW * $subtotal_local)  / $rateMount;
                                        $unidades = $subtotal_local / $totalW;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloDestKg = array_merge($arregloDestKg,$markupKG);

                                    $collectionDest->push($arregloDestKg);


                                    // ARREGLO GENERAL 99 

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null){
                                if($local->typedestiny_id == '3'){

                                    $subtotal_local =  $totalW * $local->ammount;
                                    $totalAmmount =  ( $totalW * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $totalW * $subtotal_local)  / $rateMount;
                                        $unidades = $subtotal_local / $totalW;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightKg = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloFreightKg = array_merge($arregloFreightKg,$markupKG);

                                    $collectionFreight->push($arregloFreightKg);
                                    // ARREGLO GENERAL 99 


                                    $arregloFreightKg = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,  'cantidad' => $unidades);


                                    $collectionFreight->push($arregloFreightKg);
                                }
                            }

                        }
                    }
                }

                if(in_array($local->calculationtypelcl_id, $arrayPerPack)){

                    foreach($local->localcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
                            $package_cantidad =  $package_pallet['package']['cantidad'];
                            if($chargesOrigin != null && $package_cantidad != 0){
                                if($local->typedestiny_id == '1'){

                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount =  ( $package_cantidad * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;


                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $package_cantidad * $subtotal_local)  / $rateMount;
                                        $unidades = $subtotal_local / $package_cantidad;

                                    }


                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpack =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'origin', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloOrigpack = array_merge($arregloOrigpack,$markupKG);
                                    $collectionOrig->push($arregloOrigpack);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionOrig->push($arregloOrigin);


                                }
                            }

                            if($chargesDestination != null  && $package_cantidad != 0){
                                if($local->typedestiny_id == '2'){
                                    $subtotal_local =  $package_cantidad * $local->ammount;
                                    $totalAmmount =  ( $package_cantidad * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $package_cantidad * $subtotal_local)  / $rateMount;


                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPack = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloDestPack = array_merge($arregloDestPack,$markupKG);

                                    $collectionDest->push($arregloDestPack);


                                    // ARREGLO GENERAL 99 

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null  && $package_cantidad != 0 ){
                                if($local->typedestiny_id == '3'){

                                    $subtotal_local =  $package_cantidad * $local->ammount;
                                    $totalAmmount =  ( $package_cantidad * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $package_cantidad * $subtotal_local)  / $rateMount;
                                        $unidades = $subtotal_local / $package_cantidad;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPack = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloFreightPack = array_merge($arregloFreightPack,$markupKG);

                                    $collectionFreight->push($arregloFreightPack);
                                    // ARREGLO GENERAL 99 


                                    $arregloFreightPack = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,  'cantidad' => $unidades);


                                    $collectionFreight->push($arregloFreightPack);
                                }
                            }

                        }
                    }
                }

                if(in_array($local->calculationtypelcl_id, $arrayPerPallet)){

                    foreach($local->localcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id ==  $carrier_all ){
                            $pallet_cantidad =  $package_pallet['pallet']['cantidad'];
                            if($chargesOrigin != null && $pallet_cantidad != 0){
                                if($local->typedestiny_id == '1'){

                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount =  ( $pallet_cantidad * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;


                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $pallet_cantidad * $subtotal_local)  / $rateMount;


                                    }


                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet =  array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'origin', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloOrigpallet = array_merge($arregloOrigpallet,$markupKG);
                                    $collectionOrig->push($arregloOrigpallet);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionOrig->push($arregloOrigin);


                                }
                            }

                            if($chargesDestination != null  && $pallet_cantidad != 0){
                                if($local->typedestiny_id == '2'){
                                    $subtotal_local =  $pallet_cantidad * $local->ammount;
                                    $totalAmmount =  ( $pallet_cantidad * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $pallet_cantidad * $subtotal_local)  / $rateMount;


                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloDestPallet = array_merge($arregloDestPallet,$markupKG);

                                    $collectionDest->push($arregloDestPallet);


                                    // ARREGLO GENERAL 99 

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $unidades );


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null  && $pallet_cantidad != 0 ){
                                if($local->typedestiny_id == '3'){

                                    $subtotal_local =  $pallet_cantidad * $local->ammount;
                                    $totalAmmount =  ( $pallet_cantidad * $local->ammount)  / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if($subtotal_local < $local->minimum){
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount =   ( $pallet_cantidad * $subtotal_local)  / $rateMount;


                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local =  number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPallet = array('surcharge_terms' => $terminos,'surcharge_name' => $local->surcharge->name,'cantidad' => $unidades , 'monto' => $mont, 'currency' => $local->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_local' => $subtotal_local  , 'cantidadT' => $unidades  ,'typecurrency' => $typeCurrency  ,'idCurrency' => $local->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount  );
                                    $arregloFreightPallet = array_merge($arregloFreightPallet,$markupKG);

                                    $collectionFreight->push($arregloFreightPallet);
                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $local->surcharge->id,'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $local->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $local->currency->id   ,'currency_orig_id' => $idCurrency,  'cantidad' => $unidades);
                                    $collectionFreight->push($arregloFreight);
                                }
                            }

                        }
                    }
                }



            }// Fin del calculo de los local charges


            //############ Global Charges   ####################


            $globalChar = GlobalChargeLcl::where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrierslcl', function($q) use($carrier) {
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
                    $totalWeight = $request->input('total_weight') ;
                }else{            
                    $totalW = $request->input('total_weight_pkg') / 1000; 
                    $totalV = $request->input('total_volume_pkg');
                    $totalWeight = $request->input('total_weight') ;
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


                            if($chargesOrigin != null){
                                if($global->typedestiny_id == '1'){
                                    $subtotal_global =  $global->ammount;
                                    $totalAmmount =  $global->ammount  / $rateMountG;

                                    // MARKUP

                                    $markupBL = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);


                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => '-' , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'origin'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT ,'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig,$markupBL);
                                    //$origGlo["origin"] = $arregloOrig;
                                    $collectionOrig->push($arregloOrig);
                                    // $collectionGloOrig->push($arregloOrig);



                                    // ARREGLO GENERAL 99 

                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => '1'  );


                                    $collectionOrig->push($arregloOrigin);


                                }
                            }

                            if($chargesDestination != null){
                                if($global->typedestiny_id == '2'){

                                    $subtotal_global =  $global->ammount;
                                    $totalAmmount =  $global->ammount  / $rateMountG;
                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');   
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => '1' , 'monto' => $global->ammount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'destination', 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDest = array_merge($arregloDest,$markupBL);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99 


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad'=>'1');


                                    $collectionDest->push($arregloDest);

                                }
                            }

                            if($chargesFreight != null){
                                if($global->typedestiny_id == '3'){
                                    $subtotal_global =  $global->ammount;
                                    $totalAmmount =  $global->ammount  / $rateMountG;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => '-' , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency  , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id ,'type'=>'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight,$markupBL);

                                    $collectionFreight->push($arregloFreight);



                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => '1'   );


                                    $collectionFreight->push($arregloFreight);

                                }
                            }
                        }
                    }
                }

                if(in_array($global->calculationtypelcl_id, $arraytonM3)){
                    //ROUNDED
                    if($global->calculationtypelcl_id == '11'){
                        $ton_weight =   ceil($weight);
                    }else{
                        $ton_weight =   $weight;
                    }
                    $cantidadT = $ton_weight;


                    foreach($global->globalcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){


                            if($chargesOrigin != null){
                                if($global->typedestiny_id == '1'){
                                    $subtotal_global =  $ton_weight * $global->ammount;
                                    $totalAmmount =  ( $ton_weight * $global->ammount)  / $rateMountG;
                                    $mont = $global->ammount;
                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =    $subtotal_global / $rateMountG ;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $cantidadT , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'origin'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT ,'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig,$markupTonM3);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99 

                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency, 'cantidad' => $cantidadT );


                                    $collectionOrig->push($arregloOrigin);


                                }
                            }

                            if($chargesDestination != null){
                                if($global->typedestiny_id == '2'){

                                    $subtotal_global =  $ton_weight * $global->ammount;
                                    $totalAmmount =  ( $ton_weight * $global->ammount)  / $rateMountG;
                                    $mont = $global->ammount;
                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =    $subtotal_global / $rateMountG ;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);
                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $cantidadT , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDest = array_merge($arregloDest,$markupTonM3);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99 

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $cantidadT );


                                    $collectionDest->push($arregloDest);

                                }
                            }

                            if($chargesFreight != null){
                                if($global->typedestiny_id == '3'){
                                    $subtotal_global =  $ton_weight * $global->ammount;
                                    $totalAmmount =  ( $ton_weight * $global->ammount)  / $rateMountG;
                                    $mont = $global->ammount;
                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =    $subtotal_global / $rateMountG ;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }
                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $cantidadT , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $cantidadT , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight,$markupTonM3);

                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $cantidadT  );


                                    $collectionFreight->push($arregloFreight);

                                }
                            }

                        }
                    }
                }

                if(in_array($global->calculationtypelcl_id, $arrayPerTon)){

                    foreach($global->globalcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){

                            //ROUNDED
                            if($global->calculationtypelcl_id == '10'){
                                $totalW =   ceil($totalW);
                            }
                            if($chargesOrigin != null){
                                if($global->typedestiny_id == '1'){


                                    $subtotal_global =  $totalW * $global->ammount;
                                    $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =    $subtotal_global / $rateMountG ;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');

                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'origin'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades,'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig,$markupTON);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $unidades  );


                                    $collectionOrig->push($arregloOrigin);

                                }
                            }

                            if($chargesDestination != null){
                                if($global->typedestiny_id == '2'){

                                    $subtotal_global =  $totalW * $global->ammount;
                                    $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =    $subtotal_global / $rateMountG ;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');

                                    }
                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDest = array_merge($arregloDest,$markupTON);
                                    $collectionDest->push($arregloDest);
                                    // ARREGLO GENERAL 99 


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99','rate_id' => $data->id  ,'calculation_id'=> $global->calculationtypelcl->id  , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades);


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null){
                                if($global->typedestiny_id == '3'){

                                    $subtotal_global =  $totalW * $global->ammount;
                                    $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =    $subtotal_global / $rateMountG ;
                                        $mont = $global->minimum / $totalW;

                                        $mont = number_format($mont, 2, '.', '');

                                    }
                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight,$markupTON);
                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades );


                                    $collectionFreight->push($arregloFreight);

                                }
                            }

                        }
                    }

                }

                if(in_array($global->calculationtypelcl_id, $arraytonCompli)){

                    foreach($global->globalcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){


                            if($chargesOrigin != null){
                                if($global->typedestiny_id == '1'){

                                    if($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13'){
                                        if($global->calculationtypelcl_id == '13'){
                                            $totalV = ceil($totalV);
                                        }
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
                                        if($global->calculationtypelcl_id == '12'){
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global =  $totalW * $global->ammount;
                                        $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                                        $mont = $global->ammount;
                                        if($totalW > 1)
                                            $unidades = $totalW;
                                        else
                                            $unidades = '1';
                                        if($subtotal_global < $global->minimum){
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount =    $subtotal_global / $rateMountG ;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');

                                        }
                                    }

                                    // MARKUP
                                    $markupTONM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'calculation_id'=> $global->calculationtypelcl->id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'origin'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'typecurrency' => $typeCurrency  ,'currency_id' => $global->currency->id   ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig,$markupTONM3);
                                    $dataGOrig[] = $arregloOrig;


                                    // ARREGLO GENERAL 99 

                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades );

                                    $dataGOrig[] = $arregloOrigin;

                                }
                            }

                            if($chargesDestination != null){
                                if($global->typedestiny_id == '2'){
                                    if($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13'){
                                        if($global->calculationtypelcl_id == '13'){
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global =  $totalV * $global->ammount;
                                        $totalAmmount =  ( $totalV * $global->ammount)  / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if($subtotal_global < $global->minimum){
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount =    $subtotal_global / $rateMountG ;
                                            $mont = $global->minimum / $totalV;// monto por unidad
                                            $mont = number_format($mont, 2, '.', '');

                                        }
                                    }else{
                                        if($global->calculationtypelcl_id == '12'){
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global =  $totalW * $global->ammount;
                                        $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                                        $mont = $global->ammount;
                                        if($totalW > 1)
                                            $unidades = $totalW;
                                        else
                                            $unidades = '1';
                                        if($subtotal_global < $global->minimum){
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount =    $subtotal_global / $rateMountG ;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');

                                        }
                                    }
                                    // MARKUP
                                    $markupTONM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'calculation_id'=> $global->calculationtypelcl->id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'typecurrency' => $typeCurrency  ,'currency_id' => $global->currency->id   ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDest = array_merge($arregloDest,$markupTONM3);
                                    $dataGDest[] = $arregloDest;

                                    // ARREGLO GENERAL 99 

                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades );

                                    $dataGDest[] = $arregloDest;


                                }
                            }

                            if($chargesFreight != null){
                                if($global->typedestiny_id == '3'){

                                    if($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13'){
                                        if($global->calculationtypelcl_id == '13'){
                                            $totalV = ceil($totalV);
                                        }
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
                                        if($global->calculationtypelcl_id == '12'){
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global =  $totalW * $global->ammount;
                                        $totalAmmount =  ( $totalW * $global->ammount)  / $rateMountG;
                                        $mont = $global->ammount;
                                        if($totalW > 1)
                                            $unidades = $totalW;
                                        else
                                            $unidades = '1';
                                        if($subtotal_global < $global->minimum){
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount =    $subtotal_global / $rateMountG ;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');

                                        }
                                    }
                                    // MARKUP

                                    $markupTONM3 = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight =  array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'calculation_id'=> $global->calculationtypelcl->id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'typecurrency' => $typeCurrency  ,'currency_id' => $global->currency->id   ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight,$markupTONM3);
                                    $dataGFreight[] = $arregloFreight;


                                    // ARREGLO GENERAL 99 
                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades);
                                    $dataGFreight[] = $arregloFreight;



                                }
                            }

                        }
                    }
                }

                if(in_array($global->calculationtypelcl_id, $arrayPerKG)){

                    foreach($global->globalcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){


                            if($chargesOrigin != null){
                                if($global->typedestiny_id == '1'){

                                    $subtotal_global =  $totalWeight * $global->ammount;
                                    $totalAmmount =  ( $totalWeight * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $totalWeight * $subtotal_global)  / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'origin'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades,'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloOrigKg = array_merge($arregloOrigKg,$markupKG);

                                    $collectionOrig->push($arregloOrigKg);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $unidades  );


                                    $collectionOrig->push($arregloOrigin);

                                }
                            }

                            if($chargesDestination != null){
                                if($global->typedestiny_id == '2'){

                                    $subtotal_global =  $totalWeight * $global->ammount;
                                    $totalAmmount =  ( $totalWeight * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $totalWeight * $subtotal_global)  / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestKg = array_merge($arregloDestKg,$markupKG);
                                    $collectionDest->push($arregloDestKg);
                                    // ARREGLO GENERAL 99 


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99','rate_id' => $data->id  ,'calculation_id'=> $global->calculationtypelcl->id  , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades);


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null){
                                if($global->typedestiny_id == '3'){

                                    $subtotal_global =  $totalWeight * $global->ammount;
                                    $totalAmmount =  ( $totalWeight * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $totalWeight * $subtotal_global)  / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightKg =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloFreightKg = array_merge($arregloFreightKg,$markupKG);
                                    $collectionFreight->push($arregloFreightKg);

                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades );


                                    $collectionFreight->push($arregloFreight);

                                }
                            }

                        }
                    }

                }

                if(in_array($global->calculationtypelcl_id, $arrayPerPack)){

                    foreach($global->globalcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){
                            $package_cantidad =  $package_pallet['package']['cantidad'];
                            if($chargesOrigin != null && $package_cantidad != '0' ){
                                if($global->typedestiny_id == '1'){

                                    $subtotal_global =  $package_cantidad * $global->ammount;
                                    $totalAmmount =  ( $package_cantidad * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $package_cantidad * $subtotal_global)  / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPack = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'origin'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades,'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloOrigPack = array_merge($arregloOrigPack,$markupKG);

                                    $collectionOrig->push($arregloOrigPack);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $unidades  );


                                    $collectionOrig->push($arregloOrigin);

                                }
                            }

                            if($chargesDestination != null && $package_cantidad != '0'){
                                if($global->typedestiny_id == '2'){

                                    $subtotal_global =  $package_cantidad * $global->ammount;
                                    $totalAmmount =  ( $package_cantidad * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $package_cantidad * $subtotal_global)  / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPack = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestPack = array_merge($arregloDestPack,$markupKG);
                                    $collectionDest->push($arregloDestPack);
                                    // ARREGLO GENERAL 99 


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99','rate_id' => $data->id  ,'calculation_id'=> $global->calculationtypelcl->id  , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades);


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null && $package_cantidad != '0'){
                                if($global->typedestiny_id == '3'){

                                    $subtotal_global =  $package_cantidad * $global->ammount;
                                    $totalAmmount =  ( $package_cantidad * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $package_cantidad * $subtotal_global)  / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPack =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloFreightPack = array_merge($arregloFreightPack,$markupKG);
                                    $collectionFreight->push($arregloFreightPack);

                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades );


                                    $collectionFreight->push($arregloFreight);

                                }
                            }

                        }
                    }

                }

                if(in_array($global->calculationtypelcl_id, $arrayPerPallet)){

                    foreach($global->globalcharcarrierslcl as $carrierGlobal){
                        if($carrierGlobal->carrier_id == $data->carrier_id  || $carrierGlobal->carrier_id ==  $carrier_all){
                            $pallet_cantidad =  $package_pallet['pallet']['cantidad'];

                            if($chargesOrigin != null && $pallet_cantidad != '0' ){
                                if($global->typedestiny_id == '1'){

                                    $subtotal_global =  $pallet_cantidad * $global->ammount;
                                    $totalAmmount =  ( $pallet_cantidad * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $pallet_cantidad * $subtotal_global)  / $rateMountG;


                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalOrigin += $totalAmmount ;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPallet = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'origin'  , 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades,'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloOrigPallet = array_merge($arregloOrigPallet,$markupKG);

                                    $collectionOrig->push($arregloOrigPallet);

                                    // ARREGLO GENERAL 99 


                                    $arregloOrigin = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency,'cantidad' => $unidades  );


                                    $collectionOrig->push($arregloOrigin);

                                }
                            }

                            if($chargesDestination != null && $pallet_cantidad != '0'){
                                if($global->typedestiny_id == '2'){

                                    $subtotal_global =  $pallet_cantidad * $global->ammount;
                                    $totalAmmount =  ( $pallet_cantidad * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $pallet_cantidad * $subtotal_global)  / $rateMountG;

                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');

                                    $arregloDestPallet = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestKg = array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'destination'  , 'subtotal_global' => $subtotal_global , 'cantidad' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloDestPallet = array_merge($arregloDestPallet,$markupKG);
                                    $collectionDest->push($arregloDestPallet);
                                    // ARREGLO GENERAL 99 


                                    $arregloDest = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contractlcl_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99','rate_id' => $data->id  ,'calculation_id'=> $global->calculationtypelcl->id  , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency ,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades);


                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if($chargesFreight != null && $pallet_cantidad != '0'){
                                if($global->typedestiny_id == '3'){

                                    $subtotal_global =  $pallet_cantidad * $global->ammount;
                                    $totalAmmount =  ( $pallet_cantidad * $global->ammount)  / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if($subtotal_global < $global->minimum){
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount =   ( $pallet_cantidad * $subtotal_global)  / $rateMountG;


                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage,$localAmmount,$localMarkup,$totalAmmount,$typeCurrency,$markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global =  number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPallet =  array('surcharge_terms' => $terminos,'surcharge_name' => $global->surcharge->name,'cantidad' => $unidades , 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode,'totalAmmount' =>  $totalAmmount.' '.$typeCurrency   , 'calculation_name' => $global->calculationtypelcl->name,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'freight', 'subtotal_global' => $subtotal_global , 'cantidadT' => $unidades , 'typecurrency' => $typeCurrency  ,'idCurrency' => $global->currency->id,'currency_orig_id' => $idCurrency ,'montoOrig' =>$totalAmmount);
                                    $arregloFreightPallet = array_merge($arregloFreightPallet,$markupKG);
                                    $collectionFreight->push($arregloFreightPallet);

                                    // ARREGLO GENERAL 99 

                                    $arregloFreight = array('surcharge_terms' => $terminos,'surcharge_id' => $global->surcharge->id,'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00,'montoMarkup' => 0.00,'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name,'contract_id' => $data->contract_id,'carrier_id' => $carrierGlobal->carrier_id,'type'=>'99' ,'rate_id' => $data->id ,'calculation_id'=> $global->calculationtypelcl->id , 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency,'currency_id' => $global->currency->id   ,'currency_orig_id' => $idCurrency ,'cantidad' => $unidades );


                                    $collectionFreight->push($arregloFreight);

                                }
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
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id',$carrArreglo)->first();

                    if(!empty($test)){
                        $totalA = explode(' ',$test['totalAmmount']);
                        $totalOrigin += $totalA[0];  
                        $collectionOrig->push($test);

                        return $test;
                    }
                });
            }

            if(!empty($dataDest)){
                $collectDest = Collection::make($dataDest);
                $m3tonDest= $collectDest->groupBy('surcharge_name')->map(function($item) use($collectionDest,&$totalDestiny,$data,$carrier_all){
                    $carrArreglo = array($data->carrier_id,$carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id',$carrArreglo)->first();
                    if(!empty($test)){
                        $totalA = explode(' ',$test['totalAmmount']);
                        $totalDestiny += $totalA[0];  
                        //            $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if(!empty($dataFreight)){

                $collectFreight = Collection::make($dataFreight);
                $m3tonFreight= $collectFreight->groupBy('surcharge_name')->map(function($item) use($collectionFreight,&$totalFreight,$data,$carrier_all){
                    $carrArreglo = array($data->carrier_id,$carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id',$carrArreglo)->first();
                    if(!empty($test)){
                        $totalA = explode(' ',$test['totalAmmount']);
                        $totalFreight += $totalA[0];  
                        //$arre['freight'] = $test;
                        $collectionFreight->push($test);
                        return $test;
                    }
                });
            }

            // Globales 
            if(!empty($dataGOrig)){
                $collectGOrig = Collection::make($dataGOrig);

                $m3tonGOrig= $collectGOrig->groupBy('surcharge_name')->map(function($item) use($collectionOrig,&$totalOrigin,$data,$carrier_all){
                    $carrArreglo = array($data->carrier_id,$carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id',$carrArreglo)->first();
                    if(!empty($test)){
                        $totalA = explode(' ',$test['totalAmmount']);
                        $totalOrigin += $totalA[0];  

                        //$arre['origin'] = $test;
                        $collectionOrig->push($test);
                        return $test;
                    }
                });
            }

            if(!empty($dataGDest)){
                $collectGDest = Collection::make($dataGDest);
                $m3tonDestG= $collectGDest->groupBy('surcharge_name')->map(function($item) use($collectionDest,&$totalDestiny,$data,$carrier_all){
                    $carrArreglo = array($data->carrier_id,$carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id',$carrArreglo)->first();
                    if(!empty($test)){
                        $totalA = explode(' ',$test['totalAmmount']);
                        $totalDestiny += $totalA[0];  
                        // $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if(!empty($dataGFreight)){

                $collectGFreight = Collection::make($dataGFreight);
                $m3tonFreightG= $collectGFreight->groupBy('surcharge_name')->map(function($item) use($collectionFreight,&$totalFreight,$data,$carrier_all){
                    $carrArreglo = array($data->carrier_id,$carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id',$carrArreglo)->first();
                    if(!empty($test)){
                        $totalA = explode(' ',$test['totalAmmount']);
                        $totalFreight += $totalA[0];  
                        //$arre['freight'] = $test;
                        $collectionFreight->push($test);
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


            if(!empty($collectionOrig))
                $collectionOrig = $this->OrdenarCollectionLCL($collectionOrig);
            if(!empty($collectionDest))
                $collectionDest = $this->OrdenarCollectionLCL($collectionDest);
            if(!empty($collectionFreight))
                $collectionFreight = $this->OrdenarCollectionLCL($collectionFreight);


            // SCHEDULE TYPE 
            if($data->schedule_type_id != null){
                $sheduleType = ScheduleType::find($data->schedule_type_id);
                $data->setAttribute('sheduleType',$sheduleType->name);
            }else{
                $data->setAttribute('sheduleType',null);
            }
            //remarks
            $mode = "";
            $remarks="";
            if($data->contract->comments != "")
                $remarks = $data->contract->comments."<br>";

            $typeMode = $request->input('mode');
            $remarks .= $this->remarksCondition($data->port_origin,$data->port_destiny,$data->carrier,$typeMode);
            $remarks = trim($remarks);


            // EXCEL REQUEST 

            $excelRequestLCL = ContractLclFile::where('contractlcl_id',$data->contract->id)->first();
            if(!empty($excelRequestLCL)){
                $excelRequestIdLCL = $excelRequestLCL->id;
            }else{
                $excelRequestIdLCL= '0';

            }

            $excelRequest = NewContractRequestLcl::where('contract_id',$data->contract->id)->first();
            if(!empty($excelRequest)){
                $excelRequestId = $excelRequest->id;
            }else{
                $excelRequestId = "";
            }



            //COlor
            $data->setAttribute('color','');
            $data->setAttribute('remarks',$remarks);
            $data->setAttribute('excelRequest',$excelRequestId);
            $data->setAttribute('excelRequestLCL',$excelRequestIdLCL);
            $data->setAttribute('localOrig',$collectionOrig);
            $data->setAttribute('localDest',$collectionDest);
            $data->setAttribute('localFreight',$collectionFreight);


            $data->setAttribute('freightCharges',$FreightCharges);
            $data->setAttribute('totalFreight',$totalFreight);
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

            // Ordenar las colecciones


        }

        $arreglo  =  $arreglo->sortBy('totalQuote');

        $chargeOrigin = ($chargesOrigin != null ) ? true : false;
        $chargeDestination = ($chargesDestination != null ) ? true : false;
        $chargeFreight = ($chargesFreight != null ) ? true : false;
        $chargeAPI = ($chargesAPI != null ) ? true : false;
        $chargeAPI_M =  ($chargesAPI_M != null ) ? true : false;
        $chargeAPI_SF =  ($chargesAPI_SF != null ) ? true : false;

        $hideO = 'hide';
        $hideD = 'hide';
        $form  = $request->all();
        //dd($form);
        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name','id');

        return view('quotesv2/searchLCL', compact('harbor','formulario','arreglo','form','companies','harbors','hideO','hideD','incoterm','simple','paquete','chargeOrigin','chargeDestination','chargeFreight','chargeAPI','chargeAPI_M', 'chargeAPI_SF'));

    }



    /**
  * Ordena las colleciones LCL 
  * @method function
  * @param {Object} recibe el objeto coleccion destino , origen o freight 
  * @return {Object} coleccion ordenada segun surcharge y calculation name 
  */
    public function OrdenarCollectionLCL($collection){

        $collection = $collection->groupBy([
            'surcharge_name','calculation_name',
            function ($item)  {
                return $item['type'];
            },
        ], $preserveKeys = true);

        // Se Ordena y unen la collection
        $collect = new collection();
        $monto = 0;
        $montoMarkup = 0;
        $totalMarkup = 0;


        foreach($collection as $item){
            foreach($item as $items){
                $total = count($items);

                if($total > 1 ){
                    foreach($items as $itemsT){
                        foreach($itemsT as $itemsDetail){
                            $monto += $itemsDetail['monto']; 
                            $montoMarkup += $itemsDetail['montoMarkup']; 
                            $totalMarkup += $itemsDetail['markup']; 

                        }
                    }
                    $itemsDetail['monto'] = number_format($monto, 2, '.', '');
                    $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', ''); 
                    $itemsDetail['markup'] =  number_format($totalMarkup, 2, '.', '');
                    $itemsDetail['currency'] = $itemsDetail['typecurrency'];
                    $itemsDetail['currency_id'] = $itemsDetail['currency_orig_id'];
                    $collect->push($itemsDetail);
                    $monto = 0;
                    $montoMarkup = 0;
                    $totalMarkup = 0;


                }else{
                    foreach($items as $itemsT){
                        foreach($itemsT as $itemsDetail){
                            $itemsDetail['monto'] = number_format($itemsDetail['montoOrig'], 2, '.', ''); 
                            $collect->push($itemsDetail); 
                            $monto = 0;
                            $montoMarkup = 0;
                            $totalMarkup = 0;
                        }

                    }
                }
            }
        }

        $collect = $collect->groupBy([
            'surcharge_name',
            function ($item) use($collect) {
                $collect->put('x','surcharge_name');
                return $item['type'];
            },
        ], $preserveKeys = true);

        return $collect;
    }


    // Store  LCL AUTOMATIC


    public function storeLCL(Request $request){

        if(!empty($request->input('form'))){
            $form =  json_decode($request->input('form'));

            $info = $request->input('info');
            $dateQ = explode('/',$form->date);
            $since = $dateQ[0];
            $until = $dateQ[1];
            $priceId = null;
            $mode =   $form->mode;
            if(isset($form->price_id )){
                $priceId = $form->price_id;
                if($priceId=="0"){
                    $priceId = null;
                }
            }


            $fcompany_id = null;
            $fcontact_id  = null;
            $payments = null;
            if(isset($form->company_id_quote )){
                if($form->company_id_quote != "0" && $form->company_id_quote != null ){
                    $payments = $this->getCompanyPayments($form->company_id_quote);
                    $fcompany_id = $form->company_id_quote;
                    $fcontact_id  = $form->contact_id;
                }
            }



            $typeText = "LCL";
            $arregloNull = array();
            $arregloNull = json_encode($arregloNull);
            $equipment =  $arregloNull;
            $delivery_type = $request->input('delivery_type') ;


            $request->request->add(['company_user_id' => \Auth::user()->company_user_id ,'quote_id'=>$this->idPersonalizado(),'type'=>'LCL','delivery_type'=>$form->delivery_type,'company_id'=>$fcompany_id,'contact_id' => $fcontact_id,'validity_start'=>$since,'validity_end'=>$until,'user_id'=>\Auth::id(), 'equipment'=>$equipment  , 'status'=>'Draft' ,'date_issued'=>$since ,'price_id' => $priceId ,'payment_conditions' => $payments,'total_quantity' => $form->total_quantity , 'total_weight' => $form->total_weight , 'total_volume' => $form->total_volume , 'chargeable_weight' => $form->chargeable_weight]);
            $quote= QuoteV2::create($request->all());

            $company = User::where('id',\Auth::id())->with('companyUser.currency')->first();
            $currency_id = $company->companyUser->currency_id;
            $currency = Currency::find($currency_id);
            $quantity = array_values( array_filter($form->quantity) );
            //dd($input);
            $type_cargo = array_values( array_filter($form->type_load_cargo) );
            $height = array_values( array_filter($form->height) );
            $width = array_values( array_filter($form->width) );
            $large = array_values( array_filter($form->large) );
            $weight = array_values( array_filter($form->weight) );
            $volume = array_values( array_filter($form->volume) );


            if(count($quantity)>0){
                foreach($type_cargo as $key=>$item){

                    $package_load = new PackageLoadV2();
                    $package_load->quote_id = $quote->id;
                    $package_load->type_cargo = $type_cargo[$key];
                    $package_load->quantity = $quantity[$key];
                    $package_load->height = $height[$key];
                    $package_load->width = $width[$key];
                    $package_load->large = $large[$key];
                    $package_load->weight = $weight[$key];
                    $package_load->total_weight = $weight[$key]*$quantity[$key];
                    if(!empty($volume[$key]) && $volume[$key] != null){
                        $package_load->volume = $volume[$key];
                    }else{
                        $package_load->volume = 0.01;
                    }

                    $package_load->save();
                }
            }


            $pdf_option = new PdfOption();
            $pdf_option->quote_id=$quote->id;
            $pdf_option->show_type='detailed';
            $pdf_option->grouped_total_currency=0;
            $pdf_option->total_in_currency=$currency->alphacode;
            $pdf_option->freight_charges_currency=$currency->alphacode;
            $pdf_option->origin_charges_currency=$currency->alphacode;
            $pdf_option->destination_charges_currency=$currency->alphacode;
            $pdf_option->show_total_freight_in_currency=$currency->alphacode;
            $pdf_option->show_schedules=1;
            $pdf_option->show_gdp_logo=1;
            $pdf_option->language='English';
            $pdf_option->save();

        }

        //AUTOMATIC QUOTE
        if(!empty($info)){
            $terms = '';
            foreach($info as $infoA){
                $info_D = json_decode($infoA);

                // Rates

                foreach($info_D->rates as $rateO){

                    $arregloNull = array();    
                    $remarks = $info_D->remarks."<br>";          
                    $request->request->add(['contract' => $info_D->contract->name." / ".$info_D->contract->number ,'origin_port_id'=> $info_D->port_origin->id,'destination_port_id'=>$info_D->port_destiny->id ,'carrier_id'=>$info_D->carrier->id ,'currency_id'=>  $info_D->currency->id ,'quote_id'=>$quote->id,'remarks'=>$remarks , 'schedule_type' =>$info_D->sheduleType , 'transit_time'=> $info_D->transit_time  , 'via' => $info_D->via ]);

                    $rate = AutomaticRate::create($request->all());

                    $oceanFreight = new ChargeLclAir();
                    $oceanFreight->automatic_rate_id= $rate->id;
                    $oceanFreight->type_id = '3' ;
                    $oceanFreight->surcharge_id = null ;
                    $oceanFreight->calculation_type_id = '5' ;
                    $oceanFreight->units = $rateO->cantidad;
                    $oceanFreight->price_per_unit =  $rateO->price;
                    $oceanFreight->total = $rateO->subtotal;
                    $oceanFreight->markup =  $rateO->markup;
                    $oceanFreight->currency_id =  $rateO->idCurrency; 
                    $oceanFreight->save();

                    //    $inlandD =  $request->input('inlandD'.$rateO->rate_id);
                    //  $inlandO =  $request->input('inlandO'.$rateO->rate_id);

                    //INLAND DESTINO
                    /*if(!empty($inlandD)){

            foreach( $inlandD as $inlandDestiny){

              $inlandDestiny = json_decode($inlandDestiny);

              $arregloMontoInDest = array();
              $arregloMarkupsInDest = array();
              $montoInDest = array();
              $markupInDest = array();
              foreach($inlandDestiny->inlandDetails as $key => $inlandDetails){

                if($inlandDetails->amount != 0){
                  $arregloMontoInDest = array($key => $inlandDetails->amount);
                  $montoInDest = array_merge($arregloMontoInDest,$montoInDest);  
                }
                if($inlandDetails->markup != 0){
                  $arregloMarkupsInDest = array($key => $inlandDetails->markup);
                  $markupInDest = array_merge($arregloMarkupsInDest,$markupInDest);
                }

              }

              $arregloMontoInDest =  json_encode($montoInDest);
              $arregloMarkupsInDest =  json_encode($markupInDest);
              $inlandDest = new AutomaticInland();
              $inlandDest->quote_id= $quote->id;
              $inlandDest->automatic_rate_id = $rate->id;
              $inlandDest->provider =  $inlandDestiny->providerName;
              $inlandDest->distance =  $inlandDestiny->km;
              $inlandDest->contract = $info_D->contract->id;
              $inlandDest->port_id = $inlandDestiny->port_id;
              $inlandDest->type = $inlandDestiny->type;
              $inlandDest->rate = $arregloMontoInDest;
              $inlandDest->markup = $arregloMarkupsInDest;
              $inlandDest->validity_start =$inlandDestiny->validity_start ;
              $inlandDest->validity_end=$inlandDestiny->validity_end ;
              $inlandDest->currency_id =  $info_D->currency->id;
              $inlandDest->save();

            }  
          }*/
                    //INLAND ORIGEN 

                    /* if(!empty($inlandO)){

            foreach( $inlandO as $inlandOrigin){

              $inlandOrigin = json_decode($inlandOrigin);

              $arregloMontoInOrig = array();
              $arregloMarkupsInOrig = array();
              $montoInOrig = array();
              $markupInOrig = array();
              foreach($inlandOrigin->inlandDetails as $key => $inlandDetails){

                if($inlandDetails->amount != 0){
                  $arregloMontoInOrig = array($key => $inlandDetails->amount);
                  $montoInOrig = array_merge($arregloMontoInOrig,$montoInOrig);  
                }
                if($inlandDetails->markup != 0){
                  $arregloMarkupsInOrig = array($key => $inlandDetails->markup);
                  $markupInOrig = array_merge($arregloMarkupsInOrig,$markupInOrig);
                }

              }

              $arregloMontoInOrig =  json_encode($montoInOrig);
              $arregloMarkupsInOrig =  json_encode($markupInOrig);
              $inlandOrig = new AutomaticInland();
              $inlandOrig->quote_id= $quote->id;
              $inlandOrig->automatic_rate_id = $rate->id;
              $inlandOrig->provider =  $inlandOrigin->providerName;
              $inlandOrig->distance =  $inlandOrigin->km;
              $inlandOrig->contract = $info_D->contract->id;
              $inlandOrig->port_id = $inlandOrigin->port_id;
              $inlandOrig->type = $inlandOrigin->type;
              $inlandOrig->rate = $arregloMontoInOrig;
              $inlandOrig->markup = $arregloMarkupsInOrig;
              $inlandOrig->validity_start =$inlandOrigin->validity_start ;
              $inlandOrig->validity_end=$inlandOrigin->validity_end ;
              $inlandOrig->currency_id =  $info_D->currency->id;
              $inlandOrig->save();

            }  
          } */

                }

                //CHARGES ORIGIN
                foreach($info_D->localOrig as $localorigin){

                    foreach($localorigin as $localO){
                        foreach($localO as $local){
                            $price_per_unit =  $local->monto / $local->cantidad;
                            $chargeOrigin = new ChargeLclAir();
                            $chargeOrigin->automatic_rate_id= $rate->id;
                            $chargeOrigin->type_id = '1';
                            $chargeOrigin->surcharge_id =$local->surcharge_id ;
                            $chargeOrigin->calculation_type_id =  $local->calculation_id ;
                            $chargeOrigin->units = $local->cantidad;
                            $chargeOrigin->price_per_unit = $price_per_unit;
                            $chargeOrigin->total =$local->montoMarkup ;
                            $chargeOrigin->markup =  $local->markup;
                            $chargeOrigin->currency_id =  $local->currency_id;
                            $chargeOrigin->save();

                        }
                    }





                }


                // CHARGES DESTINY 
                //dd($info_D->localDest);
                foreach($info_D->localDest as $localdestiny){

                    foreach($localdestiny as $localD){
                        foreach($localD as $local){

                            $price_per_unit =  $local->monto / $local->cantidad;
                            $chargeDestiny = new ChargeLclAir();
                            $chargeDestiny->automatic_rate_id= $rate->id;
                            $chargeDestiny->type_id = '2';
                            $chargeDestiny->surcharge_id =$local->surcharge_id ;
                            $chargeDestiny->calculation_type_id =  $local->calculation_id ;
                            $chargeDestiny->units = $local->cantidad;
                            $chargeDestiny->price_per_unit = $price_per_unit;
                            $chargeDestiny->total =$local->montoMarkup ;
                            $chargeDestiny->markup =  $local->markup;
                            $chargeDestiny->currency_id =  $local->currency_id;
                            $chargeDestiny->save();
                        }
                    }





                }

                // CHARGES FREIGHT 
                foreach($info_D->localFreight as $localfreight){
                    // --------------------
                    foreach($localfreight as $localF){
                        foreach($localF as $local){
                            $price_per_unit =  $local->monto / $local->cantidad;
                            $chargeFreight = new ChargeLclAir();
                            $chargeFreight->automatic_rate_id= $rate->id;
                            $chargeFreight->type_id = '3';
                            $chargeFreight->surcharge_id =$local->surcharge_id ;
                            $chargeFreight->calculation_type_id =  $local->calculation_id ;
                            $chargeFreight->units = $local->cantidad;
                            $chargeFreight->price_per_unit = $price_per_unit;
                            $chargeFreight->total =$local->montoMarkup ;
                            $chargeFreight->markup =  $local->markup;
                            $chargeFreight->currency_id =  $local->currency_id;
                            $chargeFreight->save();
                        }
                    }
                }

            }  

            // Terminos Automatica
            $modo  =  $request->input('mode');
            $companyUser = CompanyUser::All();
            $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
            $terms = TermAndConditionV2::where('company_user_id', Auth::user()->company_user_id)->where('type','LCL')->with('language')->get();


            $terminos_english="";
            $terminos_spanish="";
            $terminos_portuguese="";
            //Export
            foreach($terms as $term){
                if($modo == '1'){
                    if($term->language_id == '1')
                        $terminos_english .=$term->export."<br>";
                    if($term->language_id == '2')
                        $terminos_spanish .=$term->export."<br>";
                    if($term->language_id == '3')
                        $terminos_portuguese .=$term->export."<br>";
                }else{ // import

                    if($term->language_id == '1')
                        $terminos_english .=$term->import."<br>";
                    if($term->language_id == '2')
                        $terminos_spanish .=$term->import."<br>";
                    if($term->language_id == '3')
                        $terminos_portuguese .=$term->import."<br>";
                }
            }

            $quoteEdit = QuoteV2::find($quote->id);
            $quoteEdit->terms_english= $terminos_english;
            $quoteEdit->terms_and_conditions = $terminos_spanish;
            $quoteEdit->terms_portuguese = $terminos_portuguese;
            $quoteEdit->update();
        }

        //$request->session()->flash('message.nivel', 'success');
        //$request->session()->flash('message.title', 'Well done!');
        //$request->session()->flash('message.content', 'Register completed successfully!');
        //return redirect()->route('quotes.index');

        return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
    }

    public function unidadesTON($unidades){

        if($unidades < 1 ){
            return 1;
        }else{
            return $unidades;
        }

    }

    public function processGlobalRates($rates, $quote, $currency_cfg){
        foreach ($rates as $item) {
            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;

            $total_markup20=0;
            $total_markup40=0;
            $total_markup40hc=0;
            $total_markup40nor=0;
            $total_markup45=0;

            $total_lcl_air_freight=0;
            $total_lcl_air_origin=0;
            $total_lcl_air_destination=0;

            $currency = Currency::find($item->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;

            //Charges
            foreach ($item->charge as $value) {

                if($quote->pdf_option->grouped_total_currency==1){
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                }else{
                    $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                $array_amounts = json_decode($value->amount,true);
                $array_markups = json_decode($value->markups,true);

                if(isset($array_amounts['c20'])){
                    $amount20=$array_amounts['c20'];
                    $total20=$amount20/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                    $value->total_20=number_format($sum20, 2, '.', '');
                }

                if(isset($array_markups['m20'])){
                    $markup20=$array_markups['m20'];
                    $total_markup20=$markup20/$currency_rate;
                    $value->total_markup20=number_format($total_markup20, 2, '.', '');
                }

                if(isset($array_amounts['c40'])){
                    $amount40=$array_amounts['c40'];
                    $total40=$amount40/$currency_rate;          
                    $sum40 = number_format($total40, 2, '.', '');
                    $value->total_40=number_format($sum40, 2, '.', '');
                }

                if(isset($array_markups['m40'])){
                    $markup40=$array_markups['m40'];
                    $total_markup40=$markup40/$currency_rate;
                    $value->total_markup40=number_format($total_markup40, 2, '.', '');
                }

                if(isset($array_amounts['c40hc'])){
                    $amount40hc=$array_amounts['c40hc'];
                    $total40hc=$amount40hc/$currency_rate;          
                    $sum40hc = number_format($total40hc, 2, '.', '');
                    $value->total_40hc=number_format($sum40hc, 2, '.', '');
                }

                if(isset($array_markups['m40hc'])){
                    $markup40hc=$array_markups['m40hc'];
                    $total_markup40hc=$markup40hc/$currency_rate;
                    $value->total_markup40hc=number_format($total_markup40hc, 2, '.', '');
                }

                if(isset($array_amounts['c40nor'])){
                    $amount40nor=$array_amounts['c40nor'];
                    $total40nor=$amount40nor/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                    $value->total_40nor=number_format($sum40nor, 2, '.', '');
                }

                if(isset($array_markups['m40nor'])){
                    $markup40nor=$array_markups['m40nor'];
                    $total_markup40nor=$markup40nor/$currency_rate;
                    $value->total_markup40nor=number_format($total_markup40nor, 2, '.', '');
                }

                if(isset($array_amounts['c45'])){
                    $amount45=$array_amounts['c45'];
                    $total45=$amount45/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                    $value->total_45=number_format($sum45, 2, '.', '');
                }

                if(isset($array_markups['m45'])){
                    $markup45=$array_markups['m45'];
                    $total_markup45=$markup45/$currency_rate;
                    $value->total_markup45=number_format($total_markup45, 2, '.', '');
                }

                $currency_charge = Currency::find($value->currency_id);
                $value->currency_usd = $currency_charge->rates;
                $value->currency_eur = $currency_charge->rates_eur;
            }

            //Inland
            foreach ($item->inland as $inland) {
                if($quote->pdf_option->grouped_total_currency==1){
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                }else{
                    $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                $array_amounts = json_decode($inland->rate,true);
                $array_markups = json_decode($inland->markup,true);
                if(isset($array_amounts['c20'])){
                    $amount20=$array_amounts['c20'];
                    $total20=$amount20/$currency_rate;
                    $sum20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_markups['m20'])){
                    $markup20=$array_markups['m20'];
                    $total_markup20=$markup20/$currency_rate;
                }

                if(isset($array_amounts['c40'])){
                    $amount40=$array_amounts['c40'];
                    $total40=$amount40/$currency_rate;          
                    $sum40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_markups['m40'])){
                    $markup40=$array_markups['m40'];
                    $total_markup40=$markup40/$currency_rate;
                }

                if(isset($array_amounts['c40hc'])){
                    $amount40hc=$array_amounts['c40hc'];
                    $total40hc=$amount40hc/$currency_rate;          
                    $sum40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_markups['m40hc'])){
                    $markup40hc=$array_markups['m40hc'];
                    $total_markup40hc=$markup40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor'])){
                    $amount40nor=$array_amounts['c40nor'];
                    $total40nor=$amount40nor/$currency_rate;
                    $sum40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_markups['m40nor'])){
                    $markup40nor=$array_markups['m40nor'];
                    $total_markup40nor=$markup40nor/$currency_rate;
                }

                if(isset($array_amounts['c45'])){
                    $amount45=$array_amounts['c45'];
                    $total45=$amount45/$currency_rate;
                    $sum45 = number_format($total45, 2, '.', '');
                }

                if(isset($array_markups['m45'])){
                    $markup45=$array_markups['m45'];
                    $total_markup45=$markup45/$currency_rate;
                }

                $inland->total_20=number_format($sum20, 2, '.', '');
                $inland->total_40=number_format($sum40, 2, '.', '');
                $inland->total_40hc=number_format($sum40hc, 2, '.', '');
                $inland->total_40nor=number_format($sum40nor, 2, '.', '');
                $inland->total_45=number_format($sum45, 2, '.', '');

                $inland->total_m20=number_format($total_markup20, 2, '.', '');
                $inland->total_m40=number_format($total_markup40, 2, '.', '');
                $inland->total_m40hc=number_format($total_markup40hc, 2, '.', '');
                $inland->total_m40nor=number_format($total_markup40nor, 2, '.', '');
                $inland->total_m45=number_format($total_markup45, 2, '.', '');

                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }
        }

        return $rates;
    }

    /**
     * Process collections origins grouped rates
     * @param  collection $origin_charges
     * @param  collection $quote
     * @return collection
     */
    public function processOriginDetailed($origin_charges, $quote, $currency_cfg){
        $origin_charges_detailed = collect($origin_charges);

        $origin_charges_detailed = $origin_charges_detailed->groupBy([

            function ($item) {
                return $item['carrier']['name'];
            },   
            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'];
            },

        ], $preserveKeys = true);

        foreach($origin_charges_detailed as $origin=>$item){
            foreach($item as $destination=>$items){
                foreach($items as $carrier=>$itemsDetail){
                    foreach ($itemsDetail as $value) {     
                        foreach ($value->charge as $amounts) {
                            $sum20=0;
                            $sum40=0;
                            $sum40hc=0;
                            $sum40nor=0;
                            $sum45=0;
                            $total40=0;
                            $total20=0;
                            $total40hc=0;
                            $total40nor=0;
                            $total45=0;
                            $inland20= 0;
                            $inland40= 0;
                            $inland40hc= 0;
                            $inland40nor= 0;
                            $inland45= 0;
                            if($amounts->type_id==1){
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                $array_amounts = json_decode($amounts->amount,true);
                                $array_markups = json_decode($amounts->markups,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum20=$array_amounts['c20']+$array_markups['m20'];
                                    $total20=$sum20/$currency_rate;
                                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                    $sum20=$array_amounts['c20'];
                                    $total20=$sum20/$currency_rate;
                                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum20=$array_markups['m20'];
                                    $total20=$sum20/$currency_rate;
                                }

                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum40=$array_amounts['c40']+$array_markups['m40'];
                                    $total40=$sum40/$currency_rate;
                                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                    $sum40=$array_amounts['c40'];
                                    $total40=$sum40/$currency_rate;
                                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum40=$array_markups['m40'];
                                    $total40=$sum40/$currency_rate;
                                }

                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                    $sum40hc=$array_amounts['c40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum40hc=$array_markups['m40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }

                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                    $sum40nor=$array_amounts['c40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum40nor=$array_markups['m40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }

                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum45=$array_amounts['c45']+$array_markups['m45'];
                                    $total45=$sum45/$currency_rate;
                                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                    $sum45=$array_amounts['c45'];
                                    $total45=$sum45/$currency_rate;
                                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum45=$array_markups['m45'];
                                    $total45=$sum45/$currency_rate;
                                }

                                $amounts->total_20=number_format($total20, 2, '.', '');
                                $amounts->total_40=number_format($total40, 2, '.', '');
                                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                                $amounts->total_45=number_format($total45, 2, '.', '');
                            }
                        }
                        if(!$value->inland->isEmpty()){
                            foreach($value->inland as $value){
                                $inland20=0;
                                $inland40=0;
                                $inland40hc=0;
                                $inland40nor=0;
                                $inland45=0;

                                if($quote->pdf_option->grouped_origin_charges==1){
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                $array_amounts = json_decode($value->rate,true);
                                $array_markups = json_decode($value->markup,true);

                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $amount20=$array_amounts['c20'];
                                    $markup20=$array_markups['m20'];
                                    $total20=($amount20+$markup20)/$currency_rate;
                                    $inland20 = number_format($total20, 2, '.', '');
                                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                    $amount20=$array_amounts['c20'];
                                    $total20=$amount20/$currency_rate;
                                    $inland20 = number_format($total20, 2, '.', '');
                                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $markup20=$array_markups['m20'];
                                    $total20=$markup20/$currency_rate;
                                    $inland20 = number_format($total20, 2, '.', '');
                                }

                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $amount40=$array_amounts['c40'];
                                    $markup40=$array_markups['m40'];
                                    $total40=($amount40+$markup40)/$currency_rate;
                                    $inland40 = number_format($total40, 2, '.', '');
                                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                    $amount40=$array_amounts['c40'];
                                    $total40=$amount40/$currency_rate;
                                    $inland40 = number_format($total40, 2, '.', '');
                                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $markup40=$array_markups['m40'];
                                    $total40=$markup40/$currency_rate;
                                    $inland40 = number_format($total40, 2, '.', '');
                                }

                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $amount40hc=$array_amounts['c40hc'];
                                    $markup40hc=$array_markups['m40hc'];
                                    $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                                    $inland40hc = number_format($total40hc, 2, '.', '');
                                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                    $amount40hc=$array_amounts['c40hc'];
                                    $total40hc=$amount40hc/$currency_rate;
                                    $inland40hc = number_format($total40hc, 2, '.', '');
                                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $markup40hc=$array_markups['m40hc'];
                                    $total40hc=$markup40hc/$currency_rate;
                                    $inland40hc = number_format($total40hc, 2, '.', '');
                                }

                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $amount40nor=$array_amounts['c40nor'];
                                    $markup40nor=$array_markups['m40nor'];
                                    $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                                    $inland40nor = number_format($total40nor, 2, '.', '');
                                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                    $amount40nor=$array_amounts['c40nor'];
                                    $total40nor=$amount40nor/$currency_rate;
                                    $inland40nor = number_format($total40nor, 2, '.', '');
                                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $markup40nor=$array_markups['m40nor'];
                                    $total40nor=$markup40nor/$currency_rate;
                                    $inland40nor = number_format($total40nor, 2, '.', '');
                                }

                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $amount45=$array_amounts['c45'];
                                    $markup45=$array_markups['m45'];
                                    $total45=($amount45+$markup45)/$currency_rate;
                                    $inland45 = number_format($total45, 2, '.', '');
                                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                    $amount45=$array_amounts['c45'];
                                    $total45=$amount45/$currency_rate;
                                    $inland45 = number_format($total45, 2, '.', '');
                                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $markup45=$array_markups['m45'];
                                    $total45=$markup45/$currency_rate;
                                    $inland45 = number_format($total45, 2, '.', '');
                                }

                                $value->total_20=number_format($inland20, 2, '.', '');
                                $value->total_40=number_format($inland40, 2, '.', '');
                                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                                $value->total_45=number_format($inland45, 2, '.', '');
                            }
                        }            
                    }
                } 
            }
        }

        return $origin_charges_detailed;
    }    

    /**
     * Process collections origins grouped rates
     * @param  collection $origin_charges
     * @param  collection $quote
     * @return collection
     */
    public function processOriginGrouped($origin_charges, $quote, $currency_cfg){
        $origin_charges_grouped = collect($origin_charges);

        $origin_charges_grouped = $origin_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },

        ], $preserveKeys = true);

        foreach($origin_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $rate){

                    $sum20= 0;
                    $sum40= 0;
                    $sum40hc= 0;
                    $sum40nor= 0;
                    $sum45= 0;
                    $inland20= 0;
                    $inland40= 0;
                    $inland40hc= 0;
                    $inland40nor= 0;
                    $inland45= 0;

                    foreach($rate->charge as $value){

                        if($value->type_id==1){
                            if($quote->pdf_option->grouped_origin_charges==1){
                                $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                            }else{
                                $typeCurrency =  $currency_cfg->alphacode;
                            }
                            $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                            $array_amounts = json_decode($value->amount,true);
                            $array_markups = json_decode($value->markups,true);
                            if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $markup20=$array_markups['m20'];
                                $total20=($amount20+$markup20)/$currency_rate;
                                $sum20 += number_format($total20, 2, '.', '');
                            }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $total20=$amount20/$currency_rate;
                                $sum20 += number_format($total20, 2, '.', '');
                            }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $markup20=$array_markups['m20'];
                                $total20=$markup20/$currency_rate;
                                $sum20 += number_format($total20, 2, '.', '');
                            }

                            if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $markup40=$array_markups['m40'];
                                $total40=($amount40+$markup40)/$currency_rate;
                                $sum40 += number_format($total40, 2, '.', '');
                            }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $total40=$amount40/$currency_rate;
                                $sum40 += number_format($total40, 2, '.', '');
                            }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $markup40=$array_markups['m40'];
                                $total40=$markup40/$currency_rate;
                                $sum40 += number_format($total40, 2, '.', '');
                            }

                            if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                                $sum40hc += number_format($total40hc, 2, '.', '');
                            }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $total40hc=$amount40hc/$currency_rate;
                                $sum40hc += number_format($total40hc, 2, '.', '');
                            }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc=$markup40hc/$currency_rate;
                                $sum40hc += number_format($total40hc, 2, '.', '');
                            }

                            if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                                $sum40nor += number_format($total40nor, 2, '.', '');
                            }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $total40nor=$amount40nor/$currency_rate;
                                $sum40nor += number_format($total40nor, 2, '.', '');
                            }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=$markup40nor/$currency_rate;
                                $sum40nor += number_format($total40nor, 2, '.', '');
                            }

                            if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $markup45=$array_markups['m45'];
                                $total45=($amount45+$markup45)/$currency_rate;
                                $sum45 += number_format($total45, 2, '.', '');
                            }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $total45=$amount45/$currency_rate;
                                $sum45 += number_format($total45, 2, '.', '');
                            }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $markup45=$array_markups['m45'];
                                $total45=$markup45/$currency_rate;
                                $sum45 += number_format($total45, 2, '.', '');
                            }

                            $value->total_20=number_format($sum20, 2, '.', '');
                            $value->total_40=number_format($sum40, 2, '.', '');
                            $value->total_40hc=number_format($sum40hc, 2, '.', '');
                            $value->total_40nor=number_format($sum40nor, 2, '.', '');
                            $value->total_45=number_format($sum45, 2, '.', '');
                        }
                    }
                    if(!$rate->inland->isEmpty()){
                        foreach($rate->inland as $value){
                            $inland20=0;
                            $inland40=0;
                            $inland40hc=0;
                            $inland40nor=0;
                            $inland45=0;

                            if($quote->pdf_option->grouped_destination_charges==1){
                                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                            }else{
                                $typeCurrency =  $currency_cfg->alphacode;
                            }
                            $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                            $array_amounts = json_decode($value->rate,true);
                            $array_markups = json_decode($value->markup,true);

                            if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $markup20=$array_markups['m20'];
                                $total20=($amount20+$markup20)/$currency_rate;
                                $inland20 += number_format($total20, 2, '.', '');
                            }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $total20=$amount20/$currency_rate;
                                $inland20 += number_format($total20, 2, '.', ''); 
                            }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $markup20=$array_markups['m20'];
                                $total20=$markup20/$currency_rate;
                                $inland20 += number_format($total20, 2, '.', '');
                            }else{
                                $inland20 = 0;
                            }

                            if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $markup40=$array_markups['m40'];
                                $total40=($amount40+$markup40)/$currency_rate;
                                $inland40 += number_format($total40, 2, '.', '');
                            }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $total40=$amount40/$currency_rate;
                                $inland40 += number_format($total40, 2, '.', ''); 
                            }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $markup40=$array_markups['m40'];
                                $total40=$markup40/$currency_rate;
                                $inland40 += number_format($total40, 2, '.', '');
                            }

                            if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                                $inland40hc += number_format($total40hc, 2, '.', '');
                            }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $total40hc=$amount40hc/$currency_rate;
                                $inland40hc += number_format($total40hc, 2, '.', ''); 
                            }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc=$markup40hc/$currency_rate;
                                $inland40hc += number_format($total40hc, 2, '.', '');
                            }

                            if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                                $inland40nor += number_format($total40nor, 2, '.', '');
                            }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $total40nor=$amount40nor/$currency_rate;
                                $inland40nor += number_format($total40nor, 2, '.', ''); 
                            }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=$markup40nor/$currency_rate;
                                $inland40nor += number_format($total40nor, 2, '.', '');
                            }

                            if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $markup45=$array_markups['m45'];
                                $total45=($amount45+$markup45)/$currency_rate;
                                $inland45 += number_format($total45, 2, '.', '');
                            }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $total45=$amount45/$currency_rate;
                                $inland45 += number_format($total45, 2, '.', ''); 
                            }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $markup45=$array_markups['m45'];
                                $total45=$markup45/$currency_rate;
                                $inland45 += number_format($total45, 2, '.', '');
                            }

                            $value->total_20=number_format($inland20, 2, '.', '');
                            $value->total_40=number_format($inland40, 2, '.', '');
                            $value->total_40hc=number_format($inland40hc, 2, '.', '');
                            $value->total_40nor=number_format($inland40nor, 2, '.', '');
                            $value->total_45=number_format($inland45, 2, '.', '');
                        }
                    } 
                }
            }
        }

        return $origin_charges_grouped;
    }

    /**
     * Process collections destination grouped rates
     * @param  collection $destination_charges
     * @param  collection $quote
     * @return collection
     */
    public function processDestinationGrouped($destination_charges, $quote, $currency_cfg){
        $destination_charges_grouped = collect($destination_charges);

        $destination_charges_grouped = $destination_charges_grouped->groupBy([

            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },

        ], $preserveKeys = true);
        foreach($destination_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $rate){

                    $sum20= 0;
                    $sum40= 0;
                    $sum40hc= 0;
                    $sum40nor= 0;
                    $sum45= 0;
                    $inland20= 0;
                    $inland40= 0;
                    $inland40hc= 0;
                    $inland40nor= 0;
                    $inland45= 0;

                    foreach($rate->charge as $value){

                        if($value->type_id==2){
                            if($quote->pdf_option->grouped_destination_charges==1){
                                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                            }else{
                                $typeCurrency =  $currency_cfg->alphacode;
                            }
                            $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                            $array_amounts = json_decode($value->amount,true);
                            $array_markups = json_decode($value->markups,true);
                            if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $markup20=$array_markups['m20'];
                                $total20=($amount20+$markup20)/$currency_rate;
                                $sum20 += number_format($total20, 2, '.', '');
                            }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $total20=$amount20/$currency_rate;
                                $sum20 += number_format($total20, 2, '.', '');
                            }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $markup20=$array_markups['m20'];
                                $total20=$markup20/$currency_rate;
                                $sum20 += number_format($total20, 2, '.', '');
                            }

                            if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $markup40=$array_markups['m40'];
                                $total40=($amount40+$markup40)/$currency_rate;
                                $sum40 += number_format($total40, 2, '.', '');
                            }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $total40=$amount40/$currency_rate;
                                $sum40 += number_format($total40, 2, '.', '');
                            }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $markup40=$array_markups['m40'];
                                $total40=$markup40/$currency_rate;
                                $sum40 += number_format($total40, 2, '.', '');
                            }

                            if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                                $sum40hc += number_format($total40hc, 2, '.', '');
                            }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $total40hc=$amount40hc/$currency_rate;
                                $sum40hc += number_format($total40hc, 2, '.', '');
                            }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc= $markup40hc/$currency_rate;
                                $sum40hc += number_format($total40hc, 2, '.', '');
                            }

                            if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                                $sum40nor += number_format($total40nor, 2, '.', '');
                            }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $total40nor=$amount40nor/$currency_rate;
                                $sum40nor += number_format($total40nor, 2, '.', '');
                            }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=$markup40nor/$currency_rate;
                                $sum40nor += number_format($total40nor, 2, '.', '');
                            }

                            if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $markup45=$array_markups['m45'];
                                $total45=($amount45+$markup45)/$currency_rate;
                                $sum45 += number_format($total45, 2, '.', '');
                            }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $total45=$amount45/$currency_rate;
                                $sum45 += number_format($total45, 2, '.', '');
                            }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $markup45=$array_markups['m45'];
                                $total45=$markup45/$currency_rate;
                                $sum45 += number_format($total45, 2, '.', '');
                            }

                            $value->total_20=number_format($sum20, 2, '.', '');
                            $value->total_40=number_format($sum40, 2, '.', '');
                            $value->total_40hc=number_format($sum40hc, 2, '.', '');
                            $value->total_40nor=number_format($sum40nor, 2, '.', '');
                            $value->total_45=number_format($sum45, 2, '.', '');
                        }
                    }
                    if(!$rate->inland->isEmpty()){
                        foreach($rate->inland as $value){
                            $inland20=0;
                            $inland40=0;
                            $inland40hc=0;
                            $inland40nor=0;
                            $inland45=0;                            
                            if($quote->pdf_option->grouped_destination_charges==1){
                                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                            }else{
                                $typeCurrency =  $currency_cfg->alphacode;
                            }
                            $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                            $array_amounts = json_decode($value->rate,true);
                            $array_markups = json_decode($value->markup,true);

                            if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $markup20=$array_markups['m20'];
                                $total20=($amount20+$markup20)/$currency_rate;
                                $inland20 += number_format($total20, 2, '.', '');
                            }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                $amount20=$array_amounts['c20'];
                                $total20=$amount20/$currency_rate;
                                $inland20 += number_format($total20, 2, '.', ''); 
                            }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                $markup20=$array_markups['m20'];
                                $total20=$markup20/$currency_rate;
                                $inland20 += number_format($total20, 2, '.', '');
                            }

                            if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $markup40=$array_markups['m40'];
                                $total40=($amount40+$markup40)/$currency_rate;
                                $inland40 += number_format($total40, 2, '.', '');
                            }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                $amount40=$array_amounts['c40'];
                                $total40=$amount40/$currency_rate;
                                $inland40 += number_format($total40, 2, '.', ''); 
                            }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                $markup40=$array_markups['m40'];
                                $total40=$markup40/$currency_rate;
                                $inland40 += number_format($total40, 2, '.', '');
                            }

                            if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                                $inland40hc += number_format($total40hc, 2, '.', '');
                            }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $total40hc=$amount40hc/$currency_rate;
                                $inland40hc += number_format($total40hc, 2, '.', ''); 
                            }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                $markup40hc=$array_markups['m40hc'];
                                $total40hc=$markup40hc/$currency_rate;
                                $inland40hc += number_format($total40hc, 2, '.', '');
                            }

                            if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                                $inland40nor += number_format($total40nor, 2, '.', '');
                            }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $total40nor=$amount40nor/$currency_rate;
                                $inland40nor += number_format($total40nor, 2, '.', ''); 
                            }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                $markup40nor=$array_markups['m40nor'];
                                $total40nor=$markup40nor/$currency_rate;
                                $inland40nor += number_format($total40nor, 2, '.', '');
                            }

                            if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $markup45=$array_markups['m45'];
                                $total45=($amount45+$markup45)/$currency_rate;
                                $inland45 += number_format($total45, 2, '.', '');
                            }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                $amount45=$array_amounts['c45'];
                                $total45=$amount45/$currency_rate;
                                $inland45 += number_format($total45, 2, '.', ''); 
                            }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                $markup45=$array_markups['m45'];
                                $total45=$markup45/$currency_rate;
                                $inland45 += number_format($total45, 2, '.', '');
                            }

                            $value->total_20=number_format($inland20, 2, '.', '');
                            $value->total_40=number_format($inland40, 2, '.', '');
                            $value->total_40hc=number_format($inland40hc, 2, '.', '');
                            $value->total_40nor=number_format($inland40nor, 2, '.', '');
                            $value->total_45=number_format($inland45, 2, '.', '');
                        }
                    } 
                }
            }
        }

        return $destination_charges_grouped;
    }

    /**
     * Process collections destination detailed rates
     * @param  collection $destination_charges
     * @param  collection $quote
     * @return collection
     */
    public function processDestinationDetailed($destination_charges, $quote, $currency_cfg){
        $destination_charges = $destination_charges->groupBy([

            function ($item) {
                return $item['carrier']['name'];
            },   
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['origin_port']['name'];
            },

        ], $preserveKeys = true);

        foreach($destination_charges as $carrier=>$item){
            foreach($item as $destination=>$items){
                foreach($items as $origin=>$itemsDetail){
                    foreach ($itemsDetail as $value) {     
                        foreach ($value->charge as $amounts) {
                            $sum20=0;
                            $sum40=0;
                            $sum40hc=0;
                            $sum40nor=0;
                            $sum45=0;
                            $total40=0;
                            $total20=0;
                            $total40hc=0;
                            $total40nor=0;
                            $total45=0;
                            $inland20= 0;
                            $inland40= 0;
                            $inland40hc= 0;
                            $inland40nor= 0;
                            $inland45= 0;          
                            if($amounts->type_id==2){
                                //dd($quote->pdf_option->destination_charges_currency);
                                if($quote->pdf_option->grouped_destination_charges==1){
                                    $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                $array_amounts = json_decode($amounts->amount,true);
                                $array_markups = json_decode($amounts->markups,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum20=$array_amounts['c20']+$array_markups['m20'];
                                    $total20=$sum20/$currency_rate;
                                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                    $sum20=$array_amounts['c20'];
                                    $total20=$sum20/$currency_rate;
                                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum20=$array_markups['m20'];
                                    $total20=$sum20/$currency_rate;
                                }

                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum40=$array_amounts['c40']+$array_markups['m40'];
                                    $total40=$sum40/$currency_rate;
                                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                    $sum40=$array_amounts['c40'];
                                    $total40=$sum40/$currency_rate;
                                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum40=$array_markups['m40'];
                                    $total40=$sum40/$currency_rate;
                                }

                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                    $sum40hc=$array_amounts['c40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum40hc=$array_markups['m40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }

                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                    $sum40nor=$array_amounts['c40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum40nor=$array_markups['m40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }

                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum45=$array_amounts['c45']+$array_markups['m45'];
                                    $total45=$sum45/$currency_rate;
                                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                    $sum45=$array_amounts['c45'];
                                    $total45=$sum45/$currency_rate;
                                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum45=$array_markups['m45'];
                                    $total45=$sum45/$currency_rate;
                                }        

                                $amounts->total_20=number_format($total20, 2, '.', '');
                                $amounts->total_40=number_format($total40, 2, '.', '');
                                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                                $amounts->total_45=number_format($total45, 2, '.', '');
                            }
                        }
                        if(!$value->inland->isEmpty()){
                            $inland20=0;
                            $inland40=0;
                            $inland40hc=0;
                            $inland40nor=0;
                            $inland45=0;                            
                            foreach($value->inland as $value){
                                if($quote->pdf_option->grouped_destination_charges==1){
                                    $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                $array_amounts = json_decode($value->rate,true);
                                $array_markups = json_decode($value->markup,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $amount20=$array_amounts['c20'];
                                    $markup20=$array_markups['m20'];
                                    $total20=($amount20+$markup20)/$currency_rate;
                                    $inland20 += number_format($total20, 2, '.', '');
                                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                    $amount20=$array_amounts['c20'];
                                    $total20=$amount20/$currency_rate;
                                    $inland20 += number_format($total20, 2, '.', ''); 
                                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $markup20=$array_markups['m20'];
                                    $total20=$markup20/$currency_rate;
                                    $inland20 += number_format($total20, 2, '.', '');
                                }

                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $amount40=$array_amounts['c40'];
                                    $markup40=$array_markups['m40'];
                                    $total40=($amount40+$markup40)/$currency_rate;
                                    $inland40 += number_format($total40, 2, '.', '');
                                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                    $amount40=$array_amounts['c40'];
                                    $total40=$amount40/$currency_rate;
                                    $inland40 += number_format($total40, 2, '.', ''); 
                                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $markup40=$array_markups['m40'];
                                    $total40=$markup40/$currency_rate;
                                    $inland40 += number_format($total40, 2, '.', '');
                                }

                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $amount40hc=$array_amounts['c40hc'];
                                    $markup40hc=$array_markups['m40hc'];
                                    $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                                    $inland40hc += number_format($total40hc, 2, '.', '');
                                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                    $amount40hc=$array_amounts['c40hc'];
                                    $total40hc=$amount40hc/$currency_rate;
                                    $inland40hc += number_format($total40hc, 2, '.', ''); 
                                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $markup40hc=$array_markups['m40hc'];
                                    $total40hc=$markup40hc/$currency_rate;
                                    $inland40hc += number_format($total40hc, 2, '.', '');
                                }

                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $amount40nor=$array_amounts['c40nor'];
                                    $markup40nor=$array_markups['m40nor'];
                                    $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                                    $inland40nor += number_format($total40nor, 2, '.', '');
                                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                    $amount40nor=$array_amounts['c40nor'];
                                    $total40nor=$amount40nor/$currency_rate;
                                    $inland40nor += number_format($total40nor, 2, '.', ''); 
                                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $markup40nor=$array_markups['m40nor'];
                                    $total40nor=$markup40nor/$currency_rate;
                                    $inland40nor += number_format($total40nor, 2, '.', '');
                                }

                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $amount45=$array_amounts['c45'];
                                    $markup45=$array_markups['m45'];
                                    $total45=($amount45+$markup45)/$currency_rate;
                                    $inland45 += number_format($total45, 2, '.', '');
                                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                    $amount45=$array_amounts['c45'];
                                    $total45=$amount45/$currency_rate;
                                    $inland45 += number_format($total45, 2, '.', ''); 
                                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $markup45=$array_markups['m45'];
                                    $total45=$markup45/$currency_rate;
                                    $inland45 += number_format($total45, 2, '.', '');
                                }
                                $value->total_20=number_format($inland20, 2, '.', '');
                                $value->total_40=number_format($inland40, 2, '.', '');
                                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                                $value->total_45=number_format($inland45, 2, '.', '');
                            }
                        } 
                    }
                } 
            }
        }
        return $destination_charges;
    }

    /**
     * Process collections freight charges
     * @param  collection $freight_charges
     * @param  collection $quote
     * @return collection
     */
    public function processFreightCharges($freight_charges, $quote, $currency_cfg){

        $freight_charges_grouped = collect($freight_charges);

        $freight_charges_grouped = $freight_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },

        ], $preserveKeys = true);

        foreach($freight_charges_grouped as $freight){
            foreach($freight as $detail){
                foreach($detail as $item){
                    $total_rate20=0;
                    $total_rate40=0;
                    $total_rate40hc=0;
                    $total_rate40nor=0;
                    $total_rate45=0;

                    $total_rate_markup20=0;
                    $total_rate_markup40=0;
                    $total_rate_markup40hc=0;
                    $total_rate_markup40nor=0;
                    $total_rate_markup45=0;

                    foreach($item as $rate){
                        $sum20=0;
                        $sum40=0;
                        $sum40hc=0;
                        $sum40nor=0;
                        $sum45=0;
                        $total40=0;
                        $total20=0;
                        $total40hc=0;
                        $total40nor=0;
                        $total45=0;

                        foreach ($rate->charge as $amounts) {
                            if($amounts->type_id==3){
                                if($freight_charges_grouped->count()>1){
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }else{
                                    if($quote->pdf_option->grouped_freight_charges==1){
                                        $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                    }else{
                                        $typeCurrency = $currency_cfg->alphacode;   
                                    }
                                }
                                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                $array_amounts = json_decode($amounts->amount,true);
                                $array_markups = json_decode($amounts->markups,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum20=$array_amounts['c20']+$array_markups['m20'];
                                    $total20=$sum20/$currency_rate;
                                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                                    $sum20=$array_amounts['c20'];
                                    $total20=$sum20/$currency_rate;
                                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum20=$array_markups['m20'];
                                    $total20=$sum20/$currency_rate;
                                }

                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum40=$array_amounts['c40']+$array_markups['m40'];
                                    $total40=$sum40/$currency_rate;
                                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                                    $sum40=$array_amounts['c40'];
                                    $total40=$sum40/$currency_rate;
                                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum40=$array_markups['m40'];
                                    $total40=$sum40/$currency_rate;
                                }

                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                                    $sum40hc=$array_amounts['c40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum40hc=$array_markups['m40hc'];
                                    $total40hc=$sum40hc/$currency_rate;
                                }

                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                                    $sum40nor=$array_amounts['c40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum40nor=$array_markups['m40nor'];
                                    $total40nor=$sum40nor/$currency_rate;
                                }

                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum45=$array_amounts['c45']+$array_markups['m45'];
                                    $total45=$sum45/$currency_rate;
                                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                                    $sum45=$array_amounts['c45'];
                                    $total45=$sum45/$currency_rate;
                                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum45=$array_markups['m45'];
                                    $total45=$sum45/$currency_rate;
                                }

                                if(isset($array_amounts['c20']) || isset($array_markups['m20'])){
                                    $amounts->total_20=number_format($total20, 2, '.', '');
                                }
                                if(isset($array_amounts['c40']) || isset($array_markups['m40'])){
                                    $amounts->total_40=number_format($total40, 2, '.', '');
                                }
                                if(isset($array_amounts['c40hc']) || isset($array_markups['m40hc'])){
                                    $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                                }
                                if(isset($array_amounts['c40nor']) || isset($array_markups['m40nor'])){
                                    $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                                }
                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $amounts->total_45=number_format($total45, 2, '.', '');
                                }
                            }
                        }
                    }
                }
            }
        }

        return $freight_charges_grouped;
    }

    public function processChargesLclAir($charges,$type,$type_2,$carrier){
        $charges_grouped = collect(charges);

        $charges_grouped = $charges_grouped->groupBy([

            function ($item) {
                return $item[$type]['name'].', '.$item[$type]['code'];
            },
            function ($item) {
                return $item[$carrier]['name'];
            },      
            function ($item) {
                return $item[$type_2]['name'];
            },
        ], $preserveKeys = true);
        foreach($origin_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $value){

                            if($value->type_id==1){
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }

                                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                                $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                                $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                            }
                        }
                    }
                }
            }
        }

        return $charges_grouped;
    }


    public function storeSearchV2($origPort,$destPort,$pickUpDate,$equipment,$delivery,$direction,$company,$type){


        $searchRate = new SearchRate();
        $searchRate->pick_up_date  = $pickUpDate;
        $searchRate->equipment  = json_encode($equipment);
        $searchRate->delivery  = $delivery;
        $searchRate->direction  = $direction;
        $searchRate->company_user_id  = $company;
        $searchRate->type  = $type;

        $searchRate->user_id = \Auth::id();
        $searchRate->save();
        foreach($origPort as $orig => $valueOrig)
        {
            foreach($destPort as $dest => $valueDest)
            {
                $detailport = new SearchPort();
                $detailport->port_orig =$valueOrig; // $request->input('port_origlocal'.$contador.'.'.$orig);
                $detailport->port_dest = $valueDest;//$request->input('port_destlocal'.$contador.'.'.$dest);
                $detailport->search_rate()->associate($searchRate);
                $detailport->save();
            }

        }


    }

    /**
     * Descargar archivo .xlsx con listado de Cotizaciones
     */
    public function downloadQuotes(){
        //return Excel::download(new QuotesExport, 'quotes.xlsx');
        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = QuoteV2::where('owner',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }else{
            $quotes = QuoteV2::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $nameFile = str_replace([' '],'_',$now.'_quotes');
        Excel::create($nameFile, function($excel) use($nameFile, $quotes) {
            $excel->sheet('Quotes', function($sheet) use($quotes) {
                //dd($contract);
                $sheet->cells('A1:AG1', function($cells) {
                    $cells->setBackground('#2525ba');
                    $cells->setFontColor('#ffffff');
                    $cells->setValignment('center');
                });
                //$sheet->setBorder('A1:AO1', 'thin');
                $sheet->row(1, array(
                    'Id',
                    'Quote Id',
                    'Custom Quote Id',
                    'Type',
                    'Delivery type',
                    'Equipment',
                    'Total quantity (LCL/AIR)',
                    'Total weight (LCL/AIR)',
                    'Total volume (LCL/AIR)',
                    'Chargeable weight (LCL/AIR)',
                    'Company',
                    'Contact',
                    'User',
                    'Price level',
                    'Origin port',
                    'Destination port',
                    'Origin address',
                    'Destination address',
                    'Valid from',
                    'Valid until',
                    'Commodity',
                    'Kind of cargo',
                    'GDP',
                    'Risk level',
                    'Currency',
                    'Incoterm',
                    'Date issued',
                    'Remarks',
                    'Payments conditions',
                    'Terms and conditions',
                    'Status',
                    'Created at',
                ));
                $i=2;
                foreach($quotes as $quote) {
                    $rates = AutomaticRate::where('quote_id',$quote->id)->get();
                    $origin = '';
                    $incoterm = '';
                    foreach($rates as $rate){
                        if($rate->origin_port_id!=''){
                            $origin.=$rate->origin_port->name.'|';
                        }else if($rate->destination_airport_id!=''){
                            $origin.=$rate->origin_airport->name.'|';
                        }else if($rate->origin_address!=''){
                            $origin.=$rate->origin_address.'|';
                        }
                    }
                    $destination = '';
                    foreach($rates as $rate){
                        if($rate->destination_port_id!=''){
                            $destination.=$rate->destination_port->name.'|';
                        }else if($rate->destination_airport_id!=''){
                            $destination.=$rate->destination_airport->name.'|';
                        }else if($rate->destination_address!=''){
                            $destination.=$rate->destination_address.'|';
                        }
                    }

                    if ($quote->delivery_type == 1) {
                        $delivery_type = 'Port to Port';
                    } elseif ($quote->delivery_type == 2) {
                        $delivery_type = 'Port to Door';
                    } elseif ($quote->delivery_type == 3) {
                        $delivery_type = 'Door to Port';
                    } else {
                        $delivery_type = 'Door to Door';
                    }

                    if ($quote->incoterm_id == 1) {
                        $incoterm = 'EWX';
                    } elseif ($quote->incoterm_id == 2) {
                        $incoterm = 'FAS';
                    } elseif ($quote->incoterm_id == 3) {
                        $incoterm = 'FCA';
                    } elseif ($quote->incoterm_id == 4) {
                        $incoterm = 'FOB';
                    } elseif ($quote->incoterm_id == 5) {
                        $incoterm = 'CFR';
                    } elseif ($quote->incoterm_id == 6) {
                        $incoterm = 'CIF';
                    } elseif ($quote->incoterm_id == 7) {
                        $incoterm = 'CIP';
                    } elseif ($quote->incoterm_id == 8) {
                        $incoterm = 'DAT';
                    } elseif ($quote->incoterm_id == 9) {
                        $incoterm = 'DAP';
                    } elseif ($quote->incoterm_id == 10) {
                        $incoterm = 'DDP';
                    }

                    if($quote->gdp == 1){
                        $gdp = 'Yes';
                    }else{
                        $gdp = 'No';
                    }

                    if($quote->cargo_type == 1){
                        $cargo_type = 'Pallets';
                    }else{
                        $cargo_type = 'Packages';
                    }

                    $sheet->row($i, array(
                        'Id' => $quote->id,
                        'Quote Id' => $quote->quote_id,
                        'Custom Quote Id' => $quote->custom_quote_id,
                        'Type' => $quote->type,
                        'Delivery type' => $delivery_type,
                        'Equipment' => $quote->equipment,
                        'Total quantity (LCL/AIR)' => $quote->total_quantity,
                        'Total weight (LCL/AIR)' => $quote->total_weight,
                        'Total volume (LCL/AIR)' => $quote->total_volume,
                        'Chargeable weight (LCL/AIR)' => $quote->chargeable_weight,
                        'Company' => @$quote->company->business_name,
                        'Contact' => @$quote->contact->first_name . ' ' . @$quote->contact->last_name,
                        'User' => @$quote->user->name . ' ' . @$quote->user->lastname,
                        'Price level' => @$quote->price->name,
                        'Origin' => $origin,
                        'Destination' => $destination,
                        'Origin address' => $quote->origin_address,
                        'Destination address' => $quote->destination_address,
                        'Valid from' => $quote->validity_start,
                        'Valid until' => $quote->validity_end,
                        'Commodity' => $quote->commodity,
                        'Kind of cargo' => $quote->kind_of_cargo,
                        'GDP' => $gdp,
                        'Risk level' => $quote->risk_level,
                        'Currency' => @$quote->currency->alphacode,
                        'Incoterm' => $incoterm,
                        'Date issued' => $quote->date_issued,
                        'Remarks' => $quote->remarks,
                        'Payments conditions' => $quote->payments_conditions,
                        'Terms and conditions' => $quote->terms_and_conditions,
                        'Status' => $quote->status,
                        'Created at' => $quote->created_at,
                    ));
                    $sheet->setBorder('A1:I' . $i, 'thin');
                    $sheet->cells('C' . $i, function ($cells) {
                        $cells->setAlignment('center');
                    });
                    $sheet->cells('I' . $i, function ($cells) {
                        $cells->setAlignment('center');
                    });
                    $i++;
                }
            })->download('xlsx');
        });
    }

    /**
   * Update chargeable weight quotes v2
   * @param Request 
   * @return json
   */
    public function updateChargeable(Request $request, $id){

        $quote = QuoteV2::find($id);
        $quote->chargeable_weight = $request->chargeable_weight;
        $quote->update();

        return response()->json(['message' => 'Ok']);
    }
}