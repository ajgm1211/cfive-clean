<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\QuoteV2;
use App\Carrier;
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
use App\Container;
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
        foreach ($comps as $comp) {
            $cts = $comp->contact()->get();
            foreach ($cts as $ct) {
                array_push($contacts,['id'=>$ct->id,'name'=>$ct->getFullName()]);
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

        //OJO WITH THIS STATUS IN QUOTEV2 IS ACTUALLY AN ENUM
        $status_options = StatusQuote::get()->map(function ($status){
            return $status->only(['id','name']);
        });

        $kinds_of_cargo = CargoKind::get()->map(function ($kind_of_cargo){
            return $kind_of_cargo->only(['id','name']);
        });

        $languages = Language::get()->map(function ($language){
            return $language->only(['id','name']);
        });

        $data = compact(
            'companies',
            'contacts',
            'carriers',
            'incoterms',
            'users',
            'harbors',
            'payment_conditions',
            'terms_and_conditions',
            'delivery_types',
            'status_options',
            'kinds_of_cargo',
            'languages'
        );

        return response()->json(['data'=>$data]);
    }

    public function store(Request $request)
    {
        $company_user = Auth::user('web')->worksAt();
        $company_code = strtoupper(substr($company_user->name, 0, 2));
        $lastq = $company_user->companyQuotes()->OrderByDesc('id')->limit(1)->first();
        $newq_id = $company_code . '-' . strval((intval(str_replace($company_code. '-','',$lastq->quote_id))) +1 );

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
        
        //dd($request);

        $quote = QuoteV2::create([
            'quote_id' => $newq_id,
            'type' => $data['type'] + 1,
            'delivery_type' => $data['delivery_type'],
            'user_id' => \Auth::user()->id,
            'company_user_id' => $company_user->id,
            'company_id' => $data['company_id_quote'],
            'contact_id' => $data['contact_id'],
            'mode' => $data['mode'],
            'cargo_type' => $data['cargo_type'],
            'total_quantity' => $data['total_quantity'],
            'total_weight' => $data['total_weight'],
            'total_volume' => $data['total_volume'],
            'chargeable_weight' => $data['chargeable_weight'],
            'price_id' => $data['price_id_num'],
            'equipment' => $data['equipment'],
            'origin_address' => $data['origin_address'],
            'destination_address' => $data['destination_address'],
            'date_issued' => explode("/",$data['date'])[0],
            'validity_start' => explode("/",$data['date'])[0],
            'validity_end' => explode("/",$data['date'])[1],
            'status' => 'Draft' //cannot change as it is enum
        ]);

    }

    public function edit (Request $request, QuoteV2 $quote)
    {
        return view('quote.edit');
    }

    public function update (Request $request, QuoteV2 $quote)
    {
        $data = $request->validate([
            'delivery_type' => 'required',
            'equipment' => 'required',
            'company_id' => 'nullable',
            'contact' => 'nullable',
            'commodity' => 'nullable',
            'status' => 'required',
            'type' => 'required',
            'kind_of_cargo' => 'nullable',
            'issued' => 'required',
            'owner'=>'required',
            'payment_conditions' => 'nullable',
            'incoterm' => 'nullable',
            'language_id' => 'nullable',
            'validity' => 'required',
            //'terms_and_conditions' => 'nullable'
        ]);
        
        $quote->update([
            'delivery_type' => $data['delivery_type'],
            'equipment' => $data['equipment'], //TRANSFORM THIS DATA
            'company_id' => $data['company_id'],
            'contact' => $data['contact'],
            'commodity' => $data['commodity'],
            'status' => $data['status'],
            'type' => $data['type'], 
            'kind_of_cargo' => $data['kind_of_cargo'],
            'validity_start' => $data['issued'],
            'user_id' => $data['owner'],
            'payment_conditions' => $data['payment_conditions'],
            'incoterm_id' => $data['incoterm'],
            'validity_end' => $data['validity'], //is it the same as validity?
            //'terms_and_conditions' => $data['terms_and_conditions']
        ]);
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
