<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Storage;

class ContactV2Controller extends Controller
{
    public function index()
    {
        return view('contacts.v2.index');
    }

    //Retrieves all data needed for search processing and displaying
    public function data(Request $request)
    {

        $contact = Contact::whereHas('company', function ($query) {
            $query->where('company_user_id', '=', \Auth::user()->company_user_id);
        })->Company();

        $data = compact(
            'contact'
        );

        return response()->json(['data' => $data]);
    }

    public function list(Request $request)
    {
        $results = Contact::filterByCurrentCompany()->Company()->orderBy('id', 'asc')->filter($request);

        return ContactResource::collection($results);
    }

    public function retrieve(Request $request, Contact $contact)
    {
        return new ContactResource($contact);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $options         = null;
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'options' => 'json',
        ]);

        $request->request->add(
                [
                    'options' => $options
                ]
        );

        $contact = Contact::create($request->all());

        return new ContactResource($contact);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('contacts.v2.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        try {
            DB::beginTransaction();

                if ($contact) {
                    $contact->fill($request->input())->save();
                }

            DB::commit();
            return new ContactResource($contact);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    /**
     * Clone the specified resource in storage.
     *
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Contact $contact)
    {
        $new_contact = $contact->duplicate();

        return new ContactResource($new_contact);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(['message' => 'Ok']);
    }

    /**
     * Mass remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        $toDestroy = Contact::whereIn('id', $request->input('ids'))->get();
        foreach ($toDestroy as $td) {
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }

    public function downloadTemplatefile(){
        return Storage::disk('DownLoadFile')->download('contacts_template.xlsx');
    }

}
