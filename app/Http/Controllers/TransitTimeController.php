<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\DestinationType;
use App\Harbor;
use App\Http\Resources\TransitTimeResource;
use App\TransitTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class TransitTimeController extends Controller
{
    /**
     * Render index view.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('transit_time.index');
    }

    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $results = TransitTime::filter($request);

        return TransitTimeResource::collection($results);
    }

    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $carriers = Carrier::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name']);
        });

        $services = DestinationType::get()->map(function ($service) {
            return $service->only(['id', 'name']);
        });

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name']);
        });

        $data = compact(
            'carriers',
            'services',
            'harbors'
        );

        return response()->json(['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $prepared_data = $this->prepareData($data);

        $transit_time = TransitTime::create($prepared_data);

        return new TransitTimeResource($transit_time);
    }

    public function prepareData($data)
    {
        $prepared_data = [
            'origin_id' => $data['origin'],
            'destination_id' => $data['destination'],
            'carrier_id' => $data['carrier'],
            'service_id' => $data['service'],
            'transit_time' => isset($data['transit_time']) ? $data['transit_time'] : '',
            'via' => isset($data['via']) ? $data['via'] : '',
        ];

        return $prepared_data;
    }

    /**
     * Validate data submitted before save.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function validateData($request, $transit_time = null)
    {
        $vdata = [
            'origin' => 'required',
            'destination' => 'required',
            'carrier' => 'required',
            'service' => 'required',
            'transit_time' => 'required',
            'via' => 'sometimes|nullable',
        ];

        $validator = Validator::make($request->all(), $vdata);

        $val = TransitTime::scheduleExists($request, $transit_time);

        $validator->after(function ($validator) use ($val) {
            if ($val) {
                $validator->errors()->add('general', 'This schedule already exists');
            }
        });

        return $validator->validate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransitTime $transit_time)
    {
        $data = $this->validateData($request, $transit_time);

        $prepared_data = $this->prepareData($data);

        $transit_time->update($prepared_data);

        return new TransitTimeResource($transit_time);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TransitTime  $transit_time
     * @return \Illuminate\Http\Response
     */
    public function retrieve(TransitTime $transit_time)
    {
        return new TransitTimeResource($transit_time);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TransitTime  $transit_time
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransitTime $transit_time)
    {
        $transit_time->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TransitTime  $transit_time
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('transit_times')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
