<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContractResource;

class ContractController extends Controller
{
    public function index(Request $request)
    {
    	return view('contract.index');
    }

    public function list(Request $request)
    {
    	$results = Contract::filterByCurrentCompany()
    					->with('carriers.carrier','direction');

    	return ContractResource::collection($results->paginate(10));
    }
}
