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
use App\ViewQuoteV2;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection as Collection;


class CompanyController extends Controller
{
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
            if ($request->paginate) {
                $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })->orwhere('owner', \Auth::user()->id)->with('groupUserCompanies.user', 'user', 'company_user')->get();
            } else {
                $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })->orwhere('owner', \Auth::user()->id)->with('groupUserCompanies.user', 'user', 'company_user')->take($request->size)->get();
            }
        } else {
            if ($request->paginate) {
                $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user', 'user', 'company_user')->paginate($request->paginate);
            } else {
                $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user', 'user', 'company_user')->take($request->size)->get();
            }
        }

        if ($request->ajax()) {
            return $companies;
        }

        return view('companies/index', ['companies' => $companies, 'users' => $users, 'api' => $api]);
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
            $company = Company::find($id);
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
        $request->validated();

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
        $filepath_tmp = '';

        $company = new Company();
        $company->business_name = $request->business_name;
        $company->phone = $request->phone;
        $company->address = $request->address;
        $company->email = $request->email;
        $company->tax_number = $request->tax_number;
        $company->company_user_id = \Auth::user()->company_user_id;
        $company->owner = \Auth::user()->id;
        $company->pdf_language = $request->pdf_language;
        $company->payment_conditions = $request->payment_conditions;
        $company->options = $request->options;
        if ($file != "") {
            $filepath_tmp = 'Logos/Clients/' . $file->getClientOriginalName();
            $company->logo = $filepath_tmp;
        }
        $company->save();

        if ($file != "") {
            $update_company_url = Company::find($company->id);
            $update_company_url->logo = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
            $update_company_url->update();
            $filepath = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
            $name = $file->getClientOriginalName();
            \Storage::disk('logos')->put($name, file_get_contents($file), 'public');
            $s3 = \Storage::disk('s3_upload');
            $s3->put($filepath, file_get_contents($file), 'public');
            //ProcessLogo::dispatch(auth()->user()->id, $filepath, $name, 2);
        }
        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            foreach ($input['price_id'] as $key => $item) {
                $company_price = new CompanyPrice();
                $company_price->company_id = $company->id;
                $company_price->price_id = $input['price_id'][$key];
                $company_price->save();
            }
        }
        if ((isset($input['users'])) && (count($input['users']) > 0)) {
            foreach ($input['users'] as $key => $item) {
                $userCompany_group = new GroupUserCompany();
                $userCompany_group->user_id = $input['users'][$key];
                $userCompany_group->company()->associate($company);
                $userCompany_group->save();
            }
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

        $company = Company::find($input['company_id']);

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
        $company = Company::find($id);

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
    public function update(Request $request, $id)
    {
        $rules = array(
            'logo' => 'max:1000',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {

            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Error!');
            $request->session()->flash('message.content', 'Image size can not be bigger than 1 mb');
            return redirect()->back();
        } else {

            $input = Input::all();
            $file = Input::file('logo');
            $filepath = '';
            if ($file != "") {
                $filepath = 'Logos/Clients/' . $id . '/' . $file->getClientOriginalName();
            }
            $company = Company::find($id);

            $company->business_name = $request->business_name;
            $company->phone = $request->phone;
            $company->address = $request->address;
            $company->email = $request->email;
            $company->tax_number = $request->tax_number;
            $company->pdf_language = $request->pdf_language;
            $company->payment_conditions = $request->payment_conditions;
            if ($file != "") {
                $company->logo = $filepath;
            }
            $company->update();

            if ($file != "") {
                $name = $file->getClientOriginalName();
                \Storage::disk('logos')->put($name, file_get_contents($file), 'public');
                $s3 = \Storage::disk('s3_upload');
                $s3->put($filepath, file_get_contents($file), 'public');
                //ProcessLogo::dispatch(auth()->user()->id, $filepath, $name, 2);
            }
            if ((isset($input['price_id'])) && ($input['price_id'][0] != null)) {
                CompanyPrice::where('company_id', $company->id)->delete();
                foreach ($input['price_id'] as $key => $item) {
                    $company_price = new CompanyPrice();
                    $company_price->company_id = $company->id;
                    $company_price->price_id = $input['price_id'][$key];
                    $company_price->save();
                }
            }
            GroupUserCompany::where('company_id', $company->id)->delete();
            if ((isset($input['users'])) && ($input['users'][0] != null)) {

                foreach ($input['users'] as $key => $item) {
                    $userCompany_group = new GroupUserCompany();
                    $userCompany_group->user_id = $input['users'][$key];
                    $userCompany_group->company_id = $company->id;
                    $userCompany_group->save();
                }
            }

            if ($request->ajax()) {
                return response()->json($company);
            }

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Register updated successfully!');
            return redirect()->back();
        }
    }

    /**
     * delete
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $company = Company::find($id);

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
        $company = Company::find($id);
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
        $company = Company::find($id);
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
        $company = Company::find($id);
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
        $company = Company::find($id);
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
        $company = Company::find($id);
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
        $company = Company::find($id);
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
}
