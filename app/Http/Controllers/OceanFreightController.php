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
    public function store(Request $request, Contract $contract)
    {
        $vdata = [
            'origin' => 'required',
            'destination' => 'required'
            'carrier' => 'required',
            'currency' => 'required',
            'schedule_type' => 'sometimes|nullable',
            'transit_time' => 'sometimes|nullable',
            'via' => 'sometimes|nullable',
            'containers' => 'containers'
        ];

        $data = $this->validateContainers($request, $vdata, $contract);
        
        $prepared_data = [
            'origin_port' => $data['origin'],
            'destiny_port' => $data['destination'],
            'carrier_id' => $data['carrier'],
            'contract_id' => $contract->id,
            'currency_id' => $data['currency'],
            'schedule_type_id' => $data['schedule_type'],
            'transit_time' => $data['transit_time'],
            'via' => $data['via']
        ];

        $contract = Rate::create($prepared_data);

        return new OceanFreightResource($contract);
    }

    public function validateContainers($request, $vdata, $contract){
    	$container_code = $contract->group_containers->code;

    	switch ($container_code) {
    		case 'dry':
    			$vdata['20DV'] => 'sometimes|nullable';
    			$vdata['40DV'] => 'sometimes|nullable';
    			$vdata['40HC'] => 'sometimes|nullable';
    			$vdata['45HC'] => 'sometimes|nullable';
    			$vdata['40NOR'] => 'sometimes|nullable';
    			break;
    		case 'refeer':
    			$vdata['20RF'] => 'sometimes|nullable';
    			$vdata['40RF'] => 'sometimes|nullable';
    			$vdata['40HCRF'] => 'sometimes|nullable';
    			break;
    		case 'opentop':
    			$vdata['20OT'] => 'sometimes|nullable';
    			$vdata['40OT'] => 'sometimes|nullable';
    			break;
    		case 'flatrack':
    			$vdata['20FR'] => 'sometimes|nullable';
    			$vdata['40FR'] => 'sometimes|nullable';
    			break;
    	}

    	return $request->validate($vdata);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
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

        $contract = Contract::update([
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
