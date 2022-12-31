<?php

namespace App\Http\Requests\System;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class OrdersFormRequest extends BaseFormRequest
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
            'order_type' => 'nullable|in:recent,pickup,delivery,rejected,canceled',
            'sort' => 'nullable|in:asc,desc',
            'shop_id' => 'nullable|exists:provider_shop_details,id',
            'limit' => 'nullable|integer',
            'page' => 'nullable|integer'

        ];
    }
}
