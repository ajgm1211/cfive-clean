<?php

namespace App\Http\Controllers;

use App\InlandRange;
use App\Inland;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\InlandRangeResource;
use App\Container;
use Illuminate\Support\Collection as Collection;

class InlandRangeController extends Controller
{

    public function list(Request $request, Inland $inland)
    {
        $results = InlandRange::filterByInland($inland->id)->filter($request);
    	return InlandRangeResource::collection($results);
    }

    public function deleteRange(InlandRange $range)
    {
        
        $range->delete();
    }

}
