<?php

namespace App\Http\Controllers;

use App\GroupSurcharger;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class GroupSurchargerController extends Controller
{
    protected $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

    public function index(Request $request)
    {
        return view('groupsurcharger.index');
    }

    public function create()
    {
        $groups = GroupSurcharger::all();
        return Datatables::of($groups)
            ->addColumn('varation', function ($group) {
                $js = json_decode($group->varation,true);
                $js = json_encode($js['type']);
                return str_replace(',',', ',str_replace(['"','{','}',':',']','['],null,$js));
            })
            ->addColumn('action', function ($group) {
                return '<a href="#" data-id-edit="'.$group->id.'" onclick="showModal(2,'.$group->id.')" class=""><i class="la  la-edit"></i></a>
                        &nbsp 
                        &nbsp  <a href="#" data-id-remove="'.$group->id.'" class="BorrarHarbor"><i class="la  la-remove"></i></a>';
            })
            ->make();
    }

    public function showAdd(){
        return view('groupsurcharger.Body-Modals.add');
    }

    public function store(Request $request)
    {
        $caracteres = $this->caracteres;
        //dd($request->all());
        foreach($request->varation as $varation){
            $arreglo[] =  str_replace($caracteres,'',trim(strtolower($varation)));
        }
        $type['type'] = $arreglo;
        $json = json_encode($type);

        $prueba = GroupSurcharger::create([
            'name'          => $request->name,
            'varation'      => $json
        ]);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your Group was created');
        return back();
    }

    public function show($id)
    {
        $group = GroupSurcharger::find($id);
        $decodejosn = json_decode($group->varation,true);
        $decodejosn = $decodejosn['type'];
        return  view('groupsurcharger.Body-Modals.edit',compact('group','decodejosn'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $caracteres = $this->caracteres;
        foreach($request->varation as $varation){
            $arreglo[] =  str_replace($caracteres,'',trim(strtolower($varation)));
        }

        $type['type'] = $arreglo;
        $json = json_encode($type);

        $group = GroupSurcharger::find($id);
        $group->name          = $request->name;
        $group->varation      = $json;
        $group->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your Gruop was updated');
        return back();
    }

    public function destroy($id)
    {
        try{
            $group = GroupSurcharger::find($id);
            $group->delete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }
}
