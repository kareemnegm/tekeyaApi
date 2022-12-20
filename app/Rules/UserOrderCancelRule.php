<?php

namespace App\Rules;

use App\Models\CartProduct;
use App\Models\Order;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserOrderCancelRule implements Rule
{
    public $request;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $order = Order::where('id', $value)->where('user_id', auth('user')->user()->id)->first();
        if (!$order) {
            return false;
        }
        $valid = $order->orderShops()->whereHas('deliveryType', function ($query) {
            $query->where('order_shop_status', 'process');
        })->get();
        if (!$valid->isEmpty()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'cannot cancel order';
    }
}
