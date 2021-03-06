<?php

namespace App\Http\Controllers;

use App\AutomaticRate;
use App\CompanyUser;
use App\Container;
use App\Currency;
use App\QuoteV2;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $containers = Container::all();
        $cur = Currency::where('id', $company[0])->pluck('alphacode');
        $currency = $cur[0];

        if (Auth::user()->type == 'admin') {
            $users = User::pluck('name', 'id');
            $quotes = QuoteV2::all();
        } elseif (Auth::user()->type == 'company') {
            $users = User::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
            $quotes = QuoteV2::where('company_user_id', \Auth::User()->company_user_id)->get();
        } else {
            $users = User::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
            $quotes = QuoteV2::where('user_id', \Auth::id())->get();
        }

        $total = 0;
        $totalSent = 0;
        $totalDraft = 0;
        $totalNegotiated = 0;
        $totalWon = 0;
        $totalLost = 0;

        foreach ($quotes as $q) {
            $totalRate = 0;

            $charges = AutomaticRate::where('quote_id', $q->id)->with('charge')->get();

            foreach ($charges as $charge) {
                foreach ($charge->charge as $item) {
                    $rate = 0;
                    $markup = 0;

                    $exchange = ratesCurrencyFunction($item->currency_id, @Auth::user()->companyUser->currency->alphacode);

                    $amounts = json_decode($item->amount, true);
                    $markups = json_decode($item->markups, true);

                    $amounts = processOldDryContainers($amounts, 'amounts');
                    $markups = processOldDryContainers($markups, 'markups');

                    foreach ($containers as $container) {
                        $rate = (int) @$amounts['c'.$container->code];
                        $markup = (int) @$markups['m'.$container->code];
                        $totalRate += number_format(($rate + $markup) / $exchange, 2, '.', '');
                    }

                    if ($q->status == 'Draft') {
                        $totalDraft += $totalRate;
                    }
                    if ($q->status == 'Sent') {
                        $totalSent += $totalRate;
                    }
                    if ($q->status == 'Win') {
                        $totalWon += $totalRate;
                    }
                }
            }
        }

        $totalQuotes = $quotes->count();
        $draft = $quotes->where('status', 'Draft')->count();
        $sent = $quotes->where('status', 'Sent')->count();
        $won = $quotes->where('status', 'Win')->count();

        if ($totalQuotes == 0) {
            $totalQuotes = 1;
        }
        if ($total == 0) {
            $total = 1;
        }
        $users->prepend('Select an option');

        return view(
            'dashboard.index',
            compact(
                'users',
                'draft',
                'sent',
                'won',
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
        $dates = explode(' / ', $request->pick_up_date);
        $dates[0] = date('Y-m-d', strtotime($dates[0]));
        $dates[1] = date('Y-m-d', strtotime($dates[1]));

        $pick_up_dates = collect(['start_date' => $dates[0], 'end_date' => $dates[1]]);
        $company = CompanyUser::where('id', Auth::User()->company_user_id)->pluck('currency_id');
        $containers = Container::all();

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
                ->whereDate('created_at', '<=', $dates[1])->where('user_id', $request->user)->get();
        } else {
            if (Auth::user()->type == 'subuser') {
                $quotes = QuoteV2::whereDate('created_at', '>=', $dates[0])
                    ->whereDate('created_at', '<=', $dates[1])->where('user_id', \Auth::id())->get();
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
        $totalWon = 0;

        foreach ($quotes as $q) {
            $totalRate = 0;
            $charges = AutomaticRate::where('quote_id', $q->id)->with('charge')->get();

            foreach ($charges as $charge) {
                foreach ($charge->charge as $item) {
                    $exchange = ratesCurrencyFunction($item->currency_id, Auth::user()->companyUser->currency->alphacode);

                    $amounts = json_decode($item->amount, true);
                    $markups = json_decode($item->markups, true);

                    $amounts = processOldDryContainers($amounts, 'amounts');
                    $markups = processOldDryContainers($markups, 'markups');

                    foreach ($containers as $container) {
                        $totalRate += number_format((@$amounts['c'.$container->code] + @$markups['m'.$container->code]) / $exchange, 2, '.', '');
                    }

                    if ($q->status == 'Draft') {
                        $totalDraft += $totalRate;
                    }
                    if ($q->status == 'Sent') {
                        $totalSent += $totalRate;
                    }
                    if ($q->status == 'Win') {
                        $totalWon += $totalRate;
                    }
                }
            }
        }

        if ($total == 0) {
            $total = 1;
        }

        $draft = $quotes->where('status', 'Draft')->count();
        $sent = $quotes->where('status', 'Sent')->count();
        $won = $quotes->where('status', 'Win')->count();

        $users->prepend('Select an option');

        $pick_up_date = $request->pick_up_date;

        return \View::make(
            'dashboard.index',
            compact(
                'users',
                'user',
                'pick_up_dates',
                'pick_up_date',
                'draft',
                'sent',
                'won',
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
