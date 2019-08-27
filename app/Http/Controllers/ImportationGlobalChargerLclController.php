<?php

namespace App\Http\Controllers;

use App\Harbor;
use App\Region;
use App\Country;
use App\Carrier;
use App\CompanyUser;
use App\TypeDestiny;
use Illuminate\Http\Request;

class ImportationGlobalChargerLclController extends Controller
{

    public function index()
    {
        $harbor         = Harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('importationGlobalChargerLcl.index',compact('harbor','region','country','carrier','companysUser','typedestiny'));
    }

    public function UploadFileNewContract(Request $request){
        
    }
    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
