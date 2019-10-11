<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GroupGlobalsCompanyUser;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class GlobalsDuplicatedFclController extends Controller
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
        $gp_cmpuser = GroupGlobalsCompanyUser::with('alertCompany.company_user','globalcharger')->find($id);
        //dd($gp_cmpuser);
        $data = collect([
           'company_name'       => $gp_cmpuser->alertCompany->company_user->name, 
           'gb_amount'          => $gp_cmpuser->globalcharger->ammount, 
           'gb_validity'        => $gp_cmpuser->globalcharger->validity, 
           'gb_expire'          => $gp_cmpuser->globalcharger->expire 
        ]);
        //dd($data);
        return view('alertsDuplicatedsGCFcl.duplicateds.index',compact('id','data'));
    }

    public function edit($id)
    {
        $globals_dp = DB::select('call  proc_duplicado_globalcharge_fcl('.$id.')');
        dd($globals_dp);
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
