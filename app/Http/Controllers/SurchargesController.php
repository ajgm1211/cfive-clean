<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurcharge;
use Illuminate\Http\Request;
use App\User;
use App\Surcharge;
use App\SaleTerm;
use App\SaleTermSurcharge;
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

        $data = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->with('companyUser')->get();
        $saleterms = SaleTerm::where('company_user_id','=',Auth::user()->company_user_id)->get();
        return view('surcharges/index', ['surcharges' => $data,'saleterms'=>$saleterms]);
    }

    public function add()
    {
        $sale_terms = SaleTerm::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        return view('surcharges/add',compact('sale_terms'));
    }

    public function create()
    {
        //
    }

    public function store(StoreSurcharge $request)
    {
        $request->validated();

        $surcharge = new Surcharge($request->all());
        $surcharge->company_user_id =Auth::user()->company_user_id ;
        $surcharge->save();

        if ($request->ajax()) {
            return $surcharge;
        }

        return redirect()->action('SurchargesController@index');

    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $surcharges = Surcharge::find($id);
        $sale_terms = SaleTerm::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        
        return view('surcharges.edit', compact('surcharges','sale_terms'));
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
        try {
            $surcharge = Surcharge::find($id);
            $surcharge->delete();

            return response()->json(['message' => 'Ok']);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }    
    }

    public function destroySubcharge(Request $request,$id)
    {
        try {
            $user = self::destroy($id);
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'You successfully delete ');
            return redirect()->action('SurchargesController@index');

        } catch (\Illuminate\Database\QueryException $e) {

            $request->session()->flash('message.nivel', 'warning');
            $request->session()->flash('message.title', 'I\'m Sorry!');
            $request->session()->flash('message.content', 'You can not delete the charge, it belongs to a contract');
            return redirect()->action('SurchargesController@index');
        }

    }

    public function destroymsg($id)
    {
        return view('surcharges/message' ,['surcharge_id' => $id]);

    }
}
