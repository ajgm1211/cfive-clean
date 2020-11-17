<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Exports\QuotesExport;
use Maatwebsite\Excel\Facades\Excel;
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
use App\Notifications\N_general;
use App\Notifications\SlackNotification;
use Yajra\Datatables\Datatables;
use App\Repositories\Schedules;
use Carbon\Carbon;

class QuoteController extends Controller
{

    protected $schedules;

    public function __construct(Schedules $schedules)
    {
        $this->schedules = $schedules;
    }

    public function index(Request $request){
        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = Quote::where('owner',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }else{
            $quotes = Quote::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }
        $companies = Company::pluck('business_name','id');
        $harbors = Harbor::pluck('display_name','id');
        $countries = Country::pluck('name','id');
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }else{
            $company_user='';
            $currency_cfg = '';
        }
        if($request->ajax()){
            $collection = Collection::make($quotes);
            $collection->transform(function ($quote, $key) {
                $quote['client_company'] = $quote->company;
                $quote['owner'] = $quote->user;
                $quote['currency'] = $quote->currencies->alphacode;
                $quote['origin_ammount'] = $quote->originAmmount;
                $quote['freight_ammount'] = $quote->freightAmmount;
                $quote['destination_ammount'] = $quote->destinationAmmount;
                $quote['client_contact'] = $quote->contact;
                $quote['charges'] = $quote->packages;
                if($quote['origin_harbor_id']!=''){
                    $quote['origin_harbor_name'] = $quote->origin_harbor->display_name;
                }
                if($quote['origin_airport_id']!=''){
                    $quote['origin_airport_name'] = $quote->origin_airport->display_name;
                }
                if($quote['destination_harbor_id']!=''){
                    $quote['destination_harbor_name'] = $quote->destination_harbor->display_name;
                }
                if($quote['destination_airport_id']!=''){
                    $quote['destination_airport_name'] = $quote->destination_airport->display_name;
                }
                if ($quote['pdf_language'] == 1) {
                    $quote['pdf_language'] = 'English';
                } elseif ($quote['pdf_language'] == 2) {
                    $quote['pdf_language'] = 'Spanish';
                } elseif ($quote['pdf_language']== 3) {
                    $quote['pdf_language'] = 'Portuguese';
                } else {
                    $quote['pdf_language'] = 'English';
                }
                if ($quote['type_cargo'] == 1) {
                    $quote['type_cargo'] = 'FCL';
                } elseif ($quote['type_cargo'] == 2) {
                    $quote['type_cargo'] = 'LCL';
                } else {
                    $quote['type_cargo'] = 'AIR';
                }
                if ($quote['delivery_type'] == 1) {
                    $quote['delivery_type'] = 'Port to Port';
                } elseif ($quote['delivery_type'] == 2) {
                    $quote['delivery_type'] = 'Port to Door';
                } elseif ($quote['delivery_type'] == 3) {
                    $quote['delivery_type'] = 'Door to Port';
                } else {
                    $quote['delivery_type'] = 'Door to Door';
                }
                if ($quote['carrier_id'] != '') {
                    $quote['carrier_name'] = $quote->carrier->name;
                } else {
                    $quote['carrier_name'] = '';
                }
                if ($quote['modality'] == 1) {
                    $quote['modality'] = 'Export';
                } else {
                    $quote['modality'] = 'Import';
                }
                if ($quote['incoterm'] == 1) {
                    $quote['incoterm'] = 'EWX';
                } elseif ($quote['incoterm'] == 2) {
                    $quote['incoterm'] = 'FAS';
                } elseif ($quote['incoterm'] == 3) {
                    $quote['incoterm'] = 'FCA';
                } elseif ($quote['incoterm'] == 4) {
                    $quote['incoterm'] = 'FOB';
                } elseif ($quote['incoterm'] == 5) {
                    $quote['incoterm'] = 'CFR';
                } elseif ($quote['incoterm'] == 6) {
                    $quote['incoterm'] = 'CIF';
                } elseif ($quote['incoterm'] == 7) {
                    $quote['incoterm'] = 'CIP';
                } elseif ($quote['incoterm'] == 8) {
                    $quote['incoterm'] = 'DAT';
                } elseif ($quote['incoterm'] == 9) {
                    $quote['incoterm'] = 'DAP';
                } elseif ($quote['incoterm'] == 10) {
                    $quote['incoterm'] = 'DDP';
                }
                unset($quote['company_id']);
                unset($quote['contact_id']);
                unset($quote['carrier_id']);
                unset($quote['contact']);
                unset($quote['company']);
                unset($quote['currency_id']);
                unset($quote['carrier']);
                unset($quote['currencies']);
                unset($quote['origin_harbor_id']);
                unset($quote['destination_harbor_id']);
                unset($quote['origin_airport_id']);
                unset($quote['destination_airport_id']);
                unset($quote['origin_harbor']);
                unset($quote['destination_harbor']);
                unset($quote['origin_airport']);
                unset($quote['destination_airport']);
                unset($quote['packages']);
                unset($quote['user']);
                return $quote;
            });
            return $collection;
        }
        return view('quotes/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'currency_cfg'=>$currency_cfg]);
    }

    public function LoadDatatableIndex(){
        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = Quote::where('owner',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }else{
            $quotes = Quote::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }
        $companies = Company::pluck('business_name','id');
        $harbors = Harbor::pluck('display_name','id');
        $countries = Country::pluck('name','id');
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }else{
            $company_user='';
            $currency_cfg = '';
        }
        $colletions = collect([]);
        foreach($quotes as $quote){
            $comquotes      = '---';
            $businnesnames  = '---';
            $origin         = '';
            $destination    = '';
            $ammount        = '';
            $markup         = '';
            if(isset($quote->company)){
                $comquotes      = $quote->company_quote;
                $businnesnames  = $quote->company->business_name;
            }
            if($quote->origin_harbor){
                $origin = $quote->origin_harbor->display_name;
            } elseif($quote->origin_airport){
                $origin = $quote->origin_airport->name;
            } else {
                $origin = $quote->origin_address;
            }
            if($quote->destination_harbor){
                $destination = $quote->destination_harbor->display_name;
            } elseif($quote->destination_airport){
                $destination = $quote->destination_airport->name;
            } else {
                $destination = $quote->destination_address;
            }
            $ammount = $quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination .' '.$quote->currencies->alphacode;
            $markup = $quote->total_markup_origin+$quote->total_markup_freight+$quote->total_markup_destination .' '.$quote->currencies->alphacode;
            $data = [
                'id'            => $quote->id,
                'idSet'         => setearRouteKey($quote->id),
                'status'        => $quote->status->name,
                'comquotes'     => $comquotes,
                'client'        => $businnesnames,
                'created'       => date_format($quote->created_at, 'M d, Y H:i'),
                'owner'         => $quote->user->name.' '.$quote->user->lastname,
                'origin'        => $origin,
                'destination'   => $destination,
                'ammount'       => $ammount,
                'markup'        => $markup,
                'typeid'          => $quote->type,
                'image'         => $quote->type,
            ];
            $colletions->push($data);
        }
        return DataTables::of($colletions)
            ->editColumn('statusC', function ($colletion) {
                return '<span class="Status_'.$colletion['status'].'"  onclick="AbrirModal(\'change_status\','.$colletion['id'].')" style="cursor: pointer;">'.$colletion['status'].'</span>';
            })
            ->addColumn('type', function ($colletion) {
                if($colletion['typeid'] == 1){
                    return '<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
                } elseif($colletion['typeid'] == 2){
                    return '<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
                } else{
                    return  '<img src="/images/plane-blue.svg" class="img img-responsive" width="21">';
                }
            })->addColumn('action',function($colletion){
            return
                '<button class="btn btn-outline-light  dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     Options
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -136px, 0px); top: 0px; left: 0px; will-change: transform;">
                     <a class="dropdown-item" href="/quotes/'.$colletion['idSet'].'">
                        <span>
                           <i class="la la-eye"></i>
                           &nbsp;
                           Show
                        </span>
                     </a>      
                     <a href="/quotes/'.$colletion['idSet'].'/edit" class="dropdown-item" >
                        <span>
                           <i class="la la-edit"></i>
                           &nbsp;
                           Edit
                        </span>
                     </a>
                     <a href="/quotes/duplicate/'.$colletion['idSet'].'" class="dropdown-item" >
                        <span>
                           <i class="la la-plus"></i>
                           &nbsp;
                           Duplicate
                        </span>
                     </a>
                     <a href="#" class="dropdown-item" id="delete-quote" data-quote-id="'.$colletion['id'].'" >
                        <span>
                           <i class="la la-eraser"></i>
                           &nbsp;
                           Delete
                        </span>
                     </a>
                  </div>';
        })
            ->editColumn('id', 'ID: {{$id}}')->rawColumns(['action','statusC','type'])->make(true);
    }
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
        $harbors = Harbor::pluck('display_name','id');
        $countries = Country::all()->pluck('name','id');
        $carriers = Carrier::all()->pluck('name','id');
        $airlines = Airline::all()->pluck('name','id');
        $prices = Price::all()->pluck('name','id');
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
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
            $currencies->prepend ('Currency','');
            $currency_cfg = Currency::find($company_user->currency_id);
        }
        if(\Auth::user()->company_user_id && $currency_cfg != ''){
            if($currency_cfg->alphacode=='USD'){
                $exchange = Currency::where('api_code_eur','EURUSD')->first();
            }else{
                $exchange = Currency::where('api_code','USDEUR')->first();
            }
        }
        return view('quotes/add', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'prices'=>$prices,'company_user'=>$user,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates,'carriers'=>$carriers,'airlines'=>$airlines,'user'=>$user]);

    }

    public function edit($id){
        $id = obtenerRouteKey($id);
        $email_templates='';
        $quote = Quote::findOrFail($id);
        $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('display_name','id');
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
        $currencies->prepend ('Currency','');
        $carriers = Carrier::pluck('name','id');
        $airlines = Airline::pluck('name','id');
        $airports = Airport::pluck('display_name','id');
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
            $email_templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
            if($currency_cfg->alphacode=='USD'){
                $exchange = Currency::where('api_code_eur','EURUSD')->first();
            }else{
                $exchange = Currency::where('api_code','USDEUR')->first();
            }
            $port_all = harbor::where('name','ALL')->first();
            $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
        }
        $incoterm = Incoterm::pluck('name','id');

        $emaildimanicdata = json_encode([
            'quote_bool'   => 'true',
            'company_id'   => '',
            'contact_id'   => '',
            'quote_id'     => $quote->id
        ]);

        return view('quotes/edit', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                    'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'exchange'=>$exchange,'incoterm'=>$incoterm,'saleterms'=>$saleterms,'email_templates'=>$email_templates,'carriers'=>$carriers,'airports'=>$airports,'airlines'=>$airlines,'user'=>$user,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'emaildimanicdata' => $emaildimanicdata]);

    }


    public function store(Request $request){
        $rules = array(
            'pick_up_date' => 'required',
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
            $company_quote = $this->idPersonalizado();    //ID PERSONALIZADO
            $total_markup_origin=array_values( array_filter($input['origin_ammount_markup']) );
            $total_markup_freight=array_values( array_filter($input['freight_ammount_markup']) );
            $total_markup_destination=array_values( array_filter($input['destination_ammount_markup']) );
            $sum_markup_origin=array_sum($total_markup_origin);
            $sum_markup_freight=array_sum($total_markup_freight);
            $sum_markup_destination=array_sum($total_markup_destination);
            $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
            if(isset($request->validity_date)){
                $validation = explode('/',$request->validity_date);
                $since = $validation[0];
                $until = $validation[1];
            }else{
                $since = $request->validity;
                $until = $request->since_validity;
            }

            $request->request->add(['owner' => \Auth::id(),'company_user_id'=>\Auth::user()->company_user_id,'currency_id'=>$currency->currency_id,'total_markup_origin'=>$sum_markup_origin,'total_markup_freight'=>$sum_markup_freight,'total_markup_destination'=>$sum_markup_destination,'company_quote' => $company_quote,'since_validity'=>$since,'validity'=>$until]);

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

                    if (isset($origin_ammount_charge[$key]) && isset($origin_ammount_detail[$key]) && isset($origin_total_units[$key])
                        && isset($origin_ammount_currency[$key]) && isset($origin_ammount_price_per_unit[$key]) && isset($origin_total_ammount[$key])) {

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

                    if (isset($freight_ammount_charge[$key]) && isset($freight_ammount_detail[$key]) && isset($freight_total_units[$key])
                        && isset($freight_ammount_currency[$key]) && isset($freight_ammount_price_per_unit[$key]) && isset($freight_total_ammount[$key])){

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

                    if (isset($destination_ammount_charge[$key]) && isset($destination_ammount_detail[$key]) && isset($destination_ammount_units[$key])
                        && isset($destination_ammount_currency[$key]) && isset($destination_ammount_price_per_unit[$key]) && isset($destination_total_ammount[$key])) {

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
            }
            if(isset($input['schedule'])){
                if($input['schedule'] != 'null'){
                    $schedules = json_decode($input['schedule']);
                    foreach( $schedules as $schedule){
                        $sche = json_decode($schedule);
                        $dias = $this->dias_transcurridos($sche->eta,$sche->etd);
                        $saveSchedule  = new Schedule();
                        $saveSchedule->vessel = $sche->vessel;
                        $saveSchedule->etd = $sche->etd;
                        $saveSchedule->transit_time =  $dias;
                        $saveSchedule->eta = $sche->eta;
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
                    $saveSchedule->vessel = $sche->vessel;
                    $saveSchedule->etd = $sche->etd;
                    $saveSchedule->transit_time =  $dias;
                    $saveSchedule->eta = $sche->eta;
                    $saveSchedule->type = 'direct';
                    $saveSchedule->quotes()->associate($quote);
                    $saveSchedule->save();
                }
            }


            if(isset($input['lclAuto'])){
                $quantity = array_values(array_filter(json_decode($input['quantity'])));
                $type_cargo = array_values(array_filter(json_decode($input['type_load_cargo'])));
                $height = array_values(array_filter(json_decode($input['height'])));
                $width = array_values(array_filter(json_decode($input['width'])));
                $large = array_values(array_filter(json_decode($input['large'])));
                $weight = array_values(array_filter(json_decode($input['weight'])));
                $volume = array_values(array_filter(json_decode($input['volume'])));


            }else{
                $quantity = array_values( array_filter($input['quantity']) );
                $type_cargo = array_values( array_filter($input['type_load_cargo']) );
                $height = array_values( array_filter($input['height']) );
                $width = array_values( array_filter($input['width']) );
                $large = array_values( array_filter($input['large']) );
                $weight = array_values( array_filter($input['weight']) );
                $volume = array_values( array_filter($input['volume']) );
            }


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
                return redirect()->route('quotes.show', ['quote_id' => setearRouteKey($quote->id)])->with('pdf','true');
            }


            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Register completed successfully!');
            //return redirect()->route('quotes.index');
            return redirect()->action('QuoteController@show',setearRouteKey($quote->id));
        }
    }

    public function storeWithEmail(Request $request)
    {
        $rules = array(
            'pick_up_date' => 'required',
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

            $company_quote = $this->idPersonalizado();
            $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();

            $total_markup_origin=array_values( array_filter($input['origin_ammount_markup']) );
            $total_markup_freight=array_values( array_filter($input['freight_ammount_markup']) );
            $total_markup_destination=array_values( array_filter($input['destination_ammount_markup']) );
            $sum_markup_origin=array_sum($total_markup_origin);
            $sum_markup_freight=array_sum($total_markup_freight);
            $sum_markup_destination=array_sum($total_markup_destination);
            if(isset($request->validity_date)){
                $validation = explode('/',$request->validity_date);
                $since = $validation[0];
                $until = $validation[1];
            }else{
                $since = $request->validity;
                $until = $request->since_validity;
            }
            $request->request->add(['owner' => \Auth::id(),'company_user_id'=>\Auth::user()->company_user_id,'currency_id'=>$currency->currency_id,'total_markup_origin'=>$sum_markup_origin,'total_markup_freight'=>$sum_markup_freight,'total_markup_destination'=>$sum_markup_destination,'status_quote_id'=>2,'company_quote'=>$company_quote,'since_validity'=>$since,'validity'=>$until]);

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
                        $dias = $this->dias_transcurridos($sche->eta,$sche->etd);
                        $saveSchedule  = new Schedule();
                        $saveSchedule->vessel = $sche->vessel;
                        $saveSchedule->etd = $sche->etd;
                        $saveSchedule->transit_time =  $dias;
                        $saveSchedule->eta = $sche->eta;
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
                    $dias = $this->dias_transcurridos($sche->eta,$sche->etd);
                    $saveSchedule  = new Schedule();
                    $saveSchedule->vessel = $sche->vessel;
                    $saveSchedule->etd = $sche->etd;
                    $saveSchedule->transit_time =  $dias;
                    $saveSchedule->eta = $sche->eta;
                    $saveSchedule->type = 'direct';
                    $saveSchedule->quotes()->associate($quote);
                    $saveSchedule->save();
                }
            }
            if(isset($input['lclAuto'])){
                $quantity = array_values(array_filter(json_decode($input['quantity'])));
                $type_cargo = array_values(array_filter(json_decode($input['type_load_cargo'])));
                $height = array_values(array_filter(json_decode($input['height'])));
                $width = array_values(array_filter(json_decode($input['width'])));
                $large = array_values(array_filter(json_decode($input['large'])));
                $weight = array_values(array_filter(json_decode($input['weight'])));
                $volume = array_values(array_filter(json_decode($input['volume'])));


            }else{
                $quantity = array_values( array_filter($input['quantity']) );
                $type_cargo = array_values( array_filter($input['type_load_cargo']) );
                $height = array_values( array_filter($input['height']) );
                $width = array_values( array_filter($input['width']) );
                $large = array_values( array_filter($input['large']) );
                $weight = array_values( array_filter($input['weight']) );
                $volume = array_values( array_filter($input['volume']) );
            }
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

            //Sending email with pdf attached
            if(isset($input['subject']) && isset($input['body']) && isset($input['addresse'])){
                $subject = $input['subject'];
                $body = $input['body'];
                $to = $input['addresse'];
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
                $package_loads = PackageLoad::where('quote_id',$quote->id)->get();
                $company = Company::find($quote->company_id);
                if(\Auth::user()->company_user_id){
                    $company_user=CompanyUser::find(\Auth::user()->company_user_id);
                    $currency_cfg = Currency::find($company_user->currency_id);
                    $type=$company_user->type_pdf;
                    $ammounts_type=$company_user->pdf_ammounts;
                    $port_all = harbor::where('name','ALL')->first();
                    $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
                    })->get();
                    $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
                        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
                    })->get();
                    $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
                        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
                    })->get();
                }

                if($quote->pdf_language!='') {
                    if ($quote->pdf_language == 3) {
                        $view = \View::make('quotes.pdf.index-portuguese', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    } else if ($quote->pdf_language == 2) {
                        $view = \View::make('quotes.pdf.index-spanish', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    } else {
                        $view = \View::make('quotes.pdf.index', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    }
                }else if($company->pdf_language!=''){
                    if ($company->pdf_language == 3) {
                        $view = \View::make('quotes.pdf.index-portuguese', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    } else if ($company->pdf_language == 2) {
                        $view = \View::make('quotes.pdf.index-spanish', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    } else {
                        $view = \View::make('quotes.pdf.index', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    }
                }else{
                    if ($company_user->pdf_language == 1) {
                        $view = \View::make('quotes.pdf.index', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    } else if ($company_user->pdf_language == 2) {
                        $view = \View::make('quotes.pdf.index-spanish', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    } else {
                        $view = \View::make('quotes.pdf.index-portuguese', ['companies' => $companies, 'quote' => $quote, 'harbors' => $harbors, 'prices' => $prices, 'contacts' => $contacts, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'origin_ammounts' => $origin_ammounts, 'freight_ammounts' => $freight_ammounts, 'destination_ammounts' => $destination_ammounts, 'user' => $user, 'currency_cfg' => $currency_cfg, 'package_loads' => $package_loads, 'terms_origin' => $terms_origin, 'terms_destination' => $terms_destination, 'terms_all' => $terms_all, 'charges_type' => $type, 'ammounts_type' => $ammounts_type]);
                    }
                }

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');
                $explode=explode(';',$to);
                foreach($explode as $item) {
                    \Mail::to(trim($item))->bcc(\Auth::user()->email,\Auth::user()->name)->send(new SendQuotePdf($subject,$body,$quote,\Auth::user()->email));
                }
            }else{
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.title', 'Error!');
                $request->session()->flash('message.content', 'The email address or body of the email can not be empty');
                return redirect()->back();
            }
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Register completed successfully!');
            return redirect()->action('QuoteController@show',setearRouteKey($quote->id));
        }
    }


    function dias_transcurridos($fecha_i,$fecha_f)
    {
        $dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
        $dias 	= abs($dias); $dias = floor($dias);
        return intval($dias);
    }

    public function showWithPdf($id){
        $id = obtenerRouteKey($id);
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
            $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
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
                                    'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'user'=>$user,'status_quotes'=>$status_quotes,'exchange'=>$exchange,'email_templates'=>$email_templates,'package_loads'=>$package_loads,'pdf'=>$pdf,'terms_all'=>$terms_all]);
    }

    public function show($id)
    {
        $id = obtenerRouteKey($id);

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
            $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
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

        $emaildimanicdata = json_encode([
            'quote_bool'   => 'true',
            'company_id'   => '',
            'contact_id'   => '',
            'quote_id'     => $quote->id
        ]);
        return view('quotes/show', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                    'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
                                    'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg,'user'=>$user,'status_quotes'=>$status_quotes,'exchange'=>$exchange,'email_templates'=>$email_templates,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'emaildimanicdata' => $emaildimanicdata]);
    }

    public function update(Request $request, $id)
    {
        $input = Input::all();
        $quote = Quote::find($id);

        $total_markup_origin=array_values( array_filter($input['origin_ammount_markup']) );
        $total_markup_freight=array_values( array_filter($input['freight_ammount_markup']) );
        $total_markup_destination=array_values( array_filter($input['destination_ammount_markup']) );
        $sum_markup_origin=array_sum($total_markup_origin);
        $sum_markup_freight=array_sum($total_markup_freight);
        $sum_markup_destination=array_sum($total_markup_destination);
        $validation = explode('/',$request->validity_date);
        $since = $validation[0];
        $until = $validation[1];
        $custom_id='';
        if($request->custom_id!='' && $request->custom_id!==$quote->company_quote){
            $custom_id=$request->custom_id;
        }
        $request->request->add(['total_markup_origin'=>$sum_markup_origin,'total_markup_freight'=>$sum_markup_freight,'total_markup_destination'=>$sum_markup_destination,'since_validity'=>$since,'validity'=>$until,'custom_id'=>$custom_id]);
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
        }else{
            $quote->sub_total_origin=null;
            $quote->total_markup_origin=null;
            $quote->update();
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
        }else{
            $quote->sub_total_freight=null;
            $quote->total_markup_freight=null;
            $quote->update();
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
        }else{
            $quote->sub_total_destination=null;
            $quote->total_markup_destination=null;
            $quote->update();
        }

        if(isset($input['lclAuto'])){
            $quantity = array_values(json_decode($input['quantity']));
            $type_cargo = array_values(json_decode($input['type_load_cargo']));
            $height = array_values(json_decode($input['height']));
            $width = array_values(json_decode($input['width']));
            $large = array_values(json_decode($input['large']));
            $weight = array_values(json_decode($input['weight']));
            $volume = array_values(json_decode($input['volume']));


        }else{
            $quantity = array_values( array_filter($input['quantity']) );
            $type_cargo = array_values( array_filter($input['type_load_cargo']) );
            $height = array_values( array_filter($input['height']) );
            $width = array_values( array_filter($input['width']) );
            $large = array_values( array_filter($input['large']) );
            $weight = array_values( array_filter($input['weight']) );
            $volume = array_values( array_filter($input['volume']) );
        }


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
        //Deleting previous schedules
        Schedule::where('quote_id',$quote->id)->delete();

        if(isset($input['schedule'])){
            if($input['schedule'] != 'null'){
                $schedules = json_decode($input['schedule']);
                foreach( $schedules as $schedule){
                    $sche = json_decode($schedule);
                    $dias = $this->dias_transcurridos($sche->eta,$sche->etd);
                    $saveSchedule  = new Schedule();
                    $saveSchedule->vessel = $sche->vessel;
                    $saveSchedule->etd = $sche->etd;
                    $saveSchedule->transit_time =  $dias;
                    $saveSchedule->eta = $sche->eta;
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
                $dias = $this->dias_transcurridos($sche->eta,$sche->etd);
                $saveSchedule  = new Schedule();
                $saveSchedule->vessel = $sche->vessel;
                $saveSchedule->etd = $sche->etd;
                $saveSchedule->transit_time =  $dias;
                $saveSchedule->eta = $sche->eta;
                $saveSchedule->type = 'direct';
                $saveSchedule->quotes()->associate($quote);
                $saveSchedule->save();
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Quote updated successfully!');
        return redirect()->action('QuoteController@show', setearRouteKey($id));
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
        $terms = TermsPort::where('port_id',$id)->with('term')->whereHas('term', function($q)  {
            $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
        })->get();

        return json_encode($terms);
    }

    public function getContactEmail($id)
    {
        $contact = Contact::find($id);

        return $contact->email;
    }

    public function getQuoteTermsDual($origin_harbor,$destination_harbor)
    {
        $terms = TermsPort::where('port_id',$origin_harbor)->with('term')->whereHas('term', function($q)  {
            $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
        })->get();

        $terms_d = TermsPort::where('port_id',$destination_harbor)->with('term')->whereHas('term', function($q)  {
            $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
        })->get();

        $port_all = harbor::where('name','ALL')->first();
        $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
            $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
        })->get();

        $merged = $terms->merge($terms_d);
        $merge_all = $merged->merge($terms_all);

        return json_encode($merge_all);
    }
    public function getCompanyPayments($id)
    {
        $payments = Company::find($id);

        return $payments;
    }
    public function duplicate(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
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
        $quote_duplicate->company_user_id=\Auth::user()->company_user_id;
        $quote_duplicate->company_quote=$this->idPersonalizado();
        $quote_duplicate->incoterm=$quote->incoterm;
        $quote_duplicate->modality=$quote->modality;
        $quote_duplicate->currency_id=$quote->currency_id;
        $quote_duplicate->pick_up_date=$quote->pick_up_date;
        if($quote->validity){
            $quote_duplicate->validity=$quote->validity;
        }
        if($quote->since_validity){
            $quote_duplicate->since_validity=$quote->since_validity;
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
        if($quote->qty_45_hc){
            $quote_duplicate->qty_45_hc=$quote->qty_45_hc;
        }
        if($quote->qty_40_nor){
            $quote_duplicate->qty_40_nor=$quote->qty_40_nor;
        }
        if($quote->qty_20_reefer){
            $quote_duplicate->qty_20_reefer=$quote->qty_20_reefer;
        }
        if($quote->qty_40_reefer){
            $quote_duplicate->qty_40_reefer=$quote->qty_40_reefer;
        }
        if($quote->qty_40_hc_reefer){
            $quote_duplicate->qty_40_hc_reefer=$quote->qty_40_hc_reefer;
        }
        if($quote->qty_20_open_top){
            $quote_duplicate->qty_20_open_top=$quote->qty_20_open_top;
        }
        if($quote->qty_40_open_top){
            $quote_duplicate->qty_40_open_top=$quote->qty_40_open_top;
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
        if($quote->total_markup_origin){
            $quote_duplicate->total_markup_origin=$quote->total_markup_origin;
        }
        if($quote->total_markup_freight){
            $quote_duplicate->total_markup_freight=$quote->total_markup_freight;
        }
        if($quote->total_markup_destination){
            $quote_duplicate->total_markup_destination=$quote->total_markup_destination;
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
            return redirect()->action('QuoteController@show', setearRouteKey($quote_duplicate->id));
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
        // Notificaciones
        $userLogin  = auth()->user();
        //  $userLogin->notify(new SlackNotification('Ejemplo'));
        $idCompany = $userLogin->company_user_id;
        $users = User::where('company_user_id','=',$idCompany)->where('type','company')->orWhere('id','=',$userLogin->id)->get();
        $message = " ".$quote->status->name." the quote ".$quote->company_quote ;
        foreach ($users as $user) {
            $user->notify(new N_general($userLogin,$message));
        }
        // fin notificaciones
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

    public function scheduleManual($carrier,$orig_port,$dest_port,$date_pick)
    {
        $code_orig = $this->getHarborName($orig_port);
        $code_dest = $this->getHarborName($dest_port);
        $date = strtotime($date_pick);
        $carrier_name = Carrier::find($carrier);
        //dd($date);
        //$url = "http://schedules.cargofive.com/schedule/".$carrier."/".$code_orig->code."/".$code_dest->code;
        $access_token = $this->schedules->authentication();
        $data = $this->schedules->getSchedules($access_token->access_token,$carrier_name->name,$code_orig->code,$code_dest->code,$date);

        $schedules = Collection::make($data);

        $schedulesArr = new Collection();
        $schedulesFin = new Collection();
        if(!$schedules->isEmpty()){
            foreach($schedules['data'] as $schedules){

                $collectS = Collection::make($schedules);

                $days =  $this->dias_transcurridos($schedules->eta,$schedules->etd);

                $collectS->put('days',$days);
                if($schedules->route_type > 1){
                    $collectS->put('type','Scale');
                }else{
                    $collectS->put('type','Direct');
                }
                $schedulesArr->push($collectS);

            }
            $dateSchedule = strtotime($date);
            $dateSchedule =  date('Y-m-d',$dateSchedule);

            if(!$schedulesArr->isEmpty()){

                $schedulesArr =  $schedulesArr->where('etd','>=', $dateSchedule)->first();
                $schedulesFin->push($schedulesArr);
            }
        }

        return view('quotes.scheduleInfo',compact('code_orig','code_dest','schedulesFin'));
    }

    public function StoreWithPdf(Request $request)
    {
        // set API Endpoint and access key (and any options of your choice)
        $endpoint = 'live';
        $access_key = 'a0a9f774999e3ea605ee13ee9373e755';

        $input = Input::all();
        $company_quote = $this->idPersonalizado();
        $currency = CompanyUser::where('id',\Auth::user()->company_user_id)->first();

        $total_markup_origin=array_values( array_filter($input['origin_ammount_markup']) );
        $total_markup_freight=array_values( array_filter($input['freight_ammount_markup']) );
        $total_markup_destination=array_values( array_filter($input['destination_ammount_markup']) );
        $sum_markup_origin=array_sum($total_markup_origin);
        $sum_markup_freight=array_sum($total_markup_freight);
        $sum_markup_destination=array_sum($total_markup_destination);
        if(isset($request->validity_date)){
            $validation = explode('/',$request->validity_date);
            $since = $validation[0];
            $until = $validation[1];
        }else{
            $since = $request->validity;
            $until = $request->since_validity;
        }
        $request->request->add(['owner' => \Auth::id(),'company_user_id'=>\Auth::user()->company_user_id,'currency_id'=>$currency->currency_id,'total_markup_origin'=>$sum_markup_origin,'total_markup_freight'=>$sum_markup_freight,'total_markup_destination'=>$sum_markup_destination,'company_quote'=>$company_quote,'since_validity'=>$since,'validity'=>$until]);

        $quote=Quote::create($request->all());

        $package_loads = PackageLoad::where('quote_id',$quote->id)->get();

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
                    $dias = $this->dias_transcurridos($sche->eta,$sche->etd);
                    $saveSchedule  = new Schedule();
                    $saveSchedule->vessel = $sche->vessel;
                    $saveSchedule->etd = $sche->etd;
                    $saveSchedule->transit_time =  $dias;
                    $saveSchedule->eta = $sche->eta;
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
                $dias = $this->dias_transcurridos($sche->eta,$sche->etd);
                $saveSchedule  = new Schedule();
                $saveSchedule->vessel = $sche->vessel;
                $saveSchedule->etd = $sche->etd;
                $saveSchedule->transit_time =  $dias;
                $saveSchedule->eta = $sche->eta;
                $saveSchedule->type = 'direct';
                $saveSchedule->quotes()->associate($quote);
                $saveSchedule->save();
            }
        }

        if(isset($input['lclAuto'])){
            $quantity = array_values(json_decode($input['quantity']));
            $type_cargo = array_values(json_decode($input['type_load_cargo']));
            $height = array_values(json_decode($input['height']));
            $width = array_values(json_decode($input['width']));
            $large = array_values(json_decode($input['large']));
            $weight = array_values(json_decode($input['weight']));
            $volume = array_values(json_decode($input['volume']));


        }else{
            $quantity = array_values( array_filter($input['quantity']) );
            $type_cargo = array_values( array_filter($input['type_load_cargo']) );
            $height = array_values( array_filter($input['height']) );
            $width = array_values( array_filter($input['width']) );
            $large = array_values( array_filter($input['large']) );
            $weight = array_values( array_filter($input['weight']) );
            $volume = array_values( array_filter($input['volume']) );
        }


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
            $type=$company_user->type_pdf;
            $ammounts_type=$company_user->pdf_ammounts;
            $port_all = harbor::where('name','ALL')->first();
            $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
        }
        foreach($origin_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates_eur;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        foreach($freight_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        //dd(json_encode($item->markup/1.16));

        foreach($destination_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        if($quote->pdf_language!=''){
            if($quote->pdf_language==1){
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($quote->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }else if($quote->company->pdf_language!=''){
            if($quote->company->pdf_language==1){
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($quote->company->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }else{
            if($company_user->pdf_language==1){
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($company_user->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        //$pdf->download('quote');

        return redirect()->action('QuoteController@showWithPdf',setearRouteKey($quote->id));
    }

    public function idPersonalizado(){
        $user_company = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
        $iniciales =  strtoupper(substr($user_company->name,0, 2));
        $quote = Quote::where('company_user_id',$user_company->id)->orderBy('created_at', 'desc')->first();

        if($quote == null){
            $iniciales = $iniciales."-1";
        }else{
            $numeroFinal = explode('-',$quote->company_quote);

            $numeroFinal = $numeroFinal[1] +1;

            $iniciales = $iniciales."-".$numeroFinal;
        }
        return $iniciales;
    }

    public function searchAirports(Request $request){
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $airports = Airport::where('code','like',$term.'%')->get();
        if($airports->count() == '0'){
            $airports = Airport::where('name','like','%' . $term. '%')
                ->orWhere('code','like','%' . $term. '%')->get();
        }

        $formatted_airports = [];
        foreach ($airports as $airport) {
            $formatted_airports[] = ['id' => $airport->id, 'text' => $airport->display_name];
        }
        return \Response::json($formatted_airports);
    }
    public function updateCarrierVisibility(Request $request){
        $quote=Quote::find($request->quote_id);
        $quote->hide_carrier = $request->carrier_visibility;
        $quote->update();
        return response()->json(['message' => 'Ok']);
    }
    public function downloadQuotes(){
        //return Excel::download(new QuotesExport, 'quotes.xlsx');
        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = Quote::where('owner',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }else{
            $quotes = Quote::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $nameFile = str_replace([' '],'_',$now.'_quotes');
        Excel::create($nameFile, function($excel) use($nameFile, $quotes) {
            $excel->sheet('Quotes', function($sheet) use($quotes) {
                //dd($contract);
                $sheet->cells('A1:AO1', function($cells) {
                    $cells->setBackground('#2525ba');
                    $cells->setFontColor('#ffffff');
                    $cells->setValignment('center');
                });
                //$sheet->setBorder('A1:AO1', 'thin');
                $sheet->row(1, array(
                    'Id',
                    'Owner',
                    'Company quote',
                    'Incoterm',
                    'Validity',
                    'Since validity',
                    'Modality',
                    'Pick up date',
                    'Delivery type',
                    'Cargo type',
                    'Origin',
                    'Destination',
                    'Origin address',
                    'Destination address',
                    'Company',
                    'Contact',
                    'Currency',
                    'Carrier',
                    'Container 20',
                    'Container 40',
                    'Container 40 HC',
                    'Container 45 HC',
                    'Container 40 NOR',
                    'Container 20 Reefer',
                    'Container 40 Reefer',
                    'Container 40 HC Reefer',
                    'Container 20 Open Top',
                    'Container 40 Open Top',
                    'PDF language',
                    'Total quantity',
                    'Total weight',
                    'Total volume',
                    'Chargeable weight',
                    'Sub total origin',
                    'Sub total freight',
                    'Sub total destination',
                    'Total markup origin',
                    'Total markup freight',
                    'Total markup destination',
                    'Status',
                    'Created at',
                ));
                $i=2;
                foreach($quotes as $quote) {
                    if ($quote->origin_harbor) {
                        $origin = $quote->origin_harbor->display_name;
                    } elseif ($quote->origin_airport) {
                        $origin = $quote->origin_airport->name;
                    } else {
                        $origin = $quote->origin_address;
                    }
                    if ($quote->destination_harbor) {
                        $destination = $quote->destination_harbor->display_name;
                    } elseif ($quote->destination_airport) {
                        $destination = $quote->destination_airport->name;
                    } else {
                        $destination = $quote->destination_address;
                    }
                    if ($quote->pdf_language == 1) {
                        $pdf_language = 'English';
                    } elseif ($quote->pdf_language == 2) {
                        $pdf_language = 'Spanish';
                    } elseif ($quote->pdf_language == 3) {
                        $pdf_language = 'Portuguese';
                    } else {
                        $pdf_language = 'English';
                    }
                    if ($quote->type_cargo == 1) {
                        $cargo_type = 'FCL';
                    } elseif ($quote->type_cargo == 2) {
                        $cargo_type = 'LCL';
                    } else {
                        $cargo_type = 'AIR';
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
                    if ($quote->carrier_id != '') {
                        $carrier = $quote->carrier->name;
                    } else {
                        $carrier = '';
                    }
                    if ($quote->modality == 1) {
                        $modality = 'Export';
                    } else {
                        $modality = 'Import';
                    }
                    if ($quote->incoterm == 1) {
                        $incoterm = 'EWX';
                    } elseif ($quote->incoterm == 2) {
                        $incoterm = 'FAS';
                    } elseif ($quote->incoterm == 3) {
                        $incoterm = 'FCA';
                    } elseif ($quote->incoterm == 4) {
                        $incoterm = 'FOB';
                    } elseif ($quote->incoterm == 5) {
                        $incoterm = 'CFR';
                    } elseif ($quote->incoterm == 6) {
                        $incoterm = 'CIF';
                    } elseif ($quote->incoterm == 7) {
                        $incoterm = 'CIP';
                    } elseif ($quote->incoterm == 8) {
                        $incoterm = 'DAT';
                    } elseif ($quote->incoterm == 9) {
                        $incoterm = 'DAP';
                    } elseif ($quote->incoterm == 10) {
                        $incoterm = 'DDP';
                    }
                    $sheet->row($i, array(
                        "Id" => $quote->id,
                        "Owner" => $quote->user->name.' '.$quote->user->lastname,
                        "Company quote" => $quote->company_quote,
                        "Incoterm" => $incoterm,
                        "Validity" => $quote->validity,
                        "Since validity" => $quote->since_validity,
                        "Modality" => $modality,
                        "Pick up date" => $quote->pick_up_date,
                        "Delivery type" => $delivery_type,
                        "Cargo type" => $cargo_type,
                        "Origin" => $origin,
                        "Destination" => $destination,
                        "Origin address" => $quote->origin_address,
                        "Destination address" => $quote->destination_address,
                        "Client company" => $quote->company->business_name,
                        "Contact" => $quote->contact->first_name . ' ' . $quote->contact->last_name,
                        "Currency" => $quote->currencies->alphacode,
                        "Carrier" => $carrier,
                        "Container 20" => $quote->qty_20,
                        "Container 40" => $quote->qty_40,
                        "Container 40 HC" => $quote->qty_40_hc,
                        "Container 45 HC" => $quote->qty_45_hc,
                        "Container 40 NOR" => $quote->qty_40_nor,
                        "Container 20 Reefer" => $quote->qty_20_reefer,
                        "Container 40 Reefer" => $quote->qty_40_reefer,
                        "Container 40 HC Reefer" => $quote->qty_40_hc_reefer,
                        "Container 20 Open Top" => $quote->qty_20_open_top,
                        "Container 40 Open Top" => $quote->qty_40_open_top,
                        "Pdf language" => $pdf_language,
                        "Quantity" => $quote->total_quantity,
                        "Weight" => $quote->total_weight,
                        "Volume" => $quote->total_volume,
                        "Chargeable weight" => $quote->chargeable_weight,
                        "Sub total origin" => $quote->sub_total_origin,
                        "Sub total freight" => $quote->sub_total_freight,
                        "Sub total destination" => $quote->sub_total_destination,
                        "Total markup origin" => $quote->total_markup_origin,
                        "Total markup freight" => $quote->total_markup_freight,
                        "Total markup destination" => $quote->total_markup_destination,
                        "Status" => $quote->status->name,
                        "Created at" => $quote->created_at,
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
}