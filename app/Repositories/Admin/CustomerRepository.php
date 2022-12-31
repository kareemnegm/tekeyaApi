<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\CustomerInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerRepository implements CustomerInterface
{

    /**
     * Get All Shop Collection function
     *
     * @param [type] $projectId
     * @return void
     */
    public function customerList($request)
    {

         $q=User::query();

         $limit=isset($request['limit']) ? $request['limit'] :10;

         if(isset($request['keyword'])){
            $customerOrders=$q->Where(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$request['keyword']."%")
            ->orWhere('mobile', 'LIKE', '%'.$request['keyword'].'%')->  orderBy('id','DESC')
            ->withCount('orders')
            ->paginate($limit);
         }else{
            $customerOrders=$q->orderBy('id','DESC')
            ->withCount('orders')
            ->paginate($limit);
         }
       
        return  $customerOrders;
        
    }

     /**
     * Get All Shop Collection function
     *
     * @param [type] $projectId
     * @return void
     */
    public function customerDetails($user_id)
    {
        $customerOrder=User::where('id',$user_id)
        ->withCount('orders')->firstOrFail();

        return $customerOrder;
    }

    /**
     * Get All Shop Collection function
     *
     * @param [type] $projectId
     * @return void
     */
    public function customersSearch($request)
    {
        $limit=isset($request['limit']) ? $request['limit'] :10;

        $customerOrder=User::where(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$request['keyword']."%")
		->orWhere('mobile', 'LIKE', '%'.$request['keyword'].'%')
        ->withCount('orders')->paginate($limit);
        
        return $customerOrder;

    }
    

}
