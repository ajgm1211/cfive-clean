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
    $contacts = Contact::whereHas('company', function ($query) {
      $query->where('company_user_id', '=', \Auth::user()->company_user_id);
    })->get();

    $company_user_id=\Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }


    return view('contacts/index', ['contacts' => $contacts,'companies'=>$companies]);
  }

  public function add()
  {
    $company_user_id=\Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }

    return view('contacts.add', ['companies'=>$companies]);
  }
  public function addWithModal()
  {
    $company_user_id=\Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }
    return view('contacts.addwithmodal', ['companies'=>$companies]);
  }

  public function store(Request $request)
  {
    Contact::create($request->all());

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'Register completed successfully!');

    return redirect()->back();
  }

  public function edit($id)
  {
    $contact = Contact::find($id);
    $company_user_id=\Auth::user()->company_user_id;
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }

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
    try {
      $contact = Contact::find($id);
      $contact->delete();

      return response()->json(['message' => 'Ok']);
    }
    catch (\Exception $e) {
      return response()->json(['message' => $e]);
    }
  }
  public function getContacts(){
    $contact = Contact::all()->pluck('first_name','id');
    return $contact ;
  }
}
