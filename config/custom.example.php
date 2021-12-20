<?php

switch (env('APP_ENV')) {
    case 'local':
        $view  = 'local';
        $appUrl  = 'http://cargofive.local';
        $apiUrl = 'https://carriersdev.cargofive.com/api/pricing';
    break;

    case 'demo':
        $view  = 'demo';
        $appUrl  = '';
        $apiUrl = 'https://carriersdev.cargofive.com/api/pricing';
    break;

    case 'production':
        $view  = 'prd';
        $appUrl  = 'https://app.cargofive.com';
        $apiUrl = 'https://carriers.cargofive.com/api/pricing';
    break;
    
    default:
        $view = '';
        $appUrl  = '';
        $apiUrl = '';
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

    'api_url' => $appUrl,

];