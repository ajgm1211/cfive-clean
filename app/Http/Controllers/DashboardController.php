<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\QuoteV2;
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

        $company = CompanyUser::where('id', \Auth::User()->company_user_id)->pluck('currency_id');
        $cur = Currency::where('id', $company[0])->pluck('alphacode');
        $currency = $cur[0];

        if (Auth::user()->type == 'admin') {
            $users = User::pluck('name', 'id');
            $quotes = QuoteV2::all();
        } else if (Auth::user()->type == 'company') {
            $users = User::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
            $quotes = QuoteV2::where('company_user_id', \Auth::User()->company_user_id)->get();
        } else {
            $users = User::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
            $quotes = QuoteV2::where('owner', \Auth::id())->get();
        }

        $total = 0;
        $totalSent = 0;
        $totalDraft = 0;
        $totalNegotiated = 0;
        $totalWon = 0;
        $totalLost = 0;

        foreach ($quotes as $q) {
            if ($q->status_quote_id == 1) {
                $totalDraft += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 2) {
                $totalSent += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 3) {
                $totalNegotiated += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 4) {
                $totalLost += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 5) {
                $totalWon += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            $total += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
        }

        $totalQuotes = $quotes->count();
        $draft = $quotes->where('status_quote_id', 1)->count();
        $sent = $quotes->where('status_quote_id', 2)->count();
        $negotiated = $quotes->where('status_quote_id', 3)->count();
        $lost = $quotes->where('status_quote_id', 4)->count();
        $won = $quotes->where('status_quote_id', 5)->count();
        if ($totalQuotes == 0) {
            $totalQuotes = 1;
        }
        if ($total == 0) {
            $total = 1;
        }

        return view(
            'dashboard.index',
            compact(
                'users',
                'draft',
                'sent',
                'negotiated',
                'won',
                'lost',
                'totalQuotes',
                'totalDraft',
                'totalSent',
                'totalNegotiated',
                'totalWon',
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
        $dates = explode(" / ", $request->pick_up_date);
        $dates[0] = date("Y-m-d", strtotime($dates[0]));
        $dates[1] = date("Y-m-d", strtotime($dates[1]));

        $pick_up_dates = collect(['start_date' => $dates[0], 'end_date' => $dates[1]]);
        $company = CompanyUser::where('id', Auth::User()->company_user_id)->pluck('currency_id');

        $cur = Currency::where('id', $company[0])->pluck('alphacode');
        $currency = $cur[0];

        $user = User::find($request->user);

        if (Auth::user()->type == 'admin') {
            $users = User::pluck('name', 'id');
        } else {
            $users = User::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
        }

        if ($request->user) {
            $quotes = QuoteV2::whereDate('created_at', '>=', $dates[0])
                ->whereDate('created_at', '<=', $dates[1])->where('owner', $request->user)->get();
        } else {
            if (Auth::user()->type == 'subuser') {
                $quotes = QuoteV2::whereDate('created_at', '>=', $dates[0])
                    ->whereDate('created_at', '<=', $dates[1])->where('owner', \Auth::id())->get();
            } else {
                $quotes = QuoteV2::whereDate('created_at', '>=', $dates[0])
                    ->whereDate('created_at', '<=', $dates[1])->where('company_user_id', \Auth::user()->company_user_id)->get();
            }
        }

        $totalQuotes = $quotes->count();

        if ($totalQuotes == 0) {
            $totalQuotes = 1;
        }

        $total = 0;
        $totalDraft = 0;
        $totalSent = 0;
        $totalNegotiated = 0;
        $totalWon = 0;
        $totalLost = 0;

        foreach ($quotes as $q) {
            if ($q->status_quote_id == 1) {
                $totalDraft += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 2) {
                $totalSent += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 3) {
                $totalNegotiated += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 4) {
                $totalLost += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            if ($q->status_quote_id == 5) {
                $totalWon += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
            }
            $total += $q->sub_total_origin + $q->sub_total_freight + $q->sub_total_destination;
        }

        if ($total == 0) {
            $total = 1;
        }

        $draft = $quotes->where('status_quote_id', 1)->count();
        $sent = $quotes->where('status_quote_id', 2)->count();
        $negotiated = $quotes->where('status_quote_id', 3)->count();
        $lost = $quotes->where('status_quote_id', 4)->count();
        $won = $quotes->where('status_quote_id', 5)->count();

        return \View::make(
            'dashboard.index',
            compact(
                'users',
                'user',
                'pick_up_dates',
                'draft',
                'sent',
                'negotiated',
                'won',
                'lost',
                'totalQuotes',
                'totalDraft',
                'totalSent',
                'totalNegotiated',
                'totalWon',
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
