<?php

namespace App\Http\Controllers;

use App\Company;
use App\Currency;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $company = Company::where('user_id',\Auth::id())->first();
        $currencies = Currency::all()->pluck('alphacode','id');
        return view('settings/index',compact('company','currencies'));
    }

    public function update(Request $request,$id)
    {
        $company = Company::findOrFail($id);
        $company->currency_id=$request->currency_id;
        $company->update();

        return response()->json(['message' => 'Ok']);
    }
}
