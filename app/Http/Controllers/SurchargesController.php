<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Surcharge;
use Illuminate\Support\Facades\Auth;
class SurchargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $data = Surcharge::where('user_id','=',Auth::user()->id)->with('user')->get();
        return view('surcharges/index', ['arreglo' => $data]);
    }
    public function add()
    {


        return view('surcharges/add');
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $surcharges = new Surcharge($request->all());
        $surcharges->user_id =Auth::user()->id ;
        $surcharges->save();
        return redirect()->action('SurchargesController@index');

    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {

        $surcharges = Surcharge::find($id);
        return view('surcharges.edit', compact('surcharges','surcharges'));
    }


    public function update(Request $request, $id)
    {
        $requestForm = $request->all();
        $surcharges = Surcharge::find($id);
        $surcharges->update($requestForm);
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You upgrade has been success ');
        return redirect()->action('SurchargesController@index');
    }


    public function destroy($id)
    {
        $surcharges = Surcharge::find($id);
        $surcharges->delete();
        return $surcharges;
    }
    public function destroySubcharge(Request $request,$id)
    {

        $user = self::destroy($id);
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete ');
        return redirect()->action('SurchargesController@index');
    }
    public function destroymsg($id)
    {
        return view('surcharges/message' ,['surcharge_id' => $id]);

    }
}
