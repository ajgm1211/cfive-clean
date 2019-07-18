<?php

namespace App\Http\Controllers;

use App\CompanyUser;
use Illuminate\Http\Request;
use App\CompanyAutoImportation;
use Yajra\Datatables\Datatables;
use App\SurchargerForCompanyUser as FiltroSucharger;

class CompanyImportationController extends Controller
{

    public function index()
    {
        return view('importationCompanyUser.index');
    }

    public function create()
    {   
        $companies = CompanyAutoImportation::with('companyUser')->get();
        return DataTables::of($companies)
            ->editColumn('name', function ($company){ 
                return $company->companyUser->name;
            })
            ->addColumn('status', function ($company){ 
                $value = null;
                if($company->status){
                    $value = '<span style="color:#0b790b"><li>Active</li></span>';
                } else {
                    $value = '<span style="color:red"><li>Innactive</li></span>';
                }
                return $value;
            })
            ->addColumn('action', function ( $company) {
                return '
                    <a  id="edit_l" onclick="AbrirModal('."'company-import-edit'".','.$company->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <a  id="delete-company" data-delete="'.$company->id.'"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l'.$company->id.'" class="la la-times-circle"></i>
										</a>
                    <a href="'.route("CompanyImportation.filtro.index",$company->id).'" id="Schargers"  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Show Schargers ">
                    <i class="la la-list-alt"></i>
                    </a>
                    ';
            })
            ->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function add(){
        $companies = CompanyUser::pluck('name','id');
        //dd($companies);
        return view('importationCompanyUser.add',compact('companies'));
    }

    public function store(Request $request)
    {
        $cpmany_u_id = $request->input('company_user_id');
        $status      = $request->input('status');
        $companies = CompanyAutoImportation::where('company_user_id',$cpmany_u_id)->get();
        if(count($companies) == 0){
            $company = new CompanyAutoImportation();
            $company->company_user_id =  $cpmany_u_id;
            $company->status =  $status;
            $company->save();
            $request->session()->flash('message.content', 'Company registered' );
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
        } else {
            $request->session()->flash('message.content', 'company already registered' );
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Well done!');
        }
        //dd($request->all());
        return back();
    }

    public function edit($id)
    {
        $company = CompanyAutoImportation::find($id);
        $companies = CompanyUser::pluck('name','id');
        return view('importationCompanyUser.edit',compact('company','companies'));
    }

    public function update(Request $request, $id)
    {
        $cpmany_u_id = $request->input('company_user_id');
        $status      = $request->input('status');
        $company = CompanyAutoImportation::find($id);
        $company->company_user_id =  $cpmany_u_id;
        $company->status =  $status;
        $company->save();
        $request->session()->flash('message.content', 'Company Updated' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return back();
    }

    public function destroy($id)
    {
        try{
            $company = CompanyAutoImportation::find($id);
            $company->delete();
            return response()->json(['success' => '1']);
        } catch(\Exception $e){
            return response()->json(['success' => '2']);
        }
    }

    // Surchergers -------------------------------------------------------------------------------------------------------------

    public function indexFiltro($id)
    {
        $company_ob = CompanyAutoImportation::find($id);
        $company_ob = $company_ob->load('companyUser');
        return view('importationCompanyUser.show',compact('id','company_ob'));
    }

    public function show($id)
    {
        $filtrosSurch = FiltroSucharger::where('company_auto_id',$id)->get();
        return DataTables::of($filtrosSurch)
            ->editColumn('name', function ($filtroSur){ 
                return $filtroSur->name;
            })
            ->addColumn('action', function ( $filtroSur) {
                return '
                    <a  id="edit_l" onclick="AbrirModal('."'surchar-edit'".','.$filtroSur->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <a  id="delete-surcharger" data-delete="'.$filtroSur->id.'"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="" class="la la-times-circle"></i>
										</a>
                    ';
            })
            ->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function addFiltro($id){
        $company_au = CompanyAutoImportation::find($id);
        $companies = CompanyUser::pluck('name','id');
        //dd($companies);
        return view('importationCompanyUser.addSurcharge',compact('companies','company_au'));
    }

    public function storeFiltro(Request $request){
        //dd($request->all());
        $company_auto_id = $request->input('company_auto_id');
        $name            = $request->input('surcharger');
        $filtro_obj = FiltroSucharger::where('name',$name)->where('company_auto_id',$company_auto_id)->get();
        if(count($filtro_obj) == 0){
            $filtro = new FiltroSucharger();
            $filtro->name = $name;
            $filtro->company_auto_id = $company_auto_id;
            $filtro->save();
            $request->session()->flash('message.content', 'Surcharger filter registered' );
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
        } else {
            $request->session()->flash('message.content', 'Surcharger filter already registered' );
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Well done!');
        }
        return back();
    }

    public function editFiltro($id)
    {
        $filtro = FiltroSucharger::where('id',$id)->with('companyAutoUser')->first();

        $companies = CompanyUser::pluck('name','id');
        return view('importationCompanyUser.editSurcharger',compact('filtro','companies'));
    }

    public function DestroyFiltro($id){
        try{
            $filtro = FiltroSucharger::find($id);
            $filtro->delete();            
            return response()->json(['success' => '1']);
        } catch(\Exception $e){
            return response()->json(['success' => '2']);
        }
    }
}
