<?php

namespace App\Http\Controllers;

use HelperAll;
use App\CalculationType;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class CalculationTypeController extends Controller
{
  public function index(){
    return view('calculationTypes.Body-Modals.add');
  }

  public function create(){
    $calculations = CalculationType::all();
    //dd($containerscal);
    return DataTables::of($calculations)
      ->addColumn('action', function ($calculations) {
        $eliminiar_buton = '<a href="#" class="eliminarCalculation" data-id-conatiner-calculation="'.$calculations->id.'" title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

        $update_button = '&nbsp;&nbsp;&nbsp;<a href="#" title="Edit">
                    <samp class="la la-edit" onclick="showModal(\'updateCalculation\','.$calculations->id.')" style="font-size:20px; color:#031B4E"></samp>
                    </a>
                    ';
        //$button = $update_button.$eliminiar_buton;
        $button = $update_button;
        return $button;
      })
      ->editColumn('id', '{{$id}}')->toJson();
  }

  public function store(Request $request){
    $calculation        = new CalculationType();
    $calculation->name  = $request->name;
    $calculation->code	= $request->code;
    $group = $request->group ? true : false;
    $isteu = $request->isteu ? true : false;
    $options = array('group'=>$group,'isteu'=>$isteu);
    $calculation->options	= json_encode($options);

    $calculation->save();

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.content', 'Success. Calculation type created.');
    return redirect()->route('ContainerCalculation.index');
  }

  public function show($id){
    //
  }

  public function edit($id){
    $calculation = CalculationType::find($id);   

    $options = json_decode($calculation->options);
    if(empty($options)){
      $options = json_encode(array('group'=>false,'isteu'=>false));
      $options = json_decode($options);
    }


    return view('calculationTypes.Body-Modals.edit',compact('calculation','options'));
  }

  public function update(Request $request, $id){
    $calculation        = CalculationType::find($id);
    $group = $request->group ? true : false;
    $isteu = $request->isteu ? true : false;
    $calculation->name  = $request->name;
    $calculation->code	= $request->code;
    $options = array('group'=>$group,'isteu'=>$isteu);
    $calculation->options	= json_encode($options);
    $calculation->update();

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.content', 'Success. Calculation type updated.');
    return redirect()->route('ContainerCalculation.index');
  }

  public function destroy($id){
    //
  }

}
