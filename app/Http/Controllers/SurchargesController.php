<?php

namespace App\Http\Controllers;

use App\User;
use App\Surcharge;
use App\SaleTerm;
use App\SaleTermSurcharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSurcharge;



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
        $is_admin   = false;
        $sale_terms = SaleTerm::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        if(Auth::user()->hasRole(['administrator','data_entry'])){
            $is_admin   = true;
        }
        $decodejosn = [];
        return view('surcharges/add',compact('sale_terms','is_admin','decodejosn'));
    }

    public function create()
    {
        //
    }

    public function store(StoreSurcharge $request)
    {
        //dd($request->all());
        $request->validated();

        $surcharge = new Surcharge();
        $surcharge->name            = $request->name;
        $surcharge->description     = $request->description;
        $surcharge->sale_term_id    = $request->sale_term_id;
        $surcharge->variation       = strtolower(json_encode(['type' => $request->variation]));
        if(!Auth::user()->hasRole(['administrator','data_entry'])){
            $surcharge->company_user_id =Auth::user()->company_user_id;
        }
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
        $decodejosn = json_decode($surcharges->variation,true);
        $decodejosn = $decodejosn['type'];
        $sale_terms = SaleTerm::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        if(Auth::user()->hasRole(['administrator','data_entry'])){
            $is_admin   = true;
        }
        return view('surcharges.edit', compact('surcharges','decodejosn','is_admin','sale_terms'));
    }


    public function update(Request $request, $id)
    {
        $requestForm            = $request->all();
        $surcharges             = Surcharge::find($id);
        $surcharges->name            = $request->name;
        $surcharges->description     = $request->description;
        $surcharges->sale_term_id    = $request->sale_term_id;
        $surcharges->variation       = strtolower(json_encode(['type' => $request->variation]));
        $surcharges->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record updated successfully');
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
            $request->session()->flash('message.content', 'Record deleted successfully');
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
