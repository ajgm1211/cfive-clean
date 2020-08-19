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

        $contacts = Contact::where('company_id','=',$company_user_id)->get()->map(function ($contact){
            return $contact->only(['id','first_name','last_name']);
        });

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


        $data = compact(
            'companies',
            'contacts',
            'carriers',
            'incoterms',
            'users',
            'harbors',
            'payment_conditions',
            'terms_and_conditions'
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
            'company' => 'nullable',
            'contact_id_num' => 'nullable',
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
            //NOT LINKED TO ANYTHING
            'chargeOrigin' => 'nullable',
            'chargeDestination' => 'nullable',
            'type_load_cargo' => 'required',
            'total_pallets' => 'nullable',
            'total_packages' => 'nullable',
            'quantity' => 'nullable',
            'height' => 'nullable',
            'width' => 'nullable',
            'large' => 'nullable',
            'weight' => 'nullable',
            'volume' => 'nullable',
            'total_quantity_pkg' => 'nullable',
            'total_weight_pkg' => 'nullable',
            'total_volume_pkg' => 'nullable',
            'carriers' => 'required'
        ]);

        $quote = QuoteV2::create([
            'quote_id' => $newq_id,
            'type' => $data['type'] + 1,
            'delivery_type' => $data['delivery_type'],
            'user_id' => \Auth::user()->id,
            'company_user_id' => $company_user->id,
            'mode' => $data['mode'],
            'cargo_type' => $data['cargo_type'],
            'total_quantity' => $data['total_quantity'],
            'total_weight' => $data['total_weight'],
            'total_volume' => $data['total_volume'],
            'chargeable_weight' => $data['chargeable_weight'],
            //'price_id' => $data['price_id'],
            'equipment' => $data['equipment'], //TRANSFORM THIS DATA
            'origin_address' => $data['origin_address'],
            'destination_address' => $data['destination_address'],
            'date_issued' => explode("/",$data['date'])[0], //is it the same as validity?
            'validity_start' => explode("/",$data['date'])[0],
            'validity_end' => explode("/",$data['date'])[1],
            'status' => 'Draft' //confirm
            //add inland options if present
        ]);

    }

    public function edit (Request $request, QuoteV2 $quote)
    {
        return view('quote.edit');
    }

    public function update (Request $request, QuoteV2 $quote)
    {
        // SAME FORM AS IN STORE
        $data = $request->validate([
            'type' => 'required',
            'direction' => 'required',
            'delivery_type' => 'required',
            'equipment' => 'required',
            'company' => 'nullable',
            'contact' => 'nullable',
            'price_id' => 'nullable',
            'origin_port' => 'required',
            'destination_port' => 'required',
            'origin_address' => 'nullable',
            'destination_address' => 'nullable',
            'validity' => 'required',
            'expired' => 'required',
            'carrier' => 'required'
        ]);

        $quote->update([
            'type' => $data['type'],
            'delivery_type' => $data['delivery_type'],
            'mode' => $data['direction'],
            'price_id' => $data['price_id'],
            'equipment' => $data['equipment'], //TRANSFORM THIS DATA
            'origin_address' => $data['origin_port'],
            'destination_address' => $data['destination_port'],
            'date_issued' => $data['validity'], //is it the same as validity?
            'validity_start' => $data['validity'],
            'validity_end' => $data['expired'],
            'status' => 'Draft' //confirm
            //add inland options if present
            // Where are carriers?
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

    //FUNCTION NOT READY!! see model
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
