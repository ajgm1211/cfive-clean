<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CalculationType;
use App\CalculationTypeLcl;
use App\Http\Resources\CalculationTypeResource;

class ApiCalculationTypeController extends Controller
{    
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index($type)
    {

        if(strtolower($type)!='fcl' && strtolower($type)!='lcl'){
            return response()->json(['Only FCL or LCL is allowed as parameter']);
        }

        if(strtolower($type) == 'fcl'){
            $query = CalculationType::select('id','name','display_name','unique_code as code')->get();
        }

        if(strtolower($type) == 'lcl'){
            $query = CalculationTypeLcl::select('id','name','display_name','unique_code as code')->get();
        }

        return CalculationTypeResource::collection($query);
    }
}
