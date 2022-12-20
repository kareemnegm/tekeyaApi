<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseFormRequest;
use App\Models\CartProduct;
use App\Rules\ProductInCartRule;
use App\Rules\ShopinCartRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductsFormRequest extends BaseFormRequest
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
    public function rules(Request $request)
    {
        $request['cart_id'] = Auth::user()->cart->id;

        return [
            'product_id'=>['required','exists:products,id',
            new ProductInCartRule($request)
        ],

            'shop_id' => [
                'required',

                Rule::exists('products', 'shop_id')                     
                ->where('id',$request->product_id)->where('shop_id',$request->shop_id),

                new ShopinCartRule($request)
            ],
            'quantity'=> 'required|integer',
            
            'variants'=> 'nullable',

        ];
    }

      /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
        'shop_id.exists'=>'The selected product shop id is invalid.',
        ];
    }
}
