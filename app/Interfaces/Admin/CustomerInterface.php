<?php

namespace App\Interfaces\Admin;

interface CustomerInterface
{

   
    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function customerList($request);
    
    /**
     * Undocumented function
     *
     * @param [type] $collectionID
     * @return void
     */
    public function customerDetails($collectionID);

      /**
     * Undocumented function
     *
     * @param [type] $collectionID
     * @return void
     */
    public function customersSearch($collectionID);
 
}
