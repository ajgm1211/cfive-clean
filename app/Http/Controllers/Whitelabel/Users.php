<?php

namespace App\Http\Controllers\Whitelabel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\User;
use App\Http\Requests\StoreUsers;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client; 
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class Users extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('companies')
            ->join('users', 'companies.owner', '=', 'users.id')
            ->select('users.*', 'companies.*')
            ->get();
        
            return response()->json($users,200);    
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

         $data = $request->validate([
             'name' => 'required',
             'lastname' => 'required',
             'password' => 'required',
             'email' => 'required|email|unique:users',
             'phone' => 'nullable',
             'type' => 'nullable',
             'company_user_id' => 'nullable',
             'position' => 'nullable',
             'whitelabel' => 'nullable',
         ]);

         User::create($data);
        if ($request->whitelabel == 1){

         $name = $request->get('name');
         $lastname = $request->get('lastname');
         $email = $request->get('email');
         $password = $request->get('password');
         $type = 'admin';

         $client = new \GuzzleHttp\Client([              
             'Accept' => 'application/json',
             'Content-Type' => 'application/x-www-form-urlencoded']);
                 // Create a POST request
             $response = $client->request(
                 'POST',
                 'http://chirix.localhost:8000/admin',
                 [
                     'json' => [
                         'name' => $name,
                         'lastname' => $lastname,
                         'email' => $email,
                         'password' => $password,
                         'type' => $type,
                     ]
                 ]
             );
        }
        return response()->json($data,200);

        
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return response()->json($user,200);    
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
        $user = [
            'name' => 'required:field  ',
            'lastname' => 'required:field ',
            'password' => 'required:field ',
            'email' => 'required|email|unique:users',
        ];

        $user = User::findOrFail($id);
        $user->update($request->all());

        return $request->validate($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json($user,200);    
    }

}
