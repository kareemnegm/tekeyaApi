<?php

namespace App\Http\Requests;

use App\Rules\UserOrderCancelRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserCancelOrderFromRequest extends BaseFormRequest
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
        return [
            'order_id' => ['required',Rule::exists('orders','id')->where('user_id',auth('user')->user()->id),new UserOrderCancelRule($request)],
            'status' => 'required|in:canceled'
        ];
    }
}
