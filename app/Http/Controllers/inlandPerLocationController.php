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
        return view('inlandperlocation.index');
    }

    public function list(Request $request, Inland $inland) {
        
            $results = InlandPerLocation::filterByInland($inland->id)->filter($request);
    // dd($results);
            return InlandPerLocationResource::collection($results);
    }

    public function data(Request $request)
    {
        //
    }

    public function store(Request $request, Inland $inland)
    {    
        
        $data = $request->validate([
            'port' => 'required',
            'location' => 'required',
            'service' => 'required',
            'currency' => 'required',
        ]);

        $available_containers = Container::where('gp_container_id', $inland->gp_container_id ?? 1)->get()->pluck('code');

        $prepared_data = $this->prepareData($request,$data, $inland, $available_containers);

        foreach($request->port as $harbors){

            $inlandPL = new InlandPerLocation();
            $inlandPL->json_container=json_encode($prepared_data['json_containers']);
            $inlandPL->currency_id =$prepared_data['currency_id'];
            $inlandPL->harbor_id =$harbors;
            $inlandPL->inland_id =$prepared_data['inland_id'];
            $inlandPL->location_id =$prepared_data['location_id']; 
            $inlandPL->service_id=$prepared_data['service_id'];
            // $inlandPL-> = $prepared_data[];
            $inlandPL->save();

           
        }
            
        return new InlandPerLocationResource($inlandPL);
    }

    public function update(Request $request, InlandPerLocation $InlandPL)
    {
        $data = $request->validate([
            'containers' => 'required',
            'currency' => 'required',
            'harbor' => 'required',
            'inland' => 'required',
            'location' => 'required',
            'type' => 'required',
        ]);
        
        $containers = json_encode($data['containers']);

        $InlandPL->update([
            'container' => $containers,
            'currency_id' => $data['currency'],
            'harbor_id' => $data['harbor'],
            'inland_id' => $data['inland'],
            'location_id' => $data['location'],
            'type' => $data['type'],
        ]);

        return new InlandPerLocationResource($InlandPL);
    }

    public function duplicate(Request $request )
    {
        //
    }

    public function destroy(InlandPerLocation $InlandPL)
    {
        $InlandPL->delete();

        return response()->json(null, 204);
    }

    public function destroyAll(Request $request)
    {
        DB::table('inland_location')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }

    public function prepareData($request,$data,$inland, $available_containers)
    {
        $prepared_data = [
            'currency_id' => $data['currency'],
            'inland_id' => $inland->id,
            'location_id' => $data['location'], 
            'service_id' => $data['service'],

        ];

        $containers = [];
        foreach ($available_containers as $container){
            $c='rates_'.$container;
            $cont='C'.$container;

            if(isset($request->$c)){
                $containers[$cont]=$request->$c;
            }
        }
        $prepared_data['json_containers'] = $containers;

        return $prepared_data;
    }
}
