<?php

namespace App\Http\Controllers;

use App\Http\Traits\SearchTrait;
use App\Http\Traits\QuoteV2Trait;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SearchApiResource;
use App\Http\Resources\RateResource;

use App\InlandDistance;
use App\Harbor;
use App\Direction;
use App\SearchRate;
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
use App\GlobalCharge;
use App\TransitTime;
use App\RemarkCondition;
use App\Surcharge;
use App\CalculationType;
use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    use SearchTrait,QuoteV2Trait;

    //Shows the Search main view
    public function index(Request $request)
    {
        return view('searchV2.index');
    }

    //Retrieves last 4 searches made
    public function list(Request $request)
    {
        //Filtering and pagination
        $results = SearchRate::filterByCurrentCompany()->filter($request); //MAKE FILTERS PENDING

        //Grouping as collection to be managed by Vue
        return SearchApiResource::collection($results);//LIMIT TO FOUR OR MAKE SLIDER DISPLAY
    }

    //Retrieves all data needed for search processing and displaying
    public function data(Request $request)
    {
        //Querying each model used and mapping only necessary data
        $company_user_id = \Auth::user()->company_user_id;

        $carriers = Carrier::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name','image']);
        });

        $companies = Company::where('company_user_id','=',$company_user_id)->get()->map(function ($company){
            return $company->only(['id','business_name']);
        });

        $fullCompanies = Company::where('company_user_id','=',$company_user_id)->get();

        $contacts = [];

        foreach($fullCompanies as $comp){
            $newContacts = $comp->contact()->get();

            foreach($newContacts as $cont){
                if(!in_array($cont,$contacts)){
                    $cont->setAttribute('name',$cont->getFullName());
                    array_push($contacts,$cont);
                }
            }
        }

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name','country_id','code','harbor_parent']);
        });

        $terms_and_conditions = TermAndConditionV2::get()->map(function ($term_and_condition){
            return $term_and_condition->only(['id','name','user_id','type','company_user_id']);
        });

        $delivery_types = DeliveryType::get()->map(function ($delivery_type){
            return $delivery_type->only(['id','name']);
        });

        $currency = Currency::get()->map(function ($curr){
            return $curr->only(['id','alphacode','rates','rates_eur']);
        });

        $common_currencies = Currency::whereIn('id', ['46','149'])->get()->map(function ($curr){
            return $curr->only(['id','alphacode','rates','rates_eur']);
        });

        $containers = Container::all();
        
        $container_groups = GroupContainer::all();

        $directions = Direction::all();

        $schedule_types = ScheduleType::get()->map(function ($schedule_type){
            return $schedule_type->only(['id','name']);
        });
        
        $countries = Country::get()->map(function ($country){
            return $country->only(['id','code','name']);
        });

        $price_levels = Price::where('company_user_id',$company_user_id)->get()->map(function ($price){
            return $price->only(['id','name']);
        });
        
        $surcharges = Surcharge::where('company_user_id','=',$company_user_id)->get()->map(function ($surcharge){
            return $surcharge->only(['id','name',]);
        });

        $calculation_type = CalculationType::get()->map(function ($calculationt){
            return $calculationt->only(['id','name']);
        });

        $type_destiny = TypeDestiny::all();

        //Collecting all data retrieved
        $data = compact(
            'company_user_id',
            'carriers',
            'companies',
            'contacts',
            'currency',
            'common_currencies',
            'containers',
            'container_groups',
            'countries',
            'delivery_types',
            'directions',
            'harbors',
            'price_levels',
            'schedule_types',
            'terms_and_conditions',
            'type_destiny',
            'surcharges',
            'calculation_type'
        );

        return response()->json(['data'=>$data]);
    }

    //Validates search request data
    public function processSearch(Request $request)
    {   
        //dd($request);
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
            'pricelevel' => 'sometimes'
        ]);
        
        //Stripping time stamp from date
        $new_search_data['dateRange']['startDate'] = substr($new_search_data['dateRange']['startDate'], 0, 10);
        $new_search_data['dateRange']['endDate'] = substr($new_search_data['dateRange']['endDate'], 0, 10);

        //Setting current company and user
        $user = \Auth::user();
        $user_id = $user->id;
        $company_user_id = $user->company_user_id;

        //Including company and user in search data array
        $new_search_data['user'] = $user_id;
        $new_search_data['company_user'] = $company_user_id;

        //SEARCH TRAIT - Getting new array that contains only ids, for queries
        $new_search_data_ids = $this->getIdsFromArray($new_search_data);

        //Setting recent searches
        $recent_searches = $this->recentSearch($new_search_data_ids);
        
        //Storing search history
        $search = $this->store($new_search_data_ids, $recent_searches);

        if(array_key_exists('pricelevel',$new_search_data) && $new_search_data['pricelevel'] != null){
            $search->SetAttribute('price_level', $new_search_data['pricelevel']['id']);
        }

        if(array_key_exists('company',$new_search_data) && $new_search_data['company'] != null){
            $search->SetAttribute('company_id', $new_search_data_ids['company']);
        }

        if(array_key_exists('contact',$new_search_data) && $new_search_data['contact'] != null){
            $search->SetAttribute('contact_id', $new_search_data_ids['contact']);
        }       
        
        //Retrieving rates with search data
        $rates = $this->searchRates($new_search_data_ids);

        $remarks = $this->searchRemarks($rates, $new_search_data_ids);
        
        //$rateNo = 0;
        foreach($rates as $rate){
            //$rateNo += 1;
            //dump($rate->contract);
            //dump('for rate '. strval($rateNo));
            //Retrieving local charges with search data
            $local_charges = $this->searchLocalCharges($new_search_data_ids, $rate);
        
            //Retrieving global charges with search data
            $global_charges = $this->searchGlobalCharges($new_search_data_ids, $rate);

            //SEARCH TRAIT - Grouping charges by type (Origin, Destination, Freight)
            $charges = $this->groupChargesByType($local_charges, $global_charges);
            
            //SEARCH TRAIT - Calculates charges by container and appends the cost array to each charge instance
            $this->setChargesPerContainer($charges,$new_search_data['containers'], $company_user_id);
    
            //SEARCH TRAIT - Join charges (within group) if Surcharge, Carrier, Port and Typedestiny match
            $charges = $this->joinCharges($charges);
    
            //Getting price levels if requested
            if(array_key_exists('pricelevel',$new_search_data) && $new_search_data['pricelevel'] != null){
                $price_level_markups = $this->searchPriceLevels($new_search_data_ids);
            }else{
                $price_level_markups = [];
            }
    
            //Adding price levels
            if($price_level_markups != null && count($price_level_markups) != 0){
                $this->addMarkups($price_level_markups, $rate, $company_user_id);
                foreach($charges as $charge_direction){
                    foreach($charge_direction as $charge){
                        $this->addMarkups($price_level_markups, $charge, $company_user_id);
                    }
                }
            }

            //Appending Rate Id to Charges
            $this->addToRate($rate, $charges, 'charges', $company_user_id);    

            $transit_time = $this->searchTransitTime($rate);
    
            $rate->setAttribute('transit_time', $transit_time);

            $rate->setAttribute('remarks', $remarks);
    
            $rate->SetAttribute('search', $search);
        }

        return RateResource::collection($rates);
    }

    public function recentSearch($data)
    {
        //Formatting date
        $pick_up_date = $data['dateRange']['startDate'].' / '.$data['dateRange']['endDate'];

        $container_array = [];
         
        //FORMATTING FOR OLD SEARCH, MUST BE REMOVED
        foreach($data['containers'] as $container_id){
            $container = Container::where('id',$container_id)->first();

            array_push($container_array, $container->code);
        }

        //Querying for an exact match
        $recent = SearchRate::where([
            ['company_user_id', $data['company_user']],
            ['pick_up_date', $pick_up_date],
            ['delivery', $data['deliveryType']],
            ['direction', $data['direction']],
            ['type', $data['type']],
            ['equipment', $container_array], 
            ['user_id', $data['user']]
        ])->get();

        return SearchApiResource::collection($recent); 
    }

    //Stores current search if its different from other searches
    public function store($data, $recent)
    {
        //Formatting date
        $pick_up_date = $data['dateRange']['startDate'].' / '.$data['dateRange']['endDate'];

        //formatting containers
        $container_array = [];

        //FORMATTING FOR OLD SEARCH, MUST BE REMOVED
        foreach($data['containers'] as $container_id){
            $container = Container::where('id',$container_id)->first();

            array_push($container_array, $container->code);
        }

        $matches = false;

        // Checking for matches and creating new search registry if none
        if($recent != null && count((array) $recent) != 0){
            foreach($recent as $rc){
                if($rc->equipment == $container_array){
                    return $rc;
                }
            }
        }
        
        if(!$matches){
            $new_search = SearchRate::create([
                'company_user_id' => $data['company_user'],
                'pick_up_date' => $pick_up_date,
                'equipment' => $container_array, 
                'delivery' => $data['deliveryType'],
                'direction' => $data['direction'],
                'type' => $data['type'],
                'user_id' => $data['user']
            ]);
        }
        
        return $new_search;
    }

    //Finds any Rates associated to a contract valid in search dates, matching search ports
    public function searchRates($search_data)
    {       
        //setting variables for query
        $company_user_id = $search_data['company_user'];
        $company_user = CompanyUser::where('id',$search_data['company_user'])->first();
        $user_id = $search_data['user'];
        $container_group = $search_data['selectedContainerGroup'];
        $company_id = $search_data['company_user'];
        $origin_ports = $search_data['originPorts'];
        $destiny_ports = $search_data['destinationPorts'];
        $arregloCarrier = $search_data['carriers'];
        $dateSince = $search_data['dateRange']['startDate'];
        $dateUntil = $search_data['dateRange']['endDate'];
    
        //Querying rates database
        if ($company_id != null || $company_id != 0) {
            $rates_query = Rate::whereIn('origin_port', $origin_ports)->whereIn('destiny_port', $destiny_ports)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract', 'carrier','currency')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $company_id) {
                $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                    $a->where('user_id', '=', $user_id);
                })->orDoesntHave('contract_user_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $company_id, $container_group) {
                $q->whereHas('contract_company_restriction', function ($b) use ($company_id) {
                    $b->where('company_id', '=', $company_id);
                })->orDoesntHave('contract_company_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $container_group, $company_user) {
                if ($company_user->future_dates == 1) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', $container_group);
                } else {
                    $q->where(function ($query) use ($dateSince, $dateUntil) {
                        $query->where('validity', '>=', $dateSince)->where('expire', '>=', $dateUntil);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', $container_group);
                }
            });
        } else {
            $rates_query = Rate::whereIn('origin_port', $origin_ports)->whereIn('destiny_port', $destiny_ports)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract', 'carrier','currency')->whereHas('contract', function ($q) {
                $q->doesnthave('contract_user_restriction');
            })->whereHas('contract', function ($q) {
                $q->doesnthave('contract_company_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $container_group, $company_user) {
                if ($company_user->future_dates == 1) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', $container_group);
                } else {
                    $q->where(function ($query) use ($dateSince, $dateUntil) {
                        $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                    })->where('company_user_id', '=', $company_user_id)->where('status', '!=', 'incomplete')->where('gp_container_id', $container_group);
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
        $this->joinRateContainers($rates_array,$search_data['containers']);

        //Setting attribute to totalize adding charges, inlands, markups, etc. Totals are shown in the client default currency
        foreach($rates_array as $rate){
            //Converting rates to client currency
            $client_currency = $company_user->currency;
            $containers_client_currency = $this->convertToCurrency($rate->currency, $client_currency, json_decode($rate->containers,true));
            $rate->setAttribute('totals',$containers_client_currency);
            $rate->setAttribute('client_currency',$client_currency);
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
    public function searchLocalCharges($search_data, $rate)
    {
        //Creating empty collection for storing charges
        $local_charges = collect([]);
        //Pulling necessary data from the search IDs array
        $origin_ports = $search_data['originPorts'];
        $destination_ports = $search_data['destinationPorts'];
        $origin_countries = [];
        $destination_countries = [];
        //SEARCH API - Getting countries from port arrays and building countries array
        foreach($origin_ports as $origin_port){
            $origin_country = $this->getPortCountry($origin_port);
            array_push($origin_countries,$origin_country);
        }
        foreach($destination_ports as $destination_port){
            $destination_country = $this->getPortCountry($destination_port);
            array_push($destination_countries,$destination_country);
        }

        //Including "ALL" columns for querying LocalCharges with such option marked
            //IDS: On Harbors: 1485; On Countries: 250
        array_push($origin_ports,1485);
        array_push($destination_ports,1485);
        array_push($origin_countries,250);
        array_push($destination_countries,250);

        //creating carriers array with only rates carrier
        $carriers = Array($rate->carrier->id);
        //Including "ALL" column of carriers table
        array_push($carriers,26);
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
            })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm','calculationtype')->orderBy('typedestiny_id', 'calculationtype_id', 'surchage_id')->get();
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
            })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm','calculationtype')->orderBy('typedestiny_id', 'calculationtype_id', 'surchage_id')->get();
        }

        //Looping through Local Charges found, including in final collection if not there
        foreach($local_charge as $charge){
            $local_charges->push($charge);
        }

        return $local_charges;        
    }

    //Finds global charges matching search data
    public function searchGlobalCharges($search_data, $rate)
    {
        //building Carriers array from rates
        $carriers = [];
        array_push($carriers, $rate->carrier->id);
        array_push($carriers, 26);
        //Creating empty collection for storing charges
        $global_charges = collect([]);
        //Pulling necessary data from the search IDs array
        $validity_start = $search_data['dateRange']['startDate'];
        $validity_end = $search_data['dateRange']['endDate'];
        $origin_ports = $search_data['originPorts'];
        $destination_ports = $search_data['destinationPorts'];
        $company_user_id = $search_data['company_user'];
        $origin_countries = [];
        $destination_countries = [];
        //SEARCH API - Getting countries from port arrays and building countries array
        foreach($origin_ports as $origin_port){
            $origin_country = $this->getPortCountry($origin_port);
            array_push($origin_countries,$origin_country);
        }
        foreach($destination_ports as $destination_port){
            $destination_country = $this->getPortCountry($destination_port);
            array_push($destination_countries,$destination_country);
        }

        //Including "ALL" columns for querying GlobalCharges with such option marked
            //IDS: On Harbors: 1485; On Countries: 250
        array_push($origin_ports,1485);
        array_push($destination_ports,1485);
        array_push($origin_countries,250);
        array_push($destination_countries,250);

        $contractStatus = 'NOT API'; //*************** CHANGE THIS LATER ************
    
        if ($contractStatus != 'api') {

            $global_charges_found = GlobalCharge::where([['validity', '>=', $validity_start],['expire', '>=', $validity_end]])->whereHas('globalcharcarrier', function ($q) use ($carriers) {
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
        foreach($global_charges_found as $charge){
            $global_charges->push($charge);
        }

        return $global_charges;
    }

    //Retrieves and cleans markups from price levels
    public function searchPriceLevels($search_data)
    {
        //Retrieving current company
        $company_user = CompanyUser::where('id',$search_data['company_user'])->first();
    
        //getting client profile currency
        $client_currency = $company_user->currency;
    
        //SEARCH TRAIT - Markups are organized in a collection containing
            //Freight markups (fixed & percent)
            //Local Charge markups (fixed & percent)
            //Inland markups (fixed & percent)
        $markups = $this->getMarkupsFromPriceLevels($search_data['pricelevel'], $client_currency, $search_data['direction'], $search_data['type']);
    
        return $markups;
    }

    //Retrieves Global Remarks
    public function searchRemarks($rates,$search_data)
    {
        //Retrieving current companyto filter remarks
        $company_user = CompanyUser::where('id',$search_data['company_user'])->first();

        $remarks = RemarkCondition::where([['company_user_id', $company_user->id], ['type',$search_data['type']]])->get();

        $final_remarks = "";
        $included_contracts = [];
        $included_global_remarks = [];

        foreach($rates as $rate){
            $rate_origin_port = $rate->origin_port;
            $rate_destination_port = $rate->destiny_port;
            $rate_carrier = $rate->carrier_id;
    
            foreach($remarks as $remark){
                $carriers = $remark->remarksCarriers()->get()->toArray();
    
                if($remark->mode == 'port'){
                    $ports = $remark->remarksHarbors()->get()->toArray();
                }else if($remark->mode == 'country'){
                    $ports = [];
                    $countries = $remark->remarksCountries()->get();
    
                    foreach($countries as $country){
                        $country_ports = $country->ports()->get()->toArray();
    
                        foreach($country_ports as $port){
                            array_push($ports, $port);
                        }
                    }
                }
    
                $carrier_ids = $this->getIdsFromArray($carriers);
    
                $port_ids = $this->getIdsFromArray($ports);
    
                if(((in_array($rate_origin_port, $port_ids) || in_array($rate_destination_port, $port_ids)) && in_array($rate_carrier, $carrier_ids)) ||
                    in_array(26,$carrier_ids) || in_array(1485,$port_ids)) {
                    if($search_data['direction'] == 1 && !in_array($remark->id,$included_global_remarks)){
                        $final_remarks .= $remark->import . "<br>";
                        array_push($included_global_remarks,$remark->id);
                    }elseif($search_data['direction'] == 2 && !in_array($remark->id,$included_global_remarks)){
                        $final_remarks .= $remark->export . "<br>";
                        array_push($included_global_remarks,$remark->id);
                    }
                }
            }

            if(!in_array($rate->contract_id,$included_contracts)){
                $final_remarks .= $rate->contract->remarks . '<br>';
                array_push($included_contracts, $rate->contract->id);
            }
        }
        
        return $final_remarks;

    }

    //Retrives global Transit Times
    public function searchTransitTime($rate)
    {
        //Setting values fo query
        $origin_port = $rate->origin_port;
        $destination_port = $rate->destiny_port;
        $carrier = $rate->carrier_id;

        //Querying
        $transit_time = TransitTime::where('origin_id',$origin_port)->where('destination_id',$destination_port)->whereIn('carrier_id',[$carrier,26])->first();

        return $transit_time;
    }
    
    //Adds PriceLevels markups to target collection
    public function addMarkups($markups, $target, $company_user_id)
    {
        //Setting company related info
        $company_user = CompanyUser::where('id',$company_user_id)->first();
        $client_currency = $company_user->currency;

        //If markups will be added to a Rate, extracts 'freight' variables from markups array
        if(is_a($target, 'App\Rate')){
            //Info from markups array
            $markups_to_add = $markups['freight'];
            $fixed = $markups_to_add['freight_amount'];
            $percent = $markups_to_add['freight_percentage'];
            $markups_currency = $markups_to_add['freight_currency'];
            //Price arrays from rate
            $target_containers = json_decode($target->containers, true);
            $target_totals = $target->totals;
        //If markups will be added to a Local or Global Charge, extracts 'charge' variables from markups array
        }elseif(is_a($target, 'App\LocalCharge') || is_a($target, 'App\GlobalCharge')){
            //Info from markups array
            $markups_to_add = $markups['local_charges'];
            $fixed = $markups_to_add['local_charge_amount'];
            $percent = $markups_to_add['local_charge_percentage'];
            $markups_currency = $markups_to_add['local_charge_currency'];
            //Price arrays from charge
            $target_totals = $target->containers_client_currency;
            $target_containers = $target->containers;
        //SPECIAL CASE - OCEAN FREIGHT
        }elseif(isset($target->surcharge) && $target->surcharge->name == "Ocean Freight"){
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
        if($fixed != 0){
            //Converting amount to Charge and Client currency to add directly
            $fixed_target_currency = $this->convertToCurrency($markups_currency, $target->currency, Array($fixed));
            $fixed_client_currency = $this->convertToCurrency($markups_currency, $client_currency, Array($fixed));

            //Empty arrays for markups in each currency
            $markups_array = [];
            $markups_client_currency = [];

            //Looping through containers (charge currency) to populate empty arrays
            foreach($target_containers as $code => $cost){
                //Checking if container price is not 0
                if($cost != 0){
                    //Storing markup and added container price
                    $markups_array[$code] = $fixed_target_currency[0];
                    $containers_with_markups[$code] = $cost + isDecimal($fixed_target_currency[0], true);
                }else{
                    //Storing cost 0 in final price array
                    $containers_with_markups[$code] = $cost;
                }
            }
            
            //Looping through totals (client currency) to populate empty arrays
            foreach($target_totals as $code => $cost){
                //Checking if total is not 0
                if($cost != 0){
                    //Storing markup and added total 
                    $markups_client_currency[$code] = $fixed_client_currency[0];
                    $totals_with_markups[$code] = $cost + isDecimal($fixed_client_currency[0], true);
                }else{
                    //Storing cost 0 in final totals array
                    $totals_with_markups[$code] = $cost;
                }
            }
        //Same loop but for percentile markups
        }elseif($percent != 0){
            //Calculating percentage of each container and each total price, storing them directly as final markups array
            $markups_array = $this->calculatePercentage($percent,$target_containers);
            $markups_client_currency = $this->calculatePercentage($percent,$target_totals);

            foreach($target_containers as $code => $cost){
                if($cost != 0){
                    $containers_with_markups[$code] = $cost + isDecimal($markups_array[$code], true);
                }else{
                    $containers_with_markups[$code] = $cost;
                }
            }

            foreach($target_totals as $code => $cost){
                if($cost != 0){
                    $totals_with_markups[$code] = $cost + isDecimal($markups_client_currency[$code], true);
                }else{
                    $totals_with_markups[$code] = $cost;
                }
            }
        }else{
            return;
        }
        //Appending markups and added containers and totals to rate or charge
        $target->setAttribute('container_markups',$markups_array);
        $target->setAttribute('totals_markups',$markups_client_currency);
        $target->setAttribute('containers_with_markups',$containers_with_markups);
        $target->setAttribute('totals_with_markups',$totals_with_markups);
    }

    //appending charges to corresponding Rate
    public function addToRate($rate, $target, $target_type, $company_user_id)
    {
        //Setting customer currency to convert if necessary
        $company_user = CompanyUser::where('id',$company_user_id)->first();
        $client_currency = $company_user->currency;

        //Checking type of property to add
        if($target_type == 'charges'){
            $rate_charges = [];
            //Empty array for totals by type (Origin, Destination, Freight)
            $charge_type_totals = [];
            //Looping through charges type for array structure
            foreach($target as $direction => $charge_direction){
                $rate_charges[$direction] = [];
                $charge_type_totals[$direction] = [];

                //Looping through charges by type
                foreach($charge_direction as $charge){
                //checking if markups were added to rates and charges
                    //Case 1 - markups on rate and on  charge
                    if(isset($rate->totals_with_markups) && isset($charge->totals_with_markups)){
                        //Field that is gonna be updated in Rate
                        $to_update = 'totals_with_markups';
                        //Current field value
                        $totals_array = $rate->totals_with_markups;
                        //Charge totals that will be added to rate
                        $charges_to_add = $charge->totals_with_markups;
                    //Case 2 - markups on Charge NOT on Rate
                    }elseif(!isset($rate->totals_with_markups) && isset($charge->totals_with_markups)){
                        $to_update = 'totals';
                        $totals_array = $rate->totals;
                        $charges_to_add = $charge->totals_with_markups;
                    //Case 3 - markups on Rate NOT on Charge
                    }elseif(isset($rate->totals_with_markups) && !isset($charge->totals_with_markups)){
                        $to_update = 'totals_with_markups';
                        $totals_array = $rate->totals_with_markups;
                        $charges_to_add = $charge->containers_client_currency;
                    //Case 4 - markups NOT on Charge NOT on Rate
                    }elseif(!isset($rate->totals_with_markups) && !isset($charge->totals_with_markups)){
                        $to_update = 'totals';
                        $totals_array = $rate->totals;
                        $charges_to_add = $charge->containers_client_currency;
                    }

                    //Looping through current Rate totals (with or without markups)
                    foreach($totals_array as $code => $total){
                        //Checking if charge contains each container present in Rate
                        if(isset($charge->containers_client_currency[$code])){
                            //Adding charge container price to Rate totals
                            $totals_array[$code] += isDecimal($charges_to_add[$code],true);
                            //If container doesnt exist in totals by type array, set it to 0 (initialize value)
                            if(!isset($charge_type_totals[$direction][$code])){
                                $charge_type_totals[$direction][$code] = 0;
                            }
                            //Add prices from charge to totals by type
                            $charge_type_totals[$direction][$code] += $charges_to_add[$code];
                        }
                    }

                    //Updating rate to totals to new added array
                    $rate->$to_update = $totals_array;
                    array_push($rate_charges[$direction], $charge);
                    
                    //
                    if($direction == 'Freight'){
                        if($charge->joint_as == 'charge_currency'){
                            $rate_currency_containers = $this->convertToCurrency($charge->currency, $rate->currency, $charge->containers);
                            $charge->containers = $rate_currency_containers;
                        }elseif($charge->joint_as == 'client_currency'){
                            $rate_currency_containers = $this->convertToCurrency($client_currency, $rate->currency, $charge->containers_client_currency);
                            $charge->containers_client_currency = $rate_currency_containers;
                        }
                        $charge->currency = $rate->currency;
                    }
                }

                if($direction == 'Freight'){
                    $charge_type_totals[$direction] = $this->convertToCurrency($client_currency, $rate->currency, $charge_type_totals[$direction]);

                    $ocean_freight_array = [
                        'surcharge' => ['name' => 'Ocean Freight'],
                        'containers' => json_decode($rate->containers,true),
                        'calculationtype' => ['name' => 'Per Container', 'id' => '5'], //CHANGE ID LATER
                        'typedestiny_id' => 3,
                        'currency' => ['alphacode' => $rate->currency->alphacode, 'id' => $rate->currency->id]
                    ];
                    
                    if(isset($rate->container_markups)){
                        $ocean_freight_array['container_markups'] = $rate->container_markups;
                        $ocean_freight_array['totals_markups'] = $rate->totals_markups;
                        $ocean_freight_array['containers_with_markups'] = $rate->containers_with_markups;
                        $ocean_freight_array['totals_with_markups'] = $rate->totals_with_markups;

                        $totals_array = $rate->containers_with_markups;
                    }else{
                        $totals_array = json_decode($rate->containers,true);
                    }

                    foreach($totals_array as $code => $total){
                        if(!isset($charge_type_totals[$direction][$code])){
                            $charge_type_totals[$direction][$code] = 0;
                        }
                        $charge_type_totals[$direction][$code] += $total;
                    }

                    $ocean_freight_collection = collect($ocean_freight_array);

                    array_push($rate_charges[$direction], $ocean_freight_collection);
                }
                

                $rate->setAttribute('charge_totals_by_type',$charge_type_totals);
                
                if(count($rate_charges[$direction]) == 0){
                    unset($rate_charges[$direction]);
                };
            }
            $rate->setAttribute('charges',$rate_charges);
            
        }
    }    
}
