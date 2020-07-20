<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyPrice;
use App\Contact;
use App\Jobs\ProcessLogo;
use App\QuoteV2;
use App\Price;
use App\User;
use App\GroupUserCompany;
use DebugBar\DebugBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\ApiIntegrationSetting;
use App\Http\Requests\StoreCompany;
use App\Http\Traits\EntityTrait;
use App\Repositories\CompanyRepositoryInterface;
use App\ViewQuoteV2;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    use EntityTrait;

    /** @var CompanyRepositoryInterface */
    private $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;
        $api = ApiIntegrationSetting::where('company_user_id', \Auth::user()->company_user_id)
            ->whereHas('api_integration', function ($query) {
                $query->where('module', 'Companies');
            })->first();

        $user_id = \Auth::user()->id;
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', \Auth::user()->id)->where('type', '!=', 'company')->pluck('name', 'id');

        if (\Auth::user()->hasRole('subuser')) {

            $query = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->orwhere('owner', \Auth::user()->id)->with('groupUserCompanies.user')->User()->CompanyUser();
        } else {
            $query = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user')->User()->CompanyUser();
        }

        if ($request->paginate) {
            $companies = $query->paginate($request->paginate);
        } else {
            $companies = $query->take($request->size)->get();
        }

        if ($request->ajax()) {
            return $companies;
        }

        return view('companies/index', ['companies' => $companies, 'users' => $users, 'api' => $api]);
    }

    /**
     * LoadDatatableIndex
     *
     * @return void
     */
    public function LoadDatatableIndex()
    {

        $company_user_id = \Auth::user()->company_user_id;
        $user_id = \Auth::user()->id;

        if (\Auth::user()->hasRole('subuser')) {

            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->orwhere('owner', \Auth::user()->id)->with('groupUserCompanies.user')->User()->CompanyUser();
        } else {
            $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user')->User()->CompanyUser();
        }

        $companies = $companies->get();

        $colletions = collect([]);
        foreach ($companies as $company) {

            $data = [
                'id' => $company->id,
                'idSet' => setearRouteKey($company->id),
                'business_name' => $company->business_name,
                'phone' => $company->phone,
                'email' => $company->email,
                'tax_number' => $company->tax_number,
                'address' => $company->address,
            ];
            $colletions->push($data);
        }
        return DataTables::of($colletions)->addColumn('action', function ($colletion) {
            return
                '<a href="companies/' . $colletion['idSet'] . '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill">
                    <i class="la la-eye"></i>
                </a>
                <button onclick="AbrirModal(\'edit\',' . $colletion['id'] . ')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit">
                    <i class="la la-edit"></i>
                </button>
                <button id="delete-company" data-company-id="' . $colletion['id'] . '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete">
                    <i class="la la-eraser"></i>
                </button>';
        })->make(true);
    }

    /**
     * add
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', \Auth::user()->id)->where('type', '!=', 'company')->pluck('name', 'id');
        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
        return view('companies.add', compact('prices', 'users'));
    }

    /**
     * addOwner
     *
     * @return \Illuminate\Http\Response
     */
    public function addOwner()
    {
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', \Auth::user()->id)->where('type', '!=', 'company')->pluck('name', 'id');

        return view('companies.addOwner', compact('users'));
    }

    /**
     * addWithModal
     *
     * @return \Illuminate\Http\Response
     */
    public function addWithModal()
    {
        $company_user_id = \Auth::user()->company_user_id;
        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
        $user_id = \Auth::user()->id;
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', $user_id)->where('type', '!=', 'company')->pluck('name', 'id');
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->orwhere('owner', \Auth::user()->id)->with('groupUserCompanies.user', 'user')->get();
        } else {
            $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user', 'user')->get();
        }


        return view('companies.addwithmodal', compact('prices', 'users'));
    }

    /**
     * show
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $id = obtenerRouteKey($id);
        if ($request->ajax()) {
            $company = Company::with(array('owner' => function ($query) {
                $query->select('id', 'name', 'lastname', 'email', 'phone', 'position', 'state', 'company_user_id');
            }, 'company_user' => function ($query) {
                $query->select('id', 'name', 'address', 'phone', 'currency_id');
                $query->with(['currency' => function ($q) {
                    $q->select('id', 'name', 'alphacode', 'api_code_eur', 'api_code', 'rates', 'rates_eur');
                }]);
            }))->where('id', $id)->firstOrFail();

            $collection = Collection::make($company);
            return $collection;
        } else {
            $company = $this->repository->find($id);
        }

        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->get();
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $quotes = ViewQuoteV2::where('user_id', \Auth::user()->id)->orderBy('created_at', 'desc')->get();
        } else {
            $quotes = ViewQuoteV2::where('company_user_id', $company_user_id)->orderBy('created_at', 'desc')->get();
        }
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', \Auth::user()->id)->where('type', '!=', 'company')->pluck('name', 'id');
        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');

        return view('companies.show', compact('company', 'companies', 'contacts', 'quotes', 'users', 'prices'));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompany $request)
    {
        //Form Validation
        $request->validated();

        //Check if company exists by options field
        if ($request->ajax() && $request->options) {
            $data = json_decode($request->options, true);
            if (array_key_exists('external_company_id', $data)) {
                $company = Company::where('options->external_company_id', $data['external_company_id'])->first();
                if ($company) {
                    $company->fill($request->all())->save();
                    return $company;
                }
            }
        }

        $input = Input::all();
        $file = Input::file('logo');
        $filepath_tmp = null;
        $options = null;

        if ($request->key_name && $request->key_value) {
            $options_array = array();

            $options_key = $this->processArray($request->key_name);
            $options_value = $this->processArray($request->key_value);

            $options_array = json_encode(array_combine($options_key, $options_value));
        }

        if ($request->ajax()) {
            $options = $request->options;
        } else {
            if (isset($options_array)) {
                $options = $options_array;
            }
        }

        if ($file != "") {
            $filepath_tmp = 'Logos/Clients/' . $file->getClientOriginalName();
        }

        $request->request->add(['company_user_id' => Auth::user()->company_user_id, 'owner' => Auth::user()->id, 'options' => $options, 'logo' => $filepath_tmp]);

        //Save Company
        $company = Company::create($request->all());

        if ($file != "") {
            $this->saveLogo($company, $file);
        }
        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            $this->saveExtraData($input['price_id'], $company, 'price');
        }
        if ((isset($input['users'])) && (count($input['users']) > 0)) {
            $this->saveExtraData($input['users'], $company, 'users');
        }

        if ($request->ajax()) {
            return $company;
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');
        return redirect()->route('companies.index');
    }

    /**
     * storeOwner
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function storeOwner(Request $request)
    {

        $input = Input::all();

        $company = $this->repository->find($input['company_id']);
        
        if ((isset($input['users'])) && (count($input['users']) > 0)) {
            foreach ($input['users'] as $key => $item) {
                $userCompany_group = new GroupUserCompany();
                $userCompany_group->user_id = $input['users'][$key];
                $userCompany_group->company()->associate($company);
                $userCompany_group->save();
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Owner added successfully!');
        return redirect()->back();
    }

    /**
     * deleteOwner
     *
     * @param  mixed $request
     * @param  mixed $user_id
     * @return \Illuminate\Http\Response
     */
    public function deleteOwner(Request $request, $user_id)
    {

        $user = GroupUserCompany::where('user_id', $user_id)->delete();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Owner deleted successfully!');
        return redirect()->back();
    }

    /**
     * edit
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = $this->repository->find($id);

        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('type', '!=', 'company')->where('id', '!=', $company->owner)->pluck('name', 'id');

        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
        return view('companies.edit', compact('company', 'prices', 'users'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCompany $request, $id)
    {
        //Form Validation
        $request->validated();

        //Check if company exists by options field
        if ($request->ajax() && $request->options) {
            $data = json_decode($request->options, true);
            if (array_key_exists('external_company_id', $data)) {
                $company = Company::where('options->external_company_id', $data['external_company_id'])->first();
                if ($company) {
                    $company->fill($request->all())->save();
                    return $company;
                }
            }
        }

        $input = Input::all();
        $file = Input::file('logo');
        $options = null;

        if ($request->key_name && $request->key_value) {
            $options_array = array();

            $options_key = $this->processArray($request->key_name);
            $options_value = $this->processArray($request->key_value);

            $options_array = json_encode(array_combine($options_key, $options_value));
        }

        if ($request->ajax()) {
            $options = $request->options;
        } else {
            if (isset($options_array)) {
                $options = $options_array;
            }
        }

        $company = Company::findOrFail($id);
        $filepath = $company->logo;

        if ($file != "") {
            $filepath = 'Logos/Clients/' . $id . '/' . $file->getClientOriginalName();
        }

        $request->request->add(['options' => $options, 'logo' => $filepath]);

        $company->fill($request->all())->save();

        if ($file != "") {
            $this->saveLogo($company, $file);
        }

        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            CompanyPrice::where('company_id', $company->id)->delete();
            $this->saveExtraData($input['price_id'], $company, 'price');
        }

        if ((isset($input['users'])) && (count($input['users']) > 0)) {
            GroupUserCompany::where('company_id', $company->id)->delete();
            $this->saveExtraData($input['users'], $company, 'users');
        }

        if ($request->ajax()) {
            return response()->json($company);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->back();
    }

    /**
     * delete
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $company = $this->repository->find($id);

        if (count($company->contact) > 0) {
            return response()->json(['message' => count($company->contact)]);
        }

        return response()->json(['message' => 'Ok']);
    }

    /**
     * destroy
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function destroy(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();

            if ($request->ajax()) {
                return response()->json('Company deleted successfully!', 200);
            }

            return response()->json(['message' => 'Ok']);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Record not found',
                ], 404);
            }

            return response()->json(['message' => $e]);
        }
    }

    /**
     * getCompanyPrice
     *
     * @param  mixed $id
     * @return void
     */
    public function getCompanyPrice($id)
    {
        $prices = Price::whereHas('company_price', function ($query) use ($id) {
            $query->where('company_id', $id);
        })->pluck('name', 'id');

        return $prices;
    }

    /**
     * getCompanyContact
     *
     * @param  mixed $id
     * @return void
     */
    public function getCompanyContact($id)
    {
        $contacts = Contact::where('company_id', $id)->pluck('first_name', 'id');

        return $contacts;
    }

    /**
     * updatePaymentConditions
     *
     * @param  mixed $request
     * @return void
     */
    public function updatePaymentConditions(Request $request)
    {

        $company = Company::find($request->company_id);
        $company->payment_conditions = $request->payment_conditions;
        $company->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');
        return redirect()->back();
    }

    /**
     * getCompanies
     *
     * @return void
     */
    public function getCompanies()
    {
        $company_user_id = \Auth::user()->company_user_id;

        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }

        return $companies;
    }

    /**
     * updateName
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateName(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->business_name = $request->business_name;
        $company->update();

        return response()->json(['business_name' => $request->business_name]);
    }

    /**
     * updatePhone
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updatePhone(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->phone = $request->phone;
        $company->update();

        return response()->json(['phone' => $request->phone]);
    }

    /**
     * updateAddress
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateAddress(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->address = $request->address;
        $company->update();

        return response()->json(['address' => $request->address]);
    }

    /**
     * updateEmail
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateEmail(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->email = $request->email;
        $company->update();

        return response()->json(['address' => $request->email]);
    }

    /**
     * updateTaxNumber
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateTaxNumber(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->tax_number = $request->tax_number;
        $company->update();

        return response()->json(['tax_number' => $request->tax_number]);
    }

    /**
     * updatePdfLanguage
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updatePdfLanguage(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->pdf_language = $request->pdf_language;
        $company->update();

        return response()->json(['pdf_language' => $request->pdf_language]);
    }

    /**
     * updatePriceLevels
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updatePriceLevels(Request $request, $id)
    {
        $input = Input::all();

        if ((isset($input['price_id'])) && ($input['price_id'][0] != null)) {
            $company_price = CompanyPrice::where('company_id', $id)->delete();
            foreach ($input['price_id'] as $key => $item) {
                $company_price = new CompanyPrice();
                $company_price->company_id = $id;
                $company_price->price_id = $input['price_id'][$key];
                $company_price->save();
            }
        }

        $prices = Price::whereHas('company_price', function ($query) use ($id) {
            $query->where('company_id', $id);
        })->pluck('name', 'id');

        return $prices;
    }

    /**
     * apiCompanies
     *
     * @return void
     */
    public function apiCompanies()
    {
        $companies = Company::where('api_id', '!=', '')->get();

        return view('companies.api.index', compact('companies'));
    }

    /**
     * saveLogo
     *
     * @param  mixed $company
     * @param  mixed $file
     * @return void
     */
    public function saveLogo($company, $file)
    {
        $update_company_url = Company::findOrFail($company->id);
        $update_company_url->logo = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
        $update_company_url->update();
        $filepath = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
        $name = $file->getClientOriginalName();
        \Storage::disk('logos')->put($name, file_get_contents($file), 'public');
        $s3 = \Storage::disk('s3_upload');
        $s3->put($filepath, file_get_contents($file), 'public');
        //ProcessLogo::dispatch(auth()->user()->id, $filepath, $name, 2);
    }

    /**
     * saveExtraData
     *
     * @param  mixed $data
     * @param  mixed $company
     * @param  mixed $type
     * @return void
     */
    public function saveExtraData($data, $company, $type)
    {
        switch ($type) {
            case 'price':
                foreach ($data as $key => $item) {
                    $company_price = new CompanyPrice();
                    $company_price->company_id = $company->id;
                    $company_price->price_id = $data[$key];
                    $company_price->save();
                }
                break;

            case 'users':
                foreach ($data as $key => $item) {
                    $userCompany_group = new GroupUserCompany();
                    $userCompany_group->user_id = $data[$key];
                    $userCompany_group->company()->associate($company);
                    $userCompany_group->save();
                }
                break;
        }
    }

    /**
     * searchCompanies
     *
     * @param  mixed $request
     * @return void
     */
    public function searchCompanies(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->where('business_name', 'like', '%' . $term . '%')->get();

        $formatted_companies = [];
        foreach ($companies as $company) {
            $formatted_companies[] = ['id' => $company->id, 'text' => $company->business_name];
        }
        return \Response::json($formatted_companies);
    }
}
