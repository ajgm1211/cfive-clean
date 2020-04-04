<?php

namespace App\Http\Controllers;

use Excel;
use App\Harbor;
use App\Country;
use App\Harbor_copy;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class FileHarborsPortsController extends Controller
{


	public function index()
	{
		return  view('harbors.index');
	}


	public function create()
	{
		//$harbors = Harbor::with('country')->get();
		$harbors = \DB::select('call  proc_harbors');
		return Datatables::of($harbors)
			->addColumn('name', function ($harbors) {
				return '<span id="tdname'.$harbors->id.'">'.$harbors->name.'</span>';
			})
			->addColumn('code', function ($harbors) {
				return '<span id="tdcode'.$harbors->id.'">'.$harbors->code.'</span>';
			})
			->addColumn('display_name', function ($harbors) {
				return '<span id="tddisplay_name'.$harbors->id.'">'.$harbors->display_name.'</span>';
			})
			->addColumn('coordinates', function ($harbors) {
				return '<span id="tdcoordinates'.$harbors->id.'">'.$harbors->coordinates.'</span>';
			})
			->addColumn('country_id', function ($harbors) {
				return '<span id="tdcountry'.$harbors->id.'">'.$harbors->country_id.'</span>';
			})
			->addColumn('varation', function ($harbors) {
				return '<span id="tdvaration'.$harbors->id.'">'.$harbors->varation.'</span>';
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
		//return response()->json($request->all());
		$caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

		foreach($request->variation as $variation){
			$arreglo[] =  str_replace($caracteres,'',trim(strtolower($variation)));
		}
		$type['type'] = $arreglo;
		$json = json_encode($type);

		$harbor = new Harbor();
		$harbor->name			= $request->name;
		$harbor->code			= $request->code;
		$harbor->display_name	= $request->display_name;
		$harbor->coordinates	= $request->coordinate;
		$harbor->country_id		= $request->country;
		$harbor->varation		= $json;
		$harbor->save();

		$harbor->load('country');

		$buttons = '<a href="#" data-id-edit="'.$harbor->id.'" onclick="showModal(2,'.$harbor->id.')" class=""><i class="la  la-edit"></i></a>
		&nbsp &nbsp  <a href="#" data-id-remove="'.$harbor->id.'" class="BorrarHarbor"><i class="la  la-remove"></i></a>';

		$data = [
			'name'          => $harbor->name,
			'code'          => $harbor->code,
			'display_name'  => $harbor->display_name,
			'coordinates'   => $harbor->coordinates,
			'country_id'    => $harbor->country->name,
			'varation'      => $json,
			'action'		=> $buttons
		];

		return response()->json(['success' => true, 'data'=> $data]);
		//		$request->session()->flash('message.nivel', 'success');
		//		$request->session()->flash('message.content', 'Your Harbor was created');
		//		return redirect()->route('UploadFile.index');

	}


	public function show($id)
	{
		$country = Country::all()->pluck('name','id');
		$harbors = Harbor::find($id);
		$decodejosn = json_decode($harbors->varation,true);
		$decodejosn = $decodejosn['type'];
		return  view('harbors.Body-Modals.edit',compact('country','harbors','decodejosn'));

	}


	public function edit($id)
	{
		//
	}


	public function update(Request $request, $id)
	{

		$caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

		foreach($request->variation as $variation){
			$arreglo[] =  str_replace($caracteres,'',trim(strtolower($variation)));
		}

		$type['type'] = $arreglo;
		$json = json_encode($type);

		$harbor = Harbor::find($id);
		$harbor->name          = $request->name;
		$harbor->code          = $request->code;
		$harbor->display_name  = $request->display_name;
		$harbor->coordinates   = $request->coordinate;
		$harbor->country_id    = $request->country;
		$harbor->varation      = $json;
		$harbor->update();
		$harbor->load('country');
		$data = [
			'id'          	=> $harbor->id,
			'name'          => $harbor->name,
			'code'          => $harbor->code,
			'display_name'  => $harbor->display_name,
			'coordinates'   => $harbor->coordinates,
			'country_id'    => $harbor->country->name,
			'varation'      => $json
		];
		
		return response()->json(['success' => true, 'data'=> $data]);
		$request->session()->flash('message.nivel', 'success');
		$request->session()->flash('message.content', 'Your Harbor was updated');
		return redirect()->route('UploadFile.index');
	}


	public function destroyharbor($id)
	{
		try{
			$harbor = Harbor::find($id);
			$harbor->delete();
			return 1;
		}catch(\Exception $e){
			return 2;
		}
	}
}
