<?php

namespace App\Http\Controllers\System\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminDeliveryCovrageRequest;
use App\Http\Requests\Admin\AdminShopCovrageIdRequest;
use App\Http\Resources\Admin\AdminDeliveryCovrageResource;
use App\Interfaces\Admin\AdminDeliveryCoverageInterface;
use App\Models\deliveryCoverage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryCoverageController extends Controller
{
    /**
     * Undocumented variable
     *
     * @var AdminDeliveryCoverageInterface
     */
    private AdminDeliveryCoverageInterface $deliveryCoverageRepository;

    /**
     * Undocumented function
     *
     * @param AdminDeliveryCoverageInterface $deliveryCoverageRepository
     */
    public function __construct(AdminDeliveryCoverageInterface $deliveryCoverageRepository)
    {
        $this->deliveryCoverageRepository = $deliveryCoverageRepository;
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdminShopCovrageIdRequest $request)
    {
        $data=$request->validated();

        return $this->paginateCollection(AdminDeliveryCovrageResource::collection($this->deliveryCoverageRepository->getAllDeliveryCoverage($data,$data['shop_id'])), $request->limit, 'delivery_coverage');

        return $this->dataResponse(['delivery_coverage' => AdminDeliveryCovrageResource::collection($this->deliveryCoverageRepository->getAllDeliveryCoverage($data['shop_id']))], 'success', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminDeliveryCovrageRequest $request)
    {
        $details = $request->validated();
        return $this->dataResponse(['delivery_coverage' => new AdminDeliveryCovrageResource($this->deliveryCoverageRepository->storeCoverage($details))], 'created successful', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AdminShopCovrageIdRequest $request ,$id)
    {
        $data=$request->validated();

        $coverageAreas = deliveryCoverage::where('id', $id)->where('shop_id', $data['shop_id'])->exists();

        if ($coverageAreas) {
            return $this->dataResponse(['delivery_coverage' => new AdminDeliveryCovrageResource($this->deliveryCoverageRepository->getDeliveryCoverage($id))], 'success', 200);
        }
        
        return $this->errorResponseWithMessage('the delivery coverage Not exists ,or not own this delivery coverage', 401);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminDeliveryCovrageRequest $request,$id)
    {

        $data=$request->validated();

        $coverageAreas = deliveryCoverage::where('id', $id)->where('shop_id', $data['shop_id'])->exists();

        if ($coverageAreas) {
            return $this->dataResponse(['delivery_coverage' => new AdminDeliveryCovrageResource($this->deliveryCoverageRepository->updateDeliveryCoverage($id, $request->validated()))], 'updated successful', 200);
        }else{
            return $this->errorResponseWithMessage('the delivery coverage Not exists ,or not own this delivery coverage', 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdminShopCovrageIdRequest $request,$id)
    {
        $data=$request->validated();

        $this->deliveryCoverageRepository->deleteDeliveryCoverage($id,$data['shop_id']);

        return $this->successResponse('deleted successful', 200);
    }
}
