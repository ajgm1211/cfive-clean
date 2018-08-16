<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NewContractRequest;
use App\User;
use App\Notifications\N_general;

class NewContractRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Ncontract = NewContractRequest::all();
        dd($Ncontract);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        //dd($request->all());

        $time   = new \DateTime();
        $now    = $time->format('dmY_His');
        $now2   = $time->format('Y-m-d');
        $file   = $request->file('file');
        $ext    = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );

        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('Requestimporfcl');
        }
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

        $typeVal = 1;
        $arreglotype = '';

        if($request->type == 2){
            // Rate And Surcharger 
            $typeVal    = 2;
            $type['type'] = array('type'=>$typeVal,'values'=>$request->valuesCurrency);
        } else {
            $type['type'] = array('type'=>$typeVal);
            $arreglotype = '"type":'.$typeVal;
        }

        $origin  = [];
        $destiny = [];
        $carrier = [];    

        $DatOriBol = false;
        $DatDesBol = false;
        $DatCarBol = false;

        if($request->DatOri == true){
            $origin = $request->origin;
            $DatOriBol = true;
        } 

        if($request->DatDes == true){
            $destiny = $request->destiny;
            $DatDesBol = true;
        } 

        if($request->DatCar == true){
            $carrier = $request->carrier;
            $DatCarBol = true;
        } 

        $data['data'] = array('DatOri'  => $DatOriBol,
                              'origin'  => $origin,
                              'DatDes'  => $DatDesBol,
                              'destiny' => $destiny,
                              'DatCar'  => $DatCarBol,
                              'carrier' => $carrier
                             );
        $type         = json_encode($type);
        $data         = json_encode($data);

        $Ncontract  = new NewContractRequest();
        $Ncontract->namecontract    = $request->name;
        $Ncontract->numbercontract  = $request->number;
        $Ncontract->validation      = $request->validation_expire;
        $Ncontract->company_user_id = $request->CompanyUserId;
        $Ncontract->namefile        = $nombre;
        $Ncontract->user_id         = $request->user;
        $Ncontract->created         = $now2;
        $Ncontract->type            = $type;
        $Ncontract->data            = $data;
        $Ncontract->save();
        
        $user = User::find($request->user);
        $admins = User::where('type','admin')->get();
        $message = 'has created an new request: '.$Ncontract->id;
        foreach($admins as $userNotifique){
            $userNotifique->notify(new N_general($user,$message));
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your request was created');
        return redirect()->route('contracts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Ncontract = NewContractRequest::find($id);
        dd($Ncontract);
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
