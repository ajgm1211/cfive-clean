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

class QuotationController extends Controller
{
    use QuoteV2Trait, SearchTrait;

    public function index(Request $request)
    {
        return view('quote.index');
    }

    function list(Request $request) {
        $results = QuoteV2::filterByCurrentCompany()->filter($request);

        return QuotationResource::collection($results);
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
            return $user->only(['id', 'name', 'lastname']);
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

        $distances = InlandDistance::get()->map(function ($distance) {
            return $distance->only(['id', 'display_name', 'harbor_id', 'distance']);
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

        $data = compact(
            'companies',
            'contacts',
            'carriers',
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
            'providers',
            'distances',
            'cargo_types',
            'calculationtypeslcl',
            'filtered_currencies',
            'destination_types'
        );

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $company_user = Auth::user('web')->worksAt();
        $company_code = strtoupper(substr($company_user->name, 0, 2));
        $user = User::where('company_user_id', $company_user->id)->first();
        $higherq_id = $company_user->getHigherId($company_code);
        $newq_id = $company_code . '-' . strval($higherq_id + 1);

        $rate_data = $request->input();

        $search_data = $rate_data[0]['search'];

        $search_data_ids = $this->getIdsFromArray($search_data);

        $equipment = $container_string = "[\"" . implode("\",\"", $search_data_ids['containers']) . "\"]";

        $remarks = "";

        foreach ($rate_data as $rate) {
            $remarks .= $rate['remarks'];
        }

        $quote = QuoteV2::create([
            'quote_id' => $newq_id,
            'type' => $search_data_ids['type'],
            'delivery_type' => $search_data_ids['deliveryType'],
            'user_id' => $user->id,
            'company_user_id' => $company_user->id,
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
            'remarks_english' => $remarks,
            'direction_id' => $search_data_ids['direction'],
        ]);

        $quote = $quote->fresh();

        foreach ($rate_data as $rate) {

            $newRate = AutomaticRate::create([
                'quote_id' => $quote->id,
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

        return new QuotationResource($quote);
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
                ]);
            } else if ($request->input('cargo_type_id') != null) {
                $data = $request->validate([
                    'cargo_type_id' => 'nullable',
                    'total_quantity' => 'nullable',
                    'total_volume' => 'nullable',
                    'total_weight' => 'nullable',
                    'chargeable_weight' => 'nullable',
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
                }
            }
            $quote->update([$key => $data[$key]]);
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

        if ($request->input('dateRange') != null) {
            $search_data = $request->input();

            $date_range = $search_data['dateRange'];
            $start_date = substr($date_range['startDate'], 0, 10);
            $end_date = substr($date_range['endDate'], 0, 10);

            $contact = $search_data['contact'];
            $company = $search_data['company'];

            $price_level = $search_data['pricelevel'];

            $origin_charges = $search_data['originCharges'];
            $destination_charges = $search_data['destinationCharges'];

            $search_options = compact('start_date', 'end_date', 'contact', 'company', 'price_level', 'origin_charges', 'destination_charges');

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

        return new QuotationResource($new_quote);
    }

    public function specialduplicate(Request $request)
    {
        $rate_data = $request->input();
        $search_data = $rate_data[0]['search'];

        $search_data_ids = $this->getIdsFromArray($search_data);

        $quote_id = $search_data['requestData']['model_id'];

        $quote = QuoteV2::where('id', $quote_id)->first();

        $new_quote = $quote->duplicate();

        $new_quote->update([
            'contact_id' => $search_data_ids['contact'],
            'company_id' => $search_data_ids['company'],
            'price_id' => $search_data_ids['pricelevel'],
            'validity_start' => $search_data_ids['dateRange']['startDate'],
            'validity_end' => $search_data_ids['dateRange']['endDate'],
        ]);

        $old_rates = $new_quote->rates_v2()->get();

        foreach ($old_rates as $old_rate) {
            $old_rate->delete();
        }

        foreach ($rate_data as $rate) {

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
                    $total->inland_address()->first()->delete();
                }
            }
        }

        if ($quote->pdf_options == null || count($quote->pdf_options) != 4) {
            $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
            $currency_id = $company->companyUser->currency_id;
            $currency = Currency::find($currency_id);

            $pdfOptions = [
                "allIn" => true,
                "showCarrier" => true,
                "showTotals" => false,
                "totalsCurrency" => $currency,
            ];

            $quote->pdf_options = $pdfOptions;
            $quote->save();
        }

        if (count($quote_rate_totals) != 0) {
            foreach ($quote_rate_totals as $qr_total) {
                if ($qr_total->rate()->first() == null) {
                    $qr_total->delete();
                }
            }
        }
    }
}
