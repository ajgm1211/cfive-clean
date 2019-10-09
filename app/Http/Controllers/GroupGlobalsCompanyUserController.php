<?php

namespace App\Http\Controllers;

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
                return $groupsCmp->status->name;
            })
            ->addColumn('action', function ( $groupsCmp) {
                return '
                    <a href="'.route('groupglobalsduplicated.show',$groupsCmp->id).'"   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Companies G.C. Duplicateds " ">
                        <i style="color:#036aa0" class="la la-eye"></i>
				    </a>';
            })
            //->editColumn('id', '{{$alerts->id}}')->toJson();
            ->toJson();
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
