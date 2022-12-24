<?php

namespace App\Interfaces\Admin;

interface ProviderInterface
{



    // public function createShopDetails($details);
    public function updateShopDetails($details, $id);
    public function getShopDetails($id);
    public function getShops();
    public function suspendShop($data,$id);
    // public function getShopByCategoryId($id, $details);
    /**
     * !branch section
     */
    public function createBranch($details);
    public function updateBranch($details, $id);
    public function getBranches($id, $details);
    public function deleteBranch($id);
    public function BranchAddress($branchDetails);
    public function  getBranch($id);
    /**
     * ! end of branch section
     */
}
