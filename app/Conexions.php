<?php

namespace App;

use Illuminate\Support\Facades\App;

class Conexions
{
    public function getEnv(){
      return [
        'appEnv'=> config('custom.app_env'),
        'appUrl'=> config('custom.app_url'),
        'apiUrl'=> config('custom.api_url'),
        'baseUriDuplicates' => config('custom.base_uri_duplicates'),
        'appView' => config('custom.app_view'),
      ];
    }
}
