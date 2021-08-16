<?php

namespace App\Http\Controllers;

use App\ContractLcl;
use App\Http\Resources\OceanFreightLclResource;
use App\RateLcl;
use Illuminate\Http\Request;

class OceanFreightLclController extends Controller
{
    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function list(Request $request, ContractLcl $contract)
    {
        $results = RateLcl::filterByContract($contract->id)->filter($request);

        return OceanFreightLclResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ContractLcl $contract)
    {
        $data = $this->validateData($request, $contract);

         $origin = $data['origin'];
         $destination = $data['destination'];
         $carriers = $data['carrier'];

        foreach ($carriers as $carrier) {
            foreach ($origin as $origi) {
                foreach ($destination as $destiny) {
                    $rate = RateLcl::create([
                        'origin_port' => $origi,
                        'destiny_port' => $destiny,
                        'carrier_id' => $carrier,
                        'contract_id' => $contract->id,
                        'uom' => $request->uom,
                        'minimum' => $request->minimum,
                        'currency_id' => $request->currency,
                        'contractlcl_id' => $contract->id,
                    ]);
                }
            }
        }

        return new OceanFreightLclResource($rate);
    }

    public function validateData($request, $contract)
    {
        $vdata = [
            'origin' => 'required:field ',
            'destination' => 'required:field ',
            'carrier' => 'required:field ',
            'currency' => 'required:field ',
            'uom' => 'required:field | numeric ',
            'minimum' => 'required:field | numeric ',
            'schedule_type' => 'sometimes|nullable',
            'transit_time' => 'sometimes|nullable',
            'via' => 'sometimes|nullable',
        ];

        return $request->validate($vdata);
    }

    public function validateDataOrigin($request, $contract)
    {
        $vdata = [
            'origin' => 'required:field',
        ];
        return $request->validate($vdata);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContractLcl $contract, RateLcl $rate)
    {
        $data = $this->validateData($request, $contract);

        $prepared_data = $this->prepareData($data, $contract);
        
        $rate->update($prepared_data);

        return new OceanFreightLclResource($rate);
    }
    
    /**
     * prepareData
     *
     * @param  mixed $data
     * @param  mixed $contract
     * @return array
     */
    public function prepareData($data, $contract)
    {
        $prepared_data = [
            'origin_port' => $data['origin'],
            'destiny_port' => $data['destination'],
            'carrier_id' => $data['carrier'],
            'contract_id' => $contract->id,
            'currency_id' => $data['currency'],
            'uom' => $data['uom'],
            'minimum' => $data['minimum'],
        ];

        return $prepared_data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function retrieve(ContractLcl $contract, RateLcl $rate)
    {
        return new OceanFreightLclResource($rate);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function duplicate(RateLcl $rate)
    {
        $new_rate = $rate->duplicate();

        return new OceanFreightLclResource($new_rate, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(RateLcl $rate)
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
        RateLcl::whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }

    public function massiveHarborChange(Request $request, ContractLcl $contract)
    {
        $data = $this->validateDataOrigin($request, $contract);

        $prepared_data = [
            'origin_port' => $data['origin'],
        ];

        RateLcl::whereIn('id', $request->input('ids'))->update($prepared_data);

        return response()->json(null, 204);
    }

    public function massiveHarborChangeDest(Request $request, ContractLcl $contract)
    {
        $prepared_data = [
            'destiny_port' => $request->input('destination'),
        ];

        RateLcl::whereIn('id', $request->input('ids'))->update($prepared_data);

        return response()->json(null, 204);
    }
}
