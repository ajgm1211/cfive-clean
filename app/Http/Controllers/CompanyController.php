<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyPrice;
use App\Contact;
use App\Quote;
use App\Price;
use App\User;
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
        $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->get();
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
        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->get();
        $quotes = Quote::where('company_id',$id)->get();
        return view('companies.show', compact('company','companies','contacts','quotes'));
    }

    public function store(Request $request)
    {
        $input = Input::all();
        $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);
        $company=Company::create($request->all());

        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            foreach ($input['price_id'] as $key => $item) {            
                $company_price = new CompanyPrice();
                $company_price->company_id=$company->id;
                $company_price->price_id=$input['price_id'][$key];
                $company_price->save();
            }
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
            $company_price = CompanyPrice::where('company_id',$company->id)->delete();
            foreach ($input['price_id'] as $key => $item) {            
                $company_price = new CompanyPrice();
                $company_price->company_id=$company->id;
                $company_price->price_id=$input['price_id'][$key];
                $company_price->save();
            }
        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->back();
    }

    public function delete($id)
    {
        $company = Company::find($id);

        if(count($company->contact)>0){
            return response()->json(['message' => count($company->contact)]);
        }

        return response()->json(['message' => 'Ok']);
    }

    public function destroy($id)
    {
        try {
            $company = Company::find($id);
            $company->delete();

            return response()->json(['message' => 'Ok']);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }

    public function getCompanyPrice($id){
        $prices = Price::whereHas('company_price', function ($query) use($id) {
            $query->where('company_id',$id);
        })->pluck('name','id');

        return $prices;
    }

    public function getCompanyContact($id){
        $contacts = Contact::where('company_id',$id)->pluck('first_name','id');

        return $contacts;
    }
}
