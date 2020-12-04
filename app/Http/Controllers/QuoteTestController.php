<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuoteTestController extends Controller
{
    public function index(Request $request)
    {
        return view('quote.index');
    }
}
