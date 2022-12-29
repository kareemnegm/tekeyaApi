<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShopDetailsFormRequest extends BaseFormRequest
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
            'shop_name' => 'nullable|unique:provider_shop_details,shop_name,' . $this->id,
            'whatsapp_number' => 'nullable|numeric|min:10',
            'email' => 'nullable|email',
            'category_id' => 'nullable',
            'shop_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
            'facebook_link' => 'nullable',
            'instagram_link' => 'nullable',
            'web_site' => 'nullable',
            'shop_cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
            'category_id.*' => 'nullable|exists:categories,id',
            "payment_option_id.*" => 'required|exists:payment_options,id',
            'delivery' => 'in:1,0',
            'pick_up' => 'in:1,0',


        ];
    }
}
