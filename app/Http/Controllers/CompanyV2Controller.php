<?php

namespace App\Http\Controllers;

use Session;
use App\Company;
use App\Contact;
use App\FailCompany;
use App\CompanyPrice;
use App\GroupUserCompany;
use App\SettingsWhitelabel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Traits\SearchTrait;
use App\Http\Traits\WhiteLabelTrait;
use App\Http\Traits\FileHandlerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\FailCompanyResource;

class CompanyV2Controller extends Controller
{
    //
    use SearchTrait, WhiteLabelTrait, FileHandlerTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('companies.v2.index');
    }

    //Retrieves all data needed for search processing and displaying
    public function data(Request $request)
    {
        $user_id      = \Auth::user()->id;
        $company_user_id = \Auth::user()->company_user_id;
        $subtype      = \Auth::user()->options['subtype'];

        if($subtype === 'comercial') {
            //Subtype comercial solo pueden acceder a sus propias compañias            
            $companies = Company::where('company_user_id', $company_user_id)
                        ->where('owner', $user_id) 
                        ->with('groupUserCompanies.user')->User()->CompanyUser();            
        } else {
            $companies = Company::where('company_user_id', $company_user_id)
                                ->with('groupUserCompanies.user')
                                ->User()
                                ->CompanyUser();
        }

        $companies = $companies->get()->toArray();

        $data = compact(
            'companies'
        );

        return response()->json(['data' => $data]);
    }

    public function list(Request $request)
    {
        $results = Company::filterByCurrentUser()->orderBy('id', 'asc')->filter($request);

        //return $results;
        return CompanyResource::collection($results);
    }

    public function retrieve(Request $request, Company $company)
    {
        return new CompanyResource($company);
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
        $filepath_tmp    = null;
        $file            = Input::file('logo');
        $user_id         = \Auth::user()->id;
        $company_user_id = \Auth::user()->company_user_id;

        //dd($request->all());
        $data = $request->validate([
            'company.business_name' => 'required',
            'company.logo' => 'max:1000',
            'company.options' => 'json',
        ]);

        if ($file != null) {
            $filepath_tmp = 'Logos/Clients/' . $file->getClientOriginalName();
        }

        $newCompany = $request->get('company');
        $newCompany += [ "company_user_id" => $company_user_id ];
        $newCompany += [ "owner" => $user_id ];
        $newCompany += [ "options" => $options ];
        $newCompany += [ "logo" => $filepath_tmp ];
        $newCompany += [ "unique_code" => Str::random(8)];
        $newCompany += [ "whitelabel" => $request->get('whitelabel') == true ? 1 : 0];
        

        $company = Company::create($newCompany);

        if ($file != null) {
            $this->saveLogo($company, $file);
        }

        $this->saveExtraData($request, $company);
        
        if ($company->whitelabel) {

            $companyToTransfer = $company->only(['business_name', 'phone', 'address', 'email', 'unique_code']);

            $api = $this->transferEntityToWhiteLabel([$companyToTransfer], 'shipper');   
            if ($api['status'] != 200) {
                $body= json_decode($api['body']);
                return response()->json(['message' => 'unsuccessfully transfer to whitelabel'], 500);
            }
        }

        return new CompanyResource($company);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('companies.v2.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $filepath_tmp    = null;
        $file            = Input::file('logo');
        $companyForUpdate = $request->all();
        try {
            DB::beginTransaction();

                if ($file != null) {
                    $filepath_tmp = 'Logos/Clients/' . $file->getClientOriginalName();
                    $request->request->add(['logo' => $filepath_tmp]);
                }
                if ($company) {
                    $company->fill($companyForUpdate['company'])->save();
                }
                
                if ($file != null) {
                    $this->saveLogo($company, $file);
                }

            DB::commit();
            
            if ($company->whitelabel == 1) {
                $apiCompanies = $this->transferEntityToWhiteLabel($company->toArray(),'shipper');
                if ($apiCompanies['status'] != 200) {
                    return response()->json(['message' => 'unsuccessfully transfer to whitelabel'], 500);
                }
            }
            
            return new CompanyResource($company);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    /**
     * Clone the specified resource in storage.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Company $company)
    {
        $new_company = $company->duplicate();

        return new CompanyResource($new_company);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();

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
        $toDestroy = Company::whereIn('id', $request->input('ids'))->get();
        foreach ($toDestroy as $td) {
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }

    public function saveLogo($company, $file)
    {
        $update_company_url = Company::findOrFail($company->id);
        $update_company_url->logo = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
        $update_company_url->update();
        $filepath = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
        $name = $file->getClientOriginalName();
        Storage::disk('logos')->put($name, file_get_contents($file), 'public');
        $s3 = Storage::disk('s3_upload');
        $s3->put($filepath, file_get_contents($file), 'public');
    }

    //save the company price and the group user company
    public function saveExtraData($data, $company)
    {

        if ((isset($data['price_id'])) && (count($data['price_id']) > 0)) {
            foreach ($data['price_id'] as $key => $item) {
                    $company_price = new CompanyPrice();
                    $company_price->company_id = $company->id;
                    $company_price->price_id = $data[$key];
                    $company_price->save();
            }
        }
        if ((isset($data['users'])) && (count($data['users']) > 0)) {
            foreach ($data['users'] as $key => $item) {
                    $userCompany_group = new GroupUserCompany();
                    $userCompany_group->user_id = $data[$key];
                    $userCompany_group->company()->associate($company);
                    $userCompany_group->save();
            }
        }
        return true;
    }

    public function downloadTemplateFile()
    {
        return Storage::disk('DownLoadFile')->download('company_template.xlsx');
    }
    
    public function failed()
    {
        return view('companies.v2.failed');
    }
    
    public function failedList(Request $request)
    {
        $failedCompanies = FailCompany::filterByCurrentUser()->orderBy('id', 'asc')->filter($request);

        return FailCompanyResource::collection($failedCompanies);
    }

    public function failedRetrieve(Request $request, FailCompany $failed)
    {
        return new FailCompanyResource($failed);
    }

    public function failedUpdate(Request $request, FailCompany $failed)
    {

        $validated = $request->validate([
            'company.business_name' => 'required',
            'company.phone' => 'required',
            'company.address' => 'required',
            'company.email' => 'required',
            'company.tax_number' => 'required',
            'company.owner' => 'required',
        ]);

        $newCompany = $request->get('company');
        try {
            DB::beginTransaction();
                if ($failed) {
                    $company = new Company($newCompany);
                    $company->save();
                    $failed->delete();
                }
            DB::commit();
            return new CompanyResource($company);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public function contactByCompanyList(Request $request, $company)
    {

        $contactsByCompany = Contact::filterByCurrentEditingCompany($company)->orderBy('id', 'asc')->filter($request);
        return  ContactResource::collection($contactsByCompany);
    }

    public function transferToWhiteLabel(Request $request)
    {
        $companiesToSearch = $request->get('companies');
        $addContacts       = $request->get('addToContact');
        $companies         = Company::whereIn('id',$companiesToSearch);
        
        $companiesToTransfer =  $companies->get()->map(function ($company) {
                                    return $company->only(['business_name', 'phone', 'address', 'email', 'unique_code']);
                                });
        $apiCompanies = $this->transferEntityToWhiteLabel($companiesToTransfer->toArray(),'shipper');
                                
        if ($apiCompanies['status'] == 200) {

            $companies->update(array('whitelabel' => 1 ));

            if ($addContacts == true) {

                $companies_ids = [];

                foreach ($companiesToSearch as $key => $value) {
                    array_push($companies_ids,$value['id']);
                }

                $contacts = Contact::whereIn('company_id', $companies_ids);
                $contactsToTransfer = $contacts->company()->get()->map(function ($contact) {
                                        $contact->name = $contact->first_name;
                                        $contact->lastname = $contact->last_name;
                                        $contact->name_company = $contact->company->business_name;
                                        $contact->password = 'password';
                                        $contact->confirm_password = 'password';
                                        $contact->type = 'user';
                                        $contact->unique_code = $contact->company->unique_code;

                                        return $contact->only(['name', 'lastname', 'email', 'phone', 'position', 'name_company', 'password', 'confirm_password', 'type', 'unique_code' ]);
                                    });
                            
                $apiContacts = $this->transferEntityToWhiteLabel($contactsToTransfer->toArray(), 'user');

                if ($apiContacts['status'] == 200) {
                    $contacts->update(array('whitelabel'=>1));
                }else{
                    return response()->json(['message' => 'The companies were transferred successfully but there was an error with the transfer of contacts'], 500);
                }

            }

            return response()->json(['message' => 'successfully transfer to whitelabel'], 200);

        }else{
            return response()->json(['message' => 'Unsuccessfull transfer to whitelabel'], 500);
        }
    }

    public function createCompaniesMassive(Request $request)
    {
        Session::forget('massiveCreationErrors','companies','failedCompanies');
        $user = \Auth::user();
        $validate = $this->validateFile($request, 'file');
        
        if($validate){
            $filestored = $this->storeFile('companies', $request->file('file'));
        }

        $file = $this->getFile('companies', $filestored);
        $errors = 0;
        Session::put('massiveCreationErrors', 0);
        Session::put('companies', []);
        Session::put('failedCompanies', []);
        $sessionError = Session::get('massiveCreationErrors');
        $toWhiteLabel = $request->get('whitelabel') == true ? 1 : 0;

        Excel::load($file, function($reader) use ($user, $errors, $sessionError, $toWhiteLabel) {

            $company_user_id = $user->company_user_id;
            $owner = $user->id;
            $reader->each(function($sheet) use ($company_user_id, $owner, $errors, $sessionError, $toWhiteLabel) {
                if(!is_null($sheet['business_name']) && !is_null($sheet['phone']) && !is_null($sheet['email']) && !is_null($sheet['address']) && !is_null($sheet['tax_number'])){
                    if(filter_var($sheet['email'], FILTER_VALIDATE_EMAIL)){
                        $company = $this->parseCompany($sheet, $company_user_id, $toWhiteLabel, $owner);
                        Session::push('companies', $company);
                    }else{
                        $sheet['email'] = "ERROR";
                        $failedCompany = $this->parseFailedCompany($sheet, $company_user_id, $owner);
                        Session::push('failedCompanies', $failedCompany);
                        $errors = isset($sessionError) ? Session::get('massiveCreationErrors') + 1 :  0 + 1;
                        Session::put('massiveCreationErrors', $errors);
                    }
                }else{
                    if (!is_null($sheet['business_name']) || !is_null($sheet['phone']) || !is_null($sheet['email']) || !is_null($sheet['address']) || !is_null($sheet['tax_number'])) {
                        $failedCompany = $this->parseFailedCompany($sheet, $company_user_id, $owner);
                        Session::push('failedCompanies', $failedCompany);
                        $errors = isset($sessionError) ? Session::get('massiveCreationErrors') + 1 :  0 + 1;
                        Session::put('massiveCreationErrors', $errors);
                    }
                }
            });
        });
        
        $resultcompanies = $this->createCompanies();
        
        $this->createFailedCompanies();
        
        if ((int)$toWhiteLabel == 1 && count($resultcompanies) > 0) {
            $api = $this->transferEntityToWhiteLabel(Session::get('companies'), 'shipper');   
        }
        
        $errors = isset($sessionError) ? Session::get('massiveCreationErrors') : 0;
        Session::forget('massiveCreationErrors','companies','failedCompanies');
        return response('successful creation with '.$errors.' failed companies.', 200);
    }

    public function parseCompany($sheet, $company_user_id, $toWhiteLabel, $owner){
        $company= [
            'business_name' => $sheet['business_name'],
            'phone'=> $sheet['phone'],
            'email'=> $sheet['email'],
            'address'=> $sheet['address'],
            'tax_number'=> $sheet['tax_number'],
            'whitelabel'=> $toWhiteLabel,
            'company_user_id'=> $company_user_id,
            'owner' => $owner,
            'options' => null
        ];
        return $company;
    }
    public function parseFailedCompany($sheet, $company_user_id, $owner){
        $failedCompany = [
            'business_name' => $sheet['business_name'] ?? 'ERROR',
            'phone'=> $sheet['phone'] ?? 'ERROR',
            'email'=> $sheet['email'] ?? 'ERROR',
            'address'=> $sheet['address'] ?? 'ERROR',
            'tax_number'=> $sheet['tax_number'] ?? 'ERROR',
            'company_user_id'=> $company_user_id ?? 'ERROR',
            'owner' => $owner
        ];
        return $failedCompany;
    }
    public function createCompanies()
    { 
        $result = [];
        $companies = Session::get('companies');
        if (isset($companies)) {
            foreach ($companies as $key => $value) {
                $company = Company::firstOrCreate(
                ['business_name' => $value['business_name']],
                [
                    'phone'=> $value['phone'],
                    'email'=> $value['email'],
                    'address'=> $value['address'],
                    'tax_number'=> $value['tax_number'],
                    'whitelabel'=> $value['whitelabel'],
                    'company_user_id'=> $value['company_user_id'],
                    'unique_code' => Str::random(8),
                    'options' => null
                ]);
                array_push($result, $company);
            }
        }
        
        return $result;
    }

    public function createFailedCompanies()
    {
        $failedcompanies = Session::get('failedCompanies');
        FailCompany::insert($failedcompanies);
    }

    public function exportCompanies(Request $request, $format)
    {

        $filename       = "companies";
        $titleSheet1    = "companies";
        $Sheet1header   = ['business_name','phone','address','email','tax_number', 'whitelabel'];
        $sheet1Content  = Company::where('company_user_id',  \Auth::user()->company_user_id )->get()->toArray();
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

                    $formatExport['business_name'] = $value['business_name'] != null ? $value['business_name'] : 'N/A';
                    $formatExport['phone'] = $value['phone'] != null ? $value['phone'] : 'N/A';
                    $formatExport['address'] = $value['address'] != null ? $value['address'] : 'N/A';
                    $formatExport['email'] = $value['email'] != null ? $value['email'] : 'N/A';
                    $formatExport['tax_number'] = $value['tax_number'] != null ? $value['tax_number'] : 'N/A';
                    $formatExport['whitelabel'] = $value['whitelabel'] ? 'On Whitelabel' : 'Not on whitelabel';
                    
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
