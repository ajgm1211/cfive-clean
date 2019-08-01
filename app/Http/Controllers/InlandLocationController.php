<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\InlandLocation;
use App\Country;


class InlandLocationController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
  {
    $data = InlandLocation::where('company_user_id',Auth::user()->company_user_id)->get();
    return view('inlandLocations/index', compact('data'));
  }

  public function add()
  {
    $country = Country::pluck('name','id');
    return view('inlandLocations/add',compact('country'));
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
    $inlandL = new InlandLocation($request->all());
    $inlandL->company_user_id =Auth::user()->company_user_id ;
    $inlandL->save();

    return redirect()->action('InlandLocationController@index');
  }

  /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function show($id)
  {
    //
  }

  /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function edit($id)
  {
    $inlandL = InlandLocation::find($id);
    $country = Country::pluck('name','id');
    return view('inlandLocations.edit', compact('inlandL','country'));
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
    $requestForm = $request->all();
    $inlandL = InlandLocation::find($id);
    $inlandL->update($requestForm);

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You upgrade has been success ');
    return redirect()->action('InlandLocationController@index');
  }

  /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function destroy($id)
  {
    try {
      $inlandL = InlandLocation::find($id);
      $inlandL->delete();

      return response()->json(['message' => 'Ok']);
    }
    catch (\Exception $e) {
      return response()->json(['message' => $e]);
    }  
  }
}
