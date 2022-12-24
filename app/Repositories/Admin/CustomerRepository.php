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

         $customerOrders=User::orderBy('id','DESC')
         ->withCount('orders')
            ->get();
       
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
        
        $customerOrder=User::
        Where(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$request['keyword']."%")
		->orWhere('mobile', 'LIKE', '%'.$request['keyword'].'%')
        ->withCount('orders')->get();
        
        return $customerOrder;

    }
    

}
