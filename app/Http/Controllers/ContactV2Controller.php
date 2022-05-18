<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Company;
use App\FailedContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\FailedContactResource;

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
        $validated = $request->validate([
            'contact.first_name' => 'required',
            'contact.last_name' => 'required',
            'contact.phone' => 'required',
            'contact.email' => 'required',
            'contact.position' => 'required',
            'contact.company_id' => 'required',
        ]);

        $newContact = $request->get('contact');

        try {
            DB::beginTransaction();

                if ($contact && $validated) {
                    $contact->fill($newContact)->save();
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
     * Clone the specified resource in storage.
     *
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function getCompanies()
    {
        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->select('id','business_name')->get();
        $data = compact('companies');
        return response()->json(['data' => $data]);
    }

    /**
     * Failed contacts view to storage.
     *
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */

    public function failed(){
        return view('contacts.v2.failed');
    }
    
    /**
     * Failed contacts to storage.
     *
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function failedList(Request $request){
        $failedContacts = FailedContact::FilterByCurrentCompanyUser()->orderBy('id', 'asc')->filter($request);
        return FailedContactResource::collection($failedContacts);
    }

    public function failedEdit(){
        return view('contacts.v2.failedEdit');
    }

    public function failedRetrieve(Request $request, FailedContact $failed)
    {
        return new FailedContactResource($failed);
    }

    public function failedUpdate(Request $request, FailedContact $failed){
        $validated = $request->validate([
            'contact.first_name' => 'required',
            'contact.last_name' => 'required',
            'contact.phone' => 'required',
            'contact.email' => 'required',
            'contact.position' => 'required',
            'contact.company_id' => 'required',
        ]);
        try {
            DB::beginTransaction();
                if ($failed) {
                    $newContact = new Contact($validated['contact']);
                    $newContact->save();
                    $failed->delete();
                }
            DB::commit();
            return new ContactResource($newContact);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
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

    public function downloadTemplateFile(){
        return Storage::disk('DownLoadFile')->download('contacts_template.xlsx');
    }

    public function exportContacts(Request $request, $format){

        $filename       = "Contacts";
        $titleSheet1    = "Contacts";
        $Sheet1header   = ['first_name','last_name','phone','email','position', 'company'];
        $sheet1Content  = Contact::filterByCurrentCompany()->company()->get()->toArray();
        $formatExport   = [];

        return Excel::create($filename, function($excel) use ($titleSheet1, $Sheet1header, $sheet1Content, $formatExport) {

            $excel->sheet($titleSheet1, function($sheet) use ($Sheet1header, $sheet1Content, $formatExport){
                
                $sheet->row(1, $Sheet1header);
                $sheet->row(1, function($row){
                    $row->setBackground('#006bfa');
                    $row->setFontColor('#ffffff');
                    $row->setAlignment('center');
                });
                
                $sheetRow = 2;
                foreach ($sheet1Content as $key => $value) {

                    $formatExport['first_name'] = $value['first_name'] != null ? $value['first_name'] : 'N/A';
                    $formatExport['last_name'] = $value['last_name'] != null ? $value['last_name'] : 'N/A';
                    $formatExport['phone'] = $value['phone'] != null ? $value['phone'] : 'N/A';
                    $formatExport['email'] = $value['email'] != null ? $value['email'] : 'N/A';
                    $formatExport['position'] = $value['position'] != null ? $value['position'] : 'N/A';
                    $formatExport['company'] = $value['company']['business_name'] != null ? $value['company']['business_name'] : 'N/A';
                    
                    $sheet->row($sheetRow, $formatExport);
                    $sheet->row($sheetRow, function($row){
                        $row->setAlignment('center');
                    });
                    
                    $sheetRow ++;
                    $formatExport= [];
                }
            });
        })->export($format);
    }
}