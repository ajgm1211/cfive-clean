<?php

namespace App;

use Illuminate\Support\Facades\App;

class Conexions
{
    public function getEnv(){
      return [
        'appEnv'=> config('custom.app_env'),
        'appUrl'=> config('custom.app_url'),
      ];
    }
}
