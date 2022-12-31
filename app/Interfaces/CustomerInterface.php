<?php

namespace App\Interfaces;

interface CustomerInterface
{

   
    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function customerOrdersList($request);
    
    /**
     * Undocumented function
     *
     * @param [type] $collectionID
     * @return void
     */
    public function customerOrderDetails($request,$collectionID);
 
}
