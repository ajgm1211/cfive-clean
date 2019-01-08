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
use Intervention\Image\Facades\Image;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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

        if($request->ajax()){
            return response()->json($companies);
        }

        return view('companies/index', ['companies' => $companies,'users'=>$users]);
    }

    public function add()
    {
        $users = User::where('company_user_id',\Auth::user()->company_user_id)->where('id','!=',\Auth::user()->id)->where('type','!=','company')->pluck('name','id');
        $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');
        return view('companies.add', compact('prices','users'));
    }

    public function addOwner(){
        $users = User::where('company_user_id',\Auth::user()->company_user_id)->where('id','!=',\Auth::user()->id)->where('type','!=','company')->pluck('name','id');

        return view('companies.addOwner', compact('users'));   
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
        $id = obtenerRouteKey($id);
        $company = Company::find($id);
        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->get();
        $quotes = Quote::where('company_id',$id)->get();
        $users = User::where('company_user_id',\Auth::user()->company_user_id)->where('id','!=',\Auth::user()->id)->where('type','!=','company')->pluck('name','id');
        $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->pluck('name','id');

        return view('companies.show', compact('company','companies','contacts','quotes','users','prices'));
    }

    public function store(Request $request)
    {
        $input = Input::all();
        $file = Input::file('logo');

        $company = new Company();
        $company->business_name = $request->business_name;
        $company->phone = $request->phone;
        $company->address = $request->address;
        $company->email = $request->email;
        $company->tax_number = $request->tax_number;
        $company->company_user_id = \Auth::user()->company_user_id;
        $company->owner = \Auth::user()->id;
        $company->pdf_language = $request->pdf_language;
        $company->payment_conditions = $request->payment_conditions;
        if($file != ""){
            $company->logo = 'uploads/logos/'.$file->getClientOriginalName();
        }
        $company->save();

        if($file != ""){
            //Creamos una instancia de la libreria instalada   
            $image = Image::make(Input::file('logo'));
            //Ruta donde queremos guardar las imagenes
            $path = public_path().'/uploads/logos/';
            // Cambiar de tamaño
            //$image->resize(300,500);
            // Guardar
            $image->save($path.$file->getClientOriginalName());
        }

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

    public function storeOwner(Request $request){

        $input = Input::all();

        $company = Company::find($input['company_id']);

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
        $request->session()->flash('message.content', 'Owner added successfully!');
        return redirect()->back();

    }    

    public function deleteOwner(Request $request,$user_id){

        $user=GroupUserCompany::where('user_id',$user_id)->delete();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Owner deleted successfully!');
        return redirect()->back();

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
        $file = Input::file('logo');
        $company = Company::find($id);

        $company->business_name = $request->business_name;
        $company->phone = $request->phone;
        $company->address = $request->address;
        $company->email = $request->email;
        $company->tax_number = $request->tax_number;
        $company->pdf_language = $request->pdf_language;
        $company->payment_conditions = $request->payment_conditions;
        if($file != ""){
            $company->logo = 'uploads/logos/'.$file->getClientOriginalName();
        }
        $company->update();

        if($file != ""){
            //Creamos una instancia de la libreria instalada   
            $image = Image::make(Input::file('logo'));
            //Ruta donde queremos guardar las imagenes
            $path = public_path().'/uploads/logos/';
            // Cambiar de tamaño
            //$image->resize(300,500);
            // Guardar
            $image->save($path.$file->getClientOriginalName());
        }


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

    public function updatePaymentConditions(Request $request){

        $company = Company::find($request->company_id);
        $company->payment_conditions=$request->payment_conditions;
        $company->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->back();

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

    public function updateName(Request $request,$id)
    {
        $company = Company::find($id);
        $company->business_name=$request->business_name;
        $company->update();

        return response()->json(['business_name' => $request->business_name]);
    }

    public function updatePhone(Request $request,$id)
    {
        $company = Company::find($id);
        $company->phone=$request->phone;
        $company->update();

        return response()->json(['phone' => $request->phone]);
    }

    public function updateAddress(Request $request,$id)
    {
        $company = Company::find($id);
        $company->address=$request->address;
        $company->update();

        return response()->json(['address' => $request->address]);
    }

    public function updateEmail(Request $request,$id)
    {
        $company = Company::find($id);
        $company->email=$request->email;
        $company->update();

        return response()->json(['address' => $request->email]);
    }

    public function updateTaxNumber(Request $request,$id)
    {
        $company = Company::find($id);
        $company->tax_number=$request->tax_number;
        $company->update();

        return response()->json(['tax_number' => $request->tax_number]);
    }

    public function updatePdfLanguage(Request $request,$id)
    {
        $company = Company::find($id);
        $company->pdf_language=$request->pdf_language;
        $company->update();

        return response()->json(['pdf_language' => $request->pdf_language]);
    }

    public function updatePriceLevels(Request $request,$id)
    {
        $input = Input::all();

        if ((isset($input['price_id'])) && ($input['price_id'][0] != null)) {
            $company_price = CompanyPrice::where('company_id',$id)->delete();
            foreach ($input['price_id'] as $key => $item) {
                $company_price = new CompanyPrice();
                $company_price->company_id=$id;
                $company_price->price_id=$input['price_id'][$key];
                $company_price->save();
            }
        }

        $prices = Price::whereHas('company_price', function ($query) use($id) {
            $query->where('company_id',$id);
        })->pluck('name','id');

        return $prices;
    }
}
