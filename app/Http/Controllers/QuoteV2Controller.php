<?php

namespace App\Http\Controllers;

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
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -136px, 0px); top: 0px; left: 0px; will-change: transform;">
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
        $companies = Company::where('company_user_id',$company_user_id)->pluck('business_name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $incoterms = Incoterm::pluck('name','id');
        $users = User::where('company_user_id',$company_user_id)->pluck('name','id');
        $prices = Price::where('company_user_id',$company_user_id)->pluck('name','id');

        return view('quotesv2/show', compact('quote','companies','incoterms','users','prices','contacts'));
    }

    public function updateQuoteDetails(Request $request)
    {
        QuoteV2::find($request->pk)->update([$request->name => $request->value]);

        return response()->json(['success'=>'done']);
    }

    public function update(Request $request,$id)
    {
        $quote=QuoteV2::find($id);
        $quote->quote_id=$request->quote_id;
        $quote->type=$request->type;
        $quote->company_id=$request->company_id;
        $quote->contact_id=$request->contact_id;
        $quote->delivery_type=$request->delivery_type;
        $quote->date_issued=$request->date_issued;
        $quote->incoterm_id=$request->incoterm_id;
        $quote->user_id=$request->user_id;
        $quote->status=$request->status;
        $quote->update();

        return response()->json(['message'=>'Ok','quote'=>$quote]);
    }
}
