<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SaleTerm;

class SaleTermController extends Controller
{
    public function index()
    {
        $data = SaleTerm::where('company_user_id','=',\Auth::user()->company_user_id)->get();
        return view('saleTerms/index', ['saleterms' => $data]);
    }

    public function store(Request $request)
    {

        $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);
        $saleterms=SaleTerm::create($request->all());

        return redirect()->action('SaleTermController@index');

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
        return redirect()->action('SaleTermController@index');
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
