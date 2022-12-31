<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListFormRequest;
use App\Http\Requests\Provider\CustomerOrderDetailRequest;
use App\Http\Resources\Proivder\CustomerOrderDetailsResource;
use App\Http\Resources\Proivder\CustomerOrderListResource;
use App\Interfaces\CustomerInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
{


    /**
     * Undocumented function
     *
     * @param CollectionInterface $collectionInterface
     */
    public function __construct(CustomerInterface $customerInterface)
    {
        $this->customerInterface = $customerInterface;
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customersList(ListFormRequest $request)
    {
        $customerOrdersList = $this->customerInterface->customerOrdersList($request->validated());
        return $this->paginateCollection(CustomerOrderListResource::collection($customerOrdersList), $request->limit, 'customers_orders');
//
    }




     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerOrder(CustomerOrderDetailRequest $request)
    {
        $data=$request->validated();
        $customerOrderDetails = $this->customerInterface->customerOrderDetails($data,$data['user_id']);
        return $this->paginateCollection(CustomerOrderDetailsResource::collection($customerOrderDetails), $request->limit, 'customer_orders_details');

    }

}
