<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuoteLCLTestController extends Controller
{
    public function index(Request $request)
    {
        return view('quoteLCL.index');
    }
}
