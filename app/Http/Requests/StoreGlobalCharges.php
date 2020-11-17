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
            
            //PORT TO PORT
            'port_dest' => 'required_if:typeroute,==,port',
            'port_orig' => 'required_if:typeroute,==,port',

            //COUNTRY TO COUNTRY
            'country_orig' => 'required_if:typeroute,==,country',
            'country_dest' => 'required_if:typeroute,==,country',

            //PORT TO COUNTRY
            'portcountry_orig' => 'required_if:typeroute,==,portcountry',
            'portcountry_dest' => 'required_if:typeroute,==,portcountry',

            //COUNTRY  TO PORT
            'countryport_orig' => 'required_if:typeroute,==,countryport',
            'countryport_dest' => 'required_if:typeroute,==,countryport',

            'surcharge_id'=>'required',
            'calculationtype' => 'required',
            'localcarrier' => 'required', 
              
        ];
    }
}