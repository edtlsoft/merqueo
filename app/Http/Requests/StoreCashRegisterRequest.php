<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'D100000' => 'required|min:0',
            'D50000' => 'required|min:0',
            'D20000' => 'required|min:0',
            'D10000' => 'required|min:0',
            'D5000' => 'required|min:0',
            'D1000' => 'required|min:0',
            'D500' => 'required|min:0',
            'D200' => 'required|min:0',
            'D100' => 'required|min:0',
            'D50' => 'required|min:0',
        ];
    }
}
