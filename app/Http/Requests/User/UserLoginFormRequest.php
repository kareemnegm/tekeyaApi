<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserLoginFormRequest extends BaseFormRequest
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
           'mobile'=> ['required_without:email','max:11',Rule::exists('users','mobile')->whereNull('deleted_at')],
           'email'=> ['required_without:mobile','max:11',Rule::exists('users','email')->whereNull('deleted_at')],
        ];
    }
}
