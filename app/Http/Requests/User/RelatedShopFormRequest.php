<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RelatedShopFormRequest extends FormRequest
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
       if (auth('user')->check()) {
        return [
            
        ];
    }

    return [
        'page'=> 'nullable|integer',
        'limit'=> 'nullable|integer',
        'area_id'=>'required_without:latitude,longitude|exists:areas,id',
        'latitude' => ['required_without:area_id', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        'longitude' => ['required_without:area_id', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
    ];
    }
}
