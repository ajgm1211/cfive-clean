<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SearchRate;

class SearchController extends Controller
{
  public function index()
  {        
    $searchRates = SearchRate::with('search_ports')->get();
    return view('search/index', compact('searchRates'));

  }
  public function listar()
  {        
    $searchRates = SearchRate::where('type','FCL')->with('search_ports')->get();
    $searchRatesLCL = SearchRate::where('type','LCL')->with('search_ports')->get();
    
   
    return view('search/history', compact('searchRates','searchRatesLCL'));

  }
}
