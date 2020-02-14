<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use App\Contact;
use App\Company;


class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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

        if($request->ajax()){
            $collection = Collection::make($contacts);
            $collection->map(function ($contact) {
                $contact['company'] = $contact->company->business_name;
                unset($contact['company_id']);
            });

            return response()->json($collection);
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

    public function addWithModalManualQuote()
    {
        $company_user_id=\Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
                $q->where('user_id',\Auth::user()->id);
            })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
        }else{
            $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
        }
        return view('contacts.addWithModalManualQuote', ['companies'=>$companies]);
    }

    public function addWithModalCompanies($company_id)
    {
        $company_user_id=\Auth::user()->company_user_id;
        if(\Auth::user()->hasRole('subuser')){
            $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
                $q->where('user_id',\Auth::user()->id);
            })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
        }else{
            $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
        }
        return view('contacts.add', ['companies'=>$companies,'company_id'=>$company_id]);
    }

    public function store(Request $request)
    {
        Contact::create($request->all());

        if($request->ajax()) {
            return response()->json('Contact created successfully!');
        }

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
        $contact = Contact::find($id);
        
        $contact->update($request->all());

        if($request->ajax()) {
            return response()->json('Contact updated successfully!');
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');

        return redirect()->route('contacts.index');
    }

    public function show(Request $request, $id)
    {
        $contact = Contact::find($id);

        if($request->ajax()){
            $collection = Collection::make($contact);
            return $collection;
        }

        return view('contacts.show', compact('conact'));
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

            if($request->ajax()) {
                return response()->json('Contact deleted successfully!');
            }

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
    public function getContactsByCompanyId($id){
        $contact = Contact::where('company_id',$id)->pluck('first_name','id');
        return $contact ;
    }    
}
