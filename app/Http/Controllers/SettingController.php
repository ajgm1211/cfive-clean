<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyUser;
use App\Currency;
use App\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $company = User::where('id',\Auth::id())->with('companyUser')->first();
        $currencies = Currency::where('alphacode','=','USD')->orwhere('alphacode','=','EUR')->pluck('alphacode','id');
        return view('settings/index',compact('company','currencies'));
    }

    public function store(Request $request){

        $var = $request->image;
        
        if($var){
            $name = $var->getClientOriginalName();
            \Storage::disk('local')->put($name,  \File::get($var));
        }


        if(!$request->company_id){
            /*$company=CompanyUser::create($request->all());
            User::where('id',\Auth::id())->update(['company_user_id'=>$company->id]);*/
            $company = new CompanyUser();
            $company->name = $request->name;
            $company->address = $request->address;
            $company->phone = $request->phone;
            $company->currency_id = $request->currency;
            $company->logo = $name;
            $companyUser->save();

        }else{
            $company=CompanyUser::findOrFail($request->company_id);
            $company->name=$request->name;
            $company->phone=$request->phone;
            $company->address=$request->address;
            $company->currency_id=$request->currency_id;
            $company->logo = $name;
            $company->update();
        }


        return response()->json(['message' => 'Ok']);
    }
}
