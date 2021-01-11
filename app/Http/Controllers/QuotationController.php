<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\QuoteV2;
use App\Carrier;
use App\AutomaticRate;
use App\CompanyUser;
use App\Company;
use App\Contact;
use App\Incoterm;
use App\Harbor;
use App\PaymentCondition;
use App\TermAndConditionV2;
use App\DeliveryType;
use App\StatusQuote;
use App\CargoKind;
use App\CargoType;
use App\Language;
use App\Currency;
use App\Container;
use App\Charge;
use App\CalculationType;
use App\Surcharge;
use App\ScheduleType;
use App\Provider;
use App\Country;
use App\InlandDistance;
use App\CalculationTypeLcl;
use App\AutomaticRateTotal;
use App\AutomaticInlandTotal;
use App\DestinationType;
use App\Http\Resources\QuotationResource;
use App\SaleTermCode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class QuotationController extends Controller
{
    public function index(Request $request)
    {
        return view('quote.index');
    }

    public function list(Request $request)
    {
        $results = QuoteV2::filterByCurrentCompany()->filter($request);

        return QuotationResource::collection($results);
    }

    public function data(Request $request)
    {
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

        $incoterms = Incoterm::get()->map(function ($incoterm){
            return $incoterm->only(['id','name']);
        });

        $users = User::whereHas('companyUser', function ($q) use ($company_user_id) {
            $q->where('company_user_id', '=', $company_user_id);
        })->get()->map(function ($user) {
            return $user->only(['id','name','lastname']);
        });

        $harbors = Harbor::get()->map(function ($harbor) {
          return $harbor->only(['id', 'display_name','country_id','code']);
        });

        $payment_conditions = PaymentCondition::get()->map(function ($payment_condition){
            return $payment_condition->only(['id','quote_id','name']);
        });

        $terms_and_conditions = TermAndConditionV2::get()->map(function ($term_and_condition){
            return $term_and_condition->only(['id','name','user_id','type','company_user_id']);
        });

        $delivery_types = DeliveryType::get()->map(function ($delivery_type){
            return $delivery_type->only(['id','name']);
        });

        $status_options = StatusQuote::get()->map(function ($status){
            return $status->only(['id','name']);
        });

        $kind_of_cargo = CargoKind::get()->map(function ($kcargo){
            return $kcargo->only(['id','name']);
        });

        $languages = Language::get()->map(function ($language){
            return $language->only(['id','name']);
        });

        $currency = Currency::get()->map(function ($curr){
            return $curr->only(['id','alphacode','rates','rates_eur']);
        });

        $filtered_currencies = Currency::whereIn('id', ['46','149'])->get()->map(function ($curr){
            return $curr->only(['id','alphacode','rates','rates_eur']);
        });

        $containers = Container::all();

        $calculationtypes = CalculationType::get()->map(function ($ctype){
            return $ctype->only(['id','name']);
        });

        $surcharges = Surcharge::where('company_user_id','=',$company_user_id)->get()->map(function ($surcharge){
            return $surcharge->only(['id','name']);
        });

        $schedule_types = ScheduleType::get()->map(function ($schedule_type){
            return $schedule_type->only(['id','name']);
        });
        
        $countries = Country::get()->map(function ($country){
            return $country->only(['id','code','name']);
        });

        $sale_codes = SaleTermCode::where('company_user_id','=',$company_user_id)->get()->map(function ($surcharge){
            return $surcharge->only(['id','name']);
        });

        $providers = Provider::where('company_user_id',$company_user_id)->get()->map(function ($provider){
            return $provider->only(['id','name']);
        });

        $distances = InlandDistance::get()->map(function ($distance){
            return $distance->only(['id','display_name','harbor_id','distance']);
        });

        $cargo_types = CargoType::get()->map(function ($tcargo){
            return $tcargo->only(['id','name']);
        });

        $calculationtypeslcl = CalculationTypeLcl::get()->map(function ($ctype){
            return $ctype->only(['id','name']);
        });

        $destination_types = DestinationType::get()->map(function ($desttype){
            return $desttype->only(['id','name']);
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

        return response()->json(['data'=>$data]);
    }

    public function store(Request $request)
    {       
        $company_user = Auth::user('web')->worksAt();
        $company_code = strtoupper(substr($company_user->name, 0, 2));
        $higherq_id = $company_user->getHigherId($company_code);
        $newq_id = $company_code . '-' . strval($higherq_id + 1);

        if(!empty($request->input('form'))){
            $form = json_decode($request->input('form'));
            $quote_fields = ['type','mode','delivery_type','equipment','company_id_quote',
                            'contact_id','price_id_num','originport','destinyport','origin_address',
                            'destination_address','date','cargo_type','total_quantity','total_volume',
                            'total_weight','chargeable_weight','carriers']; 
            $data = [];
            foreach($form as $key=>$val){
                if(in_array($key,$quote_fields)){
                    $data[$key] = $val;
                }
            }
        } else {
            $data = $request->validate([
                'type' => 'required',
                'mode' => 'required',
                'delivery_type' => 'required',
                'equipment' => 'sometimes|required',
                'container_type' => 'required',
                'company_id_quote' => 'nullable',
                'contact_id' => 'nullable',
                'price_id_num' => 'sometimes|nullable',
                'originport' => 'required',
                'destinyport' => 'required',
                'origin_address' => 'nullable',
                'destination_address' => 'nullable',
                'date' => 'required',
                'cargo_type' => 'required',
                'total_quantity' => 'nullable',
                'total_volume' => 'nullable',
                'total_weight' => 'nullable',
                'chargeable_weight' => 'nullable',
                'carriers' => 'required'
            ]);
        }
        
        $quote = QuoteV2::create([
            'quote_id' => $newq_id,
            'type' => $data['type'] + 1,
            'delivery_type' => $data['delivery_type'],
            'user_id' => \Auth::user()->id,
            'company_user_id' => $company_user->id,
            'company_id' => isset($data['company_id_quote']) ? $data['company_id_quote'] : null,
            'contact_id' => isset($data['contact_id']) ? $data['contact_id'] : null,
            'mode' => $data['mode'],
            'cargo_type' => $data['cargo_type'],
            'total_quantity' => $data['total_quantity'],
            'total_weight' => $data['total_weight'],
            'total_volume' => $data['total_volume'],
            'chargeable_weight' => $data['chargeable_weight'],
            'price_id' => $data['price_id_num'],
            'equipment' => isset($data['equipment']) ? "[\"".implode("\",\"",$data['equipment'])."\"]" : null,
            'origin_address' => $data['origin_address'],
            'destination_address' => $data['destination_address'],
            'date_issued' => explode("/",$data['date'])[0],
            'validity_start' => explode("/",$data['date'])[0],
            'validity_end' => explode("/",$data['date'])[1],
            'status' => 'Draft' 
        ]);

        if($quote->company_id != null){
            $pay = $quote->company()->first()->payment_conditions;
            $quote->update(['payment_conditions'=>$pay]);
        }

        if(!empty($request->input('info'))){
            $info = $request->input('info');
            foreach($info as $currInfo){
                $info_decoded = json_decode($currInfo);
    
                foreach($info_decoded->rates as $rate_decoded){
                    $rate = AutomaticRate::create([
                        'quote_id' => $quote->id,
                        'contract' => '',
                        'validity_start' => explode("/",$data['date'])[0],
                        'validity_end' => explode("/",$data['date'])[1],
                        'currency_id' => $company_user->currency_id       
                    ]);
                    
                    $freight = Charge::create([
                        'automatic_rate_id' => $rate->id,
                        'type_id' => '3',
                        'calculation_type_id' => '5',
                        'currency_id' => $rate->currency_id,
                    ]);
                    
                    $freight->setContractInfo($info_decoded,$rate_decoded,$rate);
                    
                    $freight->setCalculationType($data['container_type']);
                }
            }
        }

        return redirect()->action('QuotationController@edit', $quote) ;
    }

    public function edit (Request $request, QuoteV2 $quote)
    {
        $this->validateOldQuote($quote);

        return view('quote.edit');
    }

    public function update (Request $request, QuoteV2 $quote)
    {                   
        $form_keys = $request->input('keys');

        $terms_keys = ['terms_and_conditions','terms_portuguese','terms_english','remarks_spanish','remarks_portuguese','remarks_english'];

        if($form_keys!=null){
            if(array_intersect($terms_keys,$form_keys)==[] && $request->input('cargo_type_id') == null){
                $data = $request->validate([
                    'delivery_type' => 'required',
                    'equipment' => 'required',
                    'status' => 'required',
                    'type' => 'required',
                    'validity_start' => 'required',
                    'user_id'=>'required',
                    'validity_end' => 'required',
                    'language_id' => 'required',
                    'commodity' => 'sometimes|nullable',
                    'contact_id' => 'sometimes|nullable',
                    'company_id' => 'sometimes|nullable',
                    'incoterm_id' => 'sometimes|nullable',
                    'payment_conditions' => 'sometimes|nullable',
                    'kind_of_cargo' => 'sometimes|nullable'
                ]);
            } else if($request->input('cargo_type_id')!=null){
                $data = $request->validate([
                    'cargo_type_id' => 'nullable',
                    'total_quantity' => 'nullable',
                    'total_volume' => 'nullable',
                    'total_weight' => 'nullable',
                    'chargeable_weight' => 'nullable',
                ]);
            } else {
                $data = [];

                foreach($form_keys as $fkey){
                    if(!in_array($fkey,$data) && $fkey != 'keys'){
                        $data[$fkey] = $request->input($fkey);
                    }
                }
            }

        } else {
            $data = [];
        }

        foreach(array_keys($data) as $key){
            if ($key=='equipment'){
                $data[$key] = $quote->getContainerArray($data[$key]);
            } else if($key=='contact_id'){
                if ($quote->company_id == null){
                    $data[$key] = null;
                }
            } else if($key=='cargo_type_id'){
                if($data[$key]=='Pallets'){
                    $data[$key] = 1;
                }else{
                    $data[$key] = 2;
                }
            } else if($key=='status'){
                if($data[$key] == 1){
                    $data[$key] = 'Draft';
                }else if($data[$key] == 2){
                    $data[$key] = 'Sent';
                }else if($data[$key] == 5){
                    $data[$key] = 'Win';
                }
            }
            $quote->update([$key=>$data[$key]]);
        }

        if($request->input('pdf_options') != null){
            $quote->update(['pdf_options'=>$request->input('pdf_options')]);
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

    public function destroyAll(Request $request)
    {   
        $toDestroy = QuoteV2::whereIn('id', $request->input('ids'))->get();
        
        foreach($toDestroy as $td){
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }

    public function show($id){

        $quote_id = obtenerRouteKey($id);
        $quote = QuoteV2::firstOrFail($quote_id);

        return redirect()->action('QuotationController@edit', $quote);
    }

    public function validateOldQuote($quote){

        $rates = $quote->rates_v2()->get();
        $inlandTotals = $quote->automatic_inland_totals()->get();
        $inlandAddress = $quote->automatic_inland_address()->get();
        $quote_rate_totals = $quote->automatic_rate_totals()->get();

        if(count($rates) != 0){
            foreach($rates as $rate){
                $rateTotal = $rate->totals()->first();
                if(!$rateTotal){
                    $currency = $rate->currency()->first();
                    
                    $newRateTotal = AutomaticRateTotal::create([
                        'quote_id' => $quote->id,
                        'currency_id' => $currency->id,
                        'origin_port_id' => $rate->origin_port_id,
                        'destination_port_id' => $rate->destination_port_id,
                        'automatic_rate_id' => $rate->id,
                        'totals' => null,
                        'markups' => null                    
                    ]);

                    $newRateTotal->totalize($currency->id);
                }else{
                    $currency = $rate->currency()->first();

                    $rateTotal->totalize($currency->id);
                }
            }
        }

        if(count($inlandTotals) == 0 && count($inlandAddress) != 0){
            foreach($inlandAddress as $address){
                foreach($rates as $autoRate){
                    if($address->port_id == $autoRate->origin_port_id){
                        $type = 'Origin';
                    }else if($address->port_id == $autoRate->destination_port_id){
                        $type = 'Destination';
                    }
                }
                
                $user_currency = $quote->user()->first()->companyUser()->first()->currency_id;

                $totals = AutomaticInlandTotal::create([
                    'quote_id' => $quote->id,
                    'port_id' => $address->port_id,
                    'type' => $type,
                    'inland_address_id' => $address->id,
                    'currency_id' => $user_currency
                ]);

                if($quote->type == 'FCL'){
                    $inlands = $quote->inland()->get();
                }else if($quote->type == 'LCL'){
                    $inlands = $quote->inland_lcl()->get();
                }

                if(count($inlands)!=0){
                    foreach($inlands as $inland){
                        if($inland->port_id == $totals->port_id){
                            $inland->inland_totals_id = $totals->id;
                            $inland->save();
                        }
                    }
                }

                $totals->totalize();
            }
        }else if(count($inlandTotals)!=0){
            foreach($inlandTotals as $total){
                $total->totalize();
                if($quote->type == 'FCL'){
                    $inlands = $total->inlands()->get();
                }else if($quote->type == 'LCL'){
                    $inlands = $total->inlands_lcl()->get();
                }
                
                if(count($inlands)!=0){
                    foreach($inlands as $inland){
                        if($inland->port_id == $total->port_id){
                            $inland->inland_totals_id = $total->id;
                            $inland->save();
                        }
                    }
                }else{
                    $total->inland_address()->first()->delete();
                }              
            }
        }

        if($quote->pdf_options==null || count($quote->pdf_options) != 4){            
            $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();
            $currency_id = $company->companyUser->currency_id;
            $currency = Currency::find($currency_id);
    
            $pdfOptions = [
                "allIn" =>true, 
                "showCarrier"=>true, 
                "showTotals"=>false, 
                "totalsCurrency" =>$currency];
            
            $quote->pdf_options = $pdfOptions;
            $quote->save();
        }

        if(count($quote_rate_totals) != 0){
            foreach($quote_rate_totals as $qr_total){
                if($qr_total->rate()->first() == null){
                    $qr_total->delete();
                }
            }
        }
    }
}
