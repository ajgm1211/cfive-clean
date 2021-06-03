<?php

namespace App\Http\Controllers;

use App\ContractLcl;
use App\Http\Resources\LocalChargeLclResource;
use App\LocalChargeLcl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalChargeLclController extends Controller
{
    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, ContractLcl $contract)
    {
        $results = LocalChargeLcl::filterByContract($contract->id)->filter($request);

        return LocalChargeLclResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ContractLCl $contract)
    {
        $data = $this->validateData($request, $contract);

        $prepared_data = $this->prepareData($data, $contract);

        $localcharge = LocalChargeLCl::create($prepared_data);

        $localcharge->LocalChargeCarrierSync($data['carriers']);

        if ($data['typeofroute'] == 'country') {
            $localcharge->LocalChargeCountriesSync($data['origin'], $data['destination']);
        } else {
            $localcharge->LocalChargePortsSync($data['origin'], $data['destination']);
        }

        return new LocalChargeLClResource($localcharge);
    }

    public function prepareData($data, $contract)
    {
        return [
            'surcharge_id' => $data['surcharge'],
            'typedestiny_id' => $data['destination_type'],
            'contract_id' => $contract->id,
            'currency_id' => $data['currency'],
            'calculationtypelcl_id' => $data['calculation_type'],
            'ammount' => $data['amount'],
            'minimum' => $data['minimum'],
            'contractlcl_id' => $contract->id,
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
            'minimum' => 'required',
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
    public function update(Request $request, ContractLcl $contract, LocalChargeLcl $localcharge)
    {
        $data = $this->validateData($request, $contract);

        $prepared_data = $this->prepareData($data, $contract);

        $localcharge->update($prepared_data);

        $localcharge->LocalChargeCarrierSync($data['carriers']);

        if ($data['typeofroute'] == 'country') {
            $localcharge->LocalChargeCountriesSync($data['origin'], $data['destination']);
        } else {
            $localcharge->LocalChargePortsSync($data['origin'], $data['destination']);
        }

        return new LocalChargeLclResource($localcharge);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function retrieve(ContractLcl $contract, LocalChargeLcl $localcharge)
    {
        return new LocalChargeLclResource($localcharge);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(LocalChargeLcl $localcharge)
    {
        $localcharge->delete();

        return response()->json(null, 204);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function duplicate(LocalChargeLcl $localcharge)
    {
        $new_localcharge = $localcharge->duplicate();

        return new LocalChargeLclResource($new_localcharge, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  use Spatie\Permission\Models\FCLSurcharge  $fclsurcharge
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('localcharges')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
