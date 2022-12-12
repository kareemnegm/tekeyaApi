<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductsShopFormRequest extends BaseFormRequest
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
            'shop_id' => 'required|exists:provider_shop_details,id',
            // 'area_id'=>'nullable|exists:areas,id',
            'category_id' => 'nullable|exists:categories,id',
            'collection_id' => ['nullable',Rule::exists('collections', 'id')->where('shop_id',request()->shop_id)  ],

        ];
    }
}
