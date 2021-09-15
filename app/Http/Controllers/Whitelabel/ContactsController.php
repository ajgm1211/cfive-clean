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
           $data = $request->validate(  [
               'first_name' => 'required',
               'last_name' => 'required',
               'email' => 'required|email',
               'phone'=> 'nullable',
               'position'=> 'nullable',
               'whitelabel'=> 'nullable',
               'options' => 'json',
           ]);
        // try {
        //     $client = new Client;
        //         $response = $client->post('http://chirix.localhost:8000/user', ['json' => [
        //         'name' => 'a',
        //         'lastname' => 'a',
        //         'email' => 'a2@mail.com',
        //         'type' => 'user'
        //         ]]);
        //     return json_decode($response->getBody()->getContents(), true);
        // } catch (Exception $e) {
        //     throw new Exception($e->getResponse()->getBody()->getContents());        
        // }
        Contact::create($data);
        
         if ($request->whitelabel == 1){

             $name = $request->get('first_name');
             $lastname = $request->get('last_name');
             $email = $request->get('email');
             $type = 'user';
             $phone = $request->get('phone');
             $position = $request->get('position');
            
             $client = new \GuzzleHttp\Client([              
                 'Accept' => 'application/json',
                 'Content-Type' => 'application/x-www-form-urlencoded']);
                     // Create a POST request
                 $response = $client->request(
                     'POST',
                     'http://chirix.localhost:8000/user',
                      [
                          'json' => [
                             'name' => $name,
                             'lastname' => $lastname,
                             'email' => $email,
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
