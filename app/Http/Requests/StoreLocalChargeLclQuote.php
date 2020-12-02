<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocalChargeLclQuote extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'charges.price' => 'required',
            'charges.units' => 'required',
            'charges.calculation_type' => 'required',
            'charges.currency' => 'required',
            'charges.carrier' => 'required',
            'port_id' => 'required',
            'quote_id' => 'required',
            'type_id' => 'required',
        ];
    }
}
