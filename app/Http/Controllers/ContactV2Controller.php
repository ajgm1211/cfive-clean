<?php

namespace App\Http\Controllers;

use Session;
use App\Contact;
use App\Company;
use App\FailedContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\WhiteLabelTrait;
use App\Http\Traits\FileHandlerTrait;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\FailedContactResource;

class ContactV2Controller extends Controller
{
    use WhiteLabelTrait, FileHandlerTrait;

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
            'contact.first_name' => 'required',
            'contact.last_name' => 'required',
            'contact.email' => 'required|email',
            'contact.options' => 'json',
        ]);

        $request->request->add(
                [
                    'options' => $options
                ]
        );

        $addToWhitelabel = $request->get('toWhiteLabel');

        $contact = Contact::create($request->get('contact'));

        if ( $addToWhitelabel == true) {
            
            $contact->name              = $contact->first_name;
            $contact->lastname          = $contact->last_name;
            $contact->name_company      = $contact->company->business_name;
            $contact->password          = 'password';
            $contact->confirm_password  = 'password';
            $contact->type              = 'user';
            $contact->unique_code       = $contact->company->unique_code;

            $contactToTransfer = $contact->only(['name', 'lastname', 'email', 'phone', 'position', 'name_company', 'password', 'confirm_password', 'type', 'unique_code' ]);
  
            $apiContacts = $this->transferEntityToWhiteLabel([$contactToTransfer],'user');
        
            if ($apiContacts['status'] == 200) {
                $newcontact = Contact::find($contact->id);
                $newcontact->update(array('whitelabel'=> 1));
                return response()->json(['message' => 'successfully transfer to whitelabel'], 200);
            }else{
                return response()->json(['message' => 'unsuccessfully transfer to whitelabel'], 500);
            }

        }

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
        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->select('id','business_name','whitelabel')->get();
        $data = compact('companies');
        return response()->json(['data' => $data]);
    }

    /**
     * Failed contacts view to storage.
     *
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */

    public function failed()
    {
        return view('contacts.v2.failed');
    }
    
    /**
     * Failed contacts to storage.
     *
     * @param  \App\Contact $contact
     * @return \Illuminate\Http\Response
     */

    public function failedList(Request $request)
    {
        $failedContacts = FailedContact::FilterByCurrentCompanyUser()->orderBy('id', 'asc')->filter($request);
        return FailedContactResource::collection($failedContacts);
    }

    public function failedRetrieve(Request $request, FailedContact $failed)
    {
        return new FailedContactResource($failed);
    }

    public function failedUpdate(Request $request, FailedContact $failed)
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
                if ($failed) {
                    $contact = new Contact($newContact);
                    $newContact->save();
                    $failed->delete();
                }
            DB::commit();
            return new ContactResource($contact);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public function createContactsMassive(Request $request)
    {

        $user = \Auth::user();
        $validate = $this->validateFile($request, 'file');
        
        if($validate){
            $filestored = $this->storeFile('contacts', $request->file('file'));
        }

        $file = $this->getFile('contacts', $filestored);
        $errors = 0;
        Session::put('massiveCreationErrors', 0);
        Session::put('contacts', []);
        Session::put('failedContacts', []);
        $sessionError = Session::get('massiveCreationErrors');
        $toWhiteLabel = $request->get('whitelabel') == true ? 1 : 0;
        $company_id = $request->get('company_id');
        
        Excel::load($file, function($reader) use ($user, $errors, $sessionError, $company_id, $toWhiteLabel) {

            $company_user_id = $user->company_user_id;
            $reader->each(function($sheet) use ($company_id, $errors, $sessionError, $company_user_id, $toWhiteLabel) {
                if(!is_null($sheet['first_name']) && !is_null($sheet['last_name']) && !is_null($sheet['email']) && !is_null($sheet['phone']) && !is_null($sheet['position'])){
                    if(filter_var($sheet['email'], FILTER_VALIDATE_EMAIL)){
                        $contact = $this->parseContact($sheet, $company_id, $toWhiteLabel);
                        Session::push('contacts', $contact);
                    }else{
                        $sheet['email'] = "ERROR";
                        $failedContact = $this->parseFailedContact($sheet, $company_user_id, $company_id);
                        Session::push('failedContacts', $failedContact);
                        $errors = isset($sessionError) ? Session::get('massiveCreationErrors') + 1 :  0 + 1;
                        Session::put('massiveCreationErrors', $errors);
                    }
                }else{
                    if (!is_null($sheet['first_name']) || !is_null($sheet['last_name']) || !is_null($sheet['email']) || !is_null($sheet['phone']) || !is_null($sheet['position'])) {
                        $sheet['email'] = "ERROR";
                        $failedContact = $this->parseFailedContact($sheet, $company_user_id, $company_id);
                        Session::push('failedContacts', $failedContact);
                        $errors = isset($sessionError) ? Session::get('massiveCreationErrors') + 1 :  0 + 1;
                        Session::put('massiveCreationErrors', $errors);
                    }
                }
            });
        });
        
        $errors = isset($sessionError) ? Session::get('massiveCreationErrors') : 0;
        Session::forget('massiveCreationErrors');
        return response('successful creation with '.$errors.' failed contacts.', 200);
    }

    public function parseContact($sheet, $company_id, $toWhiteLabel){
        $contact= [
            'first_name'=> $sheet['first_name'],
            'last_name'=> $sheet['last_name'],
            'phone'=> $sheet['phone'],
            'position'=> $sheet['position'],
            'whitelabel'=> $toWhiteLabel,
            'company_id'=> $company_id
        ];
        return $contact;
    }

    public function parseFailedContact($sheet, $company_user_id, $company_id){
        $failedContact = [
            'first_name' => $sheet['first_name'] ?? 'ERROR',
            'last_name'=> $sheet['last_name'] ?? 'ERROR',
            'email'=> $sheet['email'] ?? 'ERROR',
            'phone'=> $sheet['phone'] ?? 'ERROR',
            'position'=> $sheet['position'] ?? 'ERROR',
            'company_id'=> $company_id ?? 'ERROR',
            'company_user_id'=> $company_user_id ?? 'ERROR'
        ];
        return $failedContact;
    }

    public function createContact($sheet, $company_id, $toWhiteLabel)
    { 
        $result = [];
        $contacts = Session::get('contacts');
        if (isset($contacts)) {
            foreach ($contacts as $key => $value) {
                $contact = Contact::firstOrCreate(
                    ['email' => $sheet['email']],
                    [
                        'first_name'=> $value['first_name'],
                        'last_name'=> $value['last_name'],
                        'phone'=> $value['phone'],
                        'position'=> $value['position'],
                        'whitelabel'=> $value['whitelabel'],
                        'company_id'=> $value['company_id']
                    ]);
                array_push($result, $contact);
            }
        }
        
        return $result;
    }

    public function createFailedContact($sheet, $company_id, $company_user_id)
    { 
        $failedcontacts = Session::get('failedCompanies');
        FailedContact::insert($failedcontacts);
    }


    public function transferToWhiteLabel(Request $request){
        $contactsToSearch = $request->get('contacts');
        $contacts         = Contact::whereIn('id',$contactsToSearch);
        
        $contactsToTransfer =   $contacts->company()->get()->map(function ($contact) {
                                    $contact->name = $contact->first_name;
                                    $contact->lastname = $contact->last_name;
                                    $contact->name_company = $contact->company->business_name;
                                    $contact->password = 'password';
                                    $contact->confirm_password = 'password';
                                    $contact->type = 'user';
                                    $contact->unique_code = $contact->company->unique_code;

                                    return $contact->only(['name', 'lastname', 'email', 'phone', 'position', 'name_company', 'password', 'confirm_password', 'type', 'unique_code' ]);
                                });
        $apiContacts = $this->transferEntityToWhiteLabel($contactsToTransfer->toArray(),'user');
        if ($apiContacts['status'] == 200) {
            $contacts->update(array('whitelabel'=> 1));
            return response()->json(['message' => 'successfully transfer to whitelabel'], 200);
        }else{
            return response()->json(['message' => 'unsuccessfully transfer to whitelabel'], 500);
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

    public function downloadTemplateFile()
    {
        return Storage::disk('DownLoadFile')->download('contacts_template.xlsx');
    }

    public function exportContacts(Request $request, $format)
    {

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