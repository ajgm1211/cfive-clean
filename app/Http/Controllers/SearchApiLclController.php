<?php

namespace App\Http\Controllers;

use App\Http\Traits\SearchTrait;
use App\Http\Traits\QuoteV2Trait;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SearchApiLclResource;
use App\Http\Resources\RateLclResource;
use App\SearchRate;
use App\SearchPort;
use App\SearchCarrier;
use App\CompanyUser;
use App\Carrier;
use App\RateLcl;
use App\LocalChargeLcl;
use App\GlobalChargeLcl;
use App\ApiProvider;
use Illuminate\Http\Request;
use GeneaLabs\LaravelMixpanel\LaravelMixpanel;
use App\Http\Traits\MixPanelTrait;

class SearchApiLclController extends Controller
{
    use SearchTrait, QuoteV2Trait, MixPanelTrait;

    public function list(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;
        //Filtering and pagination
        $results = SearchRate::where([['company_user_id', $company_user_id],['type','LCL']])->orderBy('id', 'desc')->take(4)->get();

        //Grouping as collection to be managed by Vue
        return SearchApiLclResource::collection($results);
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
            'deliveryType.id' => 'required',
            'direction' => 'required',
            'carriers' => 'sometimes',
            'carriersApi' => 'sometimes',
            'type' => 'required',
            'company' => 'sometimes',
            'contact' => 'sometimes',
            'pricelevel' => 'sometimes',
            'originCharges' => 'sometimes',
            'destinationCharges' => 'sometimes',
            'originAddress' => 'sometimes',
            'destinationAddress' => 'sometimes',
            'lclTypeIndex' => 'sometimes',
            //LCL
            'volume' => 'sometimes|required_if:type,LCL',
            'weight' => 'sometimes|required_if:type,LCL',
            'quantity' => 'sometimes|required_if:type,LCL',
            'chargeableWeight' => 'sometimes|required_if:type,LCL',
            //by Total Shipment
            'cargoType' => 'sometimes|required_if:lclTypeIndex,0',
            //by Packaging
            'packaging' => 'sometimes|required_if:lclTypeIndex,1|array',
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

        //Building equipment array
        $equipment = [ 
            'type' => $new_search_data['lclTypeIndex'],
            'cargo_type' => $new_search_data['cargoType'],
            'packaging' => $new_search_data['packaging'],
            'volume' => $new_search_data['volume'],
            'weight' => $new_search_data['weight'],
            'quantity' => $new_search_data['quantity'],
            'chargeable_weight' => $new_search_data['chargeableWeight'],
        ];
        
        //SEARCH TRAIT - Getting new array that contains only ids, for queries
        $new_search_data_ids = $this->getIdsFromArray($new_search_data);
        //Formatting date
        $pick_up_date = $new_search_data_ids['dateRange']['startDate'] . ' / ' . $new_search_data_ids['dateRange']['endDate'];

        $new_search = SearchRate::create([
            'company_user_id' => $new_search_data_ids['company_user'],
            'pick_up_date' => $pick_up_date,
            'equipment' => json_encode($equipment),
            'delivery' => $new_search_data_ids['deliveryType'],
            'direction' => $new_search_data_ids['direction'],
            'type' => $new_search_data_ids['type'],
            'user_id' => $new_search_data_ids['user'],
            'contact_id' => $new_search_data_ids['contact'],
            'company_id' => $new_search_data_ids['company'],
            'price_level_id' => $new_search_data_ids['pricelevel'],
            'origin_charges' => $new_search_data_ids['originCharges'],
            'destination_charges' => $new_search_data_ids['destinationCharges'],
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

        if(isset($new_search_data_ids['carriers'])){
            foreach ($new_search_data_ids['carriers'] as $carrier_id) {
                $carrier = Carrier::where('id',$carrier_id)->first();
                
                $search_carrier = new SearchCarrier();

                $search_carrier->search_rate_id = $new_search->id;
                
                $search_carrier->provider()->associate($carrier)->save();
            }
        }

        if(isset($new_search_data_ids['carriersApi'])){
            foreach ($new_search_data_ids['carriersApi'] as $provider_id) {
                $provider = ApiProvider::where('id',$provider_id)->first();
                
                $search_carrier = new SearchCarrier();
    
                $search_carrier->search_rate_id = $new_search->id;
                
                $search_carrier->provider()->associate($provider)->save();
            }
        }

        return new SearchApiLclResource($new_search);
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

        $search_array['dateRange']['startDate'] = $this->formatSearchDate($search_array['dateRange']['startDate'],'date');
        $search_array['dateRange']['endDate'] = $this->formatSearchDate($search_array['dateRange']['endDate'],'date');
        
        $search_ids = $this->getIdsFromArray($search_array);
        $search_ids['company_user'] = $company_user_id;
        $search_ids['user'] = $user_id;
        $search_ids['client_currency'] = $company_user->currency;

        //Retrieving rates with search data
        $rates = $this->searchRates($search_ids);

        foreach ($rates as $rate) {
            //$rateNo += 1;
            //dump($rate->contract);
            //dump('for rate '. strval($rateNo));

            //SEARCH TRAIT - Sets totals and totals in client currency
            $this->setLclRateTotals($rate, $search_ids);

            //Retrieving local charges with search data
            $local_charges = $this->searchLocalCharges($search_ids, $rate);

            //Retrieving global charges with search data
            $global_charges = $this->searchGlobalCharges($search_ids, $rate);

            //SEARCH TRAIT - Grouping charges by type (Origin, Destination, Freight)
            $charges = $this->groupChargesByType($local_charges, $global_charges, $search_ids);

            //SEARCH TRAIT - Calculates charges appends the cost array to each charge instance
            $this->calculateLclChargesPerType($charges, $search_ids);

            //SEARCH TRAIT - Join charges (within group) if Surcharge, Carrier, Port and Typedestiny match
            $charges = $this->joinCharges($charges, $search_ids);

            $this->checkLclAdaptable($charges);

            $this->checkLclRoundable($charges);

            //Appending Rate Id to Charges
            $this->addChargesToRate($rate, $charges, $search_ids);

            //Getting price levels if requested
            if ($search_array['pricelevel']) {
                $price_level_markups = $this->searchPriceLevels($search_ids);
            } else {
                $price_level_markups = [];
            }

            $remarks = $this->searchRemarks($rate, $search_ids);

            //ATTRIBUTES AT THE END

            $rate->setAttribute('remarks', $remarks);
        }
        
        dd($charges);

        $after_rates = true;
        
        if(!$after_rates){
            //$rateNo = 0;
            foreach ($rates as $rate) {               
                //Adding price levels
                if ($price_level_markups != null && count($price_level_markups) != 0) {
                    $this->addMarkups($price_level_markups, $rate, $search_ids['client_currency']);
                    foreach ($rate->charges as $charge_direction) {
                        foreach ($charge_direction as $charge) {
                            $this->addMarkups($price_level_markups, $charge, $search_ids['client_currency']);
                        }
                    }
                }
                
                $this->calculateTotals($rate,$search_ids['client_currency']);

                $transit_time = $this->searchTransitTime($rate);

                $rate->setAttribute('transit_time', $transit_time);

                $rate->setAttribute('request_type', $request->input('requested'));

                $this->stringifyRateAmounts($rate);

                $this->setDownloadParameters($rate);
            }

            if ($rates != null && count($rates) != 0) {
                //Ordering rates by totals (cheaper to most expensive)
                $rates = $this->sortRates($rates, $search_ids);

                $terms = $this->searchTerms($search_ids);

                $search_array['terms'] = $terms;

                $rates[0]->SetAttribute('search', $search_array);
            }

            $track_array = [];
            $track_array['company_user'] = $company_user;
            $track_array['data'] = $search_array;

            /** Tracking search event with Mix Panel*/
            $this->trackEvents("search_fcl", $track_array);
        }

        return RateLclResource::collection($rates);
    }

    public function searchRates($search_data)
    {
        //setting variables for query
        $company_user_id = $search_data['company_user'];
        $company_user = CompanyUser::where('id', $search_data['company_user'])->first();

        $user_id = $search_data['user'];
        $origin_ports = $search_data['originPorts'];
        $destiny_ports = $search_data['destinationPorts'];
        $carriers = $search_data['carriers'];
        $dateSince = $search_data['dateRange']['startDate'];
        $dateUntil = $search_data['dateRange']['endDate'];
        $company_id = $search_data['company'];

        //Querying rates database
        $rates_array = RateLcl::whereIn('origin_port', $origin_ports)->whereIn('destiny_port', $destiny_ports)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
            $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                $a->where('user_id', '=', $user_id);
            })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
            $q->whereHas('contract_company_restriction', function ($b) use ($company_id) {
                $b->where('company_id', '=', $company_id);
            })->orDoesntHave('contract_company_restriction');
        })->whereHas('contract', function ($q) use ($company_user_id, $dateSince, $dateUntil, $company_user) {
            if ($company_user->future_dates == 1) {
                $q->where(function ($query) use ($dateSince) {
                    $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                })->where('company_user_id', '=', $company_user_id);
            } else {
                $q->where(function ($query) use ($dateSince, $dateUntil) {
                    $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                })->where('company_user_id', '=', $company_user_id);
            }
        })->get();

        //Setting attribute to totalize adding charges, inlands, markups, etc. Totals are shown in the client default currency
        foreach ($rates_array as $rate) {
            //Converting rates to client currency
            $client_currency = $search_data['client_currency'];
            //FOR CALCULATIONS
            //$containers_client_currency = $this->convertToCurrency($rate->currency, $client_currency, json_decode($rate->containers, true));
            //$rate->setAttribute('totals', $containers_client_currency);
            //$rate->setAttribute('client_currency', $client_currency);
        }

        return $rates_array;
    }

    //Finds local charges matching contracts
    public function searchLocalCharges($search_ids, $rate)
    {
        //Pulling necessary data from the search IDs array
        $origin_ports = [$rate->origin_port,1485];
        $destination_ports = [$rate->destiny_port,1485];
        $origin_countries = [$rate->port_origin->country()->first()->id, 250];
        $destination_countries = [$rate->port_destiny->country()->first()->id, 250];

        //creating carriers array with only rates carrier
        $carriers = [$rate->carrier->id, 26];

        $local_charges = LocalChargeLcl::where('contractlcl_id', '=', $rate->contractlcl_id)->whereHas('localcharcarrierslcl', function ($q) use ($carriers) {
            $q->whereIn('carrier_id', $carriers);
        })->where(function ($query) use ($origin_ports, $destination_ports, $origin_countries, $destination_countries) {
            $query->whereHas('localcharportslcl', function ($q) use ($origin_ports, $destination_ports) {
                $q->whereIn('port_orig', $origin_ports)->whereIn('port_dest', $destination_ports);
            })->orwhereHas('localcharcountrieslcl', function ($q) use ($origin_countries, $destination_countries) {
                $q->whereIn('country_orig', $origin_countries)->whereIn('country_dest', $destination_countries);
            });
        })->with('localcharportslcl.portOrig', 'localcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm', 'calculationtypelcl', 'surcharge')->get();

        return $local_charges;
    }

    //Finds global charges matching search data
    public function searchGlobalCharges($search_ids, $rate)
    {
        //building Carriers array from rates
        $carriers = [$rate->carrier->id, 26];
        //Pulling necessary data from the search IDs array
        $validity_start = $search_ids['dateRange']['startDate'];
        $validity_end = $search_ids['dateRange']['endDate'];
        $origin_ports = [$rate->origin_port,1485];
        $destination_ports = [$rate->destiny_port,1485];
        $origin_countries = [$rate->port_origin->country()->first()->id, 250];
        $destination_countries = [$rate->port_destiny->country()->first()->id, 250];
        $company_user_id = $search_ids['company_user'];
        
        $global_charges = GlobalChargeLcl::where('validity', '<=', $validity_start)->where('expire', '>=', $validity_end)->whereHas('globalcharcarrierslcl', function ($q) use ($carriers) {
            $q->whereIn('carrier_id', $carriers);
        })->where(function ($query) use ($origin_ports, $destination_ports, $origin_countries, $destination_countries) {
            $query->whereHas('globalcharportlcl', function ($q) use ($origin_ports, $destination_ports) {
                $q->whereIn('port_orig', $origin_ports)->whereIn('port_dest', $destination_ports);
            })->orwhereHas('globalcharcountrylcl', function ($q) use ($origin_countries, $destination_countries) {
                $q->whereIn('country_orig', $origin_countries)->whereIn('country_dest', $destination_countries);
            });
        })->where('company_user_id', '=', $company_user_id)->with('globalcharportlcl.portOrig', 'globalcharportlcl.portDest', 'globalcharcarrierslcl.carrier', 'currency', 'calculationtypelcl', 'surcharge')->get();

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
}