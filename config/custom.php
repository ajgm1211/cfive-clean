<?php

switch (env('APP_ENV')) {
    case 'local':
        $view  = 'local';
        
    break;

    case 'demo':
        $view  = 'demo';
       
    break;

    case 'production':
        $view  = 'prd';
    break;
    
    default:
        $view = '';
        break;
}

return [
    
    /*
    |--------------------------------------------------------------------------
    | Payvault URL
    |--------------------------------------------------------------------------
    |
    | This setups the payment gateway url so payments can be made.
    |
    */
    
    'app_env' => env('APP_VIEW', $view),

];