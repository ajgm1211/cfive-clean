<?php

namespace App\Http\Controllers;

use App\InlandKm;
use App\Inland;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\InlandKmResource;
use App\Container;
use Illuminate\Support\Collection as Collection;

class InlandKmController extends Controller
{


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\InlandKm $km
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inland $inland, InlandKm $km)
    {
    	$available_containers = Container::where('gp_container_id', $inland->gp_container_id ?? 1)->get()->pluck('code');

    	$data = $this->validateData($request, $inland, $available_containers);

        $prepared_data = $this->prepareData($data, $inland, $available_containers);

        $km->update($prepared_data);

        return new InlandKmResource($km);
    }

    /**
     * Prepare data to submit
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Inland $inland
     * @return Array data validated
     */
    public function prepareData($data, $inland, $available_containers)
    {
        $prepared_data = [
            'currency_id' => $data['currency'],
            'inland_id' => $inland->id
        ];

        $containers = [];
        
        if(isset($data['per_container'])){

        	foreach ($available_containers as $code) {
        		$containers['C'.$code] = number_format(floatval($data['per_container']), 2, '.', '');
        	}

        } else {

        	foreach ($available_containers as $code) {
        		$value = isset($data['rates_'.$code]) ? number_format(floatval($data['rates_'.$code]), 2, '.', '') : 0;
        		$containers['C'.$code] = $value;
        	}

        }

        $prepared_data['json_containers'] = $containers;

        return $prepared_data;
    }

    /**
     * Validate the form
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Inland $inland
     * @return Array data validated
     */
    public function validateData($request, $inland, $available_containers)
    {
        $vdata = [
            'currency' => 'required',
            'per_container' => 'sometimes|required'
        ];

        foreach ($available_containers as $container) {
           $vdata['rates_'.$container] = 'sometimes|nullable';
        }

    	return $request->validate($vdata);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InlandKm  $km
     * @return \Illuminate\Http\Response
     */
    public function retrieve(Inland $inland)
    {
    	$km = InlandKm::firstOrCreate( [ 'inland_id' => $inland->id ] );

        return new InlandKmResource($km);
    }

}
