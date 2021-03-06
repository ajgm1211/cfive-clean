<?php

namespace App\Http\Controllers;

use App\AutomaticInlandTotal;
use App\AutomaticRate;
use App\AutomaticRateTotal;
use App\CalculationType;
use App\CalculationTypeLcl;
use App\CargoKind;
use App\CargoType;
use App\Carrier;
use App\Charge;
use App\ChargeLclAir;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Container;
use App\Country;
use App\Currency;
use App\Delegation;
use App\DeliveryType;
use App\DestinationType;
use App\Harbor;
use App\Http\Resources\CostSheetResource;
use App\Http\Resources\QuotationListResource;
use App\Http\Resources\QuotationResource;
use App\Http\Traits\MixPanelTrait;
use App\Http\Traits\QuoteV2Trait;
use App\Http\Traits\SearchTrait;
use App\Incoterm;
use App\Language;
use App\LocalChargeQuote;
use App\PaymentCondition;
use App\Provider;
use App\QuoteV2;
use App\SaleTermCode;
use App\ScheduleType;
use App\StatusQuote;
use App\Surcharge;
use App\TermAndConditionV2;
use App\User;
use App\UserDelegation;
use App\ViewQuoteV2;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    use QuoteV2Trait, SearchTrait, MixPanelTrait;

    public function index(Request $request)
    {
        return view('quote.index');
    }

    function list(Request $request) {

        $user = auth()->user();

        $query = $this->getFilterByUserType($user);
        $this->getFilterBySearch($query, $request['q']);
        $this->getFilterByRequestParams($query, $request['params']);

        $results = $query->orderByDesc('id')->paginate(10); 
    
        return QuotationListResource::collection($results);
    }

    public function getFilterByUserType($user) {

        $filter_delegation = $user->companyUser->options['filter_delegations'];
        $subtype = $user->options['subtype'];

        //Filtro por permisos a nivel de usuario y compañía
        if ($subtype === 'comercial') {
            $query = ViewQuoteV2::filterByCurrentUser();
        }
        if ($filter_delegation == true) {
            $query = ViewQuoteV2::filterByDelegation();
        } else {
            $query = ViewQuoteV2::filterByCurrentCompany();
        }

        return $query;
    }

    public function getFilterBySearch($query, $qry) {            

        if (isset($qry)) {    

            return $query->where(function ($q) use ($qry) {                    
                    $filter_by = ['id', 'quote_id', 'origin_port', 'destination_port', 'business_name', 'type', 'owner', 'created_at'];        
                    foreach ($filter_by as $column) {
                        $q->orWhere('view_quote_v2s.'.$column, 'LIKE', '%' . $qry . '%');
                    }
            });

        } 

    }

    public function getFilterByRequestParams($query, $params)
    {
        $params = json_decode($params, true);
        $attributes = ['id', 'quote_id', 'custom_quote_id', 'status', 'company_id', 'type', 'user_id',];
        
        foreach ($attributes as $attr) {
            if (isset($params[$attr]) && count($params[$attr])) {
                $query->whereIn($attr, $params[$attr]);
            }
        }   

        $this->getFilterByCreatedAt($query, $params); 
        $this->getFilterByJoinConditions($query, $params); 
    }

    public function getFilterByCreatedAt($query, $params)
    {   
        $query->where(function ($query) use ($params) {        
            if (isset($params['created_at'])) {    
                foreach($params['created_at'] as $date){
                    $query->orWhere('view_quote_v2s.created_at', 'LIKE', '%' . $date . '%');
                }
            }
        });

        return $query;
    }

    public function getFilterByJoinConditions($query, $params)
    {
        if (isset($params['origin']) && count($params['origin']) && isset($params['destiny']) && count($params['destiny'])) { 
            return $query->select('view_quote_v2s.*')
                ->join('automatic_rates', function($join){
                    $join->on('automatic_rates.quote_id', '=', 'view_quote_v2s.id');
                    $join->where('automatic_rates.deleted_at', '=', null);
                })
                ->whereIn('automatic_rates.origin_port_id', $params['origin'])
                ->whereIn('automatic_rates.destination_port_id', $params['destiny'])
                ->groupBy('view_quote_v2s.id');
        }

        if (isset($params['origin']) && count($params['origin'])) { 
            $query->select('view_quote_v2s.*')
                ->join('automatic_rates', function($join){
                    $join->on('automatic_rates.quote_id', '=', 'view_quote_v2s.id');
                    $join->where('automatic_rates.deleted_at', '=', null);
                })
                ->whereIn('automatic_rates.origin_port_id', $params['origin'])
                ->groupBy('view_quote_v2s.id');
        }

        if (isset($params['destiny']) && count($params['destiny'])) { 
            $query->select('view_quote_v2s.*')
                ->join('automatic_rates', function($join){
                    $join->on('automatic_rates.quote_id', '=', 'view_quote_v2s.id');
                    $join->where('automatic_rates.deleted_at', '=', null);
                })
                ->whereIn('automatic_rates.destination_port_id', $params['destiny'])
                ->groupBy('view_quote_v2s.id');
        }
        
        return $query;
    }

    /**
     * Aplicación cliente hace request a este método
     * para obtener los datos necesarios para gestionar un quote
     */
    public function data(Request $request)
    {   
        return $this->getData();
    }

    public function getData() 
    {   
        $company_user_id = Auth::user()->company_user_id;

        $carriers = cache()->rememberForever('data_carriers', function() {
            return Carrier::get()->map(function ($carrier) {
                $carrier['model'] = 'App\Carrier';
                return $carrier->only(['id', 'name', 'image', 'model']);
            });
        }); 

        $companies = Company::where('company_user_id', '=', $company_user_id)->get()->map(function ($company) {
            return $company->only(['id', 'business_name', 'pdf_language']);
        });

        $full_contacts = cache()->rememberForever('data_full_contacts', function() {
            return Contact::get()->map(function ($contact) {
                return $contact->only(['id', 'first_name', 'last_name', 'company_id']);
            });
        }); 

        $contacts = [];
        $languages = [];
        
        foreach ($companies as $company) {
            array_push($languages, ['company_id' => $company['id'], 'name' => $company['pdf_language']]);

            foreach ($full_contacts as $contact) {
                if ($contact['company_id'] == $company['id']) {
                    array_push($contacts, ['id' => $contact['id'], 'company_id' => $contact['company_id'], 'name' => $contact['first_name'] . " " . $contact['last_name']]);
                }
            }
        };

        $incoterms = cache()->rememberForever('data_incoterms', function() {
            return Incoterm::select(['id', 'name'])->get();
        });

        $users = User::whereHas('companyUser', function ($q) use ($company_user_id) {
            $q->where('company_user_id', '=', $company_user_id);
        })->get()->map(function ($user) {
            return $user->only(['id', 'name', 'lastname', 'fullname']);
        });

        $harbors = cache()->rememberForever('data_harbors', function() {
            return Harbor::select(['id', 'display_name', 'country_id', 'code'])->get();
        });

        $payment_conditions = cache()->rememberForever('data_payment_conditions', function() {
            return PaymentCondition::select(['id', 'quote_id', 'content'])->get();
        });

        $terms_and_conditions = cache()->rememberForever('data_terms_and_conditions', function() {
            return TermAndConditionV2::select(['id', 'name', 'user_id', 'type', 'company_user_id'])->get();
        });

        $delivery_types = cache()->rememberForever('data_delivery_types', function() {
            return DeliveryType::select(['id', 'name'])->get();
        }); 

        $status_options = cache()->rememberForever('data_status_options', function() {
            return StatusQuote::select(['id', 'name'])->get();
        }); 

        $kind_of_cargo = cache()->rememberForever('data_kind_of_cargo', function() {
            return CargoKind::select(['id', 'name'])->get();
        }); 

        $languages = cache()->rememberForever('data_languages', function() {
            return Language::select(['id', 'name'])->get();
        });

        $currency = cache()->rememberForever('data_currency', function() {
            return Currency::select(['id', 'alphacode', 'rates', 'rates_eur'])->get();
        });

        $filtered_currencies = Currency::whereIn('id', ['46', '149'])->select(['id', 'alphacode', 'rates', 'rates_eur'])->get();

        $containers = cache()->rememberForever('data_containers', function() {
            return Container::get();
        });

        $calculationtypes = cache()->rememberForever('data_calculationtypes', function() {
            return CalculationType::select(['id', 'name'])->get();
        });

        $surcharges = Surcharge::where('company_user_id', '=', $company_user_id)->select(['id', 'name'])->get();

        $schedule_types = cache()->rememberForever('data_schedule_types', function() {
            return ScheduleType::select(['id', 'name'])->get();
        });

        $countries = cache()->rememberForever('data_countries', function() {
            return Country::select(['id', 'code', 'name'])->get();
        });

        $sale_codes = SaleTermCode::where('company_user_id', '=', $company_user_id)->select(['id', 'name'])->get();

        $providers = Provider::where('company_user_id', $company_user_id)->get()->map(function ($provider) {
            $provider['model'] = 'App\Provider';
            return $provider->only(['id', 'name', 'model']);
        });

        $cargo_types = cache()->rememberForever('data_cargo_types', function() {
            return CargoType::select(['id', 'name'])->get();
        });

        $calculationtypeslcl = cache()->rememberForever('data_calculationtypeslcl', function() {
            return CalculationTypeLcl::select(['id', 'name'])->get();
        });

        $destination_types = cache()->rememberForever('data_destination_types', function() {
            return DestinationType::select(['id', 'name'])->get();
        });

        $carrier_providers = $this->providers($carriers);

        $data = compact(
            'companies',
            'contacts',
            'carriers',
            'carrier_providers',
            'containers',
            'incoterms',
            'users',
            'harbors',
            'payment_conditions',
            'terms_and_conditions',
            'delivery_types',
            'status_options',
            'kind_of_cargo',
            'currency',
            'calculationtypes',
            'surcharges',
            'schedule_types',
            'countries',
            'languages',
            'sale_codes',
            'providers',
            'cargo_types',
            'calculationtypeslcl',
            'filtered_currencies',
            'destination_types'
        );

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $user = \Auth::user('web');
        $company_user = $user->worksAt();
        $formatted_code = replaceSpecialCharacter($company_user->name);
        $company_code = strtoupper(substr($formatted_code, 0, 2));
        $higherq_id = $company_user->getHigherId($company_code);
        $newq_id = $company_code . '-' . strval($higherq_id + 1);

        $data = $request->input();

        $rate_data = $data['rates'];

        $result_data = $data['results'];

        if (count($rate_data) != 0) {
            $search_data = $rate_data[0]['search'];
        } elseif (count($result_data) != 0) {
            $search_data = $result_data[0]['search'];
            //Setting terms & conditions when is from API
            $search_data['terms'] = $this->searchTerms($search_data);
        }

        $search_data_ids = $this->getIdsFromArray($search_data);

        if ($search_data_ids['type'] == 'FCL') {
            $equipment = "[\"" . implode("\",\"", $search_data_ids['containers']) . "\"]";
        } else {
            $equipment = "[]";
        }

        $remarks = "";

        foreach ($rate_data as $rate) {
            $remarks .= $rate['client_remarks'];
        }
        foreach ($result_data as $result) {
            if (isset($result['remarks'])) {
                $remarks .= $result['remarks'];
                $remarksPenalties = isset($result['formattedPenalties']) ? $this->formatPenaltyRemark($result['formattedPenalties'], $result['company'], $result['search']['containers']) : '';
                $remarks .= $remarksPenalties;
            }
        }
        // Validacion para el quote_id no sean iguales
        $validation_same_quote = QuoteV2::where('quote_id', $newq_id)->first();
        if (!empty($validation_same_quote)) {
            $newq_id = $company_code . '-' . strval($higherq_id + 2);
        }
        
        if(isset($search_data['company']['pdf_language'])){
            if(is_int($search_data['company']['pdf_language'])){
                $language_id = ($search_data['company']['pdf_language'] == 0 || $search_data['company']['pdf_language'] == null) ? 1 : $search_data['company']['pdf_language'];
            }else{
                $language = Language::where('name', $search_data['company']['pdf_language'])->first();
                if(!isset($language)){
                    $language_id = ($search_data['company']['pdf_language'] == "0" || $search_data['company']['pdf_language'] == null) ? 1 : (int)$search_data['company']['pdf_language'];    
                }else{
                    $language_id = $language->id;
                }
            }
        }else{
            $language_id = ($company_user->pdf_language == 0 || $company_user->pdf_language == null) ? 1 : $company_user->pdf_language;
        }

        $quote = QuoteV2::create([
            'quote_id' => $newq_id,
            'type' => $search_data_ids['type'],
            'delivery_type' => $search_data_ids['deliveryType'],
            'user_id' => $user->id,
            'direction_id' => $search_data_ids['direction'],
            'company_user_id' => $company_user->id,
            'language_id' => $language_id,
            'company_id' => isset($search_data_ids['company']) ? $search_data_ids['company'] : null,
            'contact_id' => isset($search_data_ids['contact']) ? $search_data_ids['contact'] : null,
            'price_id' => isset($search_data_ids['pricelevel']) ? $search_data_ids['pricelevel'] : null,
            'equipment' => $equipment,
            'date_issued' => $search_data_ids['dateRange']['startDate'],
            'validity_start' => $search_data_ids['dateRange']['startDate'],
            'validity_end' => $search_data_ids['dateRange']['endDate'],
            'status' => 'Draft',
            'terms_portuguese' => $search_data['terms'] ? $search_data['terms']['portuguese'] : null,
            'terms_and_conditions' => $search_data['terms'] ? $search_data['terms']['spanish'] : null,
            'terms_english' => $search_data['terms'] ? $search_data['terms']['english'] : null,
            'terms_italian' => $search_data['terms'] ? $search_data['terms']['italian'] : null,
            'terms_catalan' => $search_data['terms'] ? $search_data['terms']['catalan'] : null,
            'terms_french' => $search_data['terms'] ? $search_data['terms']['french'] : null,
            'total_quantity' => $search_data['quantity'],
            'total_weight' => $search_data['weight'],
            'total_volume' => $search_data['volume'],
            'chargeable_weight' => $search_data['chargeableWeight'],
        ]);

        $quote = $quote->fresh();

        if ($quote->language_id == 1) {
            $quote->update(['remarks_english' => $remarks]);
        } else if ($quote->language_id == 2) {
            $quote->update(['remarks_spanish' => $remarks]);
        } else if ($quote->language_id == 3) {
            $quote->update(['remarks_portuguese' => $remarks]);
        } else if ($quote->language_id == 4) {
            $quote->update(['remarks_italian' => $remarks]);
        } else if ($quote->language_id == 5) {
            $quote->update(['remarks_catalan' => $remarks]);
        } else if ($quote->language_id == 6) {
            $quote->update(['remarks_french' => $remarks]);
        }

        if (isset($remarksPenalties)) {
            if (empty($quote->remarks_english)) {
                $quote->update(['remarks_english' => $remarksPenalties]);
            }

            if (empty($quote->remarks_spanish)) {
                $quote->update(['remarks_spanish' => $remarksPenalties]);
            }

            if (empty($quote->remarks_portuguese)) {
                $quote->update(['remarks_portuguese' => $remarksPenalties]);
            }

            if (empty($quote->remarks_italian)) {
                $quote->update(['remarks_italian' => $remarksPenalties]);
            }

            if (empty($quote->remarks_catalan)) {
                $quote->update(['remarks_catalan' => $remarksPenalties]);
            }

            if (empty($quote->remarks_french)) {
                $quote->update(['remarks_french' => $remarksPenalties]);
            }
        }

        foreach ($rate_data as $rate) {

            $newRate = AutomaticRate::create([
                'quote_id' => $quote->id,
                'contract' => isset($rate['contract']) ? $rate['contract']['name'] : '',
                'transit_time' => isset($rate['transit_time']) ? $rate['transit_time']['transit_time'] : null,
                'via' => isset($rate['transit_time']) ? $rate['transit_time']['via'] : null,
                'schedule_type' => isset($rate['transit_time']) ? $rate['transit_time']['service_id'] : null,
                'validity_start' => $rate['contract']['validity'],
                'validity_end' => $rate['contract']['expire'],
                'currency_id' => $rate['currency_id'],
                'origin_port_id' => $rate['origin_port'],
                'destination_port_id' => $rate['destiny_port'],
                'carrier_id' => $rate['carrier_id'],
            ]);

            foreach ($rate['charges'] as $direction => $charge_direction) {
                $rate_markups[$direction] = 0;
                foreach ($charge_direction as $charge) {
                    $currency_id = isset($charge['joint_as']) && $charge['joint_as'] == 'client_currency' ? $rate['client_currency']['id'] : $charge['currency']['id'];

                    if ($search_data_ids['type'] == 'FCL') {
                        $charge = $this->formatFclChargeForQuote($charge);
                        $ocean_surcharge = Surcharge::where([['name', 'Ocean Freight'], ['company_user_id', null]])->first();

                        $freight = Charge::create([
                            'automatic_rate_id' => $newRate->id,
                            'surcharge_id' => isset($charge['surcharge_id']) ? $charge['surcharge_id'] : $ocean_surcharge->id,
                            'type_id' => $charge['typedestiny_id'],
                            'calculation_type_id' => $charge['calculationtype']['id'],
                            'currency_id' => $currency_id,
                            'amount' => json_encode($charge['amount']),
                            'markups' => json_encode($charge['markups']),
                            'total' => json_encode($charge['total']),
                        ]);
                    } elseif ($search_data_ids['type'] == 'LCL') {
                        $charge = $this->formatLclChargeForQuote($charge);
                        $rate_markups[$direction] += $charge['markup'];

                        $freight = ChargeLclAir::create([
                            'automatic_rate_id' => $newRate->id,
                            'surcharge_id' => $charge['surcharge']['id'],
                            'type_id' => $charge['typedestiny_id'],
                            'calculation_type_id' => $charge['calculationtypelcl']['id'],
                            'units' => ($charge['units']),
                            'price_per_unit' => intval($charge['ammount']),
                            'minimum' => intval($charge['minimum']),
                            'currency_id' => $currency_id,
                            'markup' => intval($charge['markup']),
                            'total' => intval($charge['total']),
                        ]);
                    }
                }
            }

            if ($search_data_ids['type'] == 'FCL') {
                $markups = isset($rate['container_markups']) ? $this->formatMarkupsForQuote($rate['container_markups']) : null;
            } else {
                $markups = [
                    'total' => 0,
                    'per_unit' => $rate_markups['Freight'] / $rate['units'],
                ];
            }

            $rateTotals = AutomaticRateTotal::create([
                'quote_id' => $quote->id,
                'automatic_rate_id' => $newRate->id,
                'origin_port_id' => $newRate->origin_port_id,
                'destination_port_id' => $newRate->destination_port_id,
                'carrier_id' => $newRate->carrier_id,
                'currency_id' => $rate['currency_id'],
                'totals' => null,
                'markups' => $markups,
            ]);

            $rateTotals->totalize($rate['currency_id']);
        }

        foreach ($result_data as $result) {

            $result = $this->formatApiResult($result, $search_data);

            if (isset($result['validityFrom'])) {
                $start_date = substr($result['validityFrom'], 0, 10);
            } else {
                $start_date = substr($search_data['dateRange']['startDate'], 0, 10);
            }

            if (isset($result['validityTo'])) {
                $end_date = substr($result['validityTo'], 0, 10);
            } else {
                $end_date = substr($search_data['dateRange']['endDate'], 0, 10);
            }

            $newRate = AutomaticRate::create([
                'quote_id' => $quote->id,
                'contract' => $result['contractReference'] ?? $result['quoteLine'],
                'validity_start' => $start_date,
                'validity_end' => $end_date,
                'transit_time' => $result['routingDetails'][0]['transitTime'],
                'via' => count($result['routingDetails'][0]['details']) > 1 ? $result['routingDetails'][0]['details'][0]['arrivalName'] : null,
                'schedule_type' => count($result['routingDetails'][0]['details']) > 1 ? 2 : 1,
                'currency_id' => $result['currency_id'],
                'origin_port_id' => $result['origin_port'],
                'destination_port_id' => $result['destiny_port'],
                'carrier_id' => $result['carrier_id'],
            ]);

            foreach ($result['pricingDetails']['surcharges'] as $charge_direction) {
                foreach ($charge_direction as $charge) {

                    $freight = Charge::create([
                        'automatic_rate_id' => $newRate->id,
                        'surcharge_id' => $charge['surcharge_id'],
                        'type_id' => $charge['type_id'],
                        'calculation_type_id' => $charge['calculationtype_id'],
                        'currency_id' => $charge['currency_id'],
                        'amount' => json_encode($charge['amount']),
                        'markups' => json_encode($charge['markups']),
                        'total' => json_encode($charge['total']),
                    ]);
                }
            }

            $rateTotals = AutomaticRateTotal::create([
                'quote_id' => $quote->id,
                'automatic_rate_id' => $newRate->id,
                'origin_port_id' => $newRate->origin_port_id,
                'destination_port_id' => $newRate->destination_port_id,
                'carrier_id' => $newRate->carrier_id,
                'currency_id' => $newRate->currency_id,
                'markups' => $result['rate_markups'],
            ]);

            $rateTotals->totalize($newRate->currency_id);

        }

        $quote->updatePdfOptions();

        /** Tracking create quote event with Mix Panel*/
        $this->trackEvents("create_quote", $quote);

        return new QuotationResource($quote);
    }

    public function edit(Request $request, QuoteV2 $quote)
    {
        $this->authorize('author', $quote); //policy para autorizar acceso.

        // $this->validateOldQuote($quote);

        return view('quote.edit');
    }

    public function update(Request $request, QuoteV2 $quote)
    {
        $form_keys = $request->input('keys');

        $terms_keys = ['terms_and_conditions', 'terms_portuguese', 'terms_english', 'remarks_spanish', 'remarks_portuguese', 'remarks_english',
                        'remarks_italian', 'remarks_catalan', 'remarks_french'];

        if ($form_keys != null) {
            if (array_intersect($terms_keys, $form_keys) == [] && $request->input('cargo_type_id') == null) {
                $data = $request->validate([
                    'delivery_type' => 'required',
                    'equipment' => 'required',
                    'status' => 'required',
                    'type' => 'required',
                    'validity_start' => 'required',
                    'user_id' => 'required',
                    'validity_end' => 'required',
                    'language_id' => 'required',
                    'commodity' => 'sometimes|nullable',
                    'contact_id' => 'sometimes|nullable',
                    'company_id' => 'sometimes|nullable',
                    'incoterm_id' => 'sometimes|nullable',
                    'payment_conditions' => 'sometimes|nullable',
                    'kind_of_cargo' => 'sometimes|nullable',
                    'cargo_type_id' => 'nullable',
                    'total_quantity' => 'sometimes|nullable|numeric',
                    'total_volume' => 'sometimes|nullable|numeric',
                    'total_weight' => 'sometimes|nullable|numeric',
                    'chargeable_weight' => 'sometimes|nullable',
                    'custom_incoterm' => 'sometimes|nullable',
                    'custom_quote_id' => 'sometimes|nullable',

                ]);
            } else {
                $data = [];
                foreach ($form_keys as $fkey) {
                    if (!in_array($fkey, $data) && $fkey != 'keys') {
                        $data[$fkey] = $request->input($fkey);
                    }
                }
            }
        } else {
            $data = [];
        }

        foreach (array_keys($data) as $key) {
            if ($key == 'equipment') {
                $data[$key] = $quote->getContainerArray($data[$key]);
            } else if ($key == 'contact_id') {
                if ($quote->company_id == null) {
                    $data[$key] = null;
                }
            } else if ($key == 'cargo_type_id') {
                if ($data[$key] == 'Pallets') {
                    $data[$key] = 1;
                } else {
                    $data[$key] = 2;
                }
            } else if ($key == 'status') {
                if ($data[$key] == 1) {
                    $data[$key] = 'Draft';
                } else if ($data[$key] == 2) {
                    $data[$key] = 'Sent';
                } else if ($data[$key] == 5) {
                    $data[$key] = 'Win';
                } else if ($data[$key] == 6) {
                    $data[$key] = 'Lost';
                }
            }

            if ($key == 'language_id') {

                $current_company_id = $quote->company_id;
                $request_company_id = $data['company_id'];

                if ($request_company_id != $current_company_id) {
                    if ($request_company_id) {
                        $company_id = (int) $request_company_id;
                        $language_id = $this->getCompanyLanguageId($company_id);
                        if ($language_id) {
                            $data[$key] = $language_id;
                        }
                    }
                }

            }

            $quote->update([$key => $data[$key]]);

            if ($key == 'validity_end') {
                $rates = $quote->rates_v2()->get();

                if ($rates != null && count($rates) != 0) {
                    foreach ($rates as $rate) {
                        $rate->update([$key => $data[$key]]);
                    }
                }
            }
        }

        if ($request->input('pdf_options') != null) {

            $request->validate([
                'pdf_options.exchangeRates.*.exchangeEUR' => 'gt:0',
                'pdf_options.exchangeRates.*.exchangeUSD' => 'gt:0',
            ]);

            $quote->update(['pdf_options' => $request->input('pdf_options')]);
        }

        if (isset($request['total_quantity']) || isset($request['total_volume']) || isset($request['total_weight'])) {

            $calc_volume = floatval($request['total_volume']);
            $calc_weight = floatval($request['total_weight']) / 1000;

            $quote->update(['total_quantity' => $request['total_quantity']]);
            $quote->update(['total_volume' => $request['total_volume']]);
            $quote->update(['total_weight' => $request['total_weight']]);
            if ($calc_volume > $calc_weight) {
                $quote->update(['chargeable_weight' => $request['total_volume']]);
            } else {
                $quote->update(['chargeable_weight' => $request['total_weight']]);
            }
        }

        if ($quote->wasChanged('status')) {
            $this->trackEvents("status_quote", $quote);
        }

    }

    public function getCompanyLanguageId($company_id)
    {
        $company = Company::find($company_id);
        $pdf_language = $company->pdf_language;

        if (!is_null($pdf_language)) {
            $language = Language::where('name', strtoupper($pdf_language))->first();
            if ($language) {
                return $language->id;
            } else {
                if ($pdf_language == 0) {
                    return 1;
                } else {
                    return $pdf_language;
                }
            }
        }
        return false;
    }

    public function updateSearchOptions(Request $request, QuoteV2 $quote)
    {
        $search_data = $request->input();

        if (isset($search_data['renew'])) {
            $quote->update(['search_options' => null]);
        } else {
            $date_range = $search_data['dateRange'];
            $start_date = substr($date_range['startDate'], 0, 10);
            $end_date = substr($date_range['endDate'], 0, 10);

            $contact = $search_data['contact'];
            $company = $search_data['company'];

            $price_level = $search_data['pricelevel'];

            $origin_charges = $search_data['originCharges'];
            $destination_charges = $search_data['destinationCharges'];
            $show_rate_currency = $search_data['showRateCurrency'];

            $origin_ports = $search_data['originPorts'];
            $destination_ports = $search_data['destinationPorts'];

            $search_options = compact(
                'start_date', 'end_date', 'contact', 'company', 'price_level', 'origin_charges', 'destination_charges',
                'origin_ports', 'destination_ports', 'show_rate_currency');

            $quote->update(['search_options' => $search_options, 'direction_id' => $search_data['direction']]);
        }

    }

    public function destroy(QuoteV2 $quote)
    {
        $quote->delete();

        return response()->json(['message' => 'Ok']);
    }

    public function retrieve(QuoteV2 $quote)
    {
        return new QuotationResource($quote);
    }

    public function duplicate(QuoteV2 $quote)
    {

        $new_quote = $quote->duplicate();

        $new_quote->update([
            'custom_quote_id' => null,
            'user_id' => Auth::user()->id,
        ]);

        return new QuotationResource($new_quote);
    }

    public function specialduplicate(Request $request)
    {
        $data = $request->input();

        //Nuevos fletes seleccionados
        $rate_data = $data['rates'];
        $result_data = $data['results'];

        if (count($rate_data) != 0) {
            $search_data = $rate_data[0]['search'];
        } else {
            $search_data = $result_data[0]['search'];
        }

        $search_data_ids = $this->getIdsFromArray($search_data);

        $quote_id = $search_data['requestData']['model_id'];

        $quote = QuoteV2::where('id', $quote_id)->first();

        //Duplicating quote
        $new_quote = $quote->duplicate();
        $new_quote->update(['user_id' => Auth::user()->id]);

        //Setting additional data
        if ($quote->search_options == null) {
            $new_quote->update([
                'contact_id' => $search_data_ids['contact'],
                'company_id' => $search_data_ids['company'],
                'price_id' => $search_data_ids['pricelevel'],
                'validity_start' => $search_data_ids['dateRange']['startDate'],
                'validity_end' => $search_data_ids['dateRange']['endDate'],
            ]);
        } else {
            $search_options_ids = $this->getIdsFromArray($quote->search_options);
            $new_quote->update([
                'contact_id' => $search_options_ids['contact'],
                'company_id' => $search_options_ids['company'],
                'price_id' => $search_options_ids['price_level'],
                'validity_start' => $search_options_ids['start_date'],
                'validity_end' => $search_options_ids['end_date'],
            ]);
        }

        //Buscar AutomaticRates viejos
        $old_rates = $new_quote->rates_v2()->get();

        $oldChargesOriginAndDestinyType = [];

        //Obtener recargos de tipo origin y destino de los fletes originales (los que se muestran en el modal)
        foreach ($old_rates as $rate) {
            $charges = $rate->charge()->get();
            foreach ($charges as $charge) {
                if ($charge->type_id !== 3) {
                    array_push($oldChargesOriginAndDestinyType, $charge);
                }
            }
        }

        //Buscar AutomaticRateTotals viejos
        $old_rates_totals = $new_quote->automatic_rate_totals()->get();

        foreach ($old_rates_totals as $old_rate_total) {
            $old_rate_total->delete();
        }

        //Setting Automatic Rates
        $rate_ports = ['origin' => [], 'destination' => []];

        foreach ($rate_data as $rate) {

            array_push($rate_ports['origin'], $rate['origin_port']);
            array_push($rate_ports['destination'], $rate['destiny_port']);

            $newRate = AutomaticRate::create([
                'quote_id' => $new_quote->id,
                'contract' => isset($rate['contract']) ? $rate['contract']['name'] : '',
                'validity_start' => $rate['contract']['validity'],
                'validity_end' => $rate['contract']['expire'],
                'currency_id' => $rate['currency_id'],
                'origin_port_id' => $rate['origin_port'],
                'destination_port_id' => $rate['destiny_port'],
                'carrier_id' => $rate['carrier_id'],
            ]);

            //Asignar automatic_rate_id en caso el origin o destino sean iguales
            foreach ($oldChargesOriginAndDestinyType as $oldCharge) {
                $automaticRateToOldCharge = $oldCharge->automatic_rate()->first();
                if ($oldCharge->type_id == 1 && $automaticRateToOldCharge->origin_port_id == $newRate->origin_port_id) {
                    $oldCharge->automatic_rate_id = $newRate->id;
                }
                if ($oldCharge->type_id == 2 && $automaticRateToOldCharge->destination_port_id == $newRate->destination_port_id) {
                    $oldCharge->automatic_rate_id = $newRate->id;
                }
            }

            //Guardar en la bd los
            foreach ($oldChargesOriginAndDestinyType as $charges) {
                $charges->save();
            }

            foreach ($rate['charges'] as $charge_direction) {
                foreach ($charge_direction as $charge) {

                    $currency_id = isset($charge['joint_as']) && $charge['joint_as'] == 'client_currency' ? $rate['client_currency']['id'] : $charge['currency']['id'];
                    $charge = $this->formatFclChargeForQuote($charge);
                    $ocean_surcharge = Surcharge::where([['name', 'Ocean Freight'], ['company_user_id', null]])->first();

                    if ($charge['typedestiny_id'] == 3) { //Crear solo charges con tipo Freight
                        $freight = Charge::create([
                            'automatic_rate_id' => $newRate->id,
                            'surcharge_id' => isset($charge['surcharge_id']) ? $charge['surcharge_id'] : $ocean_surcharge->id,
                            'type_id' => $charge['typedestiny_id'],
                            'calculation_type_id' => $charge['calculationtype']['id'],
                            'currency_id' => $currency_id,
                            'amount' => json_encode($charge['amount']),
                            'markups' => json_encode($charge['markups']),
                            'total' => json_encode($charge['total']),
                        ]);
                    }
                }
            }

            $rateTotals = AutomaticRateTotal::create([
                "quote_id" => $new_quote->id,
                'automatic_rate_id' => $newRate->id,
                'origin_port_id' => $newRate->origin_port_id,
                'destination_port_id' => $newRate->destination_port_id,
                'carrier_id' => $newRate->carrier_id,
                'currency_id' => $rate['currency_id'],
                'totals' => null,
                'markups' => isset($rate['container_markups']) ? $this->formatMarkupsForQuote($rate['container_markups']) : null,
            ]);

            $rateTotals->totalize($rate['currency_id']);
        }

        foreach ($result_data as $result) {

            $result = $this->formatApiResult($result, $search_data);

            if (isset($result['validityFrom'])) {
                $start_date = substr($result['validityFrom'], 0, 10);
            } else {
                $start_date = substr($search_data['dateRange']['startDate'], 0, 10);
            }

            if (isset($result['validityTo'])) {
                $end_date = substr($result['validityTo'], 0, 10);
            } else {
                $end_date = substr($search_data['dateRange']['endDate'], 0, 10);
            }

            array_push($rate_ports['origin'], $result['origin_port']);
            array_push($rate_ports['destination'], $result['destiny_port']);

            $newRate = AutomaticRate::create([
                'quote_id' => $new_quote->id,
                'contract' => $result['contractReference'] ?? $result['quoteLine'],
                'validity_start' => $start_date,
                'validity_end' => $end_date,
                'currency_id' => $result['currency_id'],
                'origin_port_id' => $result['origin_port'],
                'destination_port_id' => $result['destiny_port'],
                'carrier_id' => $result['carrier_id'],
            ]);
            //Asignar automatic_rate_id en caso el origin o destino sean iguales
            foreach ($oldChargesOriginAndDestinyType as $oldCharge) {
                $automaticRateToOldCharge = $oldCharge->automatic_rate()->first();
                if ($oldCharge->type_id == 1 && $automaticRateToOldCharge->origin_port_id == $newRate->origin_port_id) {
                    $oldCharge->automatic_rate_id = $newRate->id;
                }
                if ($oldCharge->type_id == 2 && $automaticRateToOldCharge->destination_port_id == $newRate->destination_port_id) {
                    $oldCharge->automatic_rate_id = $newRate->id;
                }
            }

            //Guardar en la bd los
            foreach ($oldChargesOriginAndDestinyType as $charges) {
                $charges->save();
            }

            foreach ($result['pricingDetails']['surcharges'] as $charge_direction) {
                foreach ($charge_direction as $charge) {
                    if ($charge['type_id'] == 3) { //Crear solo charges con tipo Freight
                        $freight = Charge::create([
                            'automatic_rate_id' => $newRate->id,
                            'surcharge_id' => $charge['surcharge_id'],
                            'type_id' => $charge['type_id'],
                            'calculation_type_id' => $charge['calculationtype_id'],
                            'currency_id' => $charge['currency_id'],
                            'amount' => json_encode($charge['amount']),
                            'total' => json_encode($charge['amount']),
                        ]);
                    }
                }
            }

            $rateTotals = AutomaticRateTotal::create([
                "quote_id" => $new_quote->id,
                'automatic_rate_id' => $newRate->id,
                'origin_port_id' => $newRate->origin_port_id,
                'destination_port_id' => $newRate->destination_port_id,
                'carrier_id' => $newRate->carrier_id,
                'currency_id' => $newRate->currency_id,
            ]);

            $rateTotals->totalize($result['currency_id']);
        }

        //Eliminar AutomaticRates viejos (tener en cuenta que por relación en cascada esto borra tambien los charges)
        foreach ($old_rates as $old_rate) {
            $old_rate->delete();
        }

        //Deleting Inlands without ports in rates
        $inlands = $new_quote->inland_addresses()->get();

        foreach ($inlands as $inland) {
            if ($inland->type == "Origin") {
                if (!in_array($inland->port_id, $rate_ports['origin'])) {
                    $inland->delete();
                }
            } elseif ($inland->type == "Destination") {
                if (!in_array($inland->port_id, $rate_ports['destination'])) {
                    $inland->delete();
                }
            }
        }

        $inlands = $new_quote->inland_addresses()->get();

        //Deleting Local Charges without ports in rates
        $local_charge_quotes = $new_quote->local_charges()->get();

        foreach ($local_charge_quotes as $localcharge) {
            if ($localcharge->type_id == 1) {
                if (!in_array($localcharge->port_id, $rate_ports['origin'])) {
                    $localcharge->delete();
                }
            } elseif ($localcharge->type_id == 2) {
                if (!in_array($localcharge->port_id, $rate_ports['destination'])) {
                    $localcharge->delete();
                }
            }
        }

        if ($new_quote->type == "FCL") {
            $locals = $new_quote->local_charges_totals()->get();
        } elseif ($new_quote->type == "LCL") {
            $locals = $new_quote->local_charges_lcl_totals()->get();
        }

        foreach ($locals as $local) {
            if ($local->type_id == 1) {
                if (!in_array($local->port_id, $rate_ports['origin'])) {
                    $local->delete();
                }
            } elseif ($local->type_id == 2) {
                if (!in_array($local->port_id, $rate_ports['destination'])) {
                    $local->delete();
                }
            }
        }

        $quote->update(['search_options' => null]);
        $new_quote->update(['search_options' => null]);

        $newLocalcharges = $new_quote->local_charges;

        foreach ($new_quote->automatic_inland_totals as $automaticInlnadT) {
            if ($automaticInlnadT["pdf_options"]["groupId"] != null) {
                foreach ($newLocalcharges as $local) {
                    $localcharges = LocalChargeQuote::findOrFail($automaticInlnadT["pdf_options"]["groupId"]);
                    $igual = array_diff($localcharges['price'], $local['price']);
                    if (
                        count($igual) == 0 && $local['surcharge_id'] == $localcharges['surcharge_id'] && $local['calculation_type_id'] == $localcharges['calculation_type_id']
                        && $local['type_id'] == $localcharges['type_id'] && isset($local['provider_name']) == isset($localcharges['provider_name']) && $local['charge'] == $localcharges['charge']
                    ) {
                        $localcharges = AutomaticInlandTotal::findOrFail($automaticInlnadT->id);
                        $pdfOptions = [
                            "groupId" => $local->id,
                            "grouped" => true,
                        ];
                        $localcharges->pdf_options = $pdfOptions;
                        $localcharges->update();
                    }
                }
            }
        }

        return new QuotationResource($new_quote);
    }

    public function destroyAll(Request $request)
    {
        $toDestroy = QuoteV2::whereIn('id', $request->input('ids'))->get();

        foreach ($toDestroy as $td) {
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }

    public function show($id)
    {
        $quote_id = obtenerRouteKey($id);
        $quote = QuoteV2::firstOrFail($quote_id);

        return redirect()->action('QuotationController@edit', $quote);
    }

    public function validateOldQuote($quote)
    {

        $rates = $quote->rates_v2()->get();
        $inlandTotals = $quote->automatic_inland_totals()->get();
        $inlandAddress = $quote->automatic_inland_address()->get();
        $quote_rate_totals = $quote->automatic_rate_totals()->get();

        if (isset($quote->company)) {
            $clean_payment_conditions = str_replace("&nbsp;", " ", strip_tags($quote->company->payment_conditions));

            $quote->update(['payment_conditions' => $clean_payment_conditions]);
        }

        if (count($rates) != 0) {
            foreach ($rates as $rate) {
                $rateTotal = $rate->totals()->first();
                if (!$rateTotal) {
                    $currency = $rate->currency()->first();

                    $newRateTotal = AutomaticRateTotal::create([
                        'quote_id' => $quote->id,
                        'currency_id' => $currency->id,
                        'origin_port_id' => $rate->origin_port_id,
                        'destination_port_id' => $rate->destination_port_id,
                        'automatic_rate_id' => $rate->id,
                        'carrier_id' => $rate->carrier_id,
                        'totals' => null,
                        'markups' => null,
                    ]);

                    $newRateTotal->totalize($currency->id);
                } else {
                    if ($rateTotal->carrier_id == null) {
                        $rateTotal->carrier_id = $rate->carrier_id;

                        $rateTotal->save();
                    }
                    $currency = $rate->currency()->first();

                    $rateTotal->totalize($currency->id);
                }
            }
        }

        if (count($inlandTotals) == 0 && count($inlandAddress) != 0) {
            foreach ($inlandAddress as $address) {
                foreach ($rates as $autoRate) {
                    if ($address->port_id == $autoRate->origin_port_id) {
                        $type = 'Origin';
                        $address->update(['type' => 'Origin']);
                        if ($quote->origin_address == null) {
                            $quote->update(['origin_address' => $address->address]);
                        }
                    } else if ($address->port_id == $autoRate->destination_port_id) {
                        $type = 'Destination';
                        $address->update(['type' => 'Destination']);
                        if ($quote->destination_address == null) {
                            $quote->update(['destination_address' => $address->address]);
                        }
                    }
                }

                $user_currency = $quote->user()->first()->companyUser()->first()->currency_id;

                $totals = AutomaticInlandTotal::create([
                    'quote_id' => $quote->id,
                    'port_id' => $address->port_id,
                    'type' => $type,
                    'inland_address_id' => $address->id,
                    'currency_id' => $user_currency,
                ]);

                if ($quote->type == 'FCL') {
                    $inlands = $quote->inland()->get();
                } else if ($quote->type == 'LCL') {
                    $inlands = $quote->inland_lcl()->get();
                }

                if (count($inlands) != 0) {
                    foreach ($inlands as $inland) {
                        if ($inland->port_id == $totals->port_id) {
                            $inland->inland_totals_id = $totals->id;
                            $inland->save();
                        }
                    }
                }

                $totals->totalize();
            }
        } elseif (count($inlandTotals) != 0) {
            foreach ($inlandAddress as $address) {
                foreach ($rates as $autoRate) {
                    if ($address->port_id == $autoRate->origin_port_id) {
                        $address->update(['type' => 'Origin']);
                        if ($quote->origin_address == null) {
                            $quote->update(['origin_address' => $address->address]);
                        }
                    } else if ($address->port_id == $autoRate->destination_port_id) {
                        $address->update(['type' => 'Destination']);
                        if ($quote->destination_address == null) {
                            $quote->update(['destination_address' => $address->address]);
                        }
                    }
                }
            }

            foreach ($inlandTotals as $total) {
                $total->totalize();
                if ($total->pdf_options == null) {
                    $pdfOptions = [
                        "grouped" => false,
                        "groupId" => null,
                    ];

                    $total->pdf_options = $pdfOptions;
                    $total->save();
                }
                if ($quote->type == 'FCL') {
                    $inlands = $total->inlands()->get();
                } else if ($quote->type == 'LCL') {
                    $inlands = $total->inlands_lcl()->get();
                }

                if (count($inlands) != 0) {
                    foreach ($inlands as $inland) {
                        if ($inland->port_id == $total->port_id) {
                            $inland->inland_totals_id = $total->id;
                            $inland->save();
                        }
                    }
                } else {
                    if ($total->inland_address()->first() != null) {
                        $total->inland_address()->first()->delete();
                    }
                }
            }
        }

        $quote->updatePdfOptions();

        if (count($quote_rate_totals) != 0) {
            foreach ($quote_rate_totals as $qr_total) {
                if ($qr_total->rate()->first() == null) {
                    $qr_total->delete();
                }
            }
        }
    }

    /**
     * get providers
     *
     * @param  mixed $request
     * @return void
     */
    public function providers($carriers)
    {
        $providers = Provider::where('company_user_id', \Auth::user()->company_user_id)->get();

        $providers = $providers->map(function ($value) {
            $value['model'] = 'App\Provider';
            return $value->only(['id', 'name', 'model']);
        });

        $data = $carriers->merge($providers)->unique();

        $data = $data->sortBy('name');

        $collection = $data->values()->all();

        return $collection;
    }

    public function setCostSheet(QuoteV2 $quote, AutomaticRate $autorate)
    {

        return new CostSheetResource($quote, $autorate);

    }
    public function formatPenaltyRemark($formattedPenalties, $company, $containers)
    {
        $table = '';
        $penalValue = '';
        $head = '';
        $count = count($containers);
        foreach ($containers as $key => $container) {
            $c = '';

            if ($key == 0) {
                $c = "<tr>" . "<th>" . $company . " Fees" . "</th>" . "<th>" . $container['code'] . "</th>";
            } elseif ($key == $count - 1) {
                $c = "<th>" . $container['code'] . "</th>" . "</tr>";
            } else {
                $c = "<th>" . $container['code'] . "</th>";
            }
            $head .= $c;
        }
        $table .= $head;

        foreach ($formattedPenalties as $key => $penalties) {
            $index = array_keys($penalties);
            $count = count($index);

            for ($i = 0; $i < $count; $i++) {
                $penal = '';

                if ($i == 0) {
                    $penal = "<tr>" . "<th>" . $penalties[$index[$i]] . "</th>";
                } elseif ($i == $count - 1) {
                    $penal = "<th>" . $penalties[$index[$i]] . " " . $penalValue . "</th>" . "</tr>";
                } elseif (is_int($penalties[$index[$i]])) {
                    $penalValue = $penalties[$index[$i]];
                } elseif (isset($penalValue)) {
                    $penal = "<th>" . $penalties[$index[$i]] . " " . $penalValue . "</th>";
                } else {
                    $penal = "<tr>" . "<th>" . $penalties[$index[$i]] . "</th>";
                }
                $table .= $penal;
            }
        }

        $remark = "<table>" . $table . "</table>";
        return $remark;
    }
}
