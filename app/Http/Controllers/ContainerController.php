<?php

namespace App\Http\Controllers;

use HelperAll;
use App\Container;
use App\GroupContainer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class ContainerController extends Controller
{
    public function index()
    {
        $equipments = HelperAll::addOptionSelect(GroupContainer::all(),'id','name');
        return view('containers.Body-Modals.add',compact('equipments'));
    }

    public function create()
    {
        $containers = Container::with('groupContainer')->get();
        return DataTables::of($containers)
            ->addColumn('group', function ($containers) {
                return $containers->groupContainer->name;
            })
            ->addColumn('action', function ($containers) {
                $eliminiar_buton = '<a href="#" class="eliminarCalculation" data-id-conatiner-calculation="'.$containers->id.'" title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                $update_button = '&nbsp;&nbsp;&nbsp;<a href="#" title="Edit">
                    <samp class="la la-edit" onclick="showModal(\'updateContainer\','.$containers->id.')" style="font-size:20px; color:#031B4E"></samp>
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
        $container      = new Container();
        $container->name            = $request->name;
        $container->code            = $request->code;
        $container->gp_container_id = $request->equipment_id;
        $optional = $request->optional ? true : false;
        $data = ['optional' => $optional];
        if($request->column_db_ch){
            $column = true;
        } else {
            $column = false;            
        }
        $data ['column'] = $column;
        if($request->column_db_ch){
            $name_prin_inp = $request->column_db;
        } else if(empty($request->column_db_ch)){
            $name_prin_inp = 'N\A';
        }
        $data['column_name']    = $name_prin_inp;
        $container->options     = json_encode($data);
        //dd($container);
        $container->save();
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Success. Container created.');
        return redirect()->route('ContainerCalculation.index');

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $container = Container::find($id);   
        $equipments = HelperAll::addOptionSelect(GroupContainer::all(),'id','name');
        $options = json_decode($container->options);
        if(empty($options)){
            $options = json_encode(array('group'=>false,'isteu'=>false,'name'=>'N\A'));
            $options = json_decode($options);
        }


        return view('containers.Body-Modals.edit',compact('container','equipments','options'));
    }

    public function update(Request $request, $id)
    {
        
        //dd($request->all());
        $container = Container::find($id);
        $container->name            = $request->name;
        $container->code            = $request->code;
        $container->gp_container_id = $request->equipment_id;
        $optional = $request->optional ? true : false;
        $data = ['optional' => $optional];
        if($request->column_db_ch){
            $column = true;
        } else {
            $column = false;            
        }
        $data ['column'] = $column;
        if($request->column_db_ch){
            $name_prin_inp = $request->column_db;
        } else if(empty($request->column_db_ch)){
            $name_prin_inp = 'N\A';
        }
        $data['column_name']    = $name_prin_inp;
        $container->options     = json_encode($data);
        $container->save();
        //dd($container);
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Success. Container updated.');
        return redirect()->route('ContainerCalculation.index');

        
    }

    public function destroy($id)
    {
        //
    }

    public function getContainerByGroup(Request $request)
	{
		$id_group = $request->id_group;
		$containers = Container::where('gp_container_id',$id_group)->get()->map(function ($containers) {
            return $containers->only(['id', 'code']);
        });

        return $containers;

	}
}
