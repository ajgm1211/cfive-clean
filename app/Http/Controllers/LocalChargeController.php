<?php

namespace App\Http\Controllers;

use App\Contract;
use App\Http\Resources\LocalChargeResource;
use App\LocalCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($data['typeofroute'] == 'country') {
            $localcharge->LocalChargeCountriesSync($data['origin'], $data['destination']);
        } else {
            $localcharge->LocalChargePortsSync($data['origin'], $data['destination']);
        }

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

        $localcharge->LocalChargeCarrierSync($data['carriers']);

        if ($data['typeofroute'] == 'country') {
            $localcharge->LocalChargeCountriesSync($data['origin'], $data['destination']);
        } else {
            $localcharge->LocalChargePortsSync($data['origin'], $data['destination']);
        }

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(LocalCharge $localcharge)
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
    public function duplicate(LocalCharge $localcharge)
    {
        $new_localcharge = $localcharge->duplicate();

        return new LocalChargeResource($new_localcharge, true);
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
