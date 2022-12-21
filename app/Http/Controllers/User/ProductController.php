<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterFormRequest;
use App\Http\Requests\User\ProductAreaFormRequest;
use App\Http\Resources\Provider\VariantValueResource;
use App\Http\Resources\User\ProductResource;
use App\Http\Resources\User\ProductsResource;
use App\Interfaces\User\ProductInterface;
use App\Models\Product;
use App\Traits\UserAreaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use UserAreaTrait;
    /**
     * Undocumented variable
     *
     * @var ProviderInterface
     */
    private ProductInterface $ProductRepository;
    /**
     * Undocumented function
     *
     * @param ProviderInterface $ProviderRepository
     */
    public function __construct(ProductInterface $ProductRepository)
    {
        $this->ProductRepository = $ProductRepository;
    }


    public function productsForYou(ProductAreaFormRequest $request)
    {

        $data=$request->validated();

        $data= $this->userArea($data);

        if(!isset($data['latitude']) && !isset($data['longitude'])){
            return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        }

        $products = $this->ProductRepository->productJustForYou($data);
        return $this->paginateCollection(ProductsResource::collection($products), $request->limit, 'products');
    }



    public function mostPopularProduct(ProductAreaFormRequest $request)
    {

        $data=$request->validated();

        $data= $this->userArea($data);

        if(!isset($data['latitude']) && !isset($data['longitude'])){
            return $this->errorResponseWithMessage('User not have any area location or lat and long',422);
        }

        $products = $this->ProductRepository->mostPopularProduct($data);
        return $this->paginateCollection(ProductsResource::collection($products), $request->limit, 'products');
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function relatedProducts(Request $request, $product_id)
    {
        $products = $this->ProductRepository->relatedProducts($product_id);
        return $this->paginateCollection(ProductsResource::collection($products), $request->limit, 'related_products');
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function similarProducts(ProductFilterFormRequest $request, $product_id)
    {
        $products = $this->ProductRepository->similarProducts($product_id,$request->validated());
        return $this->paginateCollection(ProductsResource::collection($products), $request->limit, 'similar_products');
    }


    public function showProduct($id)
    {
        $product = Product::where('id', $id)->where('is_published', 1)->firstOrFail();
        return $this->dataResponse(['product' => new ProductResource($product)], 'success', 200);
    }


    public function getVariantsValues(Request $variant_id)
    {
        return $this->dataResponse(['values' => VariantValueResource::collection($this->ProductRepository->getVariantsValues($variant_id->variant_id))], 'success', 200);
    }
}
