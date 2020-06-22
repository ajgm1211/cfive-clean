<?php

namespace App\Http\Controllers;

use App\InlandDistance;
use Illuminate\Http\Request;
use App\User;
use App\InlandLocation;
use App\Harbor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection as Collection;

class InlandDistanceController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index($harbor_id)
  {

    $harbor_id = obtenerRouteKey($harbor_id);
    $harbor = Harbor::where('id',$harbor_id)->first();

    $company_user_id = Auth::user()->company_user_id;
    /*  $data = InlandDistance::whereHas('InlandLocation', function($a) use($company_user_id){
      $a->where('company_user_id', '=',$company_user_id);
    })->get();*/
    $data = InlandDistance::where('harbor_id',$harbor_id)->with('inlandLocation')->get();
    return view('inlandDistances/index', compact('data','harbor'));

  }


  public function getDistance($ids)
  {
    $ids = explode(',',$ids);
   // $country = new Collection();
    $harbors = new Collection();

    foreach($ids as $id){
      $info = explode('-',$id);
     // $country->push($info[1]);
      $harbors->push($info[0]);
    }

   // $country = $country->unique();

    if($harbors->count() > 1  ){
      return response()->json(['message' => 'maxOne']);
    }else{
      
      $data = InlandDistance::where('harbor_id',$harbors->first())->pluck('address','distance');
      if(empty($data)){
        return response()->json(['message' => 'empty']);
      }else{
        return response()->json(['message' => 'ok','data' =>$data]);

      }
      

    }

  }

  public function add($id)
  {
    $harborCollect= Harbor::where('id',$id)->first();
    $inlandL = InlandLocation::where('country_id',$harborCollect->country_id)->pluck('region','id');
    $harbor = $id;
    return view('inlandDistances/add',compact('harbor','inlandL'));
  }


  /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function create()
  {
    //
  }

  /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function store(Request $request)
  {
    $inlandD = new InlandDistance($request->all());
    $inlandD->save();
    $harbor_id = setearRouteKey($request->harbor_id);

    return redirect()->route('inlandD.find', ['id' => $harbor_id]);

    
    
  }

  /**
     * Display the specified resource.
     *
     * @param  \App\InlandDistance  $inlandDistance
     * @return \Illuminate\Http\Response
     */
  public function show(InlandDistance $inlandDistance)
  {
    //
  }

  /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InlandDistance  $inlandDistance
     * @return \Illuminate\Http\Response
     */
  public function edit($id)
  {
    $inlandD = InlandDistance::find($id);
    $harbor = Harbor::pluck('name','id');
    $inlandL = InlandLocation::pluck('region','id');
    
    return view('inlandDistances/edit', compact('inlandL','harbor','inlandD'));
  }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InlandDistance  $inlandDistance
     * @return \Illuminate\Http\Response
     */
  public function update(Request $request, $id)
  {
    $requestForm = $request->all();
    $inlandD = InlandDistance::find($id);
    $inlandD->update($requestForm);
    $harbor_id = setearRouteKey($request->harbor_id);
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You upgrade has been success ');
        return redirect()->route('inlandD.find', ['id' => $harbor_id]);
  }

  /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InlandDistance  $inlandDistance
     * @return \Illuminate\Http\Response
     */
  public function destroy($id)
  {
    try {
      $inlandD = InlandDistance::find($id);
      $inlandD->delete();

      return response()->json(['message' => 'Ok']);
    }
    catch (\Exception $e) {
      return response()->json(['message' => $e]);
    }  
  }
}
