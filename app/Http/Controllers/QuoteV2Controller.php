<?php

namespace App\Http\Controllers;

use App\Airline;
use App\Airport;
use App\AutomaticInland;
use App\AutomaticRateTotal;
use App\InlandAddress;
use App\AutomaticInlandLclAir;
use App\AutomaticInlandTotal;
use App\AutomaticRate;
use App\CalculationType;
use App\CalculationTypeLcl;
use App\Carrier;
use App\Charge;
use App\ChargeLclAir;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Container;
use App\ContainerCalculation;
use App\Contract;
use App\ContractFclFile;
use App\ContractLclFile;
use App\Country;
use App\Currency;
use App\Direction;
use App\EmailTemplate;
use App\GlobalCharCarrier;
use App\GlobalCharge;
use App\GlobalChargeLcl;
use App\GroupContainer;
use App\Harbor;
use App\Http\Requests\SearchRate as SearchRateForm;
use App\Http\Requests\StoreAddRatesQuotes;
use App\Http\Traits\QuoteV2Trait;
use App\Http\Traits\SearchTrait;
use App\Incoterm;
use App\Inland;
use App\InlandDistance;
use App\Jobs\UpdatePdf;
use App\LocalCharge;
use App\LocalChargeApi;
use App\LocalChargeLcl;
use App\NewContractRequest;
use App\NewContractRequestLcl;
use App\PackageLoadV2;
use App\PdfOption;
use App\Price;
use App\Quote;
use App\QuoteV2;
use App\Rate;
use App\RateLcl;
use App\RemarkCountry;
use App\RemarkHarbor;
use App\SaleTermV2;

//LCL
use App\Schedule;
use App\SearchPort;
use App\SearchRate;
use App\Surcharge;
use App\TermAndConditionV2;
use App\TermsPort;
use App\User;
use App\ViewQuoteV2;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\MediaLibrary\MediaStream;
use Spatie\MediaLibrary\Models\Media;
use Yajra\DataTables\DataTables;

class QuoteV2Controller extends Controller
{

    use QuoteV2Trait;
    use SearchTrait;

    protected $pdf_language = 'English';

    public function __construct()
    {
        //
    }

    /**
     * Show quotes list
     * @param Request $request
     * @return Illuminate\View\View
     */
    public function newSearch(Request $request){
        return view('searchV2.index');
    }

    public function index(Request $request)
    {

        $company_user = null;
        $currency_cfg = null;
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $quotes = QuoteV2::where('user_id', \Auth::user()->id)->whereHas('user', function ($q) use ($company_user_id) {
                $q->where('company_user_id', '=', $company_user_id);
            })->orderBy('created_at', 'desc')->with(['rates_v2' => function ($query) {
                $query->with('origin_port', 'destination_port', 'origin_airport', 'destination_airport', 'currency', 'charge', 'charge_lcl_air');
            }])->get();
        } else {
            $quotes = QuoteV2::whereHas('user', function ($q) use ($company_user_id) {
                $q->where('company_user_id', '=', $company_user_id);
            })->orderBy('created_at', 'desc')->with(['rates_v2' => function ($query) {
                $query->with('origin_port', 'destination_port', 'origin_airport', 'destination_airport', 'currency', 'charge', 'charge_lcl_air');
            }])->get();
        }
        $companies = Company::pluck('business_name', 'id');
        $harbors = Harbor::pluck('display_name', 'id');
        $countries = Country::pluck('name', 'id');
        if (\Auth::user()->company_user_id) {
            $company_user = CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        if ($request->ajax()) {
            $quotes->load('user', 'company', 'contact', 'incoterm');
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

        return view('quotesv2/index', ['companies' => $companies, 'quotes' => $quotes, 'countries' => $countries, 'harbors' => $harbors, 'currency_cfg' => $currency_cfg]);
    }

    public function LoadDatatableIndex()
    {

        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $quotes = ViewQuoteV2::where('user_id', \Auth::user()->id)->orderBy('created_at', 'desc')->get();
        } else {
            $quotes = ViewQuoteV2::where('company_user_id', $company_user_id)->orderBy('created_at', 'desc')->get();
        }

        $colletions = collect([]);
        foreach ($quotes as $quote) {
            $custom_id = '---';
            $company = '---';
            $origin = '';
            $destination = '';
            $origin_li = '';
            $destination_li = '';

            if (isset($quote->company)) {
                $company = $quote->company->business_name;
            }

            if ($quote->custom_quote_id != '') {
                $id = $quote->custom_quote_id;
            } else {
                $id = $quote->quote_id;
            }

            if ($quote->type == 'AIR') {
                $origin = $quote->origin_airport;
                $destination = $quote->destination_airport;
                $img = '<img src="/images/plane-blue.svg" class="img img-responsive" width="25">';
            } else {
                $origin = $quote->origin_port;
                $destination = $quote->destination_port;
                $img = '<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
            }

            $explode_orig = explode("| ", $origin);
            $explode_dest = explode("| ", $destination);

            foreach ($explode_orig as $item) {
                $origin_li .= '<li>' . $item . '</li>';
            }

            foreach ($explode_dest as $item) {
                $destination_li .= '<li>' . $item . '</li>';
            }

            if ($quote->business_name != '') {
                $company = $quote->business_name;
            } else {
                $company = '---';
            }

            if ($quote->contact != '') {
                $contact = $quote->contact;
            } else {
                $contact = '---';
            }

            $ValueOrig = count($explode_orig);
            $valueDest = count($explode_dest);

            if ($ValueOrig == 1 && $valueDest == 1) {

                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => $origin_li,
                    'destination' => $destination_li,
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            } elseif ($ValueOrig != 1 && $valueDest == 1) {

                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See origins
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $origin_li . '</small>
                                      </div>',
                    'destination' => $destination_li,
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            } elseif ($ValueOrig == 1 && $valueDest != 1) {

                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => $origin_li,
                    'destination' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See destinations
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $destination_li . '</small>
                                      </div>',
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            } else {

                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See origins
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $origin_li . '</small>
                                      </div>',
                    'destination' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See destinations
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $destination_li . '</small>
                                      </div>',
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            }
        }

        return DataTables::of($colletions)
            ->editColumn('created', function ($colletion) {
                return [
                    'display' => e($colletion['created']->format('M d, Y H:i')),
                    'timestamp' => $colletion['created']->timestamp,
                ];
            })
            ->addColumn('action', function ($colletion) {
                return
                    '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Options
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
          <a target="_blank" class="dropdown-item" href="/v2/quotes/show/' . $colletion['idSet'] . '">
          <span>
          <i class="la la-edit"></i>
          &nbsp;
          Edit
          </span>
          </a>
          <a target="_blank" class="dropdown-item" href="/v2/quotes/pdf/' . $colletion['idSet'] . '">
          <span>
          <i class="la la-file"></i>
          &nbsp;
          PDF
          </span>
          </a>
          <a href="/v2/quotes/duplicate/' . $colletion['idSet'] . '" class="dropdown-item" >
          <span>
          <i class="la la-plus"></i>
          &nbsp;
          Duplicate
          </span>
          </a>
          <a href="#" class="dropdown-item" id="delete-quote-v2" data-quote-id="' . $colletion['idSet'] . '" >
          <span>
          <i class="la la-eraser"></i>
          &nbsp;
          Delete
          </span>
          </a>
          </div>';
            })->editColumn('id', '{{$id}}')->make(true);

        /*return DataTables::of($colletions)
    ->editColumn('created', function ($colletion) {
    return [
    'display' => e($colletion['created']->format('M d, Y H:i')),
    'timestamp' => $colletion['created']->timestamp,
    ];
    })
    ->addColumn('action', function ($colletion) {
    return
    '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Options
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
    <a target="_blank" class="dropdown-item" href="/api/quote/' . obtenerRouteKey($colletion['idSet']) . '/edit">
    <span>
    <i class="la la-edit"></i>
    &nbsp;
    Edit
    </span>
    </a>
    <a target="_blank" class="dropdown-item" href="/v2/quotes/pdf/' . $colletion['idSet'] . '">
    <span>
    <i class="la la-file"></i>
    &nbsp;
    PDF
    </span>
    </a>
    <a href="#" class="dropdown-item" id="duplicate-quote-v2" data-quote-id="' . obtenerRouteKey($colletion['idSet']) . '" >
    <span>
    <i class="la la-plus"></i>
    &nbsp;
    Duplicate
    </span>
    </a>
    <a href="#" class="dropdown-item" id="delete-quote-v2" data-quote-id="' . obtenerRouteKey($colletion['idSet']) . '" >
    <span>
    <i class="la la-eraser"></i>
    &nbsp;
    Delete
    </span>
    </a>
    </div>';
    })->editColumn('id', '{{$id}}')->make(true);*/
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
        $containers = Container::get();
        $type = $request->type;
        $status = $request->status;
        $integration = $request->integration;

        //Retrieving all data
        $company_user = CompanyUser::find(\Auth::user()->company_user_id);
        if ($company_user->companyUser) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
        }

        $company_user_id = \Auth::user()->company_user_id;

        $quote = QuoteV2::when($type, function ($query, $type) {
            return $query->where('type', $type);
        })->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })->when($integration, function ($query, $integration) {
            return $query->whereHas('integration', function ($q) {
                $q->where('status', 0);
            });
        })->with(['rates_v2' => function ($query) {
            $query->with('origin_airport', 'destination_airport', 'currency', 'carrier', 'airline');
            $query->with(['origin_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                $q->with('country');
            }]);
            $query->with(['destination_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                $q->with('country');
            }]);
            $query->with(['charge' => function ($q) {
                $q->with('type', 'surcharge', 'calculation_type', 'currency');
            }]);
            $query->with(['charge_lcl_air' => function ($q) {
                $q->with('type', 'surcharge', 'calculation_type', 'currency');
            }]);
            $query->with('inland');
            $query->with('automaticInlandLclAir');
        }])->with(['user' => function ($query) {
            $query->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state', 'company_user_id');
            $query->with(['companyUser' => function ($q) {
                $q->select('id', 'name', 'address', 'phone', 'currency_id');
                $q->with('currency');
            }]);
        }])->with(['company' => function ($query) {
            $query->with(['company_user' => function ($q) {
                $q->select('id', 'name', 'address', 'phone', 'currency_id');
                $q->with('currency');
            }]);
            $query->with(['owner' => function ($q) {
                $q->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state');
            }]);
        }])->with(['contact' => function ($query) {
            $query->with(['company' => function ($q) {
                $q->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number');
            }]);
        }])->with(['price' => function ($q) {
            $q->select('id', 'name', 'description');
        }])->with(['saleterm' => function ($q) {
            $q->with('charge');
        }])->with('incoterm')->findOrFail($id);

        /*$quote = QuoteV2::ConditionalWhen($type, $status, $integration)
        ->AuthUserCompany($company_user_id)
        ->AutomaticRate()->UserRelation()->CompanyRelation()
        ->ContactRelation()->PriceRelation()->SaletermRelation()
        ->with('incoterm')->findOrFail($id);*/

        $package_loads = PackageLoadV2::where('quote_id', $quote->id)->get();
        $inlands = AutomaticInland::where('quote_id', $quote->id)->get();
        $harbors = Harbor::get()->pluck('display_name', 'id');
        $countries = Country::pluck('name', 'id');
        $carrierMan = Carrier::pluck('name', 'id');
        $airlines = Airline::pluck('name', 'id');
        $companies = Company::where('company_user_id', $company_user_id)->pluck('business_name', 'id');
        $contacts = Contact::where('company_id', $quote->company_id)->pluck('first_name', 'id');
        $incoterms = Incoterm::pluck('name', 'id');
        $users = User::where('company_user_id', $company_user_id)->pluck('name', 'id');
        $prices = Price::where('company_user_id', $company_user_id)->pluck('name', 'id');
        $currencies = Currency::pluck('alphacode', 'id');
        $calculation_types = CalculationType::pluck('name', 'id');
        $calculation_types_lcl_air = CalculationTypeLcl::pluck('name', 'id');
        $surcharges = Surcharge::where('company_user_id', \Auth::user()->company_user_id)->orwhere('company_user_id', null)->orderBy('name', 'Asc')->pluck('name', 'id');
        $email_templates = EmailTemplate::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
        $equipmentHides = $this->hideContainerV2($quote->equipment, 'BD', $containers);
        $sale_terms = SaleTermV2::where('quote_id', $quote->id)->get();
        $sale_terms_origin = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Origin')->with('charge')->get();
        $sale_terms_destination = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Destination')->with('charge')->get();

        if ($quote->delivery_type == 2 || $quote->delivery_type == 4) {
            $destinationAddressHides = null;
        }

        if ($quote->delivery_type == 3 || $quote->delivery_type == 4) {
            $originAddressHides = null;
        }

        //Get ports with sale terms
        $sale_terms_origin = $this->processSaleTerms($sale_terms_origin, $quote, $company_user, 'origin');
        $sale_terms_destination = $this->processSaleTerms($sale_terms_destination, $quote, $company_user, 'destination');

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
        $port_origin_ids = $quote->rates_v2->implode('origin_port_id', ', ');
        $port_origin_ids = explode(",", $port_origin_ids);
        $port_destination_ids = $quote->rates_v2->implode('destination_port_id', ', ');
        $port_destination_ids = explode(",", $port_destination_ids);
        $rate_origin_ports = Harbor::whereIn('id', $port_origin_ids)->whereNotIn('id', $origin_sales)->pluck('display_name', 'id');
        $rate_destination_ports = Harbor::whereIn('id', $port_destination_ids)->whereNotIn('id', $destination_sales)->pluck('display_name', 'id');

        //Airports when saleterms
        $airport_origin_ids = $quote->rates_v2->implode('origin_airport_id', ', ');
        $airport_origin_ids = explode(",", $airport_origin_ids);
        $airport_destination_ids = $quote->rates_v2->implode('destination_airport_id', ', ');
        $airport_destination_ids = explode(",", $airport_destination_ids);
        $rate_origin_airports = Airport::whereIn('id', $airport_origin_ids)->whereNotIn('id', $origin_sales)->pluck('display_name', 'id');
        $rate_destination_airports = Airport::whereIn('id', $airport_destination_ids)->whereNotIn('id', $destination_sales)->pluck('display_name', 'id');

        $hideO = 'hide';
        $hideD = 'hide';
        $rates = $quote->rates_v2;

        $this->processShowQuoteRates($rates, $company_user, $containers);

        if (!$quote->rates_v2->isEmpty()) {
            foreach ($quote->rates_v2 as $item) {
                $quote->rates_v2->map(function ($item) {
                    if ($item->origin_port_id != '') {
                        $item['origin_country_code'] = strtolower(substr(@$item->origin_port->code, 0, 2));
                    } else {
                        $item['origin_country_code'] = strtolower(@$item->origin_airport->code);
                    }
                    if ($item->destination_port_id != '') {
                        $item['destination_country_code'] = strtolower(substr(@$item->destination_port->code, 0, 2));
                    } else {
                        $item['destination_country_code'] = strtolower(@$item->destination_airport->code);
                    }

                    return $item;
                });
            }
        }

        if (!$sale_terms->isEmpty()) {
            foreach ($sale_terms as $v) {
                $sale_terms->map(function ($v) {
                    if ($v->port_id != '') {
                        $v['country_code'] = strtolower(substr(@$v->port->code, 0, 2));
                    } else {
                        $v['country_code'] = strtolower(@$v->airport->code);
                    }
                    return $v;
                });
            }
        }

        $emaildimanicdata = json_encode([
            'quote_bool' => 'true',
            'company_id' => '',
            'contact_id' => '',
            'quote_id' => $id,
        ]);

        if ($request->ajax()) {
            $quote->load('user', 'company', 'contact', 'incoterm');
            $collection = Collection::make($quote);
            return $collection;
        }

        return view('quotesv2/show', compact('quote', 'containers', 'companies', 'company_user', 'incoterms', 'users', 'prices', 'contacts', 'currencies', 'equipmentHides', 'freight_charges', 'origin_charges', 'destination_charges', 'calculation_types', 'calculation_types_lcl_air', 'rates', 'surcharges', 'email_templates', 'inlands', 'emaildimanicdata', 'package_loads', 'countries', 'harbors', 'prices', 'airlines', 'carrierMan', 'currency_name', 'hideO', 'hideD', 'sale_terms', 'rate_origin_ports', 'rate_destination_ports', 'rate_origin_airports', 'rate_destination_airports', 'destinationAddressHides', 'originAddressHides'));
    }

    /**
     * Update charges by rate
     * @param Request $request
     * @return array json
     */
    public function updateRateCharges(Request $request)
    {
        $charge = AutomaticRate::find($request->pk);
        $name = explode("->", $request->name);
        if (strpos($request->name, '->') == true) {
            if ($name[0] == 'rates') {
                $array = json_decode($charge->rates, true);
            } else {
                $array = json_decode($charge->markups, true);
            }
            $field = (string) $name[0];
            $array[$name[1]] = $request->value;
            $array = json_encode($array);
            $charge->$field = $array;
        } else {
            $name = $request->name;
            $charge->$name = $request->value;
        }
        $charge->update();
        //$this->updatePdfApi($charge->quote_id);
        $this->updateIntegrationQuoteStatus($charge->quote_id);
        return response()->json(['success' => 'Ok']);
    }

    /**
     * Update charges
     * @param Request $request
     * @return array json
     */
    public function updateQuoteCharges(Request $request)
    {
        $charge = Charge::find($request->pk);
        $name = explode("->", $request->name);
        //$value = str_replace(",", ".", $request->value);
        $value = $this->tofloat($request->value);

        if (strpos($request->name, '->') == true) {
            if ($name[0] == 'amount') {
                $array = json_decode($charge->amount, true);
            } else {
                $array = json_decode($charge->markups, true);
            }

            foreach ($array as $key => $arr) {
                if ($key == 'c20' && $name[1] == 'c20DV') {
                    $name[1] = 'c20';
                } elseif ($key == 'c40' && $name[1] == 'c40DV') {
                    $name[1] = 'c40';
                } elseif ($key == 'c40hc' && $name[1] == 'c40HC') {
                    $name[1] = 'c40hc';
                } elseif ($key == 'c40nor' && $name[1] == 'c40NOR') {
                    $name[1] = 'c40nor';
                } elseif ($key == 'm20' && $name[1] == 'm20DV') {
                    $name[1] = 'm20';
                } elseif ($key == 'm40' && $name[1] == 'm40DV') {
                    $name[1] = 'm40';
                } elseif ($key == 'm40hc' && $name[1] == 'm40HC') {
                    $name[1] = 'm40hc';
                } elseif ($key == 'm40nor' && $name[1] == 'm40NOR') {
                    $name[1] = 'm40nor';
                } elseif ($key == 'm45hc' && $name[1] == 'm45HC') {
                    $name[1] = 'm45hc';
                }
            }

            $field = (string) $name[0];
            $array[$name[1]] = $value;
            $array = json_encode($array);
            $charge->$field = $array;
        } else {
            $name = $request->name;
            $charge->$name = $value;
        }
        $charge->update();
        if ($charge->surcharge_id == '') {
            AutomaticRate::find($charge->automatic_rate_id)->update(['currency_id' => $charge->currency_id]);
        }
        $quote_id = $charge->automatic_rate->quote_id;
        //$this->updatePdfApi($quote_id);
        $this->updateIntegrationQuoteStatus($quote_id);
        return response()->json(['success' => 'Ok']);
    }

    /**
     * Update LCL Quotes info
     * @param Request $request
     * @return array json
     */
    public function updateQuoteInfo(Request $request)
    {
        if ($request->value) {
            $quote = QuoteV2::find($request->pk);
            $name = $request->name;
            if ($name == 'total_weight' || $name == 'total_volume' || $name == 'chargeable_weight') {
                $value = $this->tofloat($request->value);
                $quote->$name = $value;
            } else {
                $quote->$name = $request->value;
            }
            $quote->update();
            //$this->updatePdfApi($quote->id);
        }
        return response()->json(['success' => 'Ok']);
    }

    /**
     * Update inland's charges
     * @param Request $request
     * @return array json
     */
    public function updateInlandCharges(Request $request)
    {
        $charge = AutomaticInland::find($request->pk);
        $name = explode("->", $request->name);
        $amount = $this->tofloat($request->value);

        if (strpos($request->name, '->') == true) {
            if ($name[0] == 'rate') {
                $array = json_decode($charge->rate, true);
            } else {
                $array = json_decode($charge->markup, true);
            }

            foreach ($array as $key => $arr) {
                if ($key == 'c20' && $name[1] == 'c20DV') {
                    $name[1] = 'c20';
                } elseif ($key == 'c40' && $name[1] == 'c40DV') {
                    $name[1] = 'c40';
                } elseif ($key == 'c40hc' && $name[1] == 'c40HC') {
                    $name[1] = 'c40hc';
                } elseif ($key == 'c40nor' && $name[1] == 'c40NOR') {
                    $name[1] = 'c40nor';
                } elseif ($key == 'm20' && $name[1] == 'm20DV') {
                    $name[1] = 'm20';
                } elseif ($key == 'm40' && $name[1] == 'm40DV') {
                    $name[1] = 'm40';
                } elseif ($key == 'm40hc' && $name[1] == 'm40HC') {
                    $name[1] = 'm40hc';
                } elseif ($key == 'm40nor' && $name[1] == 'm40NOR') {
                    $name[1] = 'm40nor';
                } elseif ($key == 'm45hc' && $name[1] == 'm45HC') {
                    $name[1] = 'm45hc';
                }
            }

            $field = (string) $name[0];
            $array[$name[1]] = $amount;
            $array = json_encode($array);
            $charge->$field = $array;
        } else {
            $name = $request->name;
            $charge->$name = $amount;
        }
        $charge->update();
        return response()->json(['success' => 'Ok']);
    }

    /**
     * Update inland's charges LCL/AIR
     * @param Request $request
     * @return array json
     */
    public function updateInlandChargeLcl(Request $request)
    {
        $charge = AutomaticInlandLclAir::find($request->pk);
        $name = $request->name;
        $charge->$name = $request->value;

        $charge->update();
        return response()->json(['success' => 'Ok']);
    }

    //Actualiza Cargos por rate en LCL y Aereo
    public function updateQuoteChargesLcl(Request $request)
    {
        $charge = ChargeLclAir::find($request->pk);
        $name = $request->name;
        $charge->$name = $request->value;
        $charge->update();
        return response()->json(['success' => 'Ok']);
    }

    /**
     * Update Quote's data
     * @param Request $request
     * @param integer $id
     * @return array json
     */
    public function update(Request $request, $id)
    {

        $validation = explode('/', $request->validity);
        $validity_start = $validation[0];
        $validity_end = $validation[1];
        $contact_name = '';
        $price_name = '';
        $gdp = 'No';

        $quote = QuoteV2::find($id);
        if ($quote->quote_id != $request->quote_id) {
            $quote->custom_quote_id = $request->quote_id;
        } else {
            $quote->custom_quote_id = '';
        }
        $quote->type = $request->type;
        $quote->company_id = $request->company_id;
        $quote->contact_id = $request->contact_id;
        $quote->delivery_type = $request->delivery_type;
        $quote->date_issued = $request->date_issued;
        $quote->incoterm_id = $request->incoterm_id;
        if ($request->equipment != '') {
            $quote->equipment = json_encode($request->equipment);
        }
        $quote->validity_start = $validity_start;
        $quote->validity_end = $validity_end;
        $quote->price_id = $request->price_id;
        $quote->user_id = $request->user_id;
        $quote->kind_of_cargo = $request->kind_of_cargo;
        $quote->commodity = $request->commodity;
        $quote->status = $request->status;
        $quote->gdp = $request->gdp;
        $quote->risk_level = $request->risk_level;
        $quote->origin_address = $request->origin_address;
        $quote->destination_address = $request->destination_address;
        $quote->update();
        //$this->updatePdfApi($quote->id);

        if ($request->contact_id != '') {
            $contact_name = $quote->contact->first_name . ' ' . $quote->contact->last_name;
        }

        if ($quote->gdp == 1) {
            $gdp = 'Yes';
        }

        if ($request->price_id != '') {
            $price_name = $quote->price->name;
        }

        $owner = $quote->user->name . ' ' . $quote->user->lastname;
        if ($quote->company_id != '') {
            $company_name = $quote->company->business_name;
        } else {
            $company_name = '';
        }

        return response()->json(['message' => 'Ok', 'quote' => $quote, 'contact_name' => $contact_name, 'owner' => $owner, 'price_name' => $price_name, 'gdp' => $gdp, 'company_name' => $company_name]);
    }

    //Actualiza condiciones de pago
    public function updatePaymentConditions(Request $request, $id)
    {
        $quote = QuoteV2::find($id);

        $quote->payment_conditions = $request->payments;
        $quote->update();
        //$this->updatePdfApi($quote->id);

        return response()->json(['message' => 'Ok', 'quote' => $quote]);
    }

    /**
     * Actualizar términos y condiciones de una cotización
     * @param Request $request
     * @param integer $id
     * @return type
     */
    public function updateTerms(Request $request, $id)
    {
        $quote = QuoteV2::find($id);
        $name = $request->name;
        $quote->$name = $request->terms;
        $quote->update();
        //$this->updatePdfApi($quote->id);
        $this->updateIntegrationQuoteStatus($quote->id);
        return response()->json(['message' => 'Ok', 'quote' => $quote]);
    }

    /**
     * Actualizar remarsk de un rate
     * @param Request $request
     * @param integer $id
     * @return type
     */
    public function updateRemarks(Request $request, $id)
    {
        $rate = AutomaticRate::find($id);

        if ($request->language == 'all') {
            $rate->remarks = $request->remarks;
        }
        if ($request->language == 'english') {
            $rate->remarks_english = $request->remarks;
        }
        if ($request->language == 'spanish') {
            $rate->remarks_spanish = $request->remarks;
        }
        if ($request->language == 'portuguese') {
            $rate->remarks_portuguese = $request->remarks;
        }

        $rate->update();

        //$this->updatePdfApi($rate->quote_id);
        $this->updateIntegrationQuoteStatus($rate->quote_id);

        return response()->json(['message' => 'Ok', 'rate' => $rate]);
    }

    /**
     * Duplicar una cotización existente
     * @param Request $request
     * @param integer $id
     * @return type
     */
    public function duplicate(Request $request, $id)
    {

        $id = obtenerRouteKey($id);
        $quote = QuoteV2::find($id);
        $quote_duplicate = new QuoteV2();
        $quote_duplicate->user_id = \Auth::id();
        $quote_duplicate->company_user_id = \Auth::user()->company_user_id;
        $quote_duplicate->quote_id = $this->idPersonalizado();
        $quote_duplicate->incoterm_id = $quote->incoterm_id;
        $quote_duplicate->type = $quote->type;
        $quote_duplicate->cargo_type = $quote->cargo_type;
        $quote_duplicate->total_quantity = $quote->total_quantity;
        $quote_duplicate->total_weight = $quote->total_weight;
        $quote_duplicate->total_volume = $quote->total_volume;
        $quote_duplicate->chargeable_weight = $quote->chargeable_weight;
        $quote_duplicate->delivery_type = $quote->delivery_type;
        $quote_duplicate->currency_id = $quote->currency_id;
        $quote_duplicate->contact_id = $quote->contact_id;
        $quote_duplicate->company_id = $quote->company_id;
        $quote_duplicate->validity_start = $quote->validity_start;
        $quote_duplicate->validity_end = $quote->validity_end;
        $quote_duplicate->equipment = $quote->equipment;
        $quote_duplicate->status = $quote->status;
        $quote_duplicate->date_issued = $quote->date_issued;
        $quote_duplicate->terms_and_conditions = $quote->terms_and_conditions;
        $quote_duplicate->terms_english = $quote->terms_english;
        $quote_duplicate->terms_portuguese = $quote->terms_portuguese;
        $quote_duplicate->payment_conditions = $quote->payment_conditions;
        if ($quote->origin_address) {
            $quote_duplicate->origin_address = $quote->origin_address;
        }
        if ($quote->destination_address) {
            $quote_duplicate->destination_address = $quote->destination_address;
        }
        if ($quote->origin_port_id) {
            $quote_duplicate->origin_port_id = $quote->origin_port_id;
        }
        if ($quote->destination_port_id) {
            $quote_duplicate->destination_port_id = $quote->destination_port_id;
        }
        if ($quote->price_id) {
            $quote_duplicate->price_id = $quote->price_id;
        }
        if ($quote->custom_quote_id) {
            $quote_duplicate->custom_quote_id = $quote->custom_quote_id;
        }
        if ($quote->kind_of_cargo) {
            $quote_duplicate->kind_of_cargo = $quote->kind_of_cargo;
        }
        if ($quote->commodity) {
            $quote_duplicate->commodity = $quote->commodity;
        }
        $quote_duplicate->save();

        $this->savePdfOptionsDuplicate($quote, $quote_duplicate);

        $this->saveScheduleQuoteDuplicate($quote, $quote_duplicate);

        $this->saveAutomaticRateDuplicate($quote, $quote_duplicate);

        if ($request->ajax()) {
            return response()->json(['message' => 'Ok']);
        } else {
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Quote duplicated successfully!');
            return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote_duplicate->id));
        }
    }

    public function savePdfOption($quote, $currency)
    {

        if (\Auth::user()->companyUser->pdf_language == 1) {
            $pdf_language = 'English';
        } elseif (\Auth::user()->companyUser->pdf_language == 2) {
            $pdf_language = 'Spanish';
        } elseif (\Auth::user()->companyUser->pdf_language == 3) {
            $pdf_language = 'Portuguese';
        } else {
            $pdf_language = 'English';
        }

        $pdf_option = new PdfOption();
        $pdf_option->quote_id = $quote->id;
        $pdf_option->show_type = 'detailed';
        $pdf_option->grouped_total_currency = 0;
        $pdf_option->total_in_currency = $currency->alphacode;
        $pdf_option->freight_charges_currency = $currency->alphacode;
        $pdf_option->origin_charges_currency = $currency->alphacode;
        $pdf_option->destination_charges_currency = $currency->alphacode;
        $pdf_option->show_total_freight_in_currency = $currency->alphacode;
        $pdf_option->show_schedules = 1;
        $pdf_option->show_gdp_logo = 1;
        $pdf_option->language = $pdf_language;
        $pdf_option->save();
    }

    public function savePdfOptionsDuplicate($quote, $quote_duplicate)
    {
        $pdf = PdfOption::where('quote_id', $quote->id)->first();
        $pdf_duplicate = new PdfOption();
        $pdf_duplicate->quote_id = $quote_duplicate->id;
        $pdf_duplicate->show_type = $pdf->show_type;
        $pdf_duplicate->grouped_total_currency = $pdf->grouped_total_currency;
        $pdf_duplicate->total_in_currency = $pdf->total_in_currency;
        $pdf_duplicate->grouped_freight_charges = $pdf->grouped_freight_charges;
        $pdf_duplicate->freight_charges_currency = $pdf->freight_charges_currency;
        $pdf_duplicate->grouped_origin_charges = $pdf->grouped_origin_charges;
        $pdf_duplicate->origin_charges_currency = $pdf->origin_charges_currency;
        $pdf_duplicate->grouped_destination_charges = $pdf->grouped_destination_charges;
        $pdf_duplicate->destination_charges_currency = $pdf->destination_charges_currency;
        $pdf_duplicate->show_total_freight_in_currency = $pdf->show_total_freight_in_currency;
        $pdf_duplicate->language = $pdf->language;
        $pdf_duplicate->show_carrier = $pdf->show_carrier;
        $pdf_duplicate->show_logo = $pdf->show_logo;
        $pdf_duplicate->show_gdp_logo = $pdf->show_gdp_logo;
        $pdf_duplicate->freight_no_grouping = $pdf->freight_no_grouping;
        $pdf_duplicate->save();
    }

    public function saveScheduleQuoteDuplicate($quote, $quote_duplicate)
    {
        $schedule_quote = Schedule::where('quote_id', $quote->id)->first();

        if ($schedule_quote) {
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

    public function saveAutomaticRateDuplicate($quote, $quote_duplicate)
    {
        $rates = AutomaticRate::where('quote_id', $quote->id)->get();

        foreach ($rates as $rate) {

            $rate_duplicate = new AutomaticRate();
            $rate_duplicate->quote_id = $quote_duplicate->id;
            $rate_duplicate->contract = $rate->contract;
            $rate_duplicate->validity_start = $rate->validity_start;
            $rate_duplicate->validity_end = $rate->validity_end;
            $rate_duplicate->origin_port_id = $rate->origin_port_id;
            $rate_duplicate->destination_port_id = $rate->destination_port_id;
            $rate_duplicate->origin_airport_id = $rate->origin_airport_id;
            $rate_duplicate->destination_airport_id = $rate->destination_airport_id;
            $rate_duplicate->carrier_id = $rate->carrier_id;
            $rate_duplicate->rates = $rate->rates;
            $rate_duplicate->markups = $rate->markups;
            $rate_duplicate->total = $rate->total;
            $rate_duplicate->currency_id = $rate->currency_id;
            $rate_duplicate->schedule_type = $rate->schedule_type;
            $rate_duplicate->transit_time = $rate->transit_time;
            $rate_duplicate->via = $rate->via;
            $rate_duplicate->remarks = $rate->remarks;
            $rate_duplicate->remarks_spanish = $rate->remarks_spanish;
            $rate_duplicate->remarks_english = $rate->remarks_english;
            $rate_duplicate->remarks_portuguese = $rate->remarks_portuguese;
            $rate_duplicate->save();

            $charges = Charge::where('automatic_rate_id', $rate->id)->get();
            if ($charges->count() > 0) {
                foreach ($charges as $charge) {
                    $charge_duplicate = new Charge();
                    $charge_duplicate->automatic_rate_id = $rate_duplicate->id;
                    $charge_duplicate->type_id = $charge->type_id;
                    $charge_duplicate->surcharge_id = $charge->surcharge_id;
                    $charge_duplicate->calculation_type_id = $charge->calculation_type_id;
                    $charge_duplicate->amount = $charge->amount;
                    $charge_duplicate->markups = $charge->markups;
                    $charge_duplicate->total = $charge->total;
                    $charge_duplicate->currency_id = $charge->currency_id;
                    $charge_duplicate->save();
                }
            }

            $chargesLcl = ChargeLclAir::where('automatic_rate_id', $rate->id)->get();
            if ($chargesLcl->count() > 0) {
                foreach ($chargesLcl as $charge) {
                    $charge_duplicate = new ChargeLclAir();
                    $charge_duplicate->automatic_rate_id = $rate_duplicate->id;
                    $charge_duplicate->type_id = $charge->type_id;
                    $charge_duplicate->surcharge_id = $charge->surcharge_id;
                    $charge_duplicate->calculation_type_id = $charge->calculation_type_id;
                    $charge_duplicate->units = $charge->units;
                    $charge_duplicate->price_per_unit = $charge->price_per_unit;
                    $charge_duplicate->markup = $charge->markup;
                    $charge_duplicate->total = $charge->total;
                    $charge_duplicate->currency_id = $charge->currency_id;
                    $charge_duplicate->save();
                }
            }
        }
    }

    /**
     * Crea Custom ID a partir de datos del usuario
     * @return type
     */
    public function idPersonalizado()
    {
        $user_company = CompanyUser::where('id', \Auth::user()->company_user_id)->first();
        $company_code = strtoupper(substr($user_company->name, 0, 2));
        $higherq_id = $user_company->getHigherId($company_code);
        $newq_id = $company_code . '-' . strval($higherq_id + 1);

        return $newq_id;
    }

    /**
     * Mostrar/Ocultar contenedores en la vista
     * @param array $equipmentForm
     * @param integer $tipo
     * @return type
     */
    public function hideContainerV2($equipmentForm, $tipo, $container)
    {

        $equipment = new Collection();

        if ($tipo == 'BD') {
            $equipmentForm = json_decode($equipmentForm);
        }

        foreach ($container as $cont) {
            $hidden = 'hidden' . $cont->code;
            $$hidden = 'hidden';
            foreach ($equipmentForm as $val) {
                if ($val == '20') {
                    $val = 1;
                } elseif ($val == '40') {
                    $val = 2;
                } elseif ($val == '40HC') {
                    $val = 3;
                } elseif ($val == '45HC') {
                    $val = 4;
                } elseif ($val == '40NOR') {
                    $val = 5;
                }
                if ($val == $cont->id) {

                    $$hidden = '';
                }
            }
            $equipment->put($cont->code, $$hidden);
        }

        // Clases para reordenamiento de la tabla y ajuste
        $originClass = 'col-md-2';
        $destinyClass = 'col-md-1';
        $dataOrigDest = 'col-md-3';

        $countEquipment = count($equipmentForm);
        $calculos = $countEquipment;
        $countEquipment = 5 - $countEquipment;
        if ($countEquipment == 1) {
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-1';
            $dataOrigDest = 'col-md-4';
        }
        if ($countEquipment == 2) {
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-5';
        }
        if ($countEquipment == 3) {
            $originClass = 'col-md-4';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-6';
        }
        if ($countEquipment == 4) {
            $originClass = 'col-md-5';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-7';
        }

        if ($calculos == 1) {
            $head_1 = 'col-lg-8';
            $head_2 = 'col-lg-3';
        }
        if ($calculos == 2) {
            $head_1 = 'col-lg-7';
            $head_2 = 'col-lg-4';
        }
        if ($calculos == 3) {
            $head_1 = 'col-lg-6';
            $head_2 = 'col-lg-5';
        }
        if ($calculos == 4) {
            $head_1 = 'col-lg-5';
            $head_2 = 'col-lg-6';
        }
        if ($calculos == 5) {
            $head_1 = 'col-lg-4';
            $head_2 = 'col-lg-7';
        }

        $equipment->put('head_1', @$head_1);
        $equipment->put('head_2', @$head_2);
        $equipment->put('originClass', $originClass);
        $equipment->put('destinyClass', $destinyClass);
        $equipment->put('dataOrigDest', $dataOrigDest);
        return ($equipment);
    }

    public function hideContainer($equipmentForm, $tipo)
    {
        $equipment = new Collection();
        $hidden20 = 'hidden';
        $hidden40 = 'hidden';
        $hidden40hc = 'hidden';
        $hidden40nor = 'hidden';
        $hidden45 = 'hidden';
        $hidden20R = 'hidden';
        // Clases para reordenamiento de la tabla y ajuste
        $originClass = 'col-md-2';
        $destinyClass = 'col-md-1';
        $dataOrigDest = 'col-md-3';

        if ($tipo == 'BD') {
            $equipmentForm = json_decode($equipmentForm);
        }

        $countEquipment = count($equipmentForm);
        $countEquipment = 5 - $countEquipment;
        if ($countEquipment == 1) {
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-1';
            $dataOrigDest = 'col-md-4';
        }
        if ($countEquipment == 2) {
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-5';
        }
        if ($countEquipment == 3) {
            $originClass = 'col-md-4';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-6';
        }
        if ($countEquipment == 4) {
            $originClass = 'col-md-5';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-7';
        }

        foreach ($equipmentForm as $val) {
            if ($val == '1') {
                $hidden20 = '';
            }
            if ($val == '2') {
                $hidden40 = '';
            }
            if ($val == '3') {
                $hidden40hc = '';
            }
            if ($val == '5') {
                $hidden40nor = '';
            }
            if ($val == '4') {
                $hidden45 = '';
            }
            if ($val == '6') {
                $hidden20R = '';
            }
        }
        $equipment->put('originClass', $originClass);
        $equipment->put('destinyClass', $destinyClass);
        $equipment->put('dataOrigDest', $dataOrigDest);
        $equipment->put('20', $hidden20);
        $equipment->put('40', $hidden40);
        $equipment->put('40hc', $hidden40hc);
        $equipment->put('40nor', $hidden40nor);
        $equipment->put('45', $hidden45);
        $equipment->put('20R', $hidden20R);
        return ($equipment);
    }

    /**
     * Delete quotes v2 (Soft Delete)
     * @param integer $id
     * @return type
     */
    public function destroy($id)
    {
        $quote_id = obtenerRouteKey($id);
        QuoteV2::where('id', $quote_id)->delete();
        return response()->json(['message' => 'Ok']);
    }

    /**
     * Destroy automatic rates
     * @param  integer $id
     * @return array json
     */
    public function delete($id)
    {
        AutomaticRate::where('id', $id)->delete();
        return response()->json(['message' => 'Ok']);
    }

    /**
     * Delete Charges FCL
     * @param Request $request
     * @param integer $id
     * @return array json
     */

    public function deleteCharge(Request $request, $id)
    {
        if ($request->type == 1) {
            Charge::where('id', $id)->delete();
        } else {
            $charge = Charge::findOrFail($id);
            $charge->amount = null;
            $charge->markups = null;
            $charge->update();
        }

        return response()->json(['message' => 'Ok', 'type' => $request->type]);
    }

    /**
     * Delete charges FCL/AIR
     * @param Request $request
     * @param integer $id
     * @return Array Json
     */

    public function deleteChargeLclAir(Request $request, $id)
    {
        if ($request->type == 1) {
            ChargeLclAir::where('id', $id)->delete();
        } else {
            $charge = ChargeLclAir::findOrFail($id);
            $charge->units = 0;
            $charge->price_per_unit = 0;
            $charge->markup = 0;
            $charge->total = 0;
            $charge->update();
        }
        return response()->json(['message' => 'Ok', 'type' => $request->type]);
    }

    /**
     * Delete inlands
     * @param Request $request
     * @param integer $id
     * @return Array Json
     */

    public function deleteInland(Request $request, $id)
    {

        AutomaticInland::where('id', $id)->delete();

        return response()->json(['message' => 'Ok']);
    }

    /**
     * Store quotes
     * @param Request $request
     * @return type
     */

    public function storeCharge(Request $request)
    {
        $containers = Container::all();
        $array_amount = 'array_amount_';
        $array_markup = 'array_markup_';
        $total_amount_markup = 'total_amount_markup';
        $amount = 'amount';
        $markup = 'markup';
        $total_markup = 'total_markup';
        $total_amount = 'total_amount';
        $total_amount_markup = 'total_amount_markup';
        $sum_total = 'sum_total_';
        $sum_total_freight = 'sum_total_freight';
        $sum_total_origin = 'sum_total_origin';
        $sum_total_destination = 'sum_total_destination';
        $sum_amount_markup = 'sum_amount_markup';
        $sum_total_array = array();
        $sum_total_freight_array = array();
        $sum_total_origin_array = array();
        $sum_total_destination_array = array();
        $merge_amounts = array();
        $merge_markups = array();

        foreach ($containers as $value) {
            ${$sum_total . $value->code} = 0;
            ${$sum_total_freight . $value->code} = 0;
            ${$sum_total_origin . $value->code} = 0;
            ${$sum_total_destination . $value->code} = 0;
            ${$array_amount . $value->code} = array();
            ${$array_markup . $value->code} = array();

            foreach ($request->equipments as $key => $equipment) {
                if (($key == 'amount_' . $value->code) && $equipment != null) {
                    ${$array_amount . $value->code} = array('c' . $value->code => $equipment);
                }
                if (($key == 'markup_' . $value->code) && $equipment != null) {
                    ${$array_markup . $value->code} = array('m' . $value->code => $equipment);
                }
            }
            $merge_amounts = array_merge($merge_amounts, ${$array_amount . $value->code});
            $merge_markups = array_merge($merge_markups, ${$array_markup . $value->code});
        }

        $charge = new Charge();
        $charge->automatic_rate_id = $request->automatic_rate_id;
        $charge->type_id = $request->type_id;
        $charge->surcharge_id = $request->surcharge_id;
        $charge->calculation_type_id = $request->calculation_type_id;
        $charge->amount = json_encode($merge_amounts);
        $charge->markups = json_encode($merge_markups);
        $charge->currency_id = $request->currency_id;
        $charge->save();

        $company_user = CompanyUser::find(Auth::user()->company_user_id);
        $surcharge = Surcharge::find($request->surcharge_id);
        $calculation_type = CalculationType::find($request->calculation_type_id);
        $currency_charge = Currency::find($request->currency_id);

        $rates = AutomaticRate::whereHas('charge', function ($query) use ($request) {
            $query->where('type_id', $request->type_id);
        })->where('id', $request->automatic_rate_id)->get();

        $charges = Charge::where('automatic_rate_id', $request->automatic_rate_id)->with('automatic_rate')->get();

        //Charges
        foreach ($charges as $value) {

            if ($request->type_id == 3) {
                $typeCurrency = @$value->automatic_rate->currency->alphacode;
            } else {
                $typeCurrency = $company_user->currency->alphacode;
            }

            $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

            $array_amounts = json_decode($value->amount, true);
            $array_markups = json_decode($value->markups, true);

            $array_amounts = $this->processOldContainers($array_amounts, 'amounts');
            $array_markups = $this->processOldContainers($array_markups, 'markups');

            foreach ($containers as $container) {
                ${$amount . $container->code} = 0;
                ${$markup . $container->code} = 0;
                ${$total_amount . $container->code} = 0;
                ${$total_markup . $container->code} = 0;
                ${$total_amount_markup . $container->code} = 0;
                ${$sum_amount_markup . $container->code} = 0;
            }

            foreach ($containers as $container) {
                if (isset($array_amounts['c' . $container->code])) {
                    ${$amount . $container->code} = $array_amounts['c' . $container->code];
                    ${$total_amount . $container->code} = number_format(${$amount . $container->code} / $currency_rate, 2, '.', '');
                }

                if (isset($array_markups['m' . $container->code])) {
                    ${$markup . $container->code} = $array_markups['m' . $container->code];
                    ${$total_markup . $container->code} = number_format(${$markup . $container->code} / $currency_rate, 2, '.', '');
                }

                //Calculando el total de tarifas+recargos
                ${$sum_amount_markup . $container->code} = ${$total_amount . $container->code}+${$total_markup . $container->code};

                //Sumando totales de freight
                if ($value->type_id == 3) {
                    ${$sum_total_freight . $container->code} += number_format(${$sum_amount_markup . $container->code}, 2, '.', '');
                }
                //Sumando totales de origin
                if ($value->type_id == 1) {
                    ${$sum_total_origin . $container->code} += number_format(${$sum_amount_markup . $container->code}, 2, '.', '');
                }
                //Sumando totales de destination
                if ($value->type_id == 2) {
                    ${$sum_total_destination . $container->code} += number_format(${$sum_amount_markup . $container->code}, 2, '.', '');
                }

                //Sumando totales generales
                ${$sum_total . $container->code} += number_format(${$sum_amount_markup . $container->code}, 2, '.', '');

                //Añadiendo totales al array
                $sum_total_array[$container->code] = ${$sum_total . $container->code};
                $sum_total_freight_array[$container->code] = ${$sum_total_freight . $container->code};
                $sum_total_origin_array[$container->code] = ${$sum_total_origin . $container->code};
                $sum_total_destination_array[$container->code] = ${$sum_total_destination . $container->code};
            }
        }

        return response()->json(['message' => 'Ok', 'charge' => $charge, 'amounts' => $merge_amounts, 'surcharge' => $surcharge->name, 'calculation_type' => $calculation_type->name, 'currency' => $currency_charge->alphacode, 'sum_total' => $sum_total_array, 'sum_total_freight' => $sum_total_freight_array, 'sum_total_origin' => $sum_total_origin_array, 'sum_total_destination' => $sum_total_destination_array, 'id' => $charge->id]);
    }

    public function storeChargeLclAir(Request $request)
    {

        $charge = new ChargeLclAir();
        $charge->automatic_rate_id = $request->automatic_rate_id;
        $charge->type_id = $request->type_id;
        $charge->surcharge_id = $request->surcharge_id;
        $charge->calculation_type_id = $request->calculation_type_id;
        $charge->units = $request->units;
        $charge->price_per_unit = $request->price_per_unit;
        $charge->total = $request->total;
        $charge->markup = $request->markup;
        $charge->currency_id = $request->currency_id;
        $charge->save();

        $charge = ChargeLclAir::find($charge->id);
        $total = ($charge->units * $charge->price_per_unit) + $charge->markup;

        return response()->json(['message' => 'Ok', 'surcharge' => $charge->surcharge->name, 'calculation_type' => $charge->calculation_type->name, 'units' => $charge->units, 'rate' => $charge->price_per_unit, 'markup' => $charge->markup, 'total' => $total, 'currency' => $charge->currency->alphacode, 'type' => $charge->type_id, 'id' => $charge->id]);
    }

    public function getCompanyPayments($id)
    {
        $payments = Company::find($id);
        return $payments->payment_conditions;
    }

    public function termsandconditions($origin_port, $destiny_port, $carrier, $mode)
    {

        // TERMS AND CONDITIONS
        $carrier_all = 26;
        $port_all = harbor::where('name', 'ALL')->first();
        $term_port_orig = array($origin_port->id);
        $term_port_dest = array($destiny_port->id);
        $term_carrier_id[] = $carrier->id;
        array_push($term_carrier_id, $carrier_all);

        /* $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q) use($term_carrier_id)  {
        $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function($b) use($term_carrier_id)  {
        $b->wherein('carrier_id',$term_carrier_id);
        });
        })->get();*/
        $terms_origin = TermsPort::wherein('port_id', $term_port_orig)->with('term')->whereHas('term', function ($q) use ($term_carrier_id) {
            $q->where('termsAndConditions.company_user_id', \Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function ($b) use ($term_carrier_id) {
                $b->wherein('carrier_id', $term_carrier_id);
            });
        })->get();

        $terms_destination = TermsPort::wherein('port_id', $term_port_dest)->with('term')->whereHas('term', function ($q) use ($term_carrier_id) {
            $q->where('termsAndConditions.company_user_id', \Auth::user()->company_user_id)->whereHas('TermConditioncarriers', function ($b) use ($term_carrier_id) {
                $b->wherein('carrier_id', $term_carrier_id);
            });
        })->get();

        $termsO = '';
        $termsD = '';
        $terms = '';

        foreach ($terms_origin as $termOrig) {
            $terms .= "<br>";
            $termsO = $origin_port->name . " / " . $carrier->name;
            $termsO .= "<br>" . $termOrig->term->export . "<br>";
        }
        foreach ($terms_destination as $termDest) {
            $terms .= "<br>";
            $termsD = $destiny_port->name . " / " . $carrier->name;
            $termsD .= "<br>" . $termDest->term->export . "<br>";
        }
        $terms = $termsO . " " . $termsD;
        return $terms;
    }

    public function remarksCondition($origin, $destiny, $carrier, $mode, $type = 'port')
    {

        // TERMS AND CONDITIONS
        $carrier_all = 26;
        $port_all = harbor::where('name', 'ALL')->first();
        $country_all = Country::where('name', 'ALL')->first();
        $rem_orig = array($origin->id);
        $rem_dest = array($destiny->id);
        $rem_carrier_id[] = $carrier->id;

        array_push($rem_carrier_id, $carrier_all);
        $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
        $language_id = $company->companyUser->pdf_language;

        if ($language_id == '') {
            $language_id = 1;
        }

        if ($type == 'port') {

            $remarks_all = RemarkHarbor::where('port_id', $port_all->id)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_origin = RemarkHarbor::wherein('port_id', $rem_orig)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_destination = RemarkHarbor::wherein('port_id', $rem_dest)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();
        }
        if ($type == 'country') {

            $remarks_all = RemarkCountry::where('country_id', $country_all->id)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->where('language_id', $language_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_origin = RemarkCountry::wherein('country_id', $rem_orig)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->where('language_id', $language_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_destination = RemarkCountry::wherein('country_id', $rem_dest)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->where('language_id', $language_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();
        }

        $remarkA = '';
        $remarkO = '';
        $remarkD = '';
        $rems = '';

        // if ($remarks_all->count() > 0) {
        //     $remarkA .= $origin->name . " / " . $destiny->name . " / " . $carrier->name . "<br>";
        // }

        foreach ($remarks_all as $remAll) {
            $rems .= "<br>";
            //$remarkA .= $origin_port->name . " / " . $carrier->name;
            if ($mode == 1) {
                $remarkA .= $remAll->remark->export . "<br>";
            } else {
                $remarkA .= $remAll->remark->import . "<br>";
            }
        }

        // if ($remarks_origin->count() > 0) {
        //     $remarkO .= $origin->name . " / " . $carrier->name;
        // }

        foreach ($remarks_origin as $remOrig) {

            $rems .= "<br>";

            if ($mode == 1) {
                $remarkO .= $remOrig->remark->export . "<br>";
            } else {
                $remarkO .= $remOrig->remark->import . "<br>";
            }
        }

        // if ($remarks_destination->count() > 0) {
        //     $remarkD .= $destiny->name . " / " . $carrier->name;
        // }

        foreach ($remarks_destination as $remDest) {
            $rems .= "<br>";

            if ($mode == 1) {
                $remarkD .= $remDest->remark->export . "<br>";
            } else {
                $remarkD .= $remDest->remark->import . "<br>";
            }
        }

        $rems = $remarkO . " " . $remarkD . " " . $remarkA;

        return $rems;
    }

    public function saveRemarks($rateId, $orig, $dest, $carrier, $modo, $type = 'port')
    {

        $carrier_all = 26;
        $port_all = harbor::where('name', 'ALL')->first();
        $country_all = Country::where('name', 'ALL')->first();
        $nameOrig = $orig->name;
        $rem_orig[] = $orig->id;
        $nameDest = $dest->name;
        $rem_dest[] = $dest->id;
        $rem_carrier_id[] = $carrier;

        array_push($rem_carrier_id, $carrier_all);
        $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
        $language_id = $company->companyUser->pdf_language;

        if ($type == 'port') {

            $remarks_all = RemarkHarbor::where('port_id', $port_all->id)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_origin = RemarkHarbor::wherein('port_id', $rem_orig)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_destination = RemarkHarbor::wherein('port_id', $rem_dest)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();
        }

        if ($type == 'country') {

            $remarks_all = RemarkCountry::where('country_id', $country_all->id)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->where('language_id', $language_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_origin = RemarkCountry::wherein('country_id', $rem_orig)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->where('language_id', $language_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();

            $remarks_destination = RemarkCountry::wherein('country_id', $rem_dest)->with('remark')->whereHas('remark', function ($q) use ($rem_carrier_id, $language_id) {
                $q->where('remark_conditions.company_user_id', \Auth::user()->company_user_id)->where('language_id', $language_id)->whereHas('remarksCarriers', function ($b) use ($rem_carrier_id) {
                    $b->wherein('carrier_id', $rem_carrier_id);
                });
            })->get();
        }

        $remarks_english = "";
        $remarks_spanish = "";
        $remarks_portuguese = "";

        foreach ($remarks_all as $remAll) {
            $remarks_english .= "<br>";
            $remarks_spanish .= "<br>";
            $remarks_portuguese .= "<br>";
            if ($modo == '1') {
                if ($remAll->remark->language_id == '1') {
                    $remarks_english .= $remAll->remark->export . "<br>";
                }

                if ($remAll->remark->language_id == '2') {
                    $remarks_spanish .= $remAll->remark->export . "<br>";
                }

                if ($remAll->remark->language_id == '3') {
                    $remarks_portuguese .= $remAll->remark->export . "<br>";
                }
            } else { // import

                if ($remAll->remark->language_id == '1') {
                    $remarks_english .= $remAll->remark->import . "<br>";
                }

                if ($remAll->remark->language_id == '2') {
                    $remarks_spanish .= $remAll->remark->import . "<br>";
                }

                if ($remAll->remark->language_id == '3') {
                    $remarks_portuguese .= $remAll->remark->import . "<br>";
                }
            }
        }

        foreach ($remarks_origin as $remOrig) {

            $remarks_english .= "<br>";
            $remarks_spanish .= "<br>";
            $remarks_portuguese .= "<br>";

            if ($modo == '1') {
                if ($remOrig->remark->language_id == '1') {
                    $remarks_english .= $remOrig->remark->export . "<br>";
                }

                if ($remOrig->remark->language_id == '2') {
                    $remarks_spanish .= $remOrig->remark->export . "<br>";
                }

                if ($remOrig->remark->language_id == '3') {
                    $remarks_portuguese .= $remOrig->remark->export . "<br>";
                }
            } else { // import

                if ($remOrig->remark->language_id == '1') {
                    $remarks_english .= $remOrig->remark->import . "<br>";
                }

                if ($remOrig->remark->language_id == '2') {
                    $remarks_spanish .= $remOrig->remark->import . "<br>";
                }

                if ($remOrig->remark->language_id == '3') {
                    $remarks_portuguese .= $remOrig->remark->import . "<br>";
                }
            }
        }

        foreach ($remarks_destination as $remDest) {

            $remarks_english .= "<br>";
            $remarks_spanish .= "<br>";
            $remarks_portuguese .= "<br>";

            if ($modo == '1') {
                if ($remDest->remark->language_id == '1') {
                    $remarks_english .= $remDest->remark->export . "<br>";
                }

                if ($remDest->remark->language_id == '2') {
                    $remarks_spanish .= $remDest->remark->export . "<br>";
                }

                if ($remDest->remark->language_id == '3') {
                    $remarks_portuguese .= $remDest->remark->export . "<br>";
                }
            } else { // import

                if ($remDest->remark->language_id == '1') {
                    $remarks_english .= $remDest->remark->import . "<br>";
                }

                if ($remDest->remark->language_id == '2') {
                    $remarks_spanish .= $remDest->remark->import . "<br>";
                }

                if ($remDest->remark->language_id == '3') {
                    $remarks_portuguese .= $remDest->remark->import . "<br>";
                }
            }
        }

        //   $remarkGenerales = array('english' => $remarks_english , 'spanish' => $remarks_spanish , 'portuguese' => $remarks_portuguese ,'origen' => $nameOrig , 'destino' => $nameDest  );

        //return $remarkGenerales ;

        $quoteEdit = AutomaticRate::find($rateId);
        $quoteEdit->remarks_english = $remarks_english;
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

    public function saveTerms($quoteId, $type, $modo)
    {

        $companyUser = CompanyUser::All();
        $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
        $terms = TermAndConditionV2::where('company_user_id', Auth::user()->company_user_id)->where('type', $type)->with('language')->get();

        $terminos_english = "";
        $terminos_spanish = "";
        $terminos_portuguese = "";

        //Export
        foreach ($terms as $term) {
            if ($modo == '1') {
                if ($term->language_id == '1') {
                    $terminos_english .= $term->export . "<br>";
                }

                if ($term->language_id == '2') {
                    $terminos_spanish .= $term->export . "<br>";
                }

                if ($term->language_id == '3') {
                    $terminos_portuguese .= $term->export . "<br>";
                }
            } else { // import

                if ($term->language_id == '1') {
                    $terminos_english .= $term->import . "<br>";
                }

                if ($term->language_id == '2') {
                    $terminos_spanish .= $term->import . "<br>";
                }

                if ($term->language_id == '3') {
                    $terminos_portuguese .= $term->import . "<br>";
                }
            }
        }

        $quoteEdit = QuoteV2::find($quoteId);
        $quoteEdit->terms_english = $terminos_english;
        $quoteEdit->terms_and_conditions = $terminos_spanish;
        $quoteEdit->terms_portuguese = $terminos_portuguese;

        $quoteEdit->update();
    }

    public function updatePdfApi($id)
    {
        //$this->dispatch((new UpdatePdf($id, Auth::user()->company_user_id, Auth::user()->id))->onQueue('default'));
        UpdatePdf::dispatch($id, Auth::user()->company_user_id, Auth::user()->id)->onQueue('default')->delay(now()->addMinutes(3));
    }

    public function store(Request $request, $type)
    {
        if (!empty($request->input('form'))) {
            $form = json_decode($request->input('form'));
            $info = $request->input('info');
            $equipment = stripslashes(json_encode($form->equipment));
            $dateQ = explode('/', $form->date);
            $since = $dateQ[0];
            $until = $dateQ[1];
            $priceId = null;
            $mode = $form->mode;
            if (isset($form->price_id)) {
                $priceId = $form->price_id;
                if ($priceId == "0") {
                    $priceId = null;
                }
            }
            $fcompany_id = null;
            $fcontact_id = null;
            $payments = null;

            if (isset($form->company_id_quote)) {

                if ($form->company_id_quote != "0" && $form->company_id_quote != null) {
                    $payments = $this->getCompanyPayments($form->company_id_quote);
                    $fcompany_id = $form->company_id_quote;
                }
            }

            if (isset($form->contact_id)) {
                if ($form->contact_id != "0" && $form->contact_id != null) {
                    $fcontact_id = $form->contact_id;
                }
            }

            $request->request->add(['company_user_id' => \Auth::user()->company_user_id, 'quote_id' => $this->idPersonalizado(), 'type' => 'FCL', 'delivery_type' => $form->delivery_type, 'company_id' => $fcompany_id, 'contact_id' => $fcontact_id, 'validity_start' => $since, 'validity_end' => $until, 'user_id' => \Auth::id(), 'equipment' => $equipment, 'status' => 'Draft', 'date_issued' => $since, 'price_id' => $priceId, 'payment_conditions' => $payments, 'origin_address' => $form->origin_address, 'destination_address' => $form->destination_address]);

            $quote = QuoteV2::create($request->all());

            $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
            $currency_id = $company->companyUser->currency_id;
            $currency = Currency::find($currency_id);

            $language = $company->companyUser->language()->first();
            $quote->language_id = $language->id ?? 1;
            $cargo_type_id = $request->input('cargo_type');
            $quote->cargo_type_id = $cargo_type_id;
            $pdfOptions = [
                "allIn" =>true, 
                "showCarrier"=>true, 
                "showTotals"=>false, 
                "totalsCurrency" =>$currency];
            $quote->pdf_options = $pdfOptions;
            $quote->save();

            $this->savePdfOption($quote, $currency);
        } else { // COTIZACION MANUAL

            $dateQ = explode('/', $request->input('date'));
            $since = $dateQ[0];
            $until = $dateQ[1];
            $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();

            $idCurrency = $company->companyUser->currency_id;
            $currency = Currency::find($idCurrency);

            $arregloNull = array();
            $arregloNull = json_encode($arregloNull);

            if ($request->input('type') == '1') {
                $typeText = "FCL";
                $equipment = stripslashes(json_encode($request->input('equipment')));
                $delivery_type = $request->input('delivery_type');
            }
            if ($request->input('type') == '2') {
                $typeText = "LCL";
                $equipment = $arregloNull;
                $delivery_type = $request->input('delivery_type');
            }
            if ($request->input('type') == '3') {
                $typeText = "AIR";
                $equipment = $arregloNull;
                $delivery_type = $request->input('delivery_type_air');
            }
            $fcompany_id = null;
            $fcontact_id = null;
            $payments = null;
            //  if(isset($request->input('company_id_quote'))){
            if ($request->input('company_id_quote') != "0" && $request->input('company_id_quote') != null) {
                $payments = $this->getCompanyPayments($request->input('company_id_quote'));
                $fcompany_id = $request->input('company_id_quote');
                $fcontact_id = $request->input('contact_id');
            }
            //  }

            $priceId = null;
            if (isset($request->price_id)) {
                $priceId = $request->price_id;
                if ($priceId == "0") {
                    $priceId = null;
                }
            }
            $request->request->add(['company_user_id' => \Auth::user()->company_user_id, 'quote_id' => $this->idPersonalizado(), 'type' => $typeText, 'delivery_type' => $delivery_type, 'company_id' => $fcompany_id, 'contact_id' => $fcontact_id, 'validity_start' => $since, 'validity_end' => $until, 'user_id' => \Auth::id(), 'equipment' => $equipment, 'status' => 'Draft', 'date_issued' => $since, 'payment_conditions' => $payments, 'price_id' => $priceId]);
            $quote = QuoteV2::create($request->all());

            $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
            $currency_id = $company->companyUser->currency_id;
            $currency = Currency::find($currency_id);

            $language = $company->companyUser->language()->first();
            $quote->language_id = $language->id ?? 1;
            $cargo_type_id = $request->input('cargo_type');
            $quote->cargo_type_id = $cargo_type_id;
            $pdfOptions = [
                "allIn" =>true, 
                "showCarrier"=>true, 
                "showTotals"=>false, 
                "totalsCurrency" =>$currency];
            $quote->pdf_options = $pdfOptions;
            $quote->save();
            $modo = $request->input('mode');
            // FCL
            if ($typeText == 'FCL') {
                /**foreach ($request->input('originport') as $origP) {
                $infoOrig = explode("-", $origP);
                $origin_port[] = $infoOrig[0];
                }
                foreach ($request->input('destinyport') as $destP) {
                $infoDest = explode("-", $destP);
                $destiny_port[] = $infoDest[0];
                }
                foreach ($origin_port as $orig) {
                foreach ($destiny_port as $dest) {
                $request->request->add(['contract' => '', 'origin_port_id' => $orig, 'destination_port_id' => $dest, 'currency_id' => $idCurrency, 'quote_id' => $quote->id]);
                $rate = AutomaticRate::create($request->all());

                $oceanFreight = new Charge();
                $oceanFreight->automatic_rate_id = $rate->id;
                $oceanFreight->type_id = '3';
                $oceanFreight->surcharge_id = null;
                $oceanFreight->calculation_type_id = '5';
                $oceanFreight->amount = $arregloNull;
                $oceanFreight->markups = $arregloNull;
                $oceanFreight->currency_id = $idCurrency;
                $oceanFreight->total = $arregloNull;
                $oceanFreight->save();
                }
                }**/

                $this->saveTerms($quote->id, 'FCL', $modo);
            }
            if ($typeText == 'LCL') {
                /**foreach ($request->input('originport') as $origP) {
                $infoOrig = explode("-", $origP);
                $origin_port[] = $infoOrig[0];
                }
                foreach ($request->input('destinyport') as $destP) {
                $infoDest = explode("-", $destP);
                $destiny_port[] = $infoDest[0];
                }
                foreach ($origin_port as $orig) {
                foreach ($destiny_port as $dest) {
                $request->request->add(['contract' => '', 'origin_port_id' => $orig, 'destination_port_id' => $dest, 'currency_id' => $idCurrency, 'quote_id' => $quote->id]);
                $rate = AutomaticRate::create($request->all());

                $oceanFreight = new ChargeLclAir();
                $oceanFreight->automatic_rate_id = $rate->id;
                $oceanFreight->type_id = '3';
                $oceanFreight->surcharge_id = null;
                $oceanFreight->calculation_type_id = '4';
                $oceanFreight->units = "0";
                $oceanFreight->price_per_unit = "0";
                $oceanFreight->total = "0";
                $oceanFreight->markup = "0";
                $oceanFreight->currency_id = $idCurrency;
                $oceanFreight->save();
                }
                }**/

                $this->saveTerms($quote->id, 'LCL', $modo);
            }
            if ($typeText == 'AIR') {

                $request->request->add(['contract' => '', 'origin_airport_id' => $request->input('origin_airport_id'), 'destination_airport_id' => $request->input('destination_airport_id'), 'currency_id' => $idCurrency, 'quote_id' => $quote->id]);
                $rate = AutomaticRate::create($request->all());

                $oceanFreight = new ChargeLclAir();
                $oceanFreight->automatic_rate_id = $rate->id;
                $oceanFreight->type_id = '3';
                $oceanFreight->surcharge_id = null;
                $oceanFreight->calculation_type_id = '4';
                $oceanFreight->units = "0";
                $oceanFreight->price_per_unit = "0";
                $oceanFreight->total = "0";
                $oceanFreight->markup = "0";
                $oceanFreight->currency_id = $idCurrency;
                $oceanFreight->save();
            }
            //LCL

            if ($typeText == 'LCL' || $typeText == 'AIR') {
                $input = Input::all();
                $quantity = array_values(array_filter($input['quantity']));
                //dd($input);
                $type_cargo = array_values(array_filter($input['type_load_cargo']));
                $height = array_values(array_filter($input['height']));
                $width = array_values(array_filter($input['width']));
                $large = array_values(array_filter($input['large']));
                $weight = array_values(array_filter($input['weight']));
                $volume = array_values(array_filter($input['volume']));
                if (count($quantity) > 0) {
                    foreach ($type_cargo as $key => $item) {
                        $package_load = new PackageLoadV2();
                        $package_load->quote_id = $quote->id;
                        $package_load->type_cargo = $type_cargo[$key];
                        $package_load->quantity = $quantity[$key];
                        $package_load->height = $height[$key];
                        $package_load->width = $width[$key];
                        $package_load->large = $large[$key];
                        $package_load->weight = $weight[$key];
                        $package_load->total_weight = $weight[$key] * $quantity[$key];
                        // if(!empty($volume)){
                        if (!empty($volume[$key]) && $volume[$key] != null) {
                            $package_load->volume = $volume[$key];
                        } else {
                            $package_load->volume = 0.01;
                        }

                        $package_load->save();
                    }
                }
            }

            $this->savePdfOption($quote, $currency);

            // MANUAL RATE
        }

        //CONDICION PARA GUARDAR AUTOMATIC QUOTE
        if (!empty($info)) {
            $terms = '';

            foreach ($info as $infoA) {
                $info_D = json_decode($infoA);

                // Rates

                foreach ($info_D->rates as $rateO) {

                    $rates = json_encode($rateO->rate);

                    $markups = json_encode($rateO->markups);
                    $arregloNull = array();

                    $remarks = $info_D->remarks . "<br>";

                    //NEW REMARKS FOR QUOTE
                    $quote_language = $company->companyUser->pdf_language;

                    if ($quote_language == 1) {
                        $quote->remarks_english = $remarks;
                        $quote->save();
                    } else if ($quote_language == 2) {
                        $quote->remarks_spanish = $remarks;
                        $quote->save();
                    } else if ($quote_language == 3) {
                        $quote->remarks_portuguese = $remarks;
                        $quote->save();
                    }

                    // $remarks .= $this->remarksCondition($info_D->port_origin,$info_D->port_destiny,$info_D->carrier,$mode);

                    //$request->request->add(['contract' => $info_D->contract->name . " / " . $info_D->contract->number, 'origin_port_id' => $info_D->port_origin->id, 'destination_port_id' => $info_D->port_destiny->id, 'carrier_id' => $info_D->carrier->id, 'currency_id' => $info_D->currency->id, 'quote_id' => $quote->id, 'remarks' => $remarks, 'schedule_type' => $info_D->sheduleType, 'transit_time' => $info_D->transit_time, 'via' => $info_D->via]);
                    if (isset($info_D->transit_time) && $info_D->transit_time != '') {
                        $transitTime = $info_D->transit_time;
                        $viaT = $info_D->via;
                        $service = $info_D->service;
                    } else {
                        $transitTime = null;
                        $viaT = null;
                        $service = null;
                    }

                    $request->request->add(['contract' => $info_D->contract->name . " / " . $info_D->contract->number, 'origin_port_id' => $info_D->port_origin->id, 'destination_port_id' => $info_D->port_destiny->id, 'carrier_id' => $info_D->carrier->id, 'currency_id' => $info_D->currency->id, 'quote_id' => $quote->id, 'remarks' => $remarks, 'transit_time' => $transitTime, 'via' => $viaT,'schedule_type'=>$service]);

                    $rate = AutomaticRate::create($request->all());

                    $oceanFreight = new Charge();
                    $oceanFreight->automatic_rate_id = $rate->id;
                    $oceanFreight->type_id = '3';
                    $oceanFreight->surcharge_id = null;
                    $oceanFreight->calculation_type_id = '5';
                    $oceanFreight->amount = $rates;
                    $oceanFreight->markups = $markups;
                    $oceanFreight->currency_id = $info_D->currency->id;
                    $oceanFreight->total = $rates;
                    $oceanFreight->save();

                    $rateTotals = new AutomaticRateTotal();
                    $rateTotals->quote_id = $quote->id;
                    $rateTotals->automatic_rate_id = $rate->id;
                    $rateTotals->origin_port_id = $rate->origin_port_id;
                    $rateTotals->destination_port_id = $rate->destination_port_id;
                    $rateTotals->currency_id = $info_D->currency->id;
                    $rateTotals->totals = null;
                    $rateTotals->markups = null;
                    $rateTotals->save();
                    $rateTotals->totalize($info_D->currency->id);

                    $inlandD = $request->input('inlandD' . $rateO->rate_id);
                    $inlandO = $request->input('inlandO' . $rateO->rate_id);
                    //INLAND DESTINO
                    if (!empty($inlandD)) {
                        foreach ($inlandD as $inlandDestiny) {

                            $inlandDestiny = json_decode($inlandDestiny);

                            $arregloMontoInDest = array();
                            $arregloMarkupsInDest = array();
                            $montoInDest = array();
                            $markupInDest = array();
                            foreach ($inlandDestiny->inlandDetails as $key => $inlandDet) {

                                if (@$inlandDet->sub_in != 0) {
                                    $arregloMontoInDest = array('c' . $key => $inlandDet->sub_in);
                                    $montoInDest = array_merge($arregloMontoInDest, $montoInDest);
                                }
                                if (@$inlandDet->markup != 0) {
                                    $arregloMarkupsInDest = array('m' . $key => $inlandDet->markup);
                                    $markupInDest = array_merge($arregloMarkupsInDest, $markupInDest);
                                }
                            }

                            //NEW TABLE INLAND ADDRESS
                            $inlandDestAddress = new InlandAddress();
                            $inlandDestAddress->quote_id = $quote->id;
                            $inlandDestAddress->address = $form->destination_address;
                            $inlandDestAddress->port_id = $inlandDestiny->port_id;
                            $inlandDestAddress->save();

                            $arregloMontoInDest = json_encode($montoInDest);
                            $arregloMarkupsInDest = json_encode($markupInDest);
                            $inlandDest = new AutomaticInland();
                            $inlandDest->quote_id = $quote->id;
                            $inlandDest->automatic_rate_id = $rate->id;
                            $inlandDest->provider = "Inland " . $form->destination_address;
                            $inlandDest->distance = $inlandDestiny->km;
                            $inlandDest->contract = $info_D->contract->id;
                            $inlandDest->port_id = $inlandDestiny->port_id;
                            $inlandDest->type = $inlandDestiny->type;
                            $inlandDest->rate = $arregloMontoInDest;
                            $inlandDest->markup = $arregloMarkupsInDest;
                            $inlandDest->validity_start = $inlandDestiny->validity_start;
                            $inlandDest->validity_end = $inlandDestiny->validity_end;
                            $inlandDest->currency_id = $info_D->idCurrency;
                            //FOR QUOTE MODULE, CREATED NEW FIELD CHARGE
                            $inlandDest->charge = $inlandDestiny->providerName;
                            //$inlandDest->provider_id = $inlandDestiny->prov_id;
                            $inlandDest->inland_address_id = $inlandDestAddress->id;
                            $inlandDest->save();

                            //NEW TABLE INLAND TOTALS
                            $inlandDestTotals = new AutomaticInlandTotal();
                            $inlandDestTotals->quote_id = $quote->id;
                            $inlandDestTotals->port_id = $inlandDestiny->port_id;
                            $inlandDestTotals->currency_id = $info_D->idCurrency;
                            $inlandDestTotals->totals = $arregloMontoInDest;
                            $inlandDestTotals->markups = $arregloMarkupsInDest;
                            $inlandDestTotals->type = $inlandDestiny->type;
                            $inlandDestTotals->inland_address_id = $inlandDestAddress->id;
                            $inlandDestTotals->save();
                        }
                    }
                    //INLAND ORIGEN

                    if (!empty($inlandO)) {

                        foreach ($inlandO as $inlandOrigin) {

                            $inlandOrigin = json_decode($inlandOrigin);

                            $arregloMontoInOrig = array();
                            $arregloMarkupsInOrig = array();
                            $montoInOrig = array();
                            $markupInOrig = array();
                            foreach ($inlandOrigin->inlandDetails as $key => $inlandDetails) {

                                if (@$inlandDetails->sub_in != 0) {
                                    $arregloMontoInOrig = array('c' . $key => $inlandDetails->sub_in);
                                    $montoInOrig = array_merge($arregloMontoInOrig, $montoInOrig);
                                }
                                if (@$inlandDetails->markup != 0) {
                                    $arregloMarkupsInOrig = array('m' . $key => $inlandDetails->markup);
                                    $markupInOrig = array_merge($arregloMarkupsInOrig, $markupInOrig);
                                }
                            }

                            //NEW TABLE INLAND ADDRESS
                            $inlandOrigAddress = new InlandAddress();
                            $inlandOrigAddress->quote_id = $quote->id;
                            $inlandOrigAddress->address = $form->origin_address;
                            $inlandOrigAddress->port_id = $inlandOrigin->port_id;
                            $inlandOrigAddress->save();

                            $arregloMontoInOrig = json_encode($montoInOrig);
                            $arregloMarkupsInOrig = json_encode($markupInOrig);
                            $inlandOrig = new AutomaticInland();
                            $inlandOrig->quote_id = $quote->id;
                            $inlandOrig->automatic_rate_id = $rate->id;
                            $inlandOrig->provider = "Inland " . $form->origin_address;
                            $inlandOrig->distance = $inlandOrigin->km;
                            $inlandOrig->contract = $info_D->contract->id;
                            $inlandOrig->port_id = $inlandOrigin->port_id;
                            $inlandOrig->type = $inlandOrigin->type;
                            $inlandOrig->rate = $arregloMontoInOrig;
                            $inlandOrig->markup = $arregloMarkupsInOrig;
                            $inlandOrig->validity_start = $inlandOrigin->validity_start;
                            $inlandOrig->validity_end = $inlandOrigin->validity_end;
                            $inlandOrig->currency_id = $info_D->idCurrency;
                            //FOR QUOTE MODULE, CREATED NEW FIELD
                            //$inlandOrig->provider_id = $inlandOrigin->prov_id;
                            $inlandOrig->charge = $inlandOrigin->providerName;
                            $inlandOrig->inland_address_id = $inlandOrigAddress->id;
                            $inlandOrig->save();

                            //NEW TABLE INLAND TOTALS
                            $inlandOrigTotals = new AutomaticInlandTotal();
                            $inlandOrigTotals->quote_id = $quote->id;
                            $inlandOrigTotals->port_id = $inlandOrigin->port_id;
                            $inlandOrigTotals->currency_id = $info_D->idCurrency;
                            $inlandOrigTotals->totals = $arregloMontoInOrig;
                            $inlandOrigTotals->markups = $arregloMarkupsInOrig;
                            $inlandOrigTotals->inland_address_id = $inlandOrigAddress->id;
                            $inlandOrigTotals->type = $inlandOrigin->type;
                            $inlandOrigTotals->save();
                        }
                    }

                    $this->saveRemarks($rate->id, $info_D->port_origin, $info_D->port_destiny, $info_D->carrier->id, $form->mode);
                    //Por pais
                    $this->saveRemarks($rate->id, $info_D->port_origin->country, $info_D->port_destiny->country, $info_D->carrier->id, $form->mode, 'country');
                }
                //CHARGES ORIGIN
                foreach ($info_D->localorigin as $localorigin) {
                    $arregloMontoO = array();
                    $arregloMarkupsO = array();
                    $montoO = array();
                    $markupO = array();
                    foreach ($localorigin as $localO) {
                        foreach ($localO as $local) {
                            if ($local->type != '99') {
                                $arregloMontoO = array('c' . $local->type => $local->monto);
                                $montoO = array_merge($arregloMontoO, $montoO);
                                $arregloMarkupsO = array('m' . $local->type => $local->markup);
                                $markupO = array_merge($arregloMarkupsO, $markupO);
                            }
                            if ($local->type == '99') {
                                $arregloO = array('type_id' => '1', 'surcharge_id' => $local->surcharge_id, 'calculation_type_id' => $local->calculation_id, 'currency_id' => $local->currency_id);
                            }
                        }
                    }

                    $arregloMontoO = json_encode($montoO);
                    $arregloMarkupsO = json_encode($markupO);

                    $chargeOrigin = new Charge();
                    $chargeOrigin->automatic_rate_id = $rate->id;
                    $chargeOrigin->type_id = $arregloO['type_id'];
                    $chargeOrigin->surcharge_id = $arregloO['surcharge_id'];
                    $chargeOrigin->calculation_type_id = $arregloO['calculation_type_id'];
                    $chargeOrigin->amount = $arregloMontoO;
                    $chargeOrigin->markups = $arregloMarkupsO;
                    $chargeOrigin->currency_id = $arregloO['currency_id'];
                    $chargeOrigin->total = $arregloMarkupsO;
                    $chargeOrigin->save();
                }

                // CHARGES DESTINY
                foreach ($info_D->localdestiny as $localdestiny) {
                    $arregloMontoD = array();
                    $arregloMarkupsD = array();
                    $montoD = array();
                    $markupD = array();
                    foreach ($localdestiny as $localD) {
                        foreach ($localD as $local) {
                            if ($local->type != '99') {

                                $arregloMontoD = array('c' . $local->type => $local->monto);
                                $montoD = array_merge($arregloMontoD, $montoD);
                                $arregloMarkupsD = array('m' . $local->type => $local->markup);
                                $markupD = array_merge($arregloMarkupsD, $markupD);
                            }
                            if ($local->type == '99') {
                                $arregloD = array('type_id' => '2', 'surcharge_id' => $local->surcharge_id, 'calculation_type_id' => $local->calculation_id, 'currency_id' => $local->currency_id);
                            }
                        }
                    }

                    $arregloMontoD = json_encode($montoD);
                    $arregloMarkupsD = json_encode($markupD);

                    $chargeDestiny = new Charge();
                    $chargeDestiny->automatic_rate_id = $rate->id;
                    $chargeDestiny->type_id = $arregloD['type_id'];
                    $chargeDestiny->surcharge_id = $arregloD['surcharge_id'];
                    $chargeDestiny->calculation_type_id = $arregloD['calculation_type_id'];
                    $chargeDestiny->amount = $arregloMontoD;
                    $chargeDestiny->markups = $arregloMarkupsD;
                    $chargeDestiny->currency_id = $arregloD['currency_id'];
                    $chargeDestiny->total = $arregloMarkupsD;
                    $chargeDestiny->save();
                }

                // CHARGES FREIGHT
                foreach ($info_D->localfreight as $localfreight) {
                    $arregloMontoF = array();
                    $arregloMarkupsF = array();
                    $montoF = array();
                    $markupF = array();
                    foreach ($localfreight as $localF) {
                        foreach ($localF as $local) {
                            if ($local->type != '99') {
                                $arregloMontoF = array('c' . $local->type => $local->monto);
                                $montoF = array_merge($arregloMontoF, $montoF);
                                $arregloMarkupsF = array('m' . $local->type => $local->markup);
                                $markupF = array_merge($arregloMarkupsF, $markupF);
                            }
                            if ($local->type == '99') {
                                $arregloF = array('type_id' => '3', 'surcharge_id' => $local->surcharge_id, 'calculation_type_id' => $local->calculation_id, 'currency_id' => $local->currency_id);
                            }
                        }
                    }
                    $arregloMontoF = json_encode($montoF);
                    $arregloMarkupsF = json_encode($markupF);

                    $chargeFreight = new Charge();
                    $chargeFreight->automatic_rate_id = $rate->id;
                    $chargeFreight->type_id = $arregloF['type_id'];
                    $chargeFreight->surcharge_id = $arregloF['surcharge_id'];
                    $chargeFreight->calculation_type_id = $arregloF['calculation_type_id'];
                    $chargeFreight->amount = $arregloMontoF;
                    $chargeFreight->markups = $arregloMarkupsF;
                    $chargeFreight->currency_id = $arregloF['currency_id'];
                    $chargeFreight->total = $arregloMarkupsF;
                    $chargeFreight->save();
                }
            }

            // Terminos Automatica
            $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
            $language_id = $company->companyUser->pdf_language;
            $this->saveTerms($quote->id, 'FCL', $form->mode);
            //$this->saveRemarks($quote->id,$remarksGenerales);
        }

        if ($type != 1) {
            return redirect()->action('QuotationController@edit', $quote);
        } else {
            return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
        }
    }

    /**
     * Store new rates
     * @param Request $request
     * @return STRING Json
     */
    public function storeRates(StoreAddRatesQuotes $request)
    {

        $arregloNull = array();
        $arregloNull = json_encode($arregloNull);
        $quote = QuoteV2::find($request->input('quote_id'));
        $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
        $idCurrency = $company->companyUser->currency_id;
        $dateQ = explode('/', $request->input('date'));
        $since = $dateQ[0];
        $until = $dateQ[1];

        // FCL & LCL
        if ($quote->type == 'FCL' || $quote->type == 'LCL') {
            foreach ($request->input('originport') as $origP) {
                $infoOrig = explode("-", $origP);
                $origin_port[] = $infoOrig[0];
            }
            foreach ($request->input('destinyport') as $destP) {
                $infoDest = explode("-", $destP);
                $destiny_port[] = $infoDest[0];
            }
            foreach ($origin_port as $orig) {
                foreach ($destiny_port as $dest) {
                    $request->request->add(['contract' => '', 'origin_port_id' => $orig, 'destination_port_id' => $dest, 'carrier_id' => $request->input('carrieManual'), 'rates' => $arregloNull, 'validity_start' => $since, 'validity_end' => $until, 'markups' => $arregloNull, 'currency_id' => $request->input('currency_id'), 'total' => $arregloNull, 'quote_id' => $quote->id]);
                    $rate = AutomaticRate::create($request->all());
                }
            }
        } else if ($quote->type == 'AIR') {
            $request->request->add(['contract' => '', 'origin_airport_id' => $request->input('origin_airport_id'), 'destination_airport_id' => $request->input('destination_airport_id'), 'airline_id' => $request->input('airline_id'), 'rates' => $arregloNull, 'markups' => $arregloNull, 'validity_start' => $since, 'validity_end' => $until, 'currency_id' => $request->input('currency_id'), 'total' => $arregloNull, 'quote_id' => $quote->id]);
            $rate = AutomaticRate::create($request->all());
        }

        if ($quote->type == 'FCL') {
            $charge = new Charge();
            $charge->automatic_rate_id = $rate->id;
            $charge->type_id = 3;
            $charge->calculation_type_id = 5;
            $charge->amount = $arregloNull;
            $charge->markups = $arregloNull;
            $charge->currency_id = $idCurrency;
            $charge->save();
        } else {
            $charge = new ChargeLclAir();
            $charge->automatic_rate_id = $rate->id;
            $charge->type_id = 3;
            $charge->calculation_type_id = 5;
            $charge->units = 0;
            $charge->price_per_unit = 0;
            $charge->markup = 0;
            $charge->total = 0;
            $charge->currency_id = $idCurrency;
            $charge->save();
        }

        return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
    }

    /**
     * Show modal with form to edit rates
     * @param integer $id
     * @return Illuminate\View\View
     */
    public function editRates($id)
    {
        $rate = AutomaticRate::find($id);
        $quote = QuoteV2::find($rate->quote_id);
        $harbors = Harbor::pluck('display_name', 'id');
        $carriers = Carrier::pluck('name', 'id');
        $airlines = Airline::pluck('name', 'id');
        $currencies = Currency::pluck('alphacode', 'id');

        return view('quotesv2.partials.editRate', compact('rate', 'quote', 'harbors', 'carriers', 'airlines', 'currencies'));
    }

    /**
     * Update rates
     * @param integer $id
     * @return Illuminate\View\View
     */
    public function updateRates(Request $request, $id)
    {

        $rate = AutomaticRate::find($id);
        if ($request->origin_port_id) {
            $rate->origin_port_id = $request->origin_port_id;
        }
        if ($request->destination_port_id) {
            $rate->destination_port_id = $request->destination_port_id;
        }
        if ($request->origin_airport_id) {
            $rate->origin_airport_id = $request->origin_airport_id;
        }
        if ($request->destination_airport_id) {
            $rate->destination_airport_id = $request->destination_airport_id;
        }
        if ($request->origin_address) {
            $rate->origin_address = $request->origin_address;
        }
        if ($request->destination_address) {
            $rate->destination_address = $request->destination_address;
        }
        if ($request->carrier_id) {
            $rate->carrier_id = $request->carrier_id;
        }
        if ($request->airline_id) {
            $rate->airline_id = $request->airline_id;
        }

        $rate->transit_time = $request->transit_time;
        $rate->schedule_type = $request->schedule_type;
        $rate->via = $request->via;
        $rate->currency_id = $request->currency_id;
        $rate->update();

        return redirect()->action('QuoteV2Controller@show', setearRouteKey($rate->quote_id));
    }

    /**
     * Store new inlands
     * @param Request $request
     * @return Illuminate\View\View
     */
    public function storeInlands(Request $request)
    {

        $quote = QuoteV2::find($request->input('quote_id'));
        $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
        $idCurrency = $company->companyUser->currency_id;
        $dateQ = explode('/', $request->input('date'));
        $since = $dateQ[0];
        $until = $dateQ[1];

        if ($request->quote_type == 'FCL') {
            $arregloNull = array();
            $arregloNull = json_encode($arregloNull);
            $request->request->add(['contract' => '', 'rate' => $arregloNull, 'validity_start' => $since, 'validity_end' => $until, 'markup' => $arregloNull]);
            AutomaticInland::create($request->all());
        } else {
            $request->request->add(['contract' => '', 'validity_start' => $since, 'validity_end' => $until]);
            AutomaticInlandLclAir::create($request->all());
        }

        $notification = array(
            'toastr' => 'Inland saved successfully!',
            'alert-type' => 'success',
        );

        return back()->with($notification);
    }

    /**
     * Show modal with form to edit inlands
     * @param integer $id
     * @return Illuminate\View\View
     */
    public function editInlands($id)
    {
        $inland = AutomaticInland::find($id);
        $quote = QuoteV2::find($inland->quote_id);
        $harbors = Harbor::pluck('display_name', 'id');
        $carriers = Carrier::pluck('name', 'id');
        $airlines = Airline::pluck('name', 'id');
        $currencies = Currency::pluck('alphacode', 'id');

        return view('quotesv2.partials.editInland', compact('inland', 'quote', 'harbors', 'carriers', 'airlines', 'currencies'));
    }

    /**
     * Show modal with form to edit inlands lcl air
     * @param integer $id
     * @return Illuminate\View\View
     */
    public function editInlandsLcl($id)
    {
        $inland = AutomaticInlandLclAir::find($id);
        $quote = QuoteV2::find($inland->quote_id);
        $harbors = Harbor::pluck('display_name', 'id');
        $carriers = Carrier::pluck('name', 'id');
        $airlines = Airline::pluck('name', 'id');
        $currencies = Currency::pluck('alphacode', 'id');

        return view('quotesv2.partials.editInland', compact('inland', 'quote', 'harbors', 'carriers', 'airlines', 'currencies'));
    }

    /**
     * Update inlands
     * @param integer $id
     * @return Illuminate\View\View
     */
    public function updateInlands(Request $request, $id)
    {

        if ($request->quote_type == 'FCL') {
            $inland = AutomaticInland::find($id);
        } else {
            $inland = AutomaticInlandLclAir::find($id);
        }
        if ($request->port_id) {
            $inland->port_id = $request->port_id;
        }
        $inland->type = $request->type;
        $inland->provider = $request->provider;
        $inland->currency_id = $request->currency_id;
        $inland->update();

        $notification = array(
            'toastr' => 'Inland updated successfully!',
            'alert-type' => 'success',
        );

        return back()->with($notification);
    }

    /**
     * Description
     * @param type $pluck
     * @return type
     */

    public function skipPluck($pluck)
    {
        $skips = ["[", "]", "\""];
        return str_replace($skips, '', $pluck);
    }

    public function ratesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->get();
        foreach ($rates as $rate) {
            if ($typeCurrency == "USD") {
                $rateC = $rate->rates;
            } else {
                $rateC = $rate->rates_eur;
            }
        }
        return $rateC;
    }

    public function getRatesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->first();
        $changeCurrency = Currency::where('id', '=', $typeCurrency)->first();
        $inDolar = $rates->rates;
       
        $inChange = $changeCurrency->rates;

        $rateC = $inDolar / $inChange;
       
        return $rateC;
    }



    public function search()
    {

        $company_user_id = \Auth::user()->company_user_id;
        //variables del modal contract
        $group_containerC = GroupContainer::pluck('name', 'id');
        $group_containerC->prepend('Select an option', '');
        $carrierC = Carrier::pluck('name', 'id');
        $directionC = Direction::pluck('name', 'id');
        $harborsR = Harbor::get()->pluck('display_name', 'id');
        $surchargesS = Surcharge::where('company_user_id',$company_user_id)->get()->pluck('name', 'id');
        $calculationTypeS = CalculationType::get()->pluck('name', 'id');
        //Fin variables

        
        $incoterm = Incoterm::pluck('name', 'id');
        $incoterm->prepend('Select an option', '');
        $group_contain = GroupContainer::pluck('name', 'id');
        //$group_contain->prepend('Select an option', '');
        $contain = Container::pluck('code', 'id');
        $contain->prepend('Select an option', '');
        $containers = Container::get();
        $harbor_origin = array();
        $harbor_destination = array();
        $company_dropdown = null;
        $pricesG = Price::doesntHave('company_price')->where('company_user_id', $company_user_id)->pluck('name', 'id');

        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }
        $companies->prepend('Select an option', '0');
        $harbors = Harbor::get()->pluck('display_name', 'id_complete');
        $countries = Country::all()->pluck('name', 'id');

        $prices = Price::all()->pluck('name', 'id');
        $carrierMan = Carrier::all()->pluck('name', 'id');
        $airlines = Airline::all()->pluck('name', 'id');

        $company_user = User::where('id', \Auth::id())->first();
        if ($company_user->companyUser) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
        } else {
            $currency_name = '';
        }
        $currencies = Currency::all()->pluck('alphacode', 'id');
        $hideO = 'hide';
        $hideD = 'hide';
        $chargeOrigin = 'true';
        $chargeDestination = 'true';
        $chargeFreight = 'true';
        $chargeAPI = 'true';
        $chargeAPI_M = 'false';
        $chargeAPI_SF = 'false';
        $form['equipment'] = array('1', '2', '3');
        $form['company_id_quote'] = '';
        $form['mode'] = '1';
        $form['containerType'] = '1';
        $validateEquipment = $this->validateEquipment($form['equipment'], $containers);
        $containerType = $validateEquipment['gpId'];
        $carriersSelected = $carrierMan;
        $allCarrier = true;
        $destinationClass = 'col-lg-4';
        $origenClass = 'col-lg-4';

        $origA['ocultarOrigA'] = 'hide';
        $origA['ocultarorigComb'] = '';

        $destA['ocultarDestA'] = 'hide';
        $destA['ocultarDestComb'] = '';

        //dd($origen);

        return view('quotesv2/search', compact('companies', 'harbor_origin', 'harbor_destination', 'carrierMan', 'hideO', 'hideD', 'countries', 'harbors', 'prices', 'company_user', 'currencies', 'currency_name', 'incoterm', 'airlines', 'chargeOrigin', 'chargeDestination', 'chargeFreight', 'chargeAPI', 'form', 'chargeAPI_M', 'contain', 'chargeAPI_SF', 'group_contain', 'containerType', 'containers', 'carriersSelected', 'allCarrier', 'destinationClass', 'origenClass', 'origA', 'pricesG', 'company_dropdown', 'group_containerC', 'carrierC', 'directionC', 'harborsR','surchargesS','calculationTypeS'));
    }

    /**
     * Return rates after process search
     * @param Request $request
     * @return Illuminate\View\View
     */

    public function processSearch(SearchRateForm $request)
    {
        $company_user_id = \Auth::user()->company_user_id;
        //variables del modal contract
        $group_containerC = GroupContainer::pluck('name', 'id');
        $group_containerC->prepend('Select an option', '');
        $carrierC = Carrier::pluck('name', 'id');
        $directionC = Direction::pluck('name', 'id');
        $harborsR = Harbor::get()->pluck('display_name', 'id');
        $surchargesS = Surcharge::where('company_user_id',$company_user_id)->get()->pluck('name', 'id');
        $calculationTypeS = CalculationType::get()->pluck('name', 'id');
        //Fin variables

        $request->validated();

        $allCarrier = false;

        $user_id = \Auth::id();
        $container_calculation = ContainerCalculation::get();
        $containers = Container::get();
        $group_contain = GroupContainer::pluck('name', 'id');
        $pricesG = Price::doesntHave('company_price')->where('company_user_id', $company_user_id)->pluck('name', 'id');
        //$group_contain->prepend('Select an option', '');

        //Variables para cargar el  Formulario
        $chargesOrigin = $request->input('chargeOrigin');
        $chargesDestination = $request->input('chargeDestination');
        $chargesFreight = 'true';
        $containerType = $request->input('container_type');
        $carriersSelected = $request->input('carriers');
        // Address inland

        $origin_address = $request->input('origin_address');
        $destination_address = $request->input('destination_address');
        //Combos del distanciero para inlands

        $destinationA = $request->input('destinationA');
        $originA = $request->input('originA');
        if ($destinationA != null) {

            $destcomboA = InlandDistance::where('id', $destinationA)->first();

            $destinationA = $destcomboA->distance;
            $request->request->add(['destination_address' => $destcomboA->display_name]);
        }
        if ($originA != null) {

            $origcomboA = InlandDistance::where('id', $originA)->first();
            $originA = $origcomboA->distance;
            $request->request->add(['origin_address' => $origcomboA->display_name]);
        }

        $address = $request->input('origin_address') . " " . $request->input('destination_address');

        //dd($request->all());

        //resquest completo del form
        $form = $request->all();

        $incoterm = Incoterm::pluck('name', 'id');
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }
        $companies->prepend('Select an option', '0');
        $airlines = Airline::all()->pluck('name', 'id');
        $harbors = Harbor::get()->pluck('display_name', 'id_complete');
        $countries = Country::all()->pluck('name', 'id');
        $prices = Price::all()->pluck('name', 'id');
        $company_user = User::where('id', \Auth::id())->first();
        $carrierMan = Carrier::all()->pluck('name', 'id');
        $contain = Container::pluck('code', 'id');
        $contain->prepend('Select an option', '');
        $company_setting = CompanyUser::where('id', \Auth::user()->company_user_id)->first();

        $typeCurrency = 'USD';
        $idCurrency = 149;
        $currency_name = '';
        $arreglo = new collection();

        if ($company_setting->currency_id != null) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
            $typeCurrency = $company_setting->currency->alphacode;
            $idCurrency = $company_setting->currency_id;
        }

        $currencies = Currency::all()->pluck('alphacode', 'id');
        //Settings de la compañia
        $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
        // Request Formulario
        $origin_harbor_all = array();
        $destination_harbor_all = array();

        foreach ($request->input('originport') as $origP) {

            $infoOrig = explode("-", $origP);
            if ($infoOrig[2] == null) {
                $origin_port[] = $infoOrig[0];
                $origin_country[] = $infoOrig[1];
                $origin_harbor_all[] = $infoOrig[0] . "-" . $infoOrig[1] . "-" . $infoOrig[2];
                $orig = Harbor::where('harbor_parent', $infoOrig[0])->get();
                foreach ($orig as $or) {
                    $origin_port[] = "$or->id";
                    $origin_country[] = "$or->country_id";
                    $origin_harbor_all[] = "$or->id-$or->country_id-$or->harbor_parent";
                }
            } else {

                $orig = Harbor::where('id', $infoOrig[2])->orwhere('harbor_parent', $infoOrig[2])->get();
                foreach ($orig as $or) {
                    $origin_port[] = "$or->id";
                    $origin_country[] = "$or->country_id";
                    $origin_harbor_all[] = "$or->id-$or->country_id-$or->harbor_parent";
                }
            }
        }

        $origin_port = array_unique($origin_port);
        $origin_country = array_unique($origin_country);
        $origin_harbor_all = array_unique($origin_harbor_all);

        foreach ($request->input('destinyport') as $destP) {

            $infoDest = explode("-", $destP);
            $destiny_port[] = $infoDest[0];
            $destiny_country[] = $infoDest[1];

            if ($infoDest[2] == null) {
                $destiny_port[] = $infoDest[0];
                $destiny_country[] = $infoDest[1];
                $destination_harbor_all[] = $infoDest[0] . "-" . $infoDest[1] . "-" . $infoDest[2];

                $dest = Harbor::where('harbor_parent', $infoDest[0])->get();
                foreach ($dest as $dt) {
                    $destiny_port[] = "$dt->id";
                    $destiny_country[] = "$dt->country_id";
                    $destination_harbor_all[] = "$dt->id-$dt->country_id-$dt->harbor_parent";
                }
            } else {

                $dest = Harbor::where('id', $infoDest[2])->orwhere('harbor_parent', $infoDest[2])->get();
                foreach ($dest as $dt) {
                    $destiny_port[] = "$dt->id";
                    $destiny_country[] = "$dt->country_id";
                    $destination_harbor_all[] = "$dt->id-$dt->country_id-$dt->harbor_parent";
                }
            }
        }

        $destiny_port = array_unique($destiny_port);
        $destiny_country = array_unique($destiny_country);
        $destination_harbor_all = array_unique($destination_harbor_all);

        $form['originport'] = $origin_harbor_all;
        $form['destinyport'] = $destination_harbor_all;

        $equipment = $request->input('equipment');

        if ($request->input('equipment') != null) {
            $carriers = $this->divideCarriers($request->input('carriers'));
        } else {

            $carriers = Carrier::all()->pluck('id')->toArray();
            $carriers = $this->divideCarriers($carriers);
        }

        $chargesAPI = isset($carriers['api']['CMA']) ? true : null;
        $chargesAPI_M = isset($carriers['api']['MAERSK']) ? true : null;
        $chargesAPI_SF = isset($carriers['api']['SAFMARINE']) ? true : null;

        $arregloCarrier = $carriers['carriers'];

        $equipmentFilter = array();
        $delivery_type = $request->input('delivery_type');
        $price_id = $request->input('price_id');
        $modality_inland = $request->modality;
        $company_id = $request->input('company_id_quote');
        $mode = $request->mode;
        $company_dropdown = null;

        if ($company_id) {
            $company_dropdown = Company::where('id', $company_id)->pluck('business_name', 'id');
        }

        $validateEquipment = $this->validateEquipment($equipment, $containers);
        $groupContainer = $validateEquipment['gpId'];
        $containerCode = $containers->whereIn('id', $equipment)->pluck('code')->toArray();

        // Historial de busqueda
        $this->storeSearchV2($origin_port, $destiny_port, $request->input('date'), $containerCode, $delivery_type, $mode, $company_user_id, 'FCL');

        // Fecha Contrato
        $dateRange = $request->input('date');

        $dateRange = explode("/", $dateRange);
        $dateSince = $dateRange[0];

        $dateUntil = $dateRange[1];

        //Colecciones
        $inlandDestiny = new collection();
        $inlandOrigin = new collection();

        $harbor_origin = Harbor::whereIn('id', $origin_port)->get();
        $harbor_destination = Harbor::whereIn('id', $destiny_port)->get();

        $hideO = 'hide';
        $hideD = 'hide';

        $markup = $this->markups($price_id, $typeCurrency, $request); // 'share this post'
        // Fin Markups

        // Consulta base de datos rates

        if ($validateEquipment['count'] < 2) {

            if ($company_id != null || $company_id != 0) {
                $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $company_id) {
                    $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                        $a->where('user_id', '=', $user_id);
                    })->orDoesntHave('contract_user_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $company_id, $validateEquipment) {
                    $q->whereHas('contract_company_restriction', function ($b) use ($company_id) {
                        $b->where('company_id', '=', $company_id);
                    })->orDoesntHave('contract_company_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $validateEquipment, $company_setting) {
                    if ($company_setting->future_dates == 1) {
                        $q->where(function ($query) use ($dateSince) {
                            $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                        })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', '=', $validateEquipment['gpId']);
                    } else {
                        $q->where(function ($query) use ($dateSince, $dateUntil) {
                            $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                        })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', '=', $validateEquipment['gpId']);
                    }

                    // $q->where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil)->
                });
            } else {
                $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) {
                    $q->doesnthave('contract_user_restriction');
                })->whereHas('contract', function ($q) {
                    $q->doesnthave('contract_company_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $validateEquipment, $company_setting) {
                    if ($company_setting->future_dates == 1) {
                        $q->where(function ($query) use ($dateSince) {
                            $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                        })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', '=', $validateEquipment['gpId']);
                    } else {
                        $q->where(function ($query) use ($dateSince, $dateUntil) {
                            $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                        })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', '=', $validateEquipment['gpId']);
                    }
                });
            }

            // ************************* CONSULTA RATE API ******************************

            /*if ($chargesAPI != null) {

            $client = new Client();

            foreach ($origin_port as $orig) {
            foreach ($destiny_port as $dest) {
            //$url =  'http://maersk_scrap/rates/api/{code}/{orig}/{dest}/{date}';
            $url = env('CMA_API_URL', 'http://carrier.cargofive.com/rates/api/{code}/{orig}/{dest}/{date}');
            $url = str_replace(['{code}', '{orig}', '{dest}', '{date}'], ['cmacgm', $orig, $dest, trim($dateUntil)], $url);
            try {
            $response = $client->request('GET', $url);
            } catch (\Exception $e) {
            }

            //$response = $client->request('GET','http://cfive-api.eu-central-1.elasticbeanstalk.com/rates/HARIndex/'.$orig.'/'.$dest.'/'.trim($dateUntil));
            //  $response = $client->request('GET','http://cmacgm/rates/HARIndex/'.$orig.'/'.$dest.'/'.trim($dateUntil));
            }
            }
            $arreglo2 = RateApi::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id) {
            $q->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->where('number', 'CMA CGM');
            });
            }*/

            /* if ($chargesAPI_M != null) {

            $client = new Client();

            foreach ($origin_port as $orig) {
            foreach ($destiny_port as $dest) {

            $url = env('MAERSK_API_URL', 'http://carrier.cargofive.com/rates/api/{code}/{orig}/{dest}/{date}');
            $url = str_replace(['{code}', '{orig}', '{dest}', '{date}'], ['maersk', $orig, $dest, trim($dateUntil)], $url);

            try {
            $response = $client->request('GET', $url);
            } catch (\Exception $e) {
            }
            }
            }

            $arreglo3 = RateApi::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id) {
            $q->where('validity', '>=', $dateSince)->where('number', 'MAERSK');
            });
            }

            if ($chargesAPI_SF != null) {

            $client = new Client();
            foreach ($origin_port as $orig) {
            foreach ($destiny_port as $dest) {

            $url = env('SAFMARINE_API_URL', 'http://carrier.cargofive.com/rates/api/{code}/{orig}/{dest}/{date}');
            $url = str_replace(['{code}', '{orig}', '{dest}', '{date}'], ['safmarine', $orig, $dest, trim($dateUntil)], $url);

            try {
            $response = $client->request('GET', $url);
            } catch (\Exception $e) {
            }
            }
            }

            $arreglo4 = RateApi::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id) {
            $q->where('validity', '>=', $dateSince)->where('number', 'SAFMARINE');
            });
            }*/

            $arreglo = $this->filtrarRate($arreglo, $equipment, $validateEquipment['gpId'], $containers);

            $arreglo = $arreglo->get();

            /*if ($chargesAPI != null) {
            $arreglo2 = $this->filtrarRate($arreglo2, $equipment, $validateEquipment['gpId'], $containers);
            $arreglo2 = $arreglo2->get();

            $arreglo = $arreglo->merge($arreglo2);
            }*/

            /*    if ($chargesAPI_M != null) {
            $arreglo3 = $this->filtrarRate($arreglo3, $equipment, $validateEquipment['gpId'], $containers);
            $arreglo3 = $arreglo3->get();
            $arreglo = $arreglo->merge($arreglo3);
            }

            if ($chargesAPI_SF != null) {
            $arreglo4 = $this->filtrarRate($arreglo4, $equipment, $validateEquipment['gpId'], $containers);
            $arreglo4 = $arreglo4->get();

            $arreglo = $arreglo->merge($arreglo4);
            }*/

            $formulario = $request;
            $arrayContainers = CalculationType::where('options->group', true)->pluck('id')->toArray();
            $totalesCont = array();
            //Collection Equipment Dinamico
            $equipmentHides = $this->hideContainerV2($equipment, '', $containers);

            foreach ($containers as $cont) {
                $totalesContainer = array($cont->code => array('tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0));
                $totalesCont = array_merge($totalesContainer, $totalesCont);
                $var = 'array' . $cont->code;
                $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();
            }

            foreach ($arreglo as $data) {

                $contractStatus = $data->contract->status;
                $collectionRate = new Collection();
                $totalFreight = 0;
                $totalRates = 0;

                $totalT20 = 0;
                $totalT40 = 0;
                $totalT40hc = 0;
                $totalT40nor = 0;
                $totalT45 = 0;
                $totalT = 0;
                //Variables Totalizadoras
                $totales = array();

                //Arreglo totalizador de freight , destination , origin por contenedor
                $totalesCont = array();
                $arregloRateSum = array();
                foreach ($containers as $cont) {
                    $totalesContainer = array($cont->code => array('tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0));
                    $totalesCont = array_merge($totalesContainer, $totalesCont);
                    // Inicializar arreglo rate
                    $arregloRate = array('c' . $cont->code => '0');
                    $arregloRateSum = array_merge($arregloRateSum, $arregloRate);
                }

                $carrier[] = $data->carrier_id;
                $orig_port = array($data->origin_port);
                $dest_port = array($data->destiny_port);
                $rateDetail = new collection();
                $collectionOrigin = new collection();
                $collectionDestiny = new collection();
                $collectionFreight = new collection();

                // ************************* CONSULTA INLANDS ******************************

                $typeCurrencyI = $this->getTypeCurrency($chargesOrigin, $chargesDestination, $data, $typeCurrency);
                $inlandParams = array(
                    'company_id_quote' => $company_id, 'destiny_port' => $dest_port,
                    'origin_port' => $orig_port, 'company_user_id' => $company_user_id,
                    'origin_address' => $origin_address, 'destination_address' => $destination_address,
                    'typeCurrency' => $typeCurrencyI,
                );
                $destA = array();
                $origA = array();

                if ($delivery_type == "2" || $delivery_type == "4") {

                    $hideD = '';
                    $dataDest = array();

                    if ($destinationA == null) {
                        $dataDest = $this->inlands($inlandParams, $markup, $equipment, $containers, 'destino', $mode, $groupContainer);
                        $destA['ocultarDestA'] = '';
                        $destA['ocultarDestComb'] = 'hide';
                    } else {
                        $dataDest = $this->inlands($inlandParams, $markup, $equipment, $containers, 'destino', $mode, $groupContainer, $destinationA);
                        $destA['ocultarDestA'] = 'hide';
                        $destA['ocultarDestComb'] = '';
                    }

                    if (!empty($dataDest)) {
                        $inlandDestiny = Collection::make($dataDest);
                    }
                }
                // Origin Addrees

                if ($delivery_type == "3" || $delivery_type == "4") {
                    $hideO = '';
                    $dataOrig = array();
                    if ($originA == null) {
                        $dataOrig = $this->inlands($inlandParams, $markup, $equipment, $containers, 'origen', $mode, $groupContainer);
                        $origA['ocultarOrigA'] = '';
                        $origA['ocultarorigComb'] = 'hide';
                    } else {
                        $dataOrig = $this->inlands($inlandParams, $markup, $equipment, $containers, 'origen', $mode, $groupContainer, $originA);
                        $origA['ocultarOrigA'] = 'hide';
                        $origA['ocultarorigComb'] = '';
                    }

                    if (!empty($dataOrig)) {
                        $inlandOrigin = Collection::make($dataOrig);
                    }
                }

                // Fin del calculo de los inlands

                $arregloRate = array();
                //Arreglos para guardar el rate

                $arregloRateSave['markups'] = array();
                $arregloRateSave['rate'] = array();
                //Arreglo para guardar charges
                $arregloCharges['origin'] = array();

                $arregloOrigin = array();
                $arregloFreight = array();
                $arregloDestiny = array();
                // globales
                $arregloOriginG = array();
                $arregloFreightG = array();
                $arregloDestinyG = array();

                $rateC = $this->ratesCurrency($data->currency->id, $typeCurrency);

                //ACACAMBIOS

                $rateFreight = $this->getRatesCurrency($data->currency->id, $idCurrency);

                // Rates
                $arregloR = $this->rates($equipment, $markup, $data, $rateC, $typeCurrency, $containers,  $rateFreight);

                $arregloRateSum = array_merge($arregloRateSum, $arregloR['arregloSum']);
                //dd($arregloRateSum);
                $arregloRateSave['rate'] = array_merge($arregloRateSave['rate'], $arregloR['arregloSaveR']);
                $arregloRateSave['markups'] = array_merge($arregloRateSave['markups'], $arregloR['arregloSaveM']);
                $arregloRate = array_merge($arregloRate, $arregloR['arregloRate']);

                $equipmentFilter = $arregloR['arregloEquipment'];

                // id de los port  ALL
                array_push($orig_port, 1485);
                array_push($dest_port, 1485);
                // id de los carrier ALL
                $carrier_all = 26;
                array_push($carrier, $carrier_all);
                // Id de los paises
                array_push($origin_country, 250);
                array_push($destiny_country, 250);

                // ################### Calculos local  Charges #############################
                if ($contractStatus != 'api') {
                    $localChar = LocalCharge::where('contract_id', '=', $data->contract_id)->whereHas('localcharcarriers', function ($q) use ($carrier) {
                        $q->whereIn('carrier_id', $carrier);
                    })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                        $query->whereHas('localcharports', function ($q) use ($orig_port, $dest_port) {
                            $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                        })->orwhereHas('localcharcountries', function ($q) use ($origin_country, $destiny_country) {
                            $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                        });
                    })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm')->orderBy('typedestiny_id', 'calculationtype_id', 'surchage_id')->get();
                } else {

                    $localChar = LocalChargeApi::where('contract_id', '=', $data->contract_id)->whereHas('localcharcarriers', function ($q) use ($carrier) {
                        $q->whereIn('carrier_id', $carrier);
                    })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                        $query->whereHas('localcharports', function ($q) use ($orig_port, $dest_port) {
                            $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                        })->orwhereHas('localcharcountries', function ($q) use ($origin_country, $destiny_country) {
                            $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                        });
                    })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm')->orderBy('typedestiny_id', 'calculationtype_id', 'surchage_id')->get();
                }

                foreach ($localChar as $local) {

                    $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);

                    // Condicion para enviar los terminos de venta o compra
                    if (isset($local->surcharge->saleterm->name)) {
                        $terminos = $local->surcharge->saleterm->name;
                    } else {
                        $terminos = $local->surcharge->name;
                    }

                    foreach ($local->localcharcarriers as $localCarrier) {
                        if ($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id == $carrier_all) {
                            $localParams = array('terminos' => $terminos, 'local' => $local, 'data' => $data, 'typeCurrency' => $typeCurrency, 'idCurrency' => $idCurrency, 'localCarrier' => $localCarrier);
                            //Origin
                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $band = false;
                                    foreach ($containers as $cont) {
                                        $name_arreglo = 'array' . $cont->code;
                                        if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {

                                            $montoOrig = $local->ammount;
                                            $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id, $cont->code);
                                            $monto = $local->ammount / $rateMount;
                                            $monto = $this->perTeu($monto, $local->calculationtype_id, $cont->code);
                                            $monto = number_format($monto, 2, '.', '');
                                            $markupGe = $this->localMarkupsFCL($markup['charges']['localPercentage'], $markup['charges']['localAmmount'], $markup['charges']['localMarkup'], $monto, $montoOrig, $typeCurrency, $markup['charges']['markupLocalCurre'], $local->currency->id);
                                            $arregloOrigin = $this->ChargesArray($localParams, $monto, $montoOrig, $cont->code);
                                            $arregloOrigin = array_merge($arregloOrigin, $markupGe);
                                            $collectionOrigin->push($arregloOrigin);
                                            $totalesCont[$cont->code]['tot_' . $cont->code . '_O'] += $markupGe['montoMarkup'];
                                            $band = true;
                                        }
                                    }
                                    if ($band) {
                                        if (in_array($local->calculationtype_id, $arrayContainers)) {
                                            $valores = $this->asociarPerCont($local->calculationtype_id);
                                            $arregloOrigin = $this->ChargesArray99($localParams, $valores['id'], $valores['name']);
                                        } else {
                                            $arregloOrigin = $this->ChargesArray99($localParams, $local->calculationtype->id, $local->calculationtype->name);
                                        }
                                        $collectionOrigin->push($arregloOrigin);
                                    }
                                }
                            }
                            //Destiny
                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $band = false;
                                    foreach ($containers as $cont) {

                                        $name_arreglo = 'array' . $cont->code;

                                        if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                            $montoOrig = $local->ammount;
                                            $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id, $cont->code);
                                            $monto = $local->ammount / $rateMount;
                                            $monto = $this->perTeu($monto, $local->calculationtype_id, $cont->code);
                                            $monto = number_format($monto, 2, '.', '');
                                            $markupGe = $this->localMarkupsFCL($markup['charges']['localPercentage'], $markup['charges']['localAmmount'], $markup['charges']['localMarkup'], $monto, $montoOrig, $typeCurrency, $markup['charges']['markupLocalCurre'], $local->currency->id);
                                            $arregloDestiny = $this->ChargesArray($localParams, $monto, $montoOrig, $cont->code);
                                            $arregloDestiny = array_merge($arregloDestiny, $markupGe);
                                            $collectionDestiny->push($arregloDestiny);
                                            $totalesCont[$cont->code]['tot_' . $cont->code . '_D'] += $markupGe['montoMarkup'];
                                            $band = true;
                                        }
                                    }
                                    if ($band) {

                                        if (in_array($local->calculationtype_id, $arrayContainers)) {
                                            $valores = $this->asociarPerCont($local->calculationtype_id);
                                            $arregloDestiny = $this->ChargesArray99($localParams, $valores['id'], $valores['name']);
                                        } else {
                                            $arregloDestiny = $this->ChargesArray99($localParams, $local->calculationtype->id, $local->calculationtype->name);
                                        }
                                        $collectionDestiny->push($arregloDestiny);
                                    }
                                }
                            }
                            //Freight
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    $band = false;
                                    //Se ajusta el calculo para freight tomando en cuenta el rate currency
                                    //$rateMount_Freight = $this->ratesCurrency($local->currency->id, $data->currency->alphacode);
                                    $rateMount_Freight = $this->getRatesCurrency($local->currency->id, $data->currency->id);
                                    $localParams['typeCurrency'] = $data->currency->alphacode;
                                    $localParams['idCurrency'] = $data->currency->id;
                                    //Fin Variables

                                   // dd($rateMount_Freight);

                                    foreach ($containers as $cont) {

                                        $name_arreglo = 'array' . $cont->code;

                                        if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                            $montoOrig = $local->ammount;
                                            $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id, $cont->code);
                                            $monto = $local->ammount / $rateMount_Freight;
                                           
                                            $monto = number_format($monto, 2, '.', '');
                                            $monto = $this->perTeu($monto, $local->calculationtype_id, $cont->code);
                                            $markupGe = $this->localMarkupsTrait($markup['charges']['localPercentage'], $markup['charges']['localAmmount'], $markup['charges']['localMarkup'], $monto, $montoOrig, $typeCurrency, $markup['charges']['markupLocalCurre'], $local->currency->id,$rateMount_Freight);
                                            $arregloFreight = $this->ChargesArray($localParams, $monto, $montoOrig, $cont->code);
                                            $arregloFreight = array_merge($arregloFreight, $markupGe);
                                            //dd($markupGe);
                                            $collectionFreight->push($arregloFreight);
                                            $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] += $markupGe['montoMarkup'];
                                           
                                            $band = true;
                                        }
                                    }

                                    if ($band) {
                                        if (in_array($local->calculationtype_id, $arrayContainers)) {
                                            $valores = $this->asociarPerCont($local->calculationtype_id);
                                            $arregloFreight = $this->ChargesArray99($localParams, $valores['id'], $valores['name']);
                                        } else {
                                            $arregloFreight = $this->ChargesArray99($localParams, $local->calculationtype->id, $local->calculationtype->name);
                                        }
                                        $collectionFreight->push($arregloFreight);
                                    }
                                }
                            }
                        }
                    }
                }
                // ################## Fin local Charges        #############################
                //################## Calculos Global Charges #################################

                if ($contractStatus != 'api') {

                    $globalChar = GlobalCharge::where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrier', function ($q) use ($carrier) {
                        $q->whereIn('carrier_id', $carrier);
                    })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                        $query->orwhereHas('globalcharport', function ($q) use ($orig_port, $dest_port) {
                            $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                        })->orwhereHas('globalcharcountry', function ($q) use ($origin_country, $destiny_country) {
                            $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                        })->orwhereHas('globalcharportcountry', function ($q) use ($orig_port, $destiny_country) {
                            $q->whereIn('port_orig', $orig_port)->whereIn('country_dest', $destiny_country);
                        })->orwhereHas('globalcharcountryport', function ($q) use ($origin_country, $dest_port) {
                            $q->whereIn('country_orig', $origin_country)->whereIn('port_dest', $dest_port);
                        });
                    })->whereDoesntHave('globalexceptioncountry', function ($q) use ($origin_country, $destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->orwhereIn('country_dest', $destiny_country);;
                    })->whereDoesntHave('globalexceptionport', function ($q) use ($orig_port, $dest_port) {
                        $q->whereIn('port_orig', $orig_port)->orwhereIn('port_dest', $dest_port);;
                    })->where('company_user_id', '=', $company_user_id)->with('globalcharcarrier.carrier', 'currency', 'surcharge.saleterm')->get();

                    foreach ($globalChar as $global) {
                        $rateMount = $this->ratesCurrency($global->currency->id, $typeCurrency);
                        // Condicion para enviar los terminos de venta o compra
                        if (isset($global->surcharge->saleterm->name)) {
                            $terminos = $global->surcharge->saleterm->name;
                        } else {
                            $terminos = $global->surcharge->name;
                        }
                        foreach ($global->globalcharcarrier as $globalCarrier) {
                            if ($globalCarrier->carrier_id == $data->carrier_id || $globalCarrier->carrier_id == $carrier_all) {
                                $globalParams = array('terminos' => $terminos, 'local' => $global, 'data' => $data, 'typeCurrency' => $typeCurrency, 'idCurrency' => $idCurrency, 'localCarrier' => $globalCarrier);
                                //Origin
                                if ($chargesOrigin != null) {
                                    if ($global->typedestiny_id == '1') {
                                        $band = false;
                                        foreach ($containers as $cont) {

                                            $name_arreglo = 'array' . $cont->code;

                                            if (in_array($global->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {

                                                $montoOrig = $global->ammount;
                                                $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id, $cont->code);
                                                $monto = $global->ammount / $rateMount;
                                                $monto = $this->perTeu($monto, $global->calculationtype_id, $cont->code);
                                                $monto = number_format($monto, 2, '.', '');
                                                $markupGe = $this->localMarkupsFCL($markup['charges']['localPercentage'], $markup['charges']['localAmmount'], $markup['charges']['localMarkup'], $monto, $montoOrig, $typeCurrency, $markup['charges']['markupLocalCurre'], $global->currency->id);
                                                $arregloOriginG = $this->ChargesArray($globalParams, $monto, $montoOrig, $cont->code);
                                                $arregloOriginG = array_merge($arregloOriginG, $markupGe);
                                                $collectionOrigin->push($arregloOriginG);
                                                $totalesCont[$cont->code]['tot_' . $cont->code . '_O'] += $markupGe['montoMarkup'];
                                                $band = true;
                                            }
                                        }

                                        if ($band) {
                                            if (in_array($global->calculationtype_id, $arrayContainers)) {
                                                $valores = $this->asociarPerCont($global->calculationtype_id);
                                                $arregloOriginG = $this->ChargesArray99($globalParams, $valores['id'], $valores['name']);
                                            } else {
                                                $arregloOriginG = $this->ChargesArray99($globalParams, $global->calculationtype->id, $global->calculationtype->name);
                                            }
                                            $collectionOrigin->push($arregloOriginG);
                                        }
                                    }
                                }
                                //Destiny
                                if ($chargesDestination != null) {
                                    if ($global->typedestiny_id == '2') {
                                        $band = false;
                                        foreach ($containers as $cont) {
                                            $name_arreglo = 'array' . $cont->code;
                                            if (in_array($global->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {

                                                $montoOrig = $global->ammount;
                                                $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id, $cont->code);
                                                $monto = $global->ammount / $rateMount;
                                                $monto = $this->perTeu($monto, $global->calculationtype_id, $cont->code);
                                                $monto = number_format($monto, 2, '.', '');
                                                $markupGe = $this->localMarkupsFCL($markup['charges']['localPercentage'], $markup['charges']['localAmmount'], $markup['charges']['localMarkup'], $monto, $montoOrig, $typeCurrency, $markup['charges']['markupLocalCurre'], $global->currency->id);
                                                $arregloDestinyG = $this->ChargesArray($globalParams, $monto, $montoOrig, $cont->code);
                                                $arregloDestinyG = array_merge($arregloDestinyG, $markupGe);
                                                $collectionDestiny->push($arregloDestinyG);
                                                $totalesCont[$cont->code]['tot_' . $cont->code . '_D'] += $markupGe['montoMarkup'];
                                                $band = true;
                                            }
                                        }

                                        if ($band) {
                                            if (in_array($global->calculationtype_id, $arrayContainers)) {
                                                $valores = $this->asociarPerCont($global->calculationtype_id);
                                                $arregloDestinyG = $this->ChargesArray99($globalParams, $valores['id'], $valores['name']);
                                            } else {
                                                $arregloDestinyG = $this->ChargesArray99($globalParams, $global->calculationtype->id, $global->calculationtype->name);
                                            }
                                            $collectionDestiny->push($arregloDestinyG);
                                        }
                                    }
                                }
                                //Freight
                                if ($chargesFreight != null) {
                                    if ($global->typedestiny_id == '3') {

                                        //$rateMount_Freight = $this->ratesCurrency($global->currency->id, $data->currency->alphacode);
                                        $rateMount_Freight = $this->getRatesCurrency($global->currency->id, $data->currency->id);
                                        $globalParams['typeCurrency'] = $data->currency->alphacode;
                                        $globalParams['idCurrency'] = $data->currency->id;
                                        //Fin Variables
                                        $band = false;
                                        foreach ($containers as $cont) {
                                            $name_arreglo = 'array' . $cont->code;

                                            if (in_array($global->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {

                                                $montoOrig = $global->ammount;
                                                $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id, $cont->code);
                                                $monto = $global->ammount / $rateMount_Freight;
                                                $monto = $this->perTeu($monto, $global->calculationtype_id, $cont->code);
                                                $monto = number_format($monto, 2, '.', '');
                                                $markupGe = $this->localMarkupsFCL($markup['charges']['localPercentage'], $markup['charges']['localAmmount'], $markup['charges']['localMarkup'], $monto, $montoOrig, $typeCurrency, $markup['charges']['markupLocalCurre'], $global->currency->id);
                                                $arregloFreightG = $this->ChargesArray($globalParams, $monto, $montoOrig, $cont->code);
                                                $arregloFreightG = array_merge($arregloFreightG, $markupGe);
                                                $collectionFreight->push($arregloFreightG);
                                                $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] += $markupGe['montoMarkup'] / $rateMount_Freight;
                                                
                                                $band = true;
                                            }
                                        }
                                        
                                        if ($band) {
                                            if (in_array($global->calculationtype_id, $arrayContainers)) {
                                                $valores = $this->asociarPerCont($global->calculationtype_id);
                                                $arregloFreightG = $this->ChargesArray99($globalParams, $valores['id'], $valores['name']);
                                            } else {
                                                $arregloFreightG = $this->ChargesArray99($globalParams, $global->calculationtype->id, $global->calculationtype->name);
                                            }
                                            $collectionFreight->push($arregloFreightG);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } // fin if contract Api
                // ############################ Fin global charges ######################

                // Ordenar las colecciones
                if (!empty($collectionFreight)) {
                    $collectionFreight = $this->OrdenarCollection($collectionFreight);
                }

                if (!empty($collectionDestiny)) {
                    $collectionDestiny = $this->OrdenarCollection($collectionDestiny);
                }

                if (!empty($collectionOrigin)) {
                    //dd($collectionOrigin);
                    $collectionOrigin = $this->OrdenarCollection($collectionOrigin);
                }

                $totalRates += $totalT;
                $array = array('type' => 'Ocean Freight', 'detail' => 'Per Container', 'subtotal' => $totalRates, 'total' => $totalRates . " " . $typeCurrency, 'idCurrency' => $data->currency_id, 'currency_rate' => $data->currency->alphacode, 'rate_id' => $data->id);
                $array = array_merge($array, $arregloRate);
                $array = array_merge($array, $arregloRateSave);
                $collectionRate->push($array);

                // SCHEDULE

                $transit_time = $this->transitTime($data->port_origin->id, $data->port_destiny->id, $data->carrier->id, $data->contract->status);

                $data->setAttribute('via', $transit_time['via']);
                $data->setAttribute('transit_time', $transit_time['transit_time']);
                $data->setAttribute('service', $transit_time['service']);

                //remarks
                $typeMode = $request->input('mode');
                $remarks = "";
                if ($data->contract->remarks != "") {
                    $remarks = $data->contract->remarks . "<br>";
                }

                $remarksGeneral = "";
                $remarksGeneral .= $this->remarksCondition($data->port_origin, $data->port_destiny, $data->carrier, $typeMode);
                //Remark Por pais
                $remarksGeneral .= $this->remarksCondition($data->port_origin->country, $data->port_destiny->country, $data->carrier, $typeMode, 'country');

                $data->setAttribute('remarks', $remarks);
                $data->setAttribute('remarksG', $remarksGeneral);

                // EXCEL REQUEST
                $excelRequestFCL = "0";
                $excelRequest = "0";
                $excelRequestIdFCL = "0";
                $excelRequestId = "0";

                if ($data->contract->status != 'api') {

                    $excelRequestFCL = ContractFclFile::where('contract_id', $data->contract->id)->first();
                    if (!empty($excelRequestFCL)) {
                        $excelRequestIdFCL = $excelRequestFCL->id;
                    } else {
                        $excelRequestIdFCL = "0";
                    }

                    $excelRequest = NewContractRequest::where('contract_id', $data->contract->id)->first();
                    if (!empty($excelRequest)) {
                        $excelRequestId = $excelRequest->id;
                    } else {
                        $excelRequestId = "0";
                    }
                }

                $idContract = 0;
                $totalItems = 0;
                if ($data->contract->status != 'api') {
                    $mediaItems = $data->contract->getMedia('document');
                    $totalItems = count($mediaItems);
                    if ($totalItems > 0) {
                        $idContract = $data->contract->id;
                    }
                }
                // Franja APIS
                $color = '';
                if ($data->contract->status == 'api') {
                    if ($data->contract->number == 'MAERSK') {
                        $color = 'bg-maersk';
                    } else {
                        $color = 'bg-danger';
                    }
                }

                $colores = '';
                if ($data->contract->is_manual == 1) {
                    $colores = 'bg-manual';
                } else {
                    $colores = 'bg-api';
                }

                // Valores
                $data->setAttribute('excelRequest', $excelRequestId);
                $data->setAttribute('excelRequestFCL', $excelRequestIdFCL);
                $data->setAttribute('rates', $collectionRate);
                $data->setAttribute('localfreight', $collectionFreight);
                $data->setAttribute('localdestiny', $collectionDestiny);
                $data->setAttribute('localorigin', $collectionOrigin);
                // Valores totales por contenedor

                if ($chargesDestination == null && $chargesOrigin == null) {

                    $typeCurrency = $data->currency->alphacode;
                    $idCurrency = $data->currency->id;

                    //$rateTot = $this->ratesCurrency($data->currency->id, $typeCurrency);
                    $rateTot = $this->getRatesCurrency($data->currency->id, $idCurrency);
                } else {
                    //$rateTot = $this->ratesCurrency($data->currency->id, $typeCurrency);
                    $rateTot = $this->getRatesCurrency($data->currency->id, $idCurrency);
                }
                
                foreach ($containers as $cont) {

                   // dd($totalesCont['40HC']['tot_40HC_F'] + $arregloRateSum['c40HC'] );
                    $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] = $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $arregloRateSum['c' . $cont->code];
                   // dd($totalesCont2,$totalesCont['40HC']['tot_40HC_F']);
                    $data->setAttribute('tot' . $cont->code . 'F', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_F'], 2, '.', ''));

                    $data->setAttribute('tot' . $cont->code . 'O', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_O'], 2, '.', ''));
                    $data->setAttribute('tot' . $cont->code . 'D', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_D'], 2, '.', ''));

                    $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] = $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] / $rateTot;
                 
                    // dd($totalesCont[$cont->code]['tot_' . $cont->code . '_F']);
                    // TOTALES
                    $name_tot = 'totalT' . $cont->code;
                    $$name_tot = $totalesCont[$cont->code]['tot_' . $cont->code . '_D'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_O'];
                    $data->setAttribute($name_tot, number_format($$name_tot, 2, '.', ''));
                }
                //dd($totalesCont);

                //Contrato Futuro
                $contratoFuturo = $this->contratoFuturo($data->contract->validity, $dateSince, $data->contract->expire, $dateUntil);

                $data->setAttribute('contratoFuturo', $contratoFuturo);
                // INLANDS

                $data->setAttribute('inlandDestiny', $inlandDestiny->where('port_id', $data->destiny_port));
                //   dd($inlandDestiny);
                $data->setAttribute('inlandOrigin', $inlandOrigin->where('port_id', $data->origin_port));
                $data->setAttribute('typeCurrency', $typeCurrency);

                $data->setAttribute('idCurrency', $idCurrency);
                //Excel
                $data->setAttribute('totalItems', $totalItems);
                $data->setAttribute('idContract', $idContract);
                //COlor
                $data->setAttribute('color', $color);
                $data->setAttribute('contract_color', $colores);
            }

            // Ordenar por Monto Total  de contenedor de menor a mayor

            foreach ($containers as $cont) {
                $name_tot = 'totalT' . $cont->code;

                if (in_array($cont->id, $equipmentFilter)) {
                    $arreglo = $arreglo->sortBy($name_tot);
                    break;
                }
            }
        } // fin validate equipment

        // Clases Origin y Destination Delivery type FCL
        if ($delivery_type == 1) {
            $destinationClass = 'col-lg-4';
            $origenClass = 'col-lg-4';
        }

        if ($delivery_type == 2 || $delivery_type == 3) {
            $destinationClass = 'col-lg-2';
            $origenClass = 'col-lg-4';
        }

        if ($delivery_type == 4) {
            $destinationClass = 'col-lg-2';
            $origenClass = 'col-lg-2';
        }

        $chargeOrigin = ($chargesOrigin != null) ? true : false;
        $chargeDestination = ($chargesDestination != null) ? true : false;
        $chargeFreight = ($chargesFreight != null) ? true : false;
        $chargeAPI = ($chargesAPI != null) ? true : false;
        $chargeAPI_M = ($chargesAPI_M != null) ? true : false;
        $chargeAPI_SF = ($chargesAPI_SF != null) ? true : false;
        $containerType = $validateEquipment['gpId'];
        $isDecimal = optional(Auth::user()->companyUser)->decimals;

        return view('quotesv2/search', compact('arreglo', 'form', 'companies', 'countries', 'harbors', 'prices', 'company_user', 'currencies', 'currency_name', 'incoterm', 'equipmentHides', 'carrierMan', 'hideD', 'hideO', 'airlines', 'chargeOrigin', 'chargeDestination', 'chargeFreight', 'chargeAPI', 'chargeAPI_M', 'contain', 'containers', 'validateEquipment', 'group_contain', 'chargeAPI_SF', 'containerType', 'carriersSelected', 'equipment', 'allCarrier', 'destinationClass', 'origenClass', 'destinationA', 'originA', 'isDecimal', 'harbor_origin', 'harbor_destination', 'pricesG', 'company_dropdown','group_containerC','group_containerC','carrierC','directionC','harborsR','surchargesS','calculationTypeS')); //aqui
    }

    public function perTeu($monto, $calculation_type, $code)
    {
        $arrayTeu = CalculationType::where('options->isteu', true)->pluck('id')->toArray();
        $codeArray = Container::where('code', 'like', '20%')->pluck('code')->toArray();

        if (!in_array($code, $codeArray)) {
            if (in_array($calculation_type, $arrayTeu)) {
                $monto = $monto * 2;
                return $monto;
            } else {
                return $monto;
            }
        } else {
            return $monto;
        }
    }

    public function perTeu2($monto, $calculation_type, $code)
    {
        $codeArray = array('20DV', '20RF');
        if (!in_array($code, $codeArray)) {
            if ($calculation_type == 4) {
                $monto = $monto * 2;
                return $monto;
            } else {
                return $monto;
            }
        } else {
            return $monto;
        }
    }

    public function inlandMarkup($inlandPercentage, $inlandAmmount, $inlandMarkup, $monto, $typeCurrency, $markupInlandCurre)
    {

        if ($inlandPercentage != 0) {
            $markup = ($monto * $inlandPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkupI = array("markup" => $markup, "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)", 'montoInlandT' => $monto, 'montoMarkupO' => $markup);
        } else {

            $markup = $inlandAmmount;
            $markup = number_format($markup, 2, '.', '');
            $monto += number_format($inlandMarkup, 2, '.', '');
            $arraymarkupI = array("markup" => $markup, "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre, 'montoInlandT' => $monto, 'montoMarkupO' => $markup);
        }
        return $arraymarkupI;
    }

    public function freightMarkups($freighPercentage, $freighAmmount, $freighMarkup, $monto, $typeCurrency, $type)
    {

        if ($freighPercentage != 0) {
            $freighPercentage = intval($freighPercentage);
            $markup = ($monto * $freighPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" . $type => $markup, "markupConvert" . $type => $markup, "typemarkup" . $type => "$typeCurrency ($freighPercentage%)", "monto" . $type => $monto, 'montoMarkupO' => $markup);
        } else {

            $markup = trim($freighAmmount);
            $monto += $freighMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" . $type => $markup, "markupConvert" . $type => $freighMarkup, "typemarkup" . $type => $typeCurrency, "monto" . $type => $monto, 'montoMarkupO' => $markup);
        }

        return $arraymarkup;
    }

    public function freightMarkupsFCL($freighPercentage, $freighAmmount, $freighMarkup, $monto, $typeCurrency, $type, $chargeCurrency)
    {

        if ($freighPercentage != 0) {
            $freighPercentage = intval($freighPercentage);
            $markup = ($monto * $freighPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" . $type => $markup, "markupConvert" . $type => $markup, "typemarkup" . $type => "$typeCurrency ($freighPercentage%)", "monto" . $type => $monto, 'montoMarkupO' . $type => $monto);
        } else {

            $valor = $this->ratesCurrency($chargeCurrency, $typeCurrency);

            $markupOrig = $freighMarkup * $valor;
            $montoOrig = $monto;

            $markup = trim($freighAmmount);
            $monto += $freighMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" . $type => $markup, "markupConvert" . $type => $markupOrig, "typemarkup" . $type => $typeCurrency, "monto" . $type => $monto, 'montoMarkupO' . $type => number_format($montoOrig + $markupOrig, 2, '.', ''));
        }

        return $arraymarkup;
    }

    public function localMarkups($localPercentage, $localAmmount, $localMarkup, $monto, $typeCurrency, $markupLocalCurre)
    {

        if ($localPercentage != 0) {
            $markup = ($monto * $localPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $arraymarkup = array("markup" => $markup, "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($localPercentage%)", 'montoMarkup' => $monto);
        } else {
            $markup = $localAmmount;
            $markup = number_format($markup, 2, '.', '');
            $monto += $localMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" => $markup, "markupConvert" => $localMarkup, "typemarkup" => $markupLocalCurre, 'montoMarkup' => $monto);
        }

        return $arraymarkup;
    }

    public function localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $chargeCurrency)
    {
       
        if ($localPercentage != 0) {

            // Monto original
            $markupO = ($montoOrig * $localPercentage) / 100;
            $montoOrig += $markupO;
            $montoOrig = number_format($montoOrig, 2, '.', '');

            $markup = ($monto * $localPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupO, "typemarkup" => "$typeCurrency ($localPercentage%)", 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig);
        } else { // oki
            $valor = $this->ratesCurrency($chargeCurrency, $typeCurrency);

            if ($valor == '1') {
                $markupOrig = $localMarkup * $valor;
            } else {
                $markupOrig = $localMarkup * $valor;
            }

            $markup = trim($localMarkup);
            $markup = number_format($markup, 2, '.', '');
            $monto += $localMarkup;
            $monto = number_format($monto, 2, '.', '');

            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupOrig, "typemarkup" => $markupLocalCurre, 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig + $markupOrig);
        }

        return $arraymarkup;
    }

    public function OrdenarCollection($collection)
    {

        $collection = $collection->groupBy([
            'surcharge_name',
            function ($item) {
                return $item['type'];
            },
        ], $preserveKeys = true);

        // Se Ordena y unen la collection
        $collect = new collection();
        $monto = 0;
        $montoMarkup = 0;
        $totalMarkup = 0;

        foreach ($collection as $item) {
            $total = count($item['99']);
            $fin = array();

            foreach ($item['99'] as $test) {
                $fin[] = $test['currency_id'];
            }
            $resultado = array_unique($fin);
            foreach ($item as $items) {
                $totalPadres = count($item['99']);
                // $totalhijos = count($items);

                if ($totalPadres >= 2 && count($resultado) > 1) {
                    foreach ($items as $itemsDetail) {

                        $monto += $itemsDetail['monto'];
                        $montoMarkup += $itemsDetail['montoMarkup'];
                        $totalMarkup += $itemsDetail['markupConvert'];
                    }
                    $itemsDetail['monto'] = number_format($monto, 2, '.', '');
                    $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', '');
                    $itemsDetail['markup'] = number_format($totalMarkup, 2, '.', '');
                    $itemsDetail['currency'] = $itemsDetail['typecurrency'];
                    $itemsDetail['currency_id'] = $itemsDetail['currency_orig_id'];
                    $collect->push($itemsDetail);
                    $monto = 0;
                    $montoMarkup = 0;
                    $totalMarkup = 0;
                } /*else if($totalhijos > 1 ){
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

                }*/else {
                    $monto = 0;
                    $montoMarkup = 0;
                    $markup = 0;

                    foreach ($items as $itemsDetail) { //aca

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
            function ($item) use ($collect) {
                $collect->put('x', 'surcharge_name');
                return $item['type'];
            },
        ], $preserveKeys = true);

        return $collect;
    }

    public function excelDownload($id, $idFcl, $idContract)
    {

        if ($idContract == 0) {
            $Ncontract = NewContractRequest::find($id);
            $mode_search = false;
            if (!empty($Ncontract)) {
                $success = false;
                $descarga = null;
                if (!empty($Ncontract->namefile)) {
                    $time = new \DateTime();
                    $now = $time->format('d-m-y');
                    $company = CompanyUser::find($Ncontract->company_user_id);
                    $extObj = new \SplFileInfo($Ncontract->namefile);
                    $ext = $extObj->getExtension();
                    $name = $Ncontract->id . '-' . $company->name . '_' . $now . '-FLC.' . $ext;
                } else {
                    $mode_search = true;
                    $Ncontract->load('companyuser');
                    $data = json_decode($Ncontract->data, true);
                    $time = new \DateTime();
                    $now = $time->format('d-m-y');
                    $mediaItem = $Ncontract->getFirstMedia('document');
                    $extObj = new \SplFileInfo($mediaItem->file_name);
                    $ext = $extObj->getExtension();
                    $name = $Ncontract->id . '-' . $Ncontract->companyuser->name . '_' . $data['group_containers']['name'] . '_' . $now . '-FLC.' . $ext;
                    $descarga = Storage::disk('s3_upload')->url('Request/FCL/' . $mediaItem->id . '/' . $mediaItem->file_name, $name);
                    $success = true;
                }
            } else {
                $Ncontract = ContractFclFile::find($idFcl);
                $time = new \DateTime();
                $now = $time->format('d-m-y');
                $extObj = new \SplFileInfo($Ncontract->namefile);
                $ext = $extObj->getExtension();
                $name = $Ncontract->id . '-' . $now . '-FLC.' . $ext;
            }

            if ($mode_search == false) {
                if (Storage::disk('s3_upload')->exists('Request/FCL/' . $Ncontract->namefile, $name)) {
                    $success = true;
                    $descarga = Storage::disk('s3_upload')->url('Request/FCL/' . $Ncontract->namefile, $name);
                } elseif (Storage::disk('s3_upload')->exists('contracts/' . $Ncontract->namefile, $name)) {
                    $success = true;
                    $descarga = Storage::disk('s3_upload')->url('contracts/' . $Ncontract->namefile, $name);
                } elseif (Storage::disk('FclRequest')->exists($Ncontract->namefile, $name)) {
                    $success = true;
                    $descarga = Storage::disk('FclRequest')->url($Ncontract->namefile, $name);
                } elseif (Storage::disk('UpLoadFile')->exists($Ncontract->namefile, $name)) {
                    $success = true;
                    $descarga = Storage::disk('UpLoadFile')->url($Ncontract->namefile, $name);
                }
            }
            return response()->json(['success' => $success, 'url' => $descarga]);
        } else {
            $contract = Contract::find($idContract);
            $downloads = $contract->getMedia('document');
            $total = count($downloads);
            if ($total > 1) {
                return MediaStream::create('my-contract.zip')->addMedia($downloads);
            } else {
                $media = $downloads->first();
                $mediaItem = Media::find($media->id);
                return $mediaItem;
            }
        }
    }

    public function excelDownloadLCL($id, $idlcl)
    {

        $Ncontract = NewContractRequestLcl::find($id);
        if (!empty($Ncontract)) {

            $time = new \DateTime();
            $now = $time->format('d-m-y');
            $company = CompanyUser::find($Ncontract->company_user_id);
            $extObj = new \SplFileInfo($Ncontract->namefile);
            $ext = $extObj->getExtension();
            $name = $Ncontract->id . '-' . $company->name . '_' . $now . '-LCL.' . $ext;
        } else {
            $Ncontract = ContractLclFile::find($idlcl);
            $time = new \DateTime();
            $now = $time->format('d-m-y');
            $extObj = new \SplFileInfo($Ncontract->namefile);
            $ext = $extObj->getExtension();
            $name = $Ncontract->id . '-' . $now . '-LCL.' . $ext;
        }

        $success = false;
        $descarga = null;

        if (Storage::disk('s3_upload')->exists('Request/LCL/' . $Ncontract->namefile, $name)) {
            $success = true;
            //return 1;
            $descarga = Storage::disk('s3_upload')->url('Request/LCL/' . $Ncontract->namefile, $name);
        } elseif (Storage::disk('s3_upload')->exists('contracts/' . $Ncontract->namefile, $name)) {
            //return 2;
            $success = true;
            $descarga = Storage::disk('s3_upload')->url('contracts/' . $Ncontract->namefile, $name);
        } elseif (Storage::disk('LclRequest')->exists($Ncontract->namefile, $name)) {
            //return 3;
            $success = true;
            $descarga = Storage::disk('LclRequest')->url($Ncontract->namefile, $name);
        } elseif (Storage::disk('UpLoadFile')->exists($Ncontract->namefile, $name)) {
            //return 4;
            $success = true;
            $descarga = Storage::disk('UpLoadFile')->url($Ncontract->namefile, $name);
        }

        return response()->json(['success' => $success, 'url' => $descarga]);

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

    public function totalPalletPackage($total_quantity, $cargo_type, $type_load_cargo, $quantity)
    {

        $cantidad_pack_pallet = array();

        if ($total_quantity != null) {
            if ($cargo_type == '1') { //Pallet
                $cantidad_pack_pallet = array('pallet' => ['cantidad' => $total_quantity], 'package' => ['cantidad' => 0]);
            } else {
                $cantidad_pack_pallet = array('pallet' => ['cantidad' => 0], 'package' => ['cantidad' => $total_quantity]);
            }
        } else {
            $cantidadPallet = 0;
            $cantidadPackage = 0;
            $type_load_cargo = array_values(array_filter($type_load_cargo));
            $quantity = array_values(array_filter($quantity));
            $count = count($type_load_cargo);
            for ($i = 0; $i < $count; $i++) {
                if ($type_load_cargo[$i] == '1') { //Pallet
                    $cantidadPallet += $quantity[$i];
                } else {
                    $cantidadPackage += $quantity[$i];
                }
            }
            $cantidad_pack_pallet = array('pallet' => ['cantidad' => $cantidadPallet], 'package' => ['cantidad' => $cantidadPackage]);
        }

        return $cantidad_pack_pallet;
    }

    /*  **************************  LCL  ******************************************** */
    public function processSearchLCL(Request $request)
    {

        //Variables del usuario conectado
        $company_user_id = \Auth::user()->company_user_id;
        $user_id = \Auth::id();

        //Variables para cargar el  Formulario
        $chargesOrigin = $request->input('chargeOrigin');
        $chargesDestination = $request->input('chargeDestination');
        $chargesFreight = true;
        $chargesAPI = $request->input('chargeAPI');
        $chargesAPI_M = $request->input('chargeAPI_M');
        $chargesAPI_SF = $request->input('chargeAPI_SF');

        $form = $request->all();

        // Traer cantidad total de paquetes y pallet segun sea el caso
        $package_pallet = $this->totalPalletPackage($request->input('total_quantity'), $request->input('cargo_type'), $request->input('type_load_cargo'), $request->input('quantity'));

        //dd($package_pallet);

        $incoterm = Incoterm::pluck('name', 'id');
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }
        $companies->prepend('Select an option', '0');
        $airlines = Airline::all()->pluck('name', 'id');
        $harbors = Harbor::get()->pluck('display_name', 'id_complete');
        $countries = Country::all()->pluck('name', 'id');
        $prices = Price::all()->pluck('name', 'id');
        $company_user = User::where('id', \Auth::id())->first();
        $carrierMan = Carrier::all()->pluck('name', 'id');
        $contain = Container::pluck('code', 'id');
        $contain->prepend('Select an option', '');
        $company_setting = CompanyUser::where('id', \Auth::user()->company_user_id)->first();
        $typeCurrency = 'USD';
        $idCurrency = 149;

        $currency_name = '';

        if ($company_setting->currency_id != null) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
            $typeCurrency = $company_setting->currency->alphacode;
            $idCurrency = $company_setting->currency_id;
        }

        /*if ($company_user->companyUser) {
        $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();
        } else {
        $currency_name = '';
        }*/

        if ($request->input('total_weight') != null) {

            $simple = 'show active';
            $paquete = '';
        }
        if ($request->input('total_weight_pkg') != null) {

            $simple = '';
            $paquete = 'show active';
        }

        $currencies = Currency::all()->pluck('alphacode', 'id');

        //Settings de la compañia
        $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
        /*$typeCurrency = $company->companyUser->currency->alphacode;
        $idCurrency = $company->companyUser->currency_id;*/

        // Request Formulario
        foreach ($request->input('originport') as $origP) {

            $infoOrig = explode("-", $origP);
            $origin_port[] = $infoOrig[0];
            $origin_country[] = $infoOrig[1];
        }
        foreach ($request->input('destinyport') as $destP) {

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
        $this->storeSearchV2($origin_port, $destiny_port, $request->input('date'), $arregloNull, $delivery_type, $mode, $company_user_id, 'LCL');

        $weight = $request->input("chargeable_weight");
        $weight = number_format($weight, 2, '.', '');
        // Fecha Contrato
        $dateRange = $request->input('date');
        $dateRange = explode("/", $dateRange);
        $dateSince = $dateRange[0];
        $dateUntil = $dateRange[1];

        //Collection Equipment Dinamico
        //Markups

        $fclMarkup = Price::whereHas('company_price', function ($q) use ($price_id) {
            $q->where('price_id', '=', $price_id);
        })->with('freight_markup', 'local_markup', 'inland_markup')->get();
        $freighPercentage = 0;
        $freighAmmount = 0;
        $localPercentage = 0;
        $localAmmount = 0;
        $inlandPercentage = 0;
        $inlandAmmount = 0;
        $freighMarkup = 0;
        $localMarkup = 0;
        $inlandMarkup = 0;
        $markupFreightCurre = $typeCurrency;
        $markupLocalCurre = $typeCurrency;
        $markupInlandCurre = $typeCurrency;
        foreach ($fclMarkup as $freight) {
            // Freight
            $fclFreight = $freight->freight_markup->where('price_type_id', '=', 2);
            $freighPercentage = $this->skipPluck($fclFreight->pluck('percent_markup'));

            // markup currency
            $markupFreightCurre = $this->skipPluck($fclFreight->pluck('currency'));
            // markup con el monto segun la moneda
            $freighMarkup = $this->ratesCurrency($markupFreightCurre, $typeCurrency);
            // Objeto con las propiedades del currency
            $markupFreightCurre = Currency::find($markupFreightCurre);
            $markupFreightCurre = $markupFreightCurre->alphacode;
            // Monto original
            $freighAmmount = $this->skipPluck($fclFreight->pluck('fixed_markup'));
            // monto aplicado al currency
            $freighMarkup = $freighAmmount / $freighMarkup;
            $freighMarkup = number_format($freighMarkup, 2, '.', '');

            // Local y global
            $fclLocal = $freight->local_markup->where('price_type_id', '=', 2);
            // markup currency

            if ($request->modality == "1") {
                $markupLocalCurre = $this->skipPluck($fclLocal->pluck('currency_export'));
                // valor de la conversion segun la moneda
                $localMarkup = $this->ratesCurrency($markupLocalCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupLocalCurre = Currency::find($markupLocalCurre);
                $markupLocalCurre = $markupLocalCurre->alphacode;
                // En caso de ser Porcentaje
                $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_export')));
                // Monto original
                $localAmmount = intval($this->skipPluck($fclLocal->pluck('fixed_markup_export')));
                // monto aplicado al currency
                if ($localMarkup == 0) {
                    $localMarkup = 1;
                }

                $localMarkup = $localAmmount / $localMarkup;
                $localMarkup = number_format($localMarkup, 2, '.', '');
            } else {
                $markupLocalCurre = $this->skipPluck($fclLocal->pluck('currency_import'));
                // valor de la conversion segun la moneda
                $localMarkup = $this->ratesCurrency($markupLocalCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupLocalCurre = Currency::find($markupLocalCurre);
                $markupLocalCurre = $markupLocalCurre->alphacode;
                // en caso de ser porcentake
                $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_import')));
                // monto original
                $localAmmount = intval($this->skipPluck($fclLocal->pluck('fixed_markup_import')));
                // monto aplicado al currency
                if ($localMarkup == 0) {
                    $localMarkup = 1;
                }

                $localMarkup = $localAmmount / $localMarkup;
                $localMarkup = number_format($localMarkup, 2, '.', '');
            }
            // Inlands
            $fclInland = $freight->inland_markup->where('price_type_id', '=', 2);
            if ($request->modality == "1") {
                $markupInlandCurre = $this->skipPluck($fclInland->pluck('currency_export'));
                // valor de la conversion segun la moneda
                $inlandMarkup = $this->ratesCurrency($markupInlandCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupInlandCurre = Currency::find($markupInlandCurre);
                $markupInlandCurre = $markupInlandCurre->alphacode;
                // en caso de ser porcentake
                $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_export')));
                // Monto original
                $inlandAmmount = intval($this->skipPluck($fclInland->pluck('fixed_markup_export')));
                // monto aplicado al currency
                if ($inlandMarkup == 0) {
                    $inlandMarkup = 1;
                }

                $inlandMarkup = $inlandAmmount / $inlandMarkup;
                $inlandMarkup = number_format($inlandMarkup, 2, '.', '');
            } else {
                $markupInlandCurre = $this->skipPluck($fclInland->pluck('currency_import'));

                // valor de la conversion segun la moneda
                $inlandMarkup = $this->ratesCurrency($markupInlandCurre, $typeCurrency);

                // Objeto con las propiedades del currency por monto fijo
                $markupInlandCurre = Currency::find($markupInlandCurre);

                $markupInlandCurre = $markupInlandCurre->alphacode;
                // en caso de ser porcentake
                $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_import')));
                // monto original
                $inlandAmmount = intval($this->skipPluck($fclInland->pluck('fixed_markup_import')));
                // monto aplicado al currency
                if ($inlandMarkup == 0) {
                    $inlandMarkup = 1;
                }

                $inlandMarkup = $inlandAmmount / $inlandMarkup;

                $inlandMarkup = number_format($inlandMarkup, 2, '.', '');
            }
        }

        //Colecciones

        $collectionRate = new Collection();

        // Rates LCL

        $arreglo = RateLcl::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
            $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                $a->where('user_id', '=', $user_id);
            })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
            $q->whereHas('contract_company_restriction', function ($b) use ($company_id) {
                $b->where('company_id', '=', $company_id);
            })->orDoesntHave('contract_company_restriction');
        })->whereHas('contract', function ($q) use ($company_user_id, $dateSince, $dateUntil, $company_setting) {
            if ($company_setting->future_dates == 1) {
                $q->where(function ($query) use ($dateSince) {
                    $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                })->where('company_user_id', '=', $company_user_id);
            } else {
                $q->where(function ($query) use ($dateSince, $dateUntil) {
                    $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                })->where('company_user_id', '=', $company_user_id);
            }
        })->get();

        foreach ($arreglo as $data) {

            //dd($arreglo);

            $tt = $data->transit_time;
            $va = $data->via;

            $totalFreight = 0;
            $FreightCharges = 0;
            $totalRates = 0;
            $totalOrigin = 0;
            $totalDestiny = 0;
            $totalQuote = 0;
            $totalAmmount = 0;
            $collectionOrig = new Collection();
            $collectionDest = new Collection();
            $collectionFreight = new Collection();
            $collectionGloOrig = new Collection();
            $collectionGloDest = new Collection();
            $collectionGloFreight = new Collection();
            $collectionRate = new Collection();

            $dataGOrig = array();
            $dataGDest = array();
            $dataGFreight = array();

            $dataOrig = array();
            $dataDest = array();
            $dataFreight = array();

            $rateC = $this->ratesCurrency($data->currency->id, $data->currency->alphacode);

            $typeCurrencyFreight = $data->currency->alphacode;
            $idCurrencyFreight = $data->currency->id;

            $subtotal = 0;

            $inlandDestiny = new Collection();
            $inlandOrigin = new Collection();
            $totalChargeOrig = 0;
            $totalChargeDest = 0;
            $totalInland = 0;

            if ($request->input('total_weight') != null) {

                $simple = 'show active';
                $paquete = '';
                $subtotalT = $weight * $data->uom;
                $totalT = ($weight * $data->uom) / $rateC;
                $priceRate = $data->uom;

                if ($subtotalT < $data->minimum) {
                    $subtotalT = $data->minimum;
                    $totalT = $subtotalT / $rateC;
                    $priceRate = $data->minimum / $weight;
                    $priceRate = number_format($priceRate, 2, '.', '');
                }

                // MARKUPS
                if ($freighPercentage != 0) {
                    $freighPercentage = intval($freighPercentage);
                    $markup = ($totalT * $freighPercentage) / 100;
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $markup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)");
                } else {

                    $markup = trim($freighAmmount);
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $freighMarkup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre);
                }

                $totalT = number_format($totalT, 2, '.', '');
                $totalFreight += $totalT;
                $totalRates += $totalT;

                $array = array('type' => 'Ocean Freight', 'cantidad' => $weight, 'detail' => 'W/M', 'price' => $priceRate, 'currency' => $data->currency->alphacode, 'subtotal' => $subtotalT, 'total' => $totalT . " " . $typeCurrency, 'idCurrency' => $data->currency_id);
                $array = array_merge($array, $arraymarkupT);
                $collectionRate->push($array);
                $data->setAttribute('montF', $array);
            }
            // POR PAQUETE
            if ($request->input('total_weight_pkg') != null) {

                $simple = '';
                $paquete = 'show active';
                $subtotalT = $weight * $data->uom;
                $totalT = ($weight * $data->uom) / $rateC;
                $priceRate = $data->uom;

                if ($subtotalT < $data->minimum) {
                    $subtotalT = $data->minimum;
                    $totalT = $subtotalT / $rateC;
                    $priceRate = $data->minimum / $weight;
                    $priceRate = number_format($priceRate, 2, '.', '');
                }
                // MARKUPS
                if ($freighPercentage != 0) {
                    $freighPercentage = intval($freighPercentage);
                    $markup = ($totalT * $freighPercentage) / 100;
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $markup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($freighPercentage%)");
                } else {

                    $markup = trim($freighAmmount);
                    $markup = number_format($markup, 2, '.', '');
                    $totalT += $freighMarkup;
                    $arraymarkupT = array("markup" => $markup, "markupConvert" => $freighMarkup, "typemarkup" => $markupFreightCurre);
                }

                $totalT = number_format($totalT, 2, '.', '');
                $totalFreight += $totalT;
                $totalRates += $totalT;
                $array = array('type' => 'Ocean Freight', 'cantidad' => $weight, 'detail' => 'W/M', 'price' => $priceRate, 'currency' => $data->currency->alphacode, 'subtotal' => $subtotalT, 'total' => $totalT . " " . $typeCurrency, 'idCurrency' => $data->currency_id);
                $array = array_merge($array, $arraymarkupT);
                $collectionRate->push($array);
                $data->setAttribute('montF', $array);
            }

            $data->setAttribute('rates', $collectionRate);

            $orig_port = array($data->origin_port);
            $dest_port = array($data->destiny_port);
            $carrier[] = $data->carrier_id;

            // id de los port  ALL
            array_push($orig_port, 1485);
            array_push($dest_port, 1485);
            // id de los carrier ALL
            $carrier_all = 26;
            array_push($carrier, $carrier_all);
            // Id de los paises
            array_push($origin_country, 250);
            array_push($destiny_country, 250);

            //Calculation type
            $arrayBlHblShip = array('1', '2', '3', '16', '18'); // id  calculation type 1 = HBL , 2=  Shipment , 3 = BL , 16 per set
            $arraytonM3 = array('4', '11', '17'); //  calculation type 4 = Per ton/m3
            $arraytonCompli = array('6', '7', '12', '13'); //  calculation type 4 = Per ton/m3
            $arrayPerTon = array('5', '10'); //  calculation type 5 = Per  TON
            $arrayPerKG = array('9'); //  calculation type 5 = Per  TON
            $arrayPerPack = array('14'); //  per package
            $arrayPerPallet = array('15'); //  per pallet
            $arrayPerM3 = array('19'); //  per m3

            // Local charges
            $localChar = LocalChargeLcl::where('contractlcl_id', '=', $data->contractlcl_id)->whereHas('localcharcarrierslcl', function ($q) use ($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                $query->whereHas('localcharportslcl', function ($q) use ($orig_port, $dest_port) {
                    $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                })->orwhereHas('localcharcountrieslcl', function ($q) use ($origin_country, $destiny_country) {
                    $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                });
            })->with('localcharportslcl.portOrig', 'localcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm')->get();

            foreach ($localChar as $local) {

                $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);
                $rateC = $this->ratesCurrency($local->currency->id, $data->currency->alphacode);
                //Totales peso y volumen
                if ($request->input('total_weight') != null) {
                    $totalW = $request->input('total_weight') / 1000;
                    $totalV = $request->input('total_volume');
                } else {
                    $totalW = $request->input('total_weight_pkg') / 1000;
                    $totalV = $request->input('total_volume_pkg');
                }

                // Condicion para enviar los terminos de venta o compra
                if (isset($local->surcharge->saleterm->name)) {
                    $terminos = $local->surcharge->saleterm->name;
                } else {
                    $terminos = $local->surcharge->name;
                }

                if (in_array($local->calculationtypelcl_id, $arrayBlHblShip)) {
                    $cantidadT = 1;
                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateMount;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig, $markupBL);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $cantidadT);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }
                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateMount;
                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupBL);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $cantidadT);

                                    $collectionDest->push($arregloDest);
                                }
                            }
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateC;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloPC = array_merge($arregloPC, $markupBL);

                                    $collectionFreight->push($arregloPC);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arraytonM3)) {

                    //ROUNDED

                    if ($local->calculationtypelcl_id == '11') {
                        $ton_weight = ceil($weight);
                    } else {
                        $ton_weight = $weight;
                    }
                    $cantidadT = $ton_weight;

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTonM3 = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'idCurrency' => $local->currency->id, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigTonM3 = array_merge($arregloOrigTonM3, $markupTonM3);

                                    $collectionOrig->push($arregloOrigTonM3);

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $cantidadT);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }
                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'idCurrency' => $local->currency->id, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupTonM3);

                                    $collectionDest->push($arregloDest);

                                    // Arreglo 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $cantidadT);

                                    $collectionDest->push($arregloDest);
                                }
                            }
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateC;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $cantidadT, 'idCurrency' => $local->currency->id, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloPC = array_merge($arregloPC, $markupTonM3);

                                    $collectionFreight->push($arregloPC);

                                    // Arreglo 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerTon)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            //ROUNDED
                            if ($local->calculationtypelcl_id == '10') {
                                $totalW = ceil($totalW);
                            }

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTon = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigTon = array_merge($arregloOrigTon, $markupTON);
                                    $collectionOrig->push($arregloOrigTon);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupTON);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateC;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloPC = array_merge($arregloPC, $markupTON);

                                    $collectionFreight->push($arregloPC);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arraytonCompli)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {

                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }

                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    //$totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'currency_id' => $local->currency->id, 'montoOrig' => $totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig, $markupTONM3);
                                    $dataOrig[] = $arregloOrig;

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $dataOrig[] = $arregloOrigin;
                                    //$collectionOrig->push($arregloOrigin);

                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {
                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupTONM3);
                                    $dataDest[] = $arregloDest;

                                    // ARREGLO 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $dataDest[] = $arregloDest;
                                    //$collectionDest->push($arregloDest);

                                }
                            }

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {
                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateC;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateC;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateC;
                                            if ($totalW < 1) {
                                                $mont = $local->minimum * $totalW;
                                            } else {
                                                $mont = $local->minimum / $totalW;
                                            }
                                        }
                                    }
                                    // Markup
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloPC = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloPC = array_merge($arregloPC, $markupTONM3);
                                    $dataFreight[] = $arregloPC;

                                    // ARREGLO 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $dataFreight[] = $arregloFreight;
                                    //$collectionFreight->push($arregloFreight);

                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerKG)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalW * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $totalW;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigKg = array_merge($arregloOrigKg, $markupKG);
                                    $collectionOrig->push($arregloOrigKg);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalW * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $totalW;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestKg = array_merge($arregloDestKg, $markupKG);

                                    $collectionDest->push($arregloDestKg);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $totalW;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalW * $subtotal_local) / $rateC;
                                        $unidades = $subtotal_local / $totalW;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightKg = array_merge($arregloFreightKg, $markupKG);

                                    $collectionFreight->push($arregloFreightKg);
                                    // ARREGLO GENERAL 99

                                    $arregloFreightKg = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreightKg);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerPack)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $package_cantidad = $package_pallet['package']['cantidad'];
                            if ($chargesOrigin != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $package_cantidad;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpack = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigpack = array_merge($arregloOrigpack, $markupKG);
                                    $collectionOrig->push($arregloOrigpack);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestPack = array_merge($arregloDestPack, $markupKG);

                                    $collectionDest->push($arregloDestPack);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateC;
                                        $unidades = $subtotal_local / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightPack = array_merge($arregloFreightPack, $markupKG);

                                    $collectionFreight->push($arregloFreightPack);
                                    // ARREGLO GENERAL 99

                                    $arregloFreightPack = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreightPack);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerPallet)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $pallet_cantidad = $package_pallet['pallet']['cantidad'];
                            if ($chargesOrigin != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateMount;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigpallet = array_merge($arregloOrigpallet, $markupKG);
                                    $collectionOrig->push($arregloOrigpallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestPallet = array_merge($arregloDestPallet, $markupKG);

                                    $collectionDest->push($arregloDestPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightPallet = array_merge($arregloFreightPallet, $markupKG);

                                    $collectionFreight->push($arregloFreightPallet);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerM3)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($request->input('total_volume') != null) {
                                $totalVol = $request->input('total_volume');
                            } else {
                                $totalVol = $request->input('total_volume_pkg');
                            }

                            if ($chargesOrigin != null && $totalVol != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $totalVol * $local->ammount;
                                    $totalAmmount = ($totalVol * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMount;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigpallet = array_merge($arregloOrigpallet, $markupKG);
                                    $collectionOrig->push($arregloOrigpallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $totalVol != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalVol * $local->ammount;
                                    $totalAmmount = ($totalVol * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestPallet = array_merge($arregloDestPallet, $markupKG);

                                    $collectionDest->push($arregloDestPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $totalVol != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalVol * $local->ammount;
                                    $totalAmmount = ($totalVol * $local->ammount) / $rateC;
                                    $mont = $local->ammount / $rateC;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightVol = array('surcharge_terms' => $terminos, 'surcharge_name' => $local->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightVol = array_merge($arregloFreightVol, $markupKG);

                                    $collectionFreight->push($arregloFreightVol);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }
            } // Fin del calculo de los local charges

            //############ Global Charges   ####################

            $globalChar = GlobalChargeLcl::where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrierslcl', function ($q) use ($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                $query->whereHas('globalcharportlcl', function ($q) use ($orig_port, $dest_port) {
                    $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                })->orwhereHas('globalcharcountrylcl', function ($q) use ($origin_country, $destiny_country) {
                    $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                });
            })->where('company_user_id', '=', $company_user_id)->with('globalcharportlcl.portOrig', 'globalcharportlcl.portDest', 'globalcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm')->get();

            foreach ($globalChar as $global) {
                $rateMountG = $this->ratesCurrency($global->currency->id, $typeCurrency);
                $rateC = $this->ratesCurrency($global->currency->id, $data->currency->alphacode);

                if ($request->input('total_weight') != null) {
                    $totalW = $request->input('total_weight') / 1000;
                    $totalV = $request->input('total_volume');
                    $totalWeight = $request->input('total_weight');
                } else {
                    $totalW = $request->input('total_weight_pkg') / 1000;
                    $totalV = $request->input('total_volume_pkg');
                    $totalWeight = $request->input('total_weight');
                }

                // Condicion para enviar los terminos de venta o compra
                if (isset($global->surcharge->saleterm->name)) {
                    $terminos = $global->surcharge->saleterm->name;
                } else {
                    $terminos = $global->surcharge->name;
                }

                if (in_array($global->calculationtypelcl_id, $arrayBlHblShip)) {
                    $cantidadT = 1;
                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {
                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateMountG;

                                    // MARKUP

                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => '-', 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig, $markupBL);
                                    //$origGlo["origin"] = $arregloOrig;
                                    $collectionOrig->push($arregloOrig);
                                    // $collectionGloOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => '1');

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateMountG;
                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => '1', 'monto' => $global->ammount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupBL);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => '1');

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {
                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateC;

                                    // MARKUP
                                    $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => '-', 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight, $markupBL);

                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => '1');

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arraytonM3)) {
                    //ROUNDED
                    if ($global->calculationtypelcl_id == '11') {
                        $ton_weight = ceil($weight);
                    } else {
                        $ton_weight = $weight;
                    }
                    $cantidadT = $ton_weight;

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {
                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig, $markupTonM3);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $cantidadT);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupTonM3);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $cantidadT);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {
                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateC;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateC;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }
                                    // MARKUP
                                    $markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight, $markupTonM3);

                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerTon)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            //ROUNDED
                            if ($global->calculationtypelcl_id == '10') {
                                $totalW = ceil($totalW);
                            }
                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig, $markupTON);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }
                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupTON);
                                    $collectionDest->push($arregloDest);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateC;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateC;
                                        $mont = $global->minimum / $totalW;

                                        $mont = number_format($mont, 2, '.', '');
                                    }
                                    // MARKUP
                                    $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight, $markupTON);
                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arraytonCompli)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }

                                    // MARKUP
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrig = array_merge($arregloOrig, $markupTONM3);
                                    $dataGOrig[] = $arregloOrig;

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $dataGOrig[] = $arregloOrigin;
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {
                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalV; // monto por unidad
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP
                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDest = array_merge($arregloDest, $markupTONM3);
                                    $dataGDest[] = $arregloDest;

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $dataGDest[] = $arregloDest;
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateC;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateC;
                                            $mont = $global->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateC;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateC;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP

                                    $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreight = array_merge($arregloFreight, $markupTONM3);
                                    $dataGFreight[] = $arregloFreight;

                                    // ARREGLO GENERAL 99
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                    $dataGFreight[] = $arregloFreight;
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerKG)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigKg = array_merge($arregloOrigKg, $markupKG);

                                    $collectionOrig->push($arregloOrigKg);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestKg = array_merge($arregloDestKg, $markupKG);
                                    $collectionDest->push($arregloDestKg);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateC;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateC;
                                        $unidades = $subtotal_global / $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightKg = array_merge($arregloFreightKg, $markupKG);
                                    $collectionFreight->push($arregloFreightKg);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerPack)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $package_cantidad = $package_pallet['package']['cantidad'];
                            if ($chargesOrigin != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigPack = array_merge($arregloOrigPack, $markupKG);

                                    $collectionOrig->push($arregloOrigPack);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestKg = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestPack = array_merge($arregloDestPack, $markupKG);
                                    $collectionDest->push($arregloDestPack);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateC;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateC;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPack = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightPack = array_merge($arregloFreightPack, $markupKG);
                                    $collectionFreight->push($arregloFreightPack);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerPallet)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $pallet_cantidad = $package_pallet['pallet']['cantidad'];

                            if ($chargesOrigin != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigPallet = array_merge($arregloOrigPallet, $markupKG);

                                    $collectionOrig->push($arregloOrigPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestPallet = array_merge($arregloDestPallet, $markupKG);
                                    $collectionDest->push($arregloDestPallet);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateC;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightPallet = array_merge($arregloFreightPallet, $markupKG);
                                    $collectionFreight->push($arregloFreightPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerM3)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($request->input('total_volume') != null) {
                                $totalVol = $request->input('total_volume');
                            } else {
                                $totalVol = $request->input('total_volume_pkg');
                            }

                            if ($chargesOrigin != null && $totalVol != 0) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_local = $totalVol * $global->ammount;
                                    $totalAmmount = ($totalVol * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $global->minimum) {
                                        $subtotal_local = $global->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMountG;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloOrigpallet = array_merge($arregloOrigpallet, $markupKG);
                                    $collectionOrig->push($arregloOrigpallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $totalVol != 0) {
                                if ($global->typedestiny_id == '2') {
                                    $subtotal_local = $totalVol * $global->ammount;
                                    $totalAmmount = ($totalVol * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $global->minimum) {
                                        $subtotal_local = $global->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrency, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'montoOrig' => $totalAmmount);
                                    $arregloDestPallet = array_merge($arregloDestPallet, $markupKG);

                                    $collectionDest->push($arregloDestPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrency, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $totalVol != 0) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_local = $totalVol * $global->ammount;
                                    $totalAmmount = ($totalVol * $global->ammount) / $rateC;
                                    $mont = $global->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $global->minimum) {
                                        $subtotal_local = $global->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $global->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightVol = array('surcharge_terms' => $terminos, 'surcharge_name' => $global->surcharge->name, 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidadT' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'idCurrency' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    $arregloFreightVol = array_merge($arregloFreightVol, $markupKG);

                                    $collectionFreight->push($arregloFreightVol);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                }
                            }
                        }
                    }
                }
            }

            //############ Fin Global Charges ##################

            // Locales

            if (!empty($dataOrig)) {
                $collectOrig = Collection::make($dataOrig);

                $m3tonOrig = $collectOrig->groupBy('surcharge_name')->map(function ($item) use ($collectionOrig, &$totalOrigin, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();

                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalOrigin += $totalA[0];
                        $collectionOrig->push($test);

                        return $test;
                    }
                });
            }

            if (!empty($dataDest)) {
                $collectDest = Collection::make($dataDest);
                $m3tonDest = $collectDest->groupBy('surcharge_name')->map(function ($item) use ($collectionDest, &$totalDestiny, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalDestiny += $totalA[0];
                        //            $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataFreight)) {

                $collectFreight = Collection::make($dataFreight);
                $m3tonFreight = $collectFreight->groupBy('surcharge_name')->map(function ($item) use ($collectionFreight, &$totalFreight, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalFreight += $totalA[0];
                        //$arre['freight'] = $test;
                        $collectionFreight->push($test);
                        return $test;
                    }
                });
            }

            // Globales
            if (!empty($dataGOrig)) {
                $collectGOrig = Collection::make($dataGOrig);

                $m3tonGOrig = $collectGOrig->groupBy('surcharge_name')->map(function ($item) use ($collectionOrig, &$totalOrigin, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalOrigin += $totalA[0];

                        //$arre['origin'] = $test;
                        $collectionOrig->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataGDest)) {
                $collectGDest = Collection::make($dataGDest);
                $m3tonDestG = $collectGDest->groupBy('surcharge_name')->map(function ($item) use ($collectionDest, &$totalDestiny, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalDestiny += $totalA[0];
                        // $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataGFreight)) {

                $collectGFreight = Collection::make($dataGFreight);
                $m3tonFreightG = $collectGFreight->groupBy('surcharge_name')->map(function ($item) use ($collectionFreight, &$totalFreight, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
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
            $totalFreight = number_format($totalFreight, 2, '.', '');
            $FreightCharges = number_format($FreightCharges, 2, '.', '');
            $totalOrigin = number_format($totalOrigin, 2, '.', '');
            $totalDestiny = number_format($totalDestiny, 2, '.', '');

            $totalFreightOrig = $totalFreight;

            $rateTotal = $this->ratesCurrency($data->currency->id, $typeCurrency);
            $totalFreight = $totalFreight / $rateTotal;
            $totalFreight = number_format($totalFreight, 2, '.', '');

            $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
            $totalQuoteSin = number_format($totalQuote, 2, ',', '');

            if ($chargesDestination == null && $chargesOrigin == null) {

                $totalQuote = $totalFreightOrig;
                $data->setAttribute('quoteCurrency', $data->currency->alphacode);
            } else {
                $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
                $data->setAttribute('quoteCurrency', $typeCurrency);
            }

            if (!empty($collectionOrig)) {
                $collectionOrig = $this->OrdenarCollectionLCL($collectionOrig);
            }

            if (!empty($collectionDest)) {
                $collectionDest = $this->OrdenarCollectionLCL($collectionDest);
            }

            if (!empty($collectionFreight)) {
                $collectionFreight = $this->OrdenarCollectionLCL($collectionFreight);
            }

            // SCHEDULE TYPE

            $transit_time = $this->transitTime($data->port_origin->id, $data->port_destiny->id, $data->carrier->id, $data->contract->status);

            /*$data->setAttribute('via', $transit_time['via']);
            $data->setAttribute('transit_time', $transit_time['transit_time']);
            $data->setAttribute('service', $transit_time['service']);
            $data->setAttribute('sheduleType', null);*/

            $data->setAttribute('sheduleType', null);
            $data->setAttribute('via', $va);
            $data->setAttribute('transit_time', $tt);
            if ($tt != '' && $tt != null) {
                $data->setAttribute('service', 'Transfer');
            } else {
                $data->setAttribute('service', 'Direct');
            }

            /*    if ($data->schedule_type_id != null) {
            $sheduleType = ScheduleType::find($data->schedule_type_id);
            $data->setAttribute('sheduleType', $sheduleType->name);
            } else {
            $data->setAttribute('sheduleType', null);
            }*/
            //remarks
            $mode = "";
            $remarks = "";
            if ($data->contract->comments != "") {
                $remarks = $data->contract->comments . "<br>";
            }

            $typeMode = $request->input('mode');
            $remarks .= $this->remarksCondition($data->port_origin, $data->port_destiny, $data->carrier, $typeMode);
            $remarks = trim($remarks);

            // EXCEL REQUEST

            $excelRequestLCL = ContractLclFile::where('contractlcl_id', $data->contract->id)->first();
            if (!empty($excelRequestLCL)) {
                $excelRequestIdLCL = $excelRequestLCL->id;
            } else {
                $excelRequestIdLCL = '0';
            }

            $excelRequest = NewContractRequestLcl::where('contract_id', $data->contract->id)->first();
            if (!empty($excelRequest)) {
                $excelRequestId = $excelRequest->id;
            } else {
                $excelRequestId = "";
            }
            $colores = '';
            if ($data->contract->is_manual == 1) {
                $colores = 'bg-manual';
            } else {
                $colores = 'bg-api';
            }
            //Contrato Futuro

            $contratoFuturo = $this->contratoFuturo($data->contract->validity, $dateSince, $data->contract->expire, $dateUntil);

            $data->setAttribute('contratoFuturo', $contratoFuturo);

            //COlor
            $data->setAttribute('contract_color', $colores);
            $data->setAttribute('remarks', $remarks);
            $data->setAttribute('excelRequest', $excelRequestId);
            $data->setAttribute('excelRequestLCL', $excelRequestIdLCL);
            $data->setAttribute('localOrig', $collectionOrig);
            $data->setAttribute('localDest', $collectionDest);
            $data->setAttribute('localFreight', $collectionFreight);

            $data->setAttribute('freightCharges', $FreightCharges);
            $data->setAttribute('totalFreight', $totalFreight);
            $data->setAttribute('totalFreightOrig', $totalFreightOrig);

            $data->setAttribute('totalrates', $totalRates);
            $data->setAttribute('totalOrigin', $totalOrigin);
            $data->setAttribute('totalDestiny', $totalDestiny);

            $data->setAttribute('totalQuote', $totalQuote);
            // INLANDS
            $data->setAttribute('inlandDestiny', $inlandDestiny);
            $data->setAttribute('inlandOrigin', $inlandOrigin);
            $data->setAttribute('totalChargeOrig', $totalChargeOrig);
            $data->setAttribute('totalChargeDest', $totalChargeDest);
            $data->setAttribute('totalInland', $totalInland);
            //Total quote atributes

            $data->setAttribute('rateCurrency', $data->currency->alphacode);
            $data->setAttribute('totalQuoteSin', $totalQuoteSin);
            $data->setAttribute('idCurrency', $idCurrency);
            // SCHEDULES
            $data->setAttribute('schedulesFin', "");

            // Ordenar las colecciones

        }

        $arreglo = $arreglo->sortBy('totalQuote');

        $chargeOrigin = ($chargesOrigin != null) ? true : false;
        $chargeDestination = ($chargesDestination != null) ? true : false;
        $chargeFreight = ($chargesFreight != null) ? true : false;
        $chargeAPI = ($chargesAPI != null) ? true : false;
        $chargeAPI_M = ($chargesAPI_M != null) ? true : false;
        $chargeAPI_SF = ($chargesAPI_M != null) ? true : false;

        $hideO = 'hide';
        $hideD = 'hide';
        $form = $request->all();

        $group_contain = GroupContainer::pluck('name', 'id');
        $carrierMan = Carrier::pluck('name', 'id');
        $carriersSelected = $request->input('carriers');
        $allCarrier = true;
        $form['equipment'] = array('1', '2', '3');
        $containers = Container::get();
        $validateEquipment = $this->validateEquipment($form['equipment'], $containers);
        $containerType = $validateEquipment['gpId'];
        $quoteType = $request->input('type');

        $objharbor = new Harbor();
        $harbor = $objharbor->all()->pluck('name', 'id');

        return view('quotesv2/searchLCL', compact('harbor', 'formulario', 'arreglo', 'form', 'companies', 'harbors', 'hideO', 'hideD', 'incoterm', 'simple', 'paquete', 'chargeOrigin', 'chargeDestination', 'chargeFreight', 'chargeAPI', 'chargeAPI_M', 'chargeAPI_SF', 'contain', 'group_contain', 'carrierMan', 'carriersSelected', 'allCarrier', 'containerType', 'quoteType'));
    }

    /**
     * Ordena las colleciones LCL
     * @method function
     * @param {Object} recibe el objeto coleccion destino , origen o freight
     * @return {Object} coleccion ordenada segun surcharge y calculation name
     */
    public function OrdenarCollectionLCL($collection)
    {

        $collection = $collection->groupBy([
            'surcharge_name', 'calculation_name',
            function ($item) {
                return $item['type'];
            },
        ], $preserveKeys = true);

        // Se Ordena y unen la collection
        $collect = new collection();
        $monto = 0;
        $montoMarkup = 0;
        $totalMarkup = 0;

        foreach ($collection as $item) {
            foreach ($item as $items) {
                $total = count($items);

                if ($total > 1) {
                    foreach ($items as $itemsT) {
                        foreach ($itemsT as $itemsDetail) {
                            $monto += $itemsDetail['monto'];
                            $montoMarkup += $itemsDetail['montoMarkup'];
                            $totalMarkup += $itemsDetail['markup'];
                        }
                    }
                    $itemsDetail['monto'] = number_format($monto, 2, '.', '');
                    $itemsDetail['montoMarkup'] = number_format($montoMarkup, 2, '.', '');
                    $itemsDetail['markup'] = number_format($totalMarkup, 2, '.', '');
                    $itemsDetail['currency'] = $itemsDetail['typecurrency'];
                    $itemsDetail['currency_id'] = $itemsDetail['currency_orig_id'];
                    $collect->push($itemsDetail);
                    $monto = 0;
                    $montoMarkup = 0;
                    $totalMarkup = 0;
                } else {
                    foreach ($items as $itemsT) {
                        foreach ($itemsT as $itemsDetail) {
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
            function ($item) use ($collect) {
                $collect->put('x', 'surcharge_name');
                return $item['type'];
            },
        ], $preserveKeys = true);

        return $collect;
    }

    // Store  LCL AUTOMATIC

    public function storeLCL(Request $request)
    {
        if (!empty($request->input('form'))) {
            $form = json_decode($request->input('form'));

            $info = $request->input('info');
            $dateQ = explode('/', $form->date);
            $since = $dateQ[0];
            $until = $dateQ[1];
            $priceId = null;
            $mode = $form->mode;
            if (isset($form->price_id)) {
                $priceId = $form->price_id;
                if ($priceId == "0") {
                    $priceId = null;
                }
            }

            $fcompany_id = null;
            $fcontact_id = null;
            $payments = null;
            if (isset($form->company_id_quote)) {
                if ($form->company_id_quote != "0" && $form->company_id_quote != null) {
                    $payments = $this->getCompanyPayments($form->company_id_quote);
                    $fcompany_id = $form->company_id_quote;
                    $fcontact_id = $form->contact_id;
                }
            }

            $typeText = "LCL";
            $arregloNull = array();
            $arregloNull = json_encode($arregloNull);
            $equipment = $arregloNull;
            $delivery_type = $request->input('delivery_type');

            $request->request->add(['company_user_id' => \Auth::user()->company_user_id, 'quote_id' => $this->idPersonalizado(), 'type' => 'LCL', 'delivery_type' => $form->delivery_type, 'company_id' => $fcompany_id, 'contact_id' => $fcontact_id, 'validity_start' => $since, 'validity_end' => $until, 'user_id' => \Auth::id(), 'equipment' => $equipment, 'status' => 'Draft', 'date_issued' => $since, 'price_id' => $priceId, 'payment_conditions' => $payments, 'total_quantity' => $form->total_quantity, 'total_weight' => $form->total_weight, 'total_volume' => $form->total_volume, 'chargeable_weight' => $form->chargeable_weight]);
            $quote = QuoteV2::create($request->all());

            $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
            $currency_id = $company->companyUser->currency_id;
            $currency = Currency::find($currency_id);
            $quantity = array_values(array_filter($form->quantity));

            $language = $company->companyUser->language()->first();
            if($language != null){
                $quote->language_id = $language->id;
            }
            $cargo_type_id = $form->cargo_type;
            $quote->cargo_type_id = $cargo_type_id;
            $quote->save();
            //dd($input);
            $type_cargo = array_values(array_filter($form->type_load_cargo));
            $height = array_values(array_filter($form->height));
            $width = array_values(array_filter($form->width));
            $large = array_values(array_filter($form->large));
            $weight = array_values(array_filter($form->weight));
            $volume = array_values(array_filter($form->volume));

            if (count($quantity) > 0) {
                foreach ($type_cargo as $key => $item) {

                    $package_load = new PackageLoadV2();
                    $package_load->quote_id = $quote->id;
                    $package_load->type_cargo = $type_cargo[$key];
                    $package_load->quantity = $quantity[$key];
                    $package_load->height = $height[$key];
                    $package_load->width = $width[$key];
                    $package_load->large = $large[$key];
                    $package_load->weight = $weight[$key];
                    $package_load->total_weight = $weight[$key] * $quantity[$key];
                    if (!empty($volume[$key]) && $volume[$key] != null) {
                        $package_load->volume = $volume[$key];
                    } else {
                        $package_load->volume = 0.01;
                    }

                    $package_load->save();
                }
            }

            $this->savePdfOption($quote, $currency);
        }

        //AUTOMATIC QUOTE
        if (!empty($info)) {
            $terms = '';
            foreach ($info as $infoA) {
                $info_D = json_decode($infoA);

                // Rates

                foreach ($info_D->rates as $rateO) {

                    $arregloNull = array();
                    $remarks = $info_D->remarks . "<br>";
                    $request->request->add(['contract' => $info_D->contract->name . " / " . $info_D->contract->number, 'origin_port_id' => $info_D->port_origin->id, 'destination_port_id' => $info_D->port_destiny->id, 'carrier_id' => $info_D->carrier->id, 'currency_id' => $info_D->currency->id, 'quote_id' => $quote->id, 'remarks' => $remarks, 'schedule_type' => $info_D->sheduleType, 'transit_time' => $info_D->transit_time, 'via' => $info_D->via]);

                    $rate = AutomaticRate::create($request->all());

                    $oceanFreight = new ChargeLclAir();
                    $oceanFreight->automatic_rate_id = $rate->id;
                    $oceanFreight->type_id = '3';
                    $oceanFreight->surcharge_id = null;
                    $oceanFreight->calculation_type_id = '5';
                    $oceanFreight->units = $rateO->cantidad;
                    $oceanFreight->price_per_unit = $rateO->price;
                    $oceanFreight->total = $rateO->subtotal;
                    $oceanFreight->markup = $rateO->markup;
                    $oceanFreight->currency_id = $rateO->idCurrency;
                    $oceanFreight->minimum = $info_D->minimum;
                    $oceanFreight->save();

                    $rateTotals = new AutomaticRateTotal();
                    $rateTotals->quote_id = $quote->id;
                    $rateTotals->automatic_rate_id = $rate->id;
                    $rateTotals->origin_port_id = $rate->origin_port_id;
                    $rateTotals->destination_port_id = $rate->destination_port_id;
                    $rateTotals->currency_id = $rateO->idCurrency;
                    $rateTotals->totals = null;
                    $rateTotals->markups = null;
                    $rateTotals->save();
                    $rateTotals->totalize($rateO->idCurrency);

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
                foreach ($info_D->localOrig as $localorigin) {

                    foreach ($localorigin as $localO) {
                        foreach ($localO as $local) {
                            $price_per_unit = $local->monto / $local->cantidad;
                            $chargeOrigin = new ChargeLclAir();
                            $chargeOrigin->automatic_rate_id = $rate->id;
                            $chargeOrigin->type_id = '1';
                            $chargeOrigin->surcharge_id = $local->surcharge_id;
                            $chargeOrigin->calculation_type_id = $local->calculation_id;
                            $chargeOrigin->units = $local->cantidad;
                            $chargeOrigin->price_per_unit = $price_per_unit;
                            $chargeOrigin->total = $local->montoMarkup;
                            $chargeOrigin->markup = $local->markup;
                            $chargeOrigin->currency_id = $local->currency_id;
                            $chargeOrigin->save();
                        }
                    }
                }

                // CHARGES DESTINY
                //dd($info_D->localDest);
                foreach ($info_D->localDest as $localdestiny) {

                    foreach ($localdestiny as $localD) {
                        foreach ($localD as $local) {
                            $price_per_unit = $local->monto / $local->cantidad;
                            $chargeDestiny = new ChargeLclAir();
                            $chargeDestiny->automatic_rate_id = $rate->id;
                            $chargeDestiny->type_id = '2';
                            $chargeDestiny->surcharge_id = $local->surcharge_id;
                            $chargeDestiny->calculation_type_id = $local->calculation_id;
                            $chargeDestiny->units = $local->cantidad;
                            $chargeDestiny->price_per_unit = $price_per_unit;
                            $chargeDestiny->total = $local->montoMarkup;
                            $chargeDestiny->markup = $local->markup;
                            $chargeDestiny->currency_id = $local->currency_id;
                            $chargeDestiny->save();
                        }
                    }
                }

                // CHARGES FREIGHT
                foreach ($info_D->localFreight as $localfreight) {
                    // --------------------
                    foreach ($localfreight as $localF) {
                        foreach ($localF as $local) {
                            $price_per_unit = $local->monto / $local->cantidad;
                            $chargeFreight = new ChargeLclAir();
                            $chargeFreight->automatic_rate_id = $rate->id;
                            $chargeFreight->type_id = '3';
                            $chargeFreight->surcharge_id = $local->surcharge_id;
                            $chargeFreight->calculation_type_id = $local->calculation_id;
                            $chargeFreight->units = $local->cantidad;
                            $chargeFreight->price_per_unit = $price_per_unit;
                            $chargeFreight->total = $local->montoMarkup;
                            $chargeFreight->markup = $local->markup;
                            $chargeFreight->currency_id = $local->currency_id;
                            $chargeFreight->save();
                        }
                    }
                }
            }

            // Terminos Automatica
            $modo = $request->input('mode');
            $companyUser = CompanyUser::All();
            $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
            $terms = TermAndConditionV2::where('company_user_id', Auth::user()->company_user_id)->where('type', 'LCL')->with('language')->get();

            $terminos_english = "";
            $terminos_spanish = "";
            $terminos_portuguese = "";
            //Export
            foreach ($terms as $term) {
                if ($modo == '1') {
                    if ($term->language_id == '1') {
                        $terminos_english .= $term->export . "<br>";
                    }

                    if ($term->language_id == '2') {
                        $terminos_spanish .= $term->export . "<br>";
                    }

                    if ($term->language_id == '3') {
                        $terminos_portuguese .= $term->export . "<br>";
                    }
                } else { // import

                    if ($term->language_id == '1') {
                        $terminos_english .= $term->import . "<br>";
                    }

                    if ($term->language_id == '2') {
                        $terminos_spanish .= $term->import . "<br>";
                    }

                    if ($term->language_id == '3') {
                        $terminos_portuguese .= $term->import . "<br>";
                    }
                }
            }

            $quoteEdit = QuoteV2::find($quote->id);
            $quoteEdit->terms_english = $terminos_english;
            $quoteEdit->terms_and_conditions = $terminos_spanish;
            $quoteEdit->terms_portuguese = $terminos_portuguese;
            $quoteEdit->update();
        }

        //$request->session()->flash('message.nivel', 'success');
        //$request->session()->flash('message.title', 'Well done!');
        //$request->session()->flash('message.content', 'Register completed successfully!');
        //return redirect()->route('quotes.index');

        //return redirect()->action('QuotationController@edit', $quote->id);
        return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote->id));
    }

    public function unidadesTON($unidades)
    {

        if ($unidades < 1) {
            return 1;
        } else {
            return $unidades;
        }
    }

    public function storeSearchV2($origPort, $destPort, $pickUpDate, $equipment, $delivery, $direction, $company, $type)
    {

        $searchRate = new SearchRate();
        $searchRate->pick_up_date = $pickUpDate;
        $searchRate->equipment = json_encode($equipment);
        $searchRate->delivery = $delivery;
        $searchRate->direction = $direction;
        $searchRate->company_user_id = $company;
        $searchRate->type = $type;

        $searchRate->user_id = \Auth::id();
        $searchRate->save();
        foreach ($origPort as $orig => $valueOrig) {
            foreach ($destPort as $dest => $valueDest) {
                $detailport = new SearchPort();
                $detailport->port_orig = $valueOrig; // $request->input('port_origlocal'.$contador.'.'.$orig);
                $detailport->port_dest = $valueDest; //$request->input('port_destlocal'.$contador.'.'.$dest);
                $detailport->search_rate()->associate($searchRate);
                $detailport->save();
            }
        }
    }

    /**
     * Descargar archivo .xlsx con listado de Cotizaciones
     */
    public function downloadQuotes()
    {
        //return Excel::download(new QuotesExport, 'quotes.xlsx');
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $quotes = QuoteV2::where('owner', \Auth::user()->id)->whereHas('user', function ($q) use ($company_user_id) {
                $q->where('company_user_id', '=', $company_user_id);
            })->orderBy('created_at', 'desc')->get();
        } else {
            $quotes = QuoteV2::whereHas('user', function ($q) use ($company_user_id) {
                $q->where('company_user_id', '=', $company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $nameFile = str_replace([' '], '_', $now . '_quotes');
        Excel::create($nameFile, function ($excel) use ($nameFile, $quotes) {
            $excel->sheet('Quotes', function ($sheet) use ($quotes) {
                //dd($contract);
                $sheet->cells('A1:AG1', function ($cells) {
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
                $i = 2;
                foreach ($quotes as $quote) {
                    $rates = AutomaticRate::where('quote_id', $quote->id)->get();
                    $origin = '';
                    $incoterm = '';
                    foreach ($rates as $rate) {
                        if ($rate->origin_port_id != '') {
                            $origin .= $rate->origin_port->name . '|';
                        } else if ($rate->destination_airport_id != '') {
                            $origin .= $rate->origin_airport->name . '|';
                        } else if ($rate->origin_address != '') {
                            $origin .= $rate->origin_address . '|';
                        }
                    }
                    $destination = '';
                    foreach ($rates as $rate) {
                        if ($rate->destination_port_id != '') {
                            $destination .= $rate->destination_port->name . '|';
                        } else if ($rate->destination_airport_id != '') {
                            $destination .= $rate->destination_airport->name . '|';
                        } else if ($rate->destination_address != '') {
                            $destination .= $rate->destination_address . '|';
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

                    if ($quote->gdp == 1) {
                        $gdp = 'Yes';
                    } else {
                        $gdp = 'No';
                    }

                    if ($quote->cargo_type == 1) {
                        $cargo_type = 'Pallets';
                    } else {
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
    public function updateChargeable(Request $request, $id)
    {

        $quote = QuoteV2::find($id);
        $quote->chargeable_weight = $request->chargeable_weight;
        $quote->update();

        return response()->json(['message' => 'Ok']);
    }

    public function getGroupContainer($id)
    {
        $group_container = Container::where('gp_container_id', $id)->get()->toJson();
        return $group_container;
    }
}
