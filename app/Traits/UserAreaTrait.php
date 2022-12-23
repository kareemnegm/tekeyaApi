<?php

namespace App\Traits;

use App\Models\Area;

trait UserAreaTrait
{


   /**
     * Undocumented function
     *
     * @param [type] $date
     * @return array
     */
    private function userArea(array $data){

        if (auth('user')->check()) {

            $user = auth('user')->user();

            if ($user->userLocation) {
                    $data['latitude'] = $user->userLocation->latitude;
                    $data['longitude'] = $user->userLocation->longitude;
            }elseif(isset($user->area_id)) {
                $area = Area::findOrFail($user->area_id);
                $data['latitude'] = $area->latitude;
                $data['longitude'] = $area->longitude;


        }
        }else{
            if(isset($data['area_id'])){
                $area = Area::findOrFail($data['area_id']);
                $data['latitude'] = $area->latitude;
                $data['longitude'] = $area->longitude;
            }
        }
        return $data;
    }


}
