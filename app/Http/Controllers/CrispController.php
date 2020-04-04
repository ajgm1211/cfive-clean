<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EventCrisp;

class CrispController extends Controller
{
  public function index()
  {
    $CrispClient = new EventCrisp();
    $people = $CrispClient->checkIfExist('jonathan.atap@gmail.com');
   
  }
}