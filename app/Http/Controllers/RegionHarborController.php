<?php

namespace App\Http\Controllers;


use App\RegionPt;
use App\PortRegion;
use App\Harbor;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class RegionHarborController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
  {
    return view('RegionHarbors.index');
  }

  /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function create()
  {
    $regions = RegionPt::with('PortRegions.harbor')->get();



    return Datatables::of($regions)
      ->addColumn('name', function ($regions) {
        return $regions['name'];
      })
      ->addColumn('harbors', function ($regions) {
        return str_replace(['[',']','"'],' ',$regions['PortRegions']->pluck('harbor')->pluck('name'));
      })
      ->addColumn('action', function ($regions) {
        return '<a href="#" data-id-edit="'.$regions->id.'" onclick="showModal(2,'.$regions->id.')" class=""><i class="la  la-edit"></i></a>
                        &nbsp 
                        &nbsp  <a href="#" data-id-remove="'.$regions->id.'" class="BorrarRegion"><i class="la  la-remove"></i></a>';
      })

      ->make();

  }


  public function LoadViewAdd(){
    $harbors =  Harbor::pluck('name','id');
    return view('RegionHarbors.Body-Modals.add',compact('harbors'));
  }

  /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
  {
    $region = new RegionPt();
    $region->name = $request->name;
    $region->save();
    foreach($request->harbors as $harbor){
      PortRegion::create([
        'harbor_id'    => $harbor,
        'region_pts_id'     => $region->id,
      ]);
    }
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.content', 'Region added successfully');
    return redirect()->route('RegionP.index');
  }

  /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function show($id)
  {
    $harbors  = Harbor::pluck('name','id');
    $region     = RegionPt::with('PortRegions.harbor')->find($id);

    return view('RegionHarbors.Body-Modals.edit',compact('region','harbors'));
  }

  /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function edit($id)
  {
    //
  }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function update(Request $request, $id)
  {
    $region = RegionPt::find($id);
    $region->name = $request->name;
    $region->save();

    PortRegion::where('region_pts_id',$region->id)->delete();
    foreach($request->harbors as $harbor){
      PortRegion::create([
        'harbor_id'    => $harbor,
        'region_pts_id'     => $region->id,
      ]);
    }

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.content', 'Region updated successfully');
    return redirect()->route('RegionP.index');
  }

  /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function destroy($id)
  {
    try{
      $region = RegionPt::find($id);
      $region->delete();
      return response()->json(['success' => '1']);
    } catch(\Exception $e){
      return response()->json(['success' => '2']);            
    }
  }
}
