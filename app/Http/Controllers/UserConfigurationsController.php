<?php

namespace App\Http\Controllers;

use App\Users;
use PrvUserConfigurations;
use App\UserConfiguration;
use Illuminate\Http\Request;

class UserConfigurationsController extends Controller
{

    public function index()
    {
        $user = \Auth::user()->id;
        $json = PrvUserConfigurations::allData($user);
        //dd($json['colors']);
        
        return view('configuration.index',compact('json','user'));
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
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //dd($request->input('notifications-request-importation-fcl'));
        $nrifcl     = true;
        $nrilcl     = true;
        $nrigcfcl   = true;
        
        if($request->input('notifications-request-importation-fcl') == null){
            $nrifcl = false;
        } else {
            $nrifcl = true;
        }
        
        if($request->input('notifications-request-importation-lcl') == null){
            $nrilcl = false;
        } else {
            $nrilcl = true;
        }
        
        if($request->input('notifications-request-importation-gcfcl') == null){
            $nrigcfcl = false;
        } else {
            $nrigcfcl = true;
        }
        
        //dd($request->all());
        $json['notifications']['request-importation-fcl']   = $nrifcl;
        $json['notifications']['request-importation-lcl']   = $nrilcl;
        $json['notifications']['request-importation-gcfcl'] = $nrigcfcl;
        
        $conf = UserConfiguration::where('user_id',$id)->first();
        $conf->paramerters = json_encode($json);
        $conf->save();
        
        $request->session()->flash('message.content', 'Updated Notifications' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('UserConfiguration.index');
    }

    public function destroy($id)
    {
        //
    }
}
