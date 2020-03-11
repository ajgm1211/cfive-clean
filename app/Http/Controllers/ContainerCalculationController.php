<?php

namespace App\Http\Controllers;

use HelperAll;
use App\Container;
use App\CalculationType;
use Illuminate\Http\Request;
use App\ContainerCalculation;
use Yajra\Datatables\Datatables;

class ContainerCalculationController extends Controller
{

    public function index()
    {
        //containersCalculations
        return view('containersCalculation.index');
    }

    public function create()
    {
        $containerscal = ContainerCalculation::all();
        $containerscal->load('container','calculationtype');
        //dd($containerscal);
        return DataTables::of($containerscal)
            ->addColumn('container', function ($containerscal) {
                return $containerscal->container->name;
            })
            ->addColumn('calculationtype', function ($containerscal) {
                return $containerscal->calculationtype->name;
            })
            ->addColumn('action', function ($containerscal) {
                $eliminiar_buton = '<a href="#" class="eliminarconatinerCalculation" data-id-conatiner-calculation="'.$containerscal->id.'" title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                $update_button = '&nbsp;&nbsp;&nbsp;<a href="#" title="Edit">
                    <samp class="la la-edit" onclick="showModal(\'update\','.$containerscal->id.')" style="font-size:20px; color:#031B4E"></samp>
                    </a>
                    ';
                $button = $update_button.$eliminiar_buton;
                return $button;
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function loadBodymodalAdd(){
        $containers    = HelperAll::addOptionSelect(Container::all(),'id','name');
        $calculationts = HelperAll::addOptionSelect(CalculationType::all(),'id','name');
        return view('containersCalculation.Body-Modals.add',compact('containers','calculationts'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $containerscal                      = new ContainerCalculation();
        $containerscal->container_id        = $request->container_id;
        $containerscal->calculationtype_id  = $request->calculationT_id;
        $containerscal->save();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Success. Container - Calculation type created.');
        return redirect()->route('ContainerCalculation.index');
    }

    public function show(ContainerCalculation $containerCalculation)
    {
        //
    }

    public function edit($containerCalculation)
    {
        //dd($containerCalculation);
        $containers    = HelperAll::addOptionSelect(Container::all(),'id','name');
        $calculationts = HelperAll::addOptionSelect(CalculationType::all(),'id','name');
        $containerCalculation = ContainerCalculation::find($containerCalculation);        
        return view('containersCalculation.Body-Modals.edit',compact('containers','calculationts','containerCalculation'));
    }

    public function update(Request $request,$id)
    {
        $containerscal                      = ContainerCalculation::find($id);
        $containerscal->container_id        = $request->container_id;
        $containerscal->calculationtype_id  = $request->calculationT_id;
        $containerscal->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Success. Container - Calculation type updated.');
        return redirect()->route('ContainerCalculation.index');
    }

    public function destroy($id)
    {
        try{
            $containerscal = ContainerCalculation::find($id);
            $containerscal->delete();
            return response()->json(['success' => true]);
        } catch(\Exception $e){
            return response()->json(['success' => false]);
        }
    }
}
