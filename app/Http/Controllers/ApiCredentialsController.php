<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyUser;
use App\ApiProvider;

class ApiCredentialsController extends Controller
{
    public function listCompanyUsers(){
        $clients = CompanyUser::paginate();
        return $clients;
    }

    public function listApiProviders(CompanyUser $companyUser) {
        //dd($companyUser);
        
        $options = $companyUser->options;
        $providerIds = $options["api_providers"];        
        
        $providers = ApiProvider::whereIn('id', $providerIds)->get([
            'id',
            'name',
            'code',
            'description',
            'image',
            'status',
            'json_settings',
            // "url": "https://apis.cma-cgm.net/",
            // "require_login": 1,
            'credentials'
        ]);
        
        $companyUser->providers = $providers->map(function ($provider) {
            $provider->image = 'abc/' . $provider->image; // Storage::get($image);
            return $provider;
        });
    
        return $companyUser;
    }
}
