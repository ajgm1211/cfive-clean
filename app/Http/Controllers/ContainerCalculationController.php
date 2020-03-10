<?php

namespace App\Http\Controllers;

use HelperAll;
use App\Container;
use App\ContainerCalculation;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class ContainerCalculationController extends Controller
{

	public function index()
	{
		//containersCalculations
		return view('containersCalculation.index');
	}

	public function create()
	{
		$containerscal = ContainerCalculation::all();
		$containerscal->load('container','calculationtype');
		//dd($containerscal);
		return DataTables::of($containerscal)
			->addColumn('container', function ($containerscal) {
				return $containerscal->container->name;
			})
			->addColumn('calculationtype', function ($containerscal) {
				return $containerscal->calculationtype->name;
			})
			->addColumn('action', function ($containerscal) {
				return '';
			})
			->editColumn('id', '{{$id}}')->toJson();
	}
	
	public function loadBodymodalAdd(){
		$conatiner = HelperAll::addOptionSelect(Container::all(),'id','name');
		return view('containersCalculation.Body-Modals.add',compact('conatiner'));
	}

	public function store(Request $request)
	{
		//
	}

	public function show(ContainerCalculation $containerCalculation)
	{
		//
	}

	public function edit(ContainerCalculation $containerCalculation)
	{
		//
	}

	public function update(Request $request, ContainerCalculation $containerCalculation)
	{
		//
	}

	public function destroy(ContainerCalculation $containerCalculation)
	{
		//
	}
}
