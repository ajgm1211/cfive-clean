<?php

namespace App\Http\Controllers;

use App\Company;
use App\Delegation;
use App\UserDelegation;
use App\Http\Requests\StoreUsers;
use App\Mail\VerifyMail;
use App\Notifications\SlackNotification;
use App\QuoteV2;
use App\Contract;
use App\ContractLcl;
use App\NewContractRequest;
use App\NewContractRequestLcl;
use App\TermAndConditionV2;
use App\User;
use App\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Intercom\IntercomClient;
use Laracasts\Flash\Flash;

class UsersController extends Controller
{

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
    public function store(StoreUsers $request)
    {

        try {
            if ($request->type == "subuser" || $request->type == "data_entry") {

                $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);
            }

            if (\Auth::user()->type == 'company' && $request->type == 'company') {
                $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);
            }

            $user = new User($request->all());
            $user->password = $request->password;
            $user->save();

            if ($request->type == "subuser") {
                $user->assignRole('subuser');
            }
            if ($request->type == "company") {
                $user->assignRole('company');
            }
            if ($request->type == "admin") {
                $user->assignRole('administrator');
            }
            if ($request->type == "data_entry") {
                $user->assignRole('data_entry');
            }
            
            if($request->delegation_id != null){
                $user->storeDelegation($request->delegation_id,$user->id);
            }

            $message = $user->name . " " . $user->lastname . " has been registered in Cargofive.";
            $user->notify(new SlackNotification($message));

            VerifyUser::create([
                'user_id' => $user->id,
                'token' => str_random(40),
            ]);

            \Mail::to($user->email)->send(new VerifyMail($user));

            // INTERCOM CLIENTE

            $client = new IntercomClient('dG9rOmVmN2IwNzI1XzgwMmFfNDdlZl84NzUxX2JlOGY5NTg4NGIxYjoxOjA=', null, ['Intercom-Version' => '1.4']);
            $this->intercom($client, $user);

            if ($user->company_user_id != '') {

                $client->users->create([
                    "email" => $user->email,
                    "user_id" => $user->id,
                    "name" => $user->name,
                    "companies" => [
                        [
                            "name" => $user->companyUser->name,
                            "company_id" => $user->company_user_id,
                        ],
                    ],
                ]);
            } else {

                $client->users->create([
                    "email" => $user->email,
                    "user_id" => $user->id,
                    "name" => $user->name,
                ]);
            }

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Record created successfully.');

            return redirect('users/home');
        } catch (\Exception $e) {

            $error = 'An error has occurred. Try again';
            // if ($e->errorInfo[0] == '23000') {
            //   $error = 'The email address entered is already registered';
            // } else {
            //   $error = 'An error has occurred. Try again';
            // }
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', '');
            $request->session()->flash('message.content', $error);

            return redirect('users/home');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = user::find(\Auth::user()->id);
        return view('users.update', compact('user'));
    }

    public function add()
    {
        $delegation= Delegation::where('company_user_id', '=', Auth::user()->company_user_id)->get();
        return view('users.add',compact('delegation'));
    }

    public function resetPass(Request $request, $user)
    {
        $user = User::find($user);
        //Password::sendResetLink(['email' => $user->email]);
        $response = \Password::sendResetLink(['email' => $user->email], function (Message $message) {
            $message->subject($this->getEmailSubject());
        });
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'The email has been sent successfully ');
        return redirect('users/home');
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
        
        $id_ud=UserDelegation::where('users_id','=',$id)->first();


        if($id_ud==null){
            $userd=null;
        }else{
            $userd=Delegation::find($id_ud->delegations_id);
        }
        
        $delegation= Delegation::where('company_user_id', '=', Auth::user()->company_user_id)->get();

        return view('users.edit', compact('user','userd','delegation'));
    }

    public function UpdateUser(Request $request, $id)
    {

        $request->validate([
          'name' => 'required',
          'lastname' => 'required',
          'email' => [
              'required',
              Rule::unique('users')->ignore($id),
          ],
          'password' => 'sometimes|confirmed',
          'password_confirmation' => 'required_with:password',
      ]);

    $requestForm = $request->all();
    $user = User::findOrFail($id);
    $user->update($requestForm);

        $user->update($requestForm);

        if ($request->ajax()) {
            return response()->json('User updated successfully!');
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record updated successfully ');

        return redirect()->route('user.info');
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

        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'sometimes|confirmed',
            'password_confirmation' => 'required_with:password',
        ]);
        $requestForm = $request->all();
        $user = User::findOrFail($id);
        $id_ud=UserDelegation::where('users_id','=',$id)->first();

        if($id_ud == null && $request->delegation_id!=null){
            $delegation= new UserDelegation();
            $delegation->users_id=$user->id;
            $delegation->delegations_id=$request->delegation_id;
            $delegation->save();
        }elseif($id_ud != null && $request->delegation_id!=null){
            $delegation = UserDelegation::find($id_ud->id);
            $delegation ->delegations_id =$request->delegation_id;
            $delegation->update();
        }elseif($id_ud != null && $request->delegation_id==null){
            $id_ud=UserDelegation::where('users_id','=',$id)->first();
            $id_ud->delete();
        }

        $roles = $user->getRoleNames();
        if (!$roles->isEmpty()) {
            $user->removeRole($roles[0]);
        }

        if ($request->type == "admin") {
            $user->assignRole('administrator');
        }
        if ($request->type == "subuser") {
            $user->assignRole('subuser');
        }
        if ($request->type == "company") {
            $user->assignRole('company');
        }
        if ($request->type == "data_entry") {
            $user->assignRole('data_entry');
        }

        $user->update($requestForm);

        if ($request->ajax()) {
            return response()->json('User updated successfully!');
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record updated successfully ');

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
        $user->delete();

        $client = new IntercomClient('dG9rOmVmN2IwNzI1XzgwMmFfNDdlZl84NzUxX2JlOGY5NTg4NGIxYjoxOjA=', null, ['Intercom-Version' => '1.4']);
        $cliente = $client->users->getUsers(["email" => $user->email]);

        if ($cliente->total_count > 0) {
            foreach ($cliente->users as $cli) {
                $client->users->archiveUser($cli->id);
            }
        }

        return $user;
    }

    public function destroyUser(Request $request, $id)
    {
        if ($request->user_id) {
            QuoteV2::where('user_id', $id)->update(['user_id' => $request->user_id]);
            NewContractRequest::where('user_id', $id)->update(['user_id' => $request->user_id]);
            NewContractRequestLcl::where('user_id', $id)->update(['user_id' => $request->user_id]);
            Contract::where('user_id', $id)->update(['user_id' => $request->user_id]);
            ContractLcl::where('user_id', $id)->update(['user_id' => $request->user_id]);
            Company::where('owner', $id)->update(['owner' => $request->user_id]);
            TermAndConditionV2::where('user_id', $id)->update(['user_id' => $request->user_id]);
        }

        $user = self::destroy($id);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully deleted : ' . $user->name . ' ' . $user->lastname);

        return redirect()->route('users.home');
    }

    public function destroymsg($id)
    {
        if (Auth::user()->type == 'admin') {
            $users = User::get()->pluck('full_name', 'id');
        } else {
            $users = User::where('company_user_id', Auth::user()->company_user_id)->where('id', '<>', $id)->get()->pluck('full_name', 'id');
        }

        $id_ud=UserDelegation::where('users_id','=',$id)->first();
        if($id_ud==null){
            $delegation=false;
        }else{
            $delegation=true;
        }

        return view('users/message', ['userid' => $id, 'users' => $users, 'delegation'=>$delegation]);
    }
    public function resetmsg($id)
    {
        return view('users/messagereset', ['userid' => $id]);
    }

    public function datahtml()
    {

        if (Auth::user()->type == 'admin') {
            $data = User::all(); 
        }
        if (Auth::user()->type == 'company' || Auth::user()->type == 'data_entry' || Auth::user()->type == 'subuser') {
            $data = User::where('company_user_id', "=", Auth::user()->company_user_id)->with('companyUser')->get();;
        }
        foreach($data as $u){
            $ud=UserDelegation::where('users_id','=',$u->id)->first();
            $delegation=Delegation::find($ud['delegations_id']);
            $u['userD']=$delegation['name'];
            $arreglo[]=$u;
        }
        // dd($arreglo);
        return view('users/indexhtml', ['arreglo' => $arreglo]);
    }

    public function datajson()
    {

        $user = new User();

        $response = User::all('name', 'lastname', 'email', 'rol')->toJson();
        return view('users/indexjson')->with('url', $response);
    }

    public function activate(Request $request, $id)
    {
        $user = User::find($id);
        //dd(json_encode($user->state));
        if ($user->state == 1) {
            $user->state = 0;
            $user->update();
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'User has been disabled successfully!');
            return redirect()->route('users.home');
        } else {
            $user->state = 1;
            $user->update();
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'User has been activated successfully!');
            return redirect()->route('users.home');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
    public function notifications()
    {
        return auth()->user()->unreadNotifications()->limit(4)->get()->toArray();
    }
    public function notifications_read()
    {
        return auth()->user()->notifications()->limit(4)->get()->toArray();
    }

    public function updateNotifications()
    {
        $notifications = auth()->user()->unreadNotifications()->limit(4)->get();
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }
    }

    public function verify(Request $request, $id)
    {
        $user = User::find($id);
        if ($user->verified == 0) {
            $user->verified = 1;
            $user->update();
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'User has been verified successfully!');
            return redirect()->route('users.home');
        } else {
            $user->verified = 0;
            $user->update();
            $request->session()->flash('message.nivel', 'warning');
            $request->session()->flash('message.title', 'Alert!');
            $request->session()->flash('message.content', 'This user has been placed as unverified!');
            return redirect()->route('users.home');
        }
    }
    public function intercom($client, $user)
    {

        $cliente = $client->users->getUsers(["email" => $user->email]);
        if ($cliente->total_count > 1) {
            foreach ($cliente->users as $u) {
                if ($u->type == "user") {
                    if ($u->user_id != $user->id) {
                        $client->users->archiveUser($u->id);
                    }
                }
            }
        }
    }
}
