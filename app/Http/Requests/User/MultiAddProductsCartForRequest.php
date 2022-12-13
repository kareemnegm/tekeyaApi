<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use App\Rules\ProductInCartRule;
use App\Rules\ShopinCartRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MultiAddProductsCartForRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {

            return [
                '*.shop_id' => [
                    'required',
                    Rule::exists('products', 'shop_id')     
                    ->where('id',$request->product_id)->where('shop_id',$request->shop_id),
                    new ShopinCartRule($request)
                ],

                '*.product_id'=>['required','exists:products,id',
                new ProductInCartRule($request)
            ],
    
                '*.quantity'=> 'required|integer'            
        ];
    }
}
