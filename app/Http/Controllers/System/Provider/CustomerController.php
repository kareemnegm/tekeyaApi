<?php

namespace App\Http\Controllers\System\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\CustomerSearchFormRequest;
use App\Http\Resources\System\CustomerResource;
use App\Http\Resources\System\CustomersResoruce;
use App\Interfaces\Admin\CustomerInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    
    private $customerRepository;

    /**
     * Undocumented function
     *
     * @param CustomerInterface $customerRepository
     */
    public function __construct(CustomerInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerList(Request $request)
    {     

       $customers= $this->customerRepository->customerList($request);
       return $this->paginateCollection(CustomersResoruce::collection($customers), $request->limit, 'customers');

    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerDetails(Request $request,$user_id)
    {
        $customer= $this->customerRepository->customerDetails($user_id);
        return $this->dataResponse(['customer' => new CustomerResource($customer)], 'OK', 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customersSearch(CustomerSearchFormRequest $request)
    {
        $data=$request->validated();
        $customers= $this->customerRepository->customersSearch($data);
        return $this->paginateCollection(CustomersResoruce::collection($customers), isset($data['limit'])?$data['limit']:null , 'customers');
    }
}
