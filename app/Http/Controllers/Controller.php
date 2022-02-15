<?php

namespace App\Http\Controllers;

use App\Conexions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $customEnv;

    public function __construct(){
        $conexions  = new Conexions();
        $this->customEnv        = $conexions->getEnv();
    }
}
