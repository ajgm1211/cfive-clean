<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyPrice;
use App\Contact;
use App\Quote;
use App\Price;
use App\User;
use App\GroupUserCompany;
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
    $company_user_id=\Auth::user()->company_user_id;
    $user_id = \Auth::user()->id;
    $users = User::where('company_user_id',\Auth::user()->company_user_id)->where('id','!=',\Auth::user()->id)->where('type','!=','company')->pluck('name','id');
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function ($query) use($user_id) {
        $query->where('user_id',$user_id);
      })->orwhere('owner',\Auth::user()->id)->with('groupUserCompanies.user','user')->get();

    }else{
      $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->with('groupUserCompanies.user','user')->get();
    }


    return view('companies/index', ['companies' => $companies,'users'=>$users]);
  }

  public function add()
  {
    $users = User::where('company_user_id',\Auth::user()->company_user_id)->where('id','!=',\Auth::user()->id)->where('type','!=','company')->pluck('name','id');
    $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    return view('companies.add', compact('prices','users'));
  }
  public function addWithModal()
  {
    $company_user_id=\Auth::user()->company_user_id;
    $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    $user_id = \Auth::user()->id;
    $users = User::where('company_user_id',\Auth::user()->company_user_id)->where('id','!=',$user_id)->where('type','!=','company')->pluck('name','id');
    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function ($query) use($user_id) {
        $query->where('user_id',$user_id);
      })->orwhere('owner',\Auth::user()->id)->with('groupUserCompanies.user','user')->get();

    }else{
      $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->with('groupUserCompanies.user','user')->get();
    }


    return view('companies.addwithmodal', compact('prices','users'));

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
    $request->request->add(['owner' => \Auth::user()->id]);
    $company=Company::create($request->all());

    if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
      foreach ($input['price_id'] as $key => $item) {            
        $company_price = new CompanyPrice();
        $company_price->company_id=$company->id;
        $company_price->price_id=$input['price_id'][$key];
        $company_price->save();
      }
    }
    if ((isset($input['users'])) && (count($input['users']) > 0)) {
      foreach ($input['users'] as $key => $item) {            
        $userCompany_group = new GroupUserCompany();
        $userCompany_group->user_id= $input['users'][$key];
        $userCompany_group->company()->associate($company);
        $userCompany_group->save();
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

    $users = User::where('company_user_id',\Auth::user()->company_user_id)->where('type','!=','company')->where('id','!=',$company->owner)->pluck('name','id');

    $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
    return view('companies.edit', compact('company','prices','users'));
  }

  public function update(Request $request, $id)
  {
    $input = Input::all();
    $company = Company::find($id);
    $company->update($request->all());


    if ((isset($input['price_id'])) && ($input['price_id'][0] != null)) {
      $company_price = CompanyPrice::where('company_id',$company->id)->delete();
      foreach ($input['price_id'] as $key => $item) {            
        $company_price = new CompanyPrice();
        $company_price->company_id=$company->id;
        $company_price->price_id=$input['price_id'][$key];
        $company_price->save();
      }
    }
    $company_price = GroupUserCompany::where('company_id',$company->id)->delete();
    if ((isset($input['users'])) && ($input['users'][0] != null)) {

      foreach ($input['users'] as $key => $item) {            
        $userCompany_group = new GroupUserCompany();
        $userCompany_group->user_id= $input['users'][$key];
        $userCompany_group->company_id=$company->id;
        $userCompany_group->save();
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

  public function getCompanies(){
    $company_user_id=\Auth::user()->company_user_id;


    if(\Auth::user()->hasRole('subuser')){
      $companies = Company::where('company_user_id','=',$company_user_id)->whereHas('groupUserCompanies', function($q)  {
        $q->where('user_id',\Auth::user()->id);
      })->orwhere('owner',\Auth::user()->id)->pluck('business_name','id');
   
    }else{
      $companies = Company::where('company_user_id','=',$company_user_id)->pluck('business_name','id');
    }
    
    return $companies;
  }
}
