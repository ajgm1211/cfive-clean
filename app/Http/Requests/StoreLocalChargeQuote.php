<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocalChargeQuote extends FormRequest
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
            'charges.surcharge' => 'required',
            'charges.price' => 'required',
            'charges.calculation_type' => 'required',
            'charges.currency' => 'required',
            'charges.carrier' => 'required',
            'port_id' => 'required',
            'quote_id' => 'required',
            'type_id' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'charges.surcharge.required'  => 'Charge is required',
            'charges.price.required'  => 'You must enter rates',
            'charges.calculation_type.required'  => 'Detail is required',
            'charges.carrier.required'  => 'Provider is required',
            'charges.currency.required'  => 'Currency is required',
        ];
    }
}
