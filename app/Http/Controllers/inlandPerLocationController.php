<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InlandPerLocation;
use App\Inland;
use App\Container;
use App\Http\Resources\InlandPerLocationResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class inlandsPerLocationController extends Controller
{
    public function index(Request $request)
    {
        return view('inlandperlocation.index');
    }

    public function list(Request $request)
    {
        //
    }

    public function data(Request $request)
    {
        //
    }

    public function store(Request $request)
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
        
        $inlandPL = InlandPerLocation::create([
            'container' => $containers,
            'currency_id' => $data['currency'],
            'harbor_id' => $data['harbor'],
            'inland_id' => $data['inland'],
            'location_id' => $data['location'],
            'type' => $data['type'],
        ]);

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
            'type' => $data['type'],,
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
}
