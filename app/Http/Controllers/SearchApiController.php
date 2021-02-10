<?php

namespace App\Http\Controllers;

use App\Http\Traits\SearchTrait;
use App\Http\Traits\QuoteV2Trait;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SearchApiResource;

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
use App\CompanyUser;
use App\LocalCharge;
use App\GlobalCharge;

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

        $comps = Company::where('company_user_id','=',$company_user_id)->get();
        $contacts = [];
        $languages = [];
        foreach ($comps as $comp) {
            array_push($languages,['company_id'=>$comp->id,'name'=>$comp->pdf_language]);
            $cts = $comp->contact()->get();
            foreach ($cts as $ct) {
                array_push($contacts,['id'=>$ct->id,'company_id'=>$ct->company_id,'name'=>$ct->getFullName()]);
            } 
        };

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

        $price_levels = Price::get()->map(function ($price){
            return $price->only(['id','name']);
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
            'comps',
            'countries',
            'delivery_types',
            'directions',
            'harbors',
            'price_levels',
            'schedule_types',
            'terms_and_conditions',
            'type_destiny'
        );

        return response()->json(['data'=>$data]);
    }

    //Validates search request data
    public function processSearch(Request $request)
    {   
        //Validating request data from form
        $new_search_data = $request->validate([
            'originPorts.*.id' => 'required',
            'destinationPorts.*.id' => 'required',
            'dateRange' => 'required',
            'containers.*.id' => 'required', 
            'selectedContainerGroup' => 'required',
            'deliveryType.id' => 'required', 
            'direction' => 'required',
            'carriers.*.id' => 'required',
            'type' => 'required',
            'company' => 'sometimes',
            'contact' => 'sometimes',
            'pricelevel' => 'sometimes'
        ]);

        //Setting current company and user
        $user = \Auth::user();
        $user_id = $user->id;
        $company_user_id = $user->company_user_id;

        //Including company and user in search data array
        $new_search_data['user'] = $user_id;
        $new_search_data['company'] = $company_user_id; 
                
        //SEARCH TRAIT - Getting new array that contains only ids, for queries
        $new_search_data_ids = $this->getIdsFromSearchRequest($new_search_data);
        
        //Storing new search with request data
        //$this->store($new_search_data_ids);
        
        //Retrieving rates with search data
        $rates = $this->searchRates($new_search_data_ids);

        //Retrieving local charges with search data
        $local_charges = $this->searchLocalCharges($new_search_data_ids, $rates);

        //Retrieving global charges with search data
        $global_charges = $this->searchGlobalCharges($new_search_data_ids, $rates);

        //SEARCH TRAIT - Grouping charges by type (Origin, Destination, Freight)
        $charges = $this->groupChargesByType($local_charges, $global_charges);

        //SEARCH TRAIT - Calculates charges by container and appends the cost array to each charge instance
        $this->setChargesPerContainer($charges,$new_search_data['containers'], $company_user_id);

        //SEARCH TRAIT - Join charges (within group) if Surcharge, Carrier, Port and Typedestiny match
        $charges = $this->joinCharges($charges);

        /**Retrieving and calculating inlands from search data, only if door delivery indicated
        if(array_key_exists('deliveryType',$new_search_data) && in_array($new_search_data_ids['deliveryType'],[2,3,4])){
            $inlands = $this->searchInlands($new_search_data_ids);
        }else{
            $inlands = [];
        }**/

         //Getting price levels if requested
         if(array_key_exists('pricelevel',$new_search_data) && $new_search_data['pricelevel'] != null){
            $price_level_markups = $this->searchPriceLevels($new_search_data_ids);
        }else{
            $price_level_markups = [];
        }

        if($price_level_markups != null && count($price_level_markups) != 0){
            $this->addMarkups($price_level_markups, $rates, $company_user_id);
            foreach($charges as $charge){
                $this->addMarkups($price_level_markups, $charge, $company_user_id);
            }
        }

        //Appending Rate Id to Charges
        $this->addToRates($rates, $charges, 'charges', $company_user_id);

        $data = [
            'rates' => $rates,
            'pricelevels' => $price_level_markups
        ];

        return $data; 
    }

    //Stores current search if its different from other searches
    public function store($data)
    {
        $user = \Auth::user();
        $company_user_id = $user->company_user_id;

        //Generating carriers codes json for searchrate query
        $equipment_json = $this->idsToProperty();

        //Querying for an exact match
        $new_search = SearchRate::where([
            ['company_user_id', $company_user_id],
            //['pick_up_date' => $data['pick_up_date']],
            ['delivery_type' => $data['deliveryType']],
            ['direction', $data['direction']],
            ['type' => $data['type']],
            ['equipment', $data['equipment']], 
            ['user' => $user]
        ])->first();

        //Checking for matches and creating new search registry if none
        if($new_search == null){
            $new_search = SearchRate::create([
                'company_user_id' => $company_user_id,
                'pick_up_date' => $pick_up_date,
                'equipment' => $data['equipment'], 
                'delivery_type' => $data['delivery_type'],
                'direction' => $data['direction'],
                'type' => $data['type'],
                'user' => $user
            ]);
        }       
    }

    //Finds any Rates associated to a contract valid in search dates, matching search ports
    public function searchRates($search_data)
    {       
        //setting variables for query
        $company_user_id = $search_data['company'];
        $company_user = CompanyUser::where('id',$search_data['company'])->first();
        $user_id = $search_data['user'];
        $container_group = $search_data['selectedContainerGroup'];
        $company_id = $search_data['company'];
        $origin_ports = $search_data['originPorts'];
        $destiny_ports = $search_data['destinationPorts'];
        $arregloCarrier = $search_data['carriers'];
        $dateSince = $search_data['dateRange']['validity_start'];
        $dateUntil = $search_data['dateRange']['validity_end'];
    
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
        $this->joinRateContainers($rates_array);

        //Setting attribute to totalize adding charges, inlands, markups, etc. Totals are shown in the client default currency
        foreach($rates_array as $rate){
            //Converting rates to client currency
            $client_currency = $company_user->currency;
            $containers_client_currency = $this->convertToCurrency($rate->currency, $client_currency, json_decode($rate->containers,true));
            $rate->setAttribute('totals',$containers_client_currency);
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
    public function searchLocalCharges($search_data, $rates)
    {
        //Checking that there are rates returned by the search
        if(count($rates) != 0){
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

            //Looping through rates
            foreach($rates as $rate){
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
                    if(!$local_charges->contains($charge)){
                        $local_charges->push($charge);
                    }
                }
            }
        }

        return $local_charges;        
    }

    //Finds global charges matching search data
    public function searchGlobalCharges($search_data, $rates)
    {
        //Checking that there are rates returned by the search
        if(count($rates) != 0){
            //building Carriers array from rates
            $carriers = [];
            foreach($rates as $rate){
                array_push($carriers,$rate->carrier->id);
            }
            array_push($carriers,26);
            //Creating empty collection for storing charges
            $global_charges = collect([]);
            //Pulling necessary data from the search IDs array
            $validity_start = $search_data['dateRange']['validity_start'];
            $validity_end = $search_data['dateRange']['validity_end'];
            $carriers = $search_data['carriers'];
            $origin_ports = $search_data['originPorts'];
            $destination_ports = $search_data['destinationPorts'];
            $company_user_id = $search_data['company'];
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
    
                $global_charge = GlobalCharge::where([['validity', '>=', $validity_start],['expire', '>=', $validity_end]])->whereHas('globalcharcarrier', function ($q) use ($carriers) {
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
                })->where('company_user_id', '=', $company_user_id)->with('globalcharcarrier.carrier', 'currency', 'surcharge.saleterm','globalcharport','calculationtype')->get();
            }

            //Looping through Global Charges found, including in final collection if not there
            foreach($global_charge as $charge){
                if(!$global_charges->contains($charge)){
                    $global_charges->push($charge);
                }
            }
        }

        return $global_charges;
    }

    //Retrieves and cleans markups from price levels
    public function searchPriceLevels($search_data)
    {
        //Retrieving current company
        $company_user = CompanyUser::where('id',$search_data['company'])->first();
    
        //getting client profile currency
        $client_currency = $company_user->currency;
    
        //SEARCH TRAIT - Markups are organized in a collection containing
            //Freight markups (fixed & percent)
            //Local Charge markups (fixed & percent)
            //Inland markups (fixed & percent)
        $markups = $this->getMarkupsFromPriceLevels($search_data['pricelevel'], $client_currency, $search_data['direction'], $search_data['type']);
    
        return $markups;
    }
    
    //Adds PriceLevels markups to target collection
    public function addMarkups($markups,$target_array, $company_user_id)
    {
        $company_user = CompanyUser::where('id',$company_user_id)->first();
        $client_currency = $company_user->currency;

        if($target_array != null && count($target_array) != 0){
            foreach($target_array as $target_element){

                if(is_a($target_element, 'App\Rate')){
                    $markups_to_add = $markups['freight'];
                    $fixed = $markups_to_add['freight_amount'];
                    $percent = $markups_to_add['freight_percentage'];
                    $markups_currency = $markups_to_add['freight_currency'];
                    $target_containers = json_decode($target_element->containers, true);
                    $target_totals = $target_element->totals;
                }elseif(is_a($target_element, 'App\LocalCharge') || is_a($target_element, 'App\GlobalCharge')){
                    $markups_to_add = $markups['local_charges'];
                    $fixed = $markups_to_add['local_charge_amount'];
                    $percent = $markups_to_add['local_charge_percentage'];
                    $markups_currency = $markups_to_add['local_charge_currency'];
                    $target_totals = $target_element->containers_client_currency;
                    $target_containers = $target_element->containers;
                }
                
                $containers_with_markups = [];
                $totals_with_markups = [];
                if($fixed != 0){
                    $fixed_target_currency = $this->convertToCurrency($markups_currency, $client_currency, Array($fixed));
                    $fixed_client_currency = $this->convertToCurrency($markups_currency, $target_element->currency, Array($fixed));

                    $markups_array = [
                        'type' => 'fixed',
                        'amount' => $fixed_target_currency[0]
                    ];

                    $markups_client_currency = [
                        'type' => 'fixed',
                        'amount' => $fixed_client_currency[0]
                    ];


                    foreach($target_containers as $code => $cost){
                        $containers_with_markups[$code] = $cost + $fixed_target_currency[0];
                    }
                    
                    foreach($target_totals as $code => $cost){
                        $totals_with_markups[$code] = $cost + $fixed_client_currency[0];
                    }

                }elseif($percent != 0){
                    $percent_target_currency = $this->calculatePercentage($percent,$target_containers);
                    $percent_client_currency = $this->calculatePercentage($percent,$target_totals);

                    $markups_array = [
                        'type' => 'percentage',
                        'amount' => $percent_target_currency
                    ];

                    $markups_client_currency = [
                        'type' => 'percent',
                        'amount' => $percent_client_currency
                    ];

                    foreach($target_containers as $code => $cost){
                        $containers_with_markups[$code] = $cost + $percent_target_currency[$code];
                    }

                    foreach($target_totals as $code => $cost){
                        $totals_with_markups[$code] = $cost + $percent_client_currency[$code];
                    }
                }
                
                $target_element->setAttribute('container_markups',$markups_array);
                $target_element->setAttribute('totals_markups',$markups_client_currency);
                $target_element->setAttribute('containers_with_markups',$containers_with_markups);
                $target_element->setAttribute('totals_with_markups',$totals_with_markups);

            }  
        }
    }

    //appending charges to corresponding Rates
    public function addToRates($rates, $target, $target_type, $company_user_id)
    {
        //Setting customer currency to convert if necessary
        $company_user = CompanyUser::where('id',$company_user_id)->first();
        $client_currency = $company_user->currency;

        if($target_type = 'charges'){
            //Looping through rates
            foreach($rates as $rate){
                //Empty array for each Rate Charges
                $rate_charges = [];
                //Looping through charges type (Origin, Destination, Freight)
                foreach($target as $direction => $charge_direction){
                    $rate_charges[$direction] = [];
                    //Looping through charges by type
                    foreach($charge_direction as $charge){
                        //Checking type of charge to set control variables
                        if(is_a($charge,'App\LocalCharge')){
                            $charge_origin_port = $charge->localcharports[0]->port_orig;
                            $charge_destination_port = $charge->localcharports[0]->port_dest;
                            $charge_carrier = $charge->localcharcarriers[0]->carrier_id;
                        }elseif(is_a($charge,'App\GlobalCharge')){
                            $charge_origin_port = $charge->globalcharport[0]->port_orig;
                            $charge_destination_port = $charge->globalcharport[0]->port_dest;
                            $charge_carrier = $charge->globalcharcarrier[0]->carrier_id;
                        }
                        //Checking if charge control variables match rate properties. "ALL" ports and carriers included (ids 1485 for ports, 26 for carriers)
                        if((in_array($rate->origin_port, [$charge_origin_port, 1485]) || $charge_destination_port == 1485) && 
                            (in_array($rate->destiny_port, [$charge_destination_port, 1485]) || $charge_destination_port == 1485) &&
                            (in_array($rate->carrier_id, [$charge_carrier, 26]) || $charge_carrier == 26)){

                                if(isset($rate->totals_with_markups) && isset($charge->totals_with_markups)){
                                    $to_update = 'totals_with_markups';
                                    $totals_array = $rate->totals_with_markups;
                                    $charges_to_add = $charge->totals_with_markups;
                                }elseif(!isset($rate->totals_with_markups) && isset($charge->totals_with_markups)){
                                    $to_update = 'totals';
                                    $totals_array = $rate->totals;
                                    $charges_to_add = $charge->totals_with_markups;
                                }elseif(isset($rate->totals_with_markups) && !isset($charge->totals_with_markups)){
                                    $to_update = 'totals_with_markups';
                                    $totals_array = $rate->totals_with_markups;
                                    $charges_to_add = $charge->containers_client_currency;
                                }elseif(!isset($rate->totals_with_markups) && !isset($charge->totals_with_markups)){
                                    $to_update = 'totals';
                                    $totals_array = $rate->totals;
                                    $charges_to_add = $charge->containers_client_currency;
                                }

                                foreach($totals_array as $code => $total){
                                    if(isset($charge->containers_client_currency[$code])){
                                        $totals_array[$code] += $charges_to_add[$code];
                                    }
                                }

                                $rate->$to_update = $totals_array;
                                array_push($rate_charges[$direction], $charge);

                                if($direction == 'freight'){
                                    $charge->containers = $this->convertToCurrency($charge->currency, $rate->currency, $charge->containers);

                                    $ocean_freight_array = [
                                        'surcharge' => ['name' => 'Ocean Freight'],
                                        'containers' => json_decode($rate->containers,true),
                                        'calculationtype' => ['name' => 'Per Container'],
                                        'currency' => ['alphacode' => $client_currency->alphacode]
                                    ];
                                    
                                    $ocean_freight_collection = collect($ocean_freight_array);
            
                                    array_push($rate_charges[$direction], $ocean_freight_collection);
                                }
                            }
                    }
                    
                    if(count($rate_charges[$direction]) == 0){
                        unset($rate_charges[$direction]);
                    };
                }
                $rate->setAttribute('charges',$rate_charges);
            }
        }
    }
    
}
