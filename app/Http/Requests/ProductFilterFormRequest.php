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

        return [
            'sortBy' => 'nullable|in:nearest,alphabetical,price,newest',
            'sort' => 'nullable|in:asc,desc',
            'latitude' => 'required_if:sortBy,==,nearest',
            'longitude' => 'required_if:sortBy,==,nearest',
            'filter' => 'nullable|in:category,shop',
            'shop_id' => 'required_if:filter,==,shop',
            'category_id' => 'required_if:filter,==,category',
        ];
    }
}
