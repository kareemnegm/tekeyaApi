<?php

namespace App\Interfaces\Admin;

interface AdminDeliveryCoverageInterface
{
    /**
     * Undocumented function
     *
     * @param [type] $details
     * @return void
     */
    public function storeCoverage($details);
    /**
     * Undocumented function
     *
     * @param [type] $shop_id
     * @return void
     */
    public function getAllDeliveryCoverage($request,$shop_id);
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function getDeliveryCoverage($id);
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function deleteDeliveryCoverage($id,$shop_id);
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @param [type] $data
     * @return void
     */
    public function updateDeliveryCoverage($id,$data);
}
