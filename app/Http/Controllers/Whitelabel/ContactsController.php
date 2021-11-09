<?php

namespace App\Http\Controllers\Whitelabel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\Contact;
use App\Http\Requests\StoreContact;
use GuzzleHttp\Client; 
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use App\CompanyUser; 
use App\SettingsWhitelabel;
use Exception;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contact = Contact::all();

        return response()->json($contact,200);    
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
        $user = \Auth::user();
        $user_id = $user->id;
        $company_user = $user->companyUser()->first();
        $company_user_id = $company_user->id;
        $url = SettingsWhitelabel::where('company_user_id', $company_user_id)->select('url')->get();  
        $url_1= $url[0]['url'] ;
        $url_final = $url_1. '/user';


           $this->validate($request,  [
               'first_name' => 'required',
               'last_name' => 'required',
               'email' => 'required|email',
               'phone'=> 'nullable',
               'position'=> 'nullable',
               'whitelabel'=> 'nullable',
               'options' => 'json',
               'password_wl' =>'nullable|min:8',
           ]);

           $data = Contact::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'whitelabel'=> $request->whitelabel,
            'options'=> $request->options,
            'password_wl' => \Hash::make($request->password_wl)
        ]);
  
         if ($request->whitelabel == 1){

             $name = $request->get('first_name');
             $lastname = $request->get('last_name');
             $email = $request->get('email');
             $type = 'user';
             $phone = $request->get('phone');
             $position = $request->get('position');
             $password = $request->get('password_wl');
            
             $client = new \GuzzleHttp\Client([              
                 'Accept' => 'application/json',
                 'Content-Type' => 'application/x-www-form-urlencoded']);
                     // Create a POST request
                 $response = $client->request(
                     'POST',
                     $url_final,
                      [
                          'json' => [
                             'name' => $name,
                             'lastname' => $lastname,
                             'email' => $email,
                             'type' => $type,
                             'phone' => $phone,
                             'position' => $position,
                             'password' => $password,
                          ]
                      ]
                     );
        }

        return response()->json([
            'message' => 'Contact successfully registered',
            'data' => $data
        ], 201);    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = Contact::find($id);

        return response()->json($contact,200);    
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
        $contact = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'options' => 'json',
        ];

        $contact = Contact::findOrFail($id);
        $contact->update($request->all());
        
        return $request->validate($contact);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return response()->json($contact,200);    
    }
}
