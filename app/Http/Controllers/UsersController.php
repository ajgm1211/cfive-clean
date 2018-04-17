<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Subuser;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        $usuario = new User($request->all());
        $usuario->password = bcrypt($usuario->password);


        $usuario->save();

        if($usuario->type == "subuser"){


            $subuser = new Subuser();
            $subuser->company_id = $request->id_company;
            $subuser->user()->associate($usuario);
            $subuser->save();


        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this user.');
        return redirect('users/home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }


    public function add()
    {

        $user = new User();
        $companyall = User::all('id','type','name_company')->where('type', '=', 'company')->pluck('name_company', 'id');
        return view('users.add',compact('companyall'));
    }
    public function resetPass($user)
    {

        $user = User::find($user);
        //Password::sendResetLink(['email' => $user->email]);
        $response = \Password::sendResetLink(['email' => $user->email ] , function (Message $message) {
            $message->subject($this->getEmailSubject());
        });
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        // colocar donde el id sea diferente de 
        $companyall = User::all('id','type','name_company')->where('type', '=', 'company')->where('id', '!=', $id)->pluck('name_company', 'id');
        // Condicion para cagar la compañia del subusuario
        if($user->type == "subuser"){

            $subuser = Subuser::find($user->subuser->id);
            $datosSubuser = User::find($subuser->company_id);
            return view('users.edit', compact('user','companyall','datosSubuser'));
        }


        return view('users.edit', compact('user','companyall'));
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
        $requestForm = $request->all();
        $user = User::find($id);

        if($user->type == "subuser"){

            $subuser = Subuser::find($user->subuser->id);
            $subuser->company_id  =  $request->id_company;
            $subuser->update();

        }


        $user->update($requestForm);
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You upgrade has been success ');
        return redirect()->route('users.home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if($user->type == "subuser"){

            $subuser = Subuser::find($user->subuser->id);
            $subuser->delete();
        }
        $user->delete();
        return $user;
    }
    public function destroyUser(Request $request,$id)
    {

        $user = self::destroy($id);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete : '.$user->name.' '.$user->lastname);
        return redirect()->route('users.home');

    }

    public function destroymsg($id)
    {
        return view('users/message' ,['userid' => $id]);

    }

    public function datahtml(){
        // temporal
        if(Auth::user()->type == 'admin' || Auth::user()->type == 'subuser' ){
            $user = new User();
            $data = $user->all();

        }
        if(Auth::user()->type == 'company' ){

            $data =  User::whereHas('subuser', function($q)
                                    {
                                        $q->where('company_id', '=', Auth::user()->id);
                                    })->get();
        }

        return view('users/indexhtml', ['arreglo' => $data]);


    }

    public function datajson() {

        $user = new User();

        $response = User::all('name', 'lastname', 'email', 'rol')->toJson();
        return view('users/indexjson')->with('url', $response);

    }
    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }



}