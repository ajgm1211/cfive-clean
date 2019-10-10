<?php

namespace App\Http\Controllers;

use App\StatusAlert;
use App\AlertCompanyUser;
use Illuminate\Http\Request;
use App\GroupGlobalsCompanyUser;
use Yajra\Datatables\Datatables;

class GroupGlobalsCompanyUserController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {

        $alertCmp = AlertCompanyUser::with('company_user')->find($id);
        return view('alertsDuplicatedsGCFcl.groupGlobals.index',compact('id','alertCmp'));
    }

    public function edit($id)
    {

        $groupsCmp = GroupGlobalsCompanyUser::where('alert_cmpuser_id',$id)->with('status')->get();
        //dd($groupsCmp);
        return DataTables::of($groupsCmp)
            ->addColumn('status', function ($groupsCmp){ 
                //return $groupsCmp->status->name;

                $color='';
                if(strnatcasecmp($groupsCmp->status->name,'pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($groupsCmp->status->name,'false')==0){
                    $color = 'color:#5527f0';
                } else if(strnatcasecmp($groupsCmp->status->name,'solved')==0){
                    $color = 'color:#e07000';
                } else {
                    $color = 'color:#04950f';
                }

                return '<a href="#" onclick="showModal('.$groupsCmp->id.')" id="statusHrf'.$groupsCmp->id.'" class="statusHrf'.$groupsCmp->id.'" style="'.$color.'">'.$groupsCmp->status->name.'</a>
                &nbsp;
                <span class="la la-pencil-square-o" for="" id="statusSamp'.$groupsCmp->id.'" class="statusHrf'.$groupsCmp->id.'" style="font-size:15px;'.$color.'"></span>';


            })
            ->addColumn('action', function ( $groupsCmp) {
                return '
                    <!--<a href="'.route('groupglobalsduplicated.show',$groupsCmp->id).'"   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Companies G.C. Duplicateds " ">
                        <i style="color:#036aa0" class="la la-eye"></i>
				    </a>-->
                    &nbsp;&nbsp;
                    <a href="#" onclick="DestroyGroup('.$groupsCmp->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Detele " ">
                        <i style="color:#036aa0" class="la la-trash"></i>
				    </a>
                    ';
            })
            //->editColumn('id', '{{$alerts->id}}')->toJson();
            ->toJson();
    }

    public function showStatus($id){
        $status     = StatusAlert::pluck('name','id');
        $groupsCmp  = GroupGlobalsCompanyUser::find($id);
        //dd($groupsCmp,$status);
        return view('alertsDuplicatedsGCFcl.groupGlobals.Body-Modals.edit',compact('status','groupsCmp'));
    }

    public function updateStatus(Request $request,$id){
        try{
            $groupsCmp  = GroupGlobalsCompanyUser::find($id);
            $groupsCmp->status_alert_id = $request->status_id;
            $groupsCmp->update();
            $groupsCmp  = GroupGlobalsCompanyUser::with('status')->find($id);
            if(strnatcasecmp($groupsCmp->status->name,'pending')==0){
                $color = '#f81538';
            } else if(strnatcasecmp($groupsCmp->status->name,'false')==0){
                $color = '#5527f0';
            } else if(strnatcasecmp($groupsCmp->status->name,'solved')==0){
                $color = '#e07000';
            } else {
                $color = '#04950f';
            }
            return response()->json(['data'=> 1,'status' => $groupsCmp->status->name,'color'=> $color]);
        } catch(\Exception $e){
            return response()->json(['data'=> 2]);            
        }
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        try{
            $groupsCmp  = GroupGlobalsCompanyUser::find($id);
            $groupsCmp->delete();
            return response()->json(['data'=> 1]);
        } catch(\Exception $e){
            return response()->json(['data'=> 2]);            
        }
    }
}
