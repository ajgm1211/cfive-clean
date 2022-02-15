<?php

namespace App\Http\Controllers;

use EventCrisp;
use Illuminate\Http\Request;

class CrispController extends Controller
{
    public function index()
    {
        $CrispClient = new EventCrisp();
        $people = $CrispClient->checkIfExist('jonathan.atap@gmail.com');
    }
}
