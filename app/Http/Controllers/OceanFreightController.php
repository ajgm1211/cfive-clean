<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rate;
use App\Container;
use App\Contract;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OceanFreightResource;
use Illuminate\Support\Facades\DB;

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
        $data = $this->validateData($request, $contract);

        $prepared_data = $this->prepareData($data, $contract);

        $rate = Rate::create($prepared_data);

        return new OceanFreightResource($rate);
    }

    public function prepareData($data, $contract)
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

            $prepared_data['twuenty'] = isset($data['rates_20DV']) ? $data['rates_20DV'] : 0;
            $prepared_data['forty'] = isset($data['rates_40DV']) ? $data['rates_40DV'] : 0;
            $prepared_data['fortyhc'] = isset($data['rates_40HC']) ? $data['rates_40HC'] : 0;
            $prepared_data['fortynor'] = isset($data['rates_40NOR']) ? $data['rates_40NOR'] : 0;
            $prepared_data['fortyfive'] = isset($data['rates_45HC']) ? $data['rates_45HC'] : 0;

        } else {

            $prepared_data['twuenty'] = 0;
            $prepared_data['forty'] = 0;
            $prepared_data['fortyhc'] = 0;
            $prepared_data['fortynor'] = 0;
            $prepared_data['fortyfive'] = 0;

            foreach ($data as $key => $value) {

                if(strpos($key, "rates_") === 0 and !empty($value))
                    $containers['C'.substr($key, 6)] = number_format(floatval($value), 2, '.', '');
            }
        }

        $prepared_data['containers'] = json_encode($containers);

        return $prepared_data;
    }

    public function validateData($request, $contract)
    {
        $vdata = [
            'origin' => 'required',
            'destination' => 'required',
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

        $prepared_data = $this->prepareData($data, $contract);

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

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Rate $rate)
    {
        
        $new_rate = $rate->duplicate(); 

        return new OceanFreightResource($new_rate, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rate $rate)
    {
        $rate->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  use Spatie\Permission\Models\FCLSurcharge  $fclsurcharge
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('rates')->whereIn('id', $request->input('ids'))->delete(); 

        return response()->json(null, 204);
    }
}
