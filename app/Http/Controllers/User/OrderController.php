<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\OrderDetielsRequest;
use App\Http\Requests\User\OrderReviewFormRequest;
use App\Http\Requests\User\PlaceOrderFormRequest;
use App\Http\Requests\UserCancelOrderFromRequest;
use App\Http\Resources\User\MyOrderListResource;
use App\Http\Resources\User\PlaceOrderResource;
use App\Interfaces\User\OrderInterface;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Order Private variable
     *
     * @var OrderInterface
     */
    private OrderInterface $orderRepository;
    /**
     * OrderInterface _consturct function
     *
     * @param OrderInterface $orderRepository
     */
    public function __construct(OrderInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Category Products function
     *
     * @param Request $request
     * @return void
     */
    public function orderReview(OrderReviewFormRequest $request)
    {
        $orderProdcuts=$this->orderRepository->orderReview($request->all());

        return $this->dataResponse(
            $orderProdcuts, 'OK', 200);


    }



    /**
     * Category Products function
     *
     * @param Request $request
     * @return object
     */
    public function placeOrder(PlaceOrderFormRequest $request)
    {
        $checkOut=$this->orderRepository->placeOrder($request->validated());

        return $checkOut;



    }

    /**
     * Category Products function
     *
     * @param Request $request
     * @return void
     */
    public function myOrderList(Request $request)
    {
        $orders=$this->orderRepository->myOrderList($request);

        return $this->paginateCollection(MyOrderListResource::collection($orders),$request->limit,'my_orders');

    }


      /**
     * Category Products function
     *
     * @param Request $request
     * @return void
     */
    public function orderDetiels(OrderDetielsRequest $request)
    {
        $orderDetails=$this->orderRepository->orderDetails($request->validated());

        return new PlaceOrderResource($orderDetails);
    }


    public function cancelOrder(UserCancelOrderFromRequest $request)
{
    $orderDetails=$this->orderRepository->cancelOrder($request->validated());

    return $this->successResponse('order canceled successful',200);
}

}
