<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyPrice;
use App\CompanyUser;
use App\FreightMarkup;
use App\InlandChargeMarkup;
use App\LocalChargeMarkup;
use App\Price;
use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;


class PriceController extends Controller
{
  public function index()
  {
    $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->get();
    $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
    return view('prices/index', ['prices' => $prices,'companies' => $companies]);
  }

  public function add()
  {
    $company_user=CompanyUser::find(\Auth::user()->company_user_id);
    $prices = Price::where('company_user_id',\Auth::user()->company_user_id)->get();
    $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
    $currencies = Currency::pluck('alphacode','id');
    $currency_cfg = Currency::find($company_user->currency_id);
    return view('prices.add', ['prices' => $prices,'companies' => $companies,'currencies'=>$currencies,'currency_cfg'=>$currency_cfg]);
  }


  public function store(Request $request)
  {
    $input = Input::all();

    $price = new Price();
    $price->name = $request->input('name');
    $price->description = $request->input('description');
    $price->company_user_id = \Auth::user()->company_user_id;
    $price->save();

    if (count($request->input("companies")) > 0) {
      foreach ($request->input("companies") as $v) {
        $company_price = new CompanyPrice();
        $company_price->company_id = $v;
        $company_price->price_id = $price->id;
        $company_price->save();
      }
    }

    //dd(json_encode($type));
    //Store Freight Markups
    foreach ($input['freight_type'] as $key => $item) {
      $freight_markup = new FreightMarkup();
      if ((isset($input['freight_percent_markup'])) && (count($input['freight_percent_markup']) > 0)) {
        $freight_markup->percent_markup = $input['freight_percent_markup'][$key];
      }
      if ((isset($input['freight_fixed_markup'])) && (count($input['freight_fixed_markup']) > 0)) {
        $freight_markup->fixed_markup = $input['freight_fixed_markup'][$key];
      }
      if ((isset($input['freight_markup_currency'])) && (count($input['freight_markup_currency']) > 0)) {
        $freight_markup->currency = $input['freight_markup_currency'][$key];
      }
      $freight_markup->price_type_id = $input['freight_type'][$key];
      $freight_markup->price_id = $price->id;
      $freight_markup->save();
    }

    //Store Local Charges values
    foreach ($input['local_type'] as $key => $item) {
      $local_markup = new LocalChargeMarkup();
      if ((isset($input['local_percent_markup_import'][$key]))) {
        $local_markup->percent_markup_import = $input['local_percent_markup_import'][$key];
      }
      if ((isset($input['local_fixed_markup_import'][$key]))) {
        $local_markup->fixed_markup_import = $input['local_fixed_markup_import'][$key];
        $local_markup->currency_import = $input['local_currency_import'][$key];
      }
      if ((isset($input['local_percent_markup_export'][$key]))) {
        $local_markup->percent_markup_export = $input['local_percent_markup_export'][$key];
      }
      if ((isset($input['local_fixed_markup_export'][$key]))) {
        $local_markup->fixed_markup_export = $input['local_fixed_markup_export'][$key];
        $local_markup->currency_export = $input['local_currency_export'][$key];
      }
      $local_markup->price_type_id = $input['local_type'][$key];
      $local_markup->price_id = $price->id;
      $local_markup->save();
    }

    //Store Inland Charges values
    foreach ($input['inland_type'] as $key => $item) {
      $inland_markup = new InlandChargeMarkup();
      if ((isset($input['inland_percent_markup_import'][$key]))) {
        $inland_markup->percent_markup_import = $input['inland_percent_markup_import'][$key];
      }
      if ((isset($input['inland_fixed_markup_import'][$key]))) {
        $inland_markup->fixed_markup_import = $input['inland_fixed_markup_import'][$key];
        $inland_markup->currency_import = $input['inland_currency_import'][$key];
      }
      if ((isset($input['inland_percent_markup_export'][$key]))) {
        $inland_markup->percent_markup_export = $input['inland_percent_markup_export'][$key];
      }
      if ((isset($input['inland_fixed_markup_export'][$key]))) {
        $inland_markup->fixed_markup_export = $input['inland_fixed_markup_export'][$key];
        $inland_markup->currency_export = $input['inland_currency_export'][$key];
      }
      $inland_markup->price_type_id = $input['inland_type'][$key];
      $inland_markup->price_id = $price->id;
      $inland_markup->save();
    }


    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'Register completed successfully!');
    return redirect()->route('prices.index');
  }


  public function edit($id)
  {
    $id = obtenerRouteKey($id);
    $price = Price::find($id);
    $selected_companies = array();
    if (isset($price->company_price)) {
      foreach ($price->company_price as $item) {
        $selected_companies []= $item->company_id;
      }
    }
    $companies = Company::where('company_user_id',\Auth::user()->company_user_id)->pluck('business_name','id');
    $currencies = Currency::all();
    $local_charges = LocalChargeMarkup::where('price_id',$id)->get();

    return view('prices.edit', compact('price','companies','selected_companies','local_charges','currencies'));
  }

  public function update(Request $request, $id)
  {
    $input=input::all();
    $price = Price::find($id);
    $price->update($request->all());

    if (count($request->input("companies")) > 0) {
      CompanyPrice::where('price_id',$price->id)->delete();
      foreach ($request->input("companies") as $v) {
        $company_price = new CompanyPrice();
        $company_price->company_id = $v;
        $company_price->price_id = $price->id;
        $company_price->save();
      }
    }else{
      CompanyPrice::where('price_id',$price->id)->delete();
    }

    FreightMarkup::where('price_id',$price->id)->delete();
    //Store Freight Markups
    foreach ($input['freight_type'] as $key => $item) {
      $freight_markup = new FreightMarkup();
      if ((isset($input['freight_percent_markup'])) && (count($input['freight_percent_markup']) > 0)) {
        $freight_markup->percent_markup = $input['freight_percent_markup'][$key];
      }
      if ((isset($input['freight_fixed_markup'])) && (count($input['freight_fixed_markup']) > 0)) {
        $freight_markup->fixed_markup = $input['freight_fixed_markup'][$key];
      }
      if ((isset($input['freight_markup_currency'])) && (count($input['freight_markup_currency']) > 0)) {
        $freight_markup->currency = $input['freight_markup_currency'][$key];
      }
      $freight_markup->price_type_id = $input['freight_type'][$key];
      $freight_markup->price_id = $price->id;
      $freight_markup->save();
    }

    LocalChargeMarkup::where('price_id',$price->id)->delete();
    //Store Local Charges values
    foreach ($input['local_type'] as $key => $item) {
      $local_markup = new LocalChargeMarkup();
      if ((isset($input['local_percent_markup_import'][$key]))) {
        $local_markup->percent_markup_import = $input['local_percent_markup_import'][$key];
      }
      if ((isset($input['local_fixed_markup_import'][$key]))) {
        $local_markup->fixed_markup_import = $input['local_fixed_markup_import'][$key];
        $local_markup->currency_import = $input['local_currency_import'][$key];
      }
      if ((isset($input['local_percent_markup_export'][$key]))) {
        $local_markup->percent_markup_export = $input['local_percent_markup_export'][$key];
      }
      if ((isset($input['local_fixed_markup_export'][$key]))) {
        $local_markup->fixed_markup_export = $input['local_fixed_markup_export'][$key];
        $local_markup->currency_export = $input['local_currency_export'][$key];
      }
      $local_markup->price_type_id = $input['local_type'][$key];
      $local_markup->price_id = $price->id;
      $local_markup->save();
    }

    InlandChargeMarkup::where('price_id',$price->id)->delete();
    //Store Inland Charges values
    foreach ($input['inland_type'] as $key => $item) {
      $inland_markup = new InlandChargeMarkup();
      if ((isset($input['inland_percent_markup_import'][$key]))) {
        $inland_markup->percent_markup_import = $input['inland_percent_markup_import'][$key];
      }
      if ((isset($input['inland_fixed_markup_import'][$key]))) {
        $inland_markup->fixed_markup_import = $input['inland_fixed_markup_import'][$key];
        $inland_markup->currency_import = $input['inland_currency_import'][$key];
      }
      if ((isset($input['inland_percent_markup_export'][$key]))) {
        $inland_markup->percent_markup_export = $input['inland_percent_markup_export'][$key];
      }
      if ((isset($input['inland_fixed_markup_export'][$key]))) {
        $inland_markup->fixed_markup_export = $input['inland_fixed_markup_export'][$key];
        $inland_markup->currency_export = $input['inland_currency_export'][$key];
      }
      $inland_markup->price_type_id = $input['inland_type'][$key];
      $inland_markup->price_id = $price->id;
      $inland_markup->save();
    }
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'Register updated successfully!');
    return redirect()->route('prices.index');
  }

  public function delete($id)
  {
    $price = Price::find($id);
    return view('prices.delete', compact('price'));
  }

  public function destroy(Request $request,$id)
  {
    $price = Price::find($id);
    try {
      $price->delete();
      return response()->json(['message' => 'Ok']);
    } catch (\Illuminate\Database\QueryException $e) {
      return response()->json(['message' => 'fail']);
    }
  }
}
