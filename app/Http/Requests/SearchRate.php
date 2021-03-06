<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRate extends FormRequest
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
            'type' => 'required',
            'mode' => 'required',
            'equipment' => 'required',
            'originport' => 'sometimes | required',
            'destinyport' => 'sometimes | required',
            'carriers' => 'required',
        ];
    }
}
