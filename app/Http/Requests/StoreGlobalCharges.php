<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGlobalCharges extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'surcharge_id' => 'required',
            'typedestiny_id' => 'required',
            'calculationtype_id ' => 'required',
            'ammount' => 'required',
            'currency_id' => 'required',
            
            
            
        ];
    }
}