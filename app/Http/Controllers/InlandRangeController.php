<?php

namespace App\Http\Controllers;

use App\InlandRange;
use App\Inland;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\InlandRangeResource;
use App\Container;
use Illuminate\Support\Collection as Collection;
use Validator;

class InlandRangeController extends Controller
{

    public function list(Request $request, Inland $inland)
    {
        $results = InlandRange::filterByInland($inland->id)->filter($request);
    	return InlandRangeResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Inland $inland)
    {
    	$available_containers = Container::where('gp_container_id', $inland->gp_container_id ?? 1)->get()->pluck('code');

        $data = $this->validateData($request, $inland, $available_containers);

        $prepared_data = $this->prepareData($data, $inland, $available_containers);

        $range = InlandRange::create($prepared_data);

        return new InlandRangeResource($range);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\InlandRange $inland_range
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inland $inland, InlandRange $range)
    {
    	$available_containers = Container::where('gp_container_id', $inland->gp_container_id ?? 1)->get()->pluck('code');

    	$data = $this->validateData($request, $inland, $range, $available_containers);

        $prepared_data = $this->prepareData($data, $inland, $available_containers);

        $range->update($prepared_data);

        return new InlandRangeResource($range);
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
            'lower' => $data['lower'],
            'upper' => $data['upper'],
            'currency_id' => $data['currency'],
            'inland_id' => $inland->id
        ];

        $containers = [];
        
        if(isset($data['per_container']))
        {
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
    public function validateData($request, $inland, $range = null, $available_containers)
    {
        $vdata = [
            'lower' => 'required',
            'upper' => 'required',
            'currency' => 'required',
            'per_container' => 'sometimes|required'
        ];

        foreach ($available_containers as $container) {
           $vdata['rates_'.$container] = 'sometimes|nullable';
        }

        $validator = Validator::make($request->all(), $vdata);

        $query_lower = InlandRange::where('lower', '<=', $request->input('lower'))->where('upper', '>=', $request->input('lower'));
        
        if($range)
        	$query_lower->where('id', '<>', $range->id);

        $validated_lower = $query_lower->get()->count() > 0;

        $query_upper = InlandRange::where('lower', '<=', $request->input('upper'))->where('upper', '>=', $request->input('upper'));

        if($range)
        	$query_upper->where('id', '<>', $range->id);

        $validated_upper = $query_upper->get()->count() > 0;

        $validator->after(function ($validator) use ($validated_lower, $validated_upper){
           
            if ($validated_lower)
                $validator->errors()->add('lower', 'This value isn\'t available');

            if ($validated_upper)
                $validator->errors()->add('upper', 'This value isn\'t available');

        });

    	return $validator->validate();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InlandRange  $range
     * @return \Illuminate\Http\Response
     */
    public function retrieve(InlandRange $range)
    {
        return new InlandRangeResource($range);
    }
}
