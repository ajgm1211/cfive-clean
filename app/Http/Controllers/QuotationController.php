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
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Container;
use App\Country;
use App\Currency;
use App\DeliveryType;
use App\DestinationType;
use App\Harbor;
use App\Http\Resources\QuotationListResource;
use App\Http\Resources\QuotationResource;
use App\Http\Traits\QuoteV2Trait;
use App\Http\Traits\SearchTrait;
use App\Incoterm;
use App\InlandDistance;
use App\Language;
use App\PaymentCondition;
use App\Provider;
use App\QuoteV2;
use App\SaleTermCode;
use App\ScheduleType;
use App\StatusQuote;
use App\Surcharge;
use App\TermAndConditionV2;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\MixPanelTrait;
use App\ViewQuoteV2;
use App\LocalChargeQuote;

class QuotationController extends Controller
{
    use QuoteV2Trait, SearchTrait, MixPanelTrait;

    public function index(Request $request)
    {
        return view('quote.index');
    }

    function list(Request $request)
    {
        $results = ViewQuoteV2::filterByCurrentCompany()->filter($request);

        return QuotationListResource::collection($results);
    }

    public function data(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;

        $carriers = Carrier::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name', 'image']);
        });

        $companies = Company::where('company_user_id', '=', $company_user_id)->get()->map(function ($company) {
            return $company->only(['id', 'business_name']);
        });

        $comps = Company::where('company_user_id', '=', $company_user_id)->get();
        $contacts = [];
        $languages = [];
        foreach ($comps as $comp) {
            array_push($languages, ['company_id' => $comp->id, 'name' => $comp->pdf_language]);
            $cts = $comp->contact()->get();
            foreach ($cts as $ct) {
                array_push($contacts, ['id' => $ct->id, 'company_id' => $ct->company_id, 'name' => $ct->getFullName()]);
            }
        };

        $incoterms = Incoterm::get()->map(function ($incoterm) {
            return $incoterm->only(['id', 'name']);
        });

        $users = User::whereHas('companyUser', function ($q) use ($company_user_id) {
            $q->where('company_user_id', '=', $company_user_id);
        })->get()->map(function ($user) {
            return $user->only(['id', 'name', 'lastname', 'fullname']);
        });

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name', 'country_id', 'code']);
        });

        $payment_conditions = PaymentCondition::get()->map(function ($payment_condition) {
            return $payment_condition->only(['id', 'quote_id', 'name']);
        });

        $terms_and_conditions = TermAndConditionV2::get()->map(function ($term_and_condition) {
            return $term_and_condition->only(['id', 'name', 'user_id', 'type', 'company_user_id']);
        });

        $delivery_types = DeliveryType::get()->map(function ($delivery_type) {
            return $delivery_type->only(['id', 'name']);
        });

        $status_options = StatusQuote::get()->map(function ($status) {
            return $status->only(['id', 'name']);
        });

        $kind_of_cargo = CargoKind::get()->map(function ($kcargo) {
            return $kcargo->only(['id', 'name']);
        });

        $languages = Language::get()->map(function ($language) {
            return $language->only(['id', 'name']);
        });

        $currency = Currency::get()->map(function ($curr) {
            return $curr->only(['id', 'alphacode', 'rates', 'rates_eur']);
        });

        $filtered_currencies = Currency::whereIn('id', ['46', '149'])->get()->map(function ($curr) {
            return $curr->only(['id', 'alphacode', 'rates', 'rates_eur']);
        });

        $containers = Container::all();

        $calculationtypes = CalculationType::get()->map(function ($ctype) {
            return $ctype->only(['id', 'name']);
        });

        $surcharges = Surcharge::where('company_user_id', '=', $company_user_id)->get()->map(function ($surcharge) {
            return $surcharge->only(['id', 'name']);
        });

        $schedule_types = ScheduleType::get()->map(function ($schedule_type) {
            return $schedule_type->only(['id', 'name']);
        });

        $countries = Country::get()->map(function ($country) {
            return $country->only(['id', 'code', 'name']);
        });

        $sale_codes = SaleTermCode::where('company_user_id', '=', $company_user_id)->get()->map(function ($surcharge) {
            return $surcharge->only(['id', 'name']);
        });

        $providers = Provider::where('company_user_id', $company_user_id)->get()->map(function ($provider) {
            return $provider->only(['id', 'name']);
        });

        $cargo_types = CargoType::get()->map(function ($tcargo) {
            return $tcargo->only(['id', 'name']);
        });

        $calculationtypeslcl = CalculationTypeLcl::get()->map(function ($ctype) {
            return $ctype->only(['id', 'name']);
        });

        $destination_types = DestinationType::get()->map(function ($desttype) {
            return $desttype->only(['id', 'name']);
        });

        $carrier_providers = $this->providers();

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
        $company_code = strtoupper(substr($company_user->name, 0, 2));
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

        $equipment = "[\"" . implode("\",\"", $search_data_ids['containers']) . "\"]";

        $remarks = "";

        foreach ($rate_data as $rate) {
            $remarks .= $rate['remarks'];
        }
        
        $quote = QuoteV2::create([
            'quote_id' => $newq_id,
            'type' => $search_data_ids['type'],
            'delivery_type' => $search_data_ids['deliveryType'],
            'user_id' => $user->id,
            'direction_id' => $search_data_ids['direction'],
            'company_user_id' => $company_user->id,
            'language_id' => ($company_user->pdf_language == 0 || $company_user->pdf_language == null) ? 1 : $company_user->pdf_language,
            'company_id' => isset($search_data_ids['company']) ? $search_data_ids['company'] : null,
            'contact_id' => isset($search_data_ids['contact']) ? $search_data_ids['contact'] : null,
            'price_id' => isset($search_data_ids['pricelevel']) ? $search_data_ids['pricelevel'] : null,
            'equipment' => $equipment,
            //'origin_address' => $data['origin_address'],
            //'destination_address' => $data['destination_address'],
            'date_issued' => $search_data_ids['dateRange']['startDate'],
            'validity_start' => $search_data_ids['dateRange']['startDate'],
            'validity_end' => $search_data_ids['dateRange']['endDate'],
            'status' => 'Draft',
            'terms_portuguese' => $search_data['terms'] ? $search_data['terms']['portuguese'] : null,
            'terms_and_conditions' => $search_data['terms'] ? $search_data['terms']['spanish'] : null,
            'terms_english' => $search_data['terms'] ? $search_data['terms']['english'] : null,
            'direction_id' => $search_data_ids['direction'],

        ]);

        $quote = $quote->fresh();

        if ($quote->language_id == 1) {
            $quote->update(['remarks_english' => $remarks]);
        } else if ($quote->language_id == 2) {
            $quote->update(['remarks_spanish' => $remarks]);
        } else if ($quote->language_id == 3) {
            $quote->update(['remarks_portuguese' => $remarks]);
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

            foreach ($rate['charges'] as $charge_direction) {
                foreach ($charge_direction as $charge) {

                    $currency_id = isset($charge['joint_as']) && $charge['joint_as'] == 'client_currency' ? $rate['client_currency']['id'] : $charge['currency']['id'];
                    $charge = $this->formatChargeForQuote($charge);

                    $freight = Charge::create([
                        'automatic_rate_id' => $newRate->id,
                        'surcharge_id' => isset($charge['surcharge_id']) ? $charge['surcharge_id'] : null,
                        'type_id' => $charge['typedestiny_id'],
                        'calculation_type_id' => $charge['calculationtype']['id'],
                        'currency_id' => $currency_id,
                        'amount' => json_encode($charge['amount']),
                        'markups' => json_encode($charge['markups']),
                        'total' => json_encode($charge['total']),
                    ]);
                }
            }

            $rateTotals = AutomaticRateTotal::create([
                "quote_id" => $quote->id,
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

            $result = $this->formatApiResult($result, $search_data['selectedContainerGroup'], $search_data['containers']);

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
                        'total' => json_encode($charge['amount']),
                    ]);
                }
            }

            $rateTotals = AutomaticRateTotal::create([
                "quote_id" => $quote->id,
                'automatic_rate_id' => $newRate->id,
                'origin_port_id' => $newRate->origin_port_id,
                'destination_port_id' => $newRate->destination_port_id,
                'carrier_id' => $newRate->carrier_id,
                'currency_id' => $newRate->currency_id,
            ]);

            $rateTotals->totalize($newRate->currency_id);
        }

        /** Tracking create quote event with Mix Panel*/
        $this->trackEvents("create_quote_fcl", $quote);

        return new QuotationResource($quote);
    }

    //Retrieves Terms and Conditions
    public function searchTerms($search_data)
    {
        $terms = TermAndConditionV2::where([['company_user_id', \Auth::user()->company_user_id], ['type', $search_data['type']]])->get();

        $terms_english = '';
        $terms_spanish = '';
        $terms_portuguese = '';

        foreach ($terms as $term) {

            if ($search_data['direction'] == 1) {
                $terms_to_add = $term->import;
            } else if ($search_data['direction'] == 2) {
                $terms_to_add = $term->export;
            }

            if ($term->language_id == 1) {
                $terms_english .= $terms_to_add . '<br>';
            } else if ($term->language_id == 2) {
                $terms_spanish .= $terms_to_add . '<br>';
            } else if ($term->language_id == 3) {
                $terms_portuguese .= $terms_to_add . '<br>';
            }
        }

        $final_terms = ['english' => $terms_english, 'spanish' => $terms_spanish, 'portuguese' => $terms_portuguese];

        return $final_terms;
    }

    public function edit(Request $request, QuoteV2 $quote)
    {
        $this->validateOldQuote($quote);

        return view('quote.edit');
    }

    public function update(Request $request, QuoteV2 $quote)
    {
        $form_keys = $request->input('keys');

        $terms_keys = ['terms_and_conditions', 'terms_portuguese', 'terms_english', 'remarks_spanish', 'remarks_portuguese', 'remarks_english'];

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
                ]);
            }
            // else if ($request->input('cargo_type_id') != null) {
            //     $data = $request->validate([
            //         'cargo_type_id' => 'nullable',
            //         'total_quantity' => 'nullable|numeric',
            //         'total_volume' => 'nullable|numeric',
            //         'total_weight' => 'nullable|numeric',
            //         'chargeable_weight' => 'nullable',
            //     ]);
            // } 
            else {
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

        if ($request->input('custom_incoterm') != null) {
            $quote->update(['custom_incoterm' => $request->input('custom_incoterm')]);
        } else {
            $quote->update(['custom_incoterm' => null]);
        }

        if ($request->input('custom_quote_id') != null) {
            $quote->update(['custom_quote_id' => $request->input('custom_quote_id')]);
        } else {
            $quote->update(['custom_quote_id' => null]);
        }

        if ($request->input('pdf_options') != null) {
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
    }

    public function updateSearchOptions(Request $request, QuoteV2 $quote)
    {
        $search_data = $request->input();

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

        $old_rates = $new_quote->rates_v2()->get();

        foreach ($old_rates as $old_rate) {
            $old_rate->delete();
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

            foreach ($rate['charges'] as $charge_direction) {
                foreach ($charge_direction as $charge) {

                    $currency_id = isset($charge['joint_as']) && $charge['joint_as'] == 'client_currency' ? $rate['client_currency']['id'] : $charge['currency']['id'];
                    $charge = $this->formatChargeForQuote($charge);

                    $freight = Charge::create([
                        'automatic_rate_id' => $newRate->id,
                        'surcharge_id' => isset($charge['surcharge_id']) ? $charge['surcharge_id'] : null,
                        'type_id' => $charge['typedestiny_id'],
                        'calculation_type_id' => $charge['calculationtype']['id'],
                        'currency_id' => $currency_id,
                        'amount' => json_encode($charge['amount']),
                        'markups' => json_encode($charge['markups']),
                        'total' => json_encode($charge['total']),
                    ]);
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

            $result = $this->formatApiResult($result, $search_data['selectedContainerGroup'], $search_data['containers']);

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

            foreach ($result['pricingDetails']['surcharges'] as $charge_direction) {
                foreach ($charge_direction as $charge) {

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

            $rateTotals = AutomaticRateTotal::create([
                "quote_id" => $quote->id,
                'automatic_rate_id' => $newRate->id,
                'origin_port_id' => $newRate->origin_port_id,
                'destination_port_id' => $newRate->destination_port_id,
                'carrier_id' => $newRate->carrier_id,
                'currency_id' => $newRate->currency_id,
            ]);

            $rateTotals->totalize($result['currency_id']);
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
                            "grouped" => true
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
    public function providers()
    {
        $carriers = Carrier::all();
        $providers = Provider::where('company_user_id', \Auth::user()->company_user_id)->get();

        $carriers = $carriers->map(function ($value) {
            $value['model'] = 'App\Carrier';
            return $value->only(['id', 'name', 'model']);
        });

        $providers = $providers->map(function ($value) {
            $value['model'] = 'App\Provider';
            return $value->only(['id', 'name', 'model']);
        });

        $data = $carriers->merge($providers)->unique();

        $data = $data->sortBy('name');

        $collection = $data->values()->all();

        return $collection;
    }
}
