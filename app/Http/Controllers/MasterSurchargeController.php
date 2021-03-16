<?php

namespace App\Http\Controllers;

use App\CalculationType;
use App\Carrier;
use App\Direction;
use App\GroupContainer;
use App\MasterSurcharge;
use App\Surcharge;
use App\TypeDestiny;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class MasterSurchargeController extends Controller
{
    public function index()
    {
        $carriers = Carrier::pluck('name', 'id');
        $directions = Direction::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');
        $calculationtype = CalculationType::where('group_container_id', '=', null)->pluck('name', 'id');
        $equiments = GroupContainer::pluck('name', 'id');

        return view('masterSurcharge.index', compact('carriers', 'directions', 'typedestiny', 'calculationtype', 'equiments'));
    }

    public function create()
    {
        $carriers = Carrier::pluck('name', 'id');
        $directions = Direction::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');
        $calculationtype = CalculationType::where('group_container_id', '=', null)->pluck('name', 'id');
        $surchargers = Surcharge::where('company_user_id', '=', null)->pluck('name', 'id');
        $equiments = GroupContainer::pluck('name', 'id');

        return view('masterSurcharge.Body-Modals.add', compact('carriers', 'directions', 'typedestiny', 'calculationtype', 'surchargers', 'equiments'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $surcharger = $request->surcharger;
        $carrier = $request->carrier;
        $typedestiny = $request->typedestiny;
        $calculationtype = $request->calculationtype;
        $direction = $request->direction;
        if (! empty($request->equiment_id)) {
            $equiment_id = $request->equiment_id;
        } else {
            $equiment_id = null;
        }

        $masterSurcharge = MasterSurcharge::where('carrier_id', $carrier)
            ->where('surcharge_id', $surcharger)
            ->where('typedestiny_id', $typedestiny)
            ->where('calculationtype_id', $calculationtype)
            ->where('direction_id', $direction)
            ->where('group_container_id', $equiment_id)
            ->get();
        if (count($masterSurcharge) == 0) {
            $masterSurcharge = new MasterSurcharge();
            $masterSurcharge->surcharge_id = $surcharger;
            $masterSurcharge->carrier_id = $carrier;
            $masterSurcharge->typedestiny_id = $typedestiny;
            $masterSurcharge->calculationtype_id = $calculationtype;
            $masterSurcharge->direction_id = $direction;
            $masterSurcharge->group_container_id = $equiment_id;
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

    public function show(Request $request, $id)
    {
        if ($id == 0) {
            $masterSurcharge = DB::select('call proc_master_surcharge()');
            $masterSurcharge = collect($masterSurcharge);
            //$masterSurcharge = $masterSurcharge->where('carrier_id', '=',2);
            if ($request->carrier_id != null) {
                $masterSurcharge = $masterSurcharge->where('carrier_id', '=', $request->carrier_id);
            }
            if ($request->typedestiny_id != null) {
                if ($request->typedestiny_id != 3) {
                    $masterSurcharge = $masterSurcharge->where('typedestiny_id', '=', $request->typedestiny_id);
                }
            }
            if ($request->calculationtype_id != null) {
                $masterSurcharge = $masterSurcharge->where('calculationtype_id', '=', $request->calculationtype_id);
            }
            if ($request->direction_id != null) {
                if ($request->direction_id != 3) {
                    $masterSurcharge = $masterSurcharge->where('direction_id', '=', $request->direction_id);
                }
            }

            if ($request->equiment_id != null) {
                $masterSurcharge = $masterSurcharge->whereIn('equiment_id', [$request->equiment_id, null]);
            }

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
        $carriers = Carrier::pluck('name', 'id');
        $directions = Direction::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');
        $calculationtype = CalculationType::where('group_container_id', '=', $masterSurcharge->group_container_id)
            //->orWhere('group_container_id','=',null)
            ->pluck('name', 'id');
        $surchargers = Surcharge::where('company_user_id', '=', null)->pluck('name', 'id');
        $equiments = GroupContainer::pluck('name', 'id');
        //dd($masterSurcharge,$surchargers);
        return view('masterSurcharge.Body-Modals.edit', compact('masterSurcharge', 'carriers', 'directions', 'typedestiny', 'calculationtype', 'surchargers', 'equiments'));
    }

    public function update(Request $request, $id)
    {
        $surcharger = $request->surcharger;
        $carrier = $request->carrier;
        $typedestiny = $request->typedestiny;
        $calculationtype = $request->calculationtype;
        $direction = $request->direction;

        if (! empty($request->equiment_id)) {
            $equiment_id = $request->equiment_id;
        } else {
            $equiment_id = null;
        }

        $masterSurcharge = MasterSurcharge::where('carrier_id', $carrier)
            ->where('surcharge_id', $surcharger)
            ->where('typedestiny_id', $typedestiny)
            ->where('calculationtype_id', $calculationtype)
            ->where('direction_id', $direction)
            ->where('group_container_id', $equiment_id)
            ->get();
        if (count($masterSurcharge) == 0) {
            $masterSurcharge = MasterSurcharge::find($id);
            $masterSurcharge->surcharge_id = $surcharger;
            $masterSurcharge->carrier_id = $carrier;
            $masterSurcharge->typedestiny_id = $typedestiny;
            $masterSurcharge->calculationtype_id = $calculationtype;
            $masterSurcharge->direction_id = $direction;
            $masterSurcharge->group_container_id = $equiment_id;
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
        if ($masterSurcharge->delete()) {
            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '2']);
        }
    }

    public function getCalculationsEquiment(Request $request)
    {
        $equiment_id = $request->equiment;
        $calculationsT = CalculationType::where('group_container_id', $equiment_id)
            //->orWhere('group_container_id',null)
            ->pluck('name', 'id');
        $data = [];
        foreach ($calculationsT as $key => $name) {
            array_push($data, ['id' => $key, 'text'=> $name]);
        }

        return response()->json(['results'=>$data]);
    }
}
