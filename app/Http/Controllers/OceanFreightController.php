<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rate;
use App\Container;
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
        $results = Rate::filter($request);

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
        $data = $this->validateData($request, $contract);

        $prepared_data = $this->preparedData($data, $contract);

        $rate = Rate::create($prepared_data);

        return new OceanFreightResource($rate);
    }

    public function preparedData($request, $contract)
    {
        $prepared_data = [
            'origin_port' => $data['origin'],
            'destiny_port' => $data['destination'],
            'carrier_id' => $data['carrier'],
            'contract_id' => $contract->id,
            'currency_id' => $data['currency'],
            'schedule_type_id' => isset($data['schedule_type']) ? $data['schedule_type'] : null,
            'transit_time' => isset($data['transit_time']) ? $data['transit_time'] : null,
            'via' => isset($data['via']) ? $data['via'] : null
        ];

        $containers = [];
        
        if($contract->isDRY()){

            $prepared_data['twuenty'] = $data['rates_20V'],
            $prepared_data['forty'] = $data['rates_40V'],
            $prepared_data['fortyhc'] = $data['rates_40HC'],
            $prepared_data['fortynor'] = $data['rates_20NOR'],
            $prepared_data['fortyfive'] = $data['rates_45HC'],

        } else {

            $prepared_data['twuenty'] = '-',
            $prepared_data['forty'] = '-',
            $prepared_data['fortyhc'] = '-',
            $prepared_data['fortynor'] = '-',
            $prepared_data['fortyfive'] = '-'

            foreach ($data as $key => $value) {

            if(strpos($key, "rates_") === 0 and !empty($value))
                $containers['C'.substr($key, 6)] = number_format(floatval($value), 2, '.', '');
            }
        }

        $prepared_data['containers'] = $containers;

        return $prepared_data;
    }

    public function validateData($request, $contract)
    {
        $vdata = [
            'origin' => 'required',
            'destination' => 'required'
            'carrier' => 'required',
            'currency' => 'required',
            'schedule_type' => 'sometimes|nullable',
            'transit_time' => 'sometimes|nullable',
            'via' => 'sometimes|nullable'
        ];
    	
        $available_containers = Container::all()->pluck('code');

        foreach ($available_containers as $container) {
           $vdata['rates_'.$container] = 'sometimes|nullable';
        }

    	return $request->validate($vdata);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract, Rate $rate)
    {
        $data = $this->validateData($request, $contract);

        $prepared_data = $this->preparedData($data, $contract);

        $rate->update($prepared_data);

        return new OceanFreightResource($rate);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function retrieve(Contract $contract, Rate $rate)
    {
        return new OceanFreightResource($rate);
    }
}
