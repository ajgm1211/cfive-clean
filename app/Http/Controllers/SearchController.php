<?php

namespace App\Http\Controllers;

use App\SearchRate;
use Yajra\DataTables\DataTables;

class SearchController extends Controller
{
    public function index()
    {
        $searchRates = SearchRate::with('search_ports')->get();
        return view('search/index', compact('searchRates'));

    }
    public function listar()
    {

        return view('search/history');

    }

    public function getListFCL()
    {
        $company_user_id = \Auth::user()->company_user_id;
        $container = array();
        if (\Auth::user()->hasRole('admin')) {
            dd('here');
            $searchRates = SearchRate::where('type', 'FCL')->with('search_ports')->get();
        } else {
            $searchRates = SearchRate::where('type', 'FCL')->where('company_user_id', $company_user_id)->with('search_ports')->get();
        }
        return DataTables::of($searchRates)

            ->editColumn('Usuario', function ($searchRates) {
                return $searchRates->user->name;
            })
            ->editColumn('pick_up', function ($searchRates) {
                return $searchRates->pick_up_date;
            })
            ->editColumn('equipment', function ($searchRates) {

                return str_replace(["[", "]", "\""], ' ', $searchRates->equipment);
            })
            ->editColumn('search_date', function ($searchRates) {
                return $searchRates->created_at;
            })
            ->editColumn('originPort', function ($searchRates) {

                return str_replace(["[", "]", "\""], ' ', $searchRates->search_ports->pluck('portOrig')->unique()->pluck('name'));
            })
            ->editColumn('destinationPort', function ($searchRates) {
                return str_replace(["[", "]", "\""], ' ', $searchRates->search_ports->pluck('portDest')->unique()->pluck('name'));
            })
            ->editColumn('deliv', function ($searchRates) {
                return $searchRates->pick_up_date;
            })
            ->editColumn('direct', function ($searchRates) {
                return ($searchRates->direction == 1) ? 'export' : 'import';
            })
            ->editColumn('company', function ($searchRates) {
                return $searchRates->company->name;
            })->make(true);
    }
    public function getListLCL()
    {
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('admin')) {
            $searchRatesLCL = SearchRate::where('type', 'LCL')->with('search_ports')->get();
        } else {
            $searchRatesLCL = SearchRate::where('type', 'LCL')->where('company_user_id', $company_user_id)->with('search_ports')->get();
        }
        return DataTables::of($searchRatesLCL)

            ->editColumn('Usuario', function ($searchRatesLCL) {
                return $searchRatesLCL->user->name;
            })
            ->editColumn('pick_up', function ($searchRatesLCL) {
                return $searchRatesLCL->pick_up_date;
            })
            ->editColumn('search_date', function ($searchRatesLCL) {
                return $searchRatesLCL->created_at;
            })
            ->editColumn('originPort', function ($searchRatesLCL) {

                return str_replace(["[", "]", "\""], ' ', $searchRatesLCL->search_ports->pluck('portOrig')->unique()->pluck('name'));
            })
            ->editColumn('destinationPort', function ($searchRatesLCL) {
                return str_replace(["[", "]", "\""], ' ', $searchRatesLCL->search_ports->pluck('portDest')->unique()->pluck('name'));
            })
            ->editColumn('deliv', function ($searchRatesLCL) {
                return $searchRatesLCL->pick_up_date;
            })
            ->editColumn('direct', function ($searchRatesLCL) {
                return ($searchRatesLCL->direction == 1) ? 'export' : 'import';
            })
            ->editColumn('company', function ($searchRatesLCL) {
                return $searchRatesLCL->company->name;
            })->make(true);

    }
}
