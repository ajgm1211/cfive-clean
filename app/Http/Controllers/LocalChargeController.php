<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LocalCharge;
use App\Http\Resources\LocalChargeResource;
use App\Contract;

class LocalChargeController extends Controller
{
    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, Contract $contract)
    {
        $results = LocalCharge::filterByContract($contract->id)->filter($request);

    	return LocalChargeResource::collection($results);
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

        $localcharge = LocalCharge::create($prepared_data);

        $localcharge->LocalChargeCarrierSync($data['carriers']);

        if($data['typeofroute'] == 'country')
            $localcharge->LocalChargeCountriesSync($data['origin'], $data['destination']);
        else
            $localcharge->LocalChargePortsSync($data['origin'], $data['destination']);

        return new LocalChargeResource($localcharge);
    }

    public function prepareData($data, $contract)
    {
        return [
            'surcharge_id' => $data['surcharge'],
            'typedestiny_id' => $data['destination_type'],
            'contract_id' => $contract->id,
            'currency_id' => $data['currency'],
            'calculationtype_id' => $data['calculation_type'],
            'ammount' => $data['amount'],
        ];
    }

    public function validateData($request, $contract)
    {
        $vdata = [
            'typeofroute' => 'required',
            'surcharge' => 'required',
            'origin' => 'required',
            'destination' => 'required',
            'destination_type' => 'required',
            'calculation_type' => 'required',
            'carriers' => 'required',
            'amount' => 'required',
            'currency' => 'required',
        ];

    	return $request->validate($vdata);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract, LocalCharge $localcharge)
    {
        $data = $this->validateData($request, $contract);

        $prepared_data = $this->prepareData($data, $contract);

        $localcharge->update($prepared_data);

        return new LocalChargeResource($localcharge);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function retrieve(Contract $contract, LocalCharge $localcharge)
    {
        return new LocalChargeResource($localcharge);
    }
}
