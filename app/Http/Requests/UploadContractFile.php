<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadContractFile extends FormRequest
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
            'reference' => 'required',
            'type' => [
                'required',
                Rule::in(['FCL', 'fcl', 'LCL', 'lcl']),
            ],
            'direction' => [
                'required',
                Rule::in(['IMPORT', 'import', 'EXPORT', 'export', 'BOTH', 'both']),
            ],
            'file' => 'required|file',
            'valid_from' => 'required',
            'valid_until' => 'required',
            'carriers' => 'required',
            //'code' => 'required',
        ];
    }
}
