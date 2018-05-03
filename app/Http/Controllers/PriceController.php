<?php

namespace App\Http\Controllers;

use App\Company;
use App\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        $prices = Price::all();
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
        Price::create($request->all());

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
