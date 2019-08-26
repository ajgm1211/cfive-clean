<?php

namespace App\Http\Controllers;

use App\Country;
use App\Airport;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class CountryController extends Controller
{

    public function index()
    {
        return  view('countries.index');
    }

    public function create()
    {
        $countries = Country::all();
        return Datatables::of($countries)
            ->addColumn('action', function ($countries) {
                return '<a href="#" data-id-edit="'.$countries->id.'" onclick="showModal(2,'.$countries->id.')" class=""><i class="la  la-edit"></i></a>
                        &nbsp 
                        &nbsp  <a href="#" data-id-remove="'.$countries->id.'" class="BorrarCountry"><i class="la  la-remove"></i></a>';
            })

            ->make();
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
        foreach($request->variation as $variation){
            $arreglo[] =  str_replace($caracteres,'',trim(strtolower($variation)));
        }
        $type['type'] = $arreglo;
        $json = json_encode($type);
        Country::create([
            'name'          => $request->name,
            'code'          => $request->code,
            'continent'     => $request->continent,
            'variation'      => $json
        ]);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your Country was created');
        return redirect()->route('Countries.index');
    }

    public function loadviewAdd(){
        return  view('countries.Body-Modals.add');

    }

    public function show($id)
    {
        $country = Country::find($id);
        $decodejosn = json_decode($country->variation,true);
        $decodejosn = $decodejosn['type'];
        return  view('countries.Body-Modals.edit',compact('country','decodejosn'));
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {
        $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];
        foreach($request->variation as $variation){
            $arreglo[] =  str_replace($caracteres,'',trim(strtolower($variation)));
        }

        $type['type'] = $arreglo;
        $json = json_encode($type);

        $country             = Country::find($id);
        $country->name       = $request->name;
        $country->code       = $request->code;
        $country->continent  = $request->continent;
        $country->variation  = $json;
        $country->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your Country was updated');
        return redirect()->route('Countries.index');
    }

    public function destroy($id)
    {
        //
    }

    public function destroycountrie($id)
    {
        try{
            $country = Country::find($id);
            $country->delete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }
}
