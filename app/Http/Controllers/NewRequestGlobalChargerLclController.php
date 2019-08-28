<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\NewRequestGlobalChargerLcl;
use App\AccountImportationGlobalChargerLcl;

class NewRequestGlobalChargerLclController extends Controller
{
   
    public function index()
    {
        $accounts = AccountImportationGlobalChargerLcl::with('companyuser')->orderBy('id','desc')->get();
        return view('RequestGlobalChargeLcl.index',compact('accounts'));
    }

    public function create()
    {
        //
    }
    
    public function create2()
    {
        $Ncontracts = NewRequestGlobalChargerLcl::with('user','companyuser')->orderBy('id', 'desc')->get();
        //dd($Ncontracts[0]['companyuser']['name']);

        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->companyuser->name;
            })
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->name;
            })
            ->addColumn('validation', function ($Ncontracts) {
                return $Ncontracts->validation;
            })
            ->addColumn('date', function ($Ncontracts) {
                return $Ncontracts->created;
            })
            ->addColumn('updated', function ($Ncontracts) {
                if(empty($Ncontract->updated) != true){
                    return Carbon::parse($Ncontract->updated)->format('d-m-Y h:i:s');
                } else {
                    return '00-00-0000 00:00:00';
                }
            })
            ->addColumn('user', function ($Ncontracts) {
                return $Ncontracts->user->name.' '.$Ncontracts->user->lastname;
            })
            ->addColumn('time_elapsed', function ($Ncontracts) {
                if(empty($Ncontracts->time_total) != true){
                    return $Ncontracts->time_total;
                } else {
                    return '--------';
                }
            })
            ->addColumn('status', function ($Ncontracts) {
                $color='';
                if(strnatcasecmp($Ncontracts->status,'Pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($Ncontracts->status,'Processing')==0){
                    $color = 'color:#5527f0';
                } else if(strnatcasecmp($Ncontracts->status,'Review')==0){
                    $color = 'color:#e07000';
                } else {
                    $color = 'color:#04950f';
                }

                return '<a href="#" onclick="showModal('.$Ncontracts->id.')"style="'.$color.'">'.$Ncontracts->status.'</a>
                &nbsp;
                <samp class="la la-pencil-square-o" for="" style="font-size:15px;'.$color.'"></samp>';
            })
            ->addColumn('action', function ($Ncontracts) {
                return '
                <a href="/ImportationGlobalchargesFcl/RequestProccessGC/'.$Ncontracts->id.'" title="Proccess GC Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="/RequestsGlobalchargers/RequestsGlobalchargersFcl/'.$Ncontracts->id.'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' Number Contract: "  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';
            })
            ->make();
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
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
    
    public function destroyRequest($id)
    {
        //
    }
    
    public function showStatus($id)
    {
        //
    }
}
