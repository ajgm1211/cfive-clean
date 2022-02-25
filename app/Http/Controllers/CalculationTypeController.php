<?php

namespace App\Http\Controllers;

use HelperAll;
use App\GroupContainer;
use App\CalculationType;
use Illuminate\Http\Request;
use App\BehaviourPerContainer;
use Yajra\Datatables\Datatables;

class CalculationTypeController extends Controller
{
    public function index()
    {
        //dd([null=>'None']+$equipments);
        $equipments = HelperAll::addOptionSelect(GroupContainer::all(), 'id', 'name');
        $behaviourpcs = HelperAll::addOptionSelect(BehaviourPerContainer::all(), 'id', 'name');
        return view('calculationTypes.Body-Modals.add',['equipments'=>$equipments,'behaviourpcs'=>$behaviourpcs]);
    }

    public function create()
    {
        $calculations = CalculationType::all();
        //dd($containerscal);
        return DataTables::of($calculations)
            ->addColumn('action', function ($calculations) {
                $eliminiar_buton = '<a href="#" class="eliminarCalculation" data-id-conatiner-calculation="' . $calculations->id . '" title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                $update_button = '&nbsp;&nbsp;&nbsp;<a href="#" title="Edit">
                    <samp class="la la-edit" onclick="showModal(\'updateCalculation\',' . $calculations->id . ')" style="font-size:20px; color:#031B4E"></samp>
                    </a>
                    ';
                //$button = $update_button.$eliminiar_buton;
                $button = $update_button;

                return $button;
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $calculation = new CalculationType();
        $calculation->name = $request->name;
        $calculation->display_name = strtoupper($request->name);
        $calculation->code = strtoupper($request->code);
        $calculation->group_container_id = $request->equipment;
        $group = $request->group ? true : false;
        $isteu = $request->isteu ? true : false;
        $calculation->gp_pcontainer = $request->gp_pcontainer ? true : false;
        if (!$request->name_prin_ch) {
            $name_prin_inp = $request->name_prin_inp;
        } else {
            $name_prin_inp = 'N\A';
            $calculation->behaviour_pc_id = $request->behaviourpcs;
        }
        $options = ['group' => $group, 'isteu' => $isteu, 'name' => $name_prin_inp];
        $calculation->options = json_encode($options);

        $calculation->save();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Success. Calculation type created.');

        return redirect()->route('ContainerCalculation.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $calculation = CalculationType::find($id);
        $equipments = HelperAll::addOptionSelect(GroupContainer::all(), 'id', 'name');
        $behaviourpcs = HelperAll::addOptionSelect(BehaviourPerContainer::all(), 'id', 'name');
        $options = json_decode($calculation->options);
        if (empty($options)) {
            $options = json_encode(['group' => false, 'isteu' => false, 'name' => 'N\A']);
            $options = json_decode($options);
        }

        return view('calculationTypes.Body-Modals.edit', compact('calculation', 'options','equipments','behaviourpcs'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $calculation = CalculationType::find($id);
        $group = $request->group ? true : false;
        $isteu = $request->isteu ? true : false;
        $calculation->name = $request->name;
        $calculation->group_container_id = $request->equipment;
        $calculation->display_name = strtoupper($request->name);
        $calculation->code = strtoupper($request->code);
        if (!$request->name_prin_ch) {
            $name_prin_inp = $request->name_prin_inp;
        } else {
            $name_prin_inp = 'N\A';
            $calculation->behaviour_pc_id = $request->behaviourpcs;
        }
        $calculation->gp_pcontainer = $request->gp_pcontainer ? true : false;
        $options = ['group' => $group, 'isteu' => $isteu, 'name' => $name_prin_inp];
        $calculation->options = json_encode($options);
        $calculation->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Success. Calculation type updated.');

        return redirect()->route('ContainerCalculation.index');
    }

    public function destroy($id)
    {
        //
    }
}
