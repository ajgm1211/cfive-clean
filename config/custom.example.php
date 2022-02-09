<?php

switch (env('APP_ENV')) {
    case 'local':
        $view    = 'local';
        $appUrl  = 'http://cargofive.local';
        $apiUrl  = 'https://carriersdev.cargofive.com/api/pricing';
        $baseUriDuplicates = 'http://duplicate-gc/DuplicateGCFCL/';
        $appView = 'local';
    break;

    case 'develop':
        $view    = 'develop';
        $appUrl  = 'https://dev.cargofive.com';
        $apiUrl  = 'https://carriersdev.cargofive.com/api/pricing';
        $baseUriDuplicates = 'http://duplicateds-globalchargers-dev.eu-central-1.elasticbeanstalk.com/DuplicateGCFCL/';
        $appView = 'local';
    break;

    case 'production':
        $view    = 'prd';
        $appUrl  = 'https://app.cargofive.com';
        $apiUrl  = 'https://carriers.cargofive.com/api/pricing';
        $baseUriDuplicates = 'http://prod.duplicatedscg.cargofive.com/DuplicateGCFCL/';
        $appView = 'local';
    break;
    
    default:
        $view = '';
        $appUrl  = '';
        $apiUrl = '';
        $baseUriDuplicates ='';
        $appView = '';
        break;
}

return [
    
    /*
    |--------------------------------------------------------------------------
    | ENV URL
    |--------------------------------------------------------------------------
    |
    | This setups the app enviroment gateway url so we can get it.
    |
    */
    
    'app_env' => env('APP_VIEW', $view),

    /*
    |--------------------------------------------------------------------------
    | APP URL
    |--------------------------------------------------------------------------
    |
    | This setups the app url gateway so we can get it.
    |
    */

    'app_url' => env('APP_URL', $appUrl),

    
    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    |
    | This setups the api url gateway so we can get it.
    |
    */

    'api_url' => $apiUrl,

    /*
    |--------------------------------------------------------------------------
    | BASE URL
    |--------------------------------------------------------------------------
    |
    | This setups the base url for duplicates gateway so we can get it.
    |
    */

    'base_uri_duplicates' => $baseUriDuplicates,

    /*
    |--------------------------------------------------------------------------
    | BASE URL
    |--------------------------------------------------------------------------
    |
    | This setups the base url for duplicates gateway so we can get it.
    |
    */

    'app_view' => env('APP_VIEW', $appView),

    

];