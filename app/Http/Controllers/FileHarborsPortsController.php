<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use App\Harbor;
use App\Harbor_copy;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Datatables;


class FileHarborsPortsController extends Controller
{

    
    public function index()
    {

        $country = Country::all()->pluck('name','id');
        return  view('harbors.index',compact('country'));
    }

   
    public function create()
    {
        $harbors = Harbor::with('country')->get();
        return Datatables::of($harbors)
            ->addColumn('country_id', function ($harbor) {
                return $harbor->country['name'];
            })
            ->addColumn('action', function ($harbor) {
                return '<a href="#" data-id-edit="'.$harbor->id.'" onclick="showModal(2,'.$harbor->id.')" class=""><i class="la  la-edit"></i></a>
                        &nbsp 
                        &nbsp  <a href="#" data-id-remove="'.$harbor->id.'" class="BorrarHarbor"><i class="la  la-remove"></i></a>';
            })
            
            ->make();
    }

    public function loadviewAdd(){
        
        $country = Country::all()->pluck('name','id');
        return  view('harbors.Body-Modals.add',compact('country'));
        
    }    
    
    public function store(Request $request)
    {  
        
        foreach($request->variation as $variation){
            $arreglo[] =  strtolower($variation);
        }
        $type['type'] = $arreglo;
        $json = json_encode($type);
        
        $prueba = Harbor::create([
                        'name'          => $request->name,
                        'code'          => $request->code,
                        'display_name'  => $request->display_name,
                        'coordinates'   => $request->coordinate,
                        'country_id'    => $request->country,
                        'varation'      => $json
                    ]);
        
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your Harbor was created');
        return redirect()->route('UploadFile.index');
        
    }


    public function show($id)
    {
        $country = Country::all()->pluck('name','id');
        $harbors = Harbor::find($id);
        $decodejosn = json_decode($harbors->varation);
        
        return  view('harbors.Body-Modals.edit',compact('country','harbors','decodejosn'));
        
    }


    public function edit($id)
    {
        //
    }

  
    public function update(Request $request, $id)
    {
        //
    }

 
    public function destroy($id)
    {
        //
    }
}
