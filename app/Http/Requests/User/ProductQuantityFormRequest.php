<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductQuantityFormRequest extends BaseFormRequest
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
                'product_id' => [                                                                  
                    'required',                                                            
                    Rule::exists('cart_product', 'product_id')                     
                    ->where('product_id',$request->product_id)
                    ->where('cart_id',$request['cart_id']),    

                    Rule::exists('products', 'id')                     
                    ->where('is_published',1),                                                                    
                ],  
                'shop_id' => [                                                                  
                    'required',                                                            
                    Rule::exists('cart_product', 'provider_shop_details_id')                     
                    ->where('product_id',$request->product_id)->where('provider_shop_details_id',$request->shop_id)
                    ->where('cart_id',$request['cart_id']),                                                                    
                ],  

                'quantity'=> 'required|integer'                                                                     
          
            ];
        
    }
}
