<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyUser;
use App\ApiProvider;
use App\ApiCredential;

class ApiCredentialsController extends Controller
{
    public function listCompanyUsers(){
        $clients = CompanyUser::paginate(10);
        return $clients;
    }
    public function listAvailableApiProviders(Request $request){
        $company_user_id = $request->input("company_user_id");
        $company_user = CompanyUser::find($company_user_id);
        $options = $company_user->options;
        $used_api_providers = $options["api_providers"];        
        
        if(sizeof($used_api_providers)) {
            //dd("hay elementos para filtrar");
            $apiProviders = ApiProvider::whereNotIn('id', $used_api_providers)->get();
        } else {
            $apiProviders = ApiProvider::all();
        }        
        
        return $apiProviders;
    }

    public function listApiProvidersByCompanyUser(CompanyUser $companyUser) {
        
        $companyUserId = $companyUser->id; 

        $options = $companyUser->options;
        $providerIds = $options["api_providers"];
        $providers = ApiProvider::whereIn('id', $providerIds)->get();
        
        $companyUser->providers = $providers->map(function ($provider) use ($companyUserId) {
            
            $provider->image = 'https://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/' . $provider->image; // Storage::get($image);            
            $apiCredential = ApiCredential::where("model_id", $companyUserId)->where("api_provider_id", $provider->id)->first([
                'id', 'status', 'credentials'
            ]);
            if ($apiCredential) {
                $provider->api_credential = $apiCredential; 
            }
            return $provider;
        });
        return $companyUser;
    }

    public function store(Request $request){
         $company_user_id = $request->input("model_id");
         $api_provider_id = $request->input("api_provider_id");

        //Agregar api_provider id al arreglo de options
         $company_user = CompanyUser::find($company_user_id);
         $options = $company_user->options;
         array_push($options["api_providers"], $api_provider_id);
         $company_user->options = $options;
         $company_user->save();

        $credentials = $request->input("credentials");
        if ($credentials) {
            $credentialsStr = json_encode($credentials);
            // Registrar en api_credentials
            ApiCredential::create([
                'model_type' => "App\CompanyUser",
                'model_id' => $request->input("model_id"),
                'status' => true,
                'credentials' => $credentialsStr,
                'api_provider_id' => $request->input("api_provider_id"),
            ]);
        }        
        
    }

    public function update(ApiCredential $apiCredential, Request $request) {
        $credentialsStr = json_encode($request->input('credentials'));
        
        $apiCredential->credentials = $credentialsStr;
        $apiCredential->save();
    }

    public function updateStatus(ApiCredential $apiCredential, Request $request) {
        $apiCredential->status = $request->input('status');
        $apiCredential->save();
    }

    public function deleteApiProviderOfCompanyUser(CompanyUser $companyUser, Request $request){
        
        $api_provider_id = $request->api_provider_id;
        $options = $companyUser->options;
        $api_providers = $options["api_providers"];
        
        $new_api_providers = array_diff($api_providers, array($api_provider_id));
        $options["api_providers"] = $new_api_providers;
        $companyUser->options = $options;
        $companyUser->save();
    }
}
