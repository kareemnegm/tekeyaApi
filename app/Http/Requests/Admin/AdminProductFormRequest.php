<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminProductFormRequest extends BaseFormRequest
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
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required',
            'offer_price' => 'nullable',

            'start_date' => 'nullable|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            // 'stock_quantity' => 'required',
            'total_weight' => 'nullable',
            'is_published' => 'required|in:1,0',
            'to_donation' => 'required|in:1,0',

            'collection_id' => ['nullable', Rule::exists('collections', 'id')->where(
                'shop_id',
                request()->shop_id
            )],

            'category_id' => 'required|exists:categories,id',
            'tags' => 'sometimes|required|array',
            'tags.*' => 'required|string|distinct|min:3',
            'product_images' => 'nullable',
            'variants' => 'nullable|array',
            'variants.*.*.value' => 'sometimes|required|string',
            // 'branches_stock' => 'required|array',
            // 'branches_stock.*.branch_id' => 'required|exists:provider_shop_branches,id,shop_id,'.auth('provider')->user()->providerShopDetails->id,
            // 'branches_stock.*.stock_qty' => 'required|numeric',
            'variants.*.*.is_default' => 'distinct'

        ];
    }
}

   