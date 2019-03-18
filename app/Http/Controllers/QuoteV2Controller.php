<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyUser;
use App\Country;
use App\Currency;
use App\Harbor;
use App\Quote;
use App\QuoteV2;
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
                     <a class="dropdown-item" href="/quotes/'.$colletion['idSet'].'">
                        <span>
                           <i class="la la-eye"></i>
                           &nbsp;
                           Show
                        </span>
                     </a>      
                     <a href="/quotes/'.$colletion['idSet'].'/edit" class="dropdown-item" >
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
        dd($colletions);
    }
}
