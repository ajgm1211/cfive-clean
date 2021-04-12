<?php

namespace App\Http\Controllers;

use App\Http\Traits\SearchTrait;
use App\Http\Traits\QuoteV2Trait;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SearchApiResource;
use App\Http\Resources\RateResource;
use App\Http\Requests\StoreContractSearch;
use App\InlandDistance;
use App\Harbor;
use App\Direction;
use App\SearchRate;
use App\SearchPort;
use App\SearchCarrier;
use App\Carrier;
use App\Company;
use App\TermAndConditionV2;
use App\DeliveryType;
use App\Currency;
use App\TypeDestiny;
use App\Container;
use App\GroupContainer;
use App\ScheduleType;
use App\Country;
use App\Rate;
use App\Contract;
use App\Price;
use App\Contact;
use App\CompanyUser;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\GlobalCharge;
use App\TransitTime;
use App\RemarkCondition;
use App\Surcharge;
use App\CalculationType;
use App\QuoteV2;
use App\CompanyPrice;
use Illuminate\Http\Request;
use GeneaLabs\LaravelMixpanel\LaravelMixpanel;
use App\Http\Traits\MixPanelTrait;

class SearchApiController extends Controller
{
    use SearchTrait, QuoteV2Trait, MixPanelTrait;

    protected $mixPanel;

    public function __construct(LaravelMixPanel $mixPanel)
    {
        $this->mixPanel = $mixPanel;
    }

    //Shows the Search main view
    public function index(Request $request)
    {
        return view('searchV2.index');
    }

    //Retrieves last 4 searches made
    public function list(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;
        //Filtering and pagination
        $results = SearchRate::where('company_user_id', $company_user_id)->orderBy('id', 'desc')->take(4)->get();

        //Grouping as collection to be managed by Vue
        return SearchApiResource::collection($results);
    }

    //Retrieves all data needed for search processing and displaying
    public function data(Request $request)
    {
        //Querying each model used and mapping only necessary data
        $company_user_id = \Auth::user()->company_user_id;

        $company_user = CompanyUser::where('id', $company_user_id)->first();

        $carriers = Carrier::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name', 'image']);
        });

        $companies = Company::where('company_user_id', '=', $company_user_id)->get();

        $contacts = [];

        foreach ($companies as $comp) {
            $newContacts = $comp->contact()->get();

            foreach ($newContacts as $cont) {
                if (!in_array($cont, $contacts)) {
                    $cont->setAttribute('name', $cont->getFullName());
                    array_push($contacts, $cont);
                }
            }
        }

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name', 'code', 'harbor_parent']);
        });

        $delivery_types = DeliveryType::get()->map(function ($delivery_type) {
            return $delivery_type->only(['id', 'name']);
        });

        $currency = Currency::get()->map(function ($curr) {
            return $curr->only(['id', 'alphacode', 'rates', 'rates_eur']);
        });

        $common_currencies = Currency::whereIn('id', ['46', '149'])->get()->map(function ($curr) {
            return $curr->only(['id', 'alphacode', 'rates', 'rates_eur']);
        });

        $containers = Container::all();

        $container_groups = GroupContainer::all();

        $directions = Direction::all();

        $schedule_types = ScheduleType::get()->map(function ($schedule_type) {
            return $schedule_type->only(['id', 'name']);
        });

        /**$countries = Country::get()->map(function ($country){
            return $country->only(['id','code','name']);
        });**/

        $price_levels = Price::where('company_user_id', $company_user_id)->get()->map(function ($price) {
            return $price->only(['id', 'name']);
        });

        $surcharges = Surcharge::where('company_user_id', '=', $company_user_id)->get()->map(function ($surcharge) {
            return $surcharge->only(['id', 'name',]);
        });

        $calculation_type = CalculationType::get()->map(function ($calculationt) {
            return $calculationt->only(['id', 'name']);
        });

        $type_destiny = TypeDestiny::get()->map(function ($type) {
            return $type->only(['id', 'description']);
        });

        $company_prices = CompanyPrice::get()->map(function ($comprice){
            return $comprice->only(['id','company_id','price_id']);
        });

        /**$inland_distances = InlandDistance::get()->map(function ($distance){
            return $distance->only(['id','display_name','harbor_id']);
        });**/

        //Collecting all data retrieved
        $data = compact(
            'company_user_id',
            'company_user',
            'carriers',
            'companies',
            'contacts',
            'currency',
            'common_currencies',
            'containers',
            'container_groups',
            //'countries',
            'delivery_types',
            'directions',
            'harbors',
            'price_levels',
            'schedule_types',
            'type_destiny',
            'surcharges',
            //'inland_distances',
            'calculation_type',
            'company_prices'
        );

        return response()->json(['data' => $data]);
    }

    //Validates search request data
    public function processSearch(Request $request)
    {
        //Setting current company and user
        $user = \Auth::user();
        $user_id = $user->id;
        $company_user = $user->companyUser()->first();
        $company_user_id = $company_user->id;

        $search_array = $request->input();

        $search_array['dateRange']['startDate'] = substr($search_array['dateRange']['startDate'], 0, 10);
        $search_array['dateRange']['endDate'] = substr($search_array['dateRange']['endDate'], 0, 10);

        $search_ids = $this->getIdsFromArray($search_array);
        $search_ids['company_user'] = $company_user_id;
        $search_ids['user'] = $user_id;
        $search_ids['client_currency'] = $company_user->currency;

        //Retrieving rates with search data
        $rates = $this->searchRates($search_ids);

        //$rateNo = 0;
        foreach ($rates as $rate) {
            //$rateNo += 1;
            //dump($rate->contract);
            //dump('for rate '. strval($rateNo));
            //Retrieving local charges with search data
            $local_charges = $this->searchLocalCharges($search_ids, $search_array, $rate);

            //Retrieving global charges with search data
            $global_charges = $this->searchGlobalCharges($search_ids, $search_array, $rate);

            //SEARCH TRAIT - Grouping charges by type (Origin, Destination, Freight)
            $charges = $this->groupChargesByType($local_charges, $global_charges, $search_ids);

            //SEARCH TRAIT - Calculates charges by container and appends the cost array to each charge instance
            $this->setChargesPerContainer($charges, $search_array['containers'], $search_array['selectedContainerGroup']['id'], $search_ids['client_currency']);

            //Getting price levels if requested
            if (array_key_exists('pricelevel', $search_array) && $search_array['pricelevel'] != null) {
                $price_level_markups = $this->searchPriceLevels($search_ids);
            } else {
                $price_level_markups = [];
            }

            //SEARCH TRAIT - Join charges (within group) if Surcharge, Carrier, Port and Typedestiny match
            $charges = $this->joinCharges($charges, $search_ids['client_currency']);
            
            //Adding price levels
            if ($price_level_markups != null && count($price_level_markups) != 0) {
                $this->addMarkups($price_level_markups, $rate, $search_ids['client_currency']);
                foreach ($charges as $charge_direction) {
                    foreach ($charge_direction as $charge) {
                        $this->addMarkups($price_level_markups, $charge, $search_ids['client_currency']);
                    }
                }
            }

            $remarks = $this->searchRemarks($rate, $search_ids);

            //Appending Rate Id to Charges
            $this->addToRate($rate, $charges, 'charges', $search_ids['client_currency']);

            $terms = $this->searchTerms($search_ids);

            $search_array['terms'] = $terms;

            $transit_time = $this->searchTransitTime($rate);

            $rate->setAttribute('transit_time', $transit_time);

            $rate->setAttribute('remarks', $remarks);

            $rate->setAttribute('request_type', $request->input('requested'));

            $this->stringifyRateAmounts($rate);
        }

        if ($rates != null && count($rates) != 0) {
            //Ordering rates by totals (cheaper to most expensive)
            $rates = $this->sortRates($rates, $search_ids);

            $rates[0]->SetAttribute('search', $search_array);
        }

        $track_array = [];
        $track_array['company_user'] = $company_user;
        $track_array['data'] = $search_array;

        /** Tracking search event with Mix Panel*/
        $this->trackEvents("search_fcl", $track_array);

        return RateResource::collection($rates);
    }

    //Stores current search
    public function store(Request $request)
    {
        //Validating request data from form
        $new_search_data = $request->validate([
            'originPorts' => 'required|array|min:1',
            'destinationPorts' => 'required|array|min:1',
            'dateRange.startDate' => 'required',
            'dateRange.endDate' => 'required',
            'containers' => 'required|array|min:1',
            'selectedContainerGroup' => 'required',
            'deliveryType.id' => 'required',
            'direction' => 'required',
            'carriers' => 'required|array|min:1',
            'type' => 'required',
            'company' => 'sometimes',
            'contact' => 'sometimes',
            'pricelevel' => 'sometimes',
            'originCharges' => 'sometimes',
            'destinationCharges' => 'sometimes',
            'originAddress' => 'sometimes',
            'destinationAddress' => 'sometimes'
        ]);

        //Stripping time stamp from date
        $new_search_data['dateRange']['startDate'] = substr($new_search_data['dateRange']['startDate'], 0, 10);
        $new_search_data['dateRange']['endDate'] = substr($new_search_data['dateRange']['endDate'], 0, 10);

        //Getting address text if in array form
        if (is_array($new_search_data['originAddress'])) {
            $new_search_data['originAddress'] = $new_search_data['originAddress']['display_name'];
        } else if (is_array($new_search_data['destinationAddress'])) {
            $new_search_data['destinationAddress'] = $new_search_data['destinationAddress']['display_name'];
        }

        //Setting current company and user
        $user = \Auth::user();
        $user_id = $user->id;
        $company_user_id = $user->company_user_id;

        //Including company and user in search data array
        $new_search_data['user'] = $user_id;
        $new_search_data['company_user'] = $company_user_id;

        //SEARCH TRAIT - Getting new array that contains only ids, for queries
        $new_search_data_ids = $this->getIdsFromArray($new_search_data);

        //Formatting date
        $pick_up_date = $new_search_data_ids['dateRange']['startDate'] . ' / ' . $new_search_data_ids['dateRange']['endDate'];

        //formatting containers
        $container_array = [];

        //FORMATTING FOR OLD SEARCH, MUST BE REMOVED
        foreach ($new_search_data_ids['containers'] as $container_id) {
            $container = Container::where('id', $container_id)->first();

            array_push($container_array, $container->code);
        }

        $new_search = SearchRate::create([
            'company_user_id' => $new_search_data_ids['company_user'],
            'pick_up_date' => $pick_up_date,
            'equipment' => $container_array,
            'delivery' => $new_search_data_ids['deliveryType'],
            'direction' => $new_search_data_ids['direction'],
            'type' => $new_search_data_ids['type'],
            'user_id' => $new_search_data_ids['user'],
            'contact_id' => $new_search_data_ids['contact'],
            'company_id' => $new_search_data_ids['company'],
            'price_level_id' => $new_search_data_ids['pricelevel'],
            'origin_charges' => $new_search_data_ids['originCharges'],
            'destination_charges' => $new_search_data_ids['destinationCharges'],
            //'origin_address' => $new_search_data_ids['originAddress'],
            //'destination_address' => $new_search_data_ids['destinationAddress']
        ]);

        foreach ($new_search_data_ids['originPorts'] as $origPort) {
            foreach ($new_search_data_ids['destinationPorts'] as $destPort) {
                $searchPort = new SearchPort();
                $searchPort->port_orig = $origPort;
                $searchPort->port_dest = $destPort;
                $searchPort->search_rate()->associate($new_search);
                $searchPort->save();
            }
        }

        foreach ($new_search_data_ids['carriers'] as $carrier_id) {
            $searchCarrier = new SearchCarrier();
            $searchCarrier->carrier_id = $carrier_id;
            $searchCarrier->search_rate()->associate($new_search);
            $searchCarrier->save();
        }

        return new SearchApiResource($new_search);
    }

    public function retrieve(SearchRate $search)
    {
        return new SearchApiResource($search);
    }

    //Finds any Rates associated to a contract valid in search dates, matching search ports
    public function searchRates($search_data)
    {
        //setting variables for query
        $company_user_id = $search_data['company_user'];
        $company_user = CompanyUser::where('id', $search_data['company_user'])->first();

        $user_id = $search_data['user'];
        $container_group = $search_data['selectedContainerGroup'];
        $origin_ports = $search_data['originPorts'];
        $destiny_ports = $search_data['destinationPorts'];
        $arregloCarrier = $search_data['carriers'];
        $dateSince = $search_data['dateRange']['startDate'];
        $dateUntil = $search_data['dateRange']['endDate'];

        //Querying rates database
        if ($company_user_id != null || $company_user_id != 0) {
            $rates_query = Rate::whereIn('origin_port', $origin_ports)->whereIn('destiny_port', $destiny_ports)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract', 'carrier', 'currency')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id) {
                $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                    $a->where('user_id', '=', $user_id);
                })->orDoesntHave('contract_user_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $container_group) {
                $q->whereHas('contract_company_restriction', function ($b) use ($company_user_id) {
                    $b->where('company_id', '=', $company_user_id);
                })->orDoesntHave('contract_company_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $container_group, $company_user) {
                if ($company_user->future_dates == 1) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('status_erased','!=',1)->where('gp_container_id', $container_group);
                } else {
                    $q->where(function ($query) use ($dateSince, $dateUntil) {
                        $query->where('validity', '>=', $dateSince)->where('expire', '>=', $dateUntil);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('status_erased','!=',1)->where('gp_container_id', $container_group);
                }
            });
        } else {
            $rates_query = Rate::whereIn('origin_port', $origin_ports)->whereIn('destiny_port', $destiny_ports)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract', 'carrier', 'currency')->whereHas('contract', function ($q) {
                $q->doesnthave('contract_user_restriction');
            })->whereHas('contract', function ($q) {
                $q->doesnthave('contract_company_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $container_group, $company_user) {
                if ($company_user->future_dates == 1) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('status_erased','!=',1)->where('gp_container_id', $container_group);
                } else {
                    $q->where(function ($query) use ($dateSince, $dateUntil) {
                        $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('status_erased','!=',1)->where('gp_container_id', $container_group);
                }
            });
        }

        //Retrieving all containers for filtering
        $all_containers = Container::get();

        //Filtering only containers that have an amount associated
        $rates_query = $this->filtrarRate($rates_query, $search_data['containers'], null, $all_containers);

        //Applying quey and returning models
        $rates_array = $rates_query->get();

        //If the container rates come separate (twuenty,forty,etc) joins them under the "containers" field for iteration purposes
        $this->joinRateContainers($rates_array, $search_data['containers']);

        //Setting attribute to totalize adding charges, inlands, markups, etc. Totals are shown in the client default currency
        foreach ($rates_array as $rate) {
            //Converting rates to client currency
            $client_currency = $search_data['client_currency'];
            $containers_client_currency = $this->convertToCurrency($rate->currency, $client_currency, json_decode($rate->containers, true));
            $rate->setAttribute('totals', $containers_client_currency);
            $rate->setAttribute('client_currency', $client_currency);
        }

        return $rates_array;
    }

    //Finds any Inlands matching search search ports, and calculates costs based on Inland data
    public function searchInlands($data)
    {
        dd($data);
        /**Tables 
         - inland additional_kms (extra KM cost)
         - inland_distances (distancieros)
         - inland_kms (costs from kms tabs)
         - inland_ranges (costs from ranges tab)
         - inland_types (KM or RANGE)
         - inlands (names and such)
         - inlandsports (inland+port association)
         **/
    }

    //Finds local charges matching contracts
    public function searchLocalCharges($search_ids, $search_data, $rate)
    {
        //Creating empty collection for storing charges
        $local_charges = collect([]);
        //Pulling necessary data from the search IDs array
        $origin_ports = $search_ids['originPorts'];
        $destination_ports = $search_ids['destinationPorts'];
        $origin_countries = [];
        $destination_countries = [];
        //SEARCH API - Getting countries from port arrays and building countries array
        foreach ($search_data['originPorts'] as $origin_port) {
            array_push($origin_countries, $origin_port['country_id']);
        }
        foreach ($search_data['destinationPorts'] as $destination_port) {
            array_push($destination_countries, $destination_port['country_id']);
        }

        //Including "ALL" columns for querying LocalCharges with such option marked
        //IDS: On Harbors: 1485; On Countries: 250
        array_push($origin_ports, 1485);
        array_push($destination_ports, 1485);
        array_push($origin_countries, 250);
        array_push($destination_countries, 250);

        //creating carriers array with only rates carrier
        $carriers = array($rate->carrier->id);
        //Including "ALL" column of carriers table
        array_push($carriers, 26);
        //Checking if contract comes from API
        if ($rate->contract->status != 'api') {
            //Querying NON API contract local charges
            $local_charge = LocalCharge::where('contract_id', '=', $rate->contract_id)->whereHas('localcharcarriers', function ($q) use ($carriers) {
                $q->whereIn('carrier_id', $carriers);
            })->where(function ($query) use ($origin_ports, $destination_ports, $origin_countries, $destination_countries) {
                $query->whereHas('localcharports', function ($q) use ($origin_ports, $destination_ports) {
                    $q->whereIn('port_orig', $origin_ports)->whereIn('port_dest', $destination_ports);
                })->orwhereHas('localcharcountries', function ($q) use ($origin_countries, $destination_countries) {
                    $q->whereIn('country_orig', $origin_countries)->whereIn('country_dest', $destination_countries);
                });
            })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm', 'calculationtype')->orderBy('typedestiny_id', 'calculationtype_id', 'surchage_id')->get();
        } else {
            //Querying API contract local charges
            $local_charge = LocalChargeApi::where('contract_id', '=', $rate->contract_id)->whereHas('localcharcarriers', function ($q) use ($carriers) {
                $q->whereIn('carrier_id', $carriers);
            })->where(function ($query) use ($origin_ports, $destination_ports, $origin_countries, $destination_countries) {
                $query->whereHas('localcharports', function ($q) use ($origin_ports, $destination_ports) {
                    $q->whereIn('port_orig', $origin_ports)->whereIn('port_dest', $destination_ports);
                })->orwhereHas('localcharcountries', function ($q) use ($origin_countries, $destination_countries) {
                    $q->whereIn('country_orig', $origin_countries)->whereIn('country_dest', $destination_countries);
                });
            })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm', 'calculationtype')->orderBy('typedestiny_id', 'calculationtype_id', 'surchage_id')->get();
        }

        //Looping through Local Charges found, including in final collection if not there
        foreach ($local_charge as $charge) {
            $local_charges->push($charge);
        }

        return $local_charges;
    }

    //Finds global charges matching search data
    public function searchGlobalCharges($search_ids, $search_data, $rate)
    {
        //building Carriers array from rates
        $carriers = [];
        array_push($carriers, $rate->carrier->id);
        array_push($carriers, 26);
        //Creating empty collection for storing charges
        $global_charges = collect([]);
        //Pulling necessary data from the search IDs array
        $validity_start = $search_ids['dateRange']['startDate'];
        $validity_end = $search_ids['dateRange']['endDate'];
        $origin_ports = $search_ids['originPorts'];
        $destination_ports = $search_ids['destinationPorts'];
        $company_user_id = $search_ids['company_user'];
        $origin_countries = [];
        $destination_countries = [];
        //SEARCH API - Getting countries from port arrays and building countries array
        foreach ($search_data['originPorts'] as $origin_port) {
            array_push($origin_countries, $origin_port['country_id']);
        }
        foreach ($search_data['destinationPorts'] as $destination_port) {
            array_push($destination_countries, $destination_port['country_id']);
        }

        //Including "ALL" columns for querying GlobalCharges with such option marked
        //IDS: On Harbors: 1485; On Countries: 250
        array_push($origin_ports, 1485);
        array_push($destination_ports, 1485);
        array_push($origin_countries, 250);
        array_push($destination_countries, 250);

        $contractStatus = 'NOT API'; //*************** CHANGE THIS LATER ************

        if ($contractStatus != 'api') {

            $global_charges_found = GlobalCharge::where([['validity', '<=', $validity_start], ['expire', '>=', $validity_end]])->whereHas('globalcharcarrier', function ($q) use ($carriers) {
                $q->whereIn('carrier_id', $carriers);
            })->where(function ($query) use ($origin_ports, $destination_ports, $origin_countries, $destination_countries) {
                $query->orwhereHas('globalcharport', function ($q) use ($origin_ports, $destination_ports) {
                    $q->whereIn('port_orig', $origin_ports)->whereIn('port_dest', $destination_ports);
                })->orwhereHas('globalcharcountry', function ($q) use ($origin_countries, $destination_countries) {
                    $q->whereIn('country_orig', $origin_countries)->whereIn('country_dest', $destination_countries);
                })->orwhereHas('globalcharportcountry', function ($q) use ($origin_ports, $destination_countries) {
                    $q->whereIn('port_orig', $origin_ports)->whereIn('country_dest', $destination_countries);
                })->orwhereHas('globalcharcountryport', function ($q) use ($origin_countries, $destination_ports) {
                    $q->whereIn('country_orig', $origin_countries)->whereIn('port_dest', $destination_ports);
                });
            })->whereDoesntHave('globalexceptioncountry', function ($q) use ($origin_countries, $destination_countries) {
                $q->whereIn('country_orig', $origin_countries)->orwhereIn('country_dest', $destination_countries);;
            })->whereDoesntHave('globalexceptionport', function ($q) use ($origin_ports, $destination_ports) {
                $q->whereIn('port_orig', $origin_ports)->orwhereIn('port_dest', $destination_ports);;
            })->where('company_user_id', '=', $company_user_id)->with('globalcharcarrier.carrier', 'currency', 'surcharge.saleterm', 'calculationtype')->get();
        }

        //Looping through Global Charges found, including in final collection if not there
        foreach ($global_charges_found as $charge) {
            $global_charges->push($charge);
        }

        return $global_charges;
    }

    //Retrieves and cleans markups from price levels
    public function searchPriceLevels($search_data)
    {
        //SEARCH TRAIT - Markups are organized in a collection containing
        //Freight markups (fixed & percent)
        //Local Charge markups (fixed & percent)
        //Inland markups (fixed & percent)
        $markups = $this->getMarkupsFromPriceLevels($search_data['pricelevel'], $search_data['client_currency'], $search_data['direction'], $search_data['type']);

        return $markups;
    }

    //Retrieves Global Remarks
    public function searchRemarks($rate, $search_data)
    {
        //Retrieving current companyto filter remarks
        $company_user = CompanyUser::where('id', $search_data['company_user'])->first();

        $remarks = RemarkCondition::where('company_user_id', $company_user->id)->get();

        $final_remarks = "";
        $included_contracts = [];
        $included_global_remarks = [];

        $rate_origin_port = $rate->origin_port;
        $rate_destination_port = $rate->destiny_port;
        $rate_carrier = $rate->carrier_id;

        foreach ($remarks as $remark) {
            $carriers = $remark->remarksCarriers()->get()->toArray();

            if ($remark->mode == 'port') {
                $ports = $remark->remarksHarbors()->get()->toArray();
            } else if ($remark->mode == 'country') {
                $ports = [];
                $countries = $remark->remarksCountries()->get();

                foreach ($countries as $country) {
                    $country_ports = $country->ports()->get()->toArray();

                    foreach ($country_ports as $port) {
                        array_push($ports, $port);
                    }
                }
            }

            $carrier_ids = $this->getIdsFromArray($carriers);

            $port_ids = $this->getIdsFromArray($ports);

            if (((in_array($rate_origin_port, $port_ids) || in_array($rate_destination_port, $port_ids)) && in_array($rate_carrier, $carrier_ids)) ||
                in_array(26, $carrier_ids) || in_array(1485, $port_ids)
            ) {
                if ($search_data['direction'] == 1 && !in_array($remark->id, $included_global_remarks)) {
                    $final_remarks .= $remark->import . "<br>";
                    array_push($included_global_remarks, $remark->id);
                } elseif ($search_data['direction'] == 2 && !in_array($remark->id, $included_global_remarks)) {
                    $final_remarks .= $remark->export . "<br>";
                    array_push($included_global_remarks, $remark->id);
                }
            }
        }

        if (!in_array($rate->contract_id, $included_contracts)) {
            $final_remarks .= $rate->contract->remarks . '<br>';
            array_push($included_contracts, $rate->contract->id);
        }

        return $final_remarks;
    }

    //Retrieves Terms and Conditions
    public function searchTerms($search_data)
    {
        //Retrieving current companyto filter terms
        $company_user = CompanyUser::where('id', $search_data['company_user'])->first();

        $terms = TermAndConditionV2::where([['company_user_id',$company_user->id],['type',$search_data['type']]])->get();

        $terms_english = '';
        $terms_spanish = '';
        $terms_portuguese = '';

        foreach($terms as $term){

            if($search_data['direction'] == 1){
                $terms_to_add = $term->import;
            }else if($search_data['direction'] == 2){
                $terms_to_add = $term->export;
            }

            if($term->language_id == 1){
                $terms_english .= $terms_to_add . '<br>';
            }else if($term->language_id == 2){
                $terms_spanish .= $terms_to_add . '<br>';
            }else if($term->language_id == 3){
                $terms_portuguese .= $terms_to_add . '<br>';
            }
        }

        $final_terms = ['english' => $terms_english, 'spanish' => $terms_spanish, 'portuguese' => $terms_portuguese ];

        return $final_terms;
    }

    //Retrives global Transit Times
    public function searchTransitTime($rate)
    {
        //Setting values fo query
        $origin_port = $rate->origin_port;
        $destination_port = $rate->destiny_port;
        $carrier = $rate->carrier_id;

        //Querying
        $transit_time = TransitTime::where([['origin_id',$origin_port],['destination_id',$destination_port]])->whereIn('carrier_id',[$carrier,26])->first();

        return $transit_time;
    }

    //Adds PriceLevels markups to target collection
    public function addMarkups($markups, $target, $client_currency)
    {
        //If markups will be added to a Rate, extracts 'freight' variables from markups array
        if (is_a($target, 'App\Rate')) {
            //Info from markups array
            $markups_to_add = $markups['freight'];
            $fixed = $markups_to_add['freight_amount'];
            $percent = $markups_to_add['freight_percentage'];
            $markups_currency = $markups_to_add['freight_currency'];
            //Price arrays from rate
            $target_containers = json_decode($target->containers, true);
            $target_totals = $target->totals;
            //If markups will be added to a Local or Global Charge, extracts 'charge' variables from markups array
        } elseif (is_a($target, 'App\LocalCharge') || is_a($target, 'App\GlobalCharge')) {
            //Info from markups array
            $markups_to_add = $markups['local_charges'];
            $fixed = $markups_to_add['local_charge_amount'];
            $percent = $markups_to_add['local_charge_percentage'];
            $markups_currency = $markups_to_add['local_charge_currency'];
            //Price arrays from charge
            $target_totals = $target->containers_client_currency;
            $target_containers = $target->containers;
            //SPECIAL CASE - OCEAN FREIGHT
        } elseif (isset($target->surcharge) && $target->surcharge->name == "Ocean Freight") {
            //Info from markups array
            $markups_to_add = $markups['freight'];
            $fixed = $markups_to_add['freight_amount'];
            $percent = $markups_to_add['freight_percentage'];
            $markups_currency = $markups_to_add['freight_currency'];
            //Price arrays from charge
            $target_totals = $target->containers_client_currency;
            $target_containers = $target->containers;
        }

        //Empty arrays to store final added values
        $containers_with_markups = [];
        $totals_with_markups = [];

        //Checking if markups are fixed rate
        if ($fixed != 0) {
            //Converting amount to Charge and Client currency to add directly
            $fixed_target_currency = $this->convertToCurrency($markups_currency, $target->currency, array($fixed));
            $fixed_client_currency = $this->convertToCurrency($markups_currency, $client_currency, array($fixed));

            //Empty arrays for markups in each currency
            $markups_array = [];
            $markups_client_currency = [];

            //Looping through containers (charge currency) to populate empty arrays
            foreach ($target_containers as $code => $cost) {
                //Checking if container price is not 0
                if ($cost != 0) {
                    //Storing markup and added container price
                    $markups_array[$code] = $fixed_target_currency[0];
                    $containers_with_markups[$code] = isDecimal($cost,true) + isDecimal($fixed_target_currency[0], true);
                }else{
                    //Storing cost 0 in final price array
                    $containers_with_markups[$code] = isDecimal($cost,true);
                }
            }

            //Looping through totals (client currency) to populate empty arrays
            foreach ($target_totals as $code => $cost) {
                //Checking if total is not 0
                if ($cost != 0) {
                    //Storing markup and added total 
                    $markups_client_currency[$code] = $fixed_client_currency[0];
                    $totals_with_markups[$code] = isDecimal($cost,true) + isDecimal($fixed_client_currency[0], true);
                }else{
                    //Storing cost 0 in final totals array
                    $totals_with_markups[$code] = isDecimal($cost,true);
                }
            }
            //Same loop but for percentile markups
        } elseif ($percent != 0) {
            //Calculating percentage of each container and each total price, storing them directly as final markups array
            $markups_array = $this->calculatePercentage($percent, $target_containers);
            $markups_client_currency = $this->calculatePercentage($percent, $target_totals);

            foreach($target_containers as $code => $cost){
                if($cost != 0){
                    $containers_with_markups[$code] = isDecimal($cost,true) + isDecimal($markups_array[$code],true);
                }else{
                    $containers_with_markups[$code] = isDecimal($cost,true);
                }
            }

            foreach($target_totals as $code => $cost){
                if($cost != 0){
                    $totals_with_markups[$code] = isDecimal($cost,true) + isDecimal($markups_client_currency[$code],true);
                }else{
                    $totals_with_markups[$code] = isDecimal($cost, true);
                }
            }
        } else {
            return;
        }

        //Appending markups and added containers and totals to rate or charge
        $target->setAttribute('container_markups', $markups_array);
        $target->setAttribute('totals_markups', $markups_client_currency);
        $target->setAttribute('containers_with_markups', $containers_with_markups);
        $target->setAttribute('totals_with_markups', $totals_with_markups);
    }

    //appending charges to corresponding Rate
    public function addToRate($rate, $target, $target_type, $client_currency)
    {
        //Checking type of property to add
        if ($target_type == 'charges') {
            $rate_charges = [];
            //Empty array for totals by type (Origin, Destination, Freight)
            $charge_type_totals = [];
            //Looping through charges type for array structure
            foreach ($target as $direction => $charge_direction) {
                $rate_charges[$direction] = [];
                $charge_type_totals[$direction] = [];

                //Looping through charges by type
                foreach ($charge_direction as $charge) {
                    if(!$charge->hide){   
                        //checking if markups were added to rates and charges
                        //Case 1 - markups on rate and on  charge
                        if (isset($rate->totals_with_markups) && isset($charge->totals_with_markups)) {
                            //Field that is gonna be updated in Rate
                            $to_update = 'totals_with_markups';
                            //Current field value
                            $totals_array = $rate->totals_with_markups;
                            //Charge totals that will be added to rate
                            $charges_to_add = $charge->totals_with_markups;
                            //Case 2 - markups on Charge NOT on Rate
                        } elseif (!isset($rate->totals_with_markups) && isset($charge->totals_with_markups)) {
                            $to_update = 'totals';
                            $totals_array = $rate->totals;
                            $charges_to_add = $charge->totals_with_markups;
                            //Case 3 - markups on Rate NOT on Charge
                        } elseif (isset($rate->totals_with_markups) && !isset($charge->totals_with_markups)) {
                            $to_update = 'totals_with_markups';
                            $totals_array = $rate->totals_with_markups;
                            $charges_to_add = $charge->containers_client_currency;
                            //Case 4 - markups NOT on Charge NOT on Rate
                        } elseif (!isset($rate->totals_with_markups) && !isset($charge->totals_with_markups)) {
                            $to_update = 'totals';
                            $totals_array = $rate->totals;
                            $charges_to_add = $charge->containers_client_currency;
                        }

                        //Looping through current Rate totals (with or without markups)
                        foreach ($totals_array as $code => $total) {
                            //Checking if charge contains each container present in Rate
                            if (isset($charge->containers_client_currency[$code])) {
                                //Adding charge container price to Rate totals
                                $totals_array[$code] += isDecimal($charges_to_add[$code], true);
                                //If container doesnt exist in totals by type array, set it to 0 (initialize value)
                                if (!isset($charge_type_totals[$direction][$code])) {
                                    $charge_type_totals[$direction][$code] = 0;
                                }
                                //Add prices from charge to totals by type
                                $charge_type_totals[$direction][$code] += isDecimal($charges_to_add[$code],true);
                            }
                        }

                        //Updating rate totals to new added array
                        $rate->$to_update = $totals_array;
                        array_push($rate_charges[$direction], $charge);

                        //
                        if ($direction == 'Freight') {
                            if ($charge->joint_as == 'charge_currency') {
                                $rate_currency_containers = $this->convertToCurrency($charge->currency, $rate->currency, $charge->containers);
                                $charge->containers = $rate_currency_containers;
                            } elseif ($charge->joint_as == 'client_currency') {
                                $rate_currency_containers = $this->convertToCurrency($client_currency, $rate->currency, $charge->containers_client_currency);
                                $charge->containers_client_currency = $rate_currency_containers;
                            }
                            $charge->currency = $rate->currency;
                        }
                    }
                }

                if ($direction == 'Freight') {
                    $charge_type_totals[$direction] = $this->convertToCurrency($client_currency, $rate->currency, $charge_type_totals[$direction]);

                    $ocean_freight_array = [
                        'surcharge' => ['name' => 'Ocean Freight'],
                        'containers' => json_decode($rate->containers, true),
                        'calculationtype' => ['name' => 'Per Container', 'id' => '5'], //CHANGE ID LATER
                        'typedestiny_id' => 3,
                        'currency' => ['alphacode' => $rate->currency->alphacode, 'id' => $rate->currency->id]
                    ];

                    if (isset($rate->container_markups)) {
                        $ocean_freight_array['container_markups'] = $rate->container_markups;
                        $ocean_freight_array['totals_markups'] = $rate->totals_markups;
                        $ocean_freight_array['containers_with_markups'] = $rate->containers_with_markups;
                        $ocean_freight_array['totals_with_markups'] = $rate->totals_with_markups;

                        $totals_array = $rate->containers_with_markups;
                    } else {
                        $totals_array = json_decode($rate->containers, true);
                    }

                    foreach ($totals_array as $code => $total) {
                        if (!isset($charge_type_totals[$direction][$code])) {
                            $charge_type_totals[$direction][$code] = 0;
                        }
                        $charge_type_totals[$direction][$code] += $total;
                    }

                    $ocean_freight_collection = collect($ocean_freight_array);

                    array_push($rate_charges[$direction], $ocean_freight_collection);
                }


                $rate->setAttribute('charge_totals_by_type', $charge_type_totals);

                if (count($rate_charges[$direction]) == 0) {
                    unset($rate_charges[$direction]);
                };
            }
            $rate->setAttribute('charges', $rate_charges);
        }
    }

    public function storeContractNewSearch(StoreContractSearch $request)
    {
        // dd($request);
        $req = $request->valueEq['id'];
        $contract = new Contract();
        $container = Container::get();

        $contract->company_user_id = Auth::user()->company_user_id;
        $contract->name = $request->reference;
        $contract->direction_id = $request->direction['id'];
        $contract->validity = date('Y-m-d', strtotime($request->datarange['startDate']));
        $contract->expire = date('Y-m-d', strtotime($request->datarange['endDate']));
        $contract->status = 'publish';
        $contract->gp_container_id = $request->valueEq['id'];
        $contract->is_manual = 2;
        $contract->save();

        $contract->ContractCarrierSyncSingle($request->carrier['id']);
        $contract->ContractRateStore($request, $contract, $req, $container);
        $contract->ContractSurchargeStore($request, $contract);

        foreach ($request->input('document', []) as $file) {
            $contract->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('document', 'contracts3');
        }

        return response()->json([
            'id' => $contract->id,
            'data' => 'Success',
        ]);
    }

    //Ordering rates by totals (cheaper to most expensive)
    public function sortRates($rates, $search_data_ids)
    {
        if (isset($search_data_ids['priceLevel'])) {
            $sortBy = 'totals_with_markups';
        } else {
            $sortBy = 'totals';
        }

        return ($rates->sortBy($sortBy)->values());
    }
}
