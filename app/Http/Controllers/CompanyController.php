<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

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
        return view('companies.add');
    }

    public function store(Request $request)
    {
        Company::create($request->all());

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');
        return redirect()->route('companies.index');
    }

    public function edit($id)
    {
        $company = Company::find($id);

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::find($id);
        $company->update($request->all());
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->route('companies.index');
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
