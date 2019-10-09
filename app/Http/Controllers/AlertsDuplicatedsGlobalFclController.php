<?php

namespace App\Http\Controllers;

use App\StatusAlert;
use App\AlertCompanyUser;
use App\AlertDuplicateGcFcl;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class AlertsDuplicatedsGlobalFclController extends Controller
{
    // CARGA LA VISTA LAS COMPAÑIAS CON G.C DUPLICADOS
    public function index()
    {
        return view('alertsDuplicatedsGCFcl.index');
    }

    // MUESTRA LAS ALERTAS QUE TIENEN G.C DUPLICADOS
    public function create()
    {
        $alerts = AlertDuplicateGcFcl::with('status')->get();
        //dd($alerts);
        return DataTables::of($alerts)
            ->addColumn('status', function ($alerts){ 
                $color='';
                if(strnatcasecmp($alerts->status->name,'pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($alerts->status->name,'false')==0){
                    $color = 'color:#5527f0';
                } else if(strnatcasecmp($alerts->status->name,'solved')==0){
                    $color = 'color:#e07000';
                } else {
                    $color = 'color:#04950f';
                }

                return '<a href="#" onclick="showModal('.$alerts->id.')"style="'.$color.'">'.$alerts->status->name.'</a>
                &nbsp;
                <samp class="la la-pencil-square-o" for="" style="font-size:15px;'.$color.'"></samp>';
                
            })
            ->addColumn('action', function ( $alerts) {
                return '
                    <a href="'.route('globalsduplicated.show',$alerts->id).'"   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Companies G.C. Duplicateds " ">
                        <i style="color:#036aa0" class="la la-eye"></i>
				    </a>';
            })
            //->editColumn('id', '{{$alerts->id}}')->toJson();
            ->toJson();
    }

    public function store(Request $request)
    {
        //
    }

    // CARGA LA VISTA LAS COMPAÑIAS CON G.C DUPLICADOS
    public function show($id)
    {
        return view('alertsDuplicatedsGCFcl.show',compact('id'));
    }

    // MUESTRA LAS COMPAÑIAS QUE TIENEN G.C DUPLICADOS DE LA PRESENTE ALERTA  
    public function edit($id)
    {
        $alertsCmp = AlertCompanyUser::where('alert_dp_id',$id)->with('alert','company_user')->get();
        
        return DataTables::of($alertsCmp)
            ->addColumn('company', function ($alertsCmp){ 
                return $alertsCmp->company_user->name;
            })
            ->addColumn('date', function ($alertsCmp){ 
                return $alertsCmp->alert->date;
            })
            ->addColumn('action', function ( $alertsCmp) {
                return '
                    <a href="'.route('groupglobalsduplicated.show',$alertsCmp->id).'"   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Companies G.C. Duplicateds " ">
                        <i style="color:#036aa0" class="la la-eye"></i>
				    </a>';
            })
            //->editColumn('id', '{{$alerts->id}}')->toJson();
            ->toJson();
    }

    public function updateStatus($id){
        $status = StatusAlert::pluck('name','id');
        $alert  = AlertDuplicateGcFcl::find($id);
        dd($alert,$status);
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
