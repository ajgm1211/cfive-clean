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
        $currencies = Currency::all()->pluck('alphacode','id');
        return view('settings/index',compact('company','currencies'));
    }

    public function store(Request $request){

        if(!$request->company_id){
            $company=CompanyUser::create($request->all());
            User::where('id',\Auth::id())->update(['company_user_id'=>$company->id]);
        }else{
            $company=CompanyUser::findOrFail($request->company_id);
            $company->name=$request->name;
            $company->phone=$request->phone;
            $company->address=$request->address;
            $company->currency_id=$request->currency_id;
            $company->update();
        }


        return response()->json(['message' => 'Ok']);
    }
}
