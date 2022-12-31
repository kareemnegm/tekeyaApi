<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\AdminFinanceOrderRequest;
use App\Http\Requests\System\FinanceStatisticsRequest;
use App\Http\Requests\System\OrdersFormRequest;
use App\Http\Requests\System\OrderShopIdFormRequest;
use App\Http\Resources\Admin\AdminShopOrderResource;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Resources\Provider\ProviderPlacedOrdersDetailsResource;
use App\Interfaces\Admin\AdminOrderInterface;
use App\Models\OrderShop;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var AdminOrderInterface
     */
    private AdminOrderInterface $AdminOrderRepository;
    /**
     * Undocumented function
     *
     * @param AdminOrderInterface $ProviderOrderRepository
     */
    public function __construct(AdminOrderInterface $AdminOrderRepository)
    {
        $this->AdminOrderRepository = $AdminOrderRepository;
    }


    /**
     * Undocumented function
     *
     * @param OrdersFormRequest $request
     * @return void
     */
    public function ShopOrders(OrdersFormRequest $request)
    {

        $limit=isset($request['limit']) ? $request['limit'] :10;


        $request->validated();

        $q = OrderShop::with('order')->with('invoice')->withSum('orderItems', 'quantity');
        if (isset($request['shop_id']) && !empty($request['shop_id'])) {
            $q = OrderShop::where('shop_id', $request['shop_id'])->with('order')->with('invoice')->withSum('orderItems', 'quantity');
        }
        if (!isset($request['order_type'])) {
            $shopOrder = $q;
        } elseif ($request['order_type'] == 'recent') {

            $shopOrder = $q->orderBy('id', 'desc');
        } elseif ($request['order_type'] == 'pickup') {

            $shopOrder = $q->where('model_type', 'App\Models\OrderPickup');
        } elseif ($request['order_type'] == 'delivery') {

            $shopOrder = $q->where('model_type', 'App\Models\OrderShipment');
        } elseif ($request['order_type'] == 'rejected') {

            $shopOrder = $q->whereHas('deliveryType', function ($query) {
                $query->where('order_shop_status', '=', 'rejected');
            });
        } elseif ($request['order_type'] == 'canceled') {
            $shopOrder = $q->whereHas('deliveryType', function ($query) {
                $query->where('order_user_status', '=', 'canceled');
            });
        }
        if (isset($request['sort']) && !empty($request['sort'])) {
            $shopOrder = $shopOrder->orderBy('id', $request['sort'])->paginate($limit);
        } else {
            $shopOrder = $shopOrder->paginate($limit);
        }

        return $this->paginateCollection(OrderResource::collection($shopOrder), $request->limit, 'orders_shop_list');
    }




    public function orderDetails($id)
    {
        return $this->dataResponse(['order_details'=>new ProviderPlacedOrdersDetailsResource($this->AdminOrderRepository->orderDetails($id))],'success',200);
    }


    public function AdminUpdateOrderDeliveryStatus(OrderShopIdFormRequest $order_shop_id)
    {
        // $order_shop_id->validate();
        $shopOrder = OrderShop::findOrFail($order_shop_id['order_shop_id']);
        $shopOrder->deliveryType->update(['order_shop_status' => $order_shop_id['status']]);
        return $this->successResponse('Order shop status updated successfuly.');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function financeOrders(AdminFinanceOrderRequest $request)
    {
        return $this->paginateCollection(AdminShopOrderResource::collection($this->AdminOrderRepository->finaanceOrders($request['shop_id'], $request->validated())), $request->limit, 'orders_shop_list');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function financeStatistics(FinanceStatisticsRequest $request)
    {


        $order=$this->AdminOrderRepository->financeStatistics($request['shop_id']);

        return $this->dataResponse(['orders'=>$order['orders'],'income'=>$order['income'],
        'cash'=>$order['cash']],'success',200);

    }
}
