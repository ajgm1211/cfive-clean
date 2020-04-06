<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rate;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OceanFreightResource;

class OceanFreightController extends Controller
{
   	/**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, Contract $contract)
    {
        $results = Rate::filterByContract($contract->id)->filter($request);

    	return OceanFreightResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'origin' => 'required',
            'destination' => 'required'
            'carrier' => 'required',
            'forty' => 'sometimes',
			'fortyhc' => 'sometimes'
			'fortynor' => 'sometimes'
			'fortyfive' => 'sometimes',
            'currency' => 'required',
            'schedule_type' => 'sometimes',
            'transit_time' => 'present',
            'via' => 'present'
        ]);

        $contract = Contract::create([
            'name' => $data['name'],
            'number' => null,
            'company_user_id' => $company_user_id,
            'account_id' => null,
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => $data['status'],
            'gp_container_id' => $data['gp_container'],
            'remarks' => $data['remarks']
        ]);

        $contract->ContractCarrierSync($data['carriers']);

        return new ContractResource($contract);
    }
}
