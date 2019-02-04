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
}
