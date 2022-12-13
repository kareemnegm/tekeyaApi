<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class CategoryIdFormRequest extends BaseFormRequest
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
        if (!auth('user')->check()) {
            return [
                'category_id' => 'required|exists:categories,id',
                'limit' => 'nullable|integer',
                'page' => 'required|integer',
                'area_id'=>'required_without:latitude,longitude|exists:areas,id',
                'latitude' => ['required_without:area_id', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
                'longitude' => ['required_without:area_id', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            ];
        }
        
        return [
            'limit' => 'nullable|integer',
            'page' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
