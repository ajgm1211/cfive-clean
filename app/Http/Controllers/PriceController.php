<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyPrice;
use App\FreightMarkup;
use App\InlandChargeMarkup;
use App\LocalChargeMarkup;
use App\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PriceController extends Controller
{
    public function index()
    {
        $prices = Price::all();
        //dd(json_encode($prices->company_name));
        $companies = Company::all()->pluck('business_name','id');
        return view('prices/index', ['prices' => $prices,'companies' => $companies]);
    }

    public function add()
    {
        $prices = Price::all();
        $companies = Company::all()->pluck('business_name','id');
        return view('prices.add', ['prices' => $prices,'companies' => $companies]);
    }

    public function store(Request $request)
    {
        $input = Input::all();

        $price = new Price();
        $price->name = $request->input('name');
        $price->description = $request->input('description');
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
        foreach ($input['subtype_3'] as $key => $item) {
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
            $freight_markup->type = $input['freight_type'][$key];
            $freight_markup->price_id = $price->id;
            $freight_markup->save();
        }

        //Store Local Charges values
        foreach ($input['subtype'] as $key => $item) {
            $local_markup = new LocalChargeMarkup();
            if ((isset($input['local_percent_markup'])) && (count($input['local_percent_markup']) > 0)) {
                $local_markup->percent_markup = $input['local_percent_markup'][$key];
            }
            if ((isset($input['local_fixed_markup'])) && (count($input['local_fixed_markup']) > 0)) {
                $local_markup->fixed_markup = $input['local_fixed_markup'][$key];
            }
            $local_markup->type = $input['local_type'][$key];
            $local_markup->subtype = $input['subtype'][$key];
            $local_markup->currency = $input['local_currency'][$key];
            $local_markup->price_id = $price->id;
            $local_markup->save();
        }

        //Store Inland Charges values
        foreach ($input['subtype_2'] as $key => $item) {
            $inland_markup = new InlandChargeMarkup();
            if ((isset($input['inland_percent_markup'])) && (count($input['inland_percent_markup']) > 0)) {
                $inland_markup->percent_markup = $input['inland_percent_markup'][$key];
            }
            if ((isset($input['inland_fixed_markup'])) && (count($input['inland_fixed_markup']) > 0)) {
                $inland_markup->fixed_markup = $input['inland_fixed_markup'][$key];
            }
            $inland_markup->type = $input['inland_type'][$key];
            $inland_markup->subtype = $input['subtype_2'][$key];
            $inland_markup->currency = $input['inland_currency'][$key];
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
        $price = Price::find($id);

        return view('prices.edit', compact('price'));
    }

    public function update(Request $request, $id)
    {
        $price = Price::find($id);
        $price->update($request->all());
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
        $price->delete();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register deleted successfully!');
        return redirect()->route('prices.index');
    }
}
