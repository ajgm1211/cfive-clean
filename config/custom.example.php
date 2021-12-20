<?php

switch (env('APP_ENV')) {
    case 'local':
        $view  = 'local';
        $appUrl  = 'http://cargofive.local';
        
    break;

    case 'demo':
        $view  = 'demo';
        $appUrl  = '';
    break;

    case 'production':
        $view  = 'prd';
        $appUrl  = 'http://cargofive.com';
    break;
    
    default:
        $view = '';
        $appUrl  = '';
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

];