<?php

namespace App\Http\Controllers;

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
        $sale_terms = SaleTerm::where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        return view('surcharges/index', ['arreglo' => $data,'sale_terms'=>$sale_terms]);
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

    public function store(Request $request)
    {
        $surcharges = new Surcharge($request->all());
        $surcharges->company_user_id =Auth::user()->company_user_id ;
        $surcharges->save();

        if (count($request->input("sale_term_id")) > 0) {
          foreach ($request->input("sale_term_id") as $v) {
            $sale_term_surcharge = new SaleTermSurcharge();
            $sale_term_surcharge->sale_term_id = $v;
            $sale_term_surcharge->surcharge_id = $surcharges->id;
            $sale_term_surcharge->save();
        }
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
    $selected_sale_terms = SaleTermSurcharge::where('surcharge_id',$surcharges->id)->get();
    $selected_sale_terms_array=array();
    foreach ($selected_sale_terms as $item) {
        $selected_sale_terms_array []= $item->sale_term_id;
    }
    
    return view('surcharges.edit', compact('surcharges','sale_terms','selected_sale_terms_array'));
}


public function update(Request $request, $id)
{
    $requestForm = $request->all();
    $surcharges = Surcharge::find($id);
    $surcharges->update($requestForm);


    if (count($request->input("sale_term_id")) > 0) {
        SaleTermSurcharge::where('surcharge_id',$surcharges->id)->delete();
        foreach ($request->input("sale_term_id") as $v) {
            $sale_term_surcharge = new SaleTermSurcharge();
            $sale_term_surcharge->sale_term_id = $v;
            $sale_term_surcharge->surcharge_id = $surcharges->id;
            $sale_term_surcharge->save();
        }
    }

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
