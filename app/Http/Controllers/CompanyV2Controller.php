<?php

namespace App\Http\Controllers;

use App\Company;
use App\Contact;
use App\FailCompany;
use App\CompanyPrice;
use GuzzleHttp\Client;
use App\GroupUserCompany;
use App\SettingsWhitelabel;
use Illuminate\Http\Request;
use App\Http\Traits\SearchTrait;
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
    use SearchTrait;

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
            '*.business_name' => 'required',
            '*.logo' => 'max:1000',
            '*.options' => 'json',
        ]);

        if ($file != null) {
            $filepath_tmp = 'Logos/Clients/' . $file->getClientOriginalName();
        }

        $newCompany = $request->get('company');
        $newCompany += [ "company_user_id" => $company_user_id ];
        $newCompany += [ "owner" => $user_id ];
        $newCompany += [ "options" => $options ];
        $newCompany += [ "logo" => $filepath_tmp ];

        $company = Company::create($newCompany);

        if ($file != null) {
            $this->saveLogo($company, $file);
        }

        $this->saveExtraData($request, $company);

        if ($company->whitelabel) {
            $this->callApiTransferToWhiteLabel($company);
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
                if ($companyForUpdate['company']['whitelabel'] == 1) {
                    $this->callApiTransferToWhiteLabel($company);
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

    public function downloadTemplateFile(){
        return Storage::disk('DownLoadFile')->download('company_template.xlsx');
    }
    
    public function failed(){
        return view('companies.v2.failed');
    }
    
    public function failedList(Request $request){
        $failedCompanies = FailCompany::filterByCurrentUser()->orderBy('id', 'asc')->filter($request);

        return FailCompanyResource::collection($failedCompanies);
    }

    public function contactByCompanyList(Request $request, $company){

        $contactsByCompany = Contact::filterByCurrentEditingCompany($company)->orderBy('id', 'asc')->filter($request);
        return  ContactResource::collection($contactsByCompany);
    }

    public function transferToWhiteLabel(Request $request){
        $companies = $request->get('companies');

        try {
            foreach ($companies as $key => $value) {
                Company::where('id', $value['id'])->update(array('whitelabel' => 1 ));
            }
            
            $this->callApiTransferToWhiteLabel();

            return "Transfer to whiteLabel";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
            
    }

    public function createCompaniesMassive(Request $request){
        //guardar el archivo excel
        //leer el archivo excel
        //insertar en la base de datos las compañias que todos los datos esten bien en la tabla companies
        //insertar en la base de datos las compañias que todos los datos que no esten bien en la tabla failed_companies
        //retornar el estado de la carga 200 para creacion completa 205 para creacion parcial 500 para fallo de creacion o almacenado de la info
    }

    //enviar esta funcion a un trait de wl
    public function callApiTransferToWhiteLabel($companyForTransfer){
        $company = $companyForTransfer->toArray();
        $company_user_id = \Auth::user()->company_user_id;

        $url = SettingsWhitelabel::where('company_user_id', $company_user_id)->select('url','token')->first()->toArray();  
        $endPoint = $url['url'].'shipper';
        $service = new Client();
        
        $result =   $service->post($endPoint,
                        [
                            'http_errors' => false,
                            'headers'=>[
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/x-www-form-urlencoded',
                                'Authorization' => Auth::user()->api_token,
                            ],
                            'form_params'=>[
                                $company
                            ]
                        ]
                    );

        return $result;
    }

    public function exportCompanies(Request $request, $format){

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
