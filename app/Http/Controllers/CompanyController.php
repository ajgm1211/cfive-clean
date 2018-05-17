<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyPrice;
use App\Contact;
use App\Quote;
use App\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();
        return view('companies/index', ['companies' => $companies]);
    }

    public function add()
    {
        $prices = Price::all()->pluck('name','id');
        return view('companies.add', compact('prices'));
    }

    public function show($id)
    {
        $company = Company::find($id);
        $companies = Company::all();
        $prices = Price::all()->pluck('name','id');
        $quotes = Quote::where('company_id',$id)->get();
        return view('companies.show', compact('company','companies','contacts','prices','quotes'));
    }

    public function store(Request $request)
    {
        $input = Input::all();

        $company=Company::create($request->all());

        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            $company_price = new CompanyPrice();
            $company_price->company_id=$company->id;
            $company_price->price_id=$input['price_id'];
            $company_price->save();
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');
        return redirect()->route('companies.index');
    }

    public function edit($id)
    {
        $company = Company::find($id);
        $prices = Price::all()->pluck('name','id');
        return view('companies.edit', compact('company','prices'));
    }

    public function update(Request $request, $id)
    {
        $input = Input::all();
        $company = Company::find($id);
        $company->update($request->all());
        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            $company_price = CompanyPrice::where('company_id',$company->id)->first();
            $company_price->price_id=$input['price_id'];
            $company_price->update();
        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->back();
    }

    public function delete($id)
    {
        $company = Company::find($id);
        return view('companies.delete', compact('company'));
    }

    public function destroy(Request $request,$id)
    {
        $company = Company::find($id);
        $company->delete();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register deleted successfully!');
        return redirect()->route('companies.index');
    }
}
