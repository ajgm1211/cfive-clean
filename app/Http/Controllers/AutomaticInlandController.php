<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AutomaticInlandResource;
use App\QuoteV2;
use App\AutomaticInland;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AutomaticInlandController extends Controller
{
    public function list(Request $request, QuoteV2 $quote)
    {   

        $results = AutomaticInland::filterByQuote($quote->id)->filter($request);
        
        return AutomaticInlandResource::collection($results);
    }
}
