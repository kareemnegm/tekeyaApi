<?php

namespace App\Repositories\Admin;

use App\Http\Resources\Admin\ShopDetailsResource;
use App\Interfaces\Admin\ProviderInterface;
use App\Models\BranchAddress;
use App\Models\Provider;
use App\Models\providerShopBranch;
use App\Models\ProviderShopDetails;

class ProviderRepository implements ProviderInterface
{
    /**
     * Undocumented function
     *
     * @param [type] $details
     * @param [type] $id
     * @return void
     */
    public function updateShopDetails($details, $id)
    {
        $shopDetails = ProviderShopDetails::where('id', $id)->firstOrFail();
        $branch_id = providerShopBranch::where('shop_id', $shopDetails->id)->first();

        if (isset($details['shop_logo'])) {

            if ($shopDetails->getMedia('shop_logo')) {

                $shopDetails->clearMediaCollectionExcept('shop_logo');
            }

            $shopDetails->saveFiles($details['shop_logo'], 'shop_logo');
        }
        if (isset($details['shop_cover'])) {

            if ($shopDetails->getMedia('shop_cover')) {
                $shopDetails->clearMediaCollectionExcept('shop_cover');
            }

            $shopDetails->saveFiles($details['shop_cover'], 'shop_cover');
        }

        $shopDetails->update($details);
        $shopProvider = ProviderShopDetails::find($shopDetails->id);

        if (isset($details['category_id'])) {
            $shopProvider->category()->sync($details['category_id']);
        }
        $this->updateBranch($details, $branch_id->id);
    }



    public function getShopDetails($id)
    {
        $shopDetails = ProviderShopDetails::findOrFail($id);
        return new  ShopDetailsResource($shopDetails);
    }




    public function getShops()
    {
        $shops = ProviderShopDetails::orderBy('id','desc')->get();
        return $shops;
    }


    public function statusShop($data,$id)
    {
        $shop = ProviderShopDetails::where('id', $id)->update(['status' => $data['status']]);
    }



    public function createBranch($details)
    {
        $details['working_hours_day'] = json_encode($details['working_hours_day']);
        $data = providerShopBranch::create($details);
        $data->paymentOption()->syncWithoutDetaching($details['payment_option_id']);
        return $data;
    }


    public function BranchAddress($branchDetails)
    {
        return  BranchAddress::create($branchDetails);
    }


    public function getBranches($id, $details)
    {

        $branches = providerShopBranch::where('shop_id', $id)->get();
        return $branches;
    }


    public function getBranch($id)
    {
        $branches = providerShopBranch::findOrFail($id);
        return $branches;
    }

    public function updateBranch($details, $id)
    {
        $branch = providerShopBranch::findOrFail($id);

        if (isset($details['payment_option_id'])) {
            $branch->paymentOption()->syncWithoutDetaching($details['payment_option_id']);
        }
        if (isset($details['working_hours_day'])) {
            $details['working_hours_day'] = json_encode($details['working_hours_day']);
        }

        $branch->update($details);
        return $branch;
    }



    public function deleteBranch($id)
    {
        $branch = providerShopBranch::findOrFail($id);
        $branch->delete();
    }
}
