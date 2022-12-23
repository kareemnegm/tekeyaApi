<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterFormRequest extends BaseFormRequest
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
                'area_id' => 'required_without:latitude,longitude|exists:areas,id',
                'latitude' => ['required_if:sortBy,==,nearest','required_without:area_id', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
                'longitude' => ['required_if:sortBy,==,nearest','required_without:area_id', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
                'sortBy' => 'nullable|in:nearest,alphabetical,price,newest',
                'sort' => 'nullable|in:asc,desc',
                'filter' => 'nullable|in:category,shop',
                'shop_id' => 'required_if:filter,==,shop',
                'category_id' => 'required_if:filter,==,category',
            ];
        }


        return [
            'sortBy' => 'nullable|in:nearest,alphabetical,price,newest',
            'sort' => 'nullable|in:asc,desc',
            'filter' => 'nullable|in:category,shop',
            'shop_id' => 'required_if:filter,==,shop',
            'category_id' => 'required_if:filter,==,category',
        ];
    }
}
