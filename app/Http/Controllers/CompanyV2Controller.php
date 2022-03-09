<?php

namespace App\Http\Controllers;

use Excel;
use App\Company;
use App\Contact;
use App\FailCompany;
use App\CompanyPrice;
use App\GroupUserCompany;
use Illuminate\Http\Request;
use App\Http\Traits\SearchTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        try {
            DB::beginTransaction();

                if ($file != null) {
                    $filepath_tmp = 'Logos/Clients/' . $file->getClientOriginalName();
                    $request->request->add(['logo' => $filepath_tmp]);
                }

                if ($company) {
                    $company->fill($request->get('company'))->save();
                }
                
                if ($file != null) {
                    $this->saveLogo($company, $file);
                }

            DB::commit();
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

    public function downloadTemplatefile(){
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
}
