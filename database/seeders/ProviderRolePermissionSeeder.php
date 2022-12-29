<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ProviderRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role=Role::where('name','provider')->first();
         
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

   
   
   
    $permissions = config('acl.provider_permission');
      $allPermissions = [];
      foreach ($permissions as $per) {
          foreach ($per as $item) {
              $allPermissions[] = $item;
          }
      }
      
      
      
       foreach($allPermissions as $permission){
          $role->givePermissionTo($permission);
      }
    }
}
