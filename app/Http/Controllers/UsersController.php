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
use App\Mail\VerifyMail;
use App\VerifyUser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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


    if($request->type == "subuser"){

      $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);
    }

    if(\Auth::user()->type=='company' && $request->type == 'company'){
      $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);   
    }

    $user = new User($request->all());
    $user->password = bcrypt($request->password);
    $user->save();
    if($request->type == "subuser"){
      $user->assignRole('subuser');
    }

    VerifyUser::create([
      'user_id' => $user->id,
      'token' => str_random(40)
    ]);

    \Mail::to($user->email)->send(new VerifyMail($user));

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'You successfully added this user.');

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
    return view('users.add');
  }

  public function resetPass(Request $request,$user)
  {
    $user = User::find($user);
    //Password::sendResetLink(['email' => $user->email]);
    $response = \Password::sendResetLink(['email' => $user->email ] , function (Message $message) {
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

    return view('users.edit', compact('user'));
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
  public function resetmsg($id)
  {
    return view('users/messagereset' ,['userid' => $id]);
  }

  public function datahtml(){
    // temporal


    if(Auth::user()->type == 'admin'  ){

      $user = new User();
      $data = $user->all();
    }

    if(Auth::user()->type == 'company' || Auth::user()->type == 'subuser' ){
      $data =  User::where('company_user_id', "=",Auth::user()->company_user_id)->with('companyUser')->get();
    }

    return view('users/indexhtml', ['arreglo' => $data]);
  }

  public function datajson() {

    $user = new User();

    $response = User::all('name', 'lastname', 'email', 'rol')->toJson();
    return view('users/indexjson')->with('url', $response);

  }

  public function activate(Request $request,$id) {
    $user=User::find($id);
    //dd(json_encode($user->state));
    if($user->state==1){
      $user->state=0;
      $user->update();
      $request->session()->flash('message.nivel', 'success');
      $request->session()->flash('message.title', 'Well done!');
      $request->session()->flash('message.content', 'User has been disabled successfully!');
      return redirect()->route('users.home');
    }else{
      $user->state=1;
      $user->update();
      $request->session()->flash('message.nivel', 'success');
      $request->session()->flash('message.title', 'Well done!');
      $request->session()->flash('message.content', 'User has been activated successfully!');
      return redirect()->route('users.home');
    }
  }

  public function logout(Request $request) {
    Auth::logout();
    return redirect('/login');
  }
  public function notifications()
  {
    return auth()->user()->unreadNotifications()->limit(4)->get()->toArray();
  }

  public function updateNotifications()
  {
    $notifications =  auth()->user()->unreadNotifications()->limit(4)->get();
    foreach($notifications as $notification ){
      $notification->markAsRead();

    }
  }
}