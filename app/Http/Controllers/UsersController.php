<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Subuser;
use App\Company;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use App\Mail\VerifyMail;
use App\VerifyUser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notifications\SlackNotification;
use App\QuoteV2;
use App\TermAndConditionV2;
use EventCrisp;
use App\Http\Requests\StoreUsers;
use Illuminate\Validation\Rule;

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
      $user->password = bcrypt($request->password);
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
      $message = $user->name . " " . $user->lastname . " has been registered in Cargofive.";
      $user->notify(new SlackNotification($message));

      VerifyUser::create([
        'user_id' => $user->id,
        'token' => str_random(40)
      ]);

      \Mail::to($user->email)->send(new VerifyMail($user));



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
  public function show($id)
  {
  }

  public function add()
  {
    return view('users.add');
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

    $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'sometimes|required',
        ]);

    $requestForm = $request->all();
    $user = User::find($id);
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

    //Crisp Delete 
    $CrispClient = new EventCrisp();
    $exist =  $CrispClient->checkIfExist($user->email);
    if ($exist == 'true') { //Eliminamos el perfil
      $people = $CrispClient->deleteProfile($user->email);
    }
    return $user;
  }


  public function destroyUser(Request $request, $id)
  {
    if ($request->user_id) {
      QuoteV2::where('user_id', $id)->update(['user_id' => $request->user_id]);
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
      $users = User::pluck('name', 'id');
    } else {
      $users = User::where('company_user_id', Auth::user()->company_user_id)->where('id', '<>', $id)->pluck('name', 'id');
    }

    return view('users/message', ['userid' => $id, 'users' => $users]);
  }
  public function resetmsg($id)
  {
    return view('users/messagereset', ['userid' => $id]);
  }

  public function datahtml()
  {
    // temporal
    if (Auth::user()->type == 'admin') {
      $data = User::all();
    }

    if (Auth::user()->type == 'company' || Auth::user()->type == 'data_entry' || Auth::user()->type == 'subuser') {
      $data =  User::where('company_user_id', "=", Auth::user()->company_user_id)->with('companyUser')->get();
    }

    return view('users/indexhtml', ['arreglo' => $data]);
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
    $notifications =  auth()->user()->unreadNotifications()->limit(4)->get();
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
}
