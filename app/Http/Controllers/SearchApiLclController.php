<?php

namespace App\Http\Controllers;

use App\Http\Traits\SearchTrait;
use App\Http\Traits\QuoteV2Trait;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SearchApiLclResource;
use App\Http\Resources\RateResource;
use App\SearchRate;
use App\SearchPort;
use App\SearchCarrier;
use App\CompanyUser;
use App\Carrier;
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
            //by Total Shipment
            'lclShipmentQuantity' => 'sometimes|required_if:lclTypeIndex,0',
            'lclShipmentVolume' => 'sometimes|required_if:lclTypeIndex,0',
            'lclShipmentWeight' => 'sometimes|required_if:lclTypeIndex,0',
            'lclShipmentCargoType' => 'sometimes|required_if:lclTypeIndex,0',
            'chargeableWeight' => 'sometimes|required_if:lclTypeIndex,0',
            //by Packaging
            'lclPackaging' => 'sometimes|required_if:lclTypeIndex,1|array',
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

        //Formatting equipment
        if($new_search_data['lclTypeIndex'] == 0){
            $equipment = [ 
                'type' => 'shipment',
                'shipment' => [
                    'cargo_type' => $new_search_data['lclShipmentCargoType'],
                    'volume' => $new_search_data['lclShipmentVolume'],
                    'weight' => $new_search_data['lclShipmentWeight'],
                    'quantity' => $new_search_data['lclShipmentQuantity'],
                    'chargeable_weight' => $new_search_data['chargeableWeight'],
                ],
            ];
        }elseif($new_search_data['lclTypeIndex'] == 1){
            $equipment = [
                'type' => 'packaging',
                'packages' => $new_search_data['lclPackaging'],
            ];
        } 

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

        $search_array['dateRange']['startDate'] = substr($search_array['dateRange']['startDate'], 0, 10);
        $search_array['dateRange']['endDate'] = substr($search_array['dateRange']['endDate'], 0, 10);

        $search_ids = $this->getIdsFromArray($search_array);
        $search_ids['company_user'] = $company_user_id;
        $search_ids['user'] = $user_id;
        $search_ids['client_currency'] = $company_user->currency;

        //Retrieving rates with search data
        $rates = $this->searchRates($search_ids);

        $after_rates = true;
        
        if(!$after_rates){
            //$rateNo = 0;
            foreach ($rates as $rate) {
                //$rateNo += 1;
                //dump($rate->contract);
                //dump('for rate '. strval($rateNo));
                //Retrieving local charges with search data
                $local_charges = $this->searchLocalCharges($search_ids, $rate);

                //Retrieving global charges with search data
                $global_charges = $this->searchGlobalCharges($search_ids, $rate);

                //SEARCH TRAIT - Grouping charges by type (Origin, Destination, Freight)
                $charges = $this->groupChargesByType($local_charges, $global_charges, $search_ids);

                //SEARCH TRAIT - Calculates charges by container and appends the cost array to each charge instance
                $this->setChargesPerContainer($charges, $search_array['containers'], $rate->containers, $search_ids['client_currency']);

                //Getting price levels if requested
                if (array_key_exists('pricelevel', $search_array) && $search_array['pricelevel'] != null) {
                    $price_level_markups = $this->searchPriceLevels($search_ids);
                } else {
                    $price_level_markups = [];
                }

                //SEARCH TRAIT - Join charges (within group) if Surcharge, Carrier, Port and Typedestiny match
                $charges = $this->joinCharges($charges, $search_ids['client_currency'], $search_ids['selectedContainerGroup']);

                //Appending Rate Id to Charges
                $this->addChargesToRate($rate, $charges, $search_ids['client_currency']);
                
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

                $remarks = $this->searchRemarks($rate, $search_ids);

                $transit_time = $this->searchTransitTime($rate);

                $rate->setAttribute('transit_time', $transit_time);

                $rate->setAttribute('remarks', $remarks);

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

        return RateResource::collection($rates);
    }
}
