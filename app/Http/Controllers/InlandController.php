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
    
    $equipments = GroupContainer::get()->map(function ($equipment) {
              return $equipment->only(['id', 'name']);
          });
    $directions = Direction::get()->map(function ($direction) {
            return $direction->only(['id', 'name']);
        });

    $data = [
      'equipments' => $equipments,
      'directions' => $directions

    ];


    return response()->json(['data' => $data ]);
  }


}
