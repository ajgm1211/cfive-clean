<?php

namespace App\Http\Controllers;

use App\Conexions;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    protected $customEnv;

    public function __construct(){
        $conexions  = new Conexions();
        $this->customEnv        = $conexions->getEnv();
    }
}
