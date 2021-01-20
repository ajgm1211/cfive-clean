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
use App\Container;
use App\GroupContainer;
use App\ScheduleType;
use App\Country;
use App\Rate;
use App\Contract;
use App\Price;
use App\CompanyUser;

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
            'terms_and_conditions'
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
                
        //SEARCH TRAIT - Getting new array that contains only ids, for queries
        $new_search_data_ids = $this->getIdsFromSearchRequest($new_search_data);
        
        //Storing new search with request data
        //$this->store($new_search_data_ids);
        
        //Retrieving rates with search data
        $rates = $this->searchRates($new_search_data_ids);

        //Retrieving local charges with search data
        //$local_charges = $this->searchLocalCharges($new_search_data_ids, $rates);

        //Retrieving and calculating inlands from search data, only if door delivery indicated
        if(array_key_exists('deliveryType',$new_search_data) && in_array($new_search_data_ids['deliveryType'],[2,3,4])){
            $inlands = $this->searchInlands($new_search_data_ids);
        }else{
            $inlands = [];
        }

        //Getting price levels if requested
        if(array_key_exists('pricelevel',$new_search_data) && $new_search_data['pricelevel'] != null){
            $price_level_markups = $this->searchPriceLevels($new_search_data_ids);
        }else{
            $price_level_markups = [];
        }

        if(count($price_level_markups) != 0){
            $this->addMarkups($price_level_markups,$rates);
        }

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
    public function searchRates($data)
    {
        //Setting current company and user
        $user = \Auth::user();
        $user_id = $user->id;
        $company_user_id = $user->company_user_id;
        $company_user = CompanyUser::where('id',$company_user_id)->first();
        
        //setting variables for query
        $container_group = $data['selectedContainerGroup'];
        $company_id = $data['company'];
        $origin_ports = $data['originPorts'];
        $destiny_ports = $data['destinationPorts'];
        $arregloCarrier = $data['carriers'];
        $dateSince = $data['dateRange']['validity_start'];
        $dateUntil = $data['dateRange']['validity_end'];
    
        //Querying rates database
        if ($company_id != null || $company_id != 0) {
            $rates_query = Rate::whereIn('origin_port', $origin_ports)->whereIn('destiny_port', $destiny_ports)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract', 'carrier','curency')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $company_id) {
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
                        $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
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

        $rates_query = $this->filtrarRate($rates_query, $data['containers'], null, $all_containers);

        //Applying quey and returning models
        $rates_array = $rates_query->get();

        //If the container rates come separate (twuenty,forty,etc) joins them under the "containers" field for iteration purposes
        $this->joinRateContainers($rates_array);

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
    public function searchLocalCharges($search_data,$rates_data)
    {
        if(count($rates_data) != 0){
            foreach($rates_data as $rate){
                dump($rate->contract);
            }
            
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
        } else {
            $local_charges = [];
        }
        
    }
    
    //Retrieves and cleans markups from price levels
    public function searchPriceLevels($request_ids)
    {
        //Setting current company and user
        $user = \Auth::user();
        $user_id = $user->id;
        $company_user_id = $user->company_user_id;
        $company_user = CompanyUser::where('id',$company_user_id)->first();
    
        //getting client profile currency
        $client_currency = $company_user->currency;
    
        //SEARCH TRAIT - Markups are organized in a collection containing
            //Freight markups (fixed & percent)
            //Local Charge markups (fixed & percent)
            //Inland markups (fixed & percent)
        $markups = $this->getMarkupsFromPriceLevels($request_ids['pricelevel'], $client_currency, $request_ids['direction'], $request_ids['type']);
    
        return $markups;
    }

    public function storeExpressContract(Request $request)
    {
        
    }
    
    //Adds PriceLevels markups to target collection
    public function addMarkups($markups,$target_array)
    {
        //dd($markups['freight']);

        if(count($target_array) != 0){
            foreach($target_array as $target_element){

                if(is_a($target_element, 'App\Rate')){
                    $markups_to_add = $markups['freight'];
                    $fixed = $markups_to_add['freight_amount'];
                    $percent = $markups_to_add['freight_percentage'];
                    $totals_with_markups = [];
                    if($fixed != 0){
                        $fixed_target = json_decode($target_element->containers,true);
                        $fixed = $this->convertToCurrency($markups_to_add['freight_currency'],$target_element->currency,Array($fixed));

                        $final_array = [
                            'type' => 'fixed',
                            'amount' => $fixed[0]
                        ];

                        foreach($fixed_target as $code => $cost){
                            $totals_with_markups[$code] = $cost + $fixed[0];
                        }

                    }elseif($percent != 0){
                        $percent_target = json_decode($target_element->containers,true);
                        $percent = $this->calculatePercentage($percent,$percent_target);

                        $final_array = [
                            'type' => 'percentage',
                            'amount' => $percent
                        ];

                        foreach($percent_target as $code => $cost){
                            $totals_with_markups[$code] = $cost + $percent[$code];
                        }
                    }
                    $target_element->setAttribute('markups',$final_array);
                    $target_element->setAttribute('totals',$totals_with_markups);
                }
            }  
        }
    }

}
