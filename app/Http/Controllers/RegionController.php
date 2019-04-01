<?php

namespace App\Http\Controllers;

use App\Region;
use App\Country;
use App\CountryRegion;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class RegionController extends Controller
{

    public function index()
    {
        return view('Regions.index');
    }

    public function create()
    {
        $regions = Region::with('CountriesRegions.country')->get();

        //dd($regions);
        //dd($regions[0]['CountriesRegions']->pluck('Country')->pluck('name'));


        return Datatables::of($regions)
            ->addColumn('name', function ($regions) {
                return $regions['name'];
            })
            ->addColumn('countries', function ($regions) {
                return str_replace(['[',']','"'],' ',$regions['CountriesRegions']->pluck('Country')->pluck('name'));
            })
            ->addColumn('action', function ($regions) {
                return '<a href="#" data-id-edit="'.$regions->id.'" onclick="showModal(2,'.$regions->id.')" class=""><i class="la  la-edit"></i></a>
                        &nbsp 
                        &nbsp  <a href="#" data-id-remove="'.$regions->id.'" class="BorrarRegion"><i class="la  la-remove"></i></a>';
            })

            ->make();
    }

    public function LoadViewAdd(){
        $countries =  Country::pluck('name','id');
        return view('Regions.Body-Modals.add',compact('countries'));
    }

    public function store(Request $request)
    {
        $region = new Region();
        $region->name = $request->name;
        $region->save();
        foreach($request->countries as $country){
            CountryRegion::create([
                'country_id'    => $country,
                'region_id'     => $region->id,
            ]);
        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Region added successfully');
        return redirect()->route('Region.index');
    }

    public function show($id)
    {
        $countries  = Country::pluck('name','id');
        $region     = Region::with('CountriesRegions.country')->find($id);
        //dd($region);
        return view('Regions.Body-Modals.edit',compact('region','countries'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $region = Region::find($id);
        $region->name = $request->name;
        $region->save();

        CountryRegion::where('region_id',$region->id)->delete();
        foreach($request->countries as $country){
            CountryRegion::create([
                'country_id'    => $country,
                'region_id'     => $region->id,
            ]);
        }
        
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Region updated successfully');
        return redirect()->route('Region.index');
    }

    public function destroy($id)
    {
        try{
            $region = Region::find($id);
            $region->delete();
            return response()->json(['success' => '1']);
        } catch(\Exception $e){
            return response()->json(['success' => '2']);            
        }
    }
}
