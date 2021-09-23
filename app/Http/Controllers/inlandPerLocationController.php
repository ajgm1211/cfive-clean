<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InlandPerLocation;
use App\Inland;
use App\Container;
use App\Http\Resources\InlandPerLocationResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class inlandPerLocationController extends Controller
{
    public function index(Request $request)
    {
        return view('inlands.location');
    }

    public function list(Request $request, Inland $inland)
    {

        $results = InlandPerLocation::filterByInland($inland->id)->filter($request);
        return InlandPerLocationResource::collection($results);
    }

    public function store(Request $request, Inland $inland)
    {
        $available_containers = Container::where('gp_container_id', $inland->gp_container_id ?? 1)->get()->pluck('code');

        $data = $request->validate([
            'port' => 'required',
            'location' => 'required',
            'service' => 'required',
            'currency' => 'required',
        ]);

        $containers = [];
        foreach ($available_containers as $code) {
            $value = isset($request['rates_' . $code]) ? number_format(floatval($request['rates_' . $code]), 2, '.', '') : 0;
            $containers['C' . $code] = $value;
        }
        $inlandPL = new InlandPerLocation();
        $inlandPL->json_containers = $containers;
        $inlandPL->currency_id = $data['currency'];
        $inlandPL->harbor_id = $data['port'];
        $inlandPL->inland_id = $inland->id;
        $inlandPL->location_id = $data['location'];
        $inlandPL->service_id = $data['service'];
        // $inlandPL->type = ;
        $inlandPL->save();

        return new InlandPerLocationResource($inlandPL);
    }

    public function update(Request $request, Inland $inland,  InlandPerLocation $location)
    {
        $available_containers = Container::where('gp_container_id', $inland->gp_container_id ?? 1)->get()->pluck('code');
        $data = $request->validate([
            'currency' => 'required',
            'port' => 'required',
            'service' => 'required',
            'location' => 'required',
        ]);

        $containers = [];
        foreach ($available_containers as $code) {
            $value = isset($request['rates_' . $code]) ? number_format(floatval($request['rates_' . $code]), 2, '.', '') : 0;
            $containers['C' . $code] = $value;
        }

        $location->json_containers = $containers;
        $location->currency_id = $data['currency'];
        $location->harbor_id = $data['port'];
        $location->location_id = $data['location'];
        $location->service_id = $data['service'];
        $location->update();

        return new InlandPerLocationResource($location);
    }

    public function duplicate(InlandPerLocation $location)
    {
        $new_inland_range = $location->duplicate();
        return new InlandPerLocationResource($new_inland_range);
    }

    public function destroy(InlandPerLocation $location)
    {
        $location->delete();
        return response()->json(null, 204);
    }

    public function destroyAll(Request $request)
    {
        DB::table('inland_per_locations')->whereIn('id', $request->input('ids'))->delete();
        return response()->json(null, 204);
    }
}
