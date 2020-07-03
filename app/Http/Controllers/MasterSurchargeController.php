<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\Direction;
use App\Surcharge;
use App\TypeDestiny;
use App\CalculationType;
use App\MasterSurcharge;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class MasterSurchargeController extends Controller
{
    public function index()
    {
        return view('masterSurcharge.index');
    }

    public function create()
    {
        $carriers           = Carrier::pluck('name','id');
        $directions         = Direction::pluck('name','id');
        $typedestiny        = TypeDestiny::pluck('description','id');
        $calculationtype    = CalculationType::pluck('name','id');
        $surchargers        = Surcharge::pluck('name','id');
        return view('masterSurcharge.Body-Modals.add',compact('carriers','directions','typedestiny','calculationtype','surchargers'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $surcharger         = $request->surcharger;
        $carrier            = $request->carrier;
        $typedestiny        = $request->typedestiny;
        $calculationtype    = $request->calculationtype;
        $direction          = $request->direction;

        $masterSurcharge = MasterSurcharge::where('carrier_id',$carrier)
            ->where('surcharge_id',$surcharger)
            ->where('typedestiny_id',$typedestiny)
            ->where('calculationtype_id',$calculationtype)
            ->where('direction_id',$direction)
            ->get();
        if(count($masterSurcharge) == 0){
            $masterSurcharge = new MasterSurcharge();
            $masterSurcharge->surcharge_id         = $surcharger;
            $masterSurcharge->carrier_id            = $carrier;
            $masterSurcharge->typedestiny_id        = $typedestiny;
            $masterSurcharge->calculationtype_id    = $calculationtype;
            $masterSurcharge->direction_id          = $direction;
            $masterSurcharge->save();
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Master Surcharge saved successfully!');
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Fail!');
            $request->session()->flash('message.content', 'Master Surcharge already exists!');
        }
        return redirect()->route('MasterSurcharge.index');

    }

    public function show($id)
    {
        if($id == 0){
            $masterSurcharge = DB::select('call proc_master_surcharge()');
            return DataTables::of($masterSurcharge)
                ->addColumn('action', function ($masterSurcharge) {
                    return '
                <a href="#" onclick="AbrirModal(\'editMasterSurcharge\','.$masterSurcharge->id.')"  class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Rate" data-id-MS="'.$masterSurcharge->id.'" data-info="'.$masterSurcharge->name.'" class="eliminarMS"><i class="la la-trash"></i></a>';
                })
                ->editColumn('id', '{{$id}}')->toJson();
        }
    }

    public function edit($id)
    {
        $masterSurcharge = MasterSurcharge::find($id);
        $carriers           = Carrier::pluck('name','id');
        $directions         = Direction::pluck('name','id');
        $typedestiny        = TypeDestiny::pluck('description','id');
        $calculationtype    = CalculationType::pluck('name','id');
        $surchargers        = Surcharge::pluck('name','id');
        return view('masterSurcharge.Body-Modals.edit',compact('masterSurcharge','carriers','directions','typedestiny','calculationtype','surchargers'));
    }

    public function update(Request $request, $id)
    {
        $surcharger         = $request->surcharger;
        $carrier            = $request->carrier;
        $typedestiny        = $request->typedestiny;
        $calculationtype    = $request->calculationtype;
        $direction          = $request->direction;

        $masterSurcharge = MasterSurcharge::where('carrier_id',$carrier)
            ->where('surcharge_id',$surcharger)
            ->where('typedestiny_id',$typedestiny)
            ->where('calculationtype_id',$calculationtype)
            ->where('direction_id',$direction)
            ->get();
        if(count($masterSurcharge) == 0){
            $masterSurcharge = MasterSurcharge::find($id);
            $masterSurcharge->surcharge_id         = $surcharger;
            $masterSurcharge->carrier_id            = $carrier;
            $masterSurcharge->typedestiny_id        = $typedestiny;
            $masterSurcharge->calculationtype_id    = $calculationtype;
            $masterSurcharge->direction_id          = $direction;
            $masterSurcharge->update();
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Master Surcharge updated successfully!');
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Fail!');
            $request->session()->flash('message.content', 'Master Surcharge already exists!');
        }
        return redirect()->route('MasterSurcharge.index');
    }

    public function destroy($id)
    {
        //$globals_id_array = $request->input('id');
		$masterSurcharge = MasterSurcharge::find($id);
		if($masterSurcharge->delete())
		{
			return response()->json(['success' => '1']);
		} else {
			return response()->json(['success' => '2']);
		}
    }
}
