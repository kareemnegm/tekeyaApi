<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CategoryIdFormRequest;
use App\Http\Requests\User\SubCategoryFormRequest;
use App\Http\Resources\ShopCategoryResource;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\User\CategoriesResource;
use App\Http\Resources\User\ProductsResource;
use App\Http\Resources\User\ShopResource;
use App\Http\Resources\User\SubCategoriesResource;
use App\Interfaces\User\CategoryInterface;
use App\Traits\UserAreaTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use UserAreaTrait;
    /**
     * Undocumented variable
     *
     * @var CategoryInterface
     */
    private CategoryInterface $CategoryRepository;
    /**
     * Undocumented function
     *
     * @param CategoryInterface $CategoryRepository
     */
    public function __construct(CategoryInterface $CategoryRepository)
    {
        $this->CategoryRepository = $CategoryRepository;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function getCategories(Request $request)
    {
        $mainCategoires=$this->CategoryRepository->getCategories($request);
        return $this->paginateCollection(CategoriesResource::collection($mainCategoires),$request->limit,'categories');

    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function getSubCategories(SubCategoryFormRequest $request)
    {
        $mainCategoires=$this->CategoryRepository->getSubCategories($request);
        return $this->paginateCollection(SubCategoriesResource::collection($mainCategoires),$request->limit,'sub_categories');
    }

    /**
     * Category Products Shop function
     *
     * @param Request $request
     * @return void
     */
    public function categoryShops(CategoryIdFormRequest $request)
    {

        $data=$request->validated();
        
        $data= $this->userArea($data);

        if(!isset($data['latitude']) && !isset($data['longitude'])){
            return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        }


        $categoryShops=$this->CategoryRepository->categoryShops($data);
        return $this->paginateCollection(ShopCategoryResource::collection($categoryShops),$request['limit'],'category_shops');

    }

    /**
     * Category Products function
     *
     * @param Request $request
     * @return void
     */
    public function categoryProducts(CategoryIdFormRequest $request)
    {
        $categoryProducts=$this->CategoryRepository->categoryProducts($request);
        return $this->paginateCollection(ProductsResource::collection($categoryProducts),$request->limit,'category_products');

    }
}
