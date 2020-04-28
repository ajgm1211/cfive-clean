<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inland;
use App\Carrier;
use App\GroupContainer;
use App\Direction;
use App\Http\Resources\InlandResource;

class InlandController extends Controller
{
  public function index(Request $request)
  {
    return view('inlands.index');
  }
  public function list(Request $request)
  {        
    $results = Inland::filterByCurrentCompany()->filter($request);
    return InlandResource::collection($results);
  }

  public function data(Request $request)
  {        
    
    $carriers = Carrier::get()->map(function ($carrier) {
      return $carrier->only(['id', 'name']);
    });

    $equipments = GroupContainer::get()->map(function ($carrier) {
      return $carrier->only(['id', 'name']);
    });
    
    $directions = Direction::get()->map(function ($carrier) {
      return $carrier->only(['id', 'name']);
    });

    $data = [
      'carriers' => $carriers,
      'equipments' => $equipments,
      'directions' => $directions

    ];

    return response()->json(['data' => $data ]);
  }


}
