<?php

namespace App\Interfaces\User;

interface OrderInterface
{

    public function cancelOrder($data);

    /**
     * Undocumented function
     *
     * @param [type] $products
     * @param [type] $cart_id
     * @return void
     */
    public function orderReview($req);
    /**
     * Undocumented function
     *
     * @param [type] $product
     * @return void
     */
    public function placeOrder($req);

      /**
     * Undocumented function
     *
     * @param [type] $product
     * @return void
     */
    public function myOrderList($req);

     /**
     * Undocumented function
     *
     * @param [type] $product
     * @return void
     */
    public function orderDetails($req);



}
