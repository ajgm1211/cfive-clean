<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewRequestLcl extends FormRequest
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
<<<<<<< HEAD
        return [
            
            'name' => 'required',
			'validation_expire' => 'required',
			'carrier' => 'required',
            'direction' => 'required',
            'file' => 'required',
                  
=======
        return [       
            'name' => 'required',
			'validation_expire' => 'required',
			'carrierM' => 'required',
            'direction' => 'required',
            'file'=>'required',       
>>>>>>> f47777ff0fdaede293c9e144f65f82be000f3c69
        ];
    }

}