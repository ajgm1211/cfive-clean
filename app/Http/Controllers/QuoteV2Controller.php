<?php

namespace App\Http\Controllers;

use App\AutomaticRate;
use App\CalculationType;
use App\Charge;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\Harbor;
use App\Incoterm;
use App\Price;
use App\Quote;
use App\QuoteV2;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;

class QuoteV2Controller extends Controller
{
    public function index(Request $request){
        $company_user='';
        $currency_cfg = '';
        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = QuoteV2::where('user_id',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }else{
            $quotes = QuoteV2::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }
        $companies = Company::pluck('business_name','id');
        $harbors = Harbor::pluck('display_name','id');
        $countries = Country::pluck('name','id');
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        return view('quotesv2/index', ['companies' => $companies,'quotes'=>$quotes,'countries'=>$countries,'harbors'=>$harbors,'currency_cfg'=>$currency_cfg]);
    }

    public function LoadDatatableIndex(){

        $company_user_id = \Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $quotes = QuoteV2::where('user_id',\Auth::user()->id)->whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }else{
            $quotes = QuoteV2::whereHas('user', function($q) use($company_user_id){
                $q->where('company_user_id','=',$company_user_id);
            })->orderBy('created_at', 'desc')->get();
        }

        $colletions = collect([]);
        foreach($quotes as $quote){
            $custom_id      = '---';
            $company  = '---';
            $origin         = '';
            $destination    = '';
            if(isset($quote->company)){
                $custom_id  = $quote->quote_id;
                $company  = $quote->company->business_name;
            }

            if(!$quote->origin_address){
                $origin = $quote->origin_port->display_name;
            } else {
                $origin = $quote->origin_address;
            }

            if(!$quote->destination_address){
                $destination = $quote->destination_port->display_name;
            } else {
                $destination = $quote->destination_address;
            }

            $data = [
                'id'            => $quote->id,
                'custom_id'     => $custom_id,
                'idSet'         => setearRouteKey($quote->id),
                'client'        => $company,
                'created'       => date_format($quote->created_at, 'M d, Y H:i'),
                'user'          => $quote->user->name.' '.$quote->user->lastname,
                'origin'        => $origin,
                'destination'   => $destination,
                'type'          => $quote->type,
            ];
            $colletions->push($data);
        }
        return DataTables::of($colletions)
            ->addColumn('type', function ($colletion) {
                return '<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
            })->addColumn('action',function($colletion){
                return
                    '<button class="btn btn-outline-light  dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     Options
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
                     <a class="dropdown-item" href="/v2/quotes/show/'.$colletion['idSet'].'">
                        <span>
                           <i class="la la-eye"></i>
                           &nbsp;
                           Show
                        </span>
                     </a>      
                     <a href="/v2/quotes/'.$colletion['idSet'].'/edit" class="dropdown-item" >
                        <span>
                           <i class="la la-edit"></i>
                           &nbsp;
                           Edit
                        </span>
                     </a>
                     <a href="/quotes/duplicate/'.$colletion['idSet'].'" class="dropdown-item" >
                        <span>
                           <i class="la la-plus"></i>
                           &nbsp;
                           Duplicate
                        </span>
                     </a>
                     <a href="#" class="dropdown-item" id="delete-quote" data-quote-id="'.$colletion['id'].'" >
                        <span>
                           <i class="la la-eraser"></i>
                           &nbsp;
                           Delete
                        </span>
                     </a>
                  </div>';
            })
            ->editColumn('id', 'ID: {{$id}}')->make(true);
    }

    public function show($id)
    {

        $id = obtenerRouteKey($id);
        $company_user_id = \Auth::user()->company_user_id;
        $quote = QuoteV2::findOrFail($id);
        $rates = AutomaticRate::where('quote_id',$quote->id)->get();
        $origin_charges = Charge::where(['automatic_rate_id'=>$quote->rate->id,'type_id'=>1])->get();
        $destination_charges = Charge::where(['automatic_rate_id'=>$quote->rate->id,'type_id'=>2])->get();
        $freight_charges = Charge::where(['automatic_rate_id'=>$quote->rate->id,'type_id'=>3])->get();
        $companies = Company::where('company_user_id',$company_user_id)->pluck('business_name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $incoterms = Incoterm::pluck('name','id');
        $users = User::where('company_user_id',$company_user_id)->pluck('name','id');
        $prices = Price::where('company_user_id',$company_user_id)->pluck('name','id');
        $currencies = Currency::pluck('alphacode','id');
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);
        $equipmentHides = $this->hideContainer($quote->equipment);
        $calculation_types = CalculationType::pluck('name','id');

        return view('quotesv2/show', compact('quote','companies','incoterms','users','prices','contacts','currencies','currency_cfg','equipmentHides','freight_charges','origin_charges','destination_charges','calculation_types','rates'));
    }

    public function updateQuoteDetails(Request $request)
    {
        QuoteV2::find($request->pk)->update([$request->name => $request->value]);

        return response()->json(['success'=>'done']);
    }

    public function update(Request $request,$id)
    {

        $validation = explode('/',$request->validity);
        $validity_start = $validation[0];
        $validity_end = $validation[1];

        $quote=QuoteV2::find($id);
        $quote->quote_id=$request->quote_id;
        $quote->type=$request->type;
        $quote->company_id=$request->company_id;
        $quote->contact_id=$request->contact_id;
        $quote->delivery_type=$request->delivery_type;
        $quote->date_issued=$request->date_issued;
        $quote->incoterm_id=$request->incoterm_id;
        $quote->equipment=$request->equipment;
        $quote->validity_start=$validity_start;
        $quote->validity_end=$validity_end;
        $quote->user_id=$request->user_id;
        $quote->status=$request->status;
        $quote->update();

        $contact_name=$quote->contact->first_name.' '.$quote->contact->last_name;

        return response()->json(['message'=>'Ok','quote'=>$quote,'contact_name'=>$contact_name]);
    }

    public function duplicate(Request $request, $id){

        $id = obtenerRouteKey($id);
        $quote=QuoteV2::find($id);
        $quote_duplicate = new QuoteV2();
        $quote_duplicate->user_id=\Auth::id();
        $quote_duplicate->company_user_id=\Auth::user()->company_user_id;
        $quote_duplicate->quote_id=$this->idPersonalizado();
        $quote_duplicate->incoterm_id=$quote->incoterm_id;
        $quote_duplicate->type=$quote->type;
        $quote_duplicate->delivery_type=$quote->delivery_type;
        $quote_duplicate->currency_id=$quote->currency_id;
        $quote_duplicate->contact_id=$quote->contact_id;
        $quote_duplicate->company_id=$quote->company_id;
        $quote_duplicate->validity_start=$quote->validity_start;
        $quote_duplicate->validity_end=$quote->validity_end;
        $quote_duplicate->equipment=$quote->equipment;
        $quote_duplicate->status=$quote->status;
        $quote_duplicate->date_issued=$quote->date_issued;
        if($quote->origin_address){
            $quote_duplicate->origin_address=$quote->origin_address;
        }
        if($quote->destination_address){
            $quote_duplicate->destination_address=$quote->destination_address;
        }
        if($quote->origin_port_id){
            $quote_duplicate->origin_port_id=$quote->origin_port_id;
        }
        if($quote->destination_port_id){
            $quote_duplicate->destination_port_id=$quote->destination_port_id;
        }
        if($quote->price_id){
            $quote_duplicate->price_id=$quote->price_id;
        }
        if($quote->custom_quote_id){
            $quote_duplicate->custom_quote_id=$quote->custom_quote_id;
        }

        $quote_duplicate->save();

        $rates = AutomaticRate::where('quote_id',$quote->id)->get();

        foreach ($rates as $rate){

            $rate_duplicate = new AutomaticRate();
            $rate_duplicate->quote_id=$quote_duplicate->id;
            $rate_duplicate->contract=$rate->contract;
            $rate_duplicate->validity_start=$rate->validity_start;
            $rate_duplicate->validity_end=$rate->validity_end;
            $rate_duplicate->origin_port_id=$rate->origin_port_id;
            $rate_duplicate->destination_port_id=$rate->destination_port_id;
            $rate_duplicate->carrier_id=$rate->carrier_id;
            $rate_duplicate->rates=$rate->rates;
            $rate_duplicate->markups=$rate->markups;
            $rate_duplicate->total=$rate->total;
            $rate_duplicate->currency_id=$rate->currency_id;
            $rate_duplicate->save();
            
            $charges=Charge::where('automatic_rate_id',$rate->id)->get();


            foreach ($charges as $charge){
                $charge_duplicate = new Charge();
                $charge_duplicate->automatic_rate_id=$rate_duplicate->id;
                $charge_duplicate->type_id=$charge->type_id;
                $charge_duplicate->surcharge_id=$charge->surcharge_id;
                $charge_duplicate->calculation_type_id=$charge->calculation_type_id;
                $charge_duplicate->amount=$charge->amount;
                $charge_duplicate->markups=$charge->markups;
                $charge_duplicate->total=$charge->total;
                $charge_duplicate->currency_id=$charge->currency_id;
                $charge_duplicate->save();
            }

        }

        if($request->ajax()){
            return response()->json(['message' => 'Ok']);
        }else{
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Quote duplicated successfully!');
            return redirect()->action('QuoteV2Controller@show', setearRouteKey($quote_duplicate->id));
        }
    }

    public function idPersonalizado(){
        $user_company = CompanyUser::where('id',\Auth::user()->company_user_id)->first();
        $iniciales =  strtoupper(substr($user_company->name,0, 2));
        $quote = QuoteV2::where('company_user_id',$user_company->id)->orderBy('created_at', 'desc')->first();

        if($quote == null){
            $iniciales = $iniciales."-1";
        }else{

            $numeroFinal = explode('-',$quote->quote_id);

            $numeroFinal = $numeroFinal[1] +1;

            $iniciales = $iniciales."-".$numeroFinal;
        }
        return $iniciales;
    }

    public function hideContainer($equipmentForm){
        $equipment = new Collection();
        $hidden20 = 'hidden';
        $hidden40 = 'hidden';
        $hidden40hc = 'hidden';
        $hidden40nor = 'hidden';
        $hidden45 = 'hidden';
        // Clases para reordenamiento de la tabla y ajuste
        $originClass = 'col-md-2';
        $destinyClass = 'col-md-1';
        $dataOrigDest = 'col-md-3';
        $countEquipment = count($equipmentForm);
        $countEquipment = 5 - $countEquipment;
        if($countEquipment == 1 ){
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-1';
            $dataOrigDest = 'col-md-4';
        }
        if($countEquipment == 2 ){
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-5';
        }
        if($countEquipment == 3){
            $originClass = 'col-md-4';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-6';
        }
        if($countEquipment == 4){
            $originClass = 'col-md-5';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-7';
        }
        foreach($equipmentForm as $val){
            if($val == '20'){
                $hidden20 = '';
            }
            if($val == '40'){
                $hidden40 = '';
            }
            if($val == '40HC'){
                $hidden40hc = '';
            }
            if($val == '40NOR'){
                $hidden40nor = '';
            }
            if($val == '45'){
                $hidden45 = '';
            }
        }
        $equipment->put('originClass',$originClass);
        $equipment->put('destinyClass',$destinyClass);
        $equipment->put('dataOrigDest',$dataOrigDest);
        $equipment->put('20',$hidden20);
        $equipment->put('40',$hidden40);
        $equipment->put('40hc',$hidden40hc);
        $equipment->put('40nor',$hidden40nor);
        $equipment->put('45',$hidden45);
        return($equipment);
    }
}
