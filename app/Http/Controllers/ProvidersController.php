<?php



namespace App\Http\Controllers;
use App\Providers;
use Illuminate\Http\Request;
use App\Http\Resources\ProvidersResource;



class ProvidersController extends Controller
{
    /**
     * Render index view 
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        return view('providers.index');
    }
    
    public function list(Request $request){

        $results = Providers::filterByCurrentCompany()->filter($request);

        return ProvidersResource::collection($results);
    }
    public function data(){

    }
    public function store(){

    }
    public function update(){

    }
    public function retrive(){

    }
    public function destroy(){

    }
    public function destroyAll(){

    }
    
}
