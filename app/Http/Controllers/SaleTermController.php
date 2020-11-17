<?php

namespace App\Http\Controllers;

use App\SaleTerm;
use App\Surcharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleTermController extends Controller
{
    public function index()
    {        
        $surcharges = Surcharge::where('company_user_id','=',\Auth::user()->company_user_id)->with('companyUser')->get();
        $saleterms = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->get();
        return view('surcharges/index', ['saleterms' => $saleterms,'surcharges'=>$surcharges]);
    }

    public function store(Request $request)
    {

        $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);
        $saleterms=SaleTerm::create($request->all());

        return redirect()->action('SurchargesController@index');

    }

    public function create(Request $request)
    {

        return view('saleTerms/add');

    }

    public function edit($id)
    {
        $saleterms = SaleTerm::find($id);
        return view('saleTerms.edit', compact('saleterms'));
    }


    public function update(Request $request, $id)
    {
        $requestForm = $request->all();
        $saleterms = SaleTerm::find($id);
        $saleterms->update($requestForm);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record has been success');
        if(Auth::user()->hasRole(['administrator','data_entry'])){
            return redirect()->action('SurchargesController@index');
        } else {
            return redirect()->action('SaleTermController@index');            
        }
    }

    public function destroy(Request $request,$id)
    {
        try {
            $contact = SaleTerm::find($id);
            $contact->delete();

            return response()->json(['message' => 'Ok']);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }

    public function destroymsg($id)
    {
        return view('saleterms/message' ,['saleterm_id' => $id]);

    }    
}
