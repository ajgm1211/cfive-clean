<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Quote;
use App\CompanyUser;
use App\Currency;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = CompanyUser::where('id', Auth::User()->company_user_id)->pluck('currency_id');
        $cur = Currency::where('id', $company[0])->pluck('alphacode');
        $currency = $cur[0];
        $allUsers = User::All();
        $users = $allUsers->where('type', 'subuser')->pluck('name', 'id');
        $quotes = Quote::where('company_id', Auth::User()->company_user_id)->get();
        $total = 0;
        $totalSent = 0;
        $totalWin = 0;
        $totalLost = 0;
        foreach ($quotes as $q){
            if($q->status_quote_id == 2){
                $totalSent += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if($q->status_quote_id == 5){
                $totalWin += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if($q->status_quote_id == 4){
                $totalLost += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }

            $total += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;

        }
        $totalQuotes = $quotes->count();
        $sent = $quotes->where('status_quote_id', 2)->count();
        $acepted = $quotes->where('status_quote_id', 5)->count();
        $lost = $quotes->where('status_quote_id', 4)->count();
        if($totalQuotes == 0){ $totalQuotes = 1; }
        if($total == 0){ $total = 1; }

        return view('dashboard.index',
            compact(
                'users',
                'sent',
                    'acepted',
                    'lost',
                    'totalQuotes',
                    'totalSent',
                    'totalWin',
                    'totalLost',
                    'total',
                    'currency'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        $dates = explode( " / ", $request->pick_up_date);
        $dates[0] = date("Y-m-d", strtotime($dates[0]));
        $dates[1] = date("Y-m-d", strtotime($dates[1]));

        $company = CompanyUser::where('id', Auth::User()->company_user_id)->pluck('currency_id');

        $cur = Currency::where('id', $company[0])->pluck('alphacode');
        $currency = $cur[0];
        
        $users = User::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
        
        $quotes = Quote::whereBetween('pick_up_date' ,[$dates[0], $dates[1]])->where('owner', $request->user)->get();

        $totalQuotes = $quotes->count();
        if($totalQuotes == 0){ $totalQuotes = 1; }
        $total = 0;
        $totalSent = 0;
        $totalWin = 0;
        $totalLost = 0;
        foreach ($quotes as $q){
            if($q->status_quote_id == 2){
                $totalSent += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if($q->status_quote_id == 5){
                $totalWin += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if($q->status_quote_id == 4){
                $totalLost += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }

            $total += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;

        }
        if($total == 0){ $total = 1; }
        $sent = $quotes->where('status_quote_id', 2)->count();
        $acepted = $quotes->where('status_quote_id', 5)->count();
        $lost = $quotes->where('status_quote_id', 4)->count();

        return \View::make('dashboard.index',
            compact('users',
                'sent',
                    'acepted',
                    'lost',
                    'totalQuotes',
                    'totalSent',
                    'totalWin',
                    'totalLost',
                    'total',
                    'currency'
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}