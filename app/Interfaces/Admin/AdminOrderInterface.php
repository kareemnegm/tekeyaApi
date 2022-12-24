<?php

namespace App\Interfaces\Admin;

interface AdminOrderInterface
{

    public function orderDetails($orderId);
    // /**
    //  * Undocumented function
    //  *
    //  * @param [type] $request
    //  * @param [type] $shop_id
    //  * @return void
    //  */
    // public function ShopOrders($request,$shop_id);
    // /**
    //  * Undocumented function
    //  *
    //  * @param [type] $provider_id
    //  * @param [type] $orderId
    //  * @return void
    //  */
    // public function orderDetails($provider_id,$orderId);
    // /**
    //  * Undocumented function
    //  *
    //  * @param [type] $order_shop_id
    //  * @return void
    //  */
    // public function UpdateOrderDeliveryStatus($order_shop_id);

    // /**
    //  * Undocumented function
    //  *
    //  * @param [type] $order_shop_id
    //  * @return void
    //  */
    // public function branchStatistics($shop_id);


    /**
     * Undocumented function
     *
     * @param [type] $order_shop_id
     * @return void
     */
    public function finaanceOrders($shop_id,$request);

     /**
     * Undocumented function
     *
     * @param [type] $order_shop_id
     * @return void
     */
    public function financeStatistics($shop_id);





    // public function OrderSearch($provider_id,$search);


}
