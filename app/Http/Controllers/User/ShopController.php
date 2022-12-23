<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductsShopFormRequest;
use App\Http\Requests\User\NewShopsFormRequest;
use App\Http\Requests\User\RelatedShopFormRequest;
use App\Http\Requests\User\ShopCollectionsFromRequest;
use App\Http\Requests\User\ShopIdFormRequest;
use App\Http\Requests\User\SingleShopResource;
use App\Http\Resources\User\ProductsResource;
use App\Http\Resources\User\ShopBracnhesResource;
use App\Http\Resources\User\ShopCollectionsResource;
use App\Http\Resources\User\ShopResource;
use App\Http\Resources\User\ShopsProductsResoruce;
use App\Http\Resources\User\ShopsResource;
use App\Interfaces\User\ShopInrerface;
use App\Models\Area;
use App\Models\Category;
use App\Models\Product;
use App\Models\providerShopBranch;
use App\Models\ProviderShopDetails;
use App\Traits\UserAreaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Monolog\Handler\IFTTTHandler;

class ShopController extends Controller
{
    use UserAreaTrait;
    /**
     * Undocumented variable
     *
     * @var ProviderInterface
     */
    private ShopInrerface $shopRepository;
    /**
     * Undocumented function
     *
     * @param ProviderInterface $ProviderRepository
     */
    public function __construct(ShopInrerface $shopRepository)
    {
        $this->shopRepository = $shopRepository;


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function nearestShops(NewShopsFormRequest $request)
    {

        $data=$request->validated();

        $data= $this->userArea($data);

        if(!isset($data['latitude']) && !isset($data['longitude'])){
            return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        }

        $nearestShops = $this->shopRepository->nearestShops($data);

        return $this->paginateCollection(ShopsResource::collection($nearestShops), $request->limit, 'shops');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function newShops(NewShopsFormRequest $request)
    {

        $data=$request->validated();

        $data= $this->userArea($data);

        if(!isset($data['latitude']) && !isset($data['longitude'])){
            return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        }

        $newShops = $this->shopRepository->newShops($data);
        return $this->paginateCollection(ShopsResource::collection($newShops), $request->limit, 'shops');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shopsProducts(NewShopsFormRequest $request)
    {

        $data=$request->validated();

        $data= $this->userArea($data);

        if(!isset($data['latitude']) && !isset($data['longitude'])){
            return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        }

        $shopsProducts = $this->shopRepository->shopsProducts($data);
        return $this->paginateCollection(ShopsProductsResoruce::collection($shopsProducts), $request->limit, 'shops');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductsShop(ProductsShopFormRequest $request)
    {
        $products = $this->shopRepository->getProductsShop($request);
        return $this->paginateCollection(ProductsResource::collection($products), $request->limit, 'products');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getShopDetails(SingleShopResource $request)
    {
        $data=$request->validated();

        $data= $this->userArea($data);

        // if(!isset($data['latitude']) && !isset($data['longitude'])){
        //     return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        // }

        $shop = $this->shopRepository->getShopDetails($data);

        if($shop){
        return $this->dataResponse(['shop' => new ShopResource($shop)], 'OK', 200);
        }{
            return $this->errorResponseWithMessage('The shop not in your area',422);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getShopBranches(ShopIdFormRequest $request)
    {
        // if (auth('user')->check()) {
        //     $userLocation = auth('user')->user()->userLocation;
        //     if ($userLocation) {
        //         $request['latitude'] = $userLocation->latitude;
        //         $request['longitude'] = $userLocation->longitude;
        //     }
        // }
        $branches = $this->shopRepository->getShopBranches($request);
        return $this->paginateCollection(ShopBracnhesResource::collection($branches), $request->limit, 'branches');
    }


    public function relatedShops(RelatedShopFormRequest $request,$productId)
    {

        $data=$request->validated();

        $data= $this->userArea($data);

        if(!isset($data['latitude']) && !isset($data['longitude'])){
            return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        }

        $shops = $this->shopRepository->relatedShops($request,$productId);


        return $this->paginateCollection(ShopsResource::collection($shops), $request->limit, 'related_shops');

    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getShopCollections(ShopCollectionsFromRequest $request)
    {
        $data=$request->validated();

        // $data= $this->userArea($data);

        // if(!isset($data['latitude']) && !isset($data['longitude'])){
        //     return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        // }


        $products = $this->shopRepository->getShopCollections($data['shop_id']);
        return $this->paginateCollection(ShopCollectionsResource::collection($products), $request->limit, 'collections');
    }

    // /**
    //  * Undocumented function
    //  *
    //  * @param [type] $date
    //  * @return array
    //  */
    // private function userArea($data){

    //     if (auth('user')->check()) {

    //         $user = auth('user')->user();

    //         if ($user->userLocation) {
    //                 $data['latitude'] = $user->userLocation->latitude;
    //                 $data['longitude'] = $user->userLocation->longitude;
    //         }elseif(isset($user->area_id)) {
    //             $area = Area::findOrFail($user->area_id);
    //             $data['latitude'] = $area->latitude;
    //             $data['longitude'] = $area->longitude;


    //         }
    //         if(!isset($data['latitude']) && !isset($data['longitude'])){
    //             return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
    //         }
    //     }else{
    //         if(isset($data['area_id'])){
    //             $area = Area::findOrFail($data['area_id']);
    //             $data['latitude'] = $area->latitude;
    //             $data['longitude'] = $area->longitude;
    //         }
    //     }
    //     return $data;
    // }
}
