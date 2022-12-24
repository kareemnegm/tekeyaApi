<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\AdminOrderInterface;
use App\Interfaces\ProviderOrderInterface;
use App\Models\DeliveryOption;
use App\Models\Order;
use App\Models\OrderShop;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class AdminOrderRepository implements AdminOrderInterface
{



    public function orderDetails($orderId)
    {

        $order = OrderShop::where('order_id', $orderId)->firstOrFail();

        return  $order;
    }
    /**
     * Undocumented function
     *
     * @param [type] $order_shop_id
     * @return void
     */
    public function finaanceOrders($shop_id, $request)
    {
        $q = OrderShop::with('order')->with('invoice')->withSum('orderItems', 'quantity');

        if (isset($request['shop_id']) && !empty($request['shop_id'])) {
            $q->where('shop_id', $shop_id);
        }

        if (!isset($request['order_type']) || isset($request['order_type']) && $request['order_type'] == 'recent') {

            $shopOrder = $q;
        } elseif (isset($request['order_type']) && $request['order_type'] == 'pickup') {

            $shopOrder = $q->where('model_type', 'App\Models\OrderPickup');
        } elseif (isset($request['order_type']) && $request['order_type'] == 'delivery') {

            $shopOrder = $q->where('model_type', 'App\Models\OrderShipment');
        } elseif (isset($request['order_type']) && $request['order_type'] == 'rejected') {

            $shopOrder = $q->whereHas('deliveryType', function ($query) {
                $query->where('order_shop_status', '=', 'rejected');
            });
        } elseif (isset($request['order_type']) && $request['order_type'] == 'canceled') {
            $shopOrder = $q->whereHas('deliveryType', function ($query) {
                $query->where('order_user_status', '=', 'canceled');
            });
        }



        if (isset($request['sort']) && !empty($request['sort'])) {
            $shopOrder = $shopOrder->orderBy('id', $request['sort'])->get();
        } else {
            $shopOrder = $shopOrder->get();
        }



        return $shopOrder;
    }


    /**
     * Undocumented function
     *
     * @param [type] $order_shop_id
     * @return void
     */
    public function financeStatistics($shop_id)
    {

        $shopOrder = OrderShop::query();


        $total_Invoice = OrderShop::with('invoice');

        if (isset($shop_id) && !empty($shop_id)) {

            $shopOrder = $shopOrder->where('shop_id', $shop_id);
            $total_Invoice = $shopOrder->where('shop_id', $shop_id);
        }

        return [
            'orders' => $shopOrder->count(),
            'income' => $total_Invoice->get()->sum('invoice.total_invoice'),
            'cash' => $total_Invoice->get()->sum('invoice.total_invoice'),

        ];
    }
}
