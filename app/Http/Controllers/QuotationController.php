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
use App\Language;
use App\Currency;
use App\Container;
use App\Charge;
use App\CalculationType;
use App\Http\Resources\QuotationResource;
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
            return $carrier->only(['id', 'name']);
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
          return $harbor->only(['id', 'display_name']);
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

        /*$languages = Language::get()->map(function ($language){
            return $language->only(['id','name']);
        });*/

        $currency = Currency::get()->map(function ($curr){
            return $curr->only(['id','alphacode']);
        });

        $containers = Container::all();

        $calculationtypes = CalculationType::get()->map(function ($ctype){
            return $ctype->only(['id','name']);
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
            'calculationtypes'
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
                'equipment' => 'required',
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
            'equipment' => "[\"".implode("\",\"",$data['equipment'])."\"]",
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
                }
            }
        } else {
            $origins = $quote->originDest($data['originport']);
            $destinations = $quote->originDest($data['destinyport']);
        
            foreach($origins as $orig){
                foreach($destinations as $dest){
                    $rate = AutomaticRate::create([
                        'quote_id' => $quote->id,
                        'contract' => '',
                        'validity_start' => explode("/",$data['date'])[0],
                        'validity_end' => explode("/",$data['date'])[1],
                        'origin_port_id' => $orig,
                        'destination_port_id' => $dest,
                        'currency_id' => $company_user->currency_id       
                    ]);

                    $freight = Charge::create([
                        'automatic_rate_id' => $rate->id,
                        'type_id' => '3',
                        'calculation_type_id' => '5',
                        'currency_id' => $rate->currency_id,
                    ]);
                }
            }
        }

        return redirect()->action('QuotationController@edit', $quote) ;
    }

    public function edit (Request $request, QuoteV2 $quote)
    {
        return view('quote.edit');
    }

    public function update (Request $request, QuoteV2 $quote)
    {   
        $form_keys = $request->input('keys');

        if(!in_array('terms_and_conditions',$form_keys)){
            $data = $request->validate([
                'delivery_type' => 'required',
                'equipment' => 'required',
                'status' => 'required',
                'type' => 'required',
                'validity_start' => 'required',
                'user_id'=>'required',
                'validity_end' => 'required',
            ]);
        } else {
            $data = [];
        }
        
        foreach($form_keys as $fkey){
            if(!in_array($fkey,$data) && $fkey != 'keys'){
                $data[$fkey] = $request->input($fkey);
            }
        };

        foreach(array_keys($data) as $key){
            if ($key=='equipment'){
                $data[$key] = $quote->getContainerArray($data[$key]);
            }
            $quote->update([$key=>$data[$key]]);
        }
    }

    //Need funcs to update remarks and update terms?

    public function destroy(QuoteV2 $quote)
    {
        $quote->delete();

        return response()->json(null, 204);
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
        DB::table('quote_v2s')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
