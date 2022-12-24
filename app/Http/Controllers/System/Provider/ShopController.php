<?php

namespace App\Http\Controllers\System\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProviderCreateFormRequest;
use App\Http\Requests\Admin\ProviderShopFormRequest;
use App\Http\Requests\Admin\ShopBranchFormRequest as AdminShopBranchFormRequest;
use App\Http\Requests\Admin\UpdateShopDetailsFormRequest as AdminUpdateShopDetailsFormRequest;
use App\Http\Requests\Provider\BranchIdFormRequest;
use App\Http\Requests\System\ShopStatusFormRequest;
use App\Http\Resources\Admin\ShopDetailsResource;
use App\Http\Resources\Provider\ShopBranchResource;
use App\Interfaces\Admin\ProviderInterface;
use App\Models\providerShopBranch;
use App\Models\ProviderShopDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    private ProviderInterface $ProviderRepository;

    public function __construct(ProviderInterface $ProviderRepository)
    {
        $this->ProviderRepository = $ProviderRepository;
    }


    public function createShop(ProviderShopFormRequest $request)
    {
        $data = $request->validated();

        $data['status'] = 'approved';

        $data['name']=$data['shop_name'];

        $shopDetails = ProviderShopDetails::create($data);

        $data['shop_id']=$shopDetails->id;

        $branch = providerShopBranch::create($data);

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


        if (isset($details['category_id'])) {
            $shopDetails->category()->sync($details['category_id']);
        }
      
        return $this->dataResponse(['shop' => new ShopDetailsResource($shopDetails)], 'success', 200);
    }


    public function approverPendingStores($id)
    {
        $shop = ProviderShopDetails::find($id);
        $shop->update(['status' => 'approved']);
        return $this->successResponse('approved success', 200);
    }

    /***
     * @param $request , $id
     * update shop details
     */
    public function updateShopDetails(AdminUpdateShopDetailsFormRequest $request, $id)
    {
        $shopExists = ProviderShopDetails::findOrFail($id);
        $details = $request->validated();
        $details['admin_id'] = Auth::user()->id;
        $this->ProviderRepository->updateShopDetails($details, $id);
        return $this->successResponse('updated successful', 200);
    }


    /***
     * @param $request , $id
     * get shop details
     */
    public function getShopDetails($id)
    {
        return $this->dataResponse(['shop' => $this->ProviderRepository->getShopDetails($id)], 'success', 200);
    }



    public function getShops(Request $request)
    {

        return $this->paginateCollection(ShopDetailsResource::collection($this->ProviderRepository->getShops()), $request->limit, 'shops');
    }


    public function statusShop(ShopStatusFormRequest $request,$id)
    {
        $data=$request->validated();
        $this->ProviderRepository->statusShop($data,$id);
        return $this->successResponse('shop suspended', 200);
    }



    /***
     * @param $request
     * create branch and branch address
     */

    public function createBranch(AdminShopBranchFormRequest $request)
    {
        $details = $request->input();
        $shopDetails = ProviderShopDetails::where('id', $details['shop_id'])->first();
        $details['shop_id'] = $shopDetails['id'];
        $address = $this->ProviderRepository->BranchAddress($details);
        $details['branch_address_id'] = $address->id;
        $branch =   $this->ProviderRepository->createBranch($details);
        return $this->dataResponse(['branch' => new ShopBranchResource($branch)], 'created successful', 200);
    }


    public function getBranches(Request $request, $id)
    {
        // $shop_id = $request->shop_id;
        return $this->paginateCollection(ShopBranchResource::collection($this->ProviderRepository->getBranches($id, $request)), $request->limit, 'branch');
    }


    public function getBranch($id)
    {
        return $this->dataResponse(['branch' => new ShopBranchResource($this->ProviderRepository->getBranch($id))], 'success', 200);
    }



    public function updateBranch(Request $request, $id)
    {
        $details = $request->input();
        $data = $this->ProviderRepository->updateBranch($details, $id);
        return $this->dataResponse(['branch' => new ShopBranchResource($data)], 'update successful', 200);
    }



    public function deleteBranch($id)
    {
        $this->ProviderRepository->deleteBranch($id);
        return $this->successResponse('deleted successful', 200);
    }
}
