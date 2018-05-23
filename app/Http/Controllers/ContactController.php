<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use App\Company;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::all();
        $companies = Company::all()->pluck('id','name');
        return view('contacts/index', ['contacts' => $contacts,'companies'=>$companies]);
    }

    public function add()
    {
        $companies = Company::all()->pluck('business_name','id');
        return view('contacts.add', ['companies'=>$companies]);
    }

    public function store(Request $request)
    {
        Contact::create($request->all());

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');
        return redirect()->route('contacts.index');
    }

    public function edit($id)
    {
        $contact = Contact::find($id);
        $companies = Company::all()->pluck('business_name','id');
        return view('contacts.edit', compact('contact','companies'));
    }

    public function update(Request $request, $id)
    {
        $company = Contact::find($id);
        $company->update($request->all());
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->route('contacts.index');
    }

    public function delete($id)
    {
        $contact = Contact::find($id);
        return view('contacts.delete', compact('contact'));
    }

    public function destroy(Request $request,$id)
    {
        $company = Contact::find($id);
        $company->delete();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register deleted successfully!');
        return redirect()->route('contacts.index');
    }
}
